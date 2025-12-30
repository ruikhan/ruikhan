<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    departmentKey: String,
    config: Object,
    isReturningUser: Boolean,      // ✅ NEW
    existingProfile: Object         // ✅ NEW
});

// Track selected document type separately
const selectedDocType = ref(props.config.types[0]);
const currentStep = ref(1);
const formErrors = ref({});

// ✅ NEW: Signature Canvas
const signatureCanvas = ref(null);
const isDrawing = ref(false);
const hasSignature = ref(false);

// ✅ NEW: User choice for returning users
const useQuickSubmit = ref(false);

// Computed property to get the current fields based on selected document type
const currentFields = computed(() => {
    if (props.config.type_specific_fields && selectedDocType.value) {
        return props.config.type_specific_fields[selectedDocType.value] || props.config.fields;
    }
    return props.config.fields;
});

// ✅ NEW: Filter fields for returning users (only document-specific fields)
const documentSpecificFields = computed(() => {
    if (!useQuickSubmit.value || !props.isReturningUser) {
        return [];
    }
    
    // These are the fields that change per document request
    const specificFieldNames = [
        'purpose', 'purpose_other', 'recipient_office', 
        'occupation', 'income_status', 'monthly_income', 
        'number_of_dependents', 'community_standing', 
        'criminal_record', 'business_name', 'business_address',
        'business_nature', 'tin', 'dti_registration',
        'educational_attainment', 'course_taken', 'employment_status',
        'year_graduated', 'land_area', 'land_location', 
        'land_boundaries', 'land_classification', 'land_owner',
        'tenancy_duration', 'community_involvement', 'blotter_history'
    ];
    
    return currentFields.value.filter(field => 
        specificFieldNames.includes(field.name)
    );
});

// Split fields into steps for better UX
const fieldsStep1 = computed(() => {
    const fields = currentFields.value;
    const half = Math.ceil(fields.length / 2);
    return fields.slice(0, half);
});

const fieldsStep2 = computed(() => {
    const fields = currentFields.value;
    const half = Math.ceil(fields.length / 2);
    return fields.slice(half);
});

// Helper function to initialize form data from fields
const initializeFormData = (fields) => {
    const formData = {};
    fields.forEach(field => {
        if (field.type !== 'file') {
            formData[field.name] = field.type === 'number' ? null : '';
        }
    });
    return formData;
};

const form = useForm({
    department: props.departmentKey,
    document_type: selectedDocType.value,
    remarks: '',
    attachments: null,
    formData: initializeFormData(currentFields.value),
    use_existing_profile: false,    // ✅ NEW
    signature_data: null            // ✅ NEW
});

// Watch for document type changes and rebuild form.formData
watch(selectedDocType, (newType) => {
    form.document_type = newType;
    
    const fieldsForType = props.config.type_specific_fields?.[newType] || props.config.fields;
    form.formData = initializeFormData(fieldsForType);
    
    // Reset to step 1 when document type changes
    currentStep.value = 1;
    formErrors.value = {};
    useQuickSubmit.value = false;
    clearSignature();
});

// ✅ NEW: Watch for quick submit toggle
watch(useQuickSubmit, (newValue) => {
    if (newValue && props.isReturningUser) {
        // User chose quick submit - mark to use existing profile
        form.use_existing_profile = true;
        // Clear out personal fields since we'll use stored ones
        const profileFields = [
            'request_level', 'applicant_last_name', 'applicant_first_name', 
            'applicant_middle_name', 'date_of_birth', 'age', 'sex', 
            'civil_status', 'purok_street', 'barangay', 'municipality', 
            'province', 'place_of_birth', 'residency_duration', 
            'valid_id_type', 'cedula_number'
        ];
        profileFields.forEach(field => {
            if (form.formData[field] !== undefined) {
                delete form.formData[field];
            }
        });
    } else {
        form.use_existing_profile = false;
    }
});

// ✅ NEW: Signature Pad Functions
const setupSignatureCanvas = () => {
    if (!signatureCanvas.value) return;
    
    const canvas = signatureCanvas.value;
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * 2;
    canvas.height = rect.height * 2;
    const ctx = canvas.getContext('2d');
    ctx.scale(2, 2);
    ctx.strokeStyle = '#3b82f6';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
};

