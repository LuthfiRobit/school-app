<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Ambil user beserta role-nya.
     */
    public function getAllWithRoles(): Collection
    {
        return $this->model->with('roles')->get();
    }

    /**
     * Cek apakah username sudah digunakan.
     */
    public function usernameExists(string $username): bool
    {
        return $this->model->where('username', $username)->exists();
    }
}
