<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Choice;
use App\Models\ExamAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Inertia\Testing\AssertableInertia;

class TeacherExamControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;
    private User $student;
    private Exam $exam;

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
            'is_active' => true
        ]);
    }

    /** @test */
    public function teacher_can_access_exam_dashboard()
    {
        $response = $this->actingAs($this->teacher)
            ->get(route('teacher.exams.index'));

        $response->assertOk();
        $response->assertInertia(
            fn(AssertableInertia $page) => $page
                ->component('Teacher/ExamIndex', false)
                ->has('exams')
        );
    }

    /** @test */
    public function teacher_can_view_exam_assignment_form()
    {
        $response = $this->actingAs($this->teacher)
            ->get(route('teacher.exams.assign.show', $this->exam));

        $response->assertOk();
        $response->assertInertia(
            fn($page) => $page
                ->component('Teacher/ExamAssign', false)
                ->has('exam')
                ->has('students')
                ->has('alreadyAssigned')
        );
    }

    /** @test */
    public function teacher_can_assign_exam_to_students()
    {
        $response = $this->actingAs($this->teacher)
            ->post(route('teacher.exams.assign', $this->exam), [
                'student_ids' => [$this->student->id]
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Vérifier que l'assignation a été créée
        $this->assertDatabaseHas('exam_assignments', [
            'exam_id' => $this->exam->id,
            'student_id' => $this->student->id,
            'status' => 'assigned'
        ]);
    }

    /** @test */
    public function teacher_cannot_assign_exam_to_invalid_students()
    {
        $response = $this->actingAs($this->teacher)
            ->post(route('teacher.exams.assign', $this->exam), [
                'student_ids' => [999] // ID qui n'existe pas
            ]);

        $response->assertSessionHasErrors('student_ids.0');
    }

    /** @test */
    public function teacher_can_view_student_results()
    {
        // Créer une assignation soumise
        $assignment = ExamAssignment::factory()->create([
            'exam_id' => $this->exam->id,
            'student_id' => $this->student->id,
            'status' => 'submitted',
            'score' => 85.5
        ]);

        $response = $this->actingAs($this->teacher)
            ->get(route('teacher.exams.results.show', [$this->exam, $assignment]));

        $response->assertOk();
        $response->assertInertia(
            fn($page) => $page
                ->component('Teacher/Exam/StudentResults')
                ->has('exam')
                ->has('student')
                ->has('answers')
        );
        $response->assertViewHas(['assignment', 'student', 'exam', 'formattedAnswers']);
    }

    /** @test */
    public function teacher_can_update_student_score()
    {
        // Créer une question et une assignation
        $question = Question::factory()->create([
            'exam_id' => $this->exam->id,
            'type' => 'text',
            'points' => 10
        ]);

        $assignment = ExamAssignment::factory()->create([
            'exam_id' => $this->exam->id,
            'student_id' => $this->student->id,
            'status' => 'submitted'
        ]);

        $response = $this->actingAs($this->teacher)
            ->post(route('teacher.exams.score.update', [$this->exam, $assignment]), [
                'exam_id' => $this->exam->id,
                'student_id' => $this->student->id,
                'question_id' => $question->id,
                'score' => 8.5,
                'teacher_notes' => 'Good answer'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /** @test */
    public function teacher_cannot_access_other_teacher_exam()
    {
        // Créer un autre enseignant et son examen
        $otherTeacher = User::factory()->create(['role' => 'teacher']);
        $otherTeacher->assignRole('teacher');

        $otherExam = Exam::factory()->create([
            'teacher_id' => $otherTeacher->id
        ]);

        $response = $this->actingAs($this->teacher)
            ->get(route('teacher.exams.assign.show', $otherExam));

        $response->assertForbidden();
    }

    /** @test */
    public function student_cannot_access_teacher_routes()
    {
        $response = $this->actingAs($this->student)
            ->get(route('teacher.exams.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function teacher_can_view_exam_assignments_list()
    {
        // Créer quelques assignations
        ExamAssignment::factory()->count(3)->create([
            'exam_id' => $this->exam->id
        ]);

        $response = $this->actingAs($this->teacher)
            ->get(route('teacher.exams.assignments', $this->exam));

        $response->assertOk();
        $response->assertInertia(
            fn($page) => $page
                ->component('Teacher/ExamAssignments', false)
                ->has('exam')
                ->has('assignments')
        );
    }

    /** @test */
    public function teacher_can_filter_assignments_by_status()
    {
        // Créer des assignations avec différents statuts
        ExamAssignment::factory()->create([
            'exam_id' => $this->exam->id,
            'status' => 'assigned'
        ]);

        ExamAssignment::factory()->create([
            'exam_id' => $this->exam->id,
            'status' => 'submitted'
        ]);

        $response = $this->actingAs($this->teacher)
            ->get(route('teacher.exams.assignments', $this->exam) . '?status=submitted');

        $response->assertOk();
        // Vérifier que seules les assignations soumises sont affichées
        $response->assertInertia(
            fn($page) => $page
                ->component('Teacher/ExamAssignments', false)
                ->has('assignments')
                ->where('assignments.data', function ($assignments) {
                    foreach ($assignments as $assignment) {
                        if ($assignment['status'] !== 'submitted') {
                            return false;
                        }
                    }
                    return true;
                })
        );
    }

    /** @test */
    public function teacher_can_save_student_review()
    {
        // Créer une question pour l'examen
        $question = Question::factory()->create([
            'exam_id' => $this->exam->id,
            'type' => 'text',
            'points' => 10
        ]);

        $assignment = ExamAssignment::factory()->create([
            'exam_id' => $this->exam->id,
            'student_id' => $this->student->id,
            'status' => 'submitted'
        ]);

        $response = $this->actingAs($this->teacher)
            ->post(route('teacher.exams.review.save', [$this->exam, $assignment]), [
                'exam_id' => $this->exam->id,
                'student_id' => $this->student->id,
                'question_id' => $question->id,
                'score' => 92.5,
                'teacher_notes' => 'Excellent work overall',
                'final_score' => 92.5
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Vérifier que les notes ont été sauvegardées
        $assignment->refresh();
        $this->assertEquals('Excellent work overall', $assignment->teacher_notes);
        $this->assertEquals(92.5, $assignment->score);
    }
}
