<?php

namespace App\Services\Student;

use App\Models\ExamAssignment;
use Carbon\Carbon;

class SecurityViolationService
{
    /**
     * @param array<string, mixed> $answers
     */
    public function handleViolation(
        ExamAssignment $assignment, 
        string $violationType, 
    ): void {
        $submissionTime = Carbon::now();

        $securityViolations = $assignment->security_violations ?? [];
        $securityViolations[] = [
            'type' => $violationType,
            'timestamp' => $submissionTime->toISOString(),
            'forced_submission' => true,
        ];

        $assignment->update([
            'status' => 'pending_review',
            'submitted_at' => $submissionTime,
            'score' => null,
            'security_violations' => $securityViolations,
            'forced_submission' => true,
        ]);
    }
}