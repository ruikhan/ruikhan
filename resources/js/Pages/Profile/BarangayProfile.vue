<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    profile: Object,
    profileStatus: Object,
    statistics: Object
});

const isEditing = ref(false);

// Initialize form with existing profile data or empty
const form = useForm({
    applicant_last_name: props.profile?.applicant_last_name || '',
    applicant_first_name: props.profile?.applicant_first_name || '',
    applicant_middle_name: props.profile?.applicant_middle_name || '',
    date_of_birth: props.profile?.date_of_birth || '',
    age: props.profile?.age || null,
    sex: props.profile?.sex || '',
    civil_status: props.profile?.civil_status || '',
    purok_street: props.profile?.purok_street || '',
    barangay: props.profile?.barangay || '',
    municipality: props.profile?.municipality || '',
    province: props.profile?.province || '',
    place_of_birth: props.profile?.place_of_birth || '',
    residency_duration: props.profile?.residency_duration || '',
    valid_id_type: props.profile?.valid_id_type || '',
    cedula_number: props.profile?.cedula_number || '',
});

const statusColor = computed(() => {
    if (!props.profileStatus) return 'gray';
    
    switch(props.profileStatus.reason) {
        case 'ok': return 'green';
        case 'outdated': return 'yellow';
        case 'incomplete': return 'orange';
        case 'no_profile': return 'red';
        default: return 'gray';
    }
});

const statusIcon = computed(() => {
    if (!props.profileStatus) return '❓';
    
    switch(props.profileStatus.reason) {
        case 'ok': return '✅';
        case 'outdated': return '⚠️';
        case 'incomplete': return '📝';
        case 'no_profile': return '❌';
        default: return '❓';
    }
});

const completenessColor = computed(() => {
    const completeness = props.statistics?.completeness || 0;
    if (completeness >= 90) return 'green';
    if (completeness >= 70) return 'blue';
    if (completeness >= 50) return 'yellow';
    return 'red';
});

const toggleEdit = () => {
    isEditing.value = !isEditing.value;
    
    if (!isEditing.value) {
        // Reset form if canceling edit
        form.reset();
    }
};

const submitProfile = () => {
    form.post(route('profile.barangay.update'), {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false;
        },
    });
};

const deleteProfile = () => {
    if (!confirm('Are you sure you want to delete your profile? This will require you to fill out complete forms for future barangay certificate requests.')) {
        return;
    }
    
    router.delete(route('profile.barangay.delete'), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirect or show message
        }
    });
};
</script>

