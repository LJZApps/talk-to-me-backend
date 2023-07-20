<?php

namespace App\API;

use App\Entity\Notification;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class Api extends AbstractController
{

    /**
     *
     * @var EntityManager
     */
    protected $orm;
    protected $db;

    protected $parameters;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $param)
    {
        $this->orm = $em;
        $this->db = $em->getConnection();

        $this->parameters = $param;
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

    public function errorResponse(string $error_code, string $error_message)
    {
        $json = array(
            "success" => false,
            "error_code" => $error_code,
            "error_message" => $error_message
        );
        return $this->json($json, 400);
    }

    public function internalErrorResponse(): JsonResponse
    {
        return $this->json([
            "success" => false,
            "error_code" => "internal_error",
            "error_message" => "Something went wrong on our end."
        ], 500);
    }

}