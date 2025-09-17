<?php

namespace Tests\Unit;

use App\Helpers\PermissionHelper;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionHelperTest extends TestCase
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
    public function returns_null_when_user_not_authenticated()
    {
        $this->assertNull(PermissionHelper::getUserMainRole());
    }

    /** @test */
    public function returns_admin_role_from_database_column()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Auth::login($admin);

        $this->assertEquals('admin', PermissionHelper::getUserMainRole());
    }

    /** @test */
    public function returns_teacher_role_from_database_column()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        Auth::login($teacher);

        $this->assertEquals('teacher', PermissionHelper::getUserMainRole());
    }

    /** @test */
    public function returns_student_role_from_database_column()
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        Auth::login($student);

        $this->assertEquals('student', PermissionHelper::getUserMainRole());
    }

    /** @test */
    public function fallback_to_spatie_role_when_database_role_is_empty()
    {
        $user = User::factory()->create();
        $user->assignRole('teacher');

        Auth::login($user);

        $this->assertEquals('teacher', PermissionHelper::getUserMainRole());
    }

    /** @test */
    public function admin_dashboard_route_is_correct()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Auth::login($admin);

        $this->assertEquals('/dashboard/admin', PermissionHelper::getDashboardRoute());
    }

    /** @test */
    public function teacher_dashboard_route_is_correct()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        Auth::login($teacher);

        $this->assertEquals('/dashboard/teacher', PermissionHelper::getDashboardRoute());
    }

    /** @test */
    public function student_dashboard_route_is_correct()
    {
        $student = User::factory()->create();
        $student->assignRole('student');
        Auth::login($student);

        $this->assertEquals('/dashboard/student', PermissionHelper::getDashboardRoute());
    }

    /** @test */
    public function default_dashboard_route_when_no_role()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $this->assertEquals('/dashboard', PermissionHelper::getDashboardRoute());
    }

    /** @test */
    public function admin_is_admin_returns_true()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Auth::login($admin);

        $this->assertTrue(PermissionHelper::isAdmin());
    }

    /** @test */
    public function teacher_is_admin_returns_false()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        Auth::login($teacher);

        $this->assertFalse(PermissionHelper::isAdmin());
    }

    /** @test */
    public function student_is_admin_returns_false()
    {
        $student = User::factory()->create();
        $student->assignRole('student');
        Auth::login($student);

        $this->assertFalse(PermissionHelper::isAdmin());
    }
}