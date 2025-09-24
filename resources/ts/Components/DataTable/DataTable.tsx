import React, { useEffect } from 'react';
import { DataTableProps } from '@/types/datatable';
import { useDataTable } from './useDataTable';
import { DataTableFilters } from './DataTableFilters';
import { DataTablePagination } from './DataTablePagination';
import { EmptyState } from './EmptyState';

export function DataTable<T>({
    data,
    config,
    onStateChange,
    isLoading = false,
    className = ''
}: DataTableProps<T>) {
    const [initialized, setInitialized] = React.useState(false);
    const { state, actions, isNavigating } = useDataTable(data);

    useEffect(() => {
        if (!initialized) {
            const params = new URLSearchParams(window.location.search);
            let hasInit = false;

            const search = params.get('search');
            if (search) {
                actions.setSearch(search);
                hasInit = true;
            }
            if (config.filters) {
                config.filters.forEach((filter) => {
                    const value = params.get(filter.key);
                    if (value !== null) {
                        actions.setFilter(filter.key, value);
                        hasInit = true;
                    }
                });
            }
            const page = params.get('page');
            if (page && !isNaN(Number(page))) {
                actions.goToPage(Number(page));
                hasInit = true;
            }
            const perPage = params.get('per_page');
            if (perPage && !isNaN(Number(perPage))) {
                actions.setPerPage(Number(perPage));
                hasInit = true;
            }
            if (hasInit) setInitialized(true);
        }
    }, [initialized, config.filters, actions]);

    useEffect(() => {
        onStateChange?.(state);
    }, [state, onStateChange]);

    const hasActiveFilters = state.search || Object.values(state.filters).some(v => v);
    const isEmpty = data.data.length === 0;
    const showEmptyState = isEmpty && !isLoading;
    const showEmptySearchState = showEmptyState && hasActiveFilters;

    const renderTableHeader = () => (
        <thead className="bg-gray-50">
            <tr>
                {config.columns.map((column) => (
                    <th
                        key={column.key}
                        className={`px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ${column.className || ''}`}
                    >
                        {column.label}
                    </th>
                ))}
            </tr>
        </thead>
    );

    const renderTableBody = () => (
        <tbody className="bg-white divide-y divide-gray-200">
            {data.data.map((item, index) => (
                <tr key={index} className="hover:bg-gray-50">
                    {config.columns.map((column) => (
                        <td
                            key={column.key}
                            className={`px-6 py-4 whitespace-nowrap ${column.className || ''}`}
                        >
                            {column.render ? (
                                column.render(item, index)
                            ) : (
                                <span className="text-sm text-gray-900">
                                    {String((item as any)[column.key] || '')}
                                </span>
                            )}
                        </td>
                    ))}
                </tr>
            ))}
        </tbody>
    );

    const renderEmptyState = () => {
        if (showEmptySearchState && config.emptySearchState) {
            return (
                <EmptyState
                    title={config.emptySearchState.title}
                    subtitle={config.emptySearchState.subtitle}
                    icon={config.emptySearchState.icon}
                    actions={
                        <button
                            onClick={actions.resetFilters}
                            className="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            {config.emptySearchState.resetLabel || 'Réinitialiser les filtres'}
                        </button>
                    }
                />
            );
        }

        if (showEmptyState && config.emptyState) {
            return (
                <EmptyState
                    title={config.emptyState.title}
                    subtitle={config.emptyState.subtitle}
                    icon={config.emptyState.icon}
                    actions={config.emptyState.actions}
                />
            );
        }

        return null;
    };

    const renderLoadingOverlay = () => {
        return null;
    };

    return (
        <div className={`bg-white rounded-lg overflow-hidden ${className}`}>
            {(config.filters?.length || config.searchPlaceholder) && (
                <DataTableFilters
                    filters={config.filters || []}
                    values={state.filters}
                    searchValue={state.search}
                    searchPlaceholder={config.searchPlaceholder}
                    onSearchChange={actions.setSearch}
                    onFilterChange={actions.setFilter}
                    onReset={actions.resetFilters}
                    isLoading={isLoading || isNavigating}
                />
            )}

            <div className="relative">
                {renderLoadingOverlay()}

                {showEmptyState ? (
                    renderEmptyState()
                ) : (
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            {renderTableHeader()}
                            {renderTableBody()}
                        </table>
                    </div>
                )}
            </div>

            {!showEmptyState && (
                <DataTablePagination
                    data={data}
                    onPageChange={actions.goToPage}
                    onPerPageChange={actions.setPerPage}
                    isLoading={isLoading || isNavigating}
                    perPageOptions={config.perPageOptions}
                />
            )}
        </div>
    );
}