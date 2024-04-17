<?php

namespace App\Controller;

use App\API\Core\InternalNews_Core_API;
use App\Repository\InternalNewsRepository;
use App\Utils\ResponseUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InternalNewsController extends AbstractController
{
    public function __construct(InternalNews_Core_API $api, InternalNewsRepository $internalNewsRepository, EntityManagerInterface $entityManager, ResponseUtil $util)
    {
        $this->api = $api;
        $this->internalNewsRepository = $internalNewsRepository;
        $this->entityManager = $entityManager;
        $this->util = $util;
    }

    #[Route('/news', name: 'internal_news')]
    public function index(): Response
    {
        $news = $this->internalNewsRepository->getAllNews();
        return $this->render('news/news.twig', [
            'articles' => $news,
        ]);
    }

    #[Route('/news/create', name: 'create_internal_news', methods: ['GET'])]
    public function createNews(Request $request): Response
    {
        return $this->render('news/pages/create.twig');
    }

    #[Route('/api/v1/news', name: 'api_get_news')]
    public function getNews(Request $request): JsonResponse
    {
        return $this->api->getNews($request);
    }
}
