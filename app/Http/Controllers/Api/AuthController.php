<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Application\Services\AuthService;
use App\Application\Services\ExceptionHandlerService;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseController
{
    protected AuthService $authService;

    public function __construct(ExceptionHandlerService $exceptionHandler, AuthService $authService)
    {
        parent::__construct($exceptionHandler);
        $this->authService = $authService;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->handleRequest(fn () =>
            $this->authService->authenticate($request->only(['email', 'password']))
        );
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->handleRequest(fn () =>
            $this->authService->register($request->only(['name', 'email', 'password']))
        );
    }

    public function me(): JsonResponse
    {
        return $this->handleRequest(fn () =>
            $this->authService->getCurrentUserInfo()
        );
    }

    public function logout(): JsonResponse
    {
        return $this->handleRequest(fn () =>
            $this->authService->logoutUser()
        );
    }

    public function refresh(): JsonResponse
    {
        return $this->handleRequest(fn () =>
            $this->authService->refreshToken()
        );
    }
} 