import { useState, useEffect, useCallback, useMemo } from 'react';
import { router } from '@inertiajs/react';
import { DataTableState, PaginationType } from '@/types/datatable';

interface UseDataTableOptions {
    initialState?: Partial<DataTableState>;
    preserveState?: boolean;
    debounceMs?: number;
}

export function useDataTable<T>(
    data: PaginationType<T>,
    options: UseDataTableOptions = {}
) {
    const {
        initialState = {},
        preserveState = true,
        debounceMs = 300
    } = options;

    const [state, setState] = useState<DataTableState>({
        search: initialState.search || '',
        filters: initialState.filters || {},
        page: data.current_page,
        perPage: data.per_page,
        ...initialState
    });

    const [isNavigating, setIsNavigating] = useState(false);

    useEffect(() => {
        setState(prev => ({
            ...prev,
            page: data.current_page,
            perPage: data.per_page
        }));
    }, [data.current_page, data.per_page]);

    const buildUrl = useCallback((
        newState: Partial<DataTableState> = {},
        basePath?: string
    ) => {
        const finalState = { ...state, ...newState };
        const params = new URLSearchParams();

        params.set('page', String(finalState.page));
        params.set('per_page', String(finalState.perPage));

        if (finalState.search.trim()) {
            params.set('search', finalState.search.trim());
        }

        Object.entries(finalState.filters).forEach(([key, value]) => {
            if (value && value.trim()) {
                params.set(key, value.trim());
            }
        });

        const path = basePath || data.path;
        return `${path}?${params.toString()}`;
    }, [state, data.path]);

    const navigate = useCallback((
        newState: Partial<DataTableState> = {},
        options: { replace?: boolean; preserveState?: boolean } = {}
    ) => {
        const url = buildUrl(newState);
        setIsNavigating(true);

        router.get(url, {}, {
            preserveState: options.preserveState ?? preserveState,
            replace: options.replace ?? true,
            onFinish: () => setIsNavigating(false)
        });
    }, [buildUrl, preserveState]);

    const [debounceTimer, setDebounceTimer] = useState<NodeJS.Timeout | null>(null);

    const debouncedNavigate = useCallback((
        newState: Partial<DataTableState>,
        immediate = false
    ) => {
        if (debounceTimer) {
            clearTimeout(debounceTimer);
        }

        if (immediate) {
            navigate(newState);
            return;
        }

        const timer = setTimeout(() => {
            navigate(newState);
        }, debounceMs);

        setDebounceTimer(timer);
    }, [navigate, debounceTimer, debounceMs]);

    const actions = useMemo(() => ({
        setSearch: (search: string) => {
            setState(prev => ({ ...prev, search }));
            debouncedNavigate({ search, page: 1 });
        },

        setFilter: (key: string, value: string) => {
            setState(prev => ({
                ...prev,
                filters: { ...prev.filters, [key]: value }
            }));
            debouncedNavigate({
                filters: { ...state.filters, [key]: value },
                page: 1
            });
        },

        setFilters: (filters: Record<string, string>) => {
            setState(prev => ({ ...prev, filters }));
            debouncedNavigate({ filters, page: 1 });
        },

        resetFilters: () => {
            const newState = { search: '', filters: {}, page: 1 };
            setState(prev => ({ ...prev, ...newState }));
            navigate(newState, { replace: true });
        },

        goToPage: (page: number) => {
            setState(prev => ({ ...prev, page }));
            navigate({ page });
        },

        setPerPage: (perPage: number) => {
            setState(prev => ({ ...prev, perPage, page: 1 }));
            navigate({ perPage, page: 1 });
        },

        navigateToState: (newState: Partial<DataTableState>, immediate = false) => {
            setState(prev => ({ ...prev, ...newState }));
            if (immediate) {
                navigate(newState);
            } else {
                debouncedNavigate(newState);
            }
        }
    }), [state, navigate, debouncedNavigate]);

    useEffect(() => {
        return () => {
            if (debounceTimer) {
                clearTimeout(debounceTimer);
            }
        };
    }, [debounceTimer]);

    return {
        state,
        actions,
        isNavigating,
        buildUrl
    };
}