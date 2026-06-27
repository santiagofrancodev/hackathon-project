<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Assessment extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'status',
        'score',
        'ai_summary',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function isValidStatus(string $status): bool
    {
        return in_array($status, ['in_progress', 'completed']);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class);
    }

    public function auditorRequest(): HasOne
    {
        return $this->hasOne(AuditorRequest::class);
    }
}
