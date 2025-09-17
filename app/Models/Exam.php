<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration',
        'teacher_id',
    ];

    /**
     * Relation avec l'enseignant (User)
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Relation avec les questions
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
