<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'nit',
        'sector',
        'size',
        'plan',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function isPro(): bool
    {
        return $this->plan === 'pro' || config('cumplia.demo_mode');
    }

    public function isFree(): bool
    {
        return ! $this->isPro();
    }
}
