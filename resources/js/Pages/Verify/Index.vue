<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const trackingCode = ref('');
const isSearching  = ref(false);

const search = () => {
    if (!trackingCode.value.trim()) return;
    isSearching.value = true;
    router.get('/verify', { code: trackingCode.value.trim() });
};
</script>

<template>
    <Head title="Verify Document — E-PILI" />

    <div class="min-h-screen bg-slate-950 flex items-center justify-center p-4">
        <!-- Background -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/8 rounded-full blur-3xl"></div>
        </div>

        <div class="relative w-full max-w-lg">
            <!-- Logo / Header -->
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-blue-600/20 border border-blue-500/30 mb-6">
                    <span class="text-4xl">🔗</span>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Document Verification</h1>
                <p class="text-slate-400 text-sm">
                    Verify the authenticity of any E-PILI document<br>using blockchain technology
                </p>
            </div>

            <!-- Search Card -->
            <div class="bg-slate-900/80 backdrop-blur border border-slate-700/50 rounded-2xl p-8">
                <label class="block text-sm font-semibold text-slate-300 mb-3">
                    Tracking Code
                </label>
                <input
                    v-model="trackingCode"
                    @keyup.enter="search"
                    type="text"
                    placeholder="e.g. BRGY-20260302-A1B2"
                    class="w-full px-4 py-3 bg-slate-800 border border-slate-600 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500/50 transition-all text-sm font-mono mb-4"
                    autofocus
                />
                <button
                    @click="search"
                    :disabled="!trackingCode.trim() || isSearching"
                    class="w-full py-3 bg-blue-600 hover:bg-blue-500 disabled:bg-slate-700 disabled:text-slate-500 text-white font-bold rounded-xl transition-all flex items-center justify-center gap-2"
                >
                    <svg v-if="isSearching" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span>{{ isSearching ? 'Verifying...' : 'Verify Document' }}</span>
                </button>

                <!-- How it works -->
                <div class="mt-6 pt-6 border-t border-slate-700/50">
                    <p class="text-xs text-slate-500 text-center mb-4">How it works</p>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-2xl mb-1">📄</div>
                            <p class="text-xs text-slate-400">Enter tracking code from your document</p>
                        </div>
                        <div>
                            <div class="text-2xl mb-1">🔗</div>
                            <p class="text-xs text-slate-400">We check the Polygon blockchain</p>
                        </div>
                        <div>
                            <div class="text-2xl mb-1">✅</div>
                            <p class="text-xs text-slate-400">Instant authenticity result</p>
                        </div>
                    </div>
                </div>
            </div>

            <p class="text-center text-xs text-slate-600 mt-6">
                Powered by Polygon blockchain · E-PILI Digital Governance System
            </p>
        </div>
    </div>
</template>