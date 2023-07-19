<?php

namespace App\Controller\API;

use App\DataServices\UserDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiUserController extends AbstractController
{
    const STATUS_SENT = "SENT";
    const STATUS_RECEIVED = "RECEIVED";
    const STATUS_READ = "READ";

    private UserDataService $userDataService;

    public function __construct(UserDataService $userDataService)
    {
        $this->userDataService = $userDataService;
    }

    public function getUserData(mixed $uid, Request $request): JsonResponse
    {
        return $this->userDataService->getUserData($uid);
    }


    public function createUser(Request $request): JsonResponse
    {
        $requestJson = json_decode($request->getContent(), true);

        $email = $requestJson['email'];
        $plainPassword = $requestJson['plain_password'];
        $displayName = $requestJson['display_name'];

        return $this->userDataService->createUser($email, $plainPassword, $displayName);
    }
}
