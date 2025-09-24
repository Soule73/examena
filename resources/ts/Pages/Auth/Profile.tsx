import Section from "@/Components/Section";
import TextEntry from "@/Components/TextEntry";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { User } from "@/types";
import { formatDate, getRoleLabel } from "@/utils";
import EditUser from "../Admin/Users/Edit";
import { useMemo, useState } from "react";
import { Button } from "@/Components";
import { route } from "ziggy-js";
import { UserAvatar } from "@/Components/Navigation";

interface Props {
    user: User;
}

export default function Profile({ user }: Props) {
    const [isShowUpdateModal, setIsShowUpdateModal] = useState(false);

    const handleEdit = () => {
        setIsShowUpdateModal(true);
    };

    const userRole = useMemo(() => (user.roles?.length ?? 0) > 0 ? user.roles![0].name : null, [user.roles]);

    return (
        <AuthenticatedLayout title="Profile">
            {user && (
                <EditUser
                    title="Modifier le profil"
                    description="Modifiez les informations de votre profil"
                    route={route('profile.update', { user: user.id })}
                    isOpen={isShowUpdateModal}
                    onClose={() => {
                        setIsShowUpdateModal(false);
                    }}
                    user={user}
                    userRole={userRole || null}
                />
            )}
            <Section title="Votre profil" subtitle="Gérez les informations de votre profil"

                actions={
                    <Button
                        onClick={handleEdit}
                        size='sm'
                        className="w-max"
                        color="primary">
                        Modifier
                    </Button>
                }
            >
                <div className="flex items-center space-x-4">
                    <UserAvatar avatar={user.avatar} name={user.name} size="large" />
                    <TextEntry label={user.name} value={user.email} />
                </div>
                <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <TextEntry label="Rôle" value={getRoleLabel(userRole ?? '')} />
                    <TextEntry label="Compte actif" value={user.active ? 'Oui' : 'Non'} />
                    <TextEntry label="Email vérifié" value={user.email_verified_at ? 'Oui' : 'Non'} />
                    <TextEntry label="Date de création" value={formatDate(user.created_at, "long")} />
                    <TextEntry label="Dernière mise à jour" value={formatDate(user.updated_at, "long")} />
                </div>
            </Section>
        </AuthenticatedLayout>
    );
}
