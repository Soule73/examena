<?php

namespace App\Services\Student;

use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamAssignment;
use App\Models\Question;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AnswerService
{
    public function saveAnswer(ExamAssignment $assignment, Question $question, array $data): void
    {
        if ($question->type === 'multiple') {
            Answer::create([
                'assignment_id' => $assignment->id,
                'question_id' => $question->id,
                'choice_id' => $data['choice_id'],
                'answer_text' => null,
            ]);
        } else {
            Answer::updateOrCreate(
                [
                    'assignment_id' => $assignment->id,
                    'question_id' => $question->id,
                ],
                $data
            );
        }
    }

    public function clearAnswersForQuestion(ExamAssignment $assignment, int $questionId): void
    {
        Answer::where('assignment_id', $assignment->id)
            ->where('question_id', $questionId)
            ->delete();
    }

    /**
     * @param array<int, mixed> $answers
     */
    public function saveMultipleAnswers(ExamAssignment $assignment, Exam $exam, array $answers): void
    {
        foreach ($answers as $questionId => $answer) {
            $question = $exam->questions()->find($questionId);
            if (!$question) continue;

            $this->clearAnswersForQuestion($assignment, $questionId);

            if ($question->type === 'multiple' && is_array($answer)) {
                foreach ($answer as $choiceId) {
                    Answer::create([
                        'assignment_id' => $assignment->id,
                        'question_id' => $questionId,
                        'choice_id' => $choiceId,
                        'answer_text' => null,
                    ]);
                }
            } elseif ($question->type === 'text') {
                Answer::create([
                    'assignment_id' => $assignment->id,
                    'question_id' => $questionId,
                    'choice_id' => null,
                    'answer_text' => $answer,
                ]);
            } else {
                Answer::create([
                    'assignment_id' => $assignment->id,
                    'question_id' => $questionId,
                    'choice_id' => $answer,
                    'answer_text' => null,
                ]);
            }
        }
    }

    public function getUserAnswers(ExamAssignment $assignment): Collection
    {
        return Answer::where('assignment_id', $assignment->id)
            ->with(['choice', 'question'])
            ->get()
            ->groupBy('question_id')
            ->map(function ($questionAnswers) {
                if ($questionAnswers->count() === 1) {
                    $answer = $questionAnswers->first();
                    return [
                        'type' => 'single',
                        'choice_id' => $answer->choice_id,
                        'answer_text' => $answer->answer_text,
                        'choice' => $answer->choice,
                    ];
                } else {
                    return [
                        'type' => 'multiple',
                        'choices' => $questionAnswers->map(function ($answer) {
                            return [
                                'choice_id' => $answer->choice_id,
                                'choice' => $answer->choice,
                            ];
                        })->toArray(),
                        'answer_text' => null,
                    ];
                }
            });
    }

    public function prepareAnswerData(Question $question, array $requestData): array
    {
        if (in_array($question->type, ['multiple', 'one_choice', 'boolean'])) {
            return [
                'choice_id' => $requestData['choice_id'],
                'answer_text' => null,
            ];
        }

        return [
            'answer_text' => $requestData['answer_text'] ?? '',
            'choice_id' => null,
        ];
    }
}
