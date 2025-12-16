<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    plans: {
        type: Object,
        default: () => ({
            basic: { price: 100, months: 1 },
            standard: { price: 150, months: 1 },
            premium: { price: 200, months: 2 }
        })
    }
});

const form = useForm({
    business_name: '',
    description: '',
    category: 'retail',
    tier: 'standard',
});

const currentStep = ref(1);
const maxSteps = 3;

const tierDetails = {
    basic: {
        name: 'Basic',
        tagline: 'Start Your Journey',
        icon: '🎯',
        gradient: 'from-blue-500/20 via-blue-600/10 to-transparent',
        borderGlow: 'hover:shadow-[0_0_30px_rgba(59,130,246,0.3)]',
        features: [
            'Standardized Dashboard',
            'Basic Sales Tracking',
            'Inventory Management',
            'Business Status Controls',
            'Customer Reviews Access'
        ]
    },
    standard: {
        name: 'Standard',
        tagline: 'Grow Your Business',
        icon: '🚀',
        gradient: 'from-purple-500/20 via-purple-600/10 to-transparent',
        borderGlow: 'hover:shadow-[0_0_30px_rgba(168,85,247,0.3)]',
        badge: 'POPULAR',
        features: [
            'All Basic Features',
            'Customizable Dashboard',
            'Advanced Analytics',
            'AI-Generated Layouts',
            'Priority Support',
            'Enhanced Visibility'
        ]
    },
    premium: {
        name: 'Premium',
        tagline: 'Elite Business Status',
        icon: '👑',
        gradient: 'from-amber-500/20 via-amber-600/10 to-transparent',
        borderGlow: 'hover:shadow-[0_0_30px_rgba(245,158,11,0.3)]',
        badge: 'VIP',
        features: [
            'All Standard Features',
            'Verified VIP Badge',
            'Extended Duration (2 Months)',
            'Featured Listings',
            'Premium Analytics Dashboard',
            'Dedicated Support Channel',
            'Exclusive Marketing Tools'
        ]
    }
};

const selectedTierDetails = computed(() => tierDetails[form.tier]);

const submit = () => {
    form.post(route('business.store'), {
        onFinish: () => form.reset(),
    });
};

const selectTier = (tierName) => {
    form.tier = tierName;
};

