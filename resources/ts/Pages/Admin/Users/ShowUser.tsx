import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Button } from '@/Components/Button';
import { formatDate, getRoleLabel } from '@/utils/formatters';
import Section from '@/Components/Section';
import TextEntry from '@/Components/TextEntry';
import { User } from '@/types';
import { useState } from 'react';
import EditUser from './Edit';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/react';
import ConfirmationModal from '@/Components/ConfirmationModal';
import { ExclamationTriangleIcon } from '@heroicons/react/16/solid';


interface Props {
    user: User;
    children?: React.ReactNode;
}

export default function ShowUser({ user, children }: Props) {
    const [isShowUpdateModal, setIsShowUpdateModal] = useState(false);

    const [isShowDeleteModal, setIsShowDeleteModal] = useState(false);
    const [deleteInProgress, setDeleteInProgress] = useState(false);

    const handleEdit = () => {
        setIsShowUpdateModal(true);
    };

    const handleBack = () => {
        router.visit(route('admin.users.index'));
    };

    const handleDelete = () => {
        setIsShowDeleteModal(true);
    };

    const onConfirmDeleteUser = () => {
        if (user) {
            setDeleteInProgress(true);
            router.delete(route('admin.users.destroy', { user: user.id }), {
                preserveScroll: true,
                onSuccess: () => {
                    setIsShowDeleteModal(false);
                    setDeleteInProgress(false);
                },
                onError: () => {
                    setIsShowDeleteModal(false);
                    setDeleteInProgress(false);
                }
            });
        }
    };


    const userRole = (user.roles?.length ?? 0) > 0 ? user.roles![0].name : null;

    return (
        <AuthenticatedLayout title={`Utilisateur : ${user.name}`}>
            <ConfirmationModal
                isOpen={isShowDeleteModal}
                isCloseableInside={true}
                type='danger'
                title="Confirmer la suppression"
                message={`Êtes-vous sûr de vouloir supprimer l'utilisateur "${user?.name}" ?`}
                icon={ExclamationTriangleIcon}
                confirmText="Supprimer"
                cancelText="Annuler"
                onConfirm={() => onConfirmDeleteUser()}
                onClose={() => setIsShowDeleteModal(false)}
                loading={deleteInProgress}
            >
                <p className='text-sm text-gray-500 mb-6'> Cette action est irréversible.</p>
            </ConfirmationModal>
            {user && (
                <EditUser
                    route={route('admin.users.update', user.id)}
                    isOpen={isShowUpdateModal}
                    onClose={() => {
                        setIsShowUpdateModal(false);
                    }}
                    user={user}
                    userRole={userRole || null}
                />
            )}
            <Section title="Profil utilisateur" subtitle="Informations personnelles de l'utilisateur"
                actions={
                    <div className="flex space-x-3">
                        <Button
                            onClick={handleBack}
                            variant='outline'
                            size='sm'
                            color="secondary">
                            Retour
                        </Button>
                        <Button
                            onClick={handleEdit}
                            size='sm'
                            color="primary">
                            Modifier
                        </Button>
                        <Button
                            onClick={handleDelete}
                            size='sm'
                            color="danger">
                            Supprimer
                        </Button>
                    </div>
                }
            >

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <TextEntry
                        label="Nom complet"
                        value={user.name}
                    />

                    <TextEntry
                        label="Adresse email"
                        value={user.email}
                    />

                    <TextEntry
                        label="Adresse email"
                        value={user.email}
                    />

                    <TextEntry
                        label="Rôle"
                        value={userRole ? getRoleLabel(userRole) : '-'}
                    />

                    <TextEntry
                        label="Membre depuis"
                        value={formatDate(user.created_at)}
                    />

                    <TextEntry
                        label="Dernière modification"
                        value={formatDate(user.updated_at)}
                    />
                </div>

            </Section>
            {children}
        </AuthenticatedLayout >
    );
}