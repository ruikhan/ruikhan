<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, ref, watch } from 'vue';

const props = defineProps({
    departmentKey: String,
    config: Object,
    isReturningUser: Boolean,
    existingProfile: Object,
    selectedBarangay: String
});

const selectedDocType = ref(props.config.types[0]);
const currentStep = ref(1);
const formErrors = ref({});
const isPageLoaded = ref(false);

// Signature Canvas
const signatureCanvas = ref(null);
const isDrawing = ref(false);
const hasSignature = ref(false);

// User choice for returning users
const useQuickSubmit = ref(false);

onMounted(() => {
    setTimeout(() => {
        isPageLoaded.value = true;
    }, 100);
});

// Computed property to get the current fields based on selected document type
const currentFields = computed(() => {
    if (props.config.type_specific_fields && selectedDocType.value) {
        return props.config.type_specific_fields[selectedDocType.value] || props.config.fields;
    }
    return props.config.fields;
});

// Filter fields for returning users (only document-specific fields)
const documentSpecificFields = computed(() => {
    if (!useQuickSubmit.value || !props.isReturningUser) {
        return [];
    }
    
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

// Helper function with barangay pre-fill support
const initializeFormData = (fields) => {
    const formData = {};
    fields.forEach(field => {
        if (field.type !== 'file') {
            if (field.name === 'barangay' && props.selectedBarangay) {
                formData[field.name] = props.selectedBarangay;
            } else {
                formData[field.name] = field.type === 'number' ? null : '';
            }
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
    use_existing_profile: false,
    signature_data: null
});

// Watch for document type changes
watch(selectedDocType, (newType) => {
    form.document_type = newType;
    const fieldsForType = props.config.type_specific_fields?.[newType] || props.config.fields;
    form.formData = initializeFormData(fieldsForType);
    currentStep.value = 1;
    formErrors.value = {};
    useQuickSubmit.value = false;
    clearSignature();
});

// Watch for quick submit toggle
watch(useQuickSubmit, (newValue) => {
    if (newValue && props.isReturningUser) {
        form.use_existing_profile = true;
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

// Signature Pad Functions
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
        return true;
    }
    
    if (step === 2) {
        if (useQuickSubmit.value && props.isReturningUser) {
            let hasErrors = false;
            
            documentSpecificFields.value.forEach(field => {
                const value = form.formData[field.name];
                const isRequired = field.required !== false;
                
                if (isRequired && (value === null || value === undefined || value === '')) {
                    formErrors.value[field.name] = `${field.label} is required`;
                    hasErrors = true;
                }
            });
            
            if (!hasSignature.value) {
                formErrors.value.signature = 'Please provide your signature';
                hasErrors = true;
            }
            
            return !hasErrors;
        }
        
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
        
        nextTick(() => {
            if (useQuickSubmit.value && props.isReturningUser) {
                setupSignatureCanvas();
            }
        });
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
    
    const oversizedFiles = files.filter(file => file.size > 10 * 1024 * 1024);
    if (oversizedFiles.length > 0) {
        alert(`${oversizedFiles.length} file(s) exceed 10MB limit`);
        event.target.value = '';
        form.attachments = null;
        return;
    }
    
    form.attachments = files;
};

const renderField = (field, suffix = '') => {
    const fieldId = field.name + suffix;
    const hasError = formErrors.value[field.name] || form.errors[`data.${field.name}`];
    
    return {
        id: fieldId,
        hasError: hasError,
        errorMessage: formErrors.value[field.name] || form.errors[`data.${field.name}`]
    };
};
</script>

<template>
    <Head :title="config.title" />

    <AuthenticatedLayout>
        <!-- Enhanced Animated Background -->
        <div class="fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-blue-950/20 to-purple-950/20"></div>
            
            <!-- Animated orbs -->
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse-slow"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/2 w-96 h-96 bg-cyan-500/5 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 4s;"></div>
            
            <!-- Grid pattern -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZGVmcz48cGF0dGVybiBpZD0iZ3JpZCIgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBwYXR0ZXJuVW5pdHM9InVzZXJTcGFjZU9uVXNlIj48cGF0aCBkPSJNIDQwIDAgTCAwIDAgMCA0MCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSJyZ2JhKDI1NSwgMjU1LCAyNTUsIDAuMDMpIiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-20"></div>
        </div>

        <div class="min-h-screen py-6 sm:py-8 lg:py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                
                <!-- Barangay Badge -->
                <Transition name="slide-down">
                    <div v-if="selectedBarangay && departmentKey === 'Barangay Certifications'" 
                         class="mb-6 group">
                        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-teal-500/10 via-cyan-500/10 to-teal-500/10 border border-teal-500/20 backdrop-blur-xl p-4 sm:p-5 shadow-xl shadow-teal-500/5">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-teal-500/20 via-cyan-500/20 to-teal-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            
                            <div class="relative flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 sm:gap-4 min-w-0 flex-1">
                                    <div class="relative flex-shrink-0">
                                        <div class="absolute inset-0 bg-gradient-to-br from-teal-400 to-cyan-500 rounded-2xl blur-lg opacity-50 animate-pulse-slow"></div>
                                        <div class="relative w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-br from-teal-400 to-cyan-500 flex items-center justify-center text-xl sm:text-2xl shadow-xl transform group-hover:scale-110 transition-transform duration-300">
                                            📍
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold uppercase tracking-widest text-teal-400 mb-1">Selected Barangay</p>
                                        <p class="text-lg sm:text-xl font-black text-white truncate">{{ selectedBarangay }}</p>
                                    </div>
                                </div>
                                <Link :href="route('services.landing')" 
                                      class="group/btn relative px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl bg-teal-500/10 hover:bg-teal-500/20 border border-teal-500/30 hover:border-teal-400/50 transition-all duration-300 overflow-hidden flex-shrink-0">
                                    <div class="absolute inset-0 bg-gradient-to-r from-teal-400/0 via-teal-400/10 to-teal-400/0 translate-x-[-100%] group-hover/btn:translate-x-[100%] transition-transform duration-700"></div>
                                    <span class="relative flex items-center gap-2 text-xs font-bold text-teal-400 group-hover/btn:text-teal-300">
                                        <svg class="w-4 h-4 transform group-hover/btn:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        <span class="hidden sm:inline">Change</span>
                                    </span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </Transition>

                <!-- Header Section -->
                <Transition name="fade-up">
                    <div v-if="isPageLoaded" class="text-center mb-6 sm:mb-8 lg:mb-12">
                        <div class="relative inline-block mb-4 sm:mb-6">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 via-purple-500/20 to-pink-500/20 rounded-3xl blur-2xl animate-pulse-slow"></div>
                            <div class="relative w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 bg-gradient-to-br from-slate-900/80 to-slate-800/80 backdrop-blur-xl rounded-3xl flex items-center justify-center border border-white/10 shadow-2xl transform hover:scale-110 transition-transform duration-500">
                                <span class="text-3xl sm:text-4xl lg:text-5xl animate-float">{{ config.icon }}</span>
                            </div>
                        </div>
                        <h1 class="text-2xl sm:text-3xl lg:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 mb-2 sm:mb-3 animate-gradient-x px-4">
                            {{ config.title }}
                        </h1>
                        <p class="text-sm sm:text-base lg:text-lg text-slate-400 max-w-2xl mx-auto px-4">{{ config.description }}</p>
                    </div>
                </Transition>

                <!-- Returning User Banner -->
                <Transition name="slide-down">
                    <div v-if="isReturningUser && departmentKey === 'Barangay Certifications' && currentStep === 1" 
                         class="mb-6 group cursor-default">
                        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-500/10 via-green-500/10 to-emerald-500/10 border border-emerald-500/20 backdrop-blur-xl p-4 sm:p-6 shadow-xl shadow-emerald-500/5">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-400/10 rounded-full blur-3xl animate-pulse-slow"></div>
                            
                            <div class="relative flex items-start gap-3 sm:gap-5">
                                <div class="text-3xl sm:text-5xl animate-wave flex-shrink-0">👋</div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg sm:text-xl font-black text-white mb-2 flex flex-wrap items-center gap-2">
                                        <span>Welcome Back!</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                            VIP Access
                                        </span>
                                    </h3>
                                    <p class="text-sm text-slate-300 leading-relaxed mb-3 sm:mb-4">
                                        We found your information on file. Use <strong class="text-emerald-400">Quick Submit</strong> 
                                        to process your request 2x faster - just sign and submit!
                                    </p>
                                    <div class="flex items-center gap-2 text-xs text-emerald-400">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-semibold">Your profile is up to date</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </Transition>

                <!-- Enhanced Progress Steps -->
                <div class="mb-6 sm:mb-8 animate-fade-in" style="animation-delay: 0.2s;">
                    <div class="relative flex items-center justify-between mb-4 px-4 sm:px-0">
                        <!-- Step 1 -->
                        <div class="flex flex-col items-center relative z-10">
                            <div :class="[
                                'w-10 h-10 sm:w-12 sm:h-12 rounded-2xl font-bold text-xs sm:text-sm transition-all duration-500 flex items-center justify-center shadow-lg',
                                currentStep >= 1 
                                    ? 'bg-gradient-to-br from-blue-500 to-purple-600 text-white scale-110 shadow-blue-500/50' 
                                    : 'bg-slate-800/50 text-slate-600 border-2 border-slate-700'
                            ]">
                                <svg v-if="currentStep > 1" class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span v-else>1</span>
                            </div>
                            <span :class="['mt-1.5 sm:mt-2 text-[10px] sm:text-xs font-bold transition-colors duration-300 text-center max-w-[80px] sm:max-w-none', currentStep >= 1 ? 'text-blue-400' : 'text-slate-600']">
                                Document
                            </span>
                        </div>

                        <!-- Progress Line -->
                        <div class="absolute left-5 right-5 sm:left-6 sm:right-6 top-5 sm:top-6 h-0.5 sm:h-1 bg-slate-800 -z-0">
                            <div 
                                class="h-full bg-gradient-to-r from-blue-500 to-purple-600 transition-all duration-700 ease-out rounded-full shadow-lg shadow-blue-500/50"
                                :style="{ width: currentStep > 1 ? '100%' : '0%' }"
                            ></div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex flex-col items-center relative z-10">
                            <div :class="[
                                'w-10 h-10 sm:w-12 sm:h-12 rounded-2xl font-bold text-xs sm:text-sm transition-all duration-500 flex items-center justify-center shadow-lg',
                                currentStep >= 2 
                                    ? 'bg-gradient-to-br from-blue-500 to-purple-600 text-white scale-110 shadow-purple-500/50' 
                                    : 'bg-slate-800/50 text-slate-600 border-2 border-slate-700'
                            ]">
                                2
                            </div>
                            <span :class="['mt-1.5 sm:mt-2 text-[10px] sm:text-xs font-bold transition-colors duration-300 text-center max-w-[80px] sm:max-w-none', currentStep >= 2 ? 'text-purple-400' : 'text-slate-600']">
                                {{ useQuickSubmit && isReturningUser ? 'Submit' : 'Details' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Main Form Card -->
                <Transition name="scale-in">
                    <div v-if="isPageLoaded" class="relative group">
                        <!-- Glow effect -->
                        <div class="absolute -inset-0.5 sm:-inset-1 bg-gradient-to-r from-blue-600/20 via-purple-600/20 to-pink-600/20 rounded-3xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                        
                        <div class="relative bg-slate-900/50 backdrop-blur-2xl border border-white/10 rounded-2xl sm:rounded-3xl shadow-2xl overflow-hidden">
                            
                            <!-- Card Header -->
                            <div class="relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 via-purple-600/10 to-pink-600/10"></div>
                                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZGVmcz48cGF0dGVybiBpZD0iZ3JpZCIgd2lkdGg9IjIwIiBoZWlnaHQ9IjIwIiBwYXR0ZXJuVW5pdHM9InVzZXJTcGFjZU9uVXNlIj48cGF0aCBkPSJNIDIwIDAgTCAwIDAgMCAyMCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSJyZ2JhKDI1NSwgMjU1LCAyNTUsIDAuMDMpIiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"></div>
                                
                                <div class="relative px-4 sm:px-6 py-4 sm:py-5 border-b border-white/5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-xl sm:text-2xl shadow-lg animate-float flex-shrink-0">
                                            {{ currentStep === 1 ? '📋' : (useQuickSubmit && isReturningUser ? '✍️' : '📝') }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h2 class="text-base sm:text-lg font-black text-white truncate">
                                                {{ currentStep === 1 ? 'Select Document Type' : (useQuickSubmit && isReturningUser ? 'Quick Submit' : 'Required Information') }}
                                            </h2>
                                            <p class="text-xs text-slate-400">Step {{ currentStep }} of 2</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form @submit.prevent="submit" class="p-4 sm:p-6 lg:p-8">
                                
                                <!-- Step 1: Document Type Selection -->
                                <Transition name="fade-slide" mode="out-in">
                                    <div v-if="currentStep === 1" key="step1" class="space-y-5 sm:space-y-6">
                                        
                                        <!-- Document Type Cards -->
                                        <div class="form-group">
                                            <label class="block text-sm font-bold text-slate-200 mb-3">
                                                <span class="flex items-center gap-2">
                                                    Type of Document 
                                                    <span class="text-red-400">*</span>
                                                    <span class="ml-auto text-xs text-slate-500 font-normal">{{ config.types.length }} options</span>
                                                </span>
                                            </label>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                <button
                                                    v-for="(type, index) in config.types"
                                                    :key="type"
                                                    type="button"
                                                    @click="selectedDocType = type"
                                                    :style="{ animationDelay: `${index * 50}ms` }"
                                                    :class="[
                                                        'relative group/card overflow-hidden rounded-xl sm:rounded-2xl border-2 transition-all duration-300 text-left animate-fade-in-up',
                                                        selectedDocType === type
                                                            ? 'bg-gradient-to-br from-blue-500/20 to-purple-500/20 border-blue-500 shadow-lg shadow-blue-500/20 scale-[1.02]'
                                                            : 'bg-slate-800/30 border-slate-700/50 hover:border-slate-600 hover:bg-slate-800/50 active:scale-[0.98]'
                                                    ]"
                                                >
                                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent translate-x-[-100%] group-hover/card:translate-x-[100%] transition-transform duration-1000"></div>
                                                    
                                                    <div class="relative p-3 sm:p-4 flex items-center gap-3 sm:gap-4">
                                                        <div :class="[
                                                            'w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center text-xl sm:text-2xl flex-shrink-0 transition-all duration-300',
                                                            selectedDocType === type 
                                                                ? 'bg-blue-500/30 shadow-lg shadow-blue-500/30 scale-110' 
                                                                : 'bg-slate-700/30 group-hover/card:scale-110'
                                                        ]">
                                                            📄
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <span class="text-xs sm:text-sm font-bold text-white block truncate">{{ type }}</span>
                                                            <span class="text-[10px] sm:text-xs text-slate-400">{{ currentFields.length }} fields required</span>
                                                        </div>
                                                        <Transition name="scale-bounce">
                                                            <div v-if="selectedDocType === type" class="flex-shrink-0">
                                                                <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-blue-500 flex items-center justify-center shadow-lg shadow-blue-500/50">
                                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                        </Transition>
                                                    </div>
                                                </button>
                                            </div>
                                            
                                            <Transition name="fade">
                                                <div v-if="formErrors.document_type || form.errors.document_type" class="mt-2 text-sm text-red-400 flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                    </svg>
                                                    {{ formErrors.document_type || form.errors.document_type }}
                                                </div>
                                            </Transition>
                                        </div>

                                        <!-- Submission Method (Returning Users) -->
                                        <Transition name="fade-slide">
                                            <div v-if="isReturningUser && departmentKey === 'Barangay Certifications' && selectedDocType" 
                                                 class="form-group">
                                                <label class="block text-sm font-bold text-slate-200 mb-3">Choose Submission Method</label>
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                                                    <!-- Quick Submit -->
                                                    <button
                                                        type="button"
                                                        @click="useQuickSubmit = true"
                                                        :class="[
                                                            'relative group/method overflow-hidden rounded-xl sm:rounded-2xl border-2 transition-all duration-300 text-left p-4 sm:p-6',
                                                            useQuickSubmit
                                                                ? 'bg-gradient-to-br from-emerald-500/20 to-green-500/20 border-emerald-500 shadow-lg shadow-emerald-500/20'
                                                                : 'bg-slate-800/30 border-slate-700/50 hover:border-slate-600 active:scale-[0.98]'
                                                        ]"
                                                    >
                                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent translate-x-[-100%] group-hover/method:translate-x-[100%] transition-transform duration-1000"></div>
                                                        
                                                        <div class="relative">
                                                            <div class="flex items-center gap-2 sm:gap-3 mb-3 sm:mb-4">
                                                                <div :class="[
                                                                    'w-12 h-12 sm:w-14 sm:h-14 rounded-xl flex items-center justify-center text-2xl sm:text-3xl transition-all duration-300 flex-shrink-0',
                                                                    useQuickSubmit ? 'bg-emerald-500/30 scale-110' : 'bg-slate-700/30'
                                                                ]">
                                                                    ⚡
                                                                </div>
                                                                <div class="flex-1 min-w-0">
                                                                    <span class="text-sm sm:text-base font-black text-white block">Quick Submit</span>
                                                                    <span class="text-[10px] sm:text-xs text-slate-400">Lightning fast</span>
                                                                </div>
                                                                <Transition name="scale-bounce">
                                                                    <div v-if="useQuickSubmit" class="flex-shrink-0">
                                                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                                        </svg>
                                                                    </div>
                                                                </Transition>
                                                            </div>
                                                            <div class="space-y-1 sm:space-y-1.5 text-[10px] sm:text-xs text-slate-400">
                                                                <p class="flex items-center gap-1.5 sm:gap-2">
                                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                    <span>Uses your saved information</span>
                                                                </p>
                                                                <p class="flex items-center gap-1.5 sm:gap-2">
                                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                    <span>Just add purpose & sign</span>
                                                                </p>
                                                                <p class="flex items-center gap-1.5 sm:gap-2">
                                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                    <span>2x faster processing</span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </button>

                                                    <!-- Full Form -->
                                                    <button
                                                        type="button"
                                                        @click="useQuickSubmit = false"
                                                        :class="[
                                                            'relative group/method overflow-hidden rounded-xl sm:rounded-2xl border-2 transition-all duration-300 text-left p-4 sm:p-6',
                                                            !useQuickSubmit
                                                                ? 'bg-gradient-to-br from-blue-500/20 to-purple-500/20 border-blue-500 shadow-lg shadow-blue-500/20'
                                                                : 'bg-slate-800/30 border-slate-700/50 hover:border-slate-600 active:scale-[0.98]'
                                                        ]"
                                                    >
                                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent translate-x-[-100%] group-hover/method:translate-x-[100%] transition-transform duration-1000"></div>
                                                        
                                                        <div class="relative">
                                                            <div class="flex items-center gap-2 sm:gap-3 mb-3 sm:mb-4">
                                                                <div :class="[
                                                                    'w-12 h-12 sm:w-14 sm:h-14 rounded-xl flex items-center justify-center text-2xl sm:text-3xl transition-all duration-300 flex-shrink-0',
                                                                    !useQuickSubmit ? 'bg-blue-500/30 scale-110' : 'bg-slate-700/30'
                                                                ]">
                                                                    📝
                                                                </div>
                                                                <div class="flex-1 min-w-0">
                                                                    <span class="text-sm sm:text-base font-black text-white block">Complete Form</span>
                                                                    <span class="text-[10px] sm:text-xs text-slate-400">Full control</span>
                                                                </div>
                                                                <Transition name="scale-bounce">
                                                                    <div v-if="!useQuickSubmit" class="flex-shrink-0">
                                                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                                        </svg>
                                                                    </div>
                                                                </Transition>
                                                            </div>
                                                            <div class="space-y-1 sm:space-y-1.5 text-[10px] sm:text-xs text-slate-400">
                                                                <p class="flex items-center gap-1.5 sm:gap-2">
                                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                    <span>Update your information</span>
                                                                </p>
                                                                <p class="flex items-center gap-1.5 sm:gap-2">
                                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                    <span>Add new details</span>
                                                                </p>
                                                                <p class="flex items-center gap-1.5 sm:gap-2">
                                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                    <span>Full control over data</span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </div>
                                            </div>
                                        </Transition>

                                        <!-- Info Box -->
                                        <Transition name="fade-slide">
                                            <div v-if="selectedDocType" class="relative overflow-hidden rounded-xl sm:rounded-2xl bg-gradient-to-r from-blue-500/10 to-purple-500/10 border border-blue-500/20 p-4 sm:p-5">
                                                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-400/10 rounded-full blur-3xl"></div>
                                                <div class="relative flex items-start gap-3 sm:gap-4">
                                                    <div class="text-2xl sm:text-3xl animate-pulse-slow flex-shrink-0">ℹ️</div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-blue-200 text-xs sm:text-sm font-bold mb-1">Selected: {{ selectedDocType }}</p>
                                                        <p class="text-blue-300/70 text-[10px] sm:text-xs leading-relaxed">
                                                            Click Continue to {{ useQuickSubmit && isReturningUser ? 'sign and submit your request' : 'fill out the required information' }}.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </Transition>
                                    </div>
                                </Transition>

                                <!-- Step 2: Form Fields or Quick Submit -->
                                <Transition name="fade-slide" mode="out-in">
                                    <div v-if="currentStep === 2" key="step2" class="space-y-5 sm:space-y-6">
                                        
                                        <!-- Quick Submit Mode -->
                                        <template v-if="useQuickSubmit && isReturningUser">
                                            <div class="relative overflow-hidden rounded-xl sm:rounded-2xl bg-gradient-to-r from-emerald-500/5 to-green-500/5 border border-emerald-500/20 p-4 sm:p-5 mb-5 sm:mb-6">
                                                <div class="flex items-start gap-3 sm:gap-4">
                                                    <div class="text-2xl sm:text-3xl flex-shrink-0">⚡</div>
                                                    <div class="flex-1 min-w-0">
                                                        <h3 class="text-sm sm:text-base font-bold text-emerald-400 mb-1">Quick Submit Mode</h3>
                                                        <p class="text-[10px] sm:text-xs text-slate-400">We're using your saved profile. Just fill in the document-specific details and sign below.</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Document-Specific Fields -->
                                            <div v-if="documentSpecificFields.length > 0" class="space-y-4 sm:space-y-5">
                                                <div
                                                    v-for="(field, index) in documentSpecificFields"
                                                    :key="field.name"
                                                    :style="{ animationDelay: `${index * 30}ms` }"
                                                    class="animate-fade-in-up"
                                                >
                                                    <component 
                                                        :is="'div'" 
                                                        class="form-group"
                                                    >
                                                        <label :for="field.name + '-quick'" class="block text-xs sm:text-sm font-bold text-slate-200 mb-2">
                                                            {{ field.label }}
                                                            <span v-if="field.required !== false" class="text-red-400 ml-1">*</span>
                                                        </label>

                                                        <!-- Text Input -->
                                                        <input
                                                            v-if="field.type === 'text'"
                                                            :id="field.name + '-quick'"
                                                            v-model="form.formData[field.name]"
                                                            type="text"
                                                            :placeholder="field.placeholder || field.label"
                                                            :class="[
                                                                'w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-slate-800/50 border rounded-xl text-sm text-white placeholder-slate-500 transition-all duration-300 focus:outline-none',
                                                                (formErrors[field.name] || form.errors[`data.${field.name}`])
                                                                    ? 'border-red-500/50 focus:border-red-500 focus:ring-2 focus:ring-red-500/20'
                                                                    : 'border-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20'
                                                            ]"
                                                        />

                                                        <!-- Textarea -->
                                                        <textarea
                                                            v-else-if="field.type === 'textarea'"
                                                            :id="field.name + '-quick'"
                                                            v-model="form.formData[field.name]"
                                                            :rows="field.rows || 3"
                                                            :placeholder="field.placeholder || field.label"
                                                            :class="[
                                                                'w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-slate-800/50 border rounded-xl text-sm text-white placeholder-slate-500 transition-all duration-300 resize-none focus:outline-none',
                                                                (formErrors[field.name] || form.errors[`data.${field.name}`])
                                                                    ? 'border-red-500/50 focus:border-red-500 focus:ring-2 focus:ring-red-500/20'
                                                                    : 'border-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20'
                                                            ]"
                                                        ></textarea>

                                                        <!-- Select -->
                                                        <select
                                                            v-else-if="field.type === 'select'"
                                                            :id="field.name + '-quick'"
                                                            v-model="form.formData[field.name]"
                                                            :class="[
                                                                'w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-slate-800/50 border rounded-xl text-sm text-white transition-all duration-300 focus:outline-none',
                                                                (formErrors[field.name] || form.errors[`data.${field.name}`])
                                                                    ? 'border-red-500/50 focus:border-red-500 focus:ring-2 focus:ring-red-500/20'
                                                                    : 'border-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20'
                                                            ]"
                                                        >
                                                            <option value="">Select {{ field.label }}</option>
                                                            <option v-for="option in field.options" :key="option" :value="option">
                                                                {{ option }}
                                                            </option>
                                                        </select>

                                                        <!-- Number Input -->
                                                        <input
                                                            v-else-if="field.type === 'number'"
                                                            :id="field.name + '-quick'"
                                                            v-model="form.formData[field.name]"
                                                            type="number"
                                                            :placeholder="field.placeholder || field.label"
                                                            :class="[
                                                                'w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-slate-800/50 border rounded-xl text-sm text-white placeholder-slate-500 transition-all duration-300 focus:outline-none',
                                                                (formErrors[field.name] || form.errors[`data.${field.name}`])
                                                                    ? 'border-red-500/50 focus:border-red-500 focus:ring-2 focus:ring-red-500/20'
                                                                    : 'border-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20'
                                                            ]"
                                                        />

                                                        <!-- Date Input -->
                                                        <input
                                                            v-else-if="field.type === 'date'"
                                                            :id="field.name + '-quick'"
                                                            v-model="form.formData[field.name]"
                                                            type="date"
                                                            :class="[
                                                                'w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-slate-800/50 border rounded-xl text-sm text-white transition-all duration-300 focus:outline-none',
                                                                (formErrors[field.name] || form.errors[`data.${field.name}`])
                                                                    ? 'border-red-500/50 focus:border-red-500 focus:ring-2 focus:ring-red-500/20'
                                                                    : 'border-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20'
                                                            ]"
                                                        />

                                                        <!-- Error Message -->
                                                        <Transition name="fade">
                                                            <div v-if="formErrors[field.name] || form.errors[`data.${field.name}`]" class="mt-1.5 text-xs text-red-400 flex items-center gap-1.5">
                                                                <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                                </svg>
                                                                {{ formErrors[field.name] || form.errors[`data.${field.name}`] }}
                                                            </div>
                                                        </Transition>
                                                    </component>
                                                </div>
                                            </div>

                                            <!-- Signature Pad -->
                                            <div class="form-group mt-6 sm:mt-8">
                                                <label class="block text-xs sm:text-sm font-bold text-slate-200 mb-3">
                                                    Your Signature
                                                    <span class="text-red-400 ml-1">*</span>
                                                </label>
                                                <div :class="[
                                                    'relative overflow-hidden rounded-xl border-2 transition-all duration-300',
                                                    formErrors.signature ? 'border-red-500/50' : 'border-slate-700'
                                                ]">
                                                    <canvas
                                                        ref="signatureCanvas"
                                                        @mousedown="startDrawing"
                                                        @mousemove="draw"
                                                        @mouseup="stopDrawing"
                                                        @mouseleave="stopDrawing"
                                                        @touchstart="startDrawing"
                                                        @touchmove="draw"
                                                        @touchend="stopDrawing"
                                                        class="w-full h-40 sm:h-48 bg-slate-800/30 cursor-crosshair touch-none"
                                                    ></canvas>
                                                    
                                                    <!-- Clear Button -->
                                                    <button
                                                        v-if="hasSignature"
                                                        type="button"
                                                        @click="clearSignature"
                                                        class="absolute top-2 right-2 p-2 bg-red-500/20 hover:bg-red-500/30 border border-red-500/50 rounded-lg transition-all duration-300 active:scale-95"
                                                    >
                                                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                    
                                                    <!-- Placeholder Text -->
                                                    <div v-if="!hasSignature" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                                        <p class="text-slate-500 text-xs sm:text-sm font-medium">Sign here with your mouse or finger</p>
                                                    </div>
                                                </div>
                                                <Transition name="fade">
                                                    <div v-if="formErrors.signature" class="mt-1.5 text-xs text-red-400 flex items-center gap-1.5">
                                                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                        </svg>
                                                        {{ formErrors.signature }}
                                                    </div>
                                                </Transition>
                                            </div>
                                        </template>

                                        <!-- Full Form Mode -->
                                        <template v-else>
                                            <div class="space-y-4 sm:space-y-5">
                                                <div
                                                    v-for="(field, index) in currentFields"
                                                    :key="field.name"
                                                    :style="{ animationDelay: `${index * 30}ms` }"
                                                    class="animate-fade-in-up"
                                                >
                                                    <component 
                                                        :is="'div'" 
                                                        class="form-group"
                                                    >
                                                        <label :for="field.name" class="block text-xs sm:text-sm font-bold text-slate-200 mb-2">
                                                            {{ field.label }}
                                                            <span v-if="field.required !== false" class="text-red-400 ml-1">*</span>
                                                        </label>

                                                        <!-- Text Input -->
                                                        <input
                                                            v-if="field.type === 'text'"
                                                            :id="field.name"
                                                            v-model="form.formData[field.name]"
                                                            type="text"
                                                            :placeholder="field.placeholder || field.label"
                                                            :disabled="field.name === 'barangay' && selectedBarangay"
                                                            :class="[
                                                                'w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-slate-800/50 border rounded-xl text-sm text-white placeholder-slate-500 transition-all duration-300 focus:outline-none',
                                                                (formErrors[field.name] || form.errors[`data.${field.name}`])
                                                                    ? 'border-red-500/50 focus:border-red-500 focus:ring-2 focus:ring-red-500/20'
                                                                    : 'border-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20',
                                                                field.name === 'barangay' && selectedBarangay && 'opacity-75 cursor-not-allowed'
                                                            ]"
                                                        />

                                                        <!-- Textarea -->
                                                        <textarea
                                                            v-else-if="field.type === 'textarea'"
                                                            :id="field.name"
                                                            v-model="form.formData[field.name]"
                                                            :rows="field.rows || 3"
                                                            :placeholder="field.placeholder || field.label"
                                                            :class="[
                                                                'w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-slate-800/50 border rounded-xl text-sm text-white placeholder-slate-500 transition-all duration-300 resize-none focus:outline-none',
                                                                (formErrors[field.name] || form.errors[`data.${field.name}`])
                                                                    ? 'border-red-500/50 focus:border-red-500 focus:ring-2 focus:ring-red-500/20'
                                                                    : 'border-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20'
                                                            ]"
                                                        ></textarea>

                                                        <!-- Select -->
                                                        <select
                                                            v-else-if="field.type === 'select'"
                                                            :id="field.name"
                                                            v-model="form.formData[field.name]"
                                                            :class="[
                                                                'w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-slate-800/50 border rounded-xl text-sm text-white transition-all duration-300 focus:outline-none',
                                                                (formErrors[field.name] || form.errors[`data.${field.name}`])
                                                                    ? 'border-red-500/50 focus:border-red-500 focus:ring-2 focus:ring-red-500/20'
                                                                    : 'border-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20'
                                                            ]"
                                                        >
                                                            <option value="">Select {{ field.label }}</option>
                                                            <option v-for="option in field.options" :key="option" :value="option">
                                                                {{ option }}
                                                            </option>
                                                        </select>

                                                        <!-- Number Input -->
                                                        <input
                                                            v-else-if="field.type === 'number'"
                                                            :id="field.name"
                                                            v-model="form.formData[field.name]"
                                                            type="number"
                                                            :placeholder="field.placeholder || field.label"
                                                            :class="[
                                                                'w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-slate-800/50 border rounded-xl text-sm text-white placeholder-slate-500 transition-all duration-300 focus:outline-none',
                                                                (formErrors[field.name] || form.errors[`data.${field.name}`])
                                                                    ? 'border-red-500/50 focus:border-red-500 focus:ring-2 focus:ring-red-500/20'
                                                                    : 'border-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20'
                                                            ]"
                                                        />

                                                        <!-- Date Input -->
                                                        <input
                                                            v-else-if="field.type === 'date'"
                                                            :id="field.name"
                                                            v-model="form.formData[field.name]"
                                                            type="date"
                                                            :class="[
                                                                'w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-slate-800/50 border rounded-xl text-sm text-white transition-all duration-300 focus:outline-none',
                                                                (formErrors[field.name] || form.errors[`data.${field.name}`])
                                                                    ? 'border-red-500/50 focus:border-red-500 focus:ring-2 focus:ring-red-500/20'
                                                                    : 'border-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20'
                                                            ]"
                                                        />

                                                        <!-- Error Message -->
                                                        <Transition name="fade">
                                                            <div v-if="formErrors[field.name] || form.errors[`data.${field.name}`]" class="mt-1.5 text-xs text-red-400 flex items-center gap-1.5">
                                                                <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                                </svg>
                                                                {{ formErrors[field.name] || form.errors[`data.${field.name}`] }}
                                                            </div>
                                                        </Transition>
                                                    </component>
                                                </div>
                                            </div>

                                            <!-- Optional Attachments -->
                                            <div class="form-group mt-6">
                                                <label class="block text-xs sm:text-sm font-bold text-slate-200 mb-2">
                                                    Attachments (Optional)
                                                    <span class="text-xs text-slate-500 font-normal ml-2">Max 10MB per file</span>
                                                </label>
                                                <div class="relative">
                                                    <input
                                                        type="file"
                                                        @change="handleFileInput"
                                                        multiple
                                                        accept="image/*,.pdf,.doc,.docx"
                                                        class="hidden"
                                                        id="file-upload"
                                                    />
                                                    <label
                                                        for="file-upload"
                                                        class="flex items-center justify-center gap-3 w-full px-4 py-6 border-2 border-dashed border-slate-700 hover:border-blue-500 rounded-xl bg-slate-800/30 hover:bg-slate-800/50 transition-all duration-300 cursor-pointer group"
                                                    >
                                                        <svg class="w-8 h-8 text-slate-500 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                                        </svg>
                                                        <div class="text-center">
                                                            <p class="text-sm font-bold text-slate-300 group-hover:text-white transition-colors">Click to upload files</p>
                                                            <p class="text-xs text-slate-500 mt-0.5">or drag and drop</p>
                                                        </div>
                                                    </label>
                                                </div>
                                                <div v-if="form.attachments && form.attachments.length > 0" class="mt-3 space-y-2">
                                                    <div v-for="(file, index) in form.attachments" :key="index" class="flex items-center gap-3 p-3 bg-slate-800/50 rounded-lg border border-slate-700/50">
                                                        <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm text-white truncate">{{ file.name }}</p>
                                                            <p class="text-xs text-slate-500">{{ (file.size / 1024 / 1024).toFixed(2) }} MB</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Optional Remarks -->
                                            <div class="form-group">
                                                <label for="remarks" class="block text-xs sm:text-sm font-bold text-slate-200 mb-2">
                                                    Additional Remarks (Optional)
                                                </label>
                                                <textarea
                                                    id="remarks"
                                                    v-model="form.remarks"
                                                    rows="3"
                                                    placeholder="Add any additional information here..."
                                                    class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 transition-all duration-300 resize-none focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                                ></textarea>
                                            </div>
                                        </template>
                                    </div>
                                </Transition>

                                <!-- Navigation Buttons -->
                                <div class="flex gap-3 mt-8 pt-6 border-t border-white/5">
                                    <Transition name="slide-left">
                                        <button
                                            v-if="currentStep > 1"
                                            type="button"
                                            @click="prevStep"
                                            class="group px-6 py-3.5 bg-slate-800/50 hover:bg-slate-700/50 text-white font-bold rounded-xl border border-slate-700/50 hover:border-slate-600 transition-all duration-300 active:scale-95 flex items-center gap-2"
                                        >
                                            <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                            </svg>
                                            <span>Back</span>
                                        </button>
                                    </Transition>

                                    <button
                                        v-if="currentStep < 2"
                                        type="button"
                                        @click="nextStep"
                                        class="group flex-1 py-3.5 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-bold rounded-xl transition-all duration-300 active:scale-95 shadow-lg shadow-blue-500/30 flex items-center justify-center gap-2 relative overflow-hidden"
                                    >
                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                                        <span class="relative">Continue</span>
                                        <svg class="relative w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>

                                    <button
                                        v-if="currentStep === 2"
                                        type="submit"
                                        :disabled="form.processing"
                                        class="group flex-1 py-3.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold rounded-xl transition-all duration-300 active:scale-95 shadow-lg shadow-emerald-500/30 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 relative overflow-hidden"
                                    >
                                        <div v-if="!form.processing" class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                                        <svg v-if="form.processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="relative">{{ form.processing ? 'Submitting...' : 'Submit Request' }}</span>
                                        <svg v-if="!form.processing" class="relative w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Animations */
