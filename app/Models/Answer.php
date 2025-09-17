<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_id',
        'answer_text',
    ];

    /**
     * Relation avec l'utilisateur (étudiant)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec la question
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
