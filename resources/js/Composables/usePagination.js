// resources/js/Composables/usePagination.js
import { router } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

export function usePagination(initialData, routeName) {
    const currentPage = ref(initialData.current_page || 1);
    const perPage = ref(initialData.per_page || 10);
    const searchQuery = ref('');
    const filters = ref({});
    const sortBy = ref('created_at');
    const sortOrder = ref('desc');
    
    // Save state to sessionStorage before any navigation
    const saveState = () => {
        const state = {
            page: currentPage.value,
            perPage: perPage.value,
            search: searchQuery.value,
            filters: filters.value,
            sortBy: sortBy.value,
            sortOrder: sortOrder.value,
            timestamp: Date.now()
        };
        sessionStorage.setItem(`pagination_${routeName}`, JSON.stringify(state));
    };

    // Restore state from sessionStorage
    const restoreState = () => {
        const saved = sessionStorage.getItem(`pagination_${routeName}`);
        if (saved) {
            try {
                const state = JSON.parse(saved);
                // Only restore if less than 5 minutes old
                if (Date.now() - state.timestamp < 5 * 60 * 1000) {
                    currentPage.value = state.page;
                    perPage.value = state.perPage;
                    searchQuery.value = state.search;
                    filters.value = state.filters;
                    sortBy.value = state.sortBy;
                    sortOrder.value = state.sortOrder;
                    return true;
                }
            } catch (e) {
                console.error('Failed to restore pagination state:', e);
            }
        }
        return false;
    };

    // Navigate to page with error handling
    const goToPage = (page) => {
        saveState();
        currentPage.value = page;
        
        router.get(route(routeName), {
            page: page,
            per_page: perPage.value,
            search: searchQuery.value,
            sort_by: sortBy.value,
            sort_order: sortOrder.value,
            ...filters.value
        }, {
            preserveState: true,
            preserveScroll: true,
            onError: (errors) => {
                if (errors.status === 419) {
                    // CSRF expired - save state and reload
                    saveState();
                    window.location.reload();
                }
            },
            onSuccess: () => {
                // Clear old state after successful navigation
                sessionStorage.removeItem(`pagination_${routeName}`);
            }
        });
    };

    // Search with debouncing
    let searchTimeout;
    const search = (query) => {
        searchQuery.value = query;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage.value = 1; // Reset to first page on search
            goToPage(1);
        }, 500);
    };

    // Apply filters
    const applyFilters = (newFilters) => {
        filters.value = { ...filters.value, ...newFilters };
        currentPage.value = 1;
        goToPage(1);
    };

    // Change sort
    const changeSort = (column) => {
        if (sortBy.value === column) {
            sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
        } else {
            sortBy.value = column;
            sortOrder.value = 'asc';
        }
        goToPage(currentPage.value);
    };

    // Computed properties
    const totalPages = computed(() => initialData.last_page || 1);
    const hasNextPage = computed(() => currentPage.value < totalPages.value);
    const hasPrevPage = computed(() => currentPage.value > 1);

    // Generate page numbers for pagination UI
    const pageNumbers = computed(() => {
        const pages = [];
        const total = totalPages.value;
        const current = currentPage.value;
        
        if (total <= 7) {
            // Show all pages if 7 or fewer
            for (let i = 1; i <= total; i++) {
                pages.push(i);
            }
        } else {
            // Smart pagination: 1 ... 4 5 [6] 7 8 ... 20
            pages.push(1);
            
            if (current > 3) {
                pages.push('...');
            }
            
            for (let i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) {
                pages.push(i);
            }
            
            if (current < total - 2) {
                pages.push('...');
            }
            
            pages.push(total);
        }
        
        return pages;
    });

    // Restore state on mount
    onMounted(() => {
        if (restoreState()) {
            goToPage(currentPage.value);
        }
    });

    // Auto-save before page unload
    window.addEventListener('beforeunload', saveState);

    return {
        currentPage,
        perPage,
        searchQuery,
        filters,
        sortBy,
        sortOrder,
        totalPages,
        hasNextPage,
        hasPrevPage,
        pageNumbers,
        goToPage,
        search,
        applyFilters,
        changeSort,
        saveState,
        restoreState
    };
}