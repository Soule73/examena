interface UserAvatarProps {
    name: string;
    size?: 'sm' | 'md' | 'lg';
    className?: string;
}

export const UserAvatar = ({ name, size = 'md', className = '' }: UserAvatarProps) => {
    const sizeClasses = {
        sm: 'w-6 h-6 text-xs',
        md: 'w-8 h-8 text-sm',
        lg: 'w-10 h-10 text-base'
    };

    const sizeClass = sizeClasses[size];

    return (
        <div className={`bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center ${sizeClass} ${className}`}>
            <span className="text-white font-medium">
                {name.charAt(0).toUpperCase()}
            </span>
        </div>
    );
};