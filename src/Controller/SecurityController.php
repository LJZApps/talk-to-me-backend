<?php

namespace App\Controller;

use App\API\Core\Login_Core_Api;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    public function __construct(Login_Core_Api $api)
    {
        $this->api = $api;
    }

    #[Route(path: '/api/v1/login', name: 'api_login', methods: ['POST'])]
    public function apiLogin(Request $request)
    {
        return $this->api->login($request);
    }

    #[Route(path: '/api/v1/register', name: 'api_register')]
    public function apiRegister(Request $request)
    {
        return $this->api->register($request);
    }

    #[Route(path: '/api/v1/validate-email', name: 'api_email_validation')]
    public function apiValidateEmail(Request $request)
    {
        return $this->api->validateEmail($request);
    }
}