<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Collection;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    /**
     * Mengambil semua role beserta nama-nama permissionnya.
     */
    public function getAllWithPermissions(): array
    {
        return $this->model->with('permissions')->get()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->name, // Menggunakan name sebagai slug untuk tampilan
                'scope' => $role->scope,
                'permissions' => $role->permissions->pluck('name')->toArray()
            ];
        })->toArray();
    }

    /**
     * Mengelompokkan permission berdasarkan prefix (modul).
     * Contoh: 'akademik.tahun-pelajaran.view' dikelompokkan ke grup 'Akademik'.
     */
    public function getGroupedPermissions(): array
    {
        $permissions = Permission::all();
        $grouped = [];

        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            
            // Pengelompokan: Module -> Resource -> Action
            $module = (count($parts) > 0) ? ucfirst($parts[0]) : 'Umum';
            $resource = (count($parts) > 1) ? ucfirst(str_replace('-', ' ', $parts[1])) : 'General';
            $action = (count($parts) > 2) ? ucfirst($parts[2]) : 'Akses';

            $grouped[$module][$resource][] = [
                'id' => $permission->id,
                'name' => $action,
                'slug' => $permission->name
            ];
        }

        // Urutkan Module berdasarkan abjad
        ksort($grouped);

        return $grouped;
    }

    /**
     * Sinkronisasi permission ke role tertentu.
     */
    public function syncPermissions(int $roleId, array $permissions): void
    {
        $role = $this->find($roleId);
        if ($role) {
            $role->syncPermissions($permissions);
        }
    }
}
