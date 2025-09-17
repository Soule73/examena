<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Choice;
use App\Models\Answer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer les rôles Spatie
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'teacher']);
        Role::create(['name' => 'student']);
    }

    /** @test */
    public function user_has_correct_fillable_attributes()
    {
        $user = new User();
        $expected = ['name', 'email', 'password'];
        
        $this->assertEquals($expected, $user->getFillable());
    }

    /** @test */
    public function user_password_is_hidden()
    {
        $user = User::factory()->create();
        $userArray = $user->toArray();
        
        $this->assertArrayNotHasKey('password', $userArray);
    }

    /** @test */
    public function user_can_have_spatie_roles()
    {
        $user = User::factory()->create();
        $user->assignRole('teacher');
        
        $this->assertTrue($user->hasRole('teacher'));
        $this->assertFalse($user->hasRole('admin'));
    }

    /** @test */
    public function exam_belongs_to_teacher()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $exam = Exam::factory()->create(['teacher_id' => $teacher->id]);
        
        $this->assertInstanceOf(User::class, $exam->teacher);
        $this->assertEquals($teacher->id, $exam->teacher->id);
    }

    /** @test */
    public function exam_has_many_questions()
    {
        $exam = Exam::factory()->create();
        $question1 = Question::factory()->create(['exam_id' => $exam->id]);
        $question2 = Question::factory()->create(['exam_id' => $exam->id]);
        
        $this->assertCount(2, $exam->questions);
        $this->assertTrue($exam->questions->contains($question1));
        $this->assertTrue($exam->questions->contains($question2));
    }

    /** @test */
    public function question_belongs_to_exam()
    {
        $exam = Exam::factory()->create();
        $question = Question::factory()->create(['exam_id' => $exam->id]);
        
        $this->assertInstanceOf(Exam::class, $question->exam);
        $this->assertEquals($exam->id, $question->exam->id);
    }

    /** @test */
    public function question_has_many_choices()
    {
        $question = Question::factory()->create();
        $choice1 = Choice::factory()->create(['question_id' => $question->id]);
        $choice2 = Choice::factory()->create(['question_id' => $question->id]);
        
        $this->assertCount(2, $question->choices);
        $this->assertTrue($question->choices->contains($choice1));
        $this->assertTrue($question->choices->contains($choice2));
    }

    /** @test */
    public function choice_belongs_to_question()
    {
        $question = Question::factory()->create();
        $choice = Choice::factory()->create(['question_id' => $question->id]);
        
        $this->assertInstanceOf(Question::class, $choice->question);
        $this->assertEquals($question->id, $choice->question->id);
    }

    /** @test */
    public function answer_belongs_to_user_and_question()
    {
        $user = User::factory()->create();
        $question = Question::factory()->create();
        $answer = Answer::factory()->create([
            'user_id' => $user->id,
            'question_id' => $question->id
        ]);
        
        $this->assertInstanceOf(User::class, $answer->user);
        $this->assertInstanceOf(Question::class, $answer->question);
        $this->assertEquals($user->id, $answer->user->id);
        $this->assertEquals($question->id, $answer->question->id);
    }

    /** @test */
    public function user_has_many_answers()
    {
        $user = User::factory()->create();
        $question1 = Question::factory()->create();
        $question2 = Question::factory()->create();
        
        $answer1 = Answer::factory()->create([
            'user_id' => $user->id,
            'question_id' => $question1->id
        ]);
        $answer2 = Answer::factory()->create([
            'user_id' => $user->id,
            'question_id' => $question2->id
        ]);
        
        $this->assertCount(2, $user->answers);
        $this->assertTrue($user->answers->contains($answer1));
        $this->assertTrue($user->answers->contains($answer2));
    }

    /** @test */
    public function exam_has_correct_fillable_attributes()
    {
        $exam = new Exam();
        $expected = ['title', 'description', 'duration', 'start_time', 'end_time', 'is_active', 'teacher_id'];

        $this->assertEquals($expected, $exam->getFillable());
    }    /** @test */
    public function question_has_correct_fillable_attributes()
    {
        $question = new Question();
        $expected = ['exam_id', 'content', 'type', 'points'];

        $this->assertEquals($expected, $question->getFillable());
    }    /** @test */
    public function choice_has_correct_fillable_attributes()
    {
        $choice = new Choice();
        $expected = ['question_id', 'content', 'is_correct'];

        $this->assertEquals($expected, $choice->getFillable());
    }    /** @test */
    public function answer_has_correct_fillable_attributes()
    {
        $answer = new Answer();
        $expected = ['user_id', 'question_id', 'answer_text'];
        
        $this->assertEquals($expected, $answer->getFillable());
    }
}