const nextStep = () => {
    if (currentStep.value < maxSteps) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const canProceedStep1 = computed(() => form.category !== '');
const canProceedStep2 = computed(() => form.tier !== '');
const canSubmit = computed(() => form.business_name.trim() !== '' && form.description.trim() !== '');
</script>

<template>
    <Head title="Business Incubation Portal" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto space-y-8">
            
            <!-- Header -->
            <div class="relative overflow-hidden rounded-3xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 shadow-[0_20px_60px_-10px_rgba(0,0,0,0.5)] ring-1 ring-white/5">
                <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-[0.03] mix-blend-overlay"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 via-purple-500/10 to-amber-500/10"></div>
                
                <div class="relative p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 border border-white/10 mb-4">
                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h1 class="text-4xl font-bold bg-gradient-to-br from-white via-gray-200 to-gray-400 bg-clip-text text-transparent mb-3">
                        Business Incubation Portal
                    </h1>
                    <p class="text-gray-400 max-w-2xl mx-auto">
                        Transform your entrepreneurial vision into reality. Choose your path, select your tier, and start building your business empire.
                    </p>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-6 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <div v-for="step in maxSteps" :key="step" class="flex items-center flex-1">
                        <div class="flex flex-col items-center flex-1">
                            <div :class="[
                                'w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300',
                                currentStep >= step 
                                    ? 'bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-[0_0_20px_rgba(59,130,246,0.5)]' 
                                    : 'bg-white/5 text-gray-500 border border-white/10'
                            ]">
                                {{ step }}
                            </div>
                            <span :class="[
                                'mt-2 text-xs font-medium transition-colors',
                                currentStep >= step ? 'text-blue-400' : 'text-gray-500'
                            ]">
                                {{ step === 1 ? 'Category' : step === 2 ? 'Tier' : 'Details' }}
                            </span>
                        </div>
                        <div v-if="step < maxSteps" :class="[
                            'flex-1 h-1 mx-2 rounded-full transition-all duration-300',
                            currentStep > step ? 'bg-gradient-to-r from-blue-500 to-blue-600' : 'bg-white/10'
                        ]"></div>
                    </div>
                </div>
            </div>

            <!-- Step 1: Category Selection -->
            <div v-if="currentStep === 1" class="space-y-6">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-white mb-2">Choose Your Business Track</h2>
                    <p class="text-gray-400">Select the category that best represents your business model</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Retail Category -->
                    <div 
                        @click="form.category = 'retail'"
                        :class="[
                            'group cursor-pointer relative overflow-hidden rounded-3xl border-2 transition-all duration-500 hover:scale-[1.02]',
                            form.category === 'retail' 
                                ? 'bg-[#121212]/90 border-blue-500 shadow-[0_0_40px_rgba(59,130,246,0.4)]' 
                                : 'bg-[#121212]/70 border-white/10 hover:border-white/30'
                        ]"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 via-transparent to-transparent"></div>
                        <div class="relative p-8">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500/20 to-blue-600/20 border border-blue-500/30 flex items-center justify-center text-3xl">
                                        🛍️
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-white">Retail & Resale</h3>
                                        <p class="text-sm text-gray-400">Buy/Sell Products</p>
                                    </div>
                                </div>
                                <div v-if="form.category === 'retail'" class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                            <ul class="space-y-3 text-sm">
                                <li class="flex items-start text-gray-300">
                                    <svg class="w-5 h-5 text-blue-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Product inventory management
                                </li>
                                <li class="flex items-start text-gray-300">
                                    <svg class="w-5 h-5 text-blue-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Order tracking and fulfillment
                                </li>
                                <li class="flex items-start text-gray-300">
                                    <svg class="w-5 h-5 text-blue-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Stock alerts and reordering
                                </li>
                                <li class="flex items-start text-gray-300">
                                    <svg class="w-5 h-5 text-blue-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Customer reviews & ratings
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Service Category -->
                    <div 
                        @click="form.category = 'service'"
                        :class="[
                            'group cursor-pointer relative overflow-hidden rounded-3xl border-2 transition-all duration-500 hover:scale-[1.02]',
                            form.category === 'service' 
                                ? 'bg-[#121212]/90 border-purple-500 shadow-[0_0_40px_rgba(168,85,247,0.4)]' 
                                : 'bg-[#121212]/70 border-white/10 hover:border-white/30'
                        ]"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 via-transparent to-transparent"></div>
                        <div class="relative p-8">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500/20 to-purple-600/20 border border-purple-500/30 flex items-center justify-center text-3xl">
                                        🏪
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-white">Service & Hospitality</h3>
                                        <p class="text-sm text-gray-400">Establishing Services</p>
                                    </div>
                                </div>
                                <div v-if="form.category === 'service'" class="w-6 h-6 rounded-full bg-purple-500 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                            <ul class="space-y-3 text-sm">
                                <li class="flex items-start text-gray-300">
                                    <svg class="w-5 h-5 text-purple-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Service booking management
                                </li>
                                <li class="flex items-start text-gray-300">
                                    <svg class="w-5 h-5 text-purple-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Capacity and quota tracking
                                </li>
                                <li class="flex items-start text-gray-300">
                                    <svg class="w-5 h-5 text-purple-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Real-time status updates
                                </li>
                                <li class="flex items-start text-gray-300">
                                    <svg class="w-5 h-5 text-purple-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Customer engagement tools
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button 
                        @click="nextStep"
                        :disabled="!canProceedStep1"
                        :class="[
                            'px-8 py-3 rounded-full font-semibold text-sm transition-all duration-300',
                            canProceedStep1 
                                ? 'bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white shadow-[0_0_20px_rgba(59,130,246,0.4)] hover:shadow-[0_0_30px_rgba(59,130,246,0.6)] active:scale-95' 
                                : 'bg-gray-700/50 text-gray-500 cursor-not-allowed'
                        ]"
                    >
                        Continue to Tier Selection →
                    </button>
                </div>
            </div>

            <!-- Step 2: Tier Selection -->
            <div v-if="currentStep === 2" class="space-y-6">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-white mb-2">Select Your Subscription Tier</h2>
                    <p class="text-gray-400">Choose the plan that fits your business goals and budget</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div 
                        v-for="(details, tierName) in tierDetails" 
                        :key="tierName"
                        @click="selectTier(tierName)"
                        :class="[
                            'group cursor-pointer relative overflow-hidden rounded-3xl border-2 transition-all duration-500 hover:scale-[1.02]',
                            form.tier === tierName 
                                ? `bg-[#121212]/90 ${tierName === 'basic' ? 'border-blue-500 shadow-[0_0_40px_rgba(59,130,246,0.4)]' : tierName === 'standard' ? 'border-purple-500 shadow-[0_0_40px_rgba(168,85,247,0.4)]' : 'border-amber-500 shadow-[0_0_40px_rgba(245,158,11,0.4)]'}`
                                : 'bg-[#121212]/70 border-white/10 hover:border-white/30'
                        ]"
                    >
                        <div :class="`absolute inset-0 bg-gradient-to-br ${details.gradient}`"></div>
                        
                        <!-- Badge -->
                        <div v-if="details.badge" class="absolute top-4 right-4 z-10">
                            <span :class="[
                                'px-3 py-1 rounded-full text-xs font-bold',
                                tierName === 'standard' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30'
                            ]">
                                {{ details.badge }}
                            </span>
                        </div>

                        <div class="relative p-8">
                            <div class="text-center mb-6">
                                <div class="text-5xl mb-4">{{ details.icon }}</div>
                                <h3 class="text-2xl font-bold text-white mb-1">{{ details.name }}</h3>
                                <p class="text-sm text-gray-400 mb-4">{{ details.tagline }}</p>
                                <div class="text-4xl font-bold bg-gradient-to-br from-white to-gray-400 bg-clip-text text-transparent">
                                    ₱{{ props.plans[tierName]?.price || 0 }}
                                </div>
                                <p class="text-sm text-gray-400 mt-1">
                                    {{ props.plans[tierName]?.months || 1 }} {{ props.plans[tierName]?.months === 1 ? 'Month' : 'Months' }} Access
                                </p>
                            </div>

                            <ul class="space-y-3 mb-6">
                                <li v-for="(feature, idx) in details.features" :key="idx" class="flex items-start text-sm text-gray-300">
                                    <svg :class="`w-5 h-5 mr-2 flex-shrink-0 mt-0.5 ${tierName === 'basic' ? 'text-blue-400' : tierName === 'standard' ? 'text-purple-400' : 'text-amber-400'}`" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ feature }}
                                </li>
                            </ul>

                            <div v-if="form.tier === tierName" class="flex items-center justify-center">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-[0_0_20px_rgba(59,130,246,0.5)]">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between">
                    <button 
                        @click="prevStep"
                        class="px-8 py-3 rounded-full bg-white/5 text-gray-300 border border-white/10 hover:bg-white/10 hover:text-white font-semibold text-sm transition-all duration-300"
                    >
                        ← Back
                    </button>
                    <button 
                        @click="nextStep"
                        :disabled="!canProceedStep2"
                        :class="[
                            'px-8 py-3 rounded-full font-semibold text-sm transition-all duration-300',
                            canProceedStep2 
                                ? 'bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white shadow-[0_0_20px_rgba(59,130,246,0.4)] hover:shadow-[0_0_30px_rgba(59,130,246,0.6)] active:scale-95' 
                                : 'bg-gray-700/50 text-gray-500 cursor-not-allowed'
                        ]"
                    >
                        Continue to Business Details →
                    </button>
                </div>
            </div>

            <!-- Step 3: Business Details -->
            <div v-if="currentStep === 3" class="space-y-6">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-white mb-2">Complete Your Business Profile</h2>
                    <p class="text-gray-400">Provide your business information to finalize registration</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Form Column -->
                    <div class="lg:col-span-2">
                        <div class="rounded-3xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-8 shadow-lg space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">
                                    Business Name <span class="text-red-400">*</span>
                                </label>
                                <input 
                                    v-model="form.business_name" 
                                    type="text" 
                                    placeholder="e.g., Tindahan ni Juan"
                                    class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:bg-white/10 focus:border-blue-500/50 focus:ring-2 focus:ring-blue-500/20 transition-all" 
                                />
                                <div v-if="form.errors.business_name" class="text-red-400 text-sm mt-2">{{ form.errors.business_name }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">
                                    Business Description <span class="text-red-400">*</span>
                                </label>
                                <textarea 
                                    v-model="form.description" 
                                    rows="4"
                                    placeholder="Describe your business, products, or services..."
                                    class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:bg-white/10 focus:border-blue-500/50 focus:ring-2 focus:ring-blue-500/20 transition-all resize-none"
                                ></textarea>
                                <div v-if="form.errors.description" class="text-red-400 text-sm mt-2">{{ form.errors.description }}</div>
                                <p class="text-xs text-gray-500 mt-2">Tell customers what makes your business unique</p>
                            </div>

                            <div class="pt-4 border-t border-white/10">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-sm font-semibold text-gray-300">Selected Configuration</h4>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                                        <p class="text-xs text-gray-400 mb-1">Category</p>
                                        <p class="text-sm font-semibold text-white">
                                            {{ form.category === 'retail' ? '🛍️ Retail & Resale' : '🏪 Service & Hospitality' }}
                                        </p>
                                    </div>
                                    <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                                        <p class="text-xs text-gray-400 mb-1">Subscription Tier</p>
                                        <p class="text-sm font-semibold text-white">
                                            {{ selectedTierDetails.icon }} {{ selectedTierDetails.name }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between pt-4">
                                <button 
                                    type="button"
                                    @click="prevStep"
                                    class="px-8 py-3 rounded-full bg-white/5 text-gray-300 border border-white/10 hover:bg-white/10 hover:text-white font-semibold text-sm transition-all duration-300"
                                >
                                    ← Back
                                </button>
                                <button 
                                    @click="submit"
                                    :disabled="form.processing || !canSubmit"
                                    :class="[
                                        'px-8 py-3 rounded-full font-semibold text-sm transition-all duration-300',
                                        (!form.processing && canSubmit)
                                            ? 'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white shadow-[0_0_20px_rgba(34,197,94,0.4)] hover:shadow-[0_0_30px_rgba(34,197,94,0.6)] active:scale-95' 
                                            : 'bg-gray-700/50 text-gray-500 cursor-not-allowed'
                                    ]"
                                >
                                    {{ form.processing ? 'Processing...' : `Pay & Register (₱${props.plans[form.tier]?.price || 0})` }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Column -->
                    <div class="space-y-6">
                        <!-- Tier Summary -->
                        <div class="rounded-3xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 overflow-hidden shadow-lg">
                            <div :class="`p-6 bg-gradient-to-br ${selectedTierDetails.gradient}`">
                                <div class="text-center">
                                    <div class="text-5xl mb-3">{{ selectedTierDetails.icon }}</div>
                                    <h3 class="text-xl font-bold text-white mb-1">{{ selectedTierDetails.name }}</h3>
                                    <p class="text-sm text-gray-400">{{ selectedTierDetails.tagline }}</p>
                                </div>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="flex justify-between items-center pb-4 border-b border-white/10">
                                    <span class="text-gray-400">Subscription</span>
                                    <span class="text-white font-bold">₱{{ props.plans[form.tier]?.price || 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center pb-4 border-b border-white/10">
                                    <span class="text-gray-400">Duration</span>
                                    <span class="text-white font-semibold">{{ props.plans[form.tier]?.months || 1 }} {{ props.plans[form.tier]?.months === 1 ? 'Month' : 'Months' }}</span>
                                </div>
                                <div class="flex justify-between items-center text-lg font-bold">
                                    <span class="text-white">Total</span>
                                    <span class="text-green-400">₱{{ props.plans[form.tier]?.price || 0 }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Features Reminder -->
                        <div class="rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-6 shadow-lg">
                            <h4 class="text-sm font-semibold text-white mb-3">What You'll Get</h4>
                            <ul class="space-y-2">
                                <li v-for="(feature, idx) in selectedTierDetails.features.slice(0, 4)" :key="idx" class="flex items-start text-xs text-gray-300">
                                    <svg class="w-4 h-4 text-blue-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ feature }}
                                </li>
                            </ul>
                        </div>

                        <!-- Trust Badge -->
                        <div class="rounded-2xl bg-gradient-to-br from-blue-500/10 to-purple-500/10 border border-white/10 p-6 text-center shadow-lg">
                            <svg class="w-12 h-12 text-blue-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <p class="text-sm text-gray-300 font-semibold mb-1">Verified & Secure</p>
                            <p class="text-xs text-gray-400">Payment processed securely</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

* {
  transition-timing-function: cubic-bezier(0.16, 1, 0.3, 1);
}
</style>