<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer les rôles Spatie
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'teacher']);
        Role::create(['name' => 'student']);

        // Créer un admin pour les tests
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com'
        ]);
        $this->admin->assignRole('admin');
    }

    /** @test */
    public function admin_can_access_users_index()
    {
        $response = $this->actingAs($this->admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
    }

    /** @test */
    public function non_admin_cannot_access_users_index()
    {
        /** @var User $student */
        $student = User::factory()->create();
        $student->assignRole('student');

        $response = $this->actingAs($student)->get('/admin/users');

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_access_create_user_form()
    {
        $response = $this->actingAs($this->admin)->get('/admin/users/create');

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.create');
    }

    /** @test */
    public function admin_can_create_new_user()
    {
        $userData = [
            'name' => 'Nouveau Utilisateur',
            'email' => 'nouveau@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student'
        ];

        $response = $this->actingAs($this->admin)->post('/admin/users', $userData);

        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Nouveau Utilisateur',
            'email' => 'nouveau@test.com'
        ]);

        // Vérifier que le rôle Spatie est assigné
        $user = User::where('email', 'nouveau@test.com')->first();
        $this->assertTrue($user->hasRole('student'));
    }

    /** @test */
    public function admin_can_access_edit_user_form()
    {
        $user = User::factory()->create();
        $user->assignRole('student');

        $response = $this->actingAs($this->admin)->get("/admin/users/{$user->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.edit');
        $response->assertViewHas('user', $user);
    }

    /** @test */
    public function admin_can_update_user()
    {
        $user = User::factory()->create([
            'name' => 'Ancien Nom',
            'email' => 'ancien@test.com'
        ]);
        $user->assignRole('student');

        $updateData = [
            'name' => 'Nouveau Nom',
            'email' => 'nouveau@test.com',
            'role' => 'teacher'
        ];

        $response = $this->actingAs($this->admin)
                        ->put("/admin/users/{$user->id}", $updateData);

        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('Nouveau Nom', $user->name);
        $this->assertEquals('nouveau@test.com', $user->email);
        $this->assertTrue($user->hasRole('teacher'));
        $this->assertFalse($user->hasRole('student'));
    }

    /** @test */
    public function admin_can_update_user_password()
    {
        $user = User::factory()->create();
        $user->assignRole('student');
        
        $updateData = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'nouveaumotdepasse',
            'password_confirmation' => 'nouveaumotdepasse',
            'role' => 'student'
        ];

        $response = $this->actingAs($this->admin)
                        ->put("/admin/users/{$user->id}", $updateData);

        $response->assertRedirect('/admin/users');
        
        $user->refresh();
        $this->assertTrue(Hash::check('nouveaumotdepasse', $user->password));
    }

    /** @test */
    public function admin_can_delete_user()
    {
        $user = User::factory()->create();
        $user->assignRole('student');

        $response = $this->actingAs($this->admin)
                        ->delete("/admin/users/{$user->id}");

        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function create_user_validation_works()
    {
        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'role' => 'invalid-role'
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'role']);
    }

    /** @test */
    public function email_must_be_unique_when_creating_user()
    {
        $existingUser = User::factory()->create(['email' => 'existing@test.com']);

        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'Test User',
            'email' => 'existing@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student'
        ]);

        $response->assertSessionHasErrors('email');
    }
}