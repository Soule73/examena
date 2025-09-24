<?php

namespace App\Services\Student;

use Carbon\Carbon;
use App\Models\Exam;
use App\Models\User;
use App\Models\ExamAssignment;

class ExamSessionService
{

    public function findOrCreateAssignment(Exam $exam, User $student): ExamAssignment
    {
        return ExamAssignment::firstOrCreate([
            'student_id' => $student->id,
            'exam_id' => $exam->id,
        ], [
            'status' => 'assigned',
        ]);
    }

    public function validateExamTiming(Exam $exam): bool
    {
        $now = Carbon::now();

        if ($exam->start_time && $now->lt($exam->start_time)) {
            return false;
        }

        if ($exam->end_time && $now->gt($exam->end_time)) {
            return false;
        }

        return true;
    }

    public function startExam(ExamAssignment $assignment): void
    {
        if (in_array($assignment->status, ['assigned'])) {
            $assignment->update([
                'status' => 'started',
                'started_at' => Carbon::now(),
            ]);
        }
    }

    public function submitExam(ExamAssignment $assignment, ?float $autoScore, bool $hasTextQuestions = false, bool $isSecurityViolation = false): void
    {
        $submissionTime = Carbon::now();

        $finalStatus = ($hasTextQuestions || $isSecurityViolation) ? 'pending_review' : 'submitted';

        $assignment->update([
            'status' => $finalStatus,
            'submitted_at' => $submissionTime,
            'forced_submission' => $isSecurityViolation,
            'score' => ($hasTextQuestions || $isSecurityViolation) ? null : $autoScore,
            'auto_score' => $autoScore ?? $assignment->auto_score,
        ]);
    }
}
