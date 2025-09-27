<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Answer;

class AnswerSeeder extends Seeder
{
    public function run()
    {
        Answer::factory()->count(30)->create();
    }
}
