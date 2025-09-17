<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les enseignants
        $teachers = \App\Models\User::role('teacher')->get();
        
        if ($teachers->isEmpty()) {
            echo "Aucun enseignant trouvé. Veuillez d'abord créer des enseignants.\n";
            return;
        }

        // Créer des examens pour chaque enseignant
        foreach ($teachers as $teacher) {
            // Examen 1: Mathématiques
            $mathExam = \App\Models\Exam::create([
                'title' => 'Examen de Mathématiques - Algèbre',
                'description' => 'Test sur les équations du second degré et les fonctions',
                'teacher_id' => $teacher->id,
                'duration' => 60, // 60 minutes
            ]);

            // Questions pour l'examen de maths
            $mathQuestions = [
                [
                    'question' => 'Quelle est la solution de l\'équation x² - 5x + 6 = 0 ?',
                    'choices' => [
                        ['text' => 'x = 2 et x = 3', 'is_correct' => true],
                        ['text' => 'x = 1 et x = 6', 'is_correct' => false],
                        ['text' => 'x = -2 et x = -3', 'is_correct' => false],
                        ['text' => 'x = 0 et x = 5', 'is_correct' => false],
                    ]
                ],
                [
                    'question' => 'Quelle est la dérivée de f(x) = 3x² + 2x - 1 ?',
                    'choices' => [
                        ['text' => '6x + 2', 'is_correct' => true],
                        ['text' => '3x + 2', 'is_correct' => false],
                        ['text' => '6x - 1', 'is_correct' => false],
                        ['text' => '3x² + 2', 'is_correct' => false],
                    ]
                ],
                [
                    'question' => 'Combien vaut √(16) ?',
                    'choices' => [
                        ['text' => '4', 'is_correct' => true],
                        ['text' => '8', 'is_correct' => false],
                        ['text' => '2', 'is_correct' => false],
                        ['text' => '16', 'is_correct' => false],
                    ]
                ]
            ];

            foreach ($mathQuestions as $questionData) {
                $question = \App\Models\Question::create([
                    'exam_id' => $mathExam->id,
                    'content' => $questionData['question'],
                    'type' => 'multiple_choice',
                ]);

                foreach ($questionData['choices'] as $choiceData) {
                    \App\Models\Choice::create([
                        'question_id' => $question->id,
                        'label' => $choiceData['text'],
                        'is_correct' => $choiceData['is_correct'],
                    ]);
                }
            }

            // Examen 2: Informatique
            $csExam = \App\Models\Exam::create([
                'title' => 'Examen d\'Informatique - Programmation',
                'description' => 'Test sur les concepts de base de la programmation',
                'teacher_id' => $teacher->id,
                'duration' => 45, // 45 minutes
            ]);

            // Questions pour l'examen d'informatique
            $csQuestions = [
                [
                    'question' => 'Qu\'est-ce qu\'une variable en programmation ?',
                    'choices' => [
                        ['text' => 'Un espace de stockage pour des données', 'is_correct' => true],
                        ['text' => 'Une fonction mathématique', 'is_correct' => false],
                        ['text' => 'Un type de boucle', 'is_correct' => false],
                        ['text' => 'Un algorithme de tri', 'is_correct' => false],
                    ]
                ],
                [
                    'question' => 'Quel est le résultat de 5 % 3 en programmation ?',
                    'choices' => [
                        ['text' => '2', 'is_correct' => true],
                        ['text' => '1', 'is_correct' => false],
                        ['text' => '5', 'is_correct' => false],
                        ['text' => '3', 'is_correct' => false],
                    ]
                ]
            ];

            foreach ($csQuestions as $questionData) {
                $question = \App\Models\Question::create([
                    'exam_id' => $csExam->id,
                    'content' => $questionData['question'],
                    'type' => 'multiple_choice',
                ]);

                foreach ($questionData['choices'] as $choiceData) {
                    \App\Models\Choice::create([
                        'question_id' => $question->id,
                        'label' => $choiceData['text'],
                        'is_correct' => $choiceData['is_correct'],
                    ]);
                }
            }
        }

        // Créer quelques réponses d'étudiants pour avoir des statistiques
        $students = \App\Models\User::role('student')->get();
        $exams = \App\Models\Exam::all();

        foreach ($students->take(2) as $student) { // Seulement 2 étudiants pour commencer
            foreach ($exams->take(1) as $exam) { // Un examen par étudiant
                foreach ($exam->questions as $question) {
                    $choices = $question->choices;
                    if ($choices->count() > 0) {
                        // 80% de chance de donner la bonne réponse
                        $correctChoice = $choices->where('is_correct', true)->first();
                        $randomChoice = $choices->random();
                        
                        $selectedChoice = (rand(1, 10) <= 8 && $correctChoice) 
                            ? $correctChoice 
                            : $randomChoice;

                        \App\Models\Answer::create([
                            'user_id' => $student->id,
                            'question_id' => $question->id,
                            'answer_text' => $selectedChoice->label,
                        ]);
                    }
                }
            }
        }

        echo "Examens et questions créés avec succès !\n";
        echo "- " . \App\Models\Exam::count() . " examens créés\n";
        echo "- " . \App\Models\Question::count() . " questions créées\n";
        echo "- " . \App\Models\Choice::count() . " choix créés\n";
        echo "- " . \App\Models\Answer::count() . " réponses d'étudiants créées\n";
    }
}