@keyframes pulse-slow {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

@keyframes gradient-x {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

@keyframes wave {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(20deg); }
    75% { transform: rotate(-15deg); }
}

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-pulse-slow { animation: pulse-slow 3s ease-in-out infinite; }
.animate-float { animation: float 3s ease-in-out infinite; }
.animate-gradient-x { 
    animation: gradient-x 3s ease infinite; 
    background-size: 200% 200%; 
}
.animate-wave { animation: wave 0.5s ease-in-out 2; }
.animate-fade-in-up { animation: fade-in-up 0.5s ease-out; }
.animate-fade-in { animation: fade-in-up 0.3s ease-out; }

/* Vue Transitions */
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.fade-slide-enter-active, .fade-slide-leave-active { transition: all 0.3s; }
.fade-slide-enter-from { opacity: 0; transform: translateY(10px); }
.fade-slide-leave-to { opacity: 0; transform: translateY(-10px); }

.slide-down-enter-active, .slide-down-leave-active { transition: all 0.3s; }
.slide-down-enter-from { opacity: 0; transform: translateY(-20px); }
.slide-down-leave-to { opacity: 0; transform: translateY(-20px); }

.fade-up-enter-active { transition: all 0.5s ease-out; }
.fade-up-enter-from { opacity: 0; transform: translateY(30px); }

.scale-in-enter-active { transition: all 0.3s; }
.scale-in-enter-from { opacity: 0; transform: scale(0.95); }

.scale-bounce-enter-active { transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55); }
.scale-bounce-enter-from { opacity: 0; transform: scale(0); }

.slide-left-enter-active { transition: all 0.3s; }
.slide-left-enter-from { opacity: 0; transform: translateX(-20px); }
</style>