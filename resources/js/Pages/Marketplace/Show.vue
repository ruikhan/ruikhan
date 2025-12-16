<script setup>
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

// 1. Import all your theme components
import AppleMacTheme from '@/Pages/Marketplace/Themes/AppleMacTheme.vue'; // Tier 3: Premium
import ModernDarkTheme from '@/Pages/Marketplace/Themes/ModernDarkTheme.vue'; // Tier 1: Basic
import StandardTheme from '@/Pages/Marketplace/Themes/StandardTheme.vue'; // Tier 2: Standard

const props = defineProps({
    business: Object,
    products: Array
});

// 2. Enhanced Logic to select theme
const currentTheme = computed(() => {
    // ----------------------------------------------------------------
    // PRIORITY 1: Manual Override
    // Check if the business has explicitly chosen a theme in their settings.
    // ----------------------------------------------------------------
    if (props.business.theme_preference) {
        switch (props.business.theme_preference) {
            case 'apple': return AppleMacTheme;
            case 'standard': return StandardTheme;
            case 'modern_dark': return ModernDarkTheme;
            // If they picked something invalid, break and fall through to tier logic
        }
    }

    // ----------------------------------------------------------------
    // PRIORITY 2: Tier-based Defaults
    // ----------------------------------------------------------------
    const tier = props.business.tier?.toLowerCase() || 'basic'; // Default to basic if null

    // Tier 3: Premium
    if (tier === 'premium') {
        return AppleMacTheme;
    }
    
    // Tier 2: Standard
    if (tier === 'standard') {
        return StandardTheme;
    }

    // Tier 1: Basic (and Default Fallback)
    // This catches 'basic' and any undefined tiers
    return ModernDarkTheme;
});
</script>

<template>
    <Head :title="business.name" />
    
    <component 
        :is="currentTheme" 
        v-bind="props" 
    />
</template>