<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;

class Group extends Model
{
    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function visibleUsers()
    {
        return $this->belongsToMany(User::class)->where(function(Builder $query) {
            if(!Auth::user()->hasPermission('viewInactiveUsers')) {
                $query->where('is_active', true);
            }
        });
    }
}
