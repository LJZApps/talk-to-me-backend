<?php

namespace App\API\Core;

use App\API\Api;
use App\Repository\InternalNewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class InternalNews_Core_API extends Api
{
    public function __construct(EntityManagerInterface $em, ParameterBagInterface $param, InternalNewsRepository $internalNewsRepository)
    {
        parent::__construct($em, $param);

        $this->internalNewsRepository = $internalNewsRepository;
    }

    public function getNews(Request $request): JsonResponse
    {
        $news = $this->internalNewsRepository->findAll();

        $result = [];

        foreach ($news as $article) {
            $result[] = [
                'id' => $article->getId(),
                'created_by' => $article->getCreatedBy()->getDisplayName(),
                'text' => $article->getText(),
                'created_at' => $article->getCreatedAt(),
                'updated_at' => $article->getUpdatedAt(),
                'title' => $article->getTitle(),
            ];
        }

        return $this->json($result);
    }
}