<?php

namespace App\Security;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class JwtAuthenticatorStaticFactory
{
    public static function createJwtAuthenticator(
        UserRepository $userRepository,
        JWTTokenManagerInterface $tokenManager
    ): ApiAuthenticator {
        return new ApiAuthenticator(
            userRepository: $userRepository,
            tokenManager: $tokenManager
        );
    }
}