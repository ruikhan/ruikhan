<script setup>
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    status:       String,
    trackingCode: String,
    documentType: String,
    department:   String,
    issuedTo:     String,
    anchoredAt:   String,
    txHash:       String,
    network:      String,
    explorerUrl:  String,
    docStatus:    String,
    chainData:    Object,
});

const statusConfig = computed(() => {
    const configs = {
        authentic: {
            icon:       '✅',
            title:      'Document Authentic',
            subtitle:   'This document is verified on the Polygon blockchain',
            badgeText:  'VERIFIED',
            badgeClass: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/40',
            cardClass:  'border-emerald-500/30 bg-emerald-950/20',
            glowClass:  'bg-emerald-500/10',
            titleClass: 'text-emerald-400',
        },
        tampered: {
            icon:       '⚠️',
            title:      'Document Tampered',
            subtitle:   'This document has been modified after official issuance',
            badgeText:  'INVALID',
            badgeClass: 'bg-red-500/20 text-red-400 border-red-500/40',
            cardClass:  'border-red-500/30 bg-red-950/20',
            glowClass:  'bg-red-500/10',
            titleClass: 'text-red-400',
        },
        revoked: {
            icon:       '🚫',
            title:      'Document Revoked',
            subtitle:   'This document has been officially revoked by the issuing authority',
            badgeText:  'REVOKED',
            badgeClass: 'bg-orange-500/20 text-orange-400 border-orange-500/40',
            cardClass:  'border-orange-500/30 bg-orange-950/20',
            glowClass:  'bg-orange-500/10',
            titleClass: 'text-orange-400',
        },
        not_found: {
            icon:       '🔍',
            title:      'Document Not Found',
            subtitle:   'No document found with this tracking code',
            badgeText:  'NOT FOUND',
            badgeClass: 'bg-slate-500/20 text-slate-400 border-slate-500/40',
            cardClass:  'border-slate-500/30 bg-slate-900/50',
            glowClass:  'bg-slate-500/10',
            titleClass: 'text-slate-400',
        },
        not_anchored: {
            icon:       '⏳',
            title:      'Not Yet on Blockchain',
            subtitle:   'This document exists but has not been anchored on the blockchain yet',
            badgeText:  'PENDING',
            badgeClass: 'bg-amber-500/20 text-amber-400 border-amber-500/40',
            cardClass:  'border-amber-500/30 bg-amber-950/20',
            glowClass:  'bg-amber-500/10',
            titleClass: 'text-amber-400',
        },
        error: {
            icon:       '❌',
            title:      'Verification Error',
            subtitle:   'Unable to connect to the blockchain. Please try again later.',
            badgeText:  'ERROR',
            badgeClass: 'bg-red-500/20 text-red-400 border-red-500/40',
            cardClass:  'border-red-500/30 bg-slate-900/50',
            glowClass:  'bg-red-500/10',
            titleClass: 'text-red-400',
        },
    };
    return configs[props.status] ?? configs.error;
});

const formattedDate = computed(() => {
    if (!props.anchoredAt) return null;
    return new Date(props.anchoredAt).toLocaleString('en-PH', {
        year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit', timeZoneName: 'short'
    });
});

const shortTxHash = computed(() => {
    if (!props.txHash) return null;
    return props.txHash.slice(0, 10) + '...' + props.txHash.slice(-8);
});
</script>

