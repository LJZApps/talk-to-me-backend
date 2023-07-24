<?php

namespace App\Controller;

use App\API\Core\User_Core_Api;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    private User_Core_Api $api;

    public function __construct(User_Core_Api $api)
    {
        $this->api = $api;
    }

    public function API_Login(Request $request)
    {
        return $this->api->login($request);
    }

    public function API_CreateUser(Request $request): JsonResponse
    {
        return $this->api->createUser($request);
    }

    public function API_CheckForUsername(Request $request): JsonResponse
    {
        return $this->api->checkForUsername($request);
    }

    // TODO
    public function API_GetUser(Request $request): JsonResponse
    {
        return $this->json([
           "success" => true
        ]);
    }
}
