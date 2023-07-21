<?php

namespace App\Factory;

use App\Entity\User;
use App\Interfaces\EntityFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserFactory implements EntityFactoryInterface
{
    public function createOrUpdate(Request $request, $entity = null): User
    {
        $user = new User();

        $requestBody = json_decode($request->getContent(), true);

        $username = $requestBody['username'];
        $email = $requestBody['email'];
        $displayName = $requestBody['display_name'];
        $plainPassword = $requestBody['plain_password'];

        // Password hasher
        $passwordHasherFactory = new PasswordHasherFactory([
            // auto hasher with default options for the User class (and children)
            User::class => ['algorithm' => 'auto'],

            // auto hasher with custom options for all PasswordAuthenticatedUserInterface instances
            PasswordAuthenticatedUserInterface::class => [
                'algorithm' => 'auto',
                'cost'      => 15,
            ],
        ]);
        $passwordHasher = new UserPasswordHasher($passwordHasherFactory);
        $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);

        /*
        // Default user settings
        $userSettings = new UserSetting();
        $userSettings->setMarkMessagesAsRead(true);
        $userSettings->setMessagePolicy()
        */

        $user->setUsername($username);
        $user->setEmail($email);
        $user->setDisplayName($displayName);
        $user->setPassword($hashedPassword);

        return $user;
    }
}