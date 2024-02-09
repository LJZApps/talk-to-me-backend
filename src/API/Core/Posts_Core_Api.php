<?php

namespace App\API\Core;

use App\API\Api;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class Posts_Core_Api extends Api
{
    public function __construct(EntityManagerInterface $em, ParameterBagInterface $param, PostRepository $postRepository)
    {
        parent::__construct($em, $param);

        $this->postRepository = $postRepository;
        $this->em = $em;
    }

    public function getPosts(Request $request): array
    {
        $posts = $this->postRepository->findAll();

        return $posts;
    }

    public function createPost(Request $request): JsonResponse
    {
        $title = (string) $request->query->get("title", "");
        $text = (string) $request->query->get("text", "");
        $createdById = (int) $request->query->get("created_by", "");

        $createdBy = $this->em->find(User::class, $createdById);

        $post = new Post();
        $post->setTitle($title);
        $post->setText($text);
        $post->setCreatedBy($createdBy);
        $post->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($post);
        $this->em->flush();

        return $this->successResponse();
    }
}