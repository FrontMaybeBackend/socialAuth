<?php

namespace App\Controller;

use App\Service\SocialAuthService;
use App\Service\UserService;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SocialAuthController extends AbstractController
{
    private $socialAuthService;
    private $userService;
    public function __construct(SocialAuthService $socialAuthService, UserService $userService)
    {
        $this->socialAuthService = $socialAuthService;
        $this->userService = $userService;
    }

    #[Route('/social/auth', name: 'app_social_auth')]
    public function index(): Response
    {
        return $this->render('social_auth/index.html.twig');
    }

    #[Route('/login/{provider}', name: 'social_login')]
    public function loginWithSocial(string $provider)
    {
        return $this->socialAuthService->authenticateWithProvider($provider);
    }

    #[Route('/callback/{provider}', name: 'connect_check')]
    public function callbackLoginWithSocial(string $provider)
    {
        try {
            $authUser = $this->socialAuthService->handleCallback($provider);
            return $this->json([
                'message' => 'Sukces, udalo sie zalogowac za pomoca ' . $provider,
                'data' => $authUser,
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Blad logowania: ' . $e->getMessage()
            ], 400);
        }
    }
}
