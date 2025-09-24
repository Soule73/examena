import { ChevronUpIcon } from "@heroicons/react/24/outline";
import React from "react";

interface SectionProps {
    title: React.ReactNode;
    subtitle?: React.ReactNode;
    actions?: React.ReactNode;
    children: React.ReactNode;
    collapsible?: boolean;
    className?: string;
    centerHeaderItems?: boolean;
}

const Section = ({ title, subtitle, actions, children, collapsible = false, className, centerHeaderItems = true }: SectionProps) => {
    const [isOpen, setIsOpen] = React.useState(true);

    const isStringTitle = typeof title === 'string';

    return (
        <section className={`bg-white rounded-lg p-2 md:p-6 ${isOpen ? "" : "!pt-1 !pb-0"} mb-6 border border-gray-200 ${className}`}>
            <div className={` text-gray-800 ${isOpen ? "mb-4 border-b pb-2" : ""} border-gray-300 `}>
                <div className={` ${centerHeaderItems ? 'md:items-center' : ''} ${actions ? "flex space-y-4 flex-col md:flex-row md:justify-between mb-2" : "mb-2"}`}>
                    <div onClick={() => setIsOpen(collapsible ? !isOpen : isOpen)} className={`flex ${collapsible ? 'cursor-pointer' : ''} items-center space-x-2`}>
                        {collapsible && (
                            <ChevronUpIcon className={`h-5 w-5 ${isOpen ? '' : 'rotate-180'}`} />

                        )}
                        {isStringTitle ? (
                            <h2 className="text-xl font-semibold text-gray-800">
                                {title}
                            </h2>
                        ) : (
                            title
                        )}
                    </div>
                    {actions && <>{actions}</>}
                </div>
                {subtitle && (
                    <div className="text-gray-800">
                        {subtitle}
                    </div>
                )}
            </div>

            {(!collapsible || isOpen) && (
                <div className="space-y-6">
                    {children}
                </div>
            )}
        </section>
    );
};

export default Section;
