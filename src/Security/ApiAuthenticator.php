<?php

namespace App\Security;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use ReallySimpleJWT\Token;
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
        private readonly UserRepository $userRepository
    ) {}

    public function supports(Request $request): ?bool
    {
        if(!is_null($request->headers->get('Authorization'))) {
            return str_starts_with($request->headers->get('Authorization', ''), 'Bearer ');
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function authenticate(Request $request): Passport
    {
        if ($request->headers->has("Authorization")) {
            $token = $request->headers->get('Authorization');
            $bearerToken = null;

            // Den Bearer-Token aus dem Header extrahieren
            if (preg_match('/Bearer\s+(.*)/', $token, $matches)) {
                $bearerToken = $matches[1];
            }

            try {
                //$isExpired = Token::validateExpiration($token);

                throw new AuthenticationException(
                    message: $bearerToken
                );

                if ($isExpired) {
                    throw new AuthenticationException(
                        message: 'Token expired!'
                    );
                }

                $userId = null;

                $tokenData = Token::getPayload($bearerToken);

                if (array_key_exists("user_id", $tokenData)) {
                    $userId = $tokenData['user_id'];
                }

                /*
                if ($userId != null) {
                    return new Passport(
                        userBadge: new UserBadge(
                            userIdentifier: (string) $userId,
                            userLoader: function (string $userIdentifier) {
                                return $this->userRepository->findByID((int) $userIdentifier);
                            }
                        ),
                        credentials: new PasswordCredentials($token)
                    );
                } else {
                    throw new AuthenticationException(
                        message: 'Invalid token!'
                    );
                }
                */
            } catch (JWTDecodeFailureException $e) {
                throw new AuthenticationException(
                    message: 'Invalid token!'
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
            "message" => "token valid"
        ]);
        // return null;
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