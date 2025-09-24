import { useForm } from '@inertiajs/react';
import { Button } from '@/Components/Button';
import Input from '@/Components/form/Input';
import { User } from '@/types';
import Modal from '@/Components/Modal';

interface Props {
    user: User;
    route: string;
    title?: string;
    description?: string;
    roles?: string[];
    userRole: string | null;
    isOpen: boolean;
    onClose: () => void;
}

export default function EditUser({ user, roles, userRole, isOpen, onClose, title, description, route }: Props) {
    const { data, setData, put, processing, errors } = useForm({
        id: user.id,
        name: user.name,
        email: user.email,
        password: '',
        password_confirmation: '',
        role: userRole || 'student',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route, {
            onSuccess: () => {
                onClose();
            },
            onError: (e) => {
                console.log('Erreur lors de la mise à jour de l\'utilisateur :', e);
            }
        });
    };

    const handleCancel = () => {
        onClose();
        setData({
            id: user.id,
            name: user.name,
            email: user.email,
            password: '',
            password_confirmation: '',
            role: userRole || 'student',
        });
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
        <Modal isOpen={isOpen} size='2xl' onClose={onClose} isCloseableInside={false}>
            <div className="p-6 md:min-w-lg lg:min-w-xl w-full ">
                <div className="mb-6">
                    <h1 className="text-2xl font-bold text-gray-900">
                        {title || "Modifier l'utilisateur"}
                    </h1>
                    <p className="text-gray-600 mt-1">
                        {description || `Modifiez les informations de ${user.name}`}
                    </p>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <Input
                        id="name"
                        type="text"
                        className="mt-1 block w-full"
                        value={data.name}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) => setData('name', e.target.value)}
                        placeholder="Entrez le nom complet"
                        required
                    />


                    <Input
                        id="email"
                        type="email"
                        value={data.email}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) => setData('email', e.target.value)}
                        placeholder="Entrez l'adresse email"
                        required
                    />
                    {roles && <div>
                        <label htmlFor="role" className="block text-sm font-medium text-gray-700">
                            Rôle
                        </label>
                        <select
                            id="role"
                            value={data.role}
                            onChange={(e: React.ChangeEvent<HTMLSelectElement>) => setData('role', e.target.value)}
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                            {roles.map((role) => (
                                <option key={role} value={role}>
                                    {getRoleLabel(role)}
                                </option>
                            ))}
                        </select>
                        {errors.role && (
                            <p className="mt-2 text-sm text-red-600">{errors.role}</p>
                        )}
                    </div>}

                    <div className="relative">
                        <div className="absolute inset-0 flex items-center">
                            <div className="w-full border-t border-gray-300" />
                        </div>
                        <div className="relative flex justify-center text-sm">
                            <span className="px-2 bg-white text-gray-500">
                                Changer le mot de passe (optionnel)
                            </span>
                        </div>
                    </div>

                    <Input
                        id="password"
                        type="password"
                        value={data.password}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) => setData('password', e.target.value)}
                        placeholder="Laissez vide pour conserver le mot de passe actuel"
                    />

                    <Input
                        id="password_confirmation"
                        type="password"
                        value={data.password_confirmation}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) => setData('password_confirmation', e.target.value)}
                        placeholder="Confirmez le nouveau mot de passe"
                    />
                    <div className="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <Button
                            type="button"
                            color="secondary"
                            variant='outline'
                            size='sm'
                            onClick={handleCancel}
                        >
                            Annuler
                        </Button>
                        <Button
                            type="submit"
                            color="primary"
                            size='sm'
                            loading={processing}
                            disabled={processing}
                        >
                            {processing ? (
                                'Mise à jour...'
                            ) : (
                                'Mettre à jour'
                            )}
                        </Button>
                    </div>
                </form>
            </div>
        </Modal>
    );
}