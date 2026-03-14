<script setup>
import { computed } from 'vue';

const props = defineProps({
    data: {
        type: Object,
        required: true
    },
    maxVisible: {
        type: Number,
        default: 7
    }
});

const emit = defineEmits(['page-changed']);

const currentPage = computed(() => props.data.current_page);
const lastPage = computed(() => props.data.last_page);
const from = computed(() => props.data.from || 0);
const to = computed(() => props.data.to || 0);
const total = computed(() => props.data.total || 0);

// Generate smart page numbers
const pages = computed(() => {
    const result = [];
    const total = lastPage.value;
    const current = currentPage.value;
    const max = props.maxVisible;
    
    if (total <= max) {
        // Show all pages if total is less than max
        for (let i = 1; i <= total; i++) {
            result.push(i);
        }
    } else {
        // Always show first page
        result.push(1);
        
        // Calculate range around current page
        const start = Math.max(2, current - Math.floor((max - 2) / 2));
        const end = Math.min(total - 1, start + max - 3);
        
        // Add ellipsis after first page if needed
        if (start > 2) {
            result.push('...');
        }
        
        // Add middle pages
        for (let i = start; i <= end; i++) {
            result.push(i);
        }
        
        // Add ellipsis before last page if needed
        if (end < total - 1) {
            result.push('...');
        }
        
        // Always show last page
        if (total > 1) {
            result.push(total);
        }
    }
    
    return result;
});

const changePage = (page) => {
    if (page >= 1 && page <= lastPage.value && page !== currentPage.value) {
        emit('page-changed', page);
    }
};

const hasPages = computed(() => lastPage.value > 1);
const hasPrevious = computed(() => currentPage.value > 1);
const hasNext = computed(() => currentPage.value < lastPage.value);
</script>

<template>
    <div v-if="hasPages" class="pagination-wrapper">
        <!-- Results Info -->
        <div class="pagination-info">
            <p>
                Showing 
                <span class="font-semibold">{{ from }}</span> 
                to 
                <span class="font-semibold">{{ to }}</span> 
                of 
                <span class="font-semibold">{{ total }}</span> 
                results
            </p>
        </div>

        <!-- Pagination Controls -->
        <nav class="pagination-nav" aria-label="Pagination">
            <!-- First Page Button (Mobile Hidden) -->
            <button
                v-if="currentPage > 2"
                @click="changePage(1)"
                class="pagination-btn pagination-arrow hidden md:flex"
                title="First Page"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
                <span class="sr-only">First</span>
            </button>

            <!-- Previous Button -->
            <button
                @click="changePage(currentPage - 1)"
                :disabled="!hasPrevious"
                class="pagination-btn pagination-arrow"
                :class="{ 'disabled': !hasPrevious }"
                title="Previous Page"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span class="hidden sm:inline ml-2">Previous</span>
                <span class="sr-only sm:hidden">Previous</span>
            </button>

            <!-- Page Numbers -->
            <div class="pagination-numbers">
                <template v-for="(page, index) in pages" :key="index">
                    <!-- Ellipsis -->
                    <span v-if="page === '...'" class="pagination-ellipsis">
                        ⋯
                    </span>
                    
                    <!-- Page Button -->
                    <button
                        v-else
                        @click="changePage(page)"
                        class="pagination-btn pagination-number"
                        :class="{ 'active': page === currentPage }"
                        :aria-current="page === currentPage ? 'page' : undefined"
                    >
                        {{ page }}
                    </button>
                </template>
            </div>

            <!-- Next Button -->
            <button
                @click="changePage(currentPage + 1)"
                :disabled="!hasNext"
                class="pagination-btn pagination-arrow"
                :class="{ 'disabled': !hasNext }"
                title="Next Page"
            >
                <span class="hidden sm:inline mr-2">Next</span>
                <span class="sr-only sm:hidden">Next</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <!-- Last Page Button (Mobile Hidden) -->
            <button
                v-if="currentPage < lastPage - 1"
                @click="changePage(lastPage)"
                class="pagination-btn pagination-arrow hidden md:flex"
                title="Last Page"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                </svg>
                <span class="sr-only">Last</span>
            </button>
        </nav>
    </div>
</template>

<style scoped>
/* Container */
.pagination-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 0;
    gap: 1rem;
    flex-wrap: wrap;
}

