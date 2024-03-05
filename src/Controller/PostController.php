<?php

namespace App\Controller;

use App\API\Core\Posts_Core_Api;
use App\Entity\Post;
use App\Repository\PostRepository;
use App\Utils\ResponseUtil;
use Doctrine\ORM\EntityManagerInterface;
use Phpml\Association\Apriori;
use Phpml\Classification\NaiveBayes;
use Phpml\Classification\SVC;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\SupportVectorMachine\Kernel;
use Phpml\Tokenization\WhitespaceTokenizer;
use Phpml\Tokenization\WordTokenizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    public function __construct(Posts_Core_Api $api, PostRepository $postRepository, EntityManagerInterface $entityManager, ResponseUtil $util)
    {
        $this->api = $api;
        $this->postRepository = $postRepository;
        $this->entityManager = $entityManager;
        $this->util = $util;
    }

    #[Route('/api/v1/posts/{id}', name: 'api_get_post_by_id', methods: ['GET'])]
    public function apiGetPostById(Request $request, $id): JsonResponse
    {
        $post = $this->entityManager->find(Post::class, $id);

        if (is_null($post)) {
            return $this->util->errorResponse(
                error_code: "invalid_id",
                error_message: "The provided id is invalid.",
                status: Response::HTTP_NOT_FOUND
            );
        }

        $createdBy = $post->getCreatedBy();
        $userData = [
            "id" => $createdBy->getId(),
            "username" => $createdBy->getUsername(),
            "display_name" => $createdBy->getDisplayName(),
        ];

        $result = [
            "id" => $post->getId(),
            "text" => $post->getText(),
            "likes" => 0,
            "comments" => [],
            "created_by" => $userData,
            "created_at" => $post->getCreatedAt()->getTimestamp(),
            "updated_at" => $post->getUpdatedAt() ? $post->getUpdatedAt()->getTimestamp() : null,
        ];

        return $this->json($result);
    }

    #[Route('/api/v1/posts', name: 'api_get_posts', methods: ['GET'])]
    public function apiGetPosts(Request $request): JsonResponse
    {
        $search = $request->query->get("q", null);

        $posts = null;
        if (is_null($search)) {
            $posts = $this->entityManager->getRepository(Post::class)->findAll();
        } else {
            $posts = $this->entityManager->getRepository(Post::class)->findByText($search);
        }

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