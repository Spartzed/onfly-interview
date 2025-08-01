<?php

namespace App\Application\Services;

use App\Domain\User\Entities\User;
use App\Application\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private ResponseService $responseService
    ) {}

    public function attemptLogin(array $credentials): ?string
    {
        return auth('api')->attempt($credentials);
    }

    public function login(User $user): ?string
    {
        return auth('api')->login($user);
    }

    public function logout(): void
    {
        auth('api')->logout();
    }

    /**
     * @return string|null
     */
    public function refresh(): ?string
    {
        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = auth('api');
        return $guard->refresh();
    }

    public function getCurrentUser(): ?User
    {
        return auth('api')->user();
    }

    /**
     * @return int
     */
    public function getTokenTTL(): int
    {
        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = auth('api');
        return $guard->factory()->getTTL() * 60;
    }

    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
        ]);
    }

    public function formatUserResponse(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];
    }

    public function formatAuthResponse(string $token, User $user): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->getTokenTTL(),
            'user' => $this->formatUserResponse($user),
        ];
    }

    public function authenticate(array $credentials): JsonResponse
    {
        $token = $this->attemptLogin($credentials);
        
        if (!$token) {
            return $this->responseService->error('As credenciais fornecidas estão incorretas.', 401);
        }

        $user = $this->getCurrentUser();
        return response()->json($this->formatAuthResponse($token, $user));
    }

    public function register(array $data): JsonResponse
    {
        $user = $this->createUser($data);
        $token = $this->login($user);

        return response()->json($this->formatAuthResponse($token, $user), 201);
    }

    public function getCurrentUserInfo(): JsonResponse
    {
        $user = $this->getCurrentUser();
        return response()->json(['user' => $this->formatUserResponse($user)]);
    }

    public function logoutUser(): JsonResponse
    {
        $this->logout();
        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    public function refreshToken(): JsonResponse
    {
        $token = $this->refresh();
        $user = $this->getCurrentUser();

        return response()->json($this->formatAuthResponse($token, $user));
    }
} 