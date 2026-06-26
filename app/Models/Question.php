<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'category_id',
        'parent_question_id',
        'question_text',
        'help_text',
        'weight',
        'sort_order',
        'is_complementary',
    ];

    protected function casts(): array
    {
        return [
            'is_complementary' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function parentQuestion(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'parent_question_id');
    }

    public function childQuestions(): HasMany
    {
        return $this->hasMany(Question::class, 'parent_question_id');
    }
}
