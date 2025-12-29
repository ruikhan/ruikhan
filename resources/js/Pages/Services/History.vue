<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    requests: Array
});

// Helper for status colors
const getStatusColor = (status) => {
    switch(status.toLowerCase()) {
        case 'pending': return 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20';
        case 'approved': return 'bg-green-500/10 text-green-500 border-green-500/20';
        case 'rejected': return 'bg-red-500/10 text-red-500 border-red-500/20';
        default: return 'bg-slate-500/10 text-slate-500 border-slate-500/20';
    }
};

// Helper for formatting dates
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};
</script>

<template>
    <Head title="My Requests" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Request History</h2>
                <Link :href="route('dashboard')" class="text-sm text-blue-400 hover:text-blue-300">
                    ← Back to Dashboard
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div v-if="$page.props.flash.success" class="mb-6 bg-green-500/10 border border-green-500/20 text-green-400 p-4 rounded-xl flex items-center justify-between">
                    <span>{{ $page.props.flash.success }}</span>
                    <button @click="$page.props.flash.success = null" class="text-green-500 hover:text-white">✕</button>
                </div>

                <div v-if="requests.length === 0" class="text-center py-20 bg-[#121212]/50 rounded-3xl border border-white/5">
                    <div class="text-6xl mb-4">📂</div>
                    <h3 class="text-xl font-semibold text-white">No Requests Found</h3>
                    <p class="text-slate-400 mt-2 mb-6">You haven't submitted any document requests yet.</p>
                    <Link :href="route('dashboard')" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-medium transition-all">
                        Create New Request
                    </Link>
                </div>

                <div v-else class="bg-[#121212] overflow-hidden shadow-sm sm:rounded-3xl border border-white/10">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-white/10 bg-white/5">
                                    <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Tracking Code</th>
                                    <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Department</th>
                                    <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Document Type</th>
                                    <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Date</th>
                                    <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <tr v-for="req in requests" :key="req.id" class="hover:bg-white/[0.02] transition-colors group">
                                    <td class="p-5 font-mono text-blue-400 font-medium">
                                        {{ req.tracking_code }}
                                    </td>
                                    <td class="p-5 text-white font-medium">
                                        {{ req.department }}
                                    </td>
                                    <td class="p-5 text-slate-300">
                                        {{ req.service_type || req.document_type || 'General Request' }}
                                    </td>
                                    <td class="p-5 text-slate-400 text-sm">
                                        {{ formatDate(req.created_at) }}
                                    </td>
                                    <td class="p-5 text-right">
                                        <span :class="['px-3 py-1 rounded-full text-xs font-bold border', getStatusColor(req.status)]">
                                            {{ req.status.toUpperCase() }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>