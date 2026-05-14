<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface UserRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Ambil user beserta role-nya.
     */
    public function getAllWithRoles(): Collection;
}