const startDrawing = (e) => {
    isDrawing.value = true;
    const canvas = signatureCanvas.value;
    const ctx = canvas.getContext('2d');
    const rect = canvas.getBoundingClientRect();
    const x = (e.clientX || e.touches[0].clientX) - rect.left;
    const y = (e.clientY || e.touches[0].clientY) - rect.top;
    ctx.beginPath();
    ctx.moveTo(x, y);
};

const draw = (e) => {
    if (!isDrawing.value) return;
    e.preventDefault();
    
    const canvas = signatureCanvas.value;
    const ctx = canvas.getContext('2d');
    const rect = canvas.getBoundingClientRect();
    const x = (e.clientX || e.touches[0].clientX) - rect.left;
    const y = (e.clientY || e.touches[0].clientY) - rect.top;
    
    ctx.lineTo(x, y);
    ctx.stroke();
    hasSignature.value = true;
};

const stopDrawing = () => {
    isDrawing.value = false;
    if (hasSignature.value) {
        form.signature_data = signatureCanvas.value.toDataURL();
    }
};

const clearSignature = () => {
    if (!signatureCanvas.value) return;
    const canvas = signatureCanvas.value;
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    hasSignature.value = false;
    form.signature_data = null;
};

// Validation
const validateStep = (step) => {
    formErrors.value = {};
    
    if (step === 1) {
        if (!selectedDocType.value) {
            formErrors.value.document_type = 'Please select a document type';
            return false;
        }
        
        // ✅ NEW: If returning user chose quick submit, validate choice confirmation
        if (props.isReturningUser && props.departmentKey === 'Barangay Certifications') {
            // They must make a choice
            return true; // Let them proceed to see the form options
        }
        
        return true;
    }
    
    if (step === 2) {
        // ✅ NEW: Different validation for quick submit
        if (useQuickSubmit.value && props.isReturningUser) {
            // Only validate document-specific fields + signature
            let hasErrors = false;
            
            documentSpecificFields.value.forEach(field => {
                const value = form.formData[field.name];
                const isRequired = field.required !== false;
                
                if (isRequired && (value === null || value === undefined || value === '')) {
                    formErrors.value[field.name] = `${field.label} is required`;
                    hasErrors = true;
                }
            });
            
            // Check signature
            if (!hasSignature.value) {
                formErrors.value.signature = 'Please provide your signature';
                hasErrors = true;
            }
            
            return !hasErrors;
        }
        
        // Standard validation for full form
        let hasErrors = false;
        currentFields.value.forEach(field => {
            if (field.type === 'file') return;
            
            const value = form.formData[field.name];
            const isRequired = field.required !== false;
            
            if (isRequired && (value === null || value === undefined || value === '')) {
                formErrors.value[field.name] = `${field.label} is required`;
                hasErrors = true;
            }
        });
        
        return !hasErrors;
    }
    
    return true;
};

const nextStep = () => {
    if (validateStep(1)) {
        currentStep.value = 2;
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        // Setup signature canvas if it's a quick submit
        if (useQuickSubmit.value && props.isReturningUser) {
            setTimeout(setupSignatureCanvas, 100);
        }
    }
};

const prevStep = () => {
    currentStep.value = 1;
    formErrors.value = {};
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const submit = () => {
    if (!validateStep(2)) {
        const firstErrorField = Object.keys(formErrors.value)[0];
        if (firstErrorField) {
            const element = document.getElementById(firstErrorField) || document.getElementById(`${firstErrorField}-2`);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                element.focus();
            }
        }
        return;
    }
    
    form.transform((data) => ({
        ...data,
        data: data.formData,
        formData: undefined
    })).post(route('services.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            selectedDocType.value = props.config.types[0];
            currentStep.value = 1;
            formErrors.value = {};
            useQuickSubmit.value = false;
            clearSignature();
        },
        onError: (errors) => {
            console.error('Submission errors:', errors);
            Object.keys(errors).forEach(key => {
                if (key.startsWith('data.')) {
                    const fieldName = key.replace('data.', '');
                    formErrors.value[fieldName] = errors[key];
                }
            });
        }
    });
};

