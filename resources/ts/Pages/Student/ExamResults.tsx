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
    assignment: ExamAssignment;
    userAnswers: Record<number, Answer>;
    creator: User;

}

const ExamResults: React.FC<Props> = ({ exam, assignment, userAnswers, creator }) => {
    const { isPendingReview, assignmentStatus, examIsActive } = useExamResults({ exam, assignment, userAnswers });
    const { totalPoints, finalPercentage, getQuestionResult } = useExamScoring({ exam, assignment, userAnswers });

    return (
        <AuthenticatedLayout title={`Résultats - ${exam.title}`}>
            <Section
                title="Résultats de l'examen"
                subtitle={
                    <div className='flex items-center space-x-4'>
                        <span className={`px-3 py-1 rounded-full text-sm font-medium ${assignmentStatus.color}`}>
                            {assignmentStatus.label}
                        </span>
                        <div>
                            {examIsActive ? (
                                <Badge label="Examen actif" type="success" />
                            ) : (
                                <Badge label="Examen désactivé" type="error" />
                            )}
                        </div>
                    </div>
                }
                actions={
                    <Button
                        color="secondary"
                        variant="outline"
                        size="sm"
                        className='w-max'
                        onClick={() => router.visit(route('student.exams.index'))}
                    >
                        Retour aux examens
                    </Button>
                }
            >
                <ExamInfoSection
                    exam={exam}
                    assignment={assignment}
                    creator={creator}
                    totalPoints={totalPoints}
                    percentage={finalPercentage}
                    isPendingReview={isPendingReview}
                    isStudentView={true}
                />

                {isPendingReview && (
                    <AlertEntry title="En attente de correction" type="warning">
                        <p className="text-sm">
                            Votre examen contient des questions nécessitant une correction manuelle.
                            Les résultats seront disponibles après correction par l'enseignant.
                        </p>
                    </AlertEntry>
                )}
            </Section>

            <Section title="Détail des réponses">
                <QuestionRenderer
                    questions={exam.questions || []}
                    getQuestionResult={getQuestionResult}
                    isTeacherView={false}
                />
            </Section>
        </AuthenticatedLayout>
    );
};



export default ExamResults;