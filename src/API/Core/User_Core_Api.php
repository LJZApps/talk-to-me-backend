<?php

namespace App\API\Core;

use App\API\Api;
use App\Factory\UserFactory;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class User_Core_Api extends Api
{
    private UserFactory $userFactory;

    public function __construct(
        EntityManagerInterface $em,
        ParameterBagInterface  $param,
        UserFactory            $userFactory
    )
    {
        parent::__construct($em, $param);

        $this->userFactory = $userFactory;
    }

    public function createUser(Request $request): JsonResponse
    {
        try {
            //$user = $this->userFactory->createOrUpdate($request);

            return $this->json([
                "success" => true,
                //"user" => $user
            ]);
        } catch (Exception|Throwable $e) {
            return $this->internalErrorResponse();
        }
    }

    // TODO
    public function checkForEmail(Request $request): JsonResponse
    {
        try {
            return $this->json([
                "success" => true
            ]);
        } catch (Exception|Throwable $e) {
            return $this->internalErrorResponse();
        }
    }

    // TODO
    public function checkForUsername(Request $request): JsonResponse
    {
        try {
            return $this->json([
                "success" => true
            ]);
        } catch (Exception|Throwable $e) {
            return $this->internalErrorResponse();
        }
    }
}