<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({ 
    requests: {
        type: Array,
        default: () => [] 
    }
});

// ✅ NEW: Loading and Error States
const isLoading = ref(false);
const error = ref(null);
const retryCount = ref(0);
const maxRetries = 3;

// ✅ NEW: Toast Notification System
const toast = ref({
    show: false,
    message: '',
    type: 'info'
});

const showToast = (message, type = 'info') => {
    toast.value = { show: true, message, type };
    setTimeout(() => {
        toast.value.show = false;
    }, 4000);
};

// ✅ NEW: Safe data accessor with runtime validation
const safeRequests = computed(() => {
    if (!Array.isArray(props.requests)) {
        console.warn('[Documents] Invalid requests data received:', typeof props.requests);
        return [];
    }
    
    // Filter out invalid entries
    return props.requests.filter(req => {
        if (!req || typeof req !== 'object') {
            console.warn('[Documents] Invalid request entry:', req);
            return false;
        }
        if (!req.id) {
            console.warn('[Documents] Request missing ID:', req);
            return false;
        }
        return true;
    });
});

// ✅ NEW: Status counters with safe access
const statusCounts = computed(() => {
    try {
        return {
            pending: safeRequests.value.filter(r => r.status === 'pending').length,
            processing: safeRequests.value.filter(r => r.status === 'processing').length,
            ready: safeRequests.value.filter(r => r.status === 'ready_for_pickup').length,
            completed: safeRequests.value.filter(r => r.status === 'completed').length,
            total: safeRequests.value.length
        };
    } catch (err) {
        console.error('[Documents] Status count error:', err);
        return { pending: 0, processing: 0, ready: 0, completed: 0, total: 0 };
    }
});

// ✅ NEW: Filter system
const activeFilter = ref('all');
const searchQuery = ref('');

const filteredRequests = computed(() => {
    try {
        let filtered = safeRequests.value;
        
        // Apply status filter
        if (activeFilter.value !== 'all') {
            filtered = filtered.filter(r => r.status === activeFilter.value);
        }
        
        // Apply search filter
        if (searchQuery.value.trim()) {
            const query = searchQuery.value.toLowerCase().trim();
            filtered = filtered.filter(r => 
                r.user?.name?.toLowerCase().includes(query) ||
                r.user?.email?.toLowerCase().includes(query) ||
                r.document_type?.toLowerCase().includes(query) ||
                r.purpose?.toLowerCase().includes(query)
            );
        }
        
        return filtered;
    } catch (err) {
        console.error('[Documents] Filter error:', err);
        return safeRequests.value;
    }
});

const form = useForm({
    status: '',
    admin_note: ''
});

// ✅ ENHANCED: Update status with error handling
const updateStatus = async (id, newStatus) => {
    if (!id) {
        showToast('Invalid request ID', 'error');
        return;
    }
    
    if (!confirm(`Confirm Action: Mark request as ${newStatus.toUpperCase()}?`)) {
        return;
    }
    
    try {
        isLoading.value = true;
        error.value = null;
        
        form.status = newStatus;
        
        form.patch(route('admin.documents.update', id), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                showToast(`Request marked as ${newStatus}`, 'success');
                retryCount.value = 0;
            },
            onError: (errors) => {
                console.error('[Documents] Update failed:', errors);
                error.value = Object.values(errors).join(', ') || 'Failed to update status';
                showToast(error.value, 'error');
                
                // Retry logic
                if (retryCount.value < maxRetries) {
                    retryCount.value++;
                    showToast(`Retrying... (${retryCount.value}/${maxRetries})`, 'warning');
                    setTimeout(() => updateStatus(id, newStatus), 2000 * retryCount.value);
                }
            },
            onFinish: () => {
                isLoading.value = false;
            }
        });
    } catch (err) {
        console.error('[Documents] Unexpected error:', err);
        error.value = 'An unexpected error occurred';
        showToast(error.value, 'error');
        isLoading.value = false;
    }
};

