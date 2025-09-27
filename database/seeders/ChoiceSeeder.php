<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Choice;

class ChoiceSeeder extends Seeder
{
    public function run()
    {
        Choice::factory()->count(40)->create();
    }
}
