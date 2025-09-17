<?php

namespace Tests\Unit\Models;

use App\Models\Question;
use App\Models\Exam;
use App\Models\Choice;
use App\Models\Answer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_question_belongs_to_exam()
    {
        $exam = Exam::factory()->create();
        $question = Question::factory()->create(['exam_id' => $exam->id]);

        $this->assertInstanceOf(Exam::class, $question->exam);
        $this->assertEquals($exam->id, $question->exam->id);
    }

    public function test_question_has_many_choices()
    {
        $question = Question::factory()->create();
        
        Choice::factory()->count(4)->create(['question_id' => $question->id]);

        $this->assertCount(4, $question->choices);
        $this->assertInstanceOf(Choice::class, $question->choices->first());
    }

    public function test_question_has_many_answers()
    {
        $question = Question::factory()->create();
        
        Answer::factory()->count(2)->create(['question_id' => $question->id]);

        $this->assertCount(2, $question->answers);
        $this->assertInstanceOf(Answer::class, $question->answers->first());
    }

    public function test_question_fillable_attributes()
    {
        $fillable = [
            'exam_id',
            'content',
            'type',
            'points',
        ];

        $question = new Question();
        $this->assertEquals($fillable, $question->getFillable());
    }

    public function test_question_types_are_valid()
    {
        $validTypes = ['text', 'multiple_choice', 'true_false'];

        foreach ($validTypes as $type) {
            $question = Question::factory()->create(['type' => $type]);
            $this->assertEquals($type, $question->type);
        }
    }
}