// ✅ NEW: Refresh data
const refreshDocuments = async () => {
    if (isLoading.value) return;
    
    try {
        isLoading.value = true;
        error.value = null;
        
        router.reload({
            only: ['requests'],
            onError: (errors) => {
                console.error('[Documents] Reload failed:', errors);
                error.value = 'Failed to refresh documents';
                showToast('Failed to refresh documents', 'error');
            },
            onSuccess: () => {
                showToast('Documents refreshed', 'success');
            },
            onFinish: () => {
                isLoading.value = false;
            }
        });
    } catch (err) {
        console.error('[Documents] Refresh error:', err);
        error.value = 'An unexpected error occurred';
        showToast(error.value, 'error');
        isLoading.value = false;
    }
};

// ✅ NEW: Safe date formatting
const formatDate = (dateString) => {
    try {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return 'Invalid Date';
        return date.toLocaleString();
    } catch (err) {
        console.error('[Documents] Date format error:', err);
        return 'N/A';
    }
};

// ✅ NEW: Auto-refresh timer
let autoRefreshTimer;

onMounted(() => {
    try {
        // Auto-refresh every 30 seconds
        autoRefreshTimer = setInterval(refreshDocuments, 30000);
    } catch (err) {
        console.error('[Documents] Mount error:', err);
    }
});

onUnmounted(() => {
    try {
        clearInterval(autoRefreshTimer);
    } catch (err) {
        console.error('[Documents] Unmount error:', err);
    }
});
</script>