<template>
    <Head :title="`Verify ${trackingCode} — E-PILI`" />

    <div class="min-h-screen bg-slate-950 flex items-center justify-center p-4">

        <!-- Background glow -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div
                :class="[
                    'absolute top-1/3 left-1/2 -translate-x-1/2 w-[600px] h-[400px] rounded-full blur-3xl opacity-40 transition-all duration-1000',
                    statusConfig.glowClass
                ]"
            ></div>
        </div>

        <div class="relative w-full max-w-xl">

            <!-- Back button -->
            <button
                @click="router.get('/verify')"
                class="flex items-center gap-2 text-slate-400 hover:text-white text-sm mb-8 transition-colors group"
            >
                <svg
                    class="w-4 h-4 group-hover:-translate-x-1 transition-transform"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Verify another document
            </button>

            <!-- Main result card -->
            <div :class="['rounded-2xl border p-8 backdrop-blur transition-all', statusConfig.cardClass]">

                <!-- Status Header -->
                <div class="text-center mb-8">
                    <div class="text-7xl mb-4 animate-bounce-once">{{ statusConfig.icon }}</div>
                    <div
                        :class="[
                            'inline-flex items-center gap-2 px-4 py-1.5 rounded-full border text-xs font-bold tracking-widest mb-4',
                            statusConfig.badgeClass
                        ]"
                    >
                        {{ statusConfig.badgeText }}
                    </div>
                    <h1 :class="['text-2xl font-bold mb-2', statusConfig.titleClass]">
                        {{ statusConfig.title }}
                    </h1>
                    <p class="text-slate-400 text-sm">{{ statusConfig.subtitle }}</p>
                </div>

                <!-- Document Details -->
                <div class="bg-slate-900/60 rounded-xl p-5 mb-6 space-y-3">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4">
                        Document Details
                    </h3>

                    <div class="flex justify-between items-center py-2 border-b border-slate-800/50">
                        <span class="text-sm text-slate-400">Tracking Code</span>
                        <span class="text-sm font-mono font-bold text-white">{{ trackingCode }}</span>
                    </div>

                    <div
                        v-if="documentType"
                        class="flex justify-between items-center py-2 border-b border-slate-800/50"
                    >
                        <span class="text-sm text-slate-400">Document Type</span>
                        <span class="text-sm text-slate-200">{{ documentType }}</span>
                    </div>

                    <div
                        v-if="department"
                        class="flex justify-between items-center py-2 border-b border-slate-800/50"
                    >
                        <span class="text-sm text-slate-400">Department</span>
                        <span class="text-sm text-slate-200">{{ department }}</span>
                    </div>

                    <div
                        v-if="issuedTo"
                        class="flex justify-between items-center py-2 border-b border-slate-800/50"
                    >
                        <span class="text-sm text-slate-400">Issued To</span>
                        <span class="text-sm text-slate-200">{{ issuedTo }}</span>
                    </div>

                    <div v-if="formattedDate" class="flex justify-between items-center py-2">
                        <span class="text-sm text-slate-400">Anchored On</span>
                        <span class="text-sm text-slate-200">{{ formattedDate }}</span>
                    </div>
                </div>

                <!-- Blockchain Proof — only for anchored docs -->
                <div v-if="txHash" class="bg-slate-900/60 rounded-xl p-5 mb-6">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4">
                        Blockchain Proof
                    </h3>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-slate-800/50">
                            <span class="text-sm text-slate-400">Network</span>
                            <span class="text-sm font-mono text-purple-400">
                                {{ network === 'polygon' ? 'Polygon Mainnet' : 'Polygon Amoy Testnet' }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center py-2 border-b border-slate-800/50">
                            <span class="text-sm text-slate-400">TX Hash</span>
                            <a
                                :href="explorerUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-sm font-mono text-blue-400 hover:text-blue-300 underline underline-offset-2 transition-colors"
                            >
                                {{ shortTxHash }}
                            </a>
                        </div>

                        <div class="pt-2">
                            <a
                                :href="explorerUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex items-center justify-center gap-2 w-full py-2.5 bg-blue-600/20 hover:bg-blue-600/30 border border-blue-500/30 rounded-xl text-blue-400 hover:text-blue-300 text-sm font-semibold transition-all"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                                    />
                                </svg>
                                View on PolygonScan
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Warning: Tampered -->
                <div
                    v-if="status === 'tampered'"
                    class="bg-red-950/40 border border-red-500/30 rounded-xl p-4 mb-6"
                >
                    <p class="text-red-300 text-sm font-semibold mb-1">⚠️ Warning</p>
                    <p class="text-red-400/80 text-xs leading-relaxed">
                        The contents of this document do not match what was recorded on the blockchain
                        at the time of issuance. This document may have been forged or altered.
                        Do not accept this document as valid.
                    </p>
                </div>

                <!-- Warning: Not Anchored -->
                <div
                    v-if="status === 'not_anchored'"
                    class="bg-amber-950/40 border border-amber-500/30 rounded-xl p-4 mb-6"
                >
                    <p class="text-amber-300 text-sm font-semibold mb-1">ℹ️ Note</p>
                    <p class="text-amber-400/80 text-xs leading-relaxed">
                        This document is currently <strong>{{ docStatus }}</strong> and has not yet
                        been approved and anchored on the blockchain. Blockchain verification is only
                        available for fully approved documents.
                    </p>
                </div>

                <!-- Footer -->
                <div class="text-center pt-4 border-t border-slate-800/50">
                    <p class="text-xs text-slate-600">
                        Verified by E-PILI Digital Governance System<br />
                        Powered by Polygon Blockchain Technology
                    </p>
                </div>

            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes bounce-once {
    0%, 100% { transform: translateY(0); }
    30%       { transform: translateY(-20px); }
    60%       { transform: translateY(-10px); }
}
.animate-bounce-once {
    animation: bounce-once 0.8s ease-out;
}
</style>