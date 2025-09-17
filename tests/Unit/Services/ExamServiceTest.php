<?php

namespace Tests\Unit\Services;

use App\Services\ExamService;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Choice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ExamServiceTest extends TestCase
{
    use RefreshDatabase;

    private ExamService $examService;
    private User $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->examService = new ExamService();

        Role::create(['name' => 'teacher']);
        $this->teacher = User::factory()->create();
        $this->teacher->assignRole('teacher');
    }

    public function test_can_get_teacher_exams()
    {
        // Créer des examens pour ce professeur
        Exam::factory()->count(3)->create(['teacher_id' => $this->teacher->id]);
        
        // Créer des examens pour un autre professeur
        $otherTeacher = User::factory()->create();
        Exam::factory()->count(2)->create(['teacher_id' => $otherTeacher->id]);

        $exams = $this->examService->getTeacherExams($this->teacher->id);

        $this->assertCount(3, $exams);
        $this->assertTrue($exams->every(fn($exam) => $exam->teacher_id === $this->teacher->id));
    }

    public function test_can_create_exam_with_questions()
    {
        $examData = [
            'title' => 'Test Exam',
            'description' => 'Test description',
            'duration' => 60,
            'start_time' => now()->addHour(),
            'end_time' => now()->addHours(3),
            'is_active' => true,
            'questions' => [
                [
                    'content' => 'Question 1',
                    'type' => 'multiple_choice',
                    'points' => 5,
                    'choices' => [
                        ['content' => 'Choice A', 'is_correct' => true],
                        ['content' => 'Choice B', 'is_correct' => false],
                    ]
                ],
                [
                    'content' => 'Question 2',
                    'type' => 'text',
                    'points' => 10,
                ]
            ]
        ];

        $exam = $this->examService->createExam($examData, $this->teacher->id);

        $this->assertInstanceOf(Exam::class, $exam);
        $this->assertEquals('Test Exam', $exam->title);
        $this->assertEquals($this->teacher->id, $exam->teacher_id);
        $this->assertCount(2, $exam->questions);
        $this->assertEquals(15, $exam->total_points);
    }

    public function test_can_update_exam()
    {
        $exam = Exam::factory()->create(['teacher_id' => $this->teacher->id]);

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'duration' => 90,
            'is_active' => false,
        ];

        $updatedExam = $this->examService->updateExam($exam, $updateData);

        $this->assertEquals('Updated Title', $updatedExam->title);
        $this->assertEquals('Updated description', $updatedExam->description);
        $this->assertEquals(90, $updatedExam->duration);
        $this->assertFalse($updatedExam->is_active);
    }

    public function test_can_delete_exam()
    {
        $exam = Exam::factory()->create(['teacher_id' => $this->teacher->id]);
        $examId = $exam->id;

        $result = $this->examService->deleteExam($exam);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('exams', ['id' => $examId]);
    }

    public function test_create_exam_validates_question_data()
    {
        $examData = [
            'title' => 'Test Exam',
            'description' => 'Test description',
            'duration' => 60,
            'start_time' => now()->addHour(),
            'end_time' => now()->addHours(3),
            'is_active' => true,
            'questions' => [
                [
                    'content' => '', // Contenu vide - devrait lever une exception
                    'type' => 'multiple_choice',
                    'points' => 5,
                ]
            ]
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->examService->createExam($examData, $this->teacher->id);
    }

    public function test_exam_service_handles_questions_without_choices()
    {
        $examData = [
            'title' => 'Text Only Exam',
            'description' => 'Only text questions',
            'duration' => 60,
            'start_time' => now()->addHour(),
            'end_time' => now()->addHours(3),
            'is_active' => true,
            'questions' => [
                [
                    'content' => 'Describe the concept',
                    'type' => 'text',
                    'points' => 20,
                ]
            ]
        ];

        $exam = $this->examService->createExam($examData, $this->teacher->id);

        $this->assertInstanceOf(Exam::class, $exam);
        $this->assertCount(1, $exam->questions);
        $this->assertCount(0, $exam->questions->first()->choices);
    }
}