const handleFileInput = (event) => {
    const files = Array.from(event.target.files);
    
    if (files.length === 0) {
        form.attachments = null;
        return;
    }
    
    // Check each file size
    const oversizedFiles = files.filter(file => file.size > 10 * 1024 * 1024);
    if (oversizedFiles.length > 0) {
        alert(`${oversizedFiles.length} file(s) exceed 10MB limit`);
        event.target.value = '';
        form.attachments = null;
        return;
    }
    
    form.attachments = files;  // ✅ Array of files
};

const progress = computed(() => (currentStep.value / 2) * 100);
</script>

<template>
    <Head :title="config.title" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900 py-6 sm:py-8 lg:py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto">
                
                <!-- Header -->
                <div class="text-center mb-6 sm:mb-8 animate-fade-in-up">
                    <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-blue-500/10 to-purple-500/10 rounded-2xl sm:rounded-3xl mb-4 shadow-lg shadow-blue-500/20 backdrop-blur-md border border-white/10">
                        <span class="text-3xl sm:text-4xl">{{ config.icon }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white mb-2">{{ config.title }}</h1>
                    <p class="text-sm sm:text-base text-gray-400">{{ config.description }}</p>
                </div>

                <!-- ✅ NEW: Returning User Welcome Banner -->
                <div v-if="isReturningUser && departmentKey === 'Barangay Certifications' && currentStep === 1" 
                     class="mb-6 bg-gradient-to-r from-green-500/10 to-emerald-500/10 border border-green-500/30 rounded-2xl p-6 animate-slide-down">
                    <div class="flex items-start gap-4">
                        <span class="text-4xl">👋</span>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-white mb-2">Welcome Back!</h3>
                            <p class="text-sm text-gray-300 mb-4">
                                We found your information on file. You can use <strong>Quick Submit</strong> 
                                to process your request faster - just sign and submit!
                            </p>
                            <div class="flex items-center gap-2 text-xs text-green-400">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Your profile is up to date</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-6 sm:mb-8 animate-slide-down">
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex items-center flex-1">
                            <div :class="[
                                'flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-full font-bold text-xs sm:text-sm transition-all duration-300',
                                currentStep >= 1 
                                    ? 'bg-gradient-to-br from-blue-500 to-purple-600 text-white shadow-lg scale-110' 
                                    : 'bg-gray-800 text-gray-500 border-2 border-gray-700'
                            ]">
                                1
                            </div>
                            <div :class="[
                                'flex-1 h-1 mx-2 sm:mx-3 rounded-full transition-all duration-500',
                                currentStep > 1 ? 'bg-gradient-to-r from-blue-500 to-purple-600' : 'bg-gray-800'
                            ]"></div>
                        </div>
                        <div :class="[
                            'flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-full font-bold text-xs sm:text-sm transition-all duration-300',
                            currentStep >= 2 
                                ? 'bg-gradient-to-br from-blue-500 to-purple-600 text-white shadow-lg scale-110' 
                                : 'bg-gray-800 text-gray-500 border-2 border-gray-700'
                        ]">
                            2
                        </div>
                    </div>
                    <div class="flex justify-between text-[10px] sm:text-xs text-gray-500 font-medium px-1">
                        <span :class="currentStep >= 1 ? 'text-blue-400' : ''">Document Type</span>
                        <span :class="currentStep >= 2 ? 'text-blue-400' : ''">
                            {{ useQuickSubmit && isReturningUser ? 'Sign & Submit' : 'Information' }}
                        </span>
                    </div>
                </div>

                <!-- Form Container -->
                <div class="bg-gray-900/50 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl overflow-hidden">
                    
                    <!-- Step Indicator -->
                    <div class="bg-gradient-to-r from-blue-600/20 to-purple-600/20 border-b border-white/10 px-4 sm:px-6 py-3 sm:py-4">
                        <h2 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                            <span class="text-xl sm:text-2xl">
                                {{ currentStep === 1 ? '📋' : (useQuickSubmit && isReturningUser ? '✍️' : '📝') }}
                            </span>
                            {{ currentStep === 1 ? 'Select Document Type' : (useQuickSubmit && isReturningUser ? 'Quick Submit' : 'Required Information') }}
                        </h2>
                    </div>

                    <form @submit.prevent="submit" class="p-4 sm:p-6 lg:p-8">
                        
                        <!-- Step 1: Document Type Selection -->
                        <div v-show="currentStep === 1" class="space-y-6 animate-fade-in">
                            
                            <!-- Document Type Selector -->
                            <div class="form-group">
                                <label class="form-label">
                                    Type of Document <span class="text-red-400">*</span>
                                </label>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <button
                                        v-for="type in config.types"
                                        :key="type"
                                        type="button"
                                        @click="selectedDocType = type"
                                        :class="[
                                            'flex items-center gap-3 p-4 rounded-xl sm:rounded-2xl border-2 transition-all duration-200 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500',
                                            selectedDocType === type
                                                ? 'bg-blue-500/20 border-blue-500 shadow-lg shadow-blue-500/20'
                                                : 'bg-gray-800/50 border-gray-700 hover:border-gray-600 active:scale-95'
                                        ]"
                                    >
                                        <div :class="[
                                            'w-10 h-10 rounded-xl flex items-center justify-center text-xl flex-shrink-0',
                                            selectedDocType === type ? 'bg-blue-500/30' : 'bg-gray-700/50'
                                        ]">
                                            📄
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span class="text-sm font-bold text-white block truncate">{{ type }}</span>
                                            <span class="text-xs text-gray-400">{{ currentFields.length }} fields required</span>
                                        </div>
                                        <div v-if="selectedDocType === type" class="flex-shrink-0">
                                            <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </button>
                                </div>
                                
                                <div v-if="formErrors.document_type || form.errors.document_type" class="form-error">
                                    {{ formErrors.document_type || form.errors.document_type }}
                                </div>
                            </div>

                            <!-- ✅ NEW: Submission Method Choice (Returning Users Only) -->
                            <div v-if="isReturningUser && departmentKey === 'Barangay Certifications' && selectedDocType" 
                                 class="form-group">
                                <label class="form-label mb-3">Choose Submission Method</label>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <!-- Quick Submit Option -->
                                    <button
                                        type="button"
                                        @click="useQuickSubmit = true"
                                        :class="[
                                            'flex flex-col items-start gap-3 p-5 rounded-xl sm:rounded-2xl border-2 transition-all duration-200 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500',
                                            useQuickSubmit
                                                ? 'bg-green-500/20 border-green-500 shadow-lg shadow-green-500/20'
                                                : 'bg-gray-800/50 border-gray-700 hover:border-gray-600 active:scale-95'
                                        ]"
                                    >
                                        <div class="flex items-center gap-3 w-full">
                                            <div :class="[
                                                'w-12 h-12 rounded-xl flex items-center justify-center text-2xl flex-shrink-0',
                                                useQuickSubmit ? 'bg-green-500/30' : 'bg-gray-700/50'
                                            ]">
                                                ⚡
                                            </div>
                                            <div class="flex-1">
                                                <span class="text-sm font-bold text-white block">Quick Submit</span>
                                                <span class="text-xs text-gray-400">Use saved profile</span>
                                            </div>
                                            <div v-if="useQuickSubmit" class="flex-shrink-0">
                                                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-400 pl-15">
                                            ✓ Uses your saved information<br>
                                            ✓ Just add purpose & sign<br>
                                            ✓ 2x faster processing
                                        </p>
                                    </button>

                                    <!-- Full Form Option -->
                                    <button
                                        type="button"
                                        @click="useQuickSubmit = false"
                                        :class="[
                                            'flex flex-col items-start gap-3 p-5 rounded-xl sm:rounded-2xl border-2 transition-all duration-200 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500',
                                            !useQuickSubmit
                                                ? 'bg-blue-500/20 border-blue-500 shadow-lg shadow-blue-500/20'
                                                : 'bg-gray-800/50 border-gray-700 hover:border-gray-600 active:scale-95'
                                        ]"
                                    >
                                        <div class="flex items-center gap-3 w-full">
                                            <div :class="[
                                                'w-12 h-12 rounded-xl flex items-center justify-center text-2xl flex-shrink-0',
                                                !useQuickSubmit ? 'bg-blue-500/30' : 'bg-gray-700/50'
                                            ]">
                                                📝
                                            </div>
                                            <div class="flex-1">
                                                <span class="text-sm font-bold text-white block">Complete Form</span>
                                                <span class="text-xs text-gray-400">Fill all fields</span>
                                            </div>
                                            <div v-if="!useQuickSubmit" class="flex-shrink-0">
                                                <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-400 pl-15">
                                            ✓ Update your information<br>
                                            ✓ Add new details<br>
                                            ✓ Full control over data
                                        </p>
                                    </button>
                                </div>
                            </div>

                            <!-- Info Box -->
                            <div v-if="selectedDocType" class="bg-blue-500/10 border border-blue-500/30 rounded-xl sm:rounded-2xl p-4 flex items-start gap-3">
                                <span class="text-xl sm:text-2xl flex-shrink-0">ℹ️</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-blue-200 text-sm font-medium">Selected: {{ selectedDocType }}</p>
                                    <p class="text-blue-300/70 text-xs mt-1">
                                        Click Continue to {{ useQuickSubmit && isReturningUser ? 'sign and submit' : 'fill out the required information' }}.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Form Fields OR Quick Submit -->
                        <div v-show="currentStep === 2" class="space-y-6 animate-fade-in">
                            
                            <!-- ✅ NEW: Quick Submit Flow (Returning Users) -->
                            <div v-if="useQuickSubmit && isReturningUser">
                                
                                <!-- Profile Preview -->
                                <div class="bg-gray-800/50 border border-gray-700 rounded-2xl p-6 mb-6">
                                    <h3 class="text-sm font-bold text-white/70 uppercase tracking-wider mb-4 flex items-center gap-2">
                                        <span class="w-1 h-4 bg-green-500 rounded-full"></span>
                                        Your Profile on File
                                    </h3>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div v-if="existingProfile.applicant_first_name">
                                            <p class="text-gray-500 text-xs">Full Name</p>
                                            <p class="text-white font-medium">
                                                {{ existingProfile.applicant_first_name }} 
                                                {{ existingProfile.applicant_middle_name }} 
                                                {{ existingProfile.applicant_last_name }}
                                            </p>
                                        </div>
                                        <div v-if="existingProfile.date_of_birth">
                                            <p class="text-gray-500 text-xs">Date of Birth</p>
                                            <p class="text-white font-medium">{{ existingProfile.date_of_birth }}</p>
                                        </div>
                                        <div v-if="existingProfile.barangay">
                                            <p class="text-gray-500 text-xs">Barangay</p>
                                            <p class="text-white font-medium">{{ existingProfile.barangay }}</p>
                                        </div>
                                        <div v-if="existingProfile.civil_status">
                                            <p class="text-gray-500 text-xs">Civil Status</p>
                                            <p class="text-white font-medium">{{ existingProfile.civil_status }}</p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-4 italic">
                                        ✓ This information will be used for your {{ selectedDocType }}
                                    </p>
                                </div>

                                <!-- Document-Specific Fields -->
                                <div v-if="documentSpecificFields.length > 0">
                                    <h3 class="text-sm font-bold text-white/50 uppercase tracking-wider mb-4 flex items-center gap-2">
                                        <span class="w-1 h-4 bg-purple-500 rounded-full"></span>
                                        Document Details
                                    </h3>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div v-for="field in documentSpecificFields" :key="field.name"
                                             :class="field.type === 'textarea' ? 'col-span-1 sm:col-span-2' : 'col-span-1'"
                                             class="form-group">
                                            
                                            <label :for="field.name" class="form-label">
                                                {{ field.label }}
                                                <span v-if="field.required !== false" class="text-red-400">*</span>
                                            </label>
                                            
                                            <input 
                                                v-if="['text', 'number', 'date'].includes(field.type)"
                                                :id="field.name"
                                                :type="field.type"
                                                v-model="form.formData[field.name]"
                                                :placeholder="field.placeholder || ''"
                                                class="form-input"
                                                :class="{'border-red-500 ring-red-500/20': formErrors[field.name]}"
                                            />

                                            <select 
                                                v-else-if="field.type === 'select'"
                                                :id="field.name"
                                                v-model="form.formData[field.name]"
                                                class="form-select"
                                                :class="{'border-red-500 ring-red-500/20': formErrors[field.name]}"
                                            >
                                                <option value="" disabled>Select an option</option>
                                                <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
                                            </select>

                                            <textarea 
                                                v-else-if="field.type === 'textarea'"
                                                :id="field.name"
                                                v-model="form.formData[field.name]"
                                                :placeholder="field.placeholder || ''"
                                                rows="2"
                                                class="form-textarea"
                                                :class="{'border-red-500 ring-red-500/20': formErrors[field.name]}"
                                            ></textarea>

                                            <p v-if="formErrors[field.name]" class="form-error">
                                                {{ formErrors[field.name] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Signature Pad -->
                                <div class="mt-6 pt-6 border-t border-white/10">
                                    <h3 class="text-sm font-bold text-white/50 uppercase tracking-wider mb-4 flex items-center gap-2">
                                        <span class="w-1 h-4 bg-blue-500 rounded-full"></span>
                                        Your Signature
                                        <span class="text-red-400">*</span>
                                    </h3>
                                    
                                    <div class="bg-gray-800/50 border-2 border-gray-700 rounded-2xl p-4">
                                        <p class="text-xs text-gray-400 mb-3">Sign below to submit your request</p>
                                        <canvas
                                            ref="signatureCanvas"
                                            @mousedown="startDrawing"
                                            @mousemove="draw"
                                            @mouseup="stopDrawing"
                                            @mouseleave="stopDrawing"
                                            @touchstart.prevent="startDrawing"
                                            @touchmove.prevent="draw"
                                            @touchend.prevent="stopDrawing"
                                            class="w-full h-40 bg-white rounded-xl cursor-crosshair touch-none"
                                        ></canvas>
                                        <div class="flex justify-end mt-3">
                                            <button
                                                type="button"
                                                @click="clearSignature"
                                                class="text-xs text-red-400 hover:text-red-300 font-medium flex items-center gap-1"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Clear Signature
                                            </button>
                                        </div>
                                        <p v-if="formErrors.signature" class="form-error mt-2">
                                            {{ formErrors.signature }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Remarks (Optional) -->
                                <div class="form-group">
                                    <label for="remarks-quick" class="form-label">
                                        Additional Notes <span class="text-gray-500 text-xs">(Optional)</span>
                                    </label>
                                    <textarea 
                                        id="remarks-quick"
                                        v-model="form.remarks" 
                                        rows="3" 
                                        class="form-textarea"
                                        placeholder="Any additional information..."
                                    ></textarea>
                                </div>
                            </div>

                            <!-- Standard Full Form Flow -->
                            <div v-else>
                                <!-- First Half of Fields -->
                                <div v-if="fieldsStep1.length > 0">
                                    <h3 class="text-sm font-bold text-white/50 uppercase tracking-wider mb-4 flex items-center gap-2">
                                        <span class="w-1 h-4 bg-blue-500 rounded-full"></span>
                                        Personal Information
                                    </h3>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div v-for="field in fieldsStep1" :key="`step1-${field.name}`"
                                             :class="field.type === 'textarea' ? 'col-span-1 sm:col-span-2' : 'col-span-1'"
                                             class="form-group">
                                            
                                            <label :for="field.name" class="form-label">
                                                {{ field.label }}
                                                <span v-if="field.required !== false" class="text-red-400">*</span>
                                            </label>
                                            
                                            <input 
                                                v-if="['text', 'number', 'date', 'datetime-local', 'time', 'email'].includes(field.type)"
                                                :id="field.name"
                                                :type="field.type"
                                                v-model="form.formData[field.name]"
                                                :placeholder="field.placeholder || ''"
                                                class="form-input"
                                                :class="{'border-red-500 ring-red-500/20': formErrors[field.name] || form.errors[`data.${field.name}`]}"
                                            />

                                            <select 
                                                v-else-if="field.type === 'select'"
                                                :id="field.name"
                                                v-model="form.formData[field.name]"
                                                class="form-select"
                                                :class="{'border-red-500 ring-red-500/20': formErrors[field.name] || form.errors[`data.${field.name}`]}"
                                            >
                                                <option value="" disabled>Select an option</option>
                                                <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
                                            </select>

                                            <textarea 
                                                v-else-if="field.type === 'textarea'"
                                                :id="field.name"
                                                v-model="form.formData[field.name]"
                                                :placeholder="field.placeholder || ''"
                                                rows="3"
                                                class="form-textarea"
                                                :class="{'border-red-500 ring-red-500/20': formErrors[field.name] || form.errors[`data.${field.name}`]}"
                                            ></textarea>

                                            <p v-if="formErrors[field.name] || form.errors[`data.${field.name}`]" class="form-error">
                                                {{ formErrors[field.name] || form.errors[`data.${field.name}`] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Second Half of Fields -->
                                <div v-if="fieldsStep2.length > 0" class="pt-4 border-t border-white/5">
                                    <h3 class="text-sm font-bold text-white/50 uppercase tracking-wider mb-4 flex items-center gap-2">
                                        <span class="w-1 h-4 bg-purple-500 rounded-full"></span>
                                        Additional Details
                                    </h3>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div v-for="field in fieldsStep2" :key="`step2-${field.name}`"
                                             :class="field.type === 'textarea' ? 'col-span-1 sm:col-span-2' : 'col-span-1'"
                                             class="form-group">
                                            
                                            <label :for="`${field.name}-2`" class="form-label">
                                                {{ field.label }}
                                                <span v-if="field.required !== false" class="text-red-400">*</span>
                                            </label>
                                            
                                            <input 
                                                v-if="['text', 'number', 'date', 'datetime-local', 'time', 'email'].includes(field.type)"
                                                :id="`${field.name}-2`"
                                                :type="field.type"
                                                v-model="form.formData[field.name]"
                                                :placeholder="field.placeholder || ''"
                                                class="form-input"
                                                :class="{'border-red-500 ring-red-500/20': formErrors[field.name] || form.errors[`data.${field.name}`]}"
                                            />

                                            <select 
                                                v-else-if="field.type === 'select'"
                                                :id="`${field.name}-2`"
                                                v-model="form.formData[field.name]"
                                                class="form-select"
                                                :class="{'border-red-500 ring-red-500/20': formErrors[field.name] || form.errors[`data.${field.name}`]}"
                                            >
                                                <option value="" disabled>Select an option</option>
                                                <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
                                            </select>

                                            <textarea 
                                                v-else-if="field.type === 'textarea'"
                                                :id="`${field.name}-2`"
                                                v-model="form.formData[field.name]"
                                                :placeholder="field.placeholder || ''"
                                                rows="3"
                                                class="form-textarea"
                                                :class="{'border-red-500 ring-red-500/20': formErrors[field.name] || form.errors[`data.${field.name}`]}"
                                            ></textarea>

                                            <p v-if="formErrors[field.name] || form.errors[`data.${field.name}`]" class="form-error">
                                                {{ formErrors[field.name] || form.errors[`data.${field.name}`] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Attachments & Remarks -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-white/5">
                                    
                                    <!-- File Upload -->
                                    <div class="form-group">
                                        <label class="form-label">Supporting Documents</label>
                                        <div class="relative">
                                            <input 
                                                type="file" 
                                                @change="handleFileInput"
                                                class="hidden" 
                                                id="file-upload"
                                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                                multiple
                                            />
                                            <label for="file-upload" class="flex flex-col items-center justify-center w-full h-28 sm:h-32 border-2 border-dashed border-white/20 rounded-xl sm:rounded-2xl cursor-pointer hover:border-blue-500/50 hover:bg-blue-500/5 transition-all">
                                                <template v-if="!form.attachments">
                                                    <span class="text-3xl mb-2">📎</span>
                                                    <p class="text-xs text-gray-400">Click to upload</p>
                                                    <p class="text-[10px] text-gray-500 mt-1">Max 10MB</p>
                                                </template>
                                                <template v-else>
                                                <div class="text-center px-4">
                                                    <span class="text-2xl mb-2 block">✅</span>
                                                    <span class="text-xs text-green-400 font-semibold block truncate max-w-[150px]">
                                                        {{ form.attachments.length }} file(s) selected
                                                    </span>
                                                    <p class="text-[10px] text-gray-500 mt-1">Click to change</p>
                                                </div>
                                                </template>
                                            </label>
                                        </div>
                                        <div v-if="form.errors.attachments" class="form-error">{{ form.errors.attachments }}</div>
                                    </div>

                                    <!-- Remarks -->
                                    <div class="form-group">
                                        <label for="remarks" class="form-label">
                                            Additional Notes <span class="text-gray-500 text-xs">(Optional)</span>
                                        </label>
                                        <textarea 
                                            id="remarks"
                                            v-model="form.remarks" 
                                            rows="4" 
                                            class="form-textarea"
                                            placeholder="Any additional information..."
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex gap-3 mt-6 sm:mt-8 pt-6 border-t border-white/10">
                            <button
                                v-if="currentStep > 1"
                                type="button"
                                @click="prevStep"
                                class="flex-1 sm:flex-none sm:px-8 py-3 sm:py-3.5 bg-gray-800 hover:bg-gray-700 text-white font-bold rounded-xl sm:rounded-2xl transition-all duration-200 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                <span class="hidden sm:inline">Back</span>
                            </button>

                            <button
                                v-if="currentStep < 2"
                                type="button"
                                @click="nextStep"
                                class="flex-1 py-3 sm:py-3.5 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-bold rounded-xl sm:rounded-2xl transition-all duration-200 active:scale-95 shadow-lg shadow-blue-500/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 flex items-center justify-center gap-2"
                            >
                                <span>Continue</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>

                            <button
                                v-if="currentStep === 2"
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1 py-3 sm:py-3.5 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold rounded-xl sm:rounded-2xl transition-all duration-200 active:scale-95 shadow-lg shadow-green-500/30 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 flex items-center justify-center gap-2"
                            >
                                <svg v-if="form.processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span v-if="!form.processing">
                                    {{ useQuickSubmit && isReturningUser ? '✓ Sign & Submit' : 'Submit Application' }}
                                </span>
                                <span v-else>Processing...</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Help Text -->
                <p class="text-center text-xs sm:text-sm text-gray-500 mt-4 sm:mt-6">
                    Need help? Contact support at <a href="tel:123-456-7890" class="text-blue-400 hover:text-blue-300 underline">123-456-7890</a>
                </p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Form Components */
.form-group {
    @apply relative;
}

.form-label {
    @apply block text-xs sm:text-sm font-bold text-gray-300 mb-2;
}

.form-input,
.form-select,
.form-textarea {
    @apply w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-800/50 border border-gray-700 rounded-xl sm:rounded-2xl text-white text-sm sm:text-base placeholder-gray-500 transition-all duration-200;
    @apply focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20;
    @apply hover:border-gray-600;
}

.form-select {
    @apply appearance-none pr-10;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-size: 1.25rem;
    background-position: right 0.75rem center;
    background-repeat: no-repeat;
}

.form-textarea {
    @apply resize-none;
}

.form-error {
    @apply text-red-400 text-xs mt-1.5 flex items-center gap-1;
}

.form-error::before {
    content: '⚠️';
    @apply text-xs;
}

/* Animations */
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);
}

@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in-up {
    animation: fade-in-up 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
}

@keyframes slide-down {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-slide-down {
    animation: slide-down 0.5s cubic-bezier(0.2, 0.8, 0.2, 1);
}

/* Mobile Optimizations */
@media (max-width: 640px) {
    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        font-size: 16px;
    }
}

/* Custom Scrollbar */
textarea::-webkit-scrollbar {
    width: 6px;
}

textarea::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 10px;
}

textarea::-webkit-scrollbar-thumb {
    background: rgba(59, 130, 246, 0.4);
    border-radius: 10px;
}
</style>