<?php

namespace App\Services\Auth;

use App\Enums\TokenNames;
use App\Repositories\Client\IClientRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\NewAccessToken;

class AuthService implements IAuthService
{
    public IUserAccountRepository $userAccounts;
    public IClientRepository $clients;

    public function __construct(IUserAccountRepository $userAccts,
       IClientRepository $clients)
    {
        $this->userAccounts = $userAccts;
        $this->clients = $clients;
    }


    /**
     * Creates a new UserAccount record
     *
     * @param array $newUser
     * @return mixed
     */
    public function register(array $newUser)
    {
        $newUser['password'] = Hash::make($newUser['password']);
        return $this->userAccounts->create($newUser);
    }

    /**
     * Attempts to authenticate the user with the
     * provided credentials
     *
     * @param string $usernameField
     * @param array $creds
     * @param string $ip
     * @return NewAccessToken
     * @throws ValidationException
     */
    public function login(string $usernameField, array $creds, string $ip): object
    {
        // $throttleKey = $this->throttleKey($creds[$usernameField], $ip);
        // $this->ensureAccountIsNotLockedOut($throttleKey);
        
        $user = $this->userAccounts->getByUsername($usernameField, $creds[$usernameField]);
        if(!$user) $this->loginFailed();

        $passwordMatched = Hash::check($creds['password'], $user->password);
        if(!$user || !$passwordMatched) {
            // RateLimiter::hit($throttleKey);
            $this->loginFailed();
        }
        
        $user->deleteAllTokens();
        // RateLimiter::clear($throttleKey);

        $result = (object)array(
            'token'=>$user->createToken(TokenNames::webToken),
            'user'=>$user
        );
        
        return $result;
    }

    /**
     * Authenticates Client Applications
     *
     * @param string $clientId
     * @param string $clientSecret
     * @return NewAccessToken
     * @throws ValidationException
     */
    public function clientLogin(string $clientId, string $clientSecret): NewAccessToken
    {
        $client = $this->clients->getClient($clientId);

        if(!$client || !Hash::check($clientSecret, $client->client_secret))
        {
            throw ValidationException::withMessages([
                'client' => 'Invalid Client Credentials'
            ]);
        }

        $client->tokens()->delete();
        return $client->createToken(TokenNames::clientToken);
    }

    private function ensureAccountIsNotLockedOut(string $throttleKey)
    {
        if($this->isRateLimited($throttleKey)) {
            throw ValidationException::withMessages([
                'account' => 'Account has been locked out. Due to subsequent failed attempts.'
            ]);
        }
    }

    private function throttleKey(string $username, string $ip): string
    {
        return Str::lower($username.'|'.$ip);
    }

    private function isRateLimited(string $throttleKey): bool
    {
        if(!RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return false;
        }

        return true;
    }

    private function loginFailed()
    {
        throw ValidationException::withMessages([
            'account' => 'Login Failed.'
        ]);
    }
}
