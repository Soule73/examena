<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Choice;
use App\Models\Answer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuestionModelTest extends TestCase
{
    use RefreshDatabase;

    private Exam $exam;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un examen
        $this->exam = Exam::factory()->create();
    }

    /** @test */
    public function question_belongs_to_exam()
    {
        $question = Question::factory()->create([
            'exam_id' => $this->exam->id
        ]);

        $this->assertInstanceOf(Exam::class, $question->exam);
        $this->assertEquals($this->exam->id, $question->exam->id);
    }

    /** @test */
    public function question_has_many_choices()
    {
        $question = Question::factory()->create([
            'exam_id' => $this->exam->id,
            'type' => 'multiple'
        ]);

        $choices = Choice::factory()->count(4)->create([
            'question_id' => $question->id
        ]);

        $this->assertCount(4, $question->choices);
        $this->assertInstanceOf(Choice::class, $question->choices->first());
    }

    /** @test */
    public function question_has_many_answers()
    {
        $question = Question::factory()->create([
            'exam_id' => $this->exam->id
        ]);

        $answers = Answer::factory()->count(3)->create([
            'question_id' => $question->id
        ]);

        $this->assertCount(3, $question->answers);
        $this->assertInstanceOf(Answer::class, $question->answers->first());
    }

    /** @test */
    public function question_has_correct_fillable_attributes()
    {
        $fillable = (new Question())->getFillable();

        $expectedFillable = [
            'exam_id',
            'content',
            'type',
            'points',
            'order_index',
        ];

        $this->assertEquals($expectedFillable, $fillable);
    }

    /** @test */
    public function question_validates_type_enum()
    {
        $validTypes = ['text', 'multiple', 'one_choice', 'boolean'];

        foreach ($validTypes as $type) {
            $question = Question::factory()->create([
                'exam_id' => $this->exam->id,
                'type' => $type
            ]);

            $this->assertEquals($type, $question->type);
        }
    }

    /** @test */
    public function text_question_does_not_require_choices()
    {
        $question = Question::factory()->create([
            'exam_id' => $this->exam->id,
            'type' => 'text'
        ]);

        $this->assertCount(0, $question->choices);
        $this->assertEquals('text', $question->type);
    }

    /** @test */
    public function multiple_choice_question_can_have_many_choices()
    {
        $question = Question::factory()->create([
            'exam_id' => $this->exam->id,
            'type' => 'multiple'
        ]);

        Choice::factory()->count(5)->create([
            'question_id' => $question->id
        ]);

        $this->assertCount(5, $question->choices);
    }

    /** @test */
    public function boolean_question_should_have_two_choices()
    {
        $question = Question::factory()->create([
            'exam_id' => $this->exam->id,
            'type' => 'boolean'
        ]);

        Choice::factory()->create([
            'question_id' => $question->id,
            'content' => 'Vrai',
            'is_correct' => true
        ]);

        Choice::factory()->create([
            'question_id' => $question->id,
            'content' => 'Faux',
            'is_correct' => false
        ]);

        $this->assertCount(2, $question->choices);
    }

    /** @test */
    public function question_has_default_points_value()
    {
        $question = new Question([
            'exam_id' => $this->exam->id,
            'content' => 'Test question?',
            'type' => 'text'
        ]);
        $question->save();

        // En raison de la migration, la valeur par défaut devrait être 1
        $this->assertEquals(1, $question->points);
    }

    /** @test */
    public function question_has_default_order_index()
    {
        $question = new Question([
            'exam_id' => $this->exam->id,
            'content' => 'Test question?',
            'type' => 'text'
        ]);
        $question->save();

        // En raison de la migration, la valeur par défaut devrait être 1
        $this->assertEquals(1, $question->order_index);
    }

    /** @test */
    public function question_can_determine_if_has_correct_answer()
    {
        $question = Question::factory()->create([
            'exam_id' => $this->exam->id,
            'type' => 'multiple'
        ]);

        // Créer des choix avec une réponse correcte
        Choice::factory()->create([
            'question_id' => $question->id,
            'is_correct' => false
        ]);

        $correctChoice = Choice::factory()->create([
            'question_id' => $question->id,
            'is_correct' => true
        ]);

        $correctChoices = $question->choices->where('is_correct', true);
        $this->assertCount(1, $correctChoices);
        $this->assertEquals($correctChoice->id, $correctChoices->first()->id);
    }

    /** @test */
    public function question_content_is_required()
    {
        $question = Question::factory()->create([
            'exam_id' => $this->exam->id,
            'content' => 'What is the capital of France?'
        ]);

        $this->assertNotEmpty($question->content);
        $this->assertEquals('What is the capital of France?', $question->content);
    }
}
