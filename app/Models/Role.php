<?php

namespace App\Models;

use App\Enums\RoleScope;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $casts = [
        'scope' => RoleScope::class,
    ];
    /**
     * Scope a query to only include roles of a given group/scope.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $group
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfGroup($query, $group)
    {
        return $query->where('scope', $group);
    }
}
