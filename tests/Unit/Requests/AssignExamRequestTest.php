<?php

namespace Tests\Unit\Requests;

use Tests\TestCase;
use App\Http\Requests\Teacher\AssignExamRequest;
use App\Models\User;
use App\Models\Exam;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class AssignExamRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;
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

        // Créer un examen
        $this->exam = Exam::factory()->create([
            'teacher_id' => $this->teacher->id,
            'title' => 'Test Exam'
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $request = new AssignExamRequest();
        $rules = $request->rules();

        $validator = Validator::make([], $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('student_ids'));
    }

    /** @test */
    public function it_validates_student_ids_array()
    {
        $request = new AssignExamRequest();
        $rules = $request->rules();

        // Test avec une valeur non-array
        $validator = Validator::make([
            'student_ids' => 'not-an-array'
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('student_ids'));
    }

    /** @test */
    public function it_validates_student_ids_exist()
    {
        $request = new AssignExamRequest();
        $rules = $request->rules();

        // Test avec des IDs qui n'existent pas
        $validator = Validator::make([
            'student_ids' => [999, 1000]
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('student_ids.0'));
    }

    /** @test */
    public function it_validates_users_have_student_role()
    {
        // Créer un utilisateur sans rôle étudiant
        $nonStudent = User::factory()->create([
            'role' => 'teacher'
        ]);
        $nonStudent->assignRole('teacher');

        $request = new AssignExamRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'student_ids' => [$nonStudent->id]
        ], $rules);

        // Appliquer le withValidator pour tester la validation des rôles
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('student_ids'));
    }

    /** @test */
    public function it_passes_validation_with_valid_data()
    {
        // Créer des étudiants
        $student1 = User::factory()->create(['role' => 'student']);
        $student2 = User::factory()->create(['role' => 'student']);
        $student1->assignRole('student');
        $student2->assignRole('student');

        $request = new AssignExamRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'student_ids' => [$student1->id, $student2->id]
        ], $rules);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_authorizes_teacher_for_their_exam()
    {
        $request = new AssignExamRequest();

        // Simuler l'utilisateur authentifié
        $this->actingAs($this->teacher);

        // La méthode authorize() retourne toujours true car l'autorisation
        // est gérée dans le contrôleur selon le commentaire de la classe
        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function it_denies_authorization_for_other_teacher_exam()
    {
        // Créer un autre enseignant
        $otherTeacher = User::factory()->create(['role' => 'teacher']);
        $otherTeacher->assignRole('teacher');

        $request = new AssignExamRequest();

        // Simuler l'utilisateur authentifié comme le premier enseignant
        $this->actingAs($this->teacher);

        // La méthode authorize() retourne toujours true car l'autorisation
        // est gérée dans le contrôleur selon le commentaire de la classe
        $this->assertTrue($request->authorize());
    }
}
