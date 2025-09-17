<?php

namespace Tests\Feature\Integration;

use App\Models\Exam;
use App\Models\Question;
use App\Models\Choice;
use App\Models\User;
use App\Services\ExamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class FullExamWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;
    private User $student;
    private ExamService $examService;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer les rôles
        Role::create(['name' => 'teacher']);
        Role::create(['name' => 'student']);
        Role::create(['name' => 'admin']);

        // Créer les utilisateurs
        $this->teacher = User::factory()->create();
        $this->teacher->assignRole('teacher');

        $this->student = User::factory()->create();
        $this->student->assignRole('student');

        $this->examService = new ExamService();
    }

    public function test_complete_exam_creation_workflow()
    {
        $this->actingAs($this->teacher);

        // 1. Accéder au formulaire de création
        $response = $this->get(route('teacher.exams.create'));
        $response->assertStatus(200);

        // 2. Créer un examen complet avec différents types de questions
        $examData = [
            'title' => 'Examen Complet de Test',
            'description' => 'Un examen complet avec tous les types de questions',
            'duration' => 120,
            'start_time' => now()->addDay()->format('Y-m-d\TH:i'),
            'end_time' => now()->addDay()->addHours(3)->format('Y-m-d\TH:i'),
            'is_active' => true,
            'questions' => [
                // Question à choix multiples
                [
                    'content' => 'Quelle est la capitale de la France ?',
                    'type' => 'multiple_choice',
                    'points' => 5,
                    'choices' => [
                        ['content' => 'Londres', 'is_correct' => false],
                        ['content' => 'Paris', 'is_correct' => true],
                        ['content' => 'Berlin', 'is_correct' => false],
                        ['content' => 'Madrid', 'is_correct' => false],
                    ]
                ],
                // Question vrai/faux
                [
                    'content' => 'Laravel est un framework PHP',
                    'type' => 'true_false',
                    'points' => 3,
                    'choices' => [
                        ['content' => 'Vrai', 'is_correct' => true],
                        ['content' => 'Faux', 'is_correct' => false],
                    ]
                ],
                // Question texte libre
                [
                    'content' => 'Expliquez le principe de l\'injection de dépendances en PHP',
                    'type' => 'text',
                    'points' => 12,
                ]
            ]
        ];

        // 3. Soumettre le formulaire
        $response = $this->post(route('teacher.exams.store'), $examData);
        $response->assertRedirect();

        // 4. Vérifier que l'examen a été créé correctement
        $exam = Exam::where('title', 'Examen Complet de Test')->first();
        $this->assertNotNull($exam);
        $this->assertEquals($this->teacher->id, $exam->teacher_id);
        $this->assertCount(3, $exam->questions);

        // 5. Vérifier les questions et leurs types
        $questions = $exam->questions;
        
        $multipleChoiceQuestion = $questions->where('type', 'multiple_choice')->first();
        $this->assertNotNull($multipleChoiceQuestion);
        $this->assertCount(4, $multipleChoiceQuestion->choices);
        $this->assertEquals(1, $multipleChoiceQuestion->choices->where('is_correct', true)->count());

        $trueFalseQuestion = $questions->where('type', 'true_false')->first();
        $this->assertNotNull($trueFalseQuestion);
        $this->assertCount(2, $trueFalseQuestion->choices);

        $textQuestion = $questions->where('type', 'text')->first();
        $this->assertNotNull($textQuestion);
        $this->assertCount(0, $textQuestion->choices);

        // 6. Vérifier le calcul du score total
        $this->assertEquals(20, $exam->total_points); // 5 + 3 + 12

        // 7. Accéder à la page de visualisation
        $response = $this->get(route('teacher.exams.show', $exam));
        $response->assertStatus(200);

        // 8. Modifier l'examen
        $response = $this->get(route('teacher.exams.edit', $exam));
        $response->assertStatus(200);

        $updateData = [
            'title' => 'Examen Modifié',
            'description' => $exam->description,
            'duration' => 150,
            'start_time' => $exam->start_time->format('Y-m-d\TH:i'),
            'end_time' => $exam->end_time->format('Y-m-d\TH:i'),
            'is_active' => false,
        ];

        $response = $this->put(route('teacher.exams.update', $exam), $updateData);
        $response->assertRedirect();

        $exam->refresh();
        $this->assertEquals('Examen Modifié', $exam->title);
        $this->assertEquals(150, $exam->duration);
        $this->assertFalse($exam->is_active);
    }

    public function test_exam_access_permissions()
    {
        // Créer un examen avec le premier professeur
        $exam = Exam::factory()->create(['teacher_id' => $this->teacher->id]);

        // 1. Le professeur propriétaire peut accéder à tout
        $this->actingAs($this->teacher);
        
        $this->get(route('teacher.exams.show', $exam))->assertStatus(200);
        $this->get(route('teacher.exams.edit', $exam))->assertStatus(200);
        $this->put(route('teacher.exams.update', $exam), [
            'title' => $exam->title,
            'description' => $exam->description,
            'duration' => $exam->duration,
            'start_time' => $exam->start_time->format('Y-m-d\TH:i'),
            'end_time' => $exam->end_time->format('Y-m-d\TH:i'),
            'is_active' => $exam->is_active,
        ])->assertRedirect();

        // 2. Un autre professeur ne peut pas accéder
        /** @var User $otherTeacher */
        $otherTeacher = User::factory()->create();
        $otherTeacher->assignRole('teacher');

        $this->actingAs($otherTeacher);
        $this->get(route('teacher.exams.show', $exam))->assertStatus(403);
        $this->get(route('teacher.exams.edit', $exam))->assertStatus(403);

        // 3. Un étudiant ne peut pas accéder aux routes professeur
        $this->actingAs($this->student);
        $this->get(route('teacher.exams.index'))->assertStatus(403);
        $this->get(route('teacher.exams.create'))->assertStatus(403);
        $this->get(route('teacher.exams.show', $exam))->assertStatus(403);
    }

    public function test_exam_service_integration()
    {
        // Test de l'intégration complète avec ExamService
        $examData = [
            'title' => 'Service Test Exam',
            'description' => 'Test via service',
            'duration' => 90,
            'start_time' => now()->addHour(),
            'end_time' => now()->addHours(3),
            'is_active' => true,
            'questions' => [
                [
                    'content' => 'Question de test',
                    'type' => 'multiple_choice',
                    'points' => 10,
                    'choices' => [
                        ['content' => 'Option A', 'is_correct' => false],
                        ['content' => 'Option B', 'is_correct' => true],
                    ]
                ]
            ]
        ];

        $exam = $this->examService->createExam($examData, $this->teacher->id);

        $this->assertInstanceOf(Exam::class, $exam);
        $this->assertEquals('Service Test Exam', $exam->title);
        $this->assertCount(1, $exam->questions);

        // Test de modification via service
        $updateData = [
            'title' => 'Service Updated Exam',
            'description' => 'Updated via service',
            'duration' => 60,
            'is_active' => false,
        ];

        $updatedExam = $this->examService->updateExam($exam, $updateData);
        $this->assertEquals('Service Updated Exam', $updatedExam->title);
        $this->assertFalse($updatedExam->is_active);

        // Test de suppression via service
        $result = $this->examService->deleteExam($exam);
        $this->assertTrue($result);
        $this->assertDatabaseMissing('exams', ['id' => $exam->id]);
    }

    public function test_database_integrity_and_relationships()
    {
        // Créer un examen avec questions et choix
        $exam = Exam::factory()->create(['teacher_id' => $this->teacher->id]);
        
        $question1 = Question::factory()->create([
            'exam_id' => $exam->id,
            'type' => 'multiple_choice',
            'points' => 5,
        ]);

        $question2 = Question::factory()->create([
            'exam_id' => $exam->id,
            'type' => 'text',
            'points' => 10,
        ]);

        Choice::factory()->count(3)->create(['question_id' => $question1->id]);

        // Vérifier les relations
        $this->assertEquals($this->teacher->id, $exam->teacher->id);
        $this->assertCount(2, $exam->questions);
        $this->assertEquals(15, $exam->total_points);
        $this->assertCount(3, $question1->choices);
        $this->assertCount(0, $question2->choices);

        // Test de suppression en cascade
        $exam->delete();
        $this->assertDatabaseMissing('questions', ['exam_id' => $exam->id]);
        $this->assertDatabaseMissing('choices', ['question_id' => $question1->id]);
    }

    public function test_validation_edge_cases()
    {
        $this->actingAs($this->teacher);

        // Test avec des données limites
        $examData = [
            'title' => str_repeat('A', 255), // Titre maximum
            'description' => str_repeat('B', 1000), // Description longue
            'duration' => 1, // Durée minimum
            'start_time' => now()->addMinute()->format('Y-m-d\TH:i'),
            'end_time' => now()->addMinutes(2)->format('Y-m-d\TH:i'),
            'is_active' => true,
        ];

        $response = $this->post(route('teacher.exams.store'), $examData);
        $response->assertRedirect(); // Devrait réussir

        // Test avec des données invalides
        $invalidData = [
            'title' => str_repeat('A', 256), // Titre trop long
            'duration' => 0, // Durée invalide
            'start_time' => now()->addHours(2)->format('Y-m-d\TH:i'),
            'end_time' => now()->addHour()->format('Y-m-d\TH:i'), // Fin avant début
        ];

        $response = $this->post(route('teacher.exams.store'), $invalidData);
        $response->assertSessionHasErrors(['title', 'duration', 'end_time']);
    }
}