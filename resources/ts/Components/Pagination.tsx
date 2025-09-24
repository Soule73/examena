import React, { useMemo, useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { ChevronDoubleLeftIcon, ChevronDownIcon, DocumentTextIcon } from '@heroicons/react/24/outline';
import Input from './form/Input';
import { Button } from './Button';

interface PaginationProps {
    searchQuery?: string;
    statusFilter?: string | undefined;
    current_page: number;
    first_page_url: string;
    from: number | null;
    last_page: number;
    last_page_url: string;
    links: {
        url: string | null;
        label: string;
        page: number | null;
        active: boolean;
    }[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
    perPageOptions?: number[];
    queryName?: string;
    onChange?: (page: number, perPage: number) => void;
}

// const defaultPerPageOptions = [10, 25, 50, 100];

const baseBtn =
    'px-3 py-2 border text-xs font-medium transition-colors inline-flex items-center justify-center';
const disabledStyle = 'bg-white border-gray-200 text-gray-400 cursor-not-allowed opacity-60';
const normalStyle = 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50';

const Pagination: React.FC<PaginationProps> = ({
    searchQuery,
    statusFilter,
    current_page,
    first_page_url,
    from,
    last_page,
    last_page_url,
    links,
    next_page_url,
    path,
    per_page,
    prev_page_url,
    to,
    total,
    perPageOptions,
    queryName = 'page',
    onChange,
}) => {
    const [jumpPage, setJumpPage] = useState<string>('');
    const [isNavigating, setIsNavigating] = useState(false);

    const cleanLabel = (label: string) => {
        if (label === 'pagination.previous') return 'Précédent';
        if (label === 'pagination.next') return 'Suivant';
        if (!isNaN(Number(label))) return label;
        return label.replace(/&[^;]+;/g, '').replace(/<\/?[^>]+(>|$)/g, '');
    };

    const buildUrl = (baseUrl: string | null, page?: number, perPage?: number) => {
        if (!baseUrl) return null;
        const params = new URLSearchParams();
        params.set('per_page', String(perPage ?? per_page));
        params.set(queryName, String(page ?? current_page));
        if (searchQuery) params.set('search', searchQuery);
        if (statusFilter) params.set('status', statusFilter);
        return `${path}?${params.toString()}`;
    };

    const navigate = (url: string | null, preserveState = true, finishedPage?: number, newPerPage?: number) => {
        if (!url) return;
        setIsNavigating(true);
        router.get(url, {}, {
            preserveState,
            onFinish: () => {
                setIsNavigating(false);
                onChange?.(finishedPage ?? current_page, newPerPage ?? per_page);
            },
        });
    };

    const handlePerPageChange = (newPerPage: number) => {
        navigate(buildUrl(first_page_url, 1, newPerPage), false, 1, newPerPage);
    };

    const handleJumpToPage = (e?: React.FormEvent) => {
        e?.preventDefault();
        const pageNum = Number(jumpPage);
        if (!pageNum || pageNum < 1 || pageNum > last_page) return;
        navigate(buildUrl(path, pageNum), true, pageNum);
        setJumpPage('');
    };

    const pageRangeText = useMemo(
        () => `Affichage de ${from ?? 0} à ${to ?? 0} sur ${total} résultats`,
        [from, to, total],
    );

    const computedPerPageOptions = useMemo(() => {
        if (perPageOptions && perPageOptions.length > 0) return perPageOptions;
        const maxSteps = 10;
        const totalInt = Math.max(1, Math.floor(total));
        const step = Math.max(1, Math.ceil(totalInt / maxSteps));
        const opts: number[] = [];
        for (let v = step; v <= totalInt; v += step) opts.push(v);
        if (opts.length < maxSteps && opts[opts.length - 1] !== totalInt) opts.push(totalInt);
        return opts.slice(0, maxSteps);
    }, [perPageOptions, total]);

    const renderButton = ({
        children,
        ariaLabel,
        onClick,
        disabled,
        roundedLeft,
        roundedRight,
        active,
        title,
        className,
    }: {
        children: React.ReactNode;
        ariaLabel?: string;
        onClick?: () => void;
        disabled?: boolean;
        roundedLeft?: boolean;
        roundedRight?: boolean;
        active?: boolean;
        title?: string;
        className?: string;
    }) => {
        const borderRadius = roundedLeft ? 'rounded-l-md' : roundedRight ? 'rounded-r-md' : '';
        const activeStyle = active ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : '';
        const stateClass = disabled ? disabledStyle : normalStyle;
        return (
            <button
                type="button"
                onClick={onClick}
                disabled={disabled || isNavigating}
                aria-label={ariaLabel}
                title={title}
                className={`${baseBtn} ${stateClass} ${activeStyle} ${borderRadius} ${className ?? ''}`}
            >
                {children}
            </button>
        );
    };

    return (
        <div className="px-6 py-3 border-t border-gray-200 bg-white">
            <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div className="flex items-center gap-4">
                    <div className="text-xs text-gray-700">{pageRangeText}</div>
                    <div className="relative">
                        <select
                            aria-label="Sélectionner le nombre de résultats par page"
                            value={per_page}
                            onChange={(e) => handlePerPageChange(Number(e.target.value))}
                            className="appearance-none border rounded px-2 py-1.5 text-xs border-gray-300 text-gray-600 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 pr-6"
                            disabled={isNavigating}
                            style={{ background: 'none' }}
                        >
                            {computedPerPageOptions.map((opt) => (
                                <option key={opt} value={opt} className="text-gray-700 bg-white">
                                    {opt} / page
                                </option>
                            ))}
                            <option key="all" value={total} className="text-gray-700 bg-white">
                                Tous
                            </option>
                        </select>
                        <ChevronDownIcon className="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 h-4 w-4" />
                    </div>
                </div>
                <div className="flex items-center">
                    {renderButton({
                        children: <ChevronDoubleLeftIcon className="h-4 w-4" />,
                        ariaLabel: 'Aller à la première page',
                        onClick: () => navigate(buildUrl(first_page_url, 1)),
                        disabled: !first_page_url || current_page === 1,
                        roundedLeft: true,
                    })}
                    {renderButton({
                        children: 'Précédent',
                        ariaLabel: 'Page précédente',
                        onClick: () => navigate(buildUrl(prev_page_url, current_page - 1)),
                        disabled: !prev_page_url,
                        className: 'border-y',
                    })}
                    <nav className="inline-flex -space-x-px rounded-md" aria-label="Pagination">
                        {links.map((link, index) => {
                            const label = cleanLabel(link.label);
                            const isEllipsis = label === '...';
                            const isActive = link.active;
                            const disabled = !link.url || isEllipsis;
                            if (link.url && !isEllipsis) {
                                return (
                                    <Link
                                        key={index}
                                        href={buildUrl(link.url, link.page ?? undefined) ?? ''}
                                        as="button"
                                        onClick={() => {
                                            setIsNavigating(true);
                                            onChange?.(link.page ?? current_page, per_page);
                                        }}
                                        className={`${baseBtn} ${isActive ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'} px-3 py-2 text-xs font-medium`}
                                        aria-current={isActive ? 'page' : undefined}
                                    >
                                        {label}
                                    </Link>
                                );
                            }
                            return (
                                <span key={index} className="relative inline-flex">
                                    {renderButton({
                                        children: label,
                                        onClick: () => {
                                            if (!disabled) navigate(link.url);
                                        },
                                        disabled,
                                        active: isActive,
                                    })}
                                </span>
                            );
                        })}
                    </nav>
                    {renderButton({
                        children: 'Suivant',
                        ariaLabel: 'Page suivante',
                        onClick: () => navigate(buildUrl(next_page_url, current_page + 1)),
                        disabled: !next_page_url,
                        className: 'border-y',
                    })}
                    {renderButton({
                        children: <ChevronDoubleLeftIcon className="h-4 w-4 rotate-180" />,
                        ariaLabel: 'Aller à la dernière page',
                        onClick: () => navigate(buildUrl(last_page_url, last_page)),
                        disabled: !last_page_url || current_page === last_page,
                        roundedRight: true,
                    })}
                    <form onSubmit={handleJumpToPage} className="flex items-center gap-2 ml-2">
                        <label htmlFor="jumpPage" className="sr-only">
                            Aller à la page
                        </label>
                        <Input
                            id="jumpPage"
                            type="number"
                            min={1}
                            max={last_page}
                            value={jumpPage}
                            onChange={(e) => setJumpPage(e.target.value)}
                            placeholder={`${current_page}/${last_page}`}
                            className="w-20 border rounded px-2 py-1 text-xs"
                            aria-label="Saisir le numéro de page"
                            disabled={isNavigating || last_page <= 1}
                        />
                        <Button
                            type="submit"
                            disabled={isNavigating || last_page <= 1}
                            className={`px-3 py-1.5 border rounded text-xs ${isNavigating ? 'opacity-60 cursor-not-allowed' : 'bg-indigo-600 text-white hover:bg-indigo-700 cursor-pointer'}`}
                        >
                            Aller
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    );
};

const EmptyState: React.FC<{ title: string; subtitle: string; actions?: React.ReactNode, icon?: React.ReactNode }> = ({ title, subtitle, actions, icon }) => {
    return (
        <div className="text-center py-12 bg-white  ">
            <div className="text-gray-400">
                {icon || <DocumentTextIcon className="w-12 h-12 mx-auto mb-4" />}
            </div>
            <h3 className=" font-medium text-gray-700 mb-2">
                {title}
            </h3>
            <p className="text-gray-600 mb-6 text-xs">
                {subtitle}
            </p>
            {actions && <div className="inline-block">{actions}</div>}
        </div>
    );
}


export default Pagination;

export { EmptyState };
