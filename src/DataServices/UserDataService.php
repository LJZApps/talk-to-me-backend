<?php

namespace App\DataServices;

use App\Utils\ResponseUtil;
use Kreait\Firebase\Contract\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserDataService
{
    private Auth $auth;
    private ResponseUtil $responseUtil;

    public function __construct(Auth $firebaseAuth, ResponseUtil $responseUtil)
    {
        // Firebase
        $this->auth = $firebaseAuth;

        // Utils
        $this->responseUtil = $responseUtil;
    }

    /**
     * Returns the user information from the given uid
     * @param string $uid UID from user
     * @return JsonResponse
     */
    public function getUserData(string $uid): JsonResponse {
        try {
            $user = $this->auth->getUser($uid);

            return new JsonResponse([
                'success' => true,
                'user' => $user
            ]);
        } catch (\Exception|\Throwable $m) {
            return $this->responseUtil->errorResponse(
                error_code: 'err_get_user',
                error_message: 'User information could not be retrieved.',
                extra_data: [
                    'exception_message' => $m->getMessage()
                ]
            );
        }
    }

    private function doesUserExist(string $email): bool
    {
        try {
            $this->auth->getUserByEmail($email);
            return true;
        } catch (\Exception|\Throwable $m) {
            return false;
        }
    }

    /**
     * Creates a new user with the given information
     * @param string $email Email
     * @param string $plainPassword Password
     * @param string $displayName Display name
     * @return JsonResponse
     */
    public function createUser(string $email, string $plainPassword, string $displayName): JsonResponse {
        try {
            if ($this->doesUserExist($email)) {
                return $this->responseUtil->errorResponse(
                    error_code: 'user_already_exists',
                    error_message: 'A user with this email already exists'
                );
            }

            $userData = [
                'email' => $email,
                'emailVerified' => false,
                'password' => $plainPassword,
                'displayName' => $displayName,
            ];

            $createdUser = $this->auth->createUser($userData);

            $signInResult = $this->auth->signInWithEmailAndPassword($email, $plainPassword);

            $this->auth->setCustomUserClaims($createdUser->uid, [
                'banned' => false,
                'admin' => false
            ]);

            return new JsonResponse([
                'success' => true,
                'user_uid' => $createdUser->uid,
                'sign_in_data' => $signInResult->asTokenResponse()
            ]);
        } catch (\Exception|\Throwable $m) {
            return $this->responseUtil->errorResponse(
                error_code: 'err_create_user',
                error_message: 'The user could not be created.',
                extra_data: [
                    'exception_message' => $m->getMessage()
                ]
            );
        }
    }
}