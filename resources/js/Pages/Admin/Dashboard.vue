<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

// State to store incoming notifications
const notifications = ref([]);

onMounted(() => {
    window.Echo.channel('admin-channel')
        // Listener 1: Documents
        .listen('.document.created', (e) => {
            notifications.value.unshift({
                id: Date.now(),
                message: `New Document Request: ${e.request.document_type}`,
                purpose: e.request.purpose,
                time: new Date().toLocaleTimeString(),
                type: 'document' // Optional: for styling
            });
        })
        // Listener 2: Concerns (NEW)
        .listen('.concern.created', (e) => {
            console.log('Concern received:', e);
            notifications.value.unshift({
                id: Date.now(),
                message: `🚨 REPORT: ${e.concern.subject}`,
                purpose: `Location: ${e.concern.location} (${e.concern.category})`,
                time: new Date().toLocaleTimeString(),
                type: 'concern',
                // Check if image exists, then build the full URL
                image: e.concern.evidence_image_path ? `/storage/${e.concern.evidence_image_path}` : null
            });
        });
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Command Center</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Live Activity Feed</h3>
                    
                    <div v-if="notifications.length === 0" class="text-gray-400 italic">
                        Waiting for resident requests... (Listening on Port 6001)
                    </div>

                    <transition-group name="list" tag="div" class="space-y-3">
                        <div v-for="notif in notifications" :key="notif.id" 
                             class="p-4 bg-blue-50 border-l-4 border-blue-500 shadow-sm flex justify-between items-center animate-pulse">
                            <div>
                                <div class="font-bold text-blue-900">{{ notif.message }}</div>
                                <div class="text-sm text-blue-700">Reason: {{ notif.purpose }}</div>
                            </div>
                            <div class="text-xs text-blue-400">{{ notif.time }}</div>
                        </div>
                    </transition-group>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>