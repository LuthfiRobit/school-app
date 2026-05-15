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
            $count = count($parts);

            if ($count === 1) {
                $module = 'Umum';
                $resource = 'General';
                $action = ucfirst(str_replace('-', ' ', $parts[0]));
            } elseif ($count === 2) {
                $module = ucfirst(str_replace('-', ' ', $parts[0]));
                $resource = 'General';
                $action = ucfirst(str_replace('-', ' ', $parts[1]));
            } else {
                $module = ucfirst(str_replace('-', ' ', $parts[0]));
                $action = ucfirst(str_replace('-', ' ', end($parts)));
                
                // Ambil semua bagian di tengah sebagai resource
                $resourceParts = array_slice($parts, 1, -1);
                $resource = ucfirst(str_replace(['-', '.'], ' ', implode(' ', $resourceParts)));
            }

            $grouped[$module][$resource][] = [
                'id' => $permission->id,
                'name' => $action,
                'slug' => $permission->name
            ];
        }

        // Urutkan Module berdasarkan abjad
        ksort($grouped);

        // Urutkan Resource di dalam setiap Module
        foreach ($grouped as $module => $resources) {
            ksort($grouped[$module]);
        }

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
