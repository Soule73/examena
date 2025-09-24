import { Head, router } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';
import { useForm } from '@/hooks/useForm';
import { Button, Input } from '@/Components';
import { commonValidationSchemas } from '@/utils/validation';

interface ForgotPasswordFormData {
    email: string;
}

interface ForgotPasswordProps {
    status?: string;
}

export default function ForgotPassword({ status }: ForgotPasswordProps) {
    const {
        values,
        errors,
        isSubmitting,
        handleChange,
        handleSubmit,
    } = useForm<ForgotPasswordFormData>({
        initialValues: {
            email: '',
        },
        onSubmit: async (data) => {
            router.post('/forgot-password', data as any);
        },
        validate: (data) => {
            const emailValidation = commonValidationSchemas.user.email;
            const errors: Partial<Record<keyof ForgotPasswordFormData, string>> = {};

            if (!data.email.trim()) {
                errors.email = 'Email requis';
            } else if (!emailValidation.pattern?.test(data.email)) {
                errors.email = 'Format d\'email invalide';
            }

            return errors;
        },
    });

    return (
        <GuestLayout title="Mot de passe oublié">
            <Head title="Mot de passe oublié" />

            <div className="mb-4 text-sm text-gray-600">
                Vous avez oublié votre mot de passe ? Aucun problème.
                Indiquez-nous votre adresse e-mail et nous vous enverrons
                un lien de réinitialisation.
            </div>

            {status && (
                <div className="mb-4 font-medium text-sm text-green-600">
                    {status}
                </div>
            )}

            <form onSubmit={handleSubmit}>
                <div className="space-y-4">
                    <Input
                        label="Email"
                        type="email"
                        value={values.email}
                        onChange={(e) => handleChange('email')(e.target.value)}
                        error={errors.email}
                        required
                        autoComplete="username"
                        autoFocus
                    />

                    <div className="flex items-center justify-end mt-4">
                        <Button
                            type="submit"
                            loading={isSubmitting}
                        >
                            Envoyer le lien
                        </Button>
                    </div>
                </div>
            </form>
        </GuestLayout>
    );
}