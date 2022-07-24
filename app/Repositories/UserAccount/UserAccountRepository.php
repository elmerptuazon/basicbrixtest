<?php

namespace App\Repositories\UserAccount;

use App\Repositories\Repository;
use App\Models\User;

class UserAccountRepository extends Repository implements IUserAccountRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function getByUsername(string $usernameField, string $email)
    {
        return $this->model->where($usernameField, '=', $email)->first();
    }
}
