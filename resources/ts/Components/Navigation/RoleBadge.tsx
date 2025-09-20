interface RoleBadgeProps {
    role: 'admin' | 'teacher' | 'student';
    className?: string;
}

export const RoleBadge = ({ role, className = '' }: RoleBadgeProps) => {
    const badgeConfig = {
        admin: {
            className: 'bg-red-100 text-red-800',
            label: 'Admin'
        },
        teacher: {
            className: 'bg-green-100 text-green-800',
            label: 'Enseignant'
        },
        student: {
            className: 'bg-blue-100 text-blue-800',
            label: 'Étudiant'
        }
    };

    const config = badgeConfig[role];

    return (
        <span className={`px-2 py-1 text-xs font-medium rounded-full ${config.className} ${className}`}>
            {config.label}
        </span>
    );
};