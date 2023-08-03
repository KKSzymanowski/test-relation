<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $guarded = [];

    public function permissions() {
        return $this->belongsToMany(Permission::class);
    }

    public function hasPermission($permissionName) {
        return $this->permissions()->where('name', $permissionName)->exists();
    }
}
