import { Link } from '@inertiajs/react';

interface LogoProps {
    className?: string;
}

export const Logo = ({ className = '' }: LogoProps) => {
    return (
        <Link href="/dashboard" className={`flex items-center space-x-2 ${className}`}>
            <div className="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                <span className="text-white font-bold text-sm">E</span>
            </div>
            <span className="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                ExamENA
            </span>
        </Link>
    );
};