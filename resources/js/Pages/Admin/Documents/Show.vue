<script setup>
import QrCodeCard from '@/Components/QrCodeCard.vue';
import RequestTracker from '@/Components/RequestTracker.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({ 
    docRequest: {
        type: Object,
        default: () => ({})
    }, 
    user: {
        type: Object,
        default: () => ({})
    }
});

// Check if required data exists
const hasValidData = computed(() => {
    return props.docRequest && Object.keys(props.docRequest).length > 0 && 
           props.user && Object.keys(props.user).length > 0;
});

const isCompleted = computed(() => props.docRequest?.status === 'completed');

// Initialize form with current values (with safe defaults)
const form = useForm({
    status: props.docRequest?.status || 'pending',
    admin_remarks: props.docRequest?.admin_remarks || '',
    appointment_date: props.docRequest?.appointment_date || ''
});

const isSubmitting = ref(false);

// Character counter for admin remarks
const remainingChars = computed(() => {
    return 1000 - (form.admin_remarks?.length || 0);
});

// Minimum datetime for appointment (now)
const minDateTime = computed(() => {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
});

// Parse attachments (handle both array and JSON string)
const attachmentsList = computed(() => {
    if (!props.docRequest?.attachments) return [];
    if (Array.isArray(props.docRequest.attachments)) return props.docRequest.attachments;
    try {
        return JSON.parse(props.docRequest.attachments);
    } catch {
        return [props.docRequest.attachments];
    }
});

const submitUpdate = () => {
    if (!hasValidData.value) {
        alert('❌ Error: Missing required data. Please refresh the page.');
        return;
    }
    if (isSubmitting.value || form.processing) return;
    if (form.status === 'rejected' && props.docRequest.status !== 'rejected') {
        if (!confirm('⚠️ Are you sure you want to REJECT this request? The user will be notified.')) return;
    }
    if (form.status === 'ready_for_pickup' && !form.appointment_date) {
        alert('⚠️ Please set a pickup appointment date.');
        return;
    }

    isSubmitting.value = true;
    form.patch(route('admin.documents.update', props.docRequest.id), {
        preserveScroll: true,
        onSuccess: () => {
            isSubmitting.value = false;
            alert('✅ Document updated successfully! Notification sent to user.');
        },
        onError: (errors) => {
            isSubmitting.value = false;
            const errorMessages = Object.values(errors).flat();
            alert(errorMessages.length > 0
                ? '❌ Update failed: ' + errorMessages.join(', ')
                : '❌ Failed to update document. Please try again.'
            );
        },
        onFinish: () => { isSubmitting.value = false; }
    });
};

const formatKey = (key) => {
    if (!key) return '';
    return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
};

