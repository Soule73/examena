<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExamAssignment;

class ExamAssignmentSeeder extends Seeder
{
    public function run()
    {
        $students = \App\Models\User::role('student')->get();
        $exams = \App\Models\Exam::all();

        foreach ($students as $student) {
            foreach ($exams as $exam) {
                \App\Models\ExamAssignment::create([
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                    'assigned_at' => now(),
                    'status' => 'assigned',
                ]);
            }
        }
    }
}
