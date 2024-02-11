<?php

namespace App\API\Core;

use App\API\Api;
use App\Entity\Post;
use App\Entity\User;
use App\Factory\PostFactory;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Posts_Core_Api extends Api
{
    public function __construct(EntityManagerInterface $em, ParameterBagInterface $param, PostRepository $postRepository, PostFactory $factory)
    {
        parent::__construct($em, $param);

        $this->postRepository = $postRepository;
        $this->em = $em;
        $this->factory = $factory;
    }

    public function createPost(Request $request): JsonResponse|NotFoundHttpException
    {
        $post = $this->factory->createOrUpdate($request);
        if (is_null($post)) {
            return $this->createNotFoundException();
        }

        $this->em->persist($post);
        $this->em->flush();

        return $this->successResponse();
    }
}