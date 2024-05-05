<?php

namespace App\API\Core;

use App\API\Api;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use ReallySimpleJWT\Token;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class Login_Core_Api extends Api
{
    public function __construct(EntityManagerInterface $em, ParameterBagInterface $param, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct($em, $param);

        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function register(Request $request): JsonResponse
    {
        return $this->successResponse();
    }

    public function validateEmail(Request $request): JsonResponse
    {
        $email = $request->query->get("email", "");

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->errorResponse(
                error_code: "invalid_email",
                error_message: "The provided email is invalid.",
                status: 401
            );
        }

        $user = $this->userRepository->findUserByEmail($email);
        if ($user != null) {
            // TODO check banned, locked and deactivated status

            // User already exists, redirect to login page
            return $this->json([
                "success" => true,
                "user_status" => "exists"
            ]);
        }

        // Email is available, redirect to register page
        return $this->json([
            "success" => true,
            "user_status" => "available"
        ]);
    }

    public function login(Request $request): JsonResponse {
        $credentials = [
            "username" => (string) $request->query->get("username", ""),
            "password" => (string) $request->query->get("password", "")
        ];

        if (strlen(trim($credentials["username"])) == 0) {
            return $this->errorResponse(
                error_code: "empty_email",
                error_message: "Please enter a email.",
                status: 401
            );
        }

        if (strlen(trim($credentials["password"])) == 0) {
            return $this->errorResponse(
                error_code: "empty_password",
                error_message: "Please enter a password.",
                status: 401
            );
        }

        if (!filter_var($credentials["username"], FILTER_VALIDATE_EMAIL)) {
            return $this->errorResponse(
                "invalid_email",
                "The provided email is invalid.",
                status: 401
            );
        }

        $user = $this->userRepository->findUserByEmail($credentials["username"]);
        if (is_null($user)) {
            return $this->errorResponse(
                error_code: "user_not_found",
                error_message: "A user with this email does not exist.",
                status: 401
            );
        }

        if (!$this->passwordHasher->isPasswordValid($user, $credentials["password"])) {
            return $this->errorResponse(
                error_code: "invalid_password",
                error_message: "The provided password is invalid.",
                status: 401
            );
        }

        // Access token (invalidates after 1 week)
        $accessTokenPayload = [
            'iat' => time(),
            'uid' => $user->getId(),
            'exp' => time() + (60*60*24*7)
        ];
        $tokenSecret = $this->getParameter('jwt.secret');
        $accessToken = Token::customPayload($accessTokenPayload, $tokenSecret);

        // Refresh token (invalidates after 3 months)
        $refreshTokenPayload = [
            'iat' => time(),
            'uid' => $user->getId(),
            'exp' => time() + (90*24*60*60),
        ];
        $refreshToken = Token::customPayload($refreshTokenPayload, $tokenSecret);

        $parsedToken = Token::parser($accessToken);
        $parsedToken->parse()->getPayload();

        //Token::validateExpiration();

        return $this->json([
            "success" => true,
            "access_token" => [
                "token" => $accessToken,
                "exp" => $accessTokenPayload["exp"]
            ],
            "refresh_token" => [
                "token" => $refreshToken,
                "exp" => $refreshTokenPayload["exp"]
            ]
        ]);
    }
}