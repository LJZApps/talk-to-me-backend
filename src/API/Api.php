<?php

namespace App\API;

use App\Entity\Notification;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Kreait\Firebase\Contract\Auth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class Api extends AbstractController
{
    protected $orm;
    protected $db;

    protected $parameters;

    // Firebase
    protected Auth $auth;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $param, Auth $firebaseAuth)
    {
        $this->orm = $em;
        $this->db = $em->getConnection();

        $this->parameters = $param;

        // Firebase
        $this->auth = $firebaseAuth;
    }

    protected function compareDates($timeA, $timeB, $format)
    {
        $dateA = $timeA instanceof Datetime ? $timeA : (is_numeric($timeA) ? (new Datetime())->setTimestamp($timeA) : (new Datetime("" . $timeA)));
        $dateB = $timeB instanceof Datetime ? $timeB : (is_numeric($timeB) ? (new Datetime())->setTimestamp($timeB) : (new Datetime("" . $timeB)));
        return $dateA->format($format) == $dateB->format($format) ? 0 : ($dateA->getTimestamp() < $dateB->getTimestamp() ? 1 : -1);
    }

    protected function generateRandomString($length = 10)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

    public function errorResponse(string $error_code, string $error_message, string $exception_message = null): JsonResponse
    {
        if (!is_null($exception_message)) {
            return $this->json([
                "success" => false,
                "error_code" => $error_code,
                "error_message" => $error_message,
                "exception_message" => $exception_message
            ], 400);
        }

        return $this->json([
            "success" => false,
            "error_code" => $error_code,
            "error_message" => $error_message
        ], 400);
    }

    public function internalErrorResponse(string $exception_message = null): JsonResponse
    {
        if (!is_null($exception_message)) {
            return $this->json([
                "success" => false,
                "error_code" => "internal_error",
                "error_message" => "Something went wrong on our end.",
                "exception_message" => $exception_message
            ], 500);
        }

        return $this->json([
            "success" => false,
            "error_code" => "internal_error",
            "error_message" => "Something went wrong on our end."
        ], 500);
    }
}