<script setup>
import QrCodeCard from '@/Components/QrCodeCard.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    ArrowUpTrayIcon,
    BellAlertIcon,
    BuildingLibraryIcon,
    DocumentCheckIcon,
    ShieldCheckIcon,
    UserIcon
} from '@heroicons/vue/24/solid';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    docRequest: Object,
});

const isCompleted = computed(() => props.docRequest.status === 'completed');

// Map your database status to the 6-Step Storyboard
const getActiveStep = (status) => {
    switch(status) {
        case 'created':         return 3;
        case 'pending':         return 4;
        case 'processing':      return 5;
        case 'ready_for_pickup':return 6;
        case 'completed':       return 7;
        default:                return 1;
    }
};

const activeStep = getActiveStep(props.docRequest.status);

// The 6-Step Storyboard Data
const storyboard = [
    {
        scene: 1,
        title: "The Entry",
        subtitle: "Resident Portal Access",
        desc: "Secure login authenticated via resident database.",
        icon: UserIcon,
        align: 'left'
    },
    {
        scene: 2,
        title: "The Selection",
        subtitle: "Document Specification",
        desc: "User identified need for: " + props.docRequest.document_type,
        icon: DocumentCheckIcon,
        align: 'right'
    },
    {
        scene: 3,
        title: "The Evidence",
        subtitle: "Attachment Upload",
        desc: "Required supporting documents were securely uploaded to the server.",
        icon: ArrowUpTrayIcon,
        align: 'left'
    },
    {
        scene: 4,
        title: "The Validation",
        subtitle: "Barangay Staff Review",
        desc: "Local officials are currently validating identity and residency records.",
        icon: ShieldCheckIcon,
        align: 'right'
    },
    {
        scene: 5,
        title: "The Official Seal",
        subtitle: "LGU Admin Processing",
        desc: "Approved request forwarded to City Hall for printing and dry sealing.",
        icon: BuildingLibraryIcon,
        align: 'left'
    },
    {
        scene: 6,
        title: "The Resolution",
        subtitle: "Notification & Release",
        desc: "SMS/Email alert sent. Document ready for claiming.",
        icon: BellAlertIcon,
        align: 'right'
    }
];
</script>

