import {
    Head,
    // Link
} from '@inertiajs/react';
// import { route } from 'ziggy-js';

interface GuestLayoutProps {
    children: React.ReactNode;
    title?: string;
}

const GuestLayout = ({ children, title }: GuestLayoutProps) => {
    return (
        <>
            <Head title={title} />

            <div className="min-h-screen px-4 pt-6 sm:pt-0 bg-gray-50">

                <div>
                    {children}
                </div>
            </div>
        </>
    );
};

export default GuestLayout;