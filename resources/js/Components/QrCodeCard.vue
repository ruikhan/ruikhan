<script setup>
import QRCode from 'qrcode';
import { onMounted, ref, watch } from 'vue';

const props = defineProps({
    trackingCode: {
        type: String,
        required: true,
    },
    status: {
        type: String,
        default: 'pending',
    },
    documentType: {
        type: String,
        default: '',
    },
    // compact = small inline badge, full = large card
    size: {
        type: String,
        default: 'full', // 'full' | 'compact'
    },
});

const qrDataUrl   = ref('');
const isGenerating = ref(false);
const hasError     = ref(false);

// The URL the QR code will point to
const verifyUrl = `${window.location.origin}/verify?code=${props.trackingCode}`;

const generateQr = async () => {
    if (!props.trackingCode) return;

    try {
        isGenerating.value = true;
        hasError.value     = false;

        qrDataUrl.value = await QRCode.toDataURL(verifyUrl, {
            width:            props.size === 'compact' ? 120 : 220,
            margin:           2,
            color: {
                dark:  '#0f172a',  // slate-950
                light: '#f8fafc',  // slate-50
            },
            errorCorrectionLevel: 'H', // High — survives 30% damage, good for print
        });
    } catch (err) {
        console.error('[QrCodeCard] Generation failed:', err);
        hasError.value = true;
    } finally {
        isGenerating.value = false;
    }
};

// Download QR as PNG
const downloadQr = () => {
    if (!qrDataUrl.value) return;
    const link      = document.createElement('a');
    link.href       = qrDataUrl.value;
    link.download   = `qr-${props.trackingCode}.png`;
    link.click();
};

// Print document with QR
const printDocument = () => {
    window.print();
};

onMounted(generateQr);
watch(() => props.trackingCode, generateQr);
</script>

<template>
    <!-- ── COMPACT MODE: small inline badge ── -->
    <div v-if="size === 'compact'" class="inline-flex items-center gap-3 p-3 bg-slate-900/80 border border-slate-700/50 rounded-xl">
        <div v-if="isGenerating" class="w-[60px] h-[60px] flex items-center justify-center">
            <div class="w-5 h-5 border-2 border-slate-600 border-t-blue-500 rounded-full animate-spin"></div>
        </div>
        <img v-else-if="qrDataUrl" :src="qrDataUrl" :alt="`QR for ${trackingCode}`"
            class="w-[60px] h-[60px] rounded-lg" />
        <div>
            <p class="text-xs font-bold text-white">{{ trackingCode }}</p>
            <p class="text-[10px] text-slate-400">Scan to verify</p>
        </div>
    </div>

    <!-- ── FULL MODE: complete card ── -->
    <div v-else class="qr-card print:shadow-none print:border-slate-300">

        <!-- Only show for completed/approved docs -->
        <div v-if="status !== 'completed'" class="text-center py-8">
            <div class="text-4xl mb-3 opacity-30">🔗</div>
            <p class="text-slate-500 text-sm">QR code available after document approval</p>
            <p class="text-slate-600 text-xs mt-1">Current status: <span class="capitalize">{{ status }}</span></p>
        </div>

        <template v-else>
            <!-- Header -->
            <div class="flex items-center gap-3 mb-6 print:mb-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-cyan-600 flex items-center justify-center text-xl shadow-lg shadow-blue-500/30">
                    🔗
                </div>
                <div>
                    <h3 class="text-white font-bold text-sm">Blockchain Verification QR</h3>
                    <p class="text-slate-400 text-xs">Scan to verify authenticity on Polygon</p>
                </div>
            </div>

            <!-- QR Code -->
            <div class="flex justify-center mb-6 print:mb-4">
                <div class="relative">
                    <!-- Glow behind QR -->
                    <div class="absolute inset-0 bg-blue-500/20 rounded-2xl blur-xl print:hidden"></div>

                    <!-- Loading -->
                    <div v-if="isGenerating"
                        class="relative w-[220px] h-[220px] bg-slate-800 rounded-2xl border border-slate-700 flex items-center justify-center">
                        <div class="w-8 h-8 border-2 border-slate-600 border-t-blue-500 rounded-full animate-spin"></div>
                    </div>

                    <!-- Error -->
                    <div v-else-if="hasError"
                        class="relative w-[220px] h-[220px] bg-slate-800 rounded-2xl border border-red-500/30 flex flex-col items-center justify-center gap-2 p-4 text-center">
                        <span class="text-2xl">⚠️</span>
                        <p class="text-red-400 text-xs">Failed to generate QR</p>
                        <button @click="generateQr"
                            class="px-3 py-1 bg-red-600/20 border border-red-500/30 rounded-lg text-red-400 text-xs hover:bg-red-600/30 transition-colors">
                            Retry
                        </button>
                    </div>

                    <!-- QR Image -->
                    <img v-else
                        :src="qrDataUrl"
                        :alt="`Verification QR for ${trackingCode}`"
                        class="relative w-[220px] h-[220px] rounded-2xl border-4 border-white shadow-2xl print:border-2 print:shadow-none" />
                </div>
            </div>

            <!-- Document Info -->
            <div class="bg-slate-900/60 rounded-xl p-4 mb-5 print:bg-slate-100 print:text-slate-900 space-y-2">
                <div class="flex justify-between items-center py-1.5 border-b border-slate-800/50 print:border-slate-300">
                    <span class="text-xs text-slate-400 print:text-slate-600">Tracking Code</span>
                    <span class="text-xs font-mono font-bold text-white print:text-slate-900">{{ trackingCode }}</span>
                </div>
                <div v-if="documentType" class="flex justify-between items-center py-1.5 border-b border-slate-800/50 print:border-slate-300">
                    <span class="text-xs text-slate-400 print:text-slate-600">Document</span>
                    <span class="text-xs text-slate-200 print:text-slate-700">{{ documentType }}</span>
                </div>
                <div class="flex justify-between items-center py-1.5">
                    <span class="text-xs text-slate-400 print:text-slate-600">Verify at</span>
                    <span class="text-xs font-mono text-blue-400 print:text-blue-700 truncate max-w-[180px]">
                        {{ verifyUrl }}
                    </span>
                </div>
            </div>

            <!-- Actions (hidden on print) -->
            <div class="flex gap-3 print:hidden">
                <button
                    @click="downloadQr"
                    :disabled="!qrDataUrl || isGenerating"
                    class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-blue-600/20 hover:bg-blue-600/30 border border-blue-500/30 rounded-xl text-blue-400 hover:text-blue-300 text-sm font-semibold transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download QR
                </button>

                <button
                    @click="printDocument"
                    class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/30 rounded-xl text-emerald-400 hover:text-emerald-300 text-sm font-semibold transition-all"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Document
                </button>
            </div>

            <!-- Blockchain Badge -->
            <div class="mt-4 flex items-center justify-center gap-2 print:mt-2">
                <div class="h-px flex-1 bg-gradient-to-r from-transparent to-slate-700/50 print:bg-slate-300"></div>
                <span class="text-[10px] text-slate-500 px-2 flex items-center gap-1 print:text-slate-500">
                    <span>🔗</span> Secured by Polygon Blockchain
                </span>
                <div class="h-px flex-1 bg-gradient-to-l from-transparent to-slate-700/50 print:bg-slate-300"></div>
            </div>
        </template>
    </div>
</template>

<style scoped>
.qr-card {
    position: relative;
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 1.5rem;
    padding: 1.5rem;
    box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.6);
}

/* Print styles */
@media print {
    .qr-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 1rem;
        box-shadow: none;
    }
}
</style>