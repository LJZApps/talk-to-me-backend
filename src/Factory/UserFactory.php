<?php

namespace App\Factory;

use App\Entity\User;
use App\Interfaces\EntityFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class UserFactory implements EntityFactoryInterface
{

    public function createOrUpdate(Request $request, $entity = null): User
    {
        // TODO
        return new User();
    }
}