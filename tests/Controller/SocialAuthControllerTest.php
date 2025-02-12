<?php

namespace App\Tests\Controller;

use App\Controller\SocialAuthController;
use App\Service\SocialAuthService;
use App\Service\UserService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SocialAuthControllerTest extends TestCase
{
    private $socialAuthService;
    private $userService;
    private $controller;
    private $container;

    protected function setUp(): void
    {
        $this->socialAuthService = $this->createMock(SocialAuthService::class);
        $this->userService = $this->createMock(UserService::class);
        $this->container = $this->createMock(ContainerInterface::class);
        $this->controller = new SocialAuthController($this->socialAuthService, $this->userService);
        $this->controller->setContainer($this->container);
    }

    public function testLoginWithSocialCallsAuthenticateWithProvider()
    {
        $this->socialAuthService->expects($this->once())
            ->method('authenticateWithProvider')
            ->with('google');

        $this->controller->loginWithSocial('google');
    }

    public function testCallbackLoginWithSocialSuccess()
    {
        $authUser = ['email' => 'test@example.com', 'id' => '123'];
        $this->socialAuthService->expects($this->once())
            ->method('handleCallback')
            ->with('google')
            ->willReturn($authUser);

        $response = $this->controller->callbackLoginWithSocial('google');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertStringContainsString('Sukces', $response->getContent());
    }

    public function testCallbackLoginWithSocialFailure()
    {
        $this->socialAuthService->expects($this->once())
            ->method('handleCallback')
            ->willThrowException(new \Exception('Błąd'));

        $response = $this->controller->callbackLoginWithSocial('google');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertStringContainsString('Blad logowania', $response->getContent());
    }
}
