<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration',
        'start_time',
        'end_time',
        'is_active',
        'teacher_id',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
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

    /**
     * Relation avec les assignations
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(ExamAssignment::class);
    }

    /**
     * Relation avec les étudiants assignés
     */
    public function assignedStudents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'exam_assignments', 'exam_id', 'student_id')
                    ->withPivot(['assigned_at', 'started_at', 'submitted_at', 'score', 'status', 'teacher_notes'])
                    ->withTimestamps();
    }

    /**
     * Relation avec les réponses des étudiants (via les questions)
     */
    public function answers(): HasManyThrough
    {
        return $this->hasManyThrough(Answer::class, Question::class);
    }

    /**
     * Obtenir le nombre de participants uniques à cet examen
     */
    public function getUniqueParticipantsCountAttribute(): int
    {
        return $this->answers()->distinct('user_id')->count('user_id');
    }

    /**
     * Obtenir le score total possible pour cet examen
     */
    public function getTotalPointsAttribute(): int
    {
        return $this->questions->sum('points');
    }
}
