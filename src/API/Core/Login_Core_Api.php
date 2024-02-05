<?php

namespace App\API\Core;

use App\API\Api;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Kreait\Firebase\Contract\Auth;
use ReallySimpleJWT\Token;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class Login_Core_Api extends Api
{
    public function __construct(EntityManagerInterface $em, ParameterBagInterface $param, Auth $firebaseAuth, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct($em, $param, $firebaseAuth);

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
                "invalid_email",
                "The provided email is invalid."
            );
        }

        $user = $this->userRepository->findUserByEmail($email);
        if ($user != null) {
            // TODO check banned, locked and deactivated status
            /*
            if ($user->isBanned()) {
                return $this->json([
                    "success" => true,
                    "user_status" => "banned"
                ]);
            }
            */

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
            "email" => (string) $request->query->get("email", ""),
            "password" => (string) $request->query->get("password", "")
        ];

        if (strlen(trim($credentials["email"])) == 0) {
            return $this->errorResponse(
                error_code: "empty_email",
                error_message: "Please enter a email."
            );
        }

        if (strlen(trim($credentials["password"])) == 0) {
            return $this->errorResponse(
                error_code: "empty_password",
                error_message: "Please enter a password."
            );
        }

        if (!filter_var($credentials["email"], FILTER_VALIDATE_EMAIL)) {
            return $this->errorResponse(
                "invalid_email",
                "The provided email is invalid."
            );
        }

        $user = $this->userRepository->findUserByEmail($credentials["email"]);
        if (is_null($user)) {
            return $this->errorResponse(
                error_code: "user_not_found",
                error_message: "A user with this email does not exist."
            );
        }

        if (!$this->passwordHasher->isPasswordValid($user, $credentials["password"])) {
            return $this->errorResponse(
                error_code: "invalid_password",
                error_message: "The provided password is invalid."
            );
        }

        // Access token (invalidates after 1 week)
        $accessTokenPayload = [
            'iat' => time(),
            'uid' => $user->getId(),
            'exp' => time() + (60*60*24*7),
        ];
        $accessTokenSecret = $this->generateSecret(30);
        $accessToken = Token::customPayload($accessTokenPayload, $accessTokenSecret);

        // Refresh token (invalidates after 3 months)
        $refreshTokenPayload = [
            'iat' => time(),
            'uid' => $user->getId(),
            'exp' => time() + (90*24*60*60),
        ];
        $refreshTokenSecret = $this->generateSecret(30);
        $refreshToken = Token::customPayload($refreshTokenPayload, $refreshTokenSecret);

        Token::getPayload($refreshToken);

        return $this->json([
            "success" => true,
            "access_token" => [
                "token" => $accessToken,
                "secret" => $accessTokenSecret,
                "exp" => $accessTokenPayload["exp"]
            ],
            "refresh_token" => [
                "token" => $refreshToken,
                "secret" => $refreshTokenSecret,
                "exp" => $refreshTokenPayload["exp"]
            ]
        ]);
    }
}