@media (min-width: 640px) {
    .pagination-wrapper {
        flex-wrap: nowrap;
    }
}

/* Info Text */
.pagination-info {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.875rem;
    line-height: 1.5;
    order: 2;
    width: 100%;
}

@media (min-width: 640px) {
    .pagination-info {
        order: 1;
        width: auto;
    }
}

.pagination-info .font-semibold {
    color: rgba(255, 255, 255, 0.9);
    font-weight: 600;
}

/* Navigation Container */
.pagination-nav {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    order: 1;
    width: 100%;
    justify-content: center;
}

@media (min-width: 640px) {
    .pagination-nav {
        order: 2;
        width: auto;
    }
}

/* Page Numbers Container */
.pagination-numbers {
    display: flex;
    gap: 0.25rem;
    align-items: center;
}

/* Base Button Styles */
.pagination-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 2.5rem;
    height: 2.5rem;
    padding: 0.5rem;
    border-radius: 0.625rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.7);
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

/* Hover Effect */
.pagination-btn:hover:not(.disabled):not(.active) {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    transform: translateY(-1px);
    border-color: rgba(255, 255, 255, 0.2);
}

/* Active Effect */
.pagination-btn:active:not(.disabled) {
    transform: translateY(0);
}

/* Page Number Specific */
.pagination-number {
    min-width: 2.5rem;
    font-variant-numeric: tabular-nums;
}

/* Active Page */
.pagination-btn.active {
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    color: white;
    border-color: transparent;
    box-shadow: 
        0 4px 12px rgba(59, 130, 246, 0.3),
        0 0 0 1px rgba(255, 255, 255, 0.1) inset;
}

.pagination-btn.active::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), transparent);
    border-radius: inherit;
    opacity: 0;
    transition: opacity 0.3s;
}

.pagination-btn.active:hover::before {
    opacity: 1;
}

/* Disabled State */
.pagination-btn.disabled {
    opacity: 0.3;
    cursor: not-allowed;
    pointer-events: none;
}

/* Arrow Buttons */
.pagination-arrow {
    gap: 0.375rem;
    padding-left: 0.75rem;
    padding-right: 0.75rem;
}

.pagination-arrow svg {
    flex-shrink: 0;
}

/* Ellipsis */
.pagination-ellipsis {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 2.5rem;
    height: 2.5rem;
    color: rgba(255, 255, 255, 0.4);
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    user-select: none;
}

/* Ripple Effect on Click */
.pagination-btn::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
    opacity: 0;
    transform: scale(0);
    transition: transform 0.4s, opacity 0.4s;
}

.pagination-btn:active::after {
    transform: scale(1);
    opacity: 1;
    transition: transform 0s, opacity 0s;
}

/* Screen Reader Only */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}

/* Responsive Adjustments */
@media (max-width: 640px) {
    .pagination-btn {
        min-width: 2.25rem;
        height: 2.25rem;
        font-size: 0.8125rem;
    }
    
    .pagination-arrow {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    .pagination-numbers {
        gap: 0.125rem;
    }
}

/* Focus Styles for Accessibility */
.pagination-btn:focus-visible {
    outline: 2px solid rgba(59, 130, 246, 0.8);
    outline-offset: 2px;
    z-index: 1;
}

/* Animation for Page Change */
@keyframes page-pulse {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
    }
    50% {
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0);
    }
}

.pagination-btn.active {
    animation: page-pulse 2s ease-in-out infinite;
}

/* Loading State (optional) */
.pagination-wrapper.loading .pagination-btn {
    pointer-events: none;
    opacity: 0.5;
}

.pagination-wrapper.loading .pagination-btn::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.1),
        transparent
    );
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}

/* Dark Mode Enhancements */
@media (prefers-color-scheme: dark) {
    .pagination-wrapper {
        background: rgba(255, 255, 255, 0.02);
    }
    
    .pagination-btn {
        background: rgba(255, 255, 255, 0.03);
    }
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
    .pagination-btn {
        border-width: 2px;
    }
    
    .pagination-btn.active {
        border: 2px solid currentColor;
    }
}

/* Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    .pagination-btn,
    .pagination-btn::after {
        transition: none;
        animation: none;
    }
}
</style>