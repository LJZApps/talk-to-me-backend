<?php

namespace App\Factory;

use App\Entity\Post;
use App\Entity\User;
use App\Interfaces\EntityFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class PostFactory implements EntityFactoryInterface
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createOrUpdate(Request $request, $entity = null)
    {
        if (is_null($entity)) {
            $entity = new Post();
        }

        $text = (string) $request->query->get("text", "");
        $createdById = (int) $request->query->get("created_by", "");

        $createdBy = $this->entityManager->find(User::class, $createdById);

        $entity->setText($text);
        $entity->setCreatedBy($createdBy);

        return $entity;
    }
}