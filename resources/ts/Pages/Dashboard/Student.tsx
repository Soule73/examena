import { router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { route } from 'ziggy-js';
import { ChartBarIcon, CheckIcon, ClockIcon, DocumentTextIcon } from '@heroicons/react/24/outline';
import StatCard from '@/Components/StatCard';
import Section from '@/Components/Section';
import { Button } from '@/Components';
import { ExamAssignment, User } from '@/types';
import ExamList from '@/Components/ExamList';

interface Stats {
    totalExams: number;
    completedExams: number;
    pendingExams: number;
    averageScore: number;
}

interface Props {
    user: User;
    stats: Stats;
    recentAssignments: ExamAssignment[];
}

export default function StudentDashboard({ user, stats, recentAssignments }: Props) {

    return (
        <AuthenticatedLayout title='Tableau de bord étudiant'>

            <Section title={`Bonjour, ${user.name} !`}
                actions={
                    <Button
                        size='sm'
                        variant='outline'
                        onClick={() => router.visit(route('student.exams.index'))}>
                        Voir mes examens
                    </Button>
                }
            >
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <StatCard
                        title="Total Examens"
                        value={`${stats.totalExams}`}
                        icon={DocumentTextIcon}
                        color="blue"
                    />

                    <StatCard
                        title="Examens en attente"
                        value={`${stats.pendingExams}`}
                        icon={ClockIcon}
                        color="yellow"
                    />

                    <StatCard
                        title="Examens terminés"
                        value={`${stats.completedExams}`}
                        icon={CheckIcon}
                        color="green"
                    />

                    <StatCard
                        title="Note moyen"
                        value={`${stats.averageScore} / 20`}
                        icon={ChartBarIcon}
                        color="red"
                    />
                </div>

                <div className="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 className="text-lg font-semibold text-gray-900 mb-4">Progression</h3>
                    <LineProgressBar
                        label="Examens terminés"
                        current={stats.completedExams}
                        total={stats.totalExams}
                    />
                </div>

                {stats.totalExams === 0 && (
                    <div className="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                        <DocumentTextIcon className="mx-auto h-12 w-12 text-gray-400 mb-4" />
                        <h3 className="text-lg font-medium text-gray-900 mb-2">Aucun examen disponible</h3>
                        <p className="text-gray-600">
                            Vous n'avez actuellement aucun examen.
                        </p>
                    </div>
                )}

            </Section>
            <Section title="Derniers examens assignés"
                actions={
                    <Button
                        size='sm'
                        variant='outline'
                        onClick={() => router.visit(route('student.exams.index'))}>
                        Voir tous les examens
                    </Button>
                }
            >
                {recentAssignments.length === 0 ? (
                    <p className="text-gray-500">Aucun examen assigné récemment.</p>
                ) : (
                    <ExamList assignments={recentAssignments} />
                )}
            </Section>
        </AuthenticatedLayout>
    );
}

interface LineProgressBarProps {
    label: string;
    current: number;
    total: number;
    color?: 'blue' | 'green' | 'purple' | 'red' | 'yellow';
}

const LineProgressBar: React.FC<LineProgressBarProps> = ({ label, current, total, color = 'blue' }) => {
    const percentage = total > 0 ? (current / total) * 100 : 0;

    const colorClasses = {
        blue: 'bg-blue-600',
        green: 'bg-green-600',
        purple: 'bg-purple-600',
        red: 'bg-red-600',
        yellow: 'bg-yellow-600',
    };

    return (
        <div>
            <div className="flex justify-between text-sm text-gray-600 mb-1">
                <span>{label}</span>
                <span>{current}/{total}</span>
            </div>
            <div className="w-full bg-gray-200 rounded-full h-2">
                <div
                    className={`${colorClasses[color]} h-2 rounded-full transition-all duration-300`}
                    style={{ width: `${percentage}%` }}
                ></div>
            </div>
        </div>
    );
}