<template>
    <Head title="My Barangay Profile" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900 py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl sm:text-4xl font-black text-white mb-2">My Barangay Profile</h1>
                    <p class="text-gray-400">Manage your personal information for barangay certifications</p>
                </div>

                <!-- Status Banner -->
                <div v-if="profileStatus" :class="[
                    'mb-6 rounded-2xl p-6 border-2',
                    statusColor === 'green' ? 'bg-green-500/10 border-green-500/30' : '',
                    statusColor === 'yellow' ? 'bg-yellow-500/10 border-yellow-500/30' : '',
                    statusColor === 'orange' ? 'bg-orange-500/10 border-orange-500/30' : '',
                    statusColor === 'red' ? 'bg-red-500/10 border-red-500/30' : ''
                ]">
                    <div class="flex items-start gap-4">
                        <span class="text-4xl">{{ statusIcon }}</span>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-white mb-1">Profile Status</h3>
                            <p class="text-sm" :class="[
                                statusColor === 'green' ? 'text-green-300' : '',
                                statusColor === 'yellow' ? 'text-yellow-300' : '',
                                statusColor === 'orange' ? 'text-orange-300' : '',
                                statusColor === 'red' ? 'text-red-300' : ''
                            ]">
                                {{ profileStatus.message }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div v-if="statistics && statistics.has_profile" class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                    <div class="bg-gray-800/50 border border-gray-700 rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-2xl">📊</span>
                            <span class="text-xs text-gray-400 uppercase tracking-wider">Completeness</span>
                        </div>
                        <div class="text-3xl font-bold text-white mb-1">{{ statistics.completeness }}%</div>
                        <div class="w-full bg-gray-700 rounded-full h-2 mt-2">
                            <div :class="[
                                'h-2 rounded-full transition-all',
                                completenessColor === 'green' ? 'bg-green-500' : '',
                                completenessColor === 'blue' ? 'bg-blue-500' : '',
                                completenessColor === 'yellow' ? 'bg-yellow-500' : '',
                                completenessColor === 'red' ? 'bg-red-500' : ''
                            ]" :style="{ width: statistics.completeness + '%' }"></div>
                        </div>
                    </div>

                    <div class="bg-gray-800/50 border border-gray-700 rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-2xl">🔄</span>
                            <span class="text-xs text-gray-400 uppercase tracking-wider">Times Used</span>
                        </div>
                        <div class="text-3xl font-bold text-white">{{ statistics.usage_count }}</div>
                        <div class="text-xs text-gray-500 mt-1">Quick Submits</div>
                    </div>

                    <div class="bg-gray-800/50 border border-gray-700 rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-2xl">📜</span>
                            <span class="text-xs text-gray-400 uppercase tracking-wider">Total Requests</span>
                        </div>
                        <div class="text-3xl font-bold text-white">{{ statistics.total_requests }}</div>
                        <div class="text-xs text-gray-500 mt-1">Certificates</div>
                    </div>
                </div>

                <!-- Profile Card -->
                <div class="bg-gray-900/50 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl overflow-hidden">
                    
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-600/20 to-purple-600/20 border-b border-white/10 px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">👤</span>
                            <h2 class="text-lg font-bold text-white">Personal Information</h2>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <button
                                v-if="profile && !isEditing"
                                @click="toggleEdit"
                                class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-xl text-sm font-medium transition-all"
                            >
                                ✏️ Edit
                            </button>
                            <button
                                v-if="isEditing"
                                @click="toggleEdit"
                                class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-xl text-sm font-medium transition-all"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6 sm:p-8">
                        
                        <!-- No Profile State -->
                        <div v-if="!profile && !isEditing" class="text-center py-12">
                            <span class="text-6xl mb-4 block">📋</span>
                            <h3 class="text-xl font-bold text-white mb-2">No Profile Yet</h3>
                            <p class="text-gray-400 mb-6">Create your profile to use Quick Submit for faster processing</p>
                            <button
                                @click="isEditing = true"
                                class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-bold rounded-2xl transition-all shadow-lg"
                            >
                                Create Profile
                            </button>
                        </div>

                        <!-- View Mode -->
                        <div v-if="profile && !isEditing" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="profile-field">
                                <label>Full Name</label>
                                <p>{{ profile.applicant_first_name }} {{ profile.applicant_middle_name }} {{ profile.applicant_last_name }}</p>
                            </div>

                            <div class="profile-field">
                                <label>Date of Birth</label>
                                <p>{{ profile.date_of_birth }} ({{ profile.age }} years old)</p>
                            </div>

                            <div class="profile-field">
                                <label>Sex</label>
                                <p>{{ profile.sex }}</p>
                            </div>

                            <div class="profile-field">
                                <label>Civil Status</label>
                                <p>{{ profile.civil_status }}</p>
                            </div>

                            <div class="profile-field col-span-1 sm:col-span-2">
                                <label>Complete Address</label>
                                <p>{{ [profile.purok_street, profile.barangay, profile.municipality, profile.province].filter(Boolean).join(', ') }}</p>
                            </div>

                            <div class="profile-field" v-if="profile.place_of_birth">
                                <label>Place of Birth</label>
                                <p>{{ profile.place_of_birth }}</p>
                            </div>

                            <div class="profile-field" v-if="profile.residency_duration">
                                <label>Residency Duration</label>
                                <p>{{ profile.residency_duration }}</p>
                            </div>

                            <div class="profile-field" v-if="profile.valid_id_type">
                                <label>Valid ID</label>
                                <p>{{ profile.valid_id_type }}</p>
                            </div>

                            <div class="profile-field" v-if="profile.cedula_number">
                                <label>Cedula Number</label>
                                <p>{{ profile.cedula_number }}</p>
                            </div>
                        </div>

                        <!-- Edit Mode -->
                        <form v-if="isEditing" @submit.prevent="submitProfile" class="space-y-6">
                            
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="form-group">
                                    <label class="form-label">Last Name <span class="text-red-400">*</span></label>
                                    <input type="text" v-model="form.applicant_last_name" class="form-input" required />
                                    <span v-if="form.errors.applicant_last_name" class="form-error">{{ form.errors.applicant_last_name }}</span>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">First Name <span class="text-red-400">*</span></label>
                                    <input type="text" v-model="form.applicant_first_name" class="form-input" required />
                                    <span v-if="form.errors.applicant_first_name" class="form-error">{{ form.errors.applicant_first_name }}</span>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" v-model="form.applicant_middle_name" class="form-input" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="form-group">
                                    <label class="form-label">Date of Birth <span class="text-red-400">*</span></label>
                                    <input type="date" v-model="form.date_of_birth" class="form-input" required />
                                    <span v-if="form.errors.date_of_birth" class="form-error">{{ form.errors.date_of_birth }}</span>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Age <span class="text-red-400">*</span></label>
                                    <input type="number" v-model="form.age" class="form-input" required />
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Sex <span class="text-red-400">*</span></label>
                                    <select v-model="form.sex" class="form-select" required>
                                        <option value="">Select</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label class="form-label">Civil Status <span class="text-red-400">*</span></label>
                                    <select v-model="form.civil_status" class="form-select" required>
                                        <option value="">Select</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widowed">Widowed</option>
                                        <option value="Separated">Separated</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Place of Birth</label>
                                    <input type="text" v-model="form.place_of_birth" class="form-input" />
                                </div>
                            </div>

                            <div class="border-t border-white/10 pt-6">
                                <h3 class="text-lg font-bold text-white mb-4">Address Information</h3>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="form-label">Purok / Street</label>
                                        <input type="text" v-model="form.purok_street" class="form-input" />
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Barangay <span class="text-red-400">*</span></label>
                                        <input type="text" v-model="form.barangay" class="form-input" required />
                                        <span v-if="form.errors.barangay" class="form-error">{{ form.errors.barangay }}</span>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Municipality <span class="text-red-400">*</span></label>
                                        <input type="text" v-model="form.municipality" class="form-input" required />
                                        <span v-if="form.errors.municipality" class="form-error">{{ form.errors.municipality }}</span>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Province <span class="text-red-400">*</span></label>
                                        <input type="text" v-model="form.province" class="form-input" required />
                                        <span v-if="form.errors.province" class="form-error">{{ form.errors.province }}</span>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Residency Duration</label>
                                        <input type="text" v-model="form.residency_duration" class="form-input" placeholder="e.g., 5 years" />
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-white/10 pt-6">
                                <h3 class="text-lg font-bold text-white mb-4">Additional Information</h3>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="form-label">Valid ID Type</label>
                                        <select v-model="form.valid_id_type" class="form-select">
                                            <option value="">Select</option>
                                            <option value="PhilID">PhilID</option>
                                            <option value="Driver's License">Driver's License</option>
                                            <option value="Passport">Passport</option>
                                            <option value="Voter's ID">Voter's ID</option>
                                            <option value="SSS/GSIS ID">SSS/GSIS ID</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Cedula Number</label>
                                        <input type="text" v-model="form.cedula_number" class="form-input" />
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-6 border-t border-white/10">
                                <button
                                    type="button"
                                    @click="deleteProfile"
                                    v-if="profile"
                                    class="px-4 py-2 text-red-400 hover:text-red-300 text-sm font-medium"
                                >
                                    🗑️ Delete Profile
                                </button>
                                <div class="flex gap-3 ml-auto">
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold rounded-2xl transition-all disabled:opacity-50"
                                    >
                                        {{ form.processing ? 'Saving...' : 'Save Profile' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.profile-field {
    @apply space-y-1;
}

.profile-field label {
    @apply text-xs text-gray-500 uppercase tracking-wider;
}

.profile-field p {
    @apply text-white font-medium;
}

.form-group {
    @apply space-y-2;
}

.form-label {
    @apply block text-sm font-bold text-gray-300;
}

.form-input,
.form-select {
    @apply w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 transition-all;
    @apply focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20;
}

.form-select {
    @apply appearance-none pr-10;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-size: 1.25rem;
    background-position: right 0.75rem center;
    background-repeat: no-repeat;
}

.form-error {
    @apply text-red-400 text-xs flex items-center gap-1;
}

.form-error::before {
    content: '⚠️';
}
</style>