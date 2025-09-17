<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAssignment extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'assigned_at',
        'started_at',
        'submitted_at',
        'score',
        'status',
        'teacher_notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'score' => 'decimal:2',
    ];

    /**
     * Relation avec l'examen
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Relation avec l'étudiant
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Scope pour les assignations en attente
     */
    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    /**
     * Scope pour les assignations commencées
     */
    public function scopeStarted($query)
    {
        return $query->where('status', 'started');
    }

    /**
     * Scope pour les assignations soumises
     */
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    /**
     * Vérifier si l'assignation est en cours
     */
    public function isInProgress(): bool
    {
        return $this->status === 'started';
    }

    /**
     * Vérifier si l'assignation est terminée
     */
    public function isCompleted(): bool
    {
        return in_array($this->status, ['submitted', 'graded']);
    }
}
