<script setup>
import { BanknotesIcon, BeakerIcon, BuildingOfficeIcon, CheckCircleIcon, ClockIcon, DevicePhoneMobileIcon, DocumentTextIcon, HomeIcon, ShieldCheckIcon, UserGroupIcon } from '@heroicons/vue/24/solid';
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true
    },
    department: {
        type: String,
        required: true
    },
    documentType: {
        type: String,
        required: true
    },
    requestData: {
        type: Object,
        default: () => ({})
    },
    timestamps: {
        type: Object,
        default: () => ({})
    }
});

// Department-specific workflow configurations
const workflowConfigs = {
    'Barangay Certifications': {
        steps: [
            {
                id: 1,
                title: 'Online Submission',
                description: 'Application received and encrypted in the system.',
                icon: DocumentTextIcon,
                matchStatus: ['pending']
            },
            {
                id: 2,
                title: props.requestData?.request_level === 'Barangay' ? 'Barangay Verification' : 'Municipal LGU Review',
                description: props.requestData?.request_level === 'Barangay' 
                    ? 'Barangay Secretary validates residency and records.'
                    : 'Municipal office reviews application and supporting documents.',
                icon: props.requestData?.request_level === 'Barangay' ? UserGroupIcon : BuildingOfficeIcon,
                matchStatus: ['processing', 'under_review']
            },
            {
                id: 3,
                title: 'Document Preparation',
                description: 'Certificate is being generated with official seal and signatures.',
                icon: DocumentTextIcon,
                matchStatus: ['approved', 'printing']
            },
            {
                id: 4,
                title: 'Ready for Release',
                description: 'Certificate ready. Notification sent via SMS/Email.',
                icon: DevicePhoneMobileIcon,
                matchStatus: ['ready_for_pickup', 'completed']
            }
        ],
        scenarios: {
            1: {
                actor: "System Gateway",
                action: "Application Logged & Queued",
                detail: `Your ${props.documentType} application has been securely received and is now in the processing queue.`
            },
            2: {
                actor: props.requestData?.request_level === 'Barangay' ? "Barangay Secretary" : "Municipal LGU Officer",
                action: "Validation in Progress",
                detail: props.requestData?.request_level === 'Barangay'
                    ? "Staff is verifying your residency status, checking barangay records, and validating your supporting documents."
                    : "Municipal office is reviewing your application details and cross-referencing with official records."
            },
            3: {
                actor: "Document Officer",
                action: "Official Certificate Generation",
                detail: `Your ${props.documentType} is being printed on official paper with security features and dry seal.`
            },
            4: {
                actor: "Release Counter",
                action: "Ready for Claiming",
                detail: "Your certificate is ready. Please bring valid ID and Official Receipt when claiming."
            }
        }
    },

    'Municipal Civil Registrar': {
        steps: [
            {
                id: 1,
                title: 'Request Submission',
                description: 'Civil registry document request received.',
                icon: DocumentTextIcon,
                matchStatus: ['pending']
            },
            {
                id: 2,
                title: 'Record Search',
                description: 'Civil Registrar searching archives and PSA database.',
                icon: BeakerIcon,
                matchStatus: ['processing', 'searching_records']
            },
            {
                id: 3,
                title: 'Document Certification',
                description: 'Certified true copy prepared by Civil Registrar.',
                icon: ShieldCheckIcon,
                matchStatus: ['approved', 'certifying']
            },
            {
                id: 4,
                title: 'Payment & Release',
                description: 'Payment verified. Document ready for claiming.',
                icon: BanknotesIcon,
                matchStatus: ['ready_for_pickup', 'completed']
            }
        ],
        scenarios: {
            1: {
                actor: "Records System",
                action: "Request Registered",
                detail: `Your request for ${props.documentType} has been logged. Payment details and requirements are being verified.`
            },
            2: {
                actor: "Civil Registrar",
                action: "Archive Search & Verification",
                detail: "The Civil Registrar is searching municipal archives and coordinating with PSA for certified records."
            },
            3: {
                actor: "Registrar Officer",
                action: "Certification & Authentication",
                detail: "Document found and verified. Preparing certified true copy with official seal and registrar's signature."
            },
            4: {
                actor: "Release Window",
                action: "Ready for Claiming",
                detail: "Your certified document is ready. Please proceed to the Civil Registry Office with your Official Receipt."
            }
        }
    },

    'Business Permits (BPLO)': {
        steps: [
            {
                id: 1,
                title: 'Application Received',
                description: 'Business permit application submitted online.',
                icon: DocumentTextIcon,
                matchStatus: ['pending']
            },
            {
                id: 2,
                title: 'Multi-Office Clearance',
                description: 'Coordinating with Barangay, Fire, Health, and Zoning offices.',
                icon: BuildingOfficeIcon,
                matchStatus: ['processing', 'clearance_coordination']
            },
            {
                id: 3,
                title: 'Assessment & Payment',
                description: 'Fees assessed by Treasurer. Awaiting payment confirmation.',
                icon: BanknotesIcon,
                matchStatus: ['for_payment', 'payment_verification']
            },
            {
                id: 4,
                title: 'Permit Issuance',
                description: 'Business Permit and Mayor’s Permit ready for release.',
                icon: CheckCircleIcon,
                matchStatus: ['ready_for_pickup', 'completed']
            }
        ],
        scenarios: {
            1: {
                actor: "BPLO System",
                action: "Application Under Review",
                detail: "Your business permit application is being reviewed for completeness and accuracy."
            },
            2: {
                actor: "Clearance Coordination Team",
                action: "Multi-Department Processing",
                detail: "Your application is being forwarded to Barangay, BFP, Health Office, and MPDO for required clearances."
            },
            3: {
                actor: "Treasury Office",
                action: "Fee Assessment Complete",
                detail: "Business taxes and permit fees have been computed. Please settle payment to proceed with permit issuance."
            },
            4: {
                actor: "BPLO Release",
                action: "Business Permit Ready",
                detail: "Your Business Permit and Mayor's Permit are ready for claiming. Display prominently at your business location."
            }
        }
    },

    'Engineering Office': {
        steps: [
            {
                id: 1,
                title: 'Permit Application',
                description: 'Building/construction permit application submitted.',
                icon: DocumentTextIcon,
                matchStatus: ['pending']
            },
            {
                id: 2,
                title: 'Plan Review',
                description: 'Municipal Engineer reviewing plans and specifications.',
                icon: BuildingOfficeIcon,
                matchStatus: ['processing', 'plan_review']
            },
            {
                id: 3,
                title: 'Site Inspection',
                description: 'Field inspection scheduled. Engineer will visit the site.',
                icon: HomeIcon,
                matchStatus: ['for_inspection', 'inspection_scheduled']
            },
            {
                id: 4,
                title: 'Permit Approved',
                description: 'Permit issued. Construction may commence.',
                icon: CheckCircleIcon,
                matchStatus: ['approved', 'ready_for_pickup', 'completed']
            }
        ],
        scenarios: {
            1: {
                actor: "Engineering Records",
                action: "Application Logged",
                detail: `Your ${props.documentType} application is being logged. Plans and documents are being reviewed for completeness.`
            },
            2: {
                actor: "Municipal Engineer",
                action: "Technical Plan Review",
                detail: "Engineer is reviewing structural plans, compliance with building codes, and zoning requirements."
            },
            3: {
                actor: "Field Inspector",
                action: "On-Site Assessment",
                detail: "Site inspection will be conducted to verify actual conditions and compliance with submitted plans."
            },
            4: {
                actor: "Engineering Office",
                action: "Permit Released",
                detail: "Construction permit approved and released. Please display at the construction site and comply with all conditions."
            }
        }
    },

    'Municipal Health Office': {
        steps: [
            {
                id: 1,
                title: 'Health Application',
                description: 'Health certificate application submitted.',
                icon: DocumentTextIcon,
                matchStatus: ['pending']
            },
            {
                id: 2,
                title: 'Medical Examination',
                description: 'Schedule appointment for health screening.',
                icon: BeakerIcon,
                matchStatus: ['for_examination', 'scheduled']
            },
            {
                id: 3,
                title: 'Laboratory Results',
                description: 'Lab results being processed and reviewed by health officer.',
                icon: DocumentTextIcon,
                matchStatus: ['processing', 'lab_review']
            },
            {
                id: 4,
                title: 'Certificate Issuance',
                description: 'Health Certificate ready for claiming.',
                icon: CheckCircleIcon,
                matchStatus: ['approved', 'ready_for_pickup', 'completed']
            }
        ],
        scenarios: {
            1: {
                actor: "Health Records",
                action: "Application Received",
                detail: "Your health certificate application has been received. Please proceed to the Health Office for medical examination."
            },
            2: {
                actor: "Medical Technologist",
                action: "Health Screening",
                detail: "Medical examination scheduled. Please bring valid ID and payment for laboratory fees."
            },
            3: {
                actor: "Health Officer",
                action: "Results Evaluation",
                detail: "Laboratory results are being reviewed by the Municipal Health Officer for certification."
            },
            4: {
                actor: "Health Office",
                action: "Certificate Ready",
                detail: "Your Health Certificate has been approved and is ready for release. Valid for one year from date of issuance."
            }
        }
    },

    'Social Welfare (MSWDO)': {
        steps: [
            {
                id: 1,
                title: 'Assistance Request',
                description: 'Social welfare application submitted.',
                icon: DocumentTextIcon,
                matchStatus: ['pending']
            },
            {
                id: 2,
                title: 'Case Assessment',
                description: 'Social Worker conducting home visit and needs assessment.',
                icon: UserGroupIcon,
                matchStatus: ['processing', 'assessment']
            },
            {
                id: 3,
                title: 'Approval Process',
                description: 'MSWDO Officer reviewing case for approval.',
                icon: ShieldCheckIcon,
                matchStatus: ['for_approval', 'manager_review']
            },
            {
                id: 4,
                title: 'Assistance Released',
                description: 'Financial aid or certificate ready for claiming.',
                icon: CheckCircleIcon,
                matchStatus: ['approved', 'ready_for_pickup', 'completed']
            }
        ],
        scenarios: {
            1: {
                actor: "MSWDO Reception",
                action: "Request Registered",
                detail: `Your ${props.documentType} request has been received and is being reviewed by the social welfare team.`
            },
            2: {
                actor: "Social Worker",
                action: "Home Visit & Documentation",
                detail: "A social worker will conduct a home visit to assess your situation and validate your application."
            },
            3: {
                actor: "MSWDO Head",
                action: "Case Evaluation",
                detail: "Your case is under review. The MSWDO officer is evaluating your eligibility for assistance."
            },
            4: {
                actor: "MSWDO Release",
                action: "Assistance Approved",
                detail: "Your request has been approved. Please claim your certificate/assistance at the MSWDO office with valid ID."
            }
        }
    },

    // Default workflow for other departments
    'default': {
        steps: [
            {
                id: 1,
                title: 'Request Submitted',
                description: 'Your application has been received by the system.',
                icon: DocumentTextIcon,
                matchStatus: ['pending']
            },
            {
                id: 2,
                title: 'Document Processing',
                description: 'Staff is reviewing and processing your request.',
                icon: BuildingOfficeIcon,
                matchStatus: ['processing', 'under_review']
            },
            {
                id: 3,
                title: 'Final Approval',
                description: 'Document approved and being prepared for release.',
                icon: CheckCircleIcon,
                matchStatus: ['approved']
            },
            {
                id: 4,
                title: 'Ready for Pickup',
                description: 'Document ready. Notification sent.',
                icon: DevicePhoneMobileIcon,
                matchStatus: ['ready_for_pickup', 'completed']
            }
        ],
        scenarios: {
            1: {
                actor: "System",
                action: "Request Logged",
                detail: "Your request has been logged in the system and is awaiting staff review."
            },
            2: {
                actor: "Processing Officer",
                action: "Document Review",
                detail: "Your application is being reviewed and validated by the department staff."
            },
            3: {
                actor: "Approving Officer",
                action: "Final Approval",
                detail: "Your document has been approved and is being prepared for release."
            },
            4: {
                actor: "Release Window",
                action: "Ready for Claiming",
                detail: "Your document is ready. Please proceed to the office to claim with valid ID."
            }
        }
    }
};

