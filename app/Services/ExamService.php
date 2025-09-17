<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ExamService
{
    /**
     * Créer un nouvel examen avec ses questions
     */
    public function createExam(array $data): Exam
    {
        return DB::transaction(function () use ($data) {
            // Créer l'examen
            $exam = Exam::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'duration' => $data['duration'],
                'start_time' => $data['start_time'] ?? null,
                'end_time' => $data['end_time'] ?? null,
                'is_active' => $data['is_active'] ?? false,
                'teacher_id' => Auth::id(),
            ]);

            // Créer les questions
            $this->createQuestions($exam, $data['questions']);

            return $exam->load(['questions.choices']);
        });
    }

    /**
     * Mettre à jour un examen existant
     */
    public function updateExam(Exam $exam, array $data): Exam
    {
        return DB::transaction(function () use ($exam, $data) {
            // Mettre à jour l'examen
            $exam->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'duration' => $data['duration'],
                'start_time' => $data['start_time'] ?? null,
                'end_time' => $data['end_time'] ?? null,
                'is_active' => $data['is_active'] ?? false,
            ]);

            // Supprimer les anciennes questions et choix
            $exam->questions()->each(function ($question) {
                $question->choices()->delete();
                $question->delete();
            });

            // Créer les nouvelles questions
            $this->createQuestions($exam, $data['questions']);

            return $exam->load(['questions.choices']);
        });
    }

    /**
     * Supprimer un examen
     */
    public function deleteExam(Exam $exam): bool
    {
        return DB::transaction(function () use ($exam) {
            // Supprimer toutes les questions et choix associés
            $exam->questions()->each(function ($question) {
                $question->choices()->delete();
                $question->delete();
            });

            // Supprimer l'examen
            return $exam->delete();
        });
    }

    /**
     * Créer les questions pour un examen
     */
    private function createQuestions(Exam $exam, array $questionsData): void
    {
        foreach ($questionsData as $questionData) {
            $question = $exam->questions()->create([
                'content' => $questionData['content'],
                'type' => $questionData['type'],
                'points' => $questionData['points'],
            ]);

            // Créer les choix selon le type de question
            $this->createChoicesForQuestion($question, $questionData);
        }
    }

    /**
     * Créer les choix pour une question selon son type
     */
    private function createChoicesForQuestion(Question $question, array $questionData): void
    {
        switch ($questionData['type']) {
            case 'multiple_choice':
                $this->createMultipleChoiceOptions($question, $questionData);
                break;
                
            case 'true_false':
                $this->createTrueFalseOptions($question, $questionData);
                break;
                
            case 'text':
                // Pas de choix à créer pour les questions texte
                break;
        }
    }

    /**
     * Créer les choix pour une question à choix multiples
     */
    private function createMultipleChoiceOptions(Question $question, array $questionData): void
    {
        if (!isset($questionData['choices']) || !is_array($questionData['choices'])) {
            return;
        }

        foreach ($questionData['choices'] as $index => $choiceData) {
            $isCorrect = isset($questionData['correct_choice']) && 
                        $questionData['correct_choice'] == $index;
            
            $question->choices()->create([
                'content' => $choiceData['content'],
                'is_correct' => $isCorrect,
            ]);
        }
    }

    /**
     * Créer les choix pour une question vrai/faux
     */
    private function createTrueFalseOptions(Question $question, array $questionData): void
    {
        $correctAnswer = $questionData['correct_answer'] ?? 'true';
        
        // Créer le choix "Vrai"
        $question->choices()->create([
            'content' => 'Vrai',
            'is_correct' => $correctAnswer === 'true',
        ]);
        
        // Créer le choix "Faux"
        $question->choices()->create([
            'content' => 'Faux',
            'is_correct' => $correctAnswer === 'false',
        ]);
    }

    /**
     * Obtenir les examens paginés pour un enseignant
     */
    public function getTeacherExams(int $teacherId, int $perPage = 10)
    {
        return Exam::where('teacher_id', $teacherId)
            ->withCount(['questions', 'answers'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Dupliquer un examen
     */
    public function duplicateExam(Exam $originalExam): Exam
    {
        return DB::transaction(function () use ($originalExam) {
            // Créer une copie de l'examen
            $examData = $originalExam->toArray();
            unset($examData['id'], $examData['created_at'], $examData['updated_at']);
            $examData['title'] = $examData['title'] . ' (Copie)';
            $examData['is_active'] = false; // Les copies sont inactives par défaut
            
            $newExam = Exam::create($examData);

            // Copier les questions et choix
            foreach ($originalExam->questions as $originalQuestion) {
                $questionData = $originalQuestion->toArray();
                unset($questionData['id'], $questionData['exam_id'], $questionData['created_at'], $questionData['updated_at']);
                
                $newQuestion = $newExam->questions()->create($questionData);

                // Copier les choix
                foreach ($originalQuestion->choices as $originalChoice) {
                    $choiceData = $originalChoice->toArray();
                    unset($choiceData['id'], $choiceData['question_id'], $choiceData['created_at'], $choiceData['updated_at']);
                    
                    $newQuestion->choices()->create($choiceData);
                }
            }

            return $newExam->load(['questions.choices']);
        });
    }
}