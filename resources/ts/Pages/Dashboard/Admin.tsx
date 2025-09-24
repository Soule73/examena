import { router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Button } from '@/Components/Button';
import { formatDate } from '@/utils/formatters';
import { User } from '@/types';
import StatCard from '@/Components/StatCard';
import Section from '@/Components/Section';
import { BookOpenIcon, DocumentTextIcon, UserGroupIcon } from '@heroicons/react/24/outline';


interface Stats {
    total_users: number;
    students_count: number;
    teachers_count: number;
    total_exams: number;
}

interface Props {
    user: User;
    stats: Stats;
    recent_users: User[];
}

export default function AdminDashboard({ user, stats, recent_users }: Props) {
    const handleManageUsers = () => {
        router.visit('/admin/users');
    };

    const handleManageExams = () => {
        router.visit('/admin/exams');
    };

    const handleCreateUser = () => {
        router.visit('/admin/users/create');
    };

    const handleViewUser = (userId: number) => {
        router.visit(`/admin/users/${userId}`);
    };

    const getRoleColor = (roleName: string) => {
        switch (roleName) {
            case 'admin':
                return 'bg-red-100 text-red-800';
            case 'teacher':
                return 'bg-blue-100 text-blue-800';
            case 'student':
                return 'bg-green-100 text-green-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getRoleLabel = (roleName: string) => {
        switch (roleName) {
            case 'admin':
                return 'Administrateur';
            case 'teacher':
                return 'Enseignant';
            case 'student':
                return 'Étudiant';
            default:
                return roleName;
        }
    };

    return (
        <AuthenticatedLayout title="Tableau de bord administrateur">
            <Section title={`Bonjour, ${user.name} !`}
                subtitle="Gérez la plateforme et supervisez les activités."
                actions={
                    <div className="flex flex-col md:flex-row space-y-2 md:space-x-3 md:space-y-0">
                        <Button
                            onClick={handleCreateUser}
                            color="secondary"
                            variant='outline'
                            size='sm'
                        >
                            Créer un utilisateur
                        </Button>
                        <Button
                            onClick={handleManageUsers}
                            variant='outline'
                            color="secondary"
                            size='sm'
                        >
                            Gérer les utilisateurs
                        </Button>
                        <Button
                            onClick={handleManageExams}
                            variant='outline'
                            color="secondary"
                            size='sm'
                        >
                            Gérer les examens
                        </Button>
                    </div>}
            >
                {/* Statistiques principales */}
                <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <StatCard
                        title="Total utilisateurs"
                        value={stats.total_users}
                        icon={
                            UserGroupIcon
                        }
                        color="blue"
                    />
                    <StatCard
                        title="Étudiants"
                        value={stats.students_count}
                        color='green'
                        icon={
                            BookOpenIcon
                        }
                    />

                    <StatCard
                        title="Enseignants"
                        value={stats.teachers_count}
                        color='purple'
                        icon={
                            UserGroupIcon
                        }
                    />

                    <StatCard
                        title="Taotal examens"
                        value={stats.total_exams}
                        color='yellow'
                        icon={
                            DocumentTextIcon
                        }
                    />
                </div>


            </Section>

            <Section title="Utilisateurs récents"
                actions={
                    <Button
                        onClick={handleManageUsers}
                        color="secondary"
                        variant='outline'
                        size='sm'
                    >
                        Voir tous les utilisateurs
                    </Button>
                }
            >

                {recent_users.length === 0 ? (
                    <div className="text-center py-8">
                        <svg className="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                        <p className="text-gray-600 mb-4">Aucun utilisateur récent</p>
                        <Button onClick={handleCreateUser} color="primary">
                            Créer le premier utilisateur
                        </Button>
                    </div>
                ) : (
                    <div className="space-y-4">
                        {recent_users.map((recentUser) => (
                            <div
                                key={recentUser.id}
                                className="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 cursor-pointer transition-colors"
                                onClick={() => handleViewUser(recentUser.id)}
                            >
                                <div className="flex justify-between items-start">
                                    <div className="flex-1">
                                        <h4 className="font-medium text-gray-900 mb-1">
                                            {recentUser.name}
                                        </h4>
                                        <p className="text-sm text-gray-600 mb-2">
                                            {recentUser.email}
                                        </p>
                                        <div className="flex items-center space-x-2">
                                            {recentUser.roles && recentUser.roles.length > 0 && (
                                                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getRoleColor(recentUser.roles[0].name)}`}>
                                                    {getRoleLabel(recentUser.roles[0].name)}
                                                </span>
                                            )}
                                            <span className="text-xs text-gray-500">
                                                Créé le {formatDate(recentUser.created_at)}
                                            </span>
                                        </div>
                                    </div>
                                    <svg className="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </Section>

        </AuthenticatedLayout >
    );
}