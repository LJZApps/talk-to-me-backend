<?php

namespace App\Controller;

use App\API\Core\Posts_Core_Api;
use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    public function __construct(Posts_Core_Api $api, PostRepository $postRepository, EntityManagerInterface $entityManager)
    {
        $this->api = $api;
        $this->postRepository = $postRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/api/v1/posts', name: 'api_get_posts', methods: ['GET'])]
    public function apiGetPosts(Request $request): JsonResponse
    {
        $posts = $this->entityManager->getRepository(Post::class)->findAll();
        $result = [];

        foreach ($posts as $post) {
            $userData = null;

            $createdBy = $post->getCreatedBy();
            $userData = [
                "id" => $createdBy->getId(),
                "username" => $createdBy->getUsername(),
                "display_name" => $createdBy->getDisplayName(),
            ];

            $result[] = [
                "id" => $post->getId(),
                "text" => $post->getText(),
                "likes" => 0,
                "comments" => [],
                "created_by" => $userData,
                "created_at" => $post->getCreatedAt()->getTimestamp(),
                "updated_at" => $post->getUpdatedAt() ? $post->getUpdatedAt()->getTimestamp() : null,
            ];
        }

        return $this->json($result);
    }

    #[Route('/api/v1/posts', name: 'api_create_posts', methods: ['POST'])]
    public function apiCreatePost(Request $request): JsonResponse
    {
        return $this->api->createPost($request);
    }
}
