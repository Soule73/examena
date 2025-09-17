<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\PermissionHelper;

class DashboardController extends Controller
{
    /**
     * Dashboard principal - redirection selon le rôle
     */
    public function index()
    {
        $role = PermissionHelper::getUserMainRole();
        
        return match($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default => abort(403, 'Rôle non reconnu')
        };
    }

    /**
     * Dashboard étudiant
     */
    public function student()
    {
        $user = Auth::user();
        
        // Calculer les statistiques pour l'étudiant
        $exams_available = \App\Models\Exam::count();
        $exams_completed = \App\Models\Answer::where('user_id', $user->id)->distinct('exam_id')->count();
        
        // Calculer la moyenne des scores
        $user_answers = \App\Models\Answer::where('user_id', $user->id)->get();
        $average_score = 0;
        if ($user_answers->count() > 0) {
            $total_questions = $user_answers->count();
            $correct_answers = 0;
            
            foreach ($user_answers as $answer) {
                $question = $answer->question;
                if ($question && $question->type === 'multiple_choice') {
                    $correctChoice = $question->choices()->where('is_correct', true)->first();
                    if ($correctChoice && $answer->answer_text === $correctChoice->label) {
                        $correct_answers++;
                    }
                }
            }
            
            $average_score = round(($correct_answers / $total_questions) * 100, 1);
        }

        $stats = [
            'exams_available' => $exams_available,
            'exams_completed' => $exams_completed,
            'average_score' => $average_score
        ];

        return view('dashboard.student', compact('user', 'stats'));
    }

    /**
     * Dashboard enseignant
     */
    public function teacher()
    {
        $user = Auth::user();
        
        // Calculer les statistiques pour l'enseignant
        $total_exams = \App\Models\Exam::where('teacher_id', $user->id)->count();
        $total_questions = \App\Models\Question::whereIn('exam_id', 
            \App\Models\Exam::where('teacher_id', $user->id)->pluck('id')
        )->count();
        
        // Nombre d'étudiants qui ont répondu à ses examens
        $students_evaluated = \App\Models\Answer::whereHas('question', function ($query) use ($user) {
            $query->whereHas('exam', function ($q) use ($user) {
                $q->where('teacher_id', $user->id);
            });
        })->distinct('user_id')->count();
        
        // Moyenne des scores sur ses examens
        $teacher_exam_answers = \App\Models\Answer::whereHas('question', function ($query) use ($user) {
            $query->whereHas('exam', function ($q) use ($user) {
                $q->where('teacher_id', $user->id);
            });
        })->get();
        
        $average_score = 0;
        if ($teacher_exam_answers->count() > 0) {
            $user_scores = [];
            
            foreach ($teacher_exam_answers as $answer) {
                $user_id = $answer->user_id;
                if (!isset($user_scores[$user_id])) {
                    $user_scores[$user_id] = ['correct' => 0, 'total' => 0];
                }
                
                $user_scores[$user_id]['total']++;
                $question = $answer->question;
                if ($question && $question->type === 'multiple_choice') {
                    $correctChoice = $question->choices()->where('is_correct', true)->first();
                    if ($correctChoice && $answer->answer_text === $correctChoice->label) {
                        $user_scores[$user_id]['correct']++;
                    }
                }
            }
            
            $total_score = 0;
            foreach ($user_scores as $scores) {
                if ($scores['total'] > 0) {
                    $total_score += ($scores['correct'] / $scores['total']) * 100;
                }
            }
            
            if (count($user_scores) > 0) {
                $average_score = round($total_score / count($user_scores), 1);
            }
        }

        // Récupérer les examens récents
        $recent_exams = \App\Models\Exam::where('teacher_id', $user->id)
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $stats = [
            'total_exams' => $total_exams,
            'total_questions' => $total_questions,
            'students_evaluated' => $students_evaluated,
            'average_score' => $average_score
        ];

        return view('dashboard.teacher', compact('user', 'stats', 'recent_exams'));
    }

    /**
     * Dashboard administrateur
     */
    public function admin()
    {
        $user = Auth::user();
        
        // Calculer les statistiques globales pour l'admin
        $total_users = \App\Models\User::count();
        $students_count = \App\Models\User::role('student')->count();
        $teachers_count = \App\Models\User::role('teacher')->count();
        $total_exams = \App\Models\Exam::count();

        // Récupérer les utilisateurs récents
        $recent_users = \App\Models\User::with('roles')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $stats = [
            'total_users' => $total_users,
            'students_count' => $students_count,
            'teachers_count' => $teachers_count,
            'total_exams' => $total_exams
        ];

        return view('dashboard.admin', compact('user', 'stats', 'recent_users'));
    }
}