const formatDateTime = (datetime) => {
    if (!datetime) return 'Not set';
    return new Date(datetime).toLocaleString('en-PH', {
        year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
};
</script>

<template>
    <Head :title="`Dossier #${docRequest?.tracking_code || 'Unknown'}`" />

    <AuthenticatedLayout>
        <!-- Error State -->
        <div v-if="!hasValidData" class="min-h-screen bg-slate-900 flex items-center justify-center p-6">
            <div class="max-w-md w-full bg-red-900/20 border-2 border-red-500/50 rounded-2xl p-8 text-center">
                <div class="text-6xl mb-4">⚠️</div>
                <h2 class="text-2xl font-bold text-red-400 mb-2">Data Not Found</h2>
                <p class="text-slate-300 mb-6">
                    Unable to load document request. The record may not exist or there was an error loading the data.
                </p>
                <Link 
                    :href="route('admin.documents.index')" 
                    class="inline-block px-6 py-3 bg-red-600 hover:bg-red-500 text-white font-bold rounded-xl transition-colors"
                >
                    ← Return to Dashboard
                </Link>
            </div>
        </div>

        <!-- Main Content -->
        <div v-else class="min-h-screen bg-slate-900 font-sans text-white p-6 lg:p-12 relative overflow-hidden">
            
            <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#64748b 1px, transparent 1px); background-size: 32px 32px;"></div>

            <!-- Header -->
            <div class="max-w-6xl mx-auto mb-8 relative z-10">
                <Link :href="route('admin.documents.index')" class="flex items-center gap-2 text-slate-400 hover:text-white transition-colors mb-4 text-sm font-bold uppercase tracking-wider group">
                    <span class="group-hover:-translate-x-1 transition-transform">←</span>
                    Back to Command Center
                </Link>
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-white tracking-tight flex items-center gap-3">
                            Request Dossier 
                            <span class="text-slate-600 font-mono">#{{ docRequest.tracking_code }}</span>
                        </h1>
                        <p class="text-blue-400 text-xs font-mono uppercase mt-1 tracking-widest">SECURE CONNECTION // OFFICIAL USE ONLY</p>
                    </div>
                    
                    <!-- ✅ Print Button — only for completed docs -->
                    <a
                        v-if="isCompleted"
                        :href="route('admin.documents.print', docRequest.id)"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-500/20 transition-all transform hover:scale-105 active:scale-95"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print / PDF
                    </a>

                    <div class="px-5 py-3 rounded-xl border border-white/10 bg-white/5 backdrop-blur-md shadow-lg">
                        <span class="text-[10px] text-slate-400 uppercase tracking-widest block mb-1">Current Status</span>
                        <span v-if="docRequest.status === 'pending'" class="text-yellow-400 font-bold uppercase flex items-center gap-2">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                            </span>
                            Pending Review
                        </span>
                        <span v-else-if="docRequest.status === 'processing'" class="text-blue-400 font-bold uppercase flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></span> Processing
                        </span>
                        <span v-else-if="docRequest.status === 'ready_for_pickup'" class="text-green-400 font-bold uppercase flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-400 rounded-full"></span> Ready for Pickup
                        </span>
                        <span v-else-if="docRequest.status === 'completed'" class="text-emerald-400 font-bold uppercase flex items-center gap-2">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full"></span> Completed
                        </span>
                        <span v-else class="text-red-400 font-bold uppercase flex items-center gap-2">
                            <span class="w-2 h-2 bg-red-400 rounded-full"></span> Rejected
                        </span>
                    </div>
                </div>
            </div>

            <!-- Body Grid -->
            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8 relative z-10">
                
                <!-- ── LEFT COLUMN ──────────────────────────────────── -->
                <div class="lg:col-span-1 space-y-6">

                    <!-- Applicant Identity -->
                    <div class="bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl p-6">
                        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-6 border-b border-white/5 pb-2">Applicant Identity</h3>
                        
                        <div class="flex flex-col items-center text-center mb-6">
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-3xl font-bold shadow-2xl shadow-indigo-500/20 mb-4 border-4 border-slate-800">
                                {{ user.name?.charAt(0) || '?' }}
                            </div>
                            <h2 class="text-xl font-bold text-white">{{ user.name || 'Unknown User' }}</h2>
                            <p class="text-sm text-slate-400">{{ user.email || 'No email' }}</p>
                        </div>

                        <div class="space-y-4">
                            <div class="p-3 bg-black/20 rounded-lg border border-white/5">
                                <label class="block text-[10px] text-slate-500 uppercase font-bold">Contact Number</label>
                                <div class="text-sm font-mono text-blue-200">{{ user.phone_number || 'N/A' }}</div>
                            </div>
                            <div class="p-3 bg-black/20 rounded-lg border border-white/5">
                                <label class="block text-[10px] text-slate-500 uppercase font-bold">Address</label>
                                <div class="text-sm text-slate-300">{{ user.address || 'Address not registered' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Attachments -->
                    <div class="bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl p-6">
                        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-4 border-b border-white/5 pb-2">Evidence / Attachments</h3>
                        
                        <div v-if="attachmentsList.length > 0" class="space-y-3">
                            <a 
                                v-for="(attachment, index) in attachmentsList" 
                                :key="index"
                                :href="`/storage/${attachment}`" 
                                target="_blank" 
                                class="flex items-center gap-3 p-3 rounded-lg bg-blue-600/10 border border-blue-500/20 hover:bg-blue-600/20 transition-all group cursor-pointer"
                            >
                                <div class="w-10 h-10 rounded bg-blue-500/20 flex items-center justify-center text-lg">📄</div>
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-sm font-bold text-blue-100 truncate">Attachment {{ index + 1 }}</p>
                                    <p class="text-[10px] text-blue-300 uppercase">Click to Open</p>
                                </div>
                                <svg class="w-4 h-4 text-blue-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                        <div v-else class="text-sm text-slate-500 italic text-center py-4">
                            No attachments provided.
                        </div>
                    </div>

                    <!-- ✅ QR Code Card — left column, visible when completed -->
                    <Transition
                        enter-active-class="transition duration-500 ease-out"
                        enter-from-class="opacity-0 translate-y-4 scale-95"
                        enter-to-class="opacity-100 translate-y-0 scale-100"
                    >
                        <div v-if="isCompleted">
                            <!-- Blockchain anchored indicator -->
                            <div v-if="docRequest.blockchain_tx_hash" class="mb-3 flex items-center gap-2 px-3 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                                <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse flex-shrink-0"></span>
                                <span class="text-xs text-emerald-400 font-semibold">Anchored on Polygon blockchain</span>
                            </div>
                            <div v-else class="mb-3 flex items-center gap-2 px-3 py-2 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                                <span class="w-2 h-2 bg-amber-400 rounded-full animate-pulse flex-shrink-0"></span>
                                <span class="text-xs text-amber-400 font-semibold">Blockchain anchoring in progress...</span>
                            </div>

                            <QrCodeCard
                                :tracking-code="docRequest.tracking_code"
                                :status="docRequest.status"
                                :document-type="docRequest.document_type"
                                size="full"
                            />
                        </div>
                    </Transition>

                </div>

                <!-- ── RIGHT COLUMN ─────────────────────────────────── -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Request Specification -->
                    <div class="bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl p-8 relative overflow-hidden group">
                        <div class="absolute -top-6 -right-6 p-6 opacity-[0.03] text-9xl pointer-events-none grayscale group-hover:grayscale-0 transition-all duration-1000">
                            {{ docRequest.icon || '📄' }}
                        </div>
                        
                        <div class="flex justify-between items-start border-b border-white/5 pb-4 mb-6">
                            <div>
                                <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest">Request Specification</h3>
                                <div class="text-2xl font-bold text-white mt-1">{{ docRequest.document_type || 'Unknown Document' }}</div>
                            </div>
                            <div class="text-right">
                                <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest">Department</h3>
                                <div class="text-sm font-bold text-blue-400 mt-1">{{ docRequest.department || 'N/A' }}</div>
                            </div>
                        </div>

                        <div class="bg-black/20 rounded-xl p-6 border border-white/5">
                            <label class="block text-[10px] text-slate-500 uppercase font-bold mb-4">Submitted Information</label>
                            <dl v-if="docRequest.data && Object.keys(docRequest.data).length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                                <div v-for="(value, key) in docRequest.data" :key="key" class="border-l-2 border-slate-700 pl-3">
                                    <dt class="text-[10px] uppercase tracking-wider text-slate-500 font-bold mb-1">{{ formatKey(key) }}</dt>
                                    <dd class="text-sm font-medium text-slate-200 break-words">{{ value || '—' }}</dd>
                                </div>
                            </dl>
                            <p v-else class="text-slate-500 text-sm italic text-center py-4">No additional data provided</p>
                        </div>

                        <div v-if="docRequest.user_remarks" class="mt-6 pt-4 border-t border-white/5">
                            <label class="block text-[10px] text-slate-500 uppercase font-bold mb-2">User Remarks</label>
                            <p class="text-sm text-slate-300 italic">"{{ docRequest.user_remarks }}"</p>
                        </div>

                        <div v-if="docRequest.updated_at" class="mt-4 text-xs text-slate-500">
                            Last updated: {{ formatDateTime(docRequest.updated_at) }}
                        </div>
                    </div>

                    <!-- Request Tracker -->
                    <RequestTracker 
                        :status="docRequest.status" 
                        :department="docRequest.department" 
                    />

                    <!-- ✅ Blockchain Info Panel — right column, only when anchored -->
                    <Transition
                        enter-active-class="transition duration-500 ease-out"
                        enter-from-class="opacity-0 translate-y-4"
                        enter-to-class="opacity-100 translate-y-0"
                    >
                        <div v-if="isCompleted && docRequest.blockchain_tx_hash"
                            class="bg-slate-800/50 backdrop-blur-xl border border-cyan-500/20 rounded-2xl p-6 relative overflow-hidden"
                        >
                            <!-- Glow -->
                            <div class="absolute top-0 right-0 w-48 h-48 bg-cyan-500/5 rounded-full blur-3xl pointer-events-none"></div>

                            <h3 class="text-xs font-bold text-cyan-400 uppercase tracking-widest mb-5 flex items-center gap-2">
                                <span class="w-2 h-2 bg-cyan-400 rounded-full animate-pulse"></span>
                                Blockchain Anchor Record
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="p-3 bg-black/20 rounded-xl border border-white/5">
                                    <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1">Network</label>
                                    <div class="text-sm font-mono text-purple-400">
                                        {{ docRequest.blockchain_network === 'polygon' ? 'Polygon Mainnet' : 'Polygon Amoy Testnet' }}
                                    </div>
                                </div>

                                <div class="p-3 bg-black/20 rounded-xl border border-white/5">
                                    <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1">Status</label>
                                    <div class="text-sm font-bold text-emerald-400 flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full"></span>
                                        {{ docRequest.blockchain_status?.toUpperCase() || 'ANCHORED' }}
                                    </div>
                                </div>

                                <div class="p-3 bg-black/20 rounded-xl border border-white/5">
                                    <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1">Anchored At</label>
                                    <div class="text-sm text-slate-300">
                                        {{ docRequest.blockchain_anchored_at ? formatDateTime(docRequest.blockchain_anchored_at) : 'N/A' }}
                                    </div>
                                </div>

                                <div class="p-3 bg-black/20 rounded-xl border border-white/5">
                                    <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1">TX Hash</label>
                                    <div class="text-xs font-mono text-blue-400 truncate">
                                        {{ docRequest.blockchain_tx_hash }}
                                    </div>
                                </div>
                            </div>

                            <a
                                v-if="docRequest.blockchain_explorer_url"
                                :href="docRequest.blockchain_explorer_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-4 flex items-center justify-center gap-2 w-full py-2.5 bg-cyan-600/15 hover:bg-cyan-600/25 border border-cyan-500/30 rounded-xl text-cyan-400 hover:text-cyan-300 text-sm font-semibold transition-all"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                View on PolygonScan
                            </a>
                        </div>
                    </Transition>

                    <!-- Official Action Console -->
                    <div class="bg-slate-800/80 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-2xl relative">
                        <!-- Loading Overlay -->
                        <div v-if="form.processing || isSubmitting" 
                            class="absolute inset-0 bg-black/70 backdrop-blur-sm rounded-2xl flex items-center justify-center z-50"
                        >
                            <div class="text-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent mx-auto mb-4"></div>
                                <p class="text-white font-bold text-lg">Updating Record...</p>
                                <p class="text-slate-400 text-sm mt-2">Sending notification to user</p>
                            </div>
                        </div>

                        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                            Official Action Console
                        </h3>
                        
                        <form @submit.prevent="submitUpdate" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 mb-2 uppercase">Update Status</label>
                                    <select 
                                        v-model="form.status"
                                        :disabled="form.processing || isSubmitting"
                                        class="w-full bg-black/40 border border-white/20 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 appearance-none cursor-pointer hover:bg-white/5 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <option value="pending">⏳ Pending Review</option>
                                        <option value="processing">🔄 Processing</option>
                                        <option value="ready_for_pickup">✅ Ready for Pickup</option>
                                        <option value="completed">🎉 Completed / Claimed</option>
                                        <option value="rejected">❌ Rejected</option>
                                    </select>
                                    <p v-if="form.errors.status" class="text-red-400 text-xs mt-1">{{ form.errors.status }}</p>
                                </div>

                                <div v-if="form.status === 'ready_for_pickup'" class="animate-fade-in">
                                    <label class="block text-xs font-bold text-green-400 mb-2 uppercase">
                                        📅 Pickup Schedule *
                                    </label>
                                    <input 
                                        type="datetime-local"
                                        v-model="form.appointment_date"
                                        :min="minDateTime"
                                        :disabled="form.processing || isSubmitting"
                                        class="w-full bg-green-500/10 border border-green-500/30 rounded-xl px-4 py-3 text-green-100 focus:ring-2 focus:ring-green-500 disabled:opacity-50"
                                        required
                                    />
                                    <p class="text-[10px] text-slate-500 mt-1">⚠️ User will be notified via email of this appointment</p>
                                    <p v-if="form.errors.appointment_date" class="text-red-400 text-xs mt-1">{{ form.errors.appointment_date }}</p>
                                </div>
                            </div>

                            <div class="flex flex-col h-full">
                                <label class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-bold text-slate-400 uppercase">Admin Remarks / Instructions</span>
                                    <span class="text-xs font-mono" :class="remainingChars < 100 ? 'text-red-400 font-bold' : 'text-slate-500'">
                                        {{ remainingChars }} / 1000
                                    </span>
                                </label>
                                <textarea 
                                    v-model="form.admin_remarks"
                                    :disabled="form.processing || isSubmitting"
                                    maxlength="1000"
                                    class="flex-1 w-full bg-black/40 border border-white/20 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 resize-none text-sm mb-2 disabled:opacity-50"
                                    placeholder="e.g., 'Bring valid government-issued ID and original birth certificate for verification...'"
                                ></textarea>
                                <p v-if="form.errors.admin_remarks" class="text-red-400 text-xs mb-2">{{ form.errors.admin_remarks }}</p>
                                
                                <button 
                                    type="submit" 
                                    :disabled="form.processing || isSubmitting"
                                    class="w-full py-3 rounded-xl font-bold text-sm text-white transition-all shadow-lg flex justify-center items-center gap-2 transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                                    :class="{
                                        'bg-blue-600 hover:bg-blue-500 shadow-blue-500/20': form.status !== 'rejected',
                                        'bg-red-600 hover:bg-red-500 shadow-red-500/20': form.status === 'rejected'
                                    }"
                                >
                                    <svg v-if="form.processing || isSubmitting" class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span v-if="form.processing || isSubmitting">Syncing Changes...</span>
                                    <span v-else>
                                        <span v-if="form.status === 'rejected'">⚠️</span>
                                        <span v-else>✓</span>
                                        Update Request Record
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>