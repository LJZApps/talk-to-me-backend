<?php

namespace App\API\Core;

use App\API\Api;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Kreait\Firebase\Auth\SendActionLink\FailedToSendActionLink;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Throwable;

class User_Core_Api extends Api
{
    private UserFactory $userFactory;
    private UserRepository $userRepository;
    private JWTTokenManagerInterface $tokenManager;
    private JWTEncoderInterface $tokenEncoder;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface      $em,
        ParameterBagInterface       $param,
        Auth                        $auth,
        UserFactory                 $userFactory,
        UserRepository              $userRepository,
        JWTTokenManagerInterface    $tokenManager,
        JWTEncoderInterface         $tokenEncoder,
        UserPasswordHasherInterface $passwordHasher,
    )
    {
        parent::__construct($em, $param, $auth);

        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
        $this->tokenManager = $tokenManager;
        $this->passwordHasher = $passwordHasher;
        $this->tokenEncoder = $tokenEncoder;
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $email = $request->query->get("email");
            $password = $request->query->get("password");

            if (is_null($email)) {
                return $this->errorResponse("key_email_missing", "Please provide an email.");
            }

            if (is_null($password)) {
                return $this->errorResponse("key_password_missing", "Please provide a password.");
            }

            $user = $this->userRepository->findUserByEmail($email);

            if (is_null($user)) {
                return $this->errorResponse(
                    "account_not_found",
                    "There is no account with this email address."
                );
            }

            if (!$this->passwordHasher->isPasswordValid($user, trim($password))) {
                return $this->errorResponse(
                    "incorrect_password",
                    "The provided password is invalid"
                );
            }

            $token = $this->tokenManager->createFromPayload(
                user: $user,
                payload: array("user_id" => $user->getId())
            );

            try {
                $tokenData = $this->tokenManager->parse($token);
                $expirationTime = 0;

                if (array_key_exists('exp', $tokenData)) {
                    $expirationTime = $tokenData['exp'];
                }

                return $this->successResponse([
                    "token" => $token,
                    "token_exp" => $expirationTime,
                    "token_data" => $tokenData
                ]);
            } catch (Exception $e) {
                throw new AuthenticationException(
                    message: 'Token expired!'
                );
            }
        } catch (Exception|Throwable $error) {
            return $this->internalErrorResponse();
        }
    }

    public function createUser(Request $request): JsonResponse
    {
        try {
            $requestBody = json_decode($request->getContent(), true);

            if (!array_key_exists("email", $requestBody)) {
                return $this->errorResponse("key_email_missing", "Email is missing.");
            }
            if (!array_key_exists("username", $requestBody)) {
                return $this->errorResponse("key_username_missing", "Username is missing");
            }
            if (!array_key_exists("display_name", $requestBody)) {
                return $this->errorResponse("key_display_name_missing", "Display name is missing");
            }
            if (!array_key_exists("plain_password", $requestBody)) {
                return $this->errorResponse("key_plain_password_missing", "Plain password is missing");
            }
            if (!array_key_exists("biography", $requestBody)) {
                return $this->errorResponse("key_biography_missing", "Biography is missing");
            }

            if (is_null(trim($requestBody['email']))) {
                return $this->errorResponse("value_email_missing", "Email cannot be null.");
            }
            if (is_null(trim($requestBody['username']))) {
                return $this->errorResponse("value_username_missing", "Username cannot be null.");
            }
            if (is_null(trim($requestBody['display_name']))) {
                return $this->errorResponse("value_display_name_missing", "Display name cannot be null.");
            }
            if (is_null(trim($requestBody['plain_password']))) {
                return $this->errorResponse("value_plain_password_missing", "Plain password cannot be null.");
            }

            $user = $this->userFactory->createOrUpdate($request);

            try {
                $userRecord = $this->auth->createUserWithEmailAndPassword($user->getEmail(), $requestBody['plain_password']);

                $user->setUid($userRecord->uid);
            } catch (AuthException|FirebaseException $e) {
                return $this->errorResponse("firebase_user_creation_failed", "Firebase user could not be created.", $e->getMessage());
            }

            try {
                $this->auth->sendEmailVerificationLink($user->getEmail());
            } catch (FailedToSendActionLink $e) {
                return $this->errorResponse("email_sent_failed", "The verification email could not be sent.", $e->getMessage());
            }

            $this->orm->persist($user);
            $this->orm->flush();

            return $this->successResponse();
        } catch (Exception|Throwable $e) {
            return $this->internalErrorResponse($e->getMessage());
        }
    }

    public function tokenTest(Request $request): JsonResponse
    {
        $token = $request->headers->get("Authorization");

        try {
            $tokenData = $this->tokenEncoder->decode($token);

            return $this->successResponse($tokenData);
        } catch (JWTDecodeFailureException $e) {
            return $this->errorResponse(
                "token_invalid",
                "The presented token is invalid."
            );
        }
    }

    // TODO
    public function checkForEmail(Request $request): JsonResponse
    {
        try {
            return $this->successResponse();
        } catch (Exception|Throwable $e) {
            return $this->internalErrorResponse();
        }
    }

    public function checkForUsername(Request $request): JsonResponse
    {
        try {
            if (is_null(trim($request->get("username")))) {
                return $this->errorResponse("value_username_missing", "Username cannot be null.");
            }

            $username = trim($request->get("username"));

            $pattern = '/^(?!\s)[a-zA-Z0-9_.]{4,20}$/';

            if (!preg_match($pattern, $username)) {
                return $this->errorResponse(
                    "invalid_username",
                    "The username is invalid."
                );
            }

            if ($this->userRepository->findByUsername($username) != null) {
                return $this->errorResponse(
                    "username_taken",
                    "This username is already taken."
                );
            }

            return $this->successResponse();
        } catch (Exception|Throwable $e) {
            return $this->internalErrorResponse(
                exception_message: $e->getMessage()
            );
        }
    }
}