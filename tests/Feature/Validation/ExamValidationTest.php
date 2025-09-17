<?php

namespace Tests\Feature\Validation;

use App\Http\Requests\Teacher\StoreExamRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ExamValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'teacher']);
        $this->teacher = User::factory()->create();
        $this->teacher->assignRole('teacher');
    }

    public function test_exam_title_is_required()
    {
        $this->actingAs($this->teacher);

        $data = [
            'description' => 'Test description',
            'duration' => 60,
        ];

        $response = $this->post(route('teacher.exams.store'), $data);
        $response->assertSessionHasErrors('title');
    }

    public function test_exam_title_has_minimum_length()
    {
        $this->actingAs($this->teacher);

        $data = [
            'title' => 'AB', // Trop court
            'description' => 'Test description',
            'duration' => 60,
            'questions' => [
                [
                    'content' => 'Question test',
                    'type' => 'text',
                    'points' => 5,
                ]
            ]
        ];

        $response = $this->post(route('teacher.exams.store'), $data);
        $response->assertSessionHasErrors('title');
    }

    public function test_exam_duration_is_required()
    {
        $this->actingAs($this->teacher);

        $data = [
            'title' => 'Test Exam',
            'description' => 'Test description',
            'questions' => [
                [
                    'content' => 'Question test',
                    'type' => 'text',
                    'points' => 5,
                ]
            ]
        ];

        $response = $this->post(route('teacher.exams.store'), $data);
        $response->assertSessionHasErrors('duration');
    }

    public function test_exam_duration_must_be_positive()
    {
        $this->actingAs($this->teacher);

        $data = [
            'title' => 'Test Exam',
            'description' => 'Test description',
            'duration' => -10,
            'questions' => [
                [
                    'content' => 'Question test',
                    'type' => 'text',
                    'points' => 5,
                ]
            ]
        ];

        $response = $this->post(route('teacher.exams.store'), $data);
        $response->assertSessionHasErrors('duration');
    }

    public function test_exam_end_time_must_be_after_start_time()
    {
        $this->actingAs($this->teacher);

        $data = [
            'title' => 'Test Exam',
            'description' => 'Test description',
            'duration' => 60,
            'start_time' => now()->addHours(2)->format('Y-m-d\TH:i'),
            'end_time' => now()->addHour()->format('Y-m-d\TH:i'), // Fin avant début
            'questions' => [
                [
                    'content' => 'Question test',
                    'type' => 'text',
                    'points' => 5,
                ]
            ]
        ];

        $response = $this->post(route('teacher.exams.store'), $data);
        $response->assertSessionHasErrors('end_time');
    }

    public function test_question_validation_rules()
    {
        $this->actingAs($this->teacher);

        // Test question sans contenu
        $data = [
            'title' => 'Test Exam',
            'description' => 'Test description',
            'duration' => 60,
            'questions' => [
                [
                    'content' => '', // Contenu vide
                    'type' => 'text',
                    'points' => 5,
                ]
            ]
        ];

        $response = $this->post(route('teacher.exams.store'), $data);
        $response->assertSessionHasErrors('questions.0.content');
    }

    public function test_multiple_choice_question_requires_choices()
    {
        $this->actingAs($this->teacher);

        $data = [
            'title' => 'Test Exam',
            'description' => 'Test description',
            'duration' => 60,
            'questions' => [
                [
                    'content' => 'What is 2+2?',
                    'type' => 'multiple_choice',
                    'points' => 5,
                    'choices' => [] // Pas de choices pour une question à choix multiples
                ]
            ]
        ];

        $response = $this->post(route('teacher.exams.store'), $data);
        $response->assertSessionHasErrors();
    }

    public function test_multiple_choice_question_requires_correct_answer()
    {
        $this->actingAs($this->teacher);

        $data = [
            'title' => 'Test Exam',
            'description' => 'Test description',
            'duration' => 60,
            'questions' => [
                [
                    'content' => 'What is 2+2?',
                    'type' => 'multiple_choice',
                    'points' => 5,
                    'choices' => [
                        ['content' => '3', 'is_correct' => false],
                        ['content' => '4', 'is_correct' => false], // Aucune réponse correcte
                        ['content' => '5', 'is_correct' => false],
                    ]
                ]
            ]
        ];

        $response = $this->post(route('teacher.exams.store'), $data);
        $response->assertSessionHasErrors();
    }

    public function test_choice_validation()
    {
        $this->actingAs($this->teacher);

        $data = [
            'title' => 'Test Exam',
            'description' => 'Test description',
            'duration' => 60,
            'questions' => [
                [
                    'content' => 'What is 2+2?',
                    'type' => 'multiple_choice',
                    'points' => 5,
                    'choices' => [
                        ['content' => ''], // Contenu vide
                        ['content' => '4'],
                    ],
                    'correct_choice' => 1
                ]
            ]
        ];

        $response = $this->post(route('teacher.exams.store'), $data);
        $response->assertSessionHasErrors('questions.0.choices.0.content');
    }

    public function test_valid_exam_data_passes_validation()
    {
        $this->actingAs($this->teacher);

        $data = [
            'title' => 'Valid Test Exam',
            'description' => 'This is a valid test exam',
            'duration' => 90,
            'start_time' => now()->addHour()->format('Y-m-d\TH:i'),
            'end_time' => now()->addHours(3)->format('Y-m-d\TH:i'),
            'is_active' => true,
            'questions' => [
                [
                    'content' => 'What is the capital of France?',
                    'type' => 'multiple_choice',
                    'points' => 5,
                    'choices' => [
                        ['content' => 'London'],
                        ['content' => 'Paris'],
                        ['content' => 'Berlin'],
                    ],
                    'correct_choice' => 1
                ],
                [
                    'content' => 'Explain object-oriented programming',
                    'type' => 'text',
                    'points' => 10,
                ]
            ]
        ];

        $response = $this->post(route('teacher.exams.store'), $data);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }
}