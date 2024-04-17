<?php

namespace App\Security;

use App\Repository\UserRepository;
use ReallySimpleJWT\Token;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use function PHPUnit\Framework\throwException;

class ApiAuthenticator extends AbstractAuthenticator
{
    private $userRepository;

    public function __construct(UserRepository $userRepository, ParameterBagInterface $parameterBag)
    {
        $this->userRepository = $userRepository;
        $this->params = $parameterBag;
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): Passport
    {
        $authorizationHeader = $request->headers->get('Authorization');
        list($bearer, $jwt) = explode(" ", $authorizationHeader);

        $secret = $this->params->get('jwt.secret');

        $valid = Token::validate($jwt, $secret);
        if (!$valid) {
            throw new AuthenticationException('Invalid token');
        }

        $validateExpiration = Token::validateExpiration($jwt);
        if (!$validateExpiration) {
            throw new AuthenticationException('Token expired');
        }

        $parsedJwt = Token::parser($jwt); // Set your JWT Secret when validating and parsing
        $payload = $parsedJwt->parse()->getPayload();
        $userId = $payload['uid'];

        return new SelfValidatingPassport(
            new UserBadge($userId,
                function ($userIdentifier) {
                    return $this->userRepository->find($userIdentifier);
                }
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // no need to return a Response if authenticate() method passes
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            [
                'success' => false,
                'message' => $exception->getMessage()
            ],
            Response::HTTP_UNAUTHORIZED
        );
    }
}