import { router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Button } from '@/Components/Button';
import { formatDate, getRoleColor, getRoleLabel } from '@/utils/formatters';
import { DataTableConfig, PaginationType } from '@/types/datatable';
import Section from '@/Components/Section';
import StatCard from '@/Components/StatCard';
import { UserGroupIcon } from '@heroicons/react/24/outline';
import { DataTable } from '@/Components/DataTable';
import { route } from 'ziggy-js';
import { useState } from 'react';
import CreateUser from './Create';
import { User } from '@/types';

interface Props {
    users: PaginationType<User>;
    roles: string[];
}

export default function UserIndex({ users, roles }: Props) {

    const [isShowCreateModal, setIsShowCreateModal] = useState(false);


    const handleCreateUser = () => {
        setIsShowCreateModal(true);
    };

    const handleViewUser = (userId: number, role: string) => {
        if (role === 'student') {
            router.visit(route('admin.users.show.student', { user: userId }));
        } else if (role === 'teacher') {
            router.visit(route('admin.users.show.teacher', { user: userId }));
        }
    };


    const dataTableConfig: DataTableConfig<User> = {
        columns: [
            {
                key: 'name',
                label: 'Utilisateur',
                render: (user) => (
                    <div>
                        <div className="text-sm font-medium text-gray-900">{user.name}</div>
                        <div className="text-sm text-gray-500">{user.email}</div>
                    </div>
                )
            },
            {
                key: 'role',
                label: 'Rôle',
                render: (user) => (
                    (user?.roles?.length ?? 0) > 0 ? (
                        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getRoleColor(user.roles?.[0]?.name ?? '')}`}>
                            {getRoleLabel(user.roles?.[0]?.name ?? '')}
                        </span>
                    ) : null
                )
            },
            {
                key: 'created_at',
                label: 'Date de création',
                render: (user) => (
                    <span className="text-sm text-gray-500">{formatDate(user.created_at)}</span>
                )
            },
            {
                key: 'actions',
                label: 'Actions',
                render: (user) => (
                    <Button
                        onClick={() => handleViewUser(user.id, user.roles?.length && user.roles[0] ? user.roles[0].name : '')}
                        color="secondary"
                        size="sm"
                        variant='outline'
                    >
                        Voir
                    </Button>
                )
            }
        ],
        searchPlaceholder: 'Rechercher par nom ou email...',
        filters: [
            {
                key: 'role',
                type: 'select',
                label: 'Filtrer par rôle',
                options: [{ label: 'Tous les rôles', value: '' }].concat(roles.map(role => ({ label: getRoleLabel(role), value: role })))
            }
        ],
        emptyState: {
            title: 'Aucun utilisateur trouvé',
            subtitle: 'Essayez de modifier vos critères de recherche',
            icon: 'UserIcon'
        },
        emptySearchState: {
            title: 'Aucun utilisateur trouvé',
            subtitle: 'Aucun utilisateur ne correspond à vos critères de recherche ou de filtre.',
            resetLabel: 'Réinitialiser les filtres'
        },
        perPageOptions: [10, 25, 50]
    };

    return (
        <AuthenticatedLayout title="Gestion des utilisateurs">

            <CreateUser
                roles={roles}
                isOpen={isShowCreateModal}
                onClose={() => setIsShowCreateModal(false)}
            />

            <Section title="Gestion des utilisateurs" subtitle="Gérez les comptes utilisateurs et leurs rôles."
                actions={
                    <Button onClick={handleCreateUser} color="secondary" variant='outline' size='sm'>
                        Créer un utilisateur
                    </Button>
                }
            >
                <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <StatCard
                        title="Total utilisateurs"
                        value={users.total}
                        icon={UserGroupIcon}
                        color="blue"
                    />
                    <StatCard
                        title="Étudiants"
                        value={users.data.filter(user => user.roles?.some(role => role.name === 'student')).length}
                        icon={UserGroupIcon}
                        color="green"
                    />
                    <StatCard
                        title="Enseignants"
                        value={users.data.filter(user => user.roles?.some(role => role.name === 'teacher')).length}
                        icon={UserGroupIcon}
                        color="purple"
                    />

                    <StatCard
                        title="Administrateurs"
                        value={users.data.filter(user => user.roles?.some(role => role.name === 'admin')).length}
                        icon={UserGroupIcon}
                        color="red"
                    />
                </div>

            </Section>
            <Section title="Liste des utilisateurs">
                <DataTable
                    data={users}
                    config={dataTableConfig}
                />
            </Section>
        </AuthenticatedLayout>
    );
}