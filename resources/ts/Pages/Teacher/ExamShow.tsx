import React from 'react';
import { Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { formatDuration } from '@/utils/formatters';
import { Button } from '@/Components';
import { Exam } from '@/types';
import StatCard from '@/Components/StatCard';
import { ClockIcon, QuestionMarkCircleIcon, StarIcon } from '@heroicons/react/24/outline';
import Section from '@/Components/Section';
import TextEntry from '@/Components/TextEntry';
import { route } from 'ziggy-js';
import QuestionReadOnlySection from '@/Components/exam/QuestionReadOnlySection';
import { QuestionResultReadOnlyText, QuestionTeacherReadOnlyChoices } from '@/Components/exam/QuestionResultReadOnly';


interface Props {
    exam: Exam;
}

const TeacherExamShow: React.FC<Props> = ({ exam }) => {

    const totalPoints = (exam.questions ?? []).reduce((sum, q) => sum + q.points, 0);

    return (
        <AuthenticatedLayout title={exam.title}>
            <div className="max-w-6xl mx-auto space-y-6">
                <Section title={"Détails et gestion de l'examen"}
                    actions={
                        <div className="flex flex-col md:flex-row space-y-2 md:space-x-3 md:space-y-0">
                            <Button
                                onClick={() => router.visit(route('teacher.exams.edit', exam.id))}
                                color="secondary"
                                variant='outline'
                                size="sm" >
                                Modifier
                            </Button>
                            <Button
                                onClick={() => router.visit(route('teacher.exams.assign', exam.id))}
                                variant='outline'
                                color="secondary"
                                size="sm" >
                                Assigner
                            </Button>
                            <Button
                                onClick={() => router.visit(route('teacher.exams.assignments', exam.id))}
                                color="secondary"
                                variant='outline'
                                size="sm" >
                                Voir les assignations
                            </Button>
                        </div>
                    }

                >
                    <div className="flex items-start justify-between">
                        <div className="flex-1">
                            <div className="flex items-center space-x-3 mb-2">

                                <TextEntry label={exam.title} value={exam.description ?? ''} />
                                <span className={`px-2 py-1 rounded-full text-xs font-medium ${exam.is_active
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-gray-100 text-gray-800'
                                    }`}>
                                    {exam.is_active ? 'Actif' : 'Inactif'}
                                </span>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <StatCard title="Questions" value={(exam.questions ?? []).length}
                                    icon={
                                        QuestionMarkCircleIcon
                                    } color='blue' />
                                <StatCard title="Points totaux" value={totalPoints} color='green'
                                    icon={
                                        StarIcon
                                    }
                                />
                                {/* <div className="bg-white p-4 rounded-lg shadow">
                                    <div className="text-2xl font-bold text-purple-600">{exam?.answers_count || 0}</div>
                                    <div className="text-sm text-gray-600">Réponses reçues</div>
                                </div> */}
                                <StatCard title="Durée" value={formatDuration(exam.duration)} color='yellow'
                                    icon={
                                        ClockIcon
                                    } />
                            </div>
                        </div>
                    </div>
                </Section>

                {/* Questions */}
                <Section title="Questions de l'examen" collapsible>
                    {(exam.questions ?? []).length === 0 ? (
                        <div className="text-center py-8 text-gray-500">
                            <p>Aucune question ajoutée à cet examen.</p>
                            <Link href={route('teacher.exams.edit', exam.id)} className="mt-2 inline-block">
                                <Button>Ajouter des questions</Button>
                            </Link>
                        </div>
                    ) : (
                        <div className="divide-y divide-gray-200">
                            {(exam.questions ?? []).map((question, index) => (
                                <QuestionReadOnlySection key={question.id} question={question} questionIndex={index}>

                                    {question.type !== 'text' && (question.choices ?? []).length > 0 && (
                                        <div className="ml-4">
                                            <h5 className="text-sm font-medium text-gray-700 mb-2">
                                                Choix de réponse :
                                            </h5>
                                            <div className="space-y-2">
                                                <QuestionTeacherReadOnlyChoices
                                                    type={question.type}
                                                    choices={question.choices ?? []}
                                                />
                                            </div>
                                        </div>
                                    )}

                                    {question.type === 'text' && (
                                        <QuestionResultReadOnlyText
                                            userText={"Question à réponse libre - correction manuelle requise"}
                                            label=""
                                        />
                                    )}

                                </QuestionReadOnlySection>
                            ))}
                        </div>
                    )}
                </Section>
            </div >
        </AuthenticatedLayout >
    );
};

export default TeacherExamShow;