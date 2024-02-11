<?php

namespace App\API\Core;

use App\API\Api;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Kreait\Firebase\Contract\Auth;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class User_Core_Api extends Api
{
    private UserFactory $userFactory;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface      $em,
        ParameterBagInterface       $param,
        Auth                        $auth,
        UserFactory                 $userFactory,
        UserRepository              $userRepository,
        UserPasswordHasherInterface $passwordHasher,
    )
    {
        parent::__construct($em, $param, $auth);

        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function test(Request $request): JsonResponse
    {
        return $this->json([
            "success" => true,
            "message" => "Das ist eine Test-Nachricht."
        ]);
    }
}