<?php

namespace App\Services\Student;

use App\Models\Answer;
use App\Models\ExamAssignment;

class ExamScoringService
{
    public function calculateAutoScore(ExamAssignment $assignment): int
    {
        $totalScore = 0;

        $autoCorrectableQuestions = $assignment->exam->questions()
            ->whereIn('type', ['multiple', 'one_choice', 'boolean'])
            ->get();

        foreach ($autoCorrectableQuestions as $question) {
            $answer = Answer::where('assignment_id', $assignment->id)
                ->where('question_id', $question->id)
                ->first();

            if ($answer && $answer->choice_id) {
                $isCorrect = $this->checkAnswerCorrectness($question, $answer);
                if ($isCorrect) {
                    $totalScore += $question->points;
                }
            }
        }

        return $totalScore;
    }

    private function checkAnswerCorrectness($question, $answer): bool
    {
        switch ($question->type) {
            case 'multiple':
            case 'one_choice':
            case 'boolean':
                if (!$answer->choice_id) {
                    return false;
                }
                
                $selectedChoice = $question->choices()->where('id', $answer->choice_id)->first();
                return $selectedChoice && $selectedChoice->is_correct;

            case 'text':
                return false;

            default:
                return false;
        }
    }
}