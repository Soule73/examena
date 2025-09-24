<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\User;
use App\Models\Question;
use App\Models\ExamAssignment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Answer>
 */
class AnswerFactory extends Factory
{
    protected $model = Answer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assignment_id' => ExamAssignment::factory(),
            'question_id' => Question::factory(),
            'answer_text' => $this->faker->sentence(),
        ];
    }
}
