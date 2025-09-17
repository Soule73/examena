<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer les rôles Spatie
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'teacher']);
        Role::create(['name' => 'student']);
    }

    /** @test */
    public function user_can_access_login_page()
    {
        $response = $this->get('/login');
        
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /** @test */
    public function user_cannot_access_login_when_authenticated()
    {
        /** @var User $student */
        $student = User::factory()->create();
        $student->assignRole('student');

        $response = $this->actingAs($student)->get('/login');

        $response->assertRedirect('/dashboard');
    }

    /** @test */
    public function admin_user_redirects_to_admin_dashboard_after_login()
    {
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole('admin');

        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard/admin');
        $this->assertAuthenticatedAs($admin);
    }

    /** @test */
    public function teacher_user_redirects_to_teacher_dashboard_after_login()
    {

        $teacher = User::factory()->create([
            'email' => 'teacher@test.com',
            'password' => Hash::make('password123'),
        ]);
        $teacher->assignRole('teacher');

        $response = $this->post('/login', [
            'email' => 'teacher@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard/teacher');
        $this->assertAuthenticatedAs($teacher);
    }

    /** @test */
    public function student_user_redirects_to_student_dashboard_after_login()
    {
        $student = User::factory()->create([
            'email' => 'student@test.com',
            'password' => Hash::make('password123'),
        ]);
        $student->assignRole('student');

        $response = $this->post('/login', [
            'email' => 'student@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard/student');
        $this->assertAuthenticatedAs($student);
    }

    /** @test */
    public function user_with_invalid_credentials_cannot_login()
    {
        $response = $this->post('/login', [
            'email' => 'invalid@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function user_can_logout()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('student');

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /** @test */
    public function login_form_validates_required_fields()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    /** @test */
    public function login_form_validates_email_format()
    {
        $response = $this->post('/login', [
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }
}