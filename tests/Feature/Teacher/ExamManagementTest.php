<?php

namespace Tests\Feature\Teacher;

use App\Models\Exam;
use App\Models\Question;
use App\Models\Choice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ExamManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $teacher;
    private User $student;
    private User $admin;

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

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_teacher_can_view_exams_index()
    {
        $this->actingAs($this->teacher);

        $response = $this->get(route('teacher.exams.index'));

        $response->assertStatus(200);
        $response->assertViewIs('teacher.exams.index');
    }

    public function test_student_cannot_access_teacher_exams_index()
    {
        $this->actingAs($this->student);

        $response = $this->get(route('teacher.exams.index'));

        $response->assertStatus(403);
    }

    public function test_teacher_can_view_exam_creation_form()
    {
        $this->actingAs($this->teacher);

        $response = $this->get(route('teacher.exams.create'));

        $response->assertStatus(200);
        $response->assertViewIs('teacher.exams.create');
    }

    public function test_teacher_can_create_exam_with_valid_data()
    {
        $this->actingAs($this->teacher);

        $examData = [
            'title' => 'Test Exam',
            'description' => 'This is a test exam',
            'duration' => 60,
            'start_time' => now()->addHour()->format('Y-m-d\TH:i'),
            'end_time' => now()->addHours(3)->format('Y-m-d\TH:i'),
            'is_active' => true,
            'questions' => [
                [
                    'content' => 'What is 2+2?',
                    'type' => 'multiple_choice',
                    'points' => 5,
                    'choices' => [
                        ['content' => '3'],
                        ['content' => '4'],
                        ['content' => '5'],
                    ],
                    'correct_choice' => 1
                ]
            ]
        ];

        $response = $this->post(route('teacher.exams.store'), $examData);

        $response->assertRedirect();
        $this->assertDatabaseHas('exams', [
            'title' => 'Test Exam',
            'teacher_id' => $this->teacher->id,
        ]);
    }

    public function test_teacher_cannot_create_exam_with_invalid_data()
    {
        $this->actingAs($this->teacher);

        $invalidData = [
            'title' => '', // titre requis
            'duration' => -10, // durée invalide
        ];

        $response = $this->post(route('teacher.exams.store'), $invalidData);

        $response->assertSessionHasErrors(['title', 'duration']);
    }

    public function test_teacher_can_create_exam_with_questions()
    {
        $this->actingAs($this->teacher);

        $examData = [
            'title' => 'Exam with Questions',
            'description' => 'Test exam with various question types',
            'duration' => 90,
            'start_time' => now()->addHour()->format('Y-m-d\TH:i'),
            'end_time' => now()->addHours(4)->format('Y-m-d\TH:i'),
            'is_active' => true,
            'questions' => [
                [
                    'content' => 'What is 2+2?',
                    'type' => 'multiple_choice',
                    'points' => 5,
                    'choices' => [
                        ['content' => '3'],
                        ['content' => '4'],
                        ['content' => '5'],
                    ],
                    'correct_choice' => 1 // Index de la bonne réponse (4)
                ],
                [
                    'content' => 'Paris is the capital of France',
                    'type' => 'true_false',
                    'points' => 3,
                    'correct_answer' => 'true'
                ],
                [
                    'content' => 'Explain the concept of polymorphism',
                    'type' => 'text',
                    'points' => 10,
                ]
            ]
        ];

        $response = $this->post(route('teacher.exams.store'), $examData);

        $response->assertRedirect();
        
        $exam = Exam::where('title', 'Exam with Questions')->first();
        $this->assertNotNull($exam);
        $this->assertEquals(3, $exam->questions()->count());
        $this->assertEquals(18, $exam->total_points); // 5 + 3 + 10
    }

    public function test_exam_validation_rules()
    {
        $this->actingAs($this->teacher);

        // Test title required
        $response = $this->post(route('teacher.exams.store'), [
            'description' => 'Test',
            'duration' => 60,
        ]);
        $response->assertSessionHasErrors('title');

        // Test duration minimum
        $response = $this->post(route('teacher.exams.store'), [
            'title' => 'Test',
            'duration' => 0,
        ]);
        $response->assertSessionHasErrors('duration');

        // Test end_time after start_time
        $response = $this->post(route('teacher.exams.store'), [
            'title' => 'Test',
            'duration' => 60,
            'start_time' => now()->addHours(2)->format('Y-m-d\TH:i'),
            'end_time' => now()->addHour()->format('Y-m-d\TH:i'),
        ]);
        $response->assertSessionHasErrors('end_time');
    }

    public function test_teacher_can_view_own_exam()
    {
        $this->actingAs($this->teacher);

        $exam = Exam::factory()->create(['teacher_id' => $this->teacher->id]);

        $response = $this->get(route('teacher.exams.show', $exam));

        $response->assertStatus(200);
        $response->assertViewIs('teacher.exams.show');
    }

    public function test_teacher_cannot_view_other_teacher_exam()
    {
        $this->actingAs($this->teacher);

        $otherTeacher = User::factory()->create();
        $otherTeacher->assignRole('teacher');
        
        $exam = Exam::factory()->create(['teacher_id' => $otherTeacher->id]);

        $response = $this->get(route('teacher.exams.show', $exam));

        $response->assertStatus(403);
    }

    public function test_teacher_can_edit_own_exam()
    {
        $this->actingAs($this->teacher);

        $exam = Exam::factory()->create(['teacher_id' => $this->teacher->id]);

        $response = $this->get(route('teacher.exams.edit', $exam));

        $response->assertStatus(200);
        $response->assertViewIs('teacher.exams.edit');
    }

    public function test_teacher_can_update_own_exam()
    {
        $this->actingAs($this->teacher);

        $exam = Exam::factory()->create(['teacher_id' => $this->teacher->id]);

        $updateData = [
            'title' => 'Updated Exam Title',
            'description' => 'Updated description',
            'duration' => 120,
            'start_time' => $exam->start_time->format('Y-m-d\TH:i'),
            'end_time' => $exam->end_time->format('Y-m-d\TH:i'),
            'is_active' => false,
            'questions' => [
                [
                    'content' => 'Updated question?',
                    'type' => 'text',
                    'points' => 10,
                ]
            ]
        ];

        $response = $this->put(route('teacher.exams.update', $exam), $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('exams', [
            'id' => $exam->id,
            'title' => 'Updated Exam Title',
            'is_active' => 0, // Utiliser 0 au lieu de false pour la base de données
        ]);
    }

    public function test_teacher_can_delete_own_exam()
    {
        $this->actingAs($this->teacher);

        $exam = Exam::factory()->create(['teacher_id' => $this->teacher->id]);

        $response = $this->delete(route('teacher.exams.destroy', $exam));

        $response->assertRedirect();
        $this->assertDatabaseMissing('exams', ['id' => $exam->id]);
    }

    public function test_exam_total_points_calculation()
    {
        $exam = Exam::factory()->create();
        
        Question::factory()->create(['exam_id' => $exam->id, 'points' => 5]);
        Question::factory()->create(['exam_id' => $exam->id, 'points' => 10]);
        Question::factory()->create(['exam_id' => $exam->id, 'points' => 3]);

        $this->assertEquals(18, $exam->fresh()->total_points);
    }
}