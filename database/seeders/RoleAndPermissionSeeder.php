<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les permissions
        $permissions = [
            // Permissions pour les examens
            'create-exams',
            'edit-exams',
            'delete-exams',
            'view-exams',
            'view-own-exams',
            
            // Permissions pour les questions
            'create-questions',
            'edit-questions',
            'delete-questions',
            'view-questions',
            
            // Permissions pour les réponses
            'submit-answers',
            'view-answers',
            'view-own-answers',
            'grade-answers',
            
            // Permissions pour les résultats
            'view-results',
            'view-own-results',
            'view-all-results',
            
            // Permissions administratives
            'manage-users',
            'export-data',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Créer les rôles et assigner les permissions

        // Rôle Étudiant
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $studentRole->givePermissionTo([
            'view-own-exams',
            'view-questions',
            'submit-answers',
            'view-own-answers',
            'view-own-results',
        ]);

        // Rôle Enseignant
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $teacherRole->givePermissionTo([
            'create-exams',
            'edit-exams',
            'delete-exams',
            'view-exams',
            'create-questions',
            'edit-questions',
            'delete-questions',
            'view-questions',
            'view-answers',
            'grade-answers',
            'view-results',
            'view-all-results',
            'export-data',
        ]);

        // Optionnel : Créer un super admin avec tous les permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());
    }
}
