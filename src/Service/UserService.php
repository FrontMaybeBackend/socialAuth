<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handleOAuthUser($email, string $provider, string $providerId)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $email
        ]);

        if (!$user) {
            $user = new User();
            $user->setEmail($email);
            $user->setPassword(bin2hex(random_bytes(16)));
        }

        $user->setProviderId($providerId);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

}