<template>
    <Head title="Request Storyboard" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-slate-950 text-white relative overflow-hidden font-sans">
            
            <div class="fixed inset-0 pointer-events-none">
                <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-900/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-900/10 rounded-full blur-3xl"></div>
                <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
            </div>

            <div class="max-w-4xl mx-auto px-6 py-12 relative z-10">
                
                <!-- Header -->
                <div class="text-center mb-16">
                    <Link :href="route('dashboard')" class="text-xs font-bold text-slate-500 uppercase hover:text-white transition mb-4 inline-block tracking-widest">
                        ← Exit Storyboard
                    </Link>
                    <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4 bg-gradient-to-r from-blue-200 to-white bg-clip-text text-transparent">
                        The Journey of Request #{{ docRequest.tracking_code }}
                    </h1>
                    <p class="text-slate-400 max-w-lg mx-auto">
                        A step-by-step visualization of your document's path through the Digital Governance System.
                    </p>
                </div>

                <!-- ✅ QR Code Card — shown prominently when completed -->
                <Transition
                    enter-active-class="transition duration-700 ease-out"
                    enter-from-class="opacity-0 translate-y-6 scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                >
                    <div v-if="isCompleted" class="mb-16">
                        <!-- Glow header -->
                        <div class="text-center mb-6">
                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500/10 border border-emerald-500/30 rounded-full text-emerald-400 text-xs font-bold tracking-widest">
                                <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                                DOCUMENT VERIFIED & ANCHORED ON BLOCKCHAIN
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- QR Code -->
                            <QrCodeCard
                                :tracking-code="docRequest.tracking_code"
                                :status="docRequest.status"
                                :document-type="docRequest.document_type"
                                size="full"
                            />

                            <!-- Document Summary -->
                            <div class="relative overflow-hidden bg-slate-900/60 backdrop-blur border border-slate-700/50 rounded-2xl p-6 flex flex-col justify-between">
                                <div class="absolute top-0 right-0 w-48 h-48 bg-emerald-500/5 rounded-full blur-3xl"></div>

                                <div>
                                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-5">Document Summary</h3>

                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center py-2 border-b border-slate-800/50">
                                            <span class="text-sm text-slate-400">Type</span>
                                            <span class="text-sm font-semibold text-white">{{ docRequest.document_type }}</span>
                                        </div>
                                        <div class="flex justify-between items-center py-2 border-b border-slate-800/50">
                                            <span class="text-sm text-slate-400">Department</span>
                                            <span class="text-sm text-slate-200">{{ docRequest.department }}</span>
                                        </div>
                                        <div class="flex justify-between items-center py-2 border-b border-slate-800/50">
                                            <span class="text-sm text-slate-400">Status</span>
                                            <span class="px-2 py-1 bg-emerald-500/15 border border-emerald-500/30 rounded-lg text-xs font-bold text-emerald-400 uppercase">
                                                ✓ Completed
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center py-2 border-b border-slate-800/50">
                                            <span class="text-sm text-slate-400">Tracking #</span>
                                            <span class="text-sm font-mono text-blue-400">{{ docRequest.tracking_code }}</span>
                                        </div>
                                        <div v-if="docRequest.appointment_date" class="flex justify-between items-center py-2">
                                            <span class="text-sm text-slate-400">Pickup Date</span>
                                            <span class="text-sm text-slate-200">
                                                {{ new Date(docRequest.appointment_date).toLocaleDateString('en-PH', { month: 'long', day: 'numeric', year: 'numeric' }) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Verification Link -->
                                <div class="mt-6 pt-4 border-t border-slate-800/50">
                                    <a
                                        :href="`/verify?code=${docRequest.tracking_code}`"
                                        target="_blank"
                                        class="flex items-center justify-center gap-2 w-full py-2.5 bg-gradient-to-r from-blue-600/20 to-cyan-600/20 hover:from-blue-600/30 hover:to-cyan-600/30 border border-blue-500/30 rounded-xl text-blue-400 hover:text-blue-300 text-sm font-semibold transition-all"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                        Open Verification Page
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </Transition>

                <!-- Storyboard Timeline -->
                <div class="relative">
                    
                    <div class="absolute left-4 md:left-1/2 top-0 bottom-0 w-0.5 bg-gradient-to-b from-blue-500 via-purple-500 to-slate-800 opacity-30 md:-translate-x-1/2"></div>

                    <div v-for="(step, index) in storyboard" :key="step.scene" 
                         class="mb-12 relative flex items-center md:justify-between group"
                         :class="{ 
                             'flex-row-reverse': step.align === 'left',
                             'opacity-50 blur-[1px] grayscale': step.scene > activeStep,
                             'opacity-100 scale-105': step.scene === activeStep
                         }"
                    >
                        <div class="w-full md:w-5/12 pl-16 md:pl-0" :class="step.align === 'left' ? 'md:text-right md:pr-12' : 'md:text-left md:pl-12'">
                            <div class="p-6 rounded-2xl border transition-all duration-500 relative overflow-hidden"
                                 :class="step.scene === activeStep 
                                    ? 'bg-slate-800/80 border-blue-400/50 shadow-2xl shadow-blue-500/10' 
                                    : 'bg-slate-900/50 border-white/5'">
                                
                                <div v-if="step.scene === activeStep" class="absolute top-0 left-0 w-1 h-full bg-blue-400"></div>

                                <span class="text-[10px] font-black uppercase tracking-widest mb-2 block"
                                      :class="step.scene === activeStep ? 'text-blue-400' : 'text-slate-600'">
                                    Scene 0{{ step.scene }}
                                </span>
                                
                                <h3 class="text-xl font-bold text-white mb-1">{{ step.title }}</h3>
                                <h4 class="text-sm font-bold text-blue-200 mb-3">{{ step.subtitle }}</h4>
                                <p class="text-sm text-slate-400 leading-relaxed">{{ step.desc }}</p>

                                <div v-if="step.scene <= activeStep" class="mt-4 pt-4 border-t border-white/5 flex items-center gap-2"
                                     :class="step.align === 'left' ? 'md:justify-end' : 'md:justify-start'">
                                    <div class="h-1.5 w-1.5 rounded-full bg-green-500"></div>
                                    <span class="text-xs font-mono text-slate-500">
                                        {{ step.scene === activeStep ? 'Action in Progress...' : 'Completed' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="absolute left-4 md:left-1/2 -translate-x-1/2 w-10 h-10 rounded-full border-4 flex items-center justify-center z-20 bg-slate-950 transition-all duration-500"
                             :class="step.scene <= activeStep ? 'border-blue-500 text-white shadow-[0_0_15px_rgba(59,130,246,0.5)]' : 'border-slate-800 text-slate-700'">
                            <component :is="step.icon" class="w-5 h-5" />
                        </div>

                        <div class="hidden md:block w-5/12"></div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-16 pb-12">
                    <div v-if="activeStep < 6" class="inline-flex flex-col items-center animate-bounce">
                        <span class="text-xs text-slate-500 uppercase tracking-widest mb-2">Story Continuing...</span>
                        <div class="w-px h-12 bg-gradient-to-b from-slate-500 to-transparent"></div>
                    </div>
                    <div v-else class="p-6 bg-green-900/20 border border-green-500/30 rounded-xl max-w-md mx-auto">
                        <h3 class="text-green-400 font-bold uppercase tracking-widest text-sm mb-2">Story Complete</h3>
                        <p class="text-white">This document request has reached its conclusion. Scan the QR code above to verify authenticity.</p>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>