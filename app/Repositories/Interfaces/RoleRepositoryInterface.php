<?php

namespace App\Repositories\Interfaces;

interface RoleRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get all roles with their permissions count/names.
     */
    public function getAllWithPermissions(): array;

    /**
     * Get all permissions grouped by module.
     */
    public function getGroupedPermissions(): array;

    /**
     * Sync permissions to a role.
     */
    public function syncPermissions(int $roleId, array $permissions): void;
}
