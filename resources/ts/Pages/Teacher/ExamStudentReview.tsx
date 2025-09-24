import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Section from '@/Components/Section';
import AlertEntry from '@/Components/AlertEntry';
import Badge from '@/Components/Badge';
import { Exam, ExamAssignment, Answer, User, Question } from '@/types';
import useExamResults from '@/hooks/exam/useExamResults';
import useExamScoring from '@/hooks/exam/useExamScoring';
import useScoreManagement from '@/hooks/exam/useScoreManagement';
import ExamInfoSection from '@/Components/exam/ExamInfoSection';
import QuestionRenderer from '@/Components/exam/QuestionRenderer';
import { requiresManualGrading, getCorrectionStatus } from '@/utils/examUtils';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/react';
import { Button } from '@/Components';

interface Props {
    exam: Exam;
    student: User;
    assignment: ExamAssignment;
    userAnswers: Record<number, Answer>;
}

const ExamStudentReview: React.FC<Props> = ({ exam, student, assignment, userAnswers }) => {
    const { assignmentStatus } = useExamResults({ exam, assignment, userAnswers });
    const { totalPoints, calculateQuestionScore, getQuestionResult } = useExamScoring({ exam, assignment, userAnswers });

    const {
        scores,
        calculatedTotalScore,
        percentage,
        handleScoreChange,
        getScoresForSave
    } = useScoreManagement({
        questions: exam.questions || [],
        userAnswers,
        calculateQuestionScore,
        totalPoints
    });

    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleSubmit = async () => {
        setIsSubmitting(true);

        try {
            router.post(route('teacher.exams.review.save', { exam: exam.id, student: student.id }), {
                scores: getScoresForSave()
            }, {
                onSuccess: () => {
                    setIsSubmitting(false);
                },
                onError: (_) => {
                    setIsSubmitting(false);
                },
                onFinish: () => {
                    setIsSubmitting(false);
                }
            });
        } catch (error) {
            console.error('Erreur lors de la sauvegarde:', error);
            setIsSubmitting(false);
        }
    };

    const renderScoreInput = (question: Question) => {
        const questionScore = scores[question.id] || 0;
        const maxScore = question.points || 0;
        const isAutoGraded = !requiresManualGrading(question);

        return (
            <div className="mt-4 p-4 bg-gray-50 rounded-lg">
                <div className="flex items-center justify-between">
                    <div>
                        <label className="text-sm font-medium text-gray-700">
                            Note pour cette question (max: {maxScore} points)
                        </label>
                        {isAutoGraded && (
                            <p className="text-xs text-blue-600 mt-1">
                                Score calculé automatiquement - modifiable si nécessaire
                            </p>
                        )}
                        {!isAutoGraded && (
                            <p className="text-xs text-orange-600 mt-1">
                                Correction manuelle requise
                            </p>
                        )}
                    </div>
                    <div className="flex items-center space-x-2">
                        <input
                            type="number"
                            min="0"
                            max={maxScore}
                            step="0.5"
                            value={questionScore}
                            onChange={(e) => handleScoreChange(question.id, parseFloat(e.target.value) || 0, maxScore)}
                            className="w-20 px-2 py-1 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                        />
                        <span className="text-sm text-gray-500">/ {maxScore}</span>
                    </div>
                </div>
            </div>
        );
    }; return (
        <AuthenticatedLayout title={`Correction - ${student.name} - ${exam.title}`}>
            <Section
                title={`Correction de l'examen de ${student.name}`}
                subtitle={
                    <div className='flex items-center space-x-4'>
                        <span className={`px-3 py-1 rounded-full text-sm font-medium ${assignmentStatus.color}`}>
                            {assignmentStatus.label}
                        </span>
                        <Badge label="Mode correction" type="warning" />
                    </div>
                }
                actions={
                    <div className="flex space-x-2">
                        <Button
                            color="primary"
                            size="sm"
                            onClick={handleSubmit}
                            disabled={isSubmitting}
                        >
                            {isSubmitting ? 'Sauvegarde...' : 'Sauvegarder les notes'}
                        </Button>
                        <Button
                            color="secondary"
                            variant="outline"
                            size="sm"
                            onClick={() => router.visit(route('teacher.exams.assignments', exam.id))}
                        >
                            Retour aux assignations
                        </Button>
                    </div>
                }
            >
                <ExamInfoSection
                    exam={exam}
                    student={student}
                    assignment={assignment}
                    score={calculatedTotalScore}
                    totalPoints={totalPoints}
                    percentage={percentage}
                    isReviewMode={true}
                />

                <AlertEntry title="Mode correction" type="info">
                    <p className="text-sm">
                        Vous pouvez modifier les notes de chaque question. Les questions à choix multiples, uniques et booléennes sont
                        <strong> automatiquement corrigées</strong> selon la logique de correction, mais vous pouvez ajuster les notes
                        si nécessaire. Les questions de type texte nécessitent une <strong>correction manuelle</strong>.
                    </p>
                </AlertEntry>
            </Section>

            <Section title="Questions et correction">
                <QuestionRenderer
                    questions={exam.questions || []}
                    getQuestionResult={getQuestionResult}
                    scores={scores}
                    isTeacherView={true}
                    renderScoreInput={renderScoreInput}
                />

                {/* Résumé de la correction */}
                <div className="mt-8 p-6 bg-blue-50 rounded-lg">
                    <h3 className="text-lg font-medium text-blue-900 mb-4">Résumé de la correction</h3>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <span className="text-sm text-blue-600">Score total:</span>
                            <div className="text-xl font-bold text-blue-900">
                                {calculatedTotalScore} / {totalPoints}
                            </div>
                        </div>
                        <div>
                            <span className="text-sm text-blue-600">Pourcentage:</span>
                            <div className="text-xl font-bold text-blue-900">
                                {percentage}%
                            </div>
                        </div>
                        <div>
                            <span className="text-sm text-blue-600">Statut:</span>
                            <div className="text-xl font-bold text-blue-900">
                                {getCorrectionStatus(calculatedTotalScore)}
                            </div>
                        </div>
                    </div>
                </div>
            </Section>
        </AuthenticatedLayout>
    );
};

export default ExamStudentReview;