<?php

namespace App\Tests\Service;

use App\Service\SocialAuthService;
use App\Service\UserService;
use GuzzleHttp\Client;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use PHPUnit\Framework\TestCase;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;

class SocialAuthServiceTest extends TestCase
{
    private $clientRegistry;
    private $userService;
    private $service;

    protected function setUp(): void
    {
        $this->clientRegistry = $this->createMock(ClientRegistry::class);
        $this->userService = $this->createMock(UserService::class);
        $this->service = new SocialAuthService($this->clientRegistry, $this->userService);
    }

    public function testAuthenticateWithProvider()
    {
        $client = $this->createMock(OAuth2ClientInterface::class);
        $this->clientRegistry->method('getClient')->willReturn($client);
        $client->expects($this->once())->method('redirect');
        $this->service->authenticateWithProvider('google');
    }

}
