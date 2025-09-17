<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    protected $model = Question::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exam_id' => Exam::factory(),
            'content' => $this->faker->sentence() . '?',
            'type' => $this->faker->randomElement(['multiple_choice', 'text']),
        ];
    }
}
