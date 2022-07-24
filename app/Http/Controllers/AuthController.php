<?php

namespace App\Http\Controllers;

use App\Enums\UsernameTypes;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\Auth\IAuthService;
use App\Services\Encryption\IEncryptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\Responses\IResponseService;
use App\Enums\SuccessMessages;

class AuthController extends Controller
{
    private IAuthService $authService;
    private IResponseService $responseService;
    
    public function __construct(IAuthService $authService,
                                IResponseService $responseService)
    {
        $this->authService = $authService;
        $this->responseService = $responseService;
    }

    /**
     * Registers a user
     *
     * @param RegisterUserRequest $request
     * @return JsonResponse
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
 
        $newUser = $request->validated();
        $user = $this->authService->register($newUser);

        return $this->responseService->successResponse($user->toArray(), SuccessMessages::accountRegistered);
    }


    /**
     * Authenticate a user
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $login = $request->validated();
        $ip = $request->ip();
     
        $result = $this->authService->login(UsernameTypes::Email, $login, $ip);
        $response = [
            'access_token' => $result->token->plainTextToken,
            'email' => $result->user->email,
            'name' => $result->user->name
        ];

        return $this->responseService->successResponse($response, SuccessMessages::loginSuccessful);
    }
}