// Get current workflow based on department
const currentWorkflow = computed(() => {
    return workflowConfigs[props.department] || workflowConfigs['default'];
});

// Get steps from current workflow
const steps = computed(() => currentWorkflow.value.steps);

// Computed: Determine the "Current Active Step" index based on status
const currentStepIndex = computed(() => {
    const currentSteps = steps.value;
    
    // Find the step that matches the current status
    for (let i = currentSteps.length - 1; i >= 0; i--) {
        if (currentSteps[i].matchStatus.includes(props.status)) {
            return i;
        }
    }
    
    // Default to first step if no match
    return 0;
});

// Computed: Get the specific "Action Scenario" text for the current state
const currentScenario = computed(() => {
    const activeStep = steps.value[currentStepIndex.value];
    const scenarios = currentWorkflow.value.scenarios;
    return scenarios[activeStep.id] || scenarios[1];
});
</script>

<template>
    <div class="space-y-8">
        
        <!-- Live Status Card -->
        <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-2xl p-6 border border-white/10 shadow-xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl -mr-16 -mt-16 animate-pulse"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    <h3 class="text-xs font-bold text-green-400 uppercase tracking-widest">Live Action Scenario</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <div class="text-[10px] text-slate-500 uppercase font-bold mb-1">Current Actor</div>
                        <div class="text-lg font-bold text-white flex items-center gap-2">
                            {{ currentScenario.actor }}
                        </div>
                    </div>
                    
                    <div class="md:col-span-2">
                        <div class="text-[10px] text-slate-500 uppercase font-bold mb-1">Ongoing System Operation</div>
                        <div class="text-blue-300 font-medium text-sm mb-2">
                            {{ currentScenario.action }}
                        </div>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            {{ currentScenario.detail }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Workflow Progress -->
        <div class="relative">
            <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-slate-700"></div>

            <div class="space-y-8">
                <div v-for="(step, index) in steps" :key="step.id" class="relative flex gap-6 group">
                    
                    <!-- Step Circle -->
                    <div 
                        class="relative z-10 flex-shrink-0 w-12 h-12 rounded-full border-4 flex items-center justify-center transition-all duration-500"
                        :class="{
                            'bg-green-500 border-slate-900 text-white': index < currentStepIndex,
                            'bg-blue-600 border-blue-900 text-white ring-4 ring-blue-500/20 animate-pulse': index === currentStepIndex,
                            'bg-slate-800 border-slate-900 text-slate-500': index > currentStepIndex
                        }"
                    >
                        <component :is="index < currentStepIndex ? CheckCircleIcon : step.icon" class="w-5 h-5" />
                    </div>

                    <!-- Step Details -->
                    <div class="pt-1 flex-1">
                        <h4 
                            class="text-sm font-bold uppercase tracking-wide transition-colors"
                            :class="{
                                'text-green-400': index < currentStepIndex,
                                'text-blue-400': index === currentStepIndex,
                                'text-slate-500': index > currentStepIndex
                            }"
                        >
                            Step {{ step.id }}: {{ step.title }}
                        </h4>
                        
                        <p class="text-slate-400 text-sm mt-1 max-w-md">
                            {{ step.description }}
                        </p>

                        <div v-if="index <= currentStepIndex" class="mt-2 flex items-center gap-1 text-[10px] text-slate-500 font-mono">
                            <ClockIcon class="w-3 h-3" />
                            <span v-if="index === currentStepIndex" class="text-blue-400 font-semibold">In Progress...</span>
                            <span v-else class="text-green-400">✓ Completed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>