<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Exécuter le seeder des rôles et permissions
        $this->call([
            RoleAndPermissionSeeder::class,
        ]);

        // User::factory(10)->create();

        // Créer des utilisateurs de test avec des rôles
        $teacher = User::factory()->create([
            'name' => 'Enseignant Test',
            'email' => 'teacher@examena.com',
            'role' => 'teacher',
        ]);
        $teacher->assignRole('teacher');

        $student = User::factory()->create([
            'name' => 'Étudiant Test',
            'email' => 'student@examena.com',
            'role' => 'student',
        ]);
        $student->assignRole('student');

        $admin = User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'admin@examena.com',
            'role' => 'teacher', // Un admin peut être considéré comme un enseignant étendu
        ]);
        $admin->assignRole('admin');
    }
}
