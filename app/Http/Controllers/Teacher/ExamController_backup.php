<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Choice;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    use AuthorizesRequests;
    /**
     * Afficher la liste des examens du professeur
     */
    public function index()
    {
        $exams = Exam::where('teacher_id', Auth::id())
                    ->with(['questions'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('teacher.exams.index', compact('exams'));
    }

    /**
     * Afficher le formulaire de création d'examen
     */
    public function create()
    {
        return view('teacher.exams.create');
    }

    /**
     * Enregistrer un nouvel examen
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1|max:480', // 1 à 480 minutes (8h)
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'is_active' => 'boolean',
            'questions' => 'required|array|min:1',
            'questions.*.content' => 'required|string',
            'questions.*.type' => 'required|in:text,multiple_choice,true_false',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.choices' => 'required_if:questions.*.type,multiple_choice|array|min:2',
            'questions.*.choices.*.content' => 'required_if:questions.*.type,multiple_choice|string',
            'questions.*.correct_choice' => 'required_if:questions.*.type,multiple_choice|integer|min:0',
            'questions.*.correct_answer' => 'required_if:questions.*.type,true_false|in:true,false',
            'questions.*.suggested_answer' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Créer l'examen
            $exam = Exam::create([
                'title' => $request->title,
                'description' => $request->description,
                'duration' => $request->duration,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'is_active' => $request->boolean('is_active', false),
                'teacher_id' => Auth::id(),
            ]);

            // Créer les questions et choix
            foreach ($request->questions as $questionData) {
                $question = $exam->questions()->create([
                    'content' => $questionData['content'],
                    'type' => $questionData['type'],
                    'points' => $questionData['points'],
                ]);

                // Gérer selon le type de question
                if ($questionData['type'] === 'multiple_choice' && isset($questionData['choices'])) {
                    foreach ($questionData['choices'] as $index => $choiceData) {
                        $isCorrect = isset($questionData['correct_choice']) && $questionData['correct_choice'] == $index;
                        
                        $question->choices()->create([
                            'content' => $choiceData['content'],
                            'is_correct' => $isCorrect,
                        ]);
                    }
                } elseif ($questionData['type'] === 'true_false') {
                    // Pour vrai/faux, créer deux choix
                    $question->choices()->create([
                        'content' => 'Vrai',
                        'is_correct' => $questionData['correct_answer'] === 'true',
                    ]);
                    
                    $question->choices()->create([
                        'content' => 'Faux',
                        'is_correct' => $questionData['correct_answer'] === 'false',
                    ]);
                }
                // Pour les questions texte, pas de choix à créer
            }

            DB::commit();

            return redirect()
                ->route('teacher.exams.index')
                ->with('success', 'Examen créé avec succès !');

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de l\'examen : ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'un examen
     */
    public function show(Exam $exam)
    {
        $this->authorize('view', $exam);
        
        $exam->load(['questions.choices', 'assignments.classe', 'assignments.students']);

        return view('teacher.exams.show', compact('exam'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Exam $exam)
    {
        $this->authorize('update', $exam);
        
        $exam->load(['questions.choices']);

        return view('teacher.exams.edit', compact('exam'));
    }

    /**
     * Mettre à jour un examen
     */
    public function update(Request $request, Exam $exam)
    {
        $this->authorize('update', $exam);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1|max:480',
            'questions' => 'required|array|min:1',
            'questions.*.content' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,text',
            'questions.*.choices' => 'required_if:questions.*.type,multiple_choice|array|min:2',
            'questions.*.choices.*.label' => 'required_if:questions.*.type,multiple_choice|string',
            'questions.*.choices.*.is_correct' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Mettre à jour l'examen
            $exam->update([
                'title' => $request->title,
                'description' => $request->description,
                'duration' => $request->duration,
            ]);

            // Supprimer les anciennes questions et leurs choix
            $exam->questions()->delete();

            // Recréer les questions et choix
            foreach ($request->questions as $questionData) {
                $question = $exam->questions()->create([
                    'content' => $questionData['content'],
                    'type' => $questionData['type'],
                ]);

                if ($questionData['type'] === 'multiple_choice' && isset($questionData['choices'])) {
                    foreach ($questionData['choices'] as $choiceData) {
                        $question->choices()->create([
                            'label' => $choiceData['label'],
                            'is_correct' => $choiceData['is_correct'] ?? false,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('teacher.exams.show', $exam)
                ->with('success', 'Examen mis à jour avec succès !');

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un examen
     */
    public function destroy(Exam $exam)
    {
        $this->authorize('delete', $exam);

        try {
            $exam->delete();

            return redirect()
                ->route('teacher.exams.index')
                ->with('success', 'Examen supprimé avec succès !');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Dupliquer un examen
     */
    public function duplicate(Exam $exam)
    {
        $this->authorize('view', $exam);

        DB::beginTransaction();
        try {
            // Créer une copie de l'examen
            $newExam = Exam::create([
                'title' => $exam->title . ' (Copie)',
                'description' => $exam->description,
                'duration' => $exam->duration,
                'teacher_id' => Auth::id(),
            ]);

            // Copier les questions et choix
            foreach ($exam->questions as $question) {
                $newQuestion = $newExam->questions()->create([
                    'content' => $question->content,
                    'type' => $question->type,
                ]);

                foreach ($question->choices as $choice) {
                    $newQuestion->choices()->create([
                        'label' => $choice->label,
                        'is_correct' => $choice->is_correct,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('teacher.exams.show', $newExam)
                ->with('success', 'Examen dupliqué avec succès !');

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()
                ->with('error', 'Erreur lors de la duplication : ' . $e->getMessage());
        }
    }
}
