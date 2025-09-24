import { Head, usePage } from '@inertiajs/react';
import { PageProps } from '@/types';
import { useEffect, useMemo, useState } from 'react';
import {
    Logo,
    DesktopNavigation,
    MobileNavigation,
    UserMenu,
    MobileMenuButton
} from '@/Components/Navigation';
import FlashToastHandler from '@/Components/Toast/FlashToastHandler';

interface AuthenticatedLayoutProps {
    children: React.ReactNode;
    title?: string;
}

const AuthenticatedLayout = ({ children, title }: AuthenticatedLayoutProps) => {
    const { auth, flash } = usePage<PageProps>().props;
    const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

    const isAdmin = !!auth.user.roles?.some(role => role.name === 'admin');
    const isTeacher = !!auth.user.roles?.some(role => role.name === 'teacher');
    const isStudent = !!auth.user.roles?.some(role => role.name === 'student');

    useEffect(() => {
        const handleResize = () => {
            if (window.innerWidth >= 640) {
                setIsMobileMenuOpen(false);
            }
        };

        window.addEventListener('resize', handleResize);
        return () => {
            window.removeEventListener('resize', handleResize);
        };
    }, []);

    const currentHref = useMemo(() => window.location.href, []);

    return (
        <>
            <Head title={title} />

            <div className="min-h-screen bg-gray-50">
                <nav className="bg-white border-b border-gray-200 fixed w-full z-10 top-0">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="flex justify-between h-16">
                            <div className="flex items-center">
                                <div className="flex-shrink-0">
                                    <Logo />
                                </div>

                                <DesktopNavigation
                                    isAdmin={isAdmin}
                                    isTeacher={isTeacher}
                                    isStudent={isStudent}
                                    currentHref={currentHref}
                                />
                            </div>

                            <div className="flex items-center">
                                <UserMenu
                                    user={auth.user}
                                    isAdmin={isAdmin}
                                    isTeacher={isTeacher}
                                />

                                <MobileMenuButton
                                    isOpen={isMobileMenuOpen}
                                    onToggle={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
                                />
                            </div>
                        </div>

                        <MobileNavigation
                            isAdmin={isAdmin}
                            isTeacher={isTeacher}
                            isStudent={isStudent}
                            currentHref={currentHref}
                            isOpen={isMobileMenuOpen}
                        />

                        {isMobileMenuOpen && (
                            <UserMenu
                                user={auth.user}
                                isAdmin={isAdmin}
                                isTeacher={isTeacher}
                                isMobile={true}
                            />
                        )}
                    </div>
                </nav>

                <main className="pb-8 pt-20">
                    <div className="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
                        {children}
                    </div>
                </main>
                <FlashToastHandler flash={flash} />
            </div>
        </>
    );
};

export default AuthenticatedLayout;