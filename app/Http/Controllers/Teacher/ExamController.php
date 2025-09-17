<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreExamRequest;
use App\Http\Requests\Teacher\UpdateExamRequest;
use App\Models\Exam;
use App\Services\ExamService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExamController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private ExamService $examService
    ) {
        // Le middleware auth sera géré dans les routes ou via les policies
    }

    /**
     * Afficher la liste des examens du professeur
     */
    public function index(): View
    {
        $this->authorize('viewAny', Exam::class);

        $exams = $this->examService->getTeacherExams(Auth::id());

        return view('teacher.exams.index', compact('exams'));
    }

    /**
     * Afficher le formulaire de création d'un nouvel examen
     */
    public function create(): View
    {
        $this->authorize('create', Exam::class);

        return view('teacher.exams.create');
    }

    /**
     * Enregistrer un nouvel examen
     */
    public function store(StoreExamRequest $request): RedirectResponse
    {
        $this->authorize('create', Exam::class);

        try {
            $exam = $this->examService->createExam($request->validated());

            return redirect()
                ->route('teacher.exams.index')
                ->with('success', 'Examen créé avec succès !');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de l\'examen : ' . $e->getMessage());
        }
    }

    /**
     * Afficher un examen spécifique
     */
    public function show(Exam $exam): View
    {
        $this->authorize('view', $exam);

        $exam->load(['questions.choices']);
        $exam->loadCount(['questions', 'answers']);

        return view('teacher.exams.show', compact('exam'));
    }

    /**
     * Afficher le formulaire d'édition d'un examen
     */
    public function edit(Exam $exam): View
    {
        $this->authorize('update', $exam);

        $exam->load(['questions.choices']);

        return view('teacher.exams.edit', compact('exam'));
    }

    /**
     * Mettre à jour un examen existant
     */
    public function update(UpdateExamRequest $request, Exam $exam): RedirectResponse
    {
        $this->authorize('update', $exam);

        try {
            $exam = $this->examService->updateExam($exam, $request->validated());

            return redirect()
                ->route('teacher.exams.show', $exam)
                ->with('success', 'Examen mis à jour avec succès !');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de l\'examen : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un examen
     */
    public function destroy(Exam $exam): RedirectResponse
    {
        $this->authorize('delete', $exam);

        try {
            $this->examService->deleteExam($exam);

            return redirect()
                ->route('teacher.exams.index')
                ->with('success', 'Examen supprimé avec succès !');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erreur lors de la suppression de l\'examen : ' . $e->getMessage());
        }
    }

    /**
     * Dupliquer un examen
     */
    public function duplicate(Exam $exam): RedirectResponse
    {
        $this->authorize('view', $exam);

        try {
            $newExam = $this->examService->duplicateExam($exam);

            return redirect()
                ->route('teacher.exams.edit', $newExam)
                ->with('success', 'Examen dupliqué avec succès ! Vous pouvez maintenant le modifier.');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erreur lors de la duplication de l\'examen : ' . $e->getMessage());
        }
    }

    /**
     * Activer/Désactiver un examen
     */
    public function toggleActive(Exam $exam): RedirectResponse
    {
        $this->authorize('update', $exam);

        try {
            $exam->update(['is_active' => !$exam->is_active]);

            $status = $exam->is_active ? 'activé' : 'désactivé';
            
            return back()
                ->with('success', "Examen {$status} avec succès !");

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erreur lors du changement de statut de l\'examen : ' . $e->getMessage());
        }
    }

    /**
     * Afficher les statistiques d'un examen
     */
    public function stats(Exam $exam): RedirectResponse
    {
        $this->authorize('view', $exam);

        // TODO: Implémenter les statistiques et créer la vue
        return back()->with('info', 'Les statistiques seront disponibles prochainement.');
    }

    /**
     * Afficher le formulaire d'assignation d'examen aux étudiants
     */
    public function showAssignForm(Exam $exam): View
    {
        $this->authorize('update', $exam);
        
        // Charger les relations nécessaires
        $exam->load(['questions', 'assignments.student']);
        
        // Récupérer tous les étudiants
        $students = \App\Models\User::role('student')
                       ->select('id', 'name', 'email')
                       ->orderBy('name')
                       ->get();
        
        // Récupérer les IDs des étudiants déjà assignés à cet examen
        $assignedStudentIds = $exam->assignments()
                                  ->pluck('student_id')
                                  ->toArray();
        
        return view('teacher.exams.assign', compact('exam', 'students', 'assignedStudentIds'));
    }

    /**
     * Assigner l'examen aux étudiants sélectionnés
     */
    public function assignToStudents(Request $request, Exam $exam): RedirectResponse
    {
        $this->authorize('update', $exam);
        
        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:users,id',
        ]);

        $assignedCount = 0;
        $alreadyAssignedCount = 0;

        foreach ($validated['student_ids'] as $studentId) {
            // Vérifier si l'étudiant a le bon rôle
            $student = \App\Models\User::find($studentId);
            if (!$student || !$student->hasRole('student')) {
                continue;
            }

            // Créer l'assignation si elle n'existe pas déjà
            $assignment = \App\Models\ExamAssignment::firstOrCreate([
                'exam_id' => $exam->id,
                'student_id' => $studentId,
            ], [
                'assigned_at' => now(),
                'status' => 'assigned',
            ]);

            if ($assignment->wasRecentlyCreated) {
                $assignedCount++;
            } else {
                $alreadyAssignedCount++;
            }
        }

        $message = "Assignation terminée : {$assignedCount} nouveaux étudiants assignés";
        if ($alreadyAssignedCount > 0) {
            $message .= " ({$alreadyAssignedCount} déjà assignés)";
        }

        return redirect()->route('teacher.exams.show', $exam)
                        ->with('success', $message);
    }

    /**
     * Retirer l'assignation d'un étudiant
     */
    public function removeAssignment(Exam $exam, \App\Models\User $user): RedirectResponse
    {
        $this->authorize('update', $exam);
        
        // Trouver l'assignation
        $assignment = $exam->assignments()->where('student_id', $user->id)->first();
        
        if (!$assignment) {
            return redirect()->route('teacher.exams.show', $exam)
                            ->with('error', "Cet étudiant n'est pas assigné à cet examen.");
        }

        $assignment->delete();

        return redirect()->route('teacher.exams.show', $exam)
                        ->with('success', "Assignation de {$user->name} supprimée avec succès.");
    }

    /**
     * Afficher la page de gestion des assignations
     */
    public function showAssignments(Exam $exam): View
    {
        $this->authorize('view', $exam);
        
        // Charger les assignations avec les étudiants
        $assignedStudents = $exam->assignments()
                                ->with('student')
                                ->orderBy('assigned_at', 'desc')
                                ->get();
        
        // Calculer les statistiques
        $stats = [
            'assigned' => $assignedStudents->count(),
            'started' => $assignedStudents->where('status', 'started')->count(),
            'submitted' => $assignedStudents->where('status', 'submitted')->count(),
            'graded' => $assignedStudents->where('status', 'graded')->count(),
        ];
        
        return view('teacher.exams.assignments', compact('exam', 'assignedStudents', 'stats'));
    }
}