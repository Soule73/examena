<?php

namespace Tests\Unit\Models;

use App\Models\Exam;
use App\Models\Question;
use App\Models\Choice;
use App\Models\Answer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_exam_belongs_to_teacher()
    {
        $teacher = User::factory()->create();
        $exam = Exam::factory()->create(['teacher_id' => $teacher->id]);

        $this->assertInstanceOf(User::class, $exam->teacher);
        $this->assertEquals($teacher->id, $exam->teacher->id);
    }

    public function test_exam_has_many_questions()
    {
        $exam = Exam::factory()->create();
        
        Question::factory()->count(3)->create(['exam_id' => $exam->id]);

        $this->assertCount(3, $exam->questions);
        $this->assertInstanceOf(Question::class, $exam->questions->first());
    }

    public function test_exam_calculates_total_points_correctly()
    {
        $exam = Exam::factory()->create();
        
        Question::factory()->create(['exam_id' => $exam->id, 'points' => 5]);
        Question::factory()->create(['exam_id' => $exam->id, 'points' => 10]);
        Question::factory()->create(['exam_id' => $exam->id, 'points' => 3]);

        $this->assertEquals(18, $exam->fresh()->total_points);
    }

    public function test_exam_casts_dates_correctly()
    {
        $exam = Exam::factory()->create([
            'start_time' => '2024-01-01 10:00:00',
            'end_time' => '2024-01-01 12:00:00',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $exam->start_time);
        $this->assertInstanceOf(\Carbon\Carbon::class, $exam->end_time);
    }

    public function test_exam_casts_is_active_to_boolean()
    {
        $exam = Exam::factory()->create(['is_active' => 1]);

        $this->assertTrue($exam->is_active);
        $this->assertIsBool($exam->is_active);
    }

    public function test_exam_fillable_attributes()
    {
        $fillable = [
            'title',
            'description',
            'duration',
            'start_time',
            'end_time',
            'is_active',
            'teacher_id',
        ];

        $exam = new Exam();
        $this->assertEquals($fillable, $exam->getFillable());
    }
}