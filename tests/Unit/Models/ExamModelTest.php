<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Choice;
use App\Models\Answer;
use App\Models\ExamAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class ExamModelTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer le rôle enseignant
        Role::create(['name' => 'teacher']);

        // Créer un enseignant
        $this->teacher = User::factory()->create([
            'email' => 'teacher@test.com',
            'role' => 'teacher'
        ]);
        $this->teacher->assignRole('teacher');
    }

    /** @test */
    public function exam_belongs_to_teacher()
    {
        $exam = Exam::factory()->create([
            'teacher_id' => $this->teacher->id
        ]);

        $this->assertInstanceOf(User::class, $exam->teacher);
        $this->assertEquals($this->teacher->id, $exam->teacher->id);
    }

    /** @test */
    public function exam_has_many_questions()
    {
        $exam = Exam::factory()->create([
            'teacher_id' => $this->teacher->id
        ]);

        $questions = Question::factory()->count(3)->create([
            'exam_id' => $exam->id
        ]);

        $this->assertCount(3, $exam->questions);
        $this->assertInstanceOf(Question::class, $exam->questions->first());
    }

    /** @test */
    public function exam_has_many_assignments()
    {
        $exam = Exam::factory()->create([
            'teacher_id' => $this->teacher->id
        ]);

        $assignments = ExamAssignment::factory()->count(2)->create([
            'exam_id' => $exam->id
        ]);

        $this->assertCount(2, $exam->assignments);
        $this->assertInstanceOf(ExamAssignment::class, $exam->assignments->first());
    }

    /** @test */
    public function exam_calculates_total_points()
    {
        $exam = Exam::factory()->create([
            'teacher_id' => $this->teacher->id
        ]);

        Question::factory()->create([
            'exam_id' => $exam->id,
            'points' => 5
        ]);

        Question::factory()->create([
            'exam_id' => $exam->id,
            'points' => 10
        ]);

        $this->assertEquals(15, $exam->total_points);
    }

    /** @test */
    public function exam_counts_unique_participants()
    {
        $exam = Exam::factory()->create([
            'teacher_id' => $this->teacher->id
        ]);

        $student1 = User::factory()->create();
        $student2 = User::factory()->create();

        // Créer des assignations pour les étudiants
        ExamAssignment::factory()->create([
            'exam_id' => $exam->id,
            'student_id' => $student1->id
        ]);

        ExamAssignment::factory()->create([
            'exam_id' => $exam->id,
            'student_id' => $student2->id
        ]);

        $this->assertEquals(2, $exam->unique_participants_count);
    }

    /** @test */
    public function exam_has_correct_fillable_attributes()
    {
        $fillable = (new Exam())->getFillable();

        $expectedFillable = [
            'title',
            'description',
            'duration',
            'start_time',
            'end_time',
            'is_active',
            'teacher_id',
        ];

        $this->assertEquals($expectedFillable, $fillable);
    }

    /** @test */
    public function exam_casts_attributes_correctly()
    {
        $exam = Exam::factory()->create([
            'teacher_id' => $this->teacher->id,
            'start_time' => '2025-01-01 10:00:00',
            'end_time' => '2025-01-01 12:00:00',
            'is_active' => 1
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $exam->start_time);
        $this->assertInstanceOf(\Carbon\Carbon::class, $exam->end_time);
        $this->assertIsBool($exam->is_active);
        $this->assertTrue($exam->is_active);
    }

    /** @test */
    public function exam_can_determine_if_active()
    {
        $activeExam = Exam::factory()->create([
            'teacher_id' => $this->teacher->id,
            'is_active' => true
        ]);

        $inactiveExam = Exam::factory()->create([
            'teacher_id' => $this->teacher->id,
            'is_active' => false
        ]);

        $this->assertTrue($activeExam->is_active);
        $this->assertFalse($inactiveExam->is_active);
    }

    /** @test */
    public function exam_has_answers_through_questions()
    {
        $exam = Exam::factory()->create([
            'teacher_id' => $this->teacher->id
        ]);

        $question = Question::factory()->create([
            'exam_id' => $exam->id
        ]);

        // Créer un assignment
        $assignment = ExamAssignment::factory()->create([
            'exam_id' => $exam->id,
            'student_id' => User::factory()->create()->id
        ]);

        $answer = Answer::factory()->create([
            'question_id' => $question->id,
            'assignment_id' => $assignment->id
        ]);

        $this->assertCount(1, $exam->answers);
        $this->assertEquals($answer->id, $exam->answers->first()->id);
    }

    /** @test */
    public function exam_questions_are_ordered_by_order_index()
    {
        $exam = Exam::factory()->create([
            'teacher_id' => $this->teacher->id
        ]);

        // Créer des questions dans un ordre différent
        $question3 = Question::factory()->create([
            'exam_id' => $exam->id,
            'order_index' => 3,
            'content' => 'Third question'
        ]);

        $question1 = Question::factory()->create([
            'exam_id' => $exam->id,
            'order_index' => 1,
            'content' => 'First question'
        ]);

        $question2 = Question::factory()->create([
            'exam_id' => $exam->id,
            'order_index' => 2,
            'content' => 'Second question'
        ]);

        $orderedQuestions = $exam->questions;

        $this->assertEquals('First question', $orderedQuestions[0]->content);
        $this->assertEquals('Second question', $orderedQuestions[1]->content);
        $this->assertEquals('Third question', $orderedQuestions[2]->content);
    }
}
