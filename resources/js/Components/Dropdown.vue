<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    align: {
        default: 'right',
    },
    width: {
        default: '48',
    },
    contentClasses: {
        default: () => ['py-1'],
    },
});

const open = ref(false);

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const widthClass = computed(() => {
    return {
        48: 'w-48',
        56: 'w-56',
        64: 'w-64',
    }[props.width.toString()];
});

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'origin-top-left left-0';
    } else if (props.align === 'right') {
        return 'origin-top-right right-0';
    } else {
        return 'origin-top';
    }
});
</script>

<template>
    <div class="dropdown-container">
        <div @click="open = !open">
            <slot name="trigger" />
        </div>

        <!-- Enhanced Backdrop -->
        <Transition name="backdrop">
            <div v-show="open" class="dropdown-backdrop" @click="open = false"></div>
        </Transition>

        <!-- Enhanced Dropdown Content -->
        <Transition
            enter-active-class="dropdown-enter-active"
            enter-from-class="dropdown-enter-from"
            enter-to-class="dropdown-enter-to"
            leave-active-class="dropdown-leave-active"
            leave-from-class="dropdown-leave-from"
            leave-to-class="dropdown-leave-to"
        >
            <div
                v-show="open"
                :class="['dropdown-menu', widthClass, alignmentClasses]"
                @click="open = false"
            >
                <!-- Glass morphism layers -->
                <div class="dropdown-glass"></div>
                <div class="dropdown-border-glow"></div>
                <div class="dropdown-shine"></div>
                
                <!-- Content wrapper -->
                <div :class="['dropdown-content-wrapper', ...contentClasses]">
                    <slot name="content" />
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.dropdown-container {
    position: relative;
}

/* Enhanced Backdrop */
.dropdown-backdrop {
    position: fixed;
    inset: 0;
    z-index: 40;
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(4px);
}

.backdrop-enter-active,
.backdrop-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.backdrop-enter-from,
.backdrop-leave-to {
    opacity: 0;
    backdrop-filter: blur(0);
}

/* Enhanced Dropdown Menu */
.dropdown-menu {
    position: absolute;
    z-index: 50;
    margin-top: 0.75rem;
    border-radius: 1.5rem;
    overflow: hidden;
    box-shadow: 
        0 25px 70px rgba(0, 0, 0, 0.9),
        0 0 80px rgba(59, 130, 246, 0.1);
}

/* Glass morphism effect */
.dropdown-glass {
    position: absolute;
    inset: 0;
    background: rgba(10, 10, 12, 0.95);
    backdrop-filter: blur(80px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 1.5rem;
}

/* Border glow */
.dropdown-border-glow {
    position: absolute;
    inset: -2px;
    border-radius: 1.5rem;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(168, 85, 247, 0.2));
    filter: blur(12px);
    opacity: 0.5;
    z-index: -1;
    animation: glow-pulse 3s ease-in-out infinite;
}

@keyframes glow-pulse {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.6; }
}

/* Shine effect */
.dropdown-shine {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.05), transparent);
    animation: shine-sweep 8s ease-in-out infinite;
    pointer-events: none;
}

@keyframes shine-sweep {
    to { left: 200%; }
}

/* Content wrapper with proper z-index */
.dropdown-content-wrapper {
    position: relative;
    z-index: 10;
    max-height: 80vh;
    overflow-y: auto;
}

/* Custom scrollbar */
.dropdown-content-wrapper::-webkit-scrollbar {
    width: 6px;
}

.dropdown-content-wrapper::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 9999px;
}

.dropdown-content-wrapper::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 9999px;
    transition: background 0.3s;
}

.dropdown-content-wrapper::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.25);
}

/* Enhanced transitions */
.dropdown-enter-active {
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.dropdown-leave-active {
    transition: all 0.2s cubic-bezier(0.4, 0, 1, 1);
}

.dropdown-enter-from {
    opacity: 0;
    transform: scale(0.92) translateY(-10px);
}

.dropdown-enter-to {
    opacity: 1;
    transform: scale(1) translateY(0);
}

.dropdown-leave-from {
    opacity: 1;
    transform: scale(1) translateY(0);
}

.dropdown-leave-to {
    opacity: 0;
    transform: scale(0.92) translateY(-10px);
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .dropdown-menu {
        max-width: calc(100vw - 2rem);
    }
}

/* Reduce motion */
@media (prefers-reduced-motion: reduce) {
    .dropdown-enter-active,
    .dropdown-leave-active,
    .backdrop-enter-active,
    .backdrop-leave-active {
        transition-duration: 0.01ms !important;
    }
    
    .dropdown-shine {
        animation: none !important;
    }
}
</style>