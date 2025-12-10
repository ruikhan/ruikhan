<script setup>
import { Head } from '@inertiajs/vue3';

// Receive the document details and the SVG QR code from Laravel
defineProps({ 
    document: Object,
    qrCode: String 
});
</script>

<template>
    <Head title="Verify Document" />

    <div class="min-h-screen bg-slate-100 flex items-center justify-center p-4 font-sans">
        
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden border-4 border-double border-slate-200 relative">
            
            <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
                <div class="text-9xl font-serif font-bold text-slate-900 rotate-45">OFFICIAL</div>
            </div>

            <div class="bg-slate-900 p-6 text-center text-white relative z-10">
                <div class="inline-block p-3 rounded-full bg-white/10 mb-2 shadow-lg">
                    <span class="text-3xl">🏛️</span>
                </div>
                <h1 class="text-xl font-bold tracking-widest uppercase">Official Document</h1>
                <p class="text-blue-200 text-xs uppercase tracking-widest">Digital Governance Verification</p>
            </div>

            <div class="p-8 text-center space-y-6 relative z-10">
                
                <div class="inline-flex items-center gap-2 px-4 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    Authentic & Valid
                </div>

                <div>
                    <h2 class="text-xs text-slate-400 uppercase font-bold tracking-wide">Document Type</h2>
                    <p class="text-2xl font-bold text-slate-800">{{ document.document_type }}</p>
                </div>

                <div>
                    <h2 class="text-xs text-slate-400 uppercase font-bold tracking-wide">Issued To</h2>
                    <p class="text-xl font-semibold text-slate-800">{{ document.user.name }}</p>
                    <p class="text-xs text-slate-500">{{ document.user.email }}</p>
                </div>

                <div>
                    <h2 class="text-xs text-slate-400 uppercase font-bold tracking-wide">Purpose</h2>
                    <p class="text-sm text-slate-600 italic bg-slate-50 p-2 rounded-lg inline-block">"{{ document.purpose }}"</p>
                </div>

                <div class="border-t border-dashed border-slate-300 pt-6">
                    <div class="flex justify-center">
                        <div v-html="qrCode" class="bg-white p-2 rounded-lg shadow-inner border border-slate-100"></div>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-2 uppercase tracking-wide">Scan to verify authenticity</p>
                </div>

                <div class="text-xs text-slate-400 pt-4">
                    Issued on: {{ new Date(document.updated_at).toLocaleDateString() }}
                </div>
            </div>

            <div class="bg-slate-50 p-3 text-center border-t border-slate-200">
                <p class="text-[10px] text-slate-400 font-mono">
                    System ID: {{ document.id }}-{{ new Date().getTime() }}
                </p>
            </div>
        </div>

    </div>
</template>