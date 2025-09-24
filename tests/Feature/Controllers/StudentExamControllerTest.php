<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Choice;
use App\Models\Answer;
use App\Models\ExamAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class StudentExamControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;
    private User $student;
    private Exam $exam;
    private ExamAssignment $assignment;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer les rôles
        Role::create(['name' => 'teacher']);
        Role::create(['name' => 'student']);

        // Créer un enseignant
        $this->teacher = User::factory()->create([
            'email' => 'teacher@test.com',
            'role' => 'teacher'
        ]);
        $this->teacher->assignRole('teacher');

        // Créer un étudiant
        $this->student = User::factory()->create([
            'email' => 'student@test.com',
            'role' => 'student'
        ]);
        $this->student->assignRole('student');

        // Créer un examen
        $this->exam = Exam::factory()->create([
            'teacher_id' => $this->teacher->id,
            'title' => 'Test Exam',
            'is_active' => true,
            'duration' => 90
        ]);

        // Créer une assignation
        $this->assignment = ExamAssignment::factory()->create([
            'exam_id' => $this->exam->id,
            'student_id' => $this->student->id,
            'status' => 'assigned'
        ]);
    }

    /** @test */
    public function student_can_access_exam_dashboard()
    {
        $response = $this->actingAs($this->student)
            ->get(route('student.exams.index'));

        $response->assertOk();
        $response->assertInertia(
            fn($page) => $page
                ->component('Student/ExamIndex', false)
                ->has('assignments')
        );
    }

    /** @test */
    public function student_can_start_assigned_exam()
    {
        $response = $this->actingAs($this->student)
            ->get(route('student.exams.show', $this->assignment));

        $response->assertOk();
        $response->assertInertia(
            fn($page) => $page
                ->component('Student/ExamShow', false)
                ->has('assignment')
                ->has('exam')
                ->has('questions')
        );

        // Vérifier que l'examen a été marqué comme commencé
        $this->assignment->refresh();
        $this->assertEquals('started', $this->assignment->status);
        $this->assertNotNull($this->assignment->started_at);
    }

    /** @test */
    public function student_cannot_start_exam_twice()
    {
        // Marquer l'examen comme déjà commencé
        $this->assignment->update([
            'status' => 'started',
            'started_at' => Carbon::now()
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('student.exams.show', $this->assignment));

        $response->assertOk();
        // L'examen devrait être affiché normalement
        $response->assertInertia(
            fn($page) => $page
                ->component('Student/ExamShow', false)
        );
    }

    /** @test */
    public function student_can_submit_text_answer()
    {
        // Créer une question de type texte
        $question = Question::factory()->create([
            'exam_id' => $this->exam->id,
            'type' => 'text',
            'content' => 'What is 2 + 2?',
            'points' => 5
        ]);

        $response = $this->actingAs($this->student)
            ->post(route('student.exams.answer.submit', [$this->assignment, $question]), [
                'answer_text' => 'Four'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Vérifier que la réponse a été sauvegardée
        $this->assertDatabaseHas('answers', [
            'assignment_id' => $this->assignment->id,
            'question_id' => $question->id,
            'answer_text' => 'Four'
        ]);
    }

    /** @test */
    public function student_can_submit_multiple_choice_answer()
    {
        // Créer une question à choix multiples
        $question = Question::factory()->create([
            'exam_id' => $this->exam->id,
            'type' => 'multiple',
            'content' => 'Which are prime numbers?',
            'points' => 3
        ]);

        // Créer des choix
        $choice1 = Choice::factory()->create([
            'question_id' => $question->id,
            'content' => '2',
            'is_correct' => true
        ]);

        $choice2 = Choice::factory()->create([
            'question_id' => $question->id,
            'content' => '4',
            'is_correct' => false
        ]);

        $response = $this->actingAs($this->student)
            ->post(route('student.exams.answer.submit', [$this->assignment, $question]), [
                'choice_id' => $choice1->id
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Vérifier que la réponse a été sauvegardée avec le bon choix
        $this->assertDatabaseHas('answers', [
            'assignment_id' => $this->assignment->id,
            'question_id' => $question->id,
            'choice_id' => $choice1->id
        ]);
    }

    /** @test */
    public function student_can_submit_exam()
    {
        // Marquer l'examen comme commencé
        $this->assignment->update(['status' => 'started']);

        $response = $this->actingAs($this->student)
            ->post(route('student.exams.submit', $this->assignment));

        $response->assertRedirect(route('student.exams.index'));
        $response->assertSessionHas('success');

        // Vérifier que l'examen a été soumis
        $this->assignment->refresh();
        $this->assertEquals('submitted', $this->assignment->status);
        $this->assertNotNull($this->assignment->submitted_at);
    }

    /** @test */
    public function student_cannot_submit_unstarted_exam()
    {
        // L'examen est encore en statut "assigned"
        $response = $this->actingAs($this->student)
            ->post(route('student.exams.submit', $this->assignment));

        $response->assertSessionHasErrors();

        // Vérifier que le statut n'a pas changé
        $this->assignment->refresh();
        $this->assertEquals('assigned', $this->assignment->status);
    }

    /** @test */
    public function student_can_view_completed_exam_results()
    {
        // Marquer l'examen comme terminé avec une note
        $this->assignment->update([
            'status' => 'graded',
            'score' => 85.5
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('student.exams.results', $this->assignment));

        $response->assertOk();
        $response->assertInertia(
            fn($page) => $page
                ->component('Student/Exam/Results', false)
                ->has('assignment')
                ->has('exam')
                ->has('questions')
                ->has('userAnswers')
        );
    }

    /** @test */
    public function student_cannot_view_results_of_ungraded_exam()
    {
        // L'examen est soumis mais pas encore noté
        $this->assignment->update(['status' => 'submitted']);

        $response = $this->actingAs($this->student)
            ->get(route('student.exams.results', $this->assignment));

        $response->assertForbidden();
    }

    /** @test */
    public function student_cannot_access_other_student_exam()
    {
        // Créer un autre étudiant et son assignation
        $otherStudent = User::factory()->create(['role' => 'student']);
        $otherStudent->assignRole('student');

        $otherAssignment = ExamAssignment::factory()->create([
            'exam_id' => $this->exam->id,
            'student_id' => $otherStudent->id
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('student.exams.show', $otherAssignment));

        $response->assertForbidden();
    }

    /** @test */
    public function teacher_cannot_access_student_routes()
    {
        $response = $this->actingAs($this->teacher)
            ->get(route('student.exams.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function student_can_update_existing_answer()
    {
        // Créer une question et une réponse existante
        $question = Question::factory()->create([
            'exam_id' => $this->exam->id,
            'type' => 'text',
            'content' => 'Test question'
        ]);

        $existingAnswer = Answer::factory()->create([
            'assignment_id' => $this->assignment->id,
            'question_id' => $question->id,
            'answer_text' => 'Old answer'
        ]);

        $response = $this->actingAs($this->student)
            ->post(route('student.exams.answer.submit', [$this->assignment, $question]), [
                'answer_text' => 'Updated answer'
            ]);

        $response->assertRedirect();

        // Vérifier que la réponse a été mise à jour
        $existingAnswer->refresh();
        $this->assertEquals('Updated answer', $existingAnswer->answer_text);
    }
}
