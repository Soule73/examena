<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\TestUsersSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Exécuter les seeders dans l'ordre
        $this->call([
            RoleAndPermissionSeeder::class,
            UserSeeder::class,
            ExamSeeder::class,
            QuestionSeeder::class,
            ChoiceSeeder::class,
            AnswerSeeder::class,
            ExamAssignmentSeeder::class,
        ]);
    }
}
