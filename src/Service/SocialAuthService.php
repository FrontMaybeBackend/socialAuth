<?php

namespace App\Service;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;

class SocialAuthService
{
    private $clientRegistry;
    private $userService;

    public function __construct(ClientRegistry $clientRegistry, UserService $userService)
    {
        $this->clientRegistry = $clientRegistry;
        $this->userService = $userService;
    }

    public function authenticateWithProvider(string $provider)
    {
        return $this->clientRegistry
            ->getClient($provider)
            ->redirect([
                'email'
            ], [
                'state' => bin2hex(random_bytes(32))
            ]);
    }

    public function handleCallback(string $provider)
    {
        $client = $this->clientRegistry->getClient($provider);
        $accessToken = $client->getAccessToken();
        $socialUser = $client->fetchUserFromToken($accessToken);

        return $this->userService->handleOAuthUser(
            $socialUser->getEmail(),
            $provider,
            $socialUser->getId()
        );
    }
}
