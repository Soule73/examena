<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
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
    public function guest_cannot_access_protected_routes()
    {
        $routes = [
            '/dashboard',
            '/dashboard/admin',
            '/dashboard/teacher',
            '/dashboard/student',
            '/admin/users'
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    /** @test */
    public function admin_can_access_admin_routes()
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/dashboard/admin');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200);
    }

    /** @test */
    public function teacher_can_access_teacher_routes()
    {
        /** @var User $teacher */
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        $response = $this->actingAs($teacher)->get('/dashboard/teacher');
        $response->assertStatus(200);
    }

    /** @test */
    public function student_can_access_student_routes()
    {
        /** @var User $student */
        $student = User::factory()->create();
        $student->assignRole('student');

        $response = $this->actingAs($student)->get('/dashboard/student');
        $response->assertStatus(200);
    }

    /** @test */
    public function teacher_cannot_access_admin_routes()
    {
        /** @var User $teacher */
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        $response = $this->actingAs($teacher)->get('/dashboard/admin');
        $response->assertStatus(403);

        $response = $this->actingAs($teacher)->get('/admin/users');
        $response->assertStatus(403);
    }

    /** @test */
    public function student_cannot_access_admin_routes()
    {
        /** @var User $student */
        $student = User::factory()->create();
        $student->assignRole('student');

        $response = $this->actingAs($student)->get('/dashboard/admin');
        $response->assertStatus(403);

        $response = $this->actingAs($student)->get('/admin/users');
        $response->assertStatus(403);
    }

    /** @test */
    public function student_cannot_access_teacher_routes()
    {
        /** @var User $student */
        $student = User::factory()->create();
        $student->assignRole('student');

        $response = $this->actingAs($student)->get('/dashboard/teacher');
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_cannot_access_teacher_specific_routes()
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/dashboard/teacher');
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_cannot_access_student_specific_routes()
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/dashboard/student');
        $response->assertStatus(403);
    }

    /** @test */
    public function all_authenticated_users_can_access_general_dashboard()
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        /** @var User $teacher */
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        /** @var User $student */
        $student = User::factory()->create();
        $student->assignRole('student');

        // Le dashboard général redirige vers le dashboard spécifique selon le rôle
        $this->actingAs($admin)->get('/dashboard')->assertRedirect('/dashboard/admin');
        $this->actingAs($teacher)->get('/dashboard')->assertRedirect('/dashboard/teacher');
        $this->actingAs($student)->get('/dashboard')->assertRedirect('/dashboard/student');
    }
}