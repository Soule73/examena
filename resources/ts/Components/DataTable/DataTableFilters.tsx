import React from 'react';
import { Input } from '@/Components';
import { ChevronDownIcon, FunnelIcon } from '@heroicons/react/24/outline';
import { FilterConfig } from '@/types/datatable';

interface DataTableFiltersProps {
    filters: FilterConfig[];
    values: Record<string, string>;
    searchValue: string;
    searchPlaceholder?: string;
    onSearchChange: (value: string) => void;
    onFilterChange: (key: string, value: string) => void;
    onReset: () => void;
    showResetButton?: boolean;
    isLoading?: boolean;
}

export const DataTableFilters: React.FC<DataTableFiltersProps> = ({
    filters,
    values,
    searchValue,
    searchPlaceholder = "Rechercher...",
    onSearchChange,
    onFilterChange,
    onReset,
    showResetButton = true,
    isLoading = false
}) => {
    const hasActiveFilters = searchValue || Object.values(values).some(v => v);

    return (
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-gray-50 border-b border-gray-200">
            <div className="flex items-center gap-3">
                <div className="flex-1 max-w-sm">
                    <Input
                        type="text"
                        placeholder={searchPlaceholder}
                        value={searchValue}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) => onSearchChange(e.target.value)}
                        className="!py-2 !px-3 text-sm"
                    />
                </div>

                {isLoading && (
                    <div className="flex items-center gap-2 text-blue-600">
                        <div className="w-4 h-4 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                        <span className="text-sm">Chargement...</span>
                    </div>
                )}
            </div>

            <div className="flex items-center gap-3">
                {filters.map((filter) => (
                    <div key={filter.key} className="relative">
                        {filter.type === 'select' && filter.options ? (
                            <div className="relative">
                                <select
                                    value={values[filter.key] || filter.defaultValue || ''}
                                    onChange={(e: React.ChangeEvent<HTMLSelectElement>) => onFilterChange(filter.key, e.target.value)}
                                    className="appearance-none bg-white border border-gray-300 rounded-md py-2 pl-3 pr-8 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">{filter.label}</option>
                                    {filter.options.map((option) => (
                                        <option key={option.value} value={option.value}>
                                            {option.label}
                                        </option>
                                    ))}
                                </select>
                                <ChevronDownIcon className="absolute right-2 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 pointer-events-none" />
                            </div>
                        ) : (
                            <Input
                                type="text"
                                placeholder={filter.placeholder || filter.label}
                                value={values[filter.key] || ''}
                                onChange={(e) => onFilterChange(filter.key, e.target.value)}
                                className="!py-2 !px-3 text-sm max-w-32"
                            />
                        )}
                    </div>
                ))}

                {showResetButton && hasActiveFilters && (
                    <button
                        onClick={onReset}
                        className="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                    >
                        <FunnelIcon className="w-4 h-4 mr-1" />
                        Réinitialiser
                    </button>
                )}
            </div>
        </div>
    );
};