<template>
    <Head title="Document Control" />

    <AuthenticatedLayout>
        <!-- ✅ NEW: Toast Notification -->
        <Transition name="toast">
            <div v-if="toast.show" :class="['toast', `toast-${toast.type}`]">
                <span class="toast-icon">
                    <span v-if="toast.type === 'success'">✓</span>
                    <span v-else-if="toast.type === 'error'">✕</span>
                    <span v-else-if="toast.type === 'warning'">⚠</span>
                    <span v-else>ℹ</span>
                </span>
                <span class="toast-message">{{ toast.message }}</span>
            </div>
        </Transition>

        <!-- ✅ NEW: Error Banner -->
        <Transition name="error-banner">
            <div v-if="error" class="error-banner">
                <div class="error-banner-content">
                    <span class="error-icon">⚠️</span>
                    <span class="error-text">{{ error }}</span>
                    <button @click="refreshDocuments" class="error-retry-btn">
                        Retry
                    </button>
                    <button @click="error = null" class="error-close-btn">
                        ✕
                    </button>
                </div>
            </div>
        </Transition>

        <!-- ✅ NEW: Loading Overlay -->
        <Transition name="loading-overlay">
            <div v-if="isLoading && form.processing" class="loading-overlay">
                <div class="loading-spinner">
                    <div class="spinner-ring"></div>
                    <div class="spinner-ring"></div>
                    <div class="spinner-ring"></div>
                </div>
                <p class="loading-text">Processing request...</p>
            </div>
        </Transition>

        <div class="min-h-screen bg-slate-900 relative overflow-hidden font-sans">
            
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#64748b 1px, transparent 1px); background-size: 32px 32px;"></div>
                <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-[100px] animate-pulse-slow"></div>
                <div class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-cyan-600/10 rounded-full blur-[100px] animate-pulse-slow" style="animation-delay: 2s;"></div>
            </div>

            <div class="relative z-10 pt-10 pb-6 px-6 lg:px-8 border-b border-white/5 bg-slate-900/80 backdrop-blur-md">
                <div class="max-w-7xl mx-auto">
                    <div class="flex justify-between items-start mb-6 animate-fade-in">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-red-400">Restricted Access</span>
                            </div>
                            <h1 class="text-3xl md:text-4xl font-bold text-white tracking-tight">Document Control</h1>
                            <p class="text-slate-400 text-sm mt-1">Review and process resident applications.</p>
                        </div>
                        
                        <!-- ✅ NEW: Refresh Button -->
                        <button 
                            @click="refreshDocuments"
                            :disabled="isLoading"
                            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white text-sm font-bold rounded-lg transition-all transform hover:scale-105 shadow-lg shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" :class="{ 'animate-spin': isLoading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span class="hidden sm:inline">{{ isLoading ? 'Refreshing...' : 'Refresh' }}</span>
                        </button>
                    </div>

                    <!-- ✅ NEW: Stats Cards -->
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                        <div class="stat-card bg-amber-500/10 border-amber-500/20">
                            <div class="stat-value text-amber-400">{{ statusCounts.pending }}</div>
                            <div class="stat-label">Pending</div>
                        </div>
                        <div class="stat-card bg-blue-500/10 border-blue-500/20">
                            <div class="stat-value text-blue-400">{{ statusCounts.processing }}</div>
                            <div class="stat-label">Processing</div>
                        </div>
                        <div class="stat-card bg-emerald-500/10 border-emerald-500/20">
                            <div class="stat-value text-emerald-400">{{ statusCounts.ready }}</div>
                            <div class="stat-label">Ready</div>
                        </div>
                        <div class="stat-card bg-slate-500/10 border-slate-500/20">
                            <div class="stat-value text-slate-400">{{ statusCounts.completed }}</div>
                            <div class="stat-label">Completed</div>
                        </div>
                        <div class="stat-card bg-purple-500/10 border-purple-500/20">
                            <div class="stat-value text-purple-400">{{ statusCounts.total }}</div>
                            <div class="stat-label">Total</div>
                        </div>
                    </div>

                    <!-- ✅ NEW: Filter and Search Bar -->
                    <div class="flex flex-col sm:flex-row gap-4 mb-6">
                        <div class="flex-1">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input 
                                    v-model="searchQuery"
                                    type="text" 
                                    placeholder="Search by name, email, or document type..."
                                    class="w-full pl-10 pr-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 transition-colors"
                                />
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button 
                                @click="activeFilter = 'all'"
                                :class="['filter-btn', activeFilter === 'all' ? 'active' : '']"
                            >
                                All ({{ statusCounts.total }})
                            </button>
                            <button 
                                @click="activeFilter = 'pending'"
                                :class="['filter-btn', activeFilter === 'pending' ? 'active' : '']"
                            >
                                Pending ({{ statusCounts.pending }})
                            </button>
                            <button 
                                @click="activeFilter = 'ready_for_pickup'"
                                :class="['filter-btn', activeFilter === 'ready_for_pickup' ? 'active' : '']"
                            >
                                Ready ({{ statusCounts.ready }})
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-10 relative z-10">
                <div class="max-w-7xl mx-auto px-6 lg:px-8">
                    
                    <div class="bg-slate-800/40 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl animate-slide-up">
                        
                        <div class="p-4 border-b border-white/5 bg-white/5 flex justify-between items-center">
                            <div class="flex items-center gap-2 text-sm text-slate-300">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                <span>Showing:</span>
                                <span class="text-white font-bold">{{ filteredRequests.length }} of {{ statusCounts.total }} Requests</span>
                            </div>
                            <div class="text-[10px] font-mono text-emerald-500 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                LIVE FEED ACTIVE
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-white/5">
                                <thead class="bg-black/20">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Timestamp</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Applicant</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Request Details</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5 text-sm">
                                    <tr v-for="req in filteredRequests" :key="req.id" class="hover:bg-white/5 transition-colors group">
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-slate-300 font-mono text-xs">
                                            {{ formatDate(req.created_at) }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs mr-3 shadow-lg shadow-indigo-500/20">
                                                    {{ req.user?.name?.charAt(0) || '?' }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-white">{{ req.user?.name || 'Unknown User' }}</div>
                                                    <div class="text-xs text-slate-500">{{ req.user?.email || 'No Email' }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="text-blue-300 font-bold mb-1">{{ req.document_type || 'N/A' }}</div>
                                            <div class="text-slate-400 text-xs italic opacity-70 truncate max-w-xs">
                                                {{ req.purpose || 'No purpose specified' }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="req.status === 'pending'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-yellow-500/10 border border-yellow-500/20 text-yellow-400 text-xs font-bold shadow-[0_0_10px_rgba(234,179,8,0.2)]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 animate-pulse"></span> Pending
                                            </span>
                                            <span v-else-if="req.status === 'processing'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold shadow-[0_0_10px_rgba(59,130,246,0.2)]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-spin-slow"></span> Processing
                                            </span>
                                            <span v-else-if="req.status === 'ready_for_pickup'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-green-500/10 border border-green-500/20 text-green-400 text-xs font-bold shadow-[0_0_10px_rgba(34,197,94,0.2)]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> Ready
                                            </span>
                                            <span v-else-if="req.status === 'completed'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-700/50 border border-slate-600 text-slate-400 text-xs font-bold">
                                                Claimed
                                            </span>
                                            <span v-else class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-700/50 border border-slate-600 text-slate-400 text-xs font-bold">
                                                {{ req.status || 'Unknown' }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <Link 
                                                v-if="req.id"
                                                :href="route('admin.documents.show', req.id)" 
                                                class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-xs font-bold text-blue-300 transition-all hover:text-blue-200 group/btn"
                                            >
                                                <span>View Dossier</span>
                                                <svg class="w-3 h-3 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </Link>
                                            <span v-else class="text-slate-500 text-xs">No ID</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <!-- ✅ ENHANCED: Empty States -->
                            <div v-if="filteredRequests.length === 0 && searchQuery" class="p-12 text-center text-slate-500">
                                <div class="text-4xl mb-2 opacity-30">🔍</div>
                                <p class="font-medium">No results found for "{{ searchQuery }}"</p>
                                <p class="text-sm mt-1 text-slate-600">Try adjusting your search or filter</p>
                                <button @click="searchQuery = ''; activeFilter = 'all'" class="mt-4 px-4 py-2 bg-white/5 hover:bg-white/10 rounded-lg text-sm font-semibold transition-colors">
                                    Clear Filters
                                </button>
                            </div>

                            <div v-else-if="filteredRequests.length === 0 && activeFilter !== 'all'" class="p-12 text-center text-slate-500">
                                <div class="text-4xl mb-2 opacity-30">📭</div>
                                <p class="font-medium">No {{ activeFilter }} requests found</p>
                                <p class="text-sm mt-1 text-slate-600">Try selecting a different filter</p>
                            </div>

                            <div v-else-if="safeRequests.length === 0" class="p-12 text-center text-slate-500">
                                <div class="text-4xl mb-2 opacity-30">📂</div>
                                <p class="font-medium">No active requests in queue</p>
                                <p class="text-sm mt-1 text-slate-600">Waiting for new submissions</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* ... (Keep existing animation styles) ... */

/* ✅ NEW: Toast styles */
.toast {
    position: fixed;
    top: 6rem;
    right: 1.5rem;
    z-index: 100;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    border-radius: 0.75rem;
    background: rgba(18, 18, 20, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    max-width: 24rem;
}

.toast-success { border-color: rgba(34, 197, 94, 0.3); background: rgba(34, 197, 94, 0.1); }
.toast-error { border-color: rgba(239, 68, 68, 0.3); background: rgba(239, 68, 68, 0.1); }
.toast-warning { border-color: rgba(245, 158, 11, 0.3); background: rgba(245, 158, 11, 0.1); }
.toast-info { border-color: rgba(59, 130, 246, 0.3); background: rgba(59, 130, 246, 0.1); }

.toast-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    font-size: 1rem;
    font-weight: 700;
    flex-shrink: 0;
}

.toast-success .toast-icon { background: rgba(34, 197, 94, 0.2); color: rgb(134, 239, 172); }
.toast-error .toast-icon { background: rgba(239, 68, 68, 0.2); color: rgb(252, 165, 165); }
.toast-warning .toast-icon { background: rgba(245, 158, 11, 0.2); color: rgb(251, 191, 36); }
.toast-info .toast-icon { background: rgba(59, 130, 246, 0.2); color: rgb(147, 197, 253); }

.toast-message {
    flex: 1;
    font-size: 0.875rem;
    font-weight: 500;
    color: white;
}

.toast-enter-active { animation: toast-in 0.3s ease; }
.toast-leave-active { animation: toast-out 0.3s ease; }

@keyframes toast-in {
    from { opacity: 0; transform: translateX(100%); }
    to { opacity: 1; transform: translateX(0); }
}

@keyframes toast-out {
    to { opacity: 0; transform: translateX(100%); }
}

/* ✅ NEW: Error Banner */
.error-banner {
    position: fixed;
    top: 5rem;
    left: 50%;
    transform: translateX(-50%);
    z-index: 100;
    width: calc(100% - 2rem);
    max-width: 48rem;
}

.error-banner-content {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    border-radius: 0.75rem;
    background: rgba(239, 68, 68, 0.15);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(239, 68, 68, 0.3);
    box-shadow: 0 10px 30px rgba(239, 68, 68, 0.2);
}

.error-icon { font-size: 1.25rem; flex-shrink: 0; }
.error-text { flex: 1; font-size: 0.875rem; font-weight: 600; color: rgb(252, 165, 165); }

.error-retry-btn {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    background: rgba(239, 68, 68, 0.2);
    color: white;
    font-size: 0.75rem;
    font-weight: 700;
    transition: all 0.2s;
    flex-shrink: 0;
}

.error-retry-btn:hover {
    background: rgba(239, 68, 68, 0.3);
    transform: scale(1.05);
}

.error-close-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border-radius: 0.5rem;
    color: white;
    background: rgba(255, 255, 255, 0.05);
    transition: all 0.2s;
    flex-shrink: 0;
}

.error-close-btn:hover { background: rgba(255, 255, 255, 0.1); }

.error-banner-enter-active { animation: slide-down 0.3s ease; }
.error-banner-leave-active { animation: slide-up 0.3s ease; }

@keyframes slide-down {
    from { opacity: 0; transform: translateX(-50%) translateY(-100%); }
    to { opacity: 1; transform: translateX(-50%) translateY(0); }
}

@keyframes slide-up {
    to { opacity: 0; transform: translateX(-50%) translateY(-100%); }
}

/* ✅ NEW: Loading Overlay */
.loading-overlay {
    position: fixed;
    inset: 0;
    z-index: 90;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(8px);
}

.loading-spinner {
    position: relative;
    width: 60px;
    height: 60px;
}

.spinner-ring {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    border: 3px solid transparent;
    animation: spinner-rotate 1.5s cubic-bezier(0.5, 0, 0.5, 1) infinite;
}

.spinner-ring:nth-child(1) { border-top-color: #3b82f6; animation-delay: -0.45s; }
.spinner-ring:nth-child(2) { border-right-color: #a855f7; animation-delay: -0.3s; }
.spinner-ring:nth-child(3) { border-bottom-color: #ec4899; animation-delay: -0.15s; }

@keyframes spinner-rotate {
    to { transform: rotate(360deg); }
}

.loading-text {
    margin-top: 1.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.8);
}

.loading-overlay-enter-active { animation: fade-in 0.2s ease; }
.loading-overlay-leave-active { animation: fade-out 0.3s ease; }

@keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
@keyframes fade-out { to { opacity: 0; } }

/* ✅ NEW: Stat Cards */
.stat-card {
    padding: 1rem;
    border-radius: 1rem;
    border: 1px solid;
    backdrop-filter: blur(20px);
    transition: all 0.3s;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: rgba(255, 255, 255, 0.6);
}

/* ✅ NEW: Filter Buttons */
.filter-btn {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.75rem;
    font-weight: 600;
    transition: all 0.2s;
    white-space: nowrap;
}

.filter-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

.filter-btn.active {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(99, 102, 241, 0.3));
    border-color: rgba(59, 130, 246, 0.5);
    color: white;
}

/* Existing animations */
.animate-pulse-slow { animation: pulse 4s ease-in-out infinite; }
.animate-spin-slow { animation: spin 3s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.animate-slide-up { animation: slideUp 0.8s ease-out; }
@keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 1s ease-out; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>