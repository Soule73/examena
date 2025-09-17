<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'content',
        'type',
        'points',
    ];

    /**
     * Relation avec l'examen
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Relation avec les choix (pour les QCM)
     */
    public function choices(): HasMany
    {
        return $this->hasMany(Choice::class);
    }

    /**
     * Relation avec les réponses des étudiants
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}
