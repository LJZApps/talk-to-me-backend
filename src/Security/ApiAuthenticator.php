<?php

namespace App\Security;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class ApiAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly JWTTokenManagerInterface $tokenManager
    ) {}

    public function supports(Request $request): ?bool
    {
        return $request->headers->has("Authorization");
    }

    /**
     * @inheritDoc
     */
    public function authenticate(Request $request): Passport
    {
        if ($request->headers->has("Authorization")) {
            $token = $request->headers->get('Authorization');

            try {
                $tokenData = $this->tokenManager->parse($token);

                $userId = null;

                if (array_key_exists("user_id", $tokenData)) {
                    $userId = $tokenData['user_id'];
                }

                if ($userId != null) {
                    return new Passport(
                        userBadge: new UserBadge(
                            userIdentifier: (string) $userId,
                            userLoader: function (string $userIdentifier) {
                                return $this->userRepository->findByID((int) $userIdentifier);
                            }
                        ),
                        credentials: new PasswordCredentials("")
                    );
                } else {
                    throw new AuthenticationException(
                        message: 'Invalid token!'
                    );
                }
            } catch (JWTDecodeFailureException $e) {
                throw new AuthenticationException(
                    message: 'Invalid token 17000!'
                );
            }
        }

        throw new AuthenticationException(
            message: 'No token!'
        );
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new JsonResponse([
            "success" => false,
            "message" => "This shit is pure shit."
        ]);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            "success" => false,
            "error_code" => "auth_error",
            "error_messge" => $exception->getMessage()
        ]);
    }
}