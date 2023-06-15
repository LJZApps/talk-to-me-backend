<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiUserController extends AbstractController
{
    public function createUser(): JsonResponse
    {
        return $this->json([
            'success' => true,
            'message' => 'Welcome in your first ever self-made API'
        ]);
    }
}
