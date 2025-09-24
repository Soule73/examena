import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Section from '@/Components/Section';
import AlertEntry from '@/Components/AlertEntry';
import Badge from '@/Components/Badge';
import { Exam, ExamAssignment, Answer, User } from '@/types';
import useExamResults from '@/hooks/exam/useExamResults';
import useExamScoring from '@/hooks/exam/useExamScoring';
import ExamInfoSection from '@/Components/exam/ExamInfoSection';
import QuestionRenderer from '@/Components/exam/QuestionRenderer';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/react';
import { Button } from '@/Components';

interface Props {
    exam: Exam;
    student: User;
    assignment: ExamAssignment;
    userAnswers: Record<number, Answer>;
    creator: User;
}

const ExamStudentResults: React.FC<Props> = ({ exam, student, assignment, userAnswers, creator }) => {
    const { isPendingReview, assignmentStatus, examIsActive } = useExamResults({ exam, assignment, userAnswers });
    const { totalPoints, finalPercentage, getQuestionResult } = useExamScoring({ exam, assignment, userAnswers });

    return (
        <AuthenticatedLayout title={`Résultats - ${student.name} - ${exam.title}`}>
            <Section
                title={`Résultats de ${student.name}`}
                subtitle={
                    <div className='flex items-center space-x-4'>
                        <span className={`px-3 py-1 rounded-full text-sm font-medium ${assignmentStatus.color}`}>
                            {assignmentStatus.label}
                        </span>
                        <div>
                            {examIsActive ? (
                                <Badge label="Examen actif" type="success" />
                            ) : (
                                <Badge label="Examen désactivé" type="gray" />
                            )}
                        </div>
                    </div>
                }
                actions={
                    <div className="flex space-x-2">
                        {isPendingReview && (
                            <Button
                                color="primary"
                                size="sm"
                                onClick={() => router.visit(route('teacher.exams.review', { exam: exam.id, student: student.id }))}
                            >
                                Corriger l'examen
                            </Button>
                        )}
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
                    creator={creator}
                    totalPoints={totalPoints}
                    percentage={finalPercentage}
                    isPendingReview={isPendingReview}
                />

                {isPendingReview && (
                    <AlertEntry title="Correction en attente" type="warning">
                        <p className="text-sm">
                            Cet examen contient des questions nécessitant une correction manuelle.
                            Cliquez sur "Corriger l'examen" pour attribuer les notes manuellement.
                        </p>
                    </AlertEntry>
                )}
            </Section>

            <Section title="Détail des réponses">
                <QuestionRenderer
                    questions={exam.questions || []}
                    getQuestionResult={getQuestionResult}
                    isTeacherView={true}
                />
            </Section>
        </AuthenticatedLayout>
    );
};

export default ExamStudentResults;