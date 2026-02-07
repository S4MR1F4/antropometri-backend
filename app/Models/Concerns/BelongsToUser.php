<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * BelongsToUser trait for row-level security.
 * Per 09_security_access.md §4.1
 * 
 * This trait:
 * 1. Auto-assigns user_id when creating records
 * 2. Applies global scope so non-admin users only see their own data
 */
trait BelongsToUser
{
    /**
     * Boot the trait.
     */
    protected static function bootBelongsToUser(): void
    {
        // Auto-assign user_id when creating
        static::creating(function ($model) {
            if (auth()->check() && empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
        });

        // Global scope: non-admin users only see their own data
        static::addGlobalScope('user', function (Builder $query) {
            if (auth()->check() && !auth()->user()->isAdmin()) {
                $query->where('user_id', auth()->id());
            }
        });
    }

    /**
     * Get the user that owns this model.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
