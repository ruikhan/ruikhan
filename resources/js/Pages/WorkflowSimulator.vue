<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, onUnmounted, ref } from 'vue';

// --- STATE MANAGEMENT ---
const activeScenarioId = ref(null);
const isRunning = ref(false);
const isPaused = ref(false);
const progress = ref(0); 
const currentStepIndex = ref(0);

let simulationInterval = null;

// --- ENHANCED SCENARIO DATABASE (Pili, CamSur Municipal Government) ---
const scenarios = {
    barangay_cert: {
        id: 'barangay_cert', 
        color: 'teal', 
        icon: '🏘️', 
        title: 'Barangay Certificate',
        subtitle: 'Certificate of Residency',
        userStory: 'Maria needs a Certificate of Residency for her job application at SM City Naga.',
        character: {
            name: 'Maria Santos',
            role: 'Resident of Brgy. San Jose',
            avatar: '👩'
        },
        steps: [
            {
                number: 1,
                title: 'Online Submission',
                bubble: 'I\'ll fill out my information online instead of going to the barangay hall!',
                visual: '📱',
                action: '📝',
                result: '✅',
                description: 'Maria logs into the E-PILI portal and selects "Certificate of Residency". She fills out her personal details, address, and residency duration. She uploads her PhilID and latest Cedula as supporting documents.',
                technical: 'System encrypts form data, validates required fields, generates tracking code BRGY-20251222-A1B2, and stores in PostgreSQL database with status="pending".'
            },
            {
                number: 2,
                title: 'Barangay Secretary Verification',
                bubble: 'Let me check Maria\'s records in our barangay database...',
                visual: '👨‍💼',
                action: '🔍',
                result: '✓',
                description: 'The Barangay Secretary receives a notification. He opens Maria\'s application and cross-references with the barangay census records, blotter database, and residency logs to verify she is a legitimate resident with good standing.',
                technical: 'Backend queries barangay_residents table, checks blotter_records for any incidents, validates residency_duration >= 6 months. Auto-flags as "verified" if all checks pass.'
            },
            {
                number: 3,
                title: 'Document Generation',
                bubble: 'Generating official certificate with dry seal and signature...',
                visual: '🖨️',
                action: '🏵️',
                result: '📄',
                description: 'The system generates a Certificate of Residency with Maria\'s details, official barangay seal, Punong Barangay\'s digital signature, and a unique O.R. number. The document is formatted according to LGU Pili standards.',
                technical: 'PDF generator uses Laravel DomPDF, embeds QR code for verification, applies watermark, saves to storage/certificates/BRGY-20251222-A1B2.pdf with encryption.'
            },
            {
                number: 4,
                title: 'Notification & Release',
                bubble: 'My certificate is ready! I got an SMS and email notification.',
                visual: '📧',
                action: '📲',
                result: '🎉',
                description: 'Maria receives an SMS and email notification that her certificate is ready. She can download it from the portal or claim a printed copy at the barangay hall. The certificate shows status "Ready for Pickup" with a pickup code.',
                technical: 'Event triggers: NewCertificateReady → Notification service sends SMS via Semaphore API + Email via SMTP. Status updated to "completed".'
            }
        ],
        impact: 'Maria received her certificate in 2 hours without visiting the barangay office. She can now proceed with her job application.',
        stats: [ 
            { label: 'Processing Time', value: 2, suffix: 'hrs', icon: '⏱️' }, 
            { label: 'Office Visits', value: 0, suffix: '', icon: '🚶' }, 
            { label: 'Documents', value: 1, suffix: '', icon: '📄' },
            { label: 'Fee Paid', value: 50, suffix: '₱', icon: '💰' }
        ]
    },

    civil_registry: {
        id: 'civil_registry', 
        color: 'pink', 
        icon: '📜', 
        title: 'Civil Registry Document',
        subtitle: 'Birth Certificate Request',
        userStory: 'Juan needs his birth certificate for passport application at DFA Manila.',
        character: {
            name: 'Juan Dela Cruz',
            role: 'Registered in Pili',
            avatar: '👨'
        },
        steps: [
            {
                number: 1,
                title: 'Document Request',
                bubble: 'I need my birth certificate. Let me request it online.',
                visual: '📱',
                action: '📝',
                result: '🎫',
                description: 'Juan accesses the Civil Registry portal and requests a birth certificate. He provides his full name, date of birth, parents\' names, and the purpose (passport application). The system generates tracking code MCR-20251222-M5N6.',
                technical: 'Form validation ensures all PSA-required fields are complete. System checks if requestor has valid ID. Creates record in document_requests table with department="Municipal Civil Registrar".'
            },
            {
                number: 2,
                title: 'Archive Search',
                bubble: 'Searching municipal archives for birth record...',
                visual: '🗄️',
                action: '🔍',
                result: '📋',
                description: 'The Civil Registrar searches the municipal archives and PSA database for Juan\'s birth record. They verify the details match and locate the original registration from 1995. The record is found and flagged as "Located".',
                technical: 'SQL query: SELECT * FROM birth_records WHERE full_name LIKE "%Juan Dela Cruz%" AND birth_date="1995-05-15". If not found locally, triggers PSA API integration for certified copy retrieval.'
            },
            {
                number: 3,
                title: 'Certification',
                bubble: 'Preparing certified true copy with official seal...',
                visual: '🏛️',
                action: '✍️',
                result: '🏵️',
                description: 'The Municipal Civil Registrar prepares a certified true copy of the birth certificate. The document is printed on security paper, stamped with the official dry seal, and signed by the Civil Registrar Officer.',
                technical: 'PDF generation includes: Registry Number, Book Number, Page Number, certified statement "This is to certify that this is a true copy...", digital signature of Civil Registrar with timestamp.'
            },
            {
                number: 4,
                title: 'Payment & Release',
                bubble: 'Payment verified! I can now claim my birth certificate.',
                visual: '💳',
                action: '🎫',
                result: '📄',
                description: 'Juan pays the documentary stamp tax (₱30) and certification fee (₱100) online. After payment verification, the certificate is marked ready for claiming. He receives his OR number and can pick up the document at the Civil Registry Office.',
                technical: 'Payment gateway integration with GCash/PayMaya. Upon successful payment, status="ready_for_pickup". SMS notification sent with OR number and claiming instructions.'
            }
        ],
        impact: 'Juan obtained his certified birth certificate in 1 day instead of the usual 3-5 days. He can now proceed with his passport application.',
        stats: [ 
            { label: 'Processing Time', value: 1, suffix: 'day', icon: '⏱️' }, 
            { label: 'Archive Search', value: 5, suffix: 'min', icon: '🔍' }, 
            { label: 'Total Fee', value: 130, suffix: '₱', icon: '💰' },
            { label: 'Visits Saved', value: 2, suffix: '', icon: '🚶' }
        ]
    },

    business_permit: {
        id: 'business_permit', 
        color: 'blue', 
        icon: '💼', 
        title: 'Business Permit',
        subtitle: 'New Business Registration',
        userStory: 'Rosa wants to open a sari-sari store in Brgy. San Antonio and needs a business permit.',
        character: {
            name: 'Rosa Martinez',
            role: 'Aspiring Entrepreneur',
            avatar: '👩‍💼'
        },
        steps: [
            {
                number: 1,
                title: 'Business Application',
                bubble: 'I\'ll apply for my business permit online!',
                visual: '📱',
                action: '📝',
                result: '🏪',
                description: 'Rosa fills out the business permit application form with her business details: store name "Rosa\'s Sari-Sari Store", business address, TIN, and type of business. She uploads her barangay clearance, DTI registration, and valid ID.',
                technical: 'Form captures: business_name, business_address, owner_name, tin, business_type="Sole Proprietorship", line_of_business="Retail - General Merchandise". Tracking: BPLO-20251222-B7C8.'
            },
            {
                number: 2,
                title: 'Multi-Office Clearance',
                bubble: 'Coordinating with Barangay, Fire, Health, and Zoning offices...',
                visual: '🏢',
                action: '📋',
                result: '✅',
                description: 'The BPLO system automatically routes Rosa\'s application to multiple offices for clearance: Barangay (community clearance), BFP (fire safety), Health Office (sanitary permit), and MPDO (zoning compliance). Each office reviews and approves in parallel.',
                technical: 'Parallel workflow triggers: barangay_clearance_status, fire_clearance_status, health_clearance_status, zoning_clearance_status. All must be "approved" before proceeding to next step.'
            },
            {
                number: 3,
                title: 'Fee Assessment',
                bubble: 'Treasurer has computed my business taxes and fees.',
                visual: '💰',
                action: '🧮',
                result: '💳',
                description: 'The Treasury Office receives the approved application and assesses Rosa\'s business permit fees based on her gross sales projection and business type. The computation includes: business tax, mayor\'s permit fee, sanitary permit fee, and garbage fee.',
                technical: 'Algorithm calculates: business_tax = (gross_sales * 0.02), mayors_permit = 500, sanitary_fee = 300, garbage_fee = 200. Total = ₱1,000 for small retail business.'
            },
            {
                number: 4,
                title: 'Permit Issuance',
                bubble: 'My business permit is ready! Time to open my store!',
                visual: '🎉',
                action: '🖨️',
                result: '📜',
                description: 'After payment confirmation, the BPLO generates Rosa\'s Business Permit and Mayor\'s Permit. Both documents include her business details, validity period (1 year), and official seals. She can now legally operate her sari-sari store.',
                technical: 'PDF generation creates two documents: business_permit.pdf and mayors_permit.pdf. Both include QR codes for verification, expiry_date = current_date + 365 days. Status = "completed".'
            }
        ],
        impact: 'Rosa completed her business registration in 3 days through coordinated online processing with multiple departments.',
        stats: [ 
            { label: 'Departments', value: 5, suffix: '', icon: '🏢' }, 
            { label: 'Processing Days', value: 3, suffix: '', icon: '📅' }, 
            { label: 'Total Fees', value: 1000, suffix: '₱', icon: '💰' },
            { label: 'Manual Steps', value: 0, suffix: '', icon: '✅' }
        ]
    },

    building_permit: {
        id: 'building_permit', 
        color: 'orange', 
        icon: '🏗️', 
        title: 'Building Permit',
        subtitle: 'Residential Construction',
        userStory: 'Pedro wants to build a 2-storey house in Brgy. Del Rosario and needs a building permit.',
        character: {
            name: 'Pedro Reyes',
            role: 'Property Owner',
            avatar: '👨‍🔧'
        },
        steps: [
            {
                number: 1,
                title: 'Permit Application',
                bubble: 'Uploading my house plans and architectural drawings...',
                visual: '📐',
                action: '📤',
                result: '📋',
                description: 'Pedro submits his building permit application online with complete documents: architectural plans, structural design, electrical layout, plumbing plans, lot title, and contractor\'s license. The system generates tracking code ENG-20251222-H3J4.',
                technical: 'File uploads accept PDF and AutoCAD formats. System validates: floor_area <= 200sqm, estimated_cost, structure_type="Residential". Documents stored in storage/building_permits/.'
            },
            {
                number: 2,
                title: 'Plan Review',
                bubble: 'Municipal Engineer reviewing structural plans for code compliance...',
                visual: '👷',
                action: '📏',
                result: '✓',
                description: 'The Municipal Engineer reviews Pedro\'s architectural and structural plans against the National Building Code. They check: foundation design, structural integrity, setback requirements, and zoning compliance. The plans are marked "Approved" after review.',
                technical: 'Engineer dashboard shows plan_review_status with checklist: structural_compliance, setback_requirements (3m front, 2m sides), floor_area_ratio, building_height <= 3_storeys. All must be checked before approval.'
            },
            {
                number: 3,
                title: 'Site Inspection',
                bubble: 'Scheduling site visit to verify actual lot conditions...',
                visual: '🚗',
                action: '📍',
                result: '📸',
                description: 'A field inspector is assigned to visit Pedro\'s property. They verify the lot boundaries, check that the actual site matches the submitted plans, take photos for documentation, and confirm that there are no existing violations or obstructions.',
                technical: 'Inspection scheduling system assigns nearest available inspector. Mobile app for inspectors captures GPS coordinates, timestamped photos, and checklist: lot_verified, access_road_adequate, no_existing_violations.'
            },
            {
                number: 4,
                title: 'Permit Issuance',
                bubble: 'Building permit approved! Construction can begin!',
                visual: '🎉',
                action: '📜',
                result: '🏗️',
                description: 'After successful site inspection and payment of permit fees (₱5,000), the Engineering Office issues the Building Permit. The permit includes all approved plans, construction schedule, and conditions. Pedro can now legally start construction.',
                technical: 'Generate building_permit.pdf with: permit_number, validity_period (1 year), approved_plans_reference, special_conditions, occupancy_limit. QR code links to digital copy. Status = "approved".'
            }
        ],
        impact: 'Pedro obtained his building permit in 7 days through streamlined online processing and coordinated site inspection.',
        stats: [ 
            { label: 'Plan Review', value: 3, suffix: 'days', icon: '📐' }, 
            { label: 'Site Inspection', value: 1, suffix: 'day', icon: '📍' }, 
            { label: 'Permit Fee', value: 5000, suffix: '₱', icon: '💰' },
            { label: 'Validity', value: 1, suffix: 'year', icon: '📅' }
        ]
    },

    health_cert: {
        id: 'health_cert', 
        color: 'emerald', 
        icon: '🩺', 
        title: 'Health Certificate',
        subtitle: 'Food Handler Certification',
        userStory: 'Anna needs a Health Certificate to work as a cook at Jollibee Pili.',
        character: {
            name: 'Anna Garcia',
            role: 'Job Applicant',
            avatar: '👩‍🍳'
        },
        steps: [
            {
                number: 1,
                title: 'Health Application',
                bubble: 'Booking my medical examination appointment online...',
                visual: '📱',
                action: '📅',
                result: '🗓️',
                description: 'Anna applies for a Health Certificate through the portal. She selects "Food Handler" as the purpose and books an appointment for medical examination at the Municipal Health Office. She receives a confirmation with her appointment date and time.',
                technical: 'Appointment booking system checks available slots, assigns doctor schedule. Sends calendar invite and SMS reminder 1 day before. Tracking: MHO-20251222-F9G0.'
            },
            {
                number: 2,
                title: 'Medical Examination',
                bubble: 'Undergoing physical exam and laboratory tests...',
                visual: '🏥',
                action: '🩺',
                result: '📊',
                description: 'Anna visits the Health Office for her scheduled medical exam. The health staff conducts physical examination, blood pressure check, and collects samples for laboratory tests (CBC, urinalysis, chest X-ray). Results are uploaded to her digital health record.',
                technical: 'Medical officer inputs: blood_pressure, height, weight, physical_exam_results. Lab orders created in LIMS (Laboratory Information Management System). X-ray scheduled with radiology.'
            },
            {
                number: 3,
                title: 'Laboratory Results',
                bubble: 'All my lab results came back normal!',
                visual: '🧪',
                action: '✅',
                result: '📋',
                description: 'After 2 days, Anna\'s laboratory results are complete and reviewed by the Municipal Health Officer. All results are within normal limits: no signs of tuberculosis, negative for parasites, and fit for food handling. Her health record is marked "Cleared for Certification".',
                technical: 'Lab results API integration: cbc_result="normal", urinalysis="negative", xray_interpretation="lungs_clear". Health Officer reviews and approves via digital dashboard.'
            },
            {
                number: 4,
                title: 'Certificate Release',
                bubble: 'Got my Health Certificate! Ready to start my new job!',
                visual: '🎉',
                action: '📄',
                result: '✅',
                description: 'The Health Office generates Anna\'s Health Certificate valid for 1 year. It includes her health examination results summary, medical findings, and certification that she is "Fit to Work" as a food handler. Anna downloads her certificate and submits it to her employer.',
                technical: 'PDF generation includes: certificate_number, health_findings, fit_for_work_status, validity_date, health_officer_signature, official_seal. Watermark: "Valid for 1 year from date of issuance".'
            }
        ],
        impact: 'Anna completed her health certification process in 3 days with scheduled appointments and digital results tracking.',
        stats: [ 
            { label: 'Total Time', value: 3, suffix: 'days', icon: '⏱️' }, 
            { label: 'Lab Tests', value: 3, suffix: '', icon: '🧪' }, 
            { label: 'Certificate Fee', value: 200, suffix: '₱', icon: '💰' },
            { label: 'Validity', value: 1, suffix: 'year', icon: '📅' }
        ]
    },

    indigency: {
        id: 'indigency', 
        color: 'cyan', 
        icon: '🤝', 
        title: 'Social Welfare',
        subtitle: 'Certificate of Indigency',
        userStory: 'Lita needs a Certificate of Indigency for her son\'s scholarship application at Ateneo.',
        character: {
            name: 'Lita Mendoza',
            role: 'Single Mother',
            avatar: '👩‍👦'
        },
        steps: [
            {
                number: 1,
                title: 'Assistance Request',
                bubble: 'Applying for indigency certificate for my son\'s scholarship...',
                visual: '📱',
                action: '📝',
                result: '📋',
                description: 'Lita applies for a Certificate of Indigency through the MSWDO portal. She provides her family details: monthly household income (₱3,000), number of dependents (3 children), and purpose (educational assistance). She uploads supporting documents: utility bills and barangay indigency.',
                technical: 'Form captures: income_status="Low Income", monthly_income=3000, dependents=3, purpose="Scholarship/Educational Assistance". System flags as priority case if income < poverty_threshold.'
            },
            {
                number: 2,
                title: 'Case Assessment',
                bubble: 'Social worker will visit our home for verification...',
                visual: '👨‍💼',
                action: '🏠',
                result: '📝',
                description: 'A social worker is assigned to Lita\'s case. They schedule a home visit to assess the family\'s living conditions, interview family members, and document their situation. The social worker takes photos, validates income sources, and prepares a case report.',
                technical: 'Social worker app records: home_visit_date, living_conditions_assessment, income_verification, family_composition. GPS-tagged photos uploaded to case file. Report submitted to MSWDO head.'
            },
            {
                number: 3,
                title: 'Approval Process',
                bubble: 'MSWDO Officer reviewing my case for approval...',
                visual: '👨‍💻',
                action: '✓',
                result: '✅',
                description: 'The MSWDO Head reviews the social worker\'s report and Lita\'s application. They verify that her family meets the criteria for indigency certification (income below poverty line, legitimate need). The case is approved and marked ready for certificate issuance.',
                technical: 'Approval workflow: IF monthly_income < 12000 AND dependents >= 2 AND home_visit_verified = true THEN status="approved". Digital approval with MSWDO head signature timestamp.'
            },
            {
                number: 4,
                title: 'Certificate Release',
                bubble: 'Certificate issued! My son can now apply for the scholarship!',
                visual: '🎉',
                action: '📄',
                result: '🎓',
                description: 'The MSWDO generates Lita\'s Certificate of Indigency. It states that her family is "Below Poverty Threshold" and qualifies for educational assistance. The certificate is signed by the MSWDO Officer and the Municipal Mayor. Lita receives it free of charge.',
                technical: 'Certificate generation includes: case_number, family_income_statement, certification_statement, purpose="Educational Assistance", validity="Good for 6 months". No fee charged for indigency certificates.'
            }
        ],
        impact: 'Lita received her indigency certificate in 4 days through proper assessment and home visit verification.',
        stats: [ 
            { label: 'Home Visit', value: 1, suffix: 'day', icon: '🏠' }, 
            { label: 'Processing', value: 4, suffix: 'days', icon: '⏱️' }, 
            { label: 'Certificate Fee', value: 0, suffix: '₱', icon: '🆓' },
            { label: 'Validity', value: 6, suffix: 'months', icon: '📅' }
        ]
    }
};

// --- COMPUTED PROPERTIES ---
const activeConfig = computed(() => activeScenarioId.value ? scenarios[activeScenarioId.value] : null);
const currentStep = computed(() => {
    if (!activeConfig.value || currentStepIndex.value >= activeConfig.value.steps.length) return null;
    return activeConfig.value.steps[currentStepIndex.value];
});

const isComplete = computed(() => {
    return activeConfig.value && currentStepIndex.value >= activeConfig.value.steps.length;
});

// --- SIMULATION ENGINE ---
const startScenario = (id) => {
    resetSimulation();
    activeScenarioId.value = id;
    isRunning.value = true;
    isPaused.value = false;
    
    simulationInterval = setInterval(() => {
        if (isPaused.value) return;
        
        progress.value += 0.8;
        
        if (progress.value >= 100 && currentStepIndex.value < activeConfig.value.steps.length - 1) {
            currentStepIndex.value++;
            progress.value = 0;
        } else if (progress.value >= 100 && currentStepIndex.value === activeConfig.value.steps.length - 1) {
            clearInterval(simulationInterval);
            currentStepIndex.value++;
        }
    }, 50);
};

const pauseSimulation = () => { isPaused.value = true; };
const resumeSimulation = () => { isPaused.value = false; };
const resetSimulation = () => {
    clearInterval(simulationInterval);
    isRunning.value = false;
    isPaused.value = false;
    progress.value = 0;
    currentStepIndex.value = 0;
    activeScenarioId.value = null;
};

onUnmounted(() => clearInterval(simulationInterval));
</script>

<template>
    <Head title="E-PILI Workflow Simulator" />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-8 px-4 sm:py-12 sm:px-6 font-sans">
        <div class="max-w-7xl mx-auto">

            <!-- Header -->
            <div class="text-center mb-12">
                <div class="flex items-center justify-between mb-6">
                    <Link :href="route('prototype')" 
                          class="group flex items-center gap-2 px-4 py-2 bg-white border-2 border-slate-200 hover:border-blue-400 rounded-xl text-slate-700 hover:text-blue-600 font-semibold transition-all hover:shadow-lg">
                        <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back to Dashboard
                    </Link>
                    
                    <Link :href="route('services.landing')" 
                          class="group flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-xl text-white font-semibold transition-all hover:shadow-lg">
                        Browse Services
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </Link>
                </div>

                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold mb-4">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75" v-if="isRunning && !isPaused"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                    </span>
                    E-PILI Government Services
                </div>
                
                <h1 class="text-4xl sm:text-5xl font-black text-slate-900 mb-4">
                    Workflow Process Simulator
                </h1>
                
                <p class="text-lg text-slate-600 max-w-3xl mx-auto">
                    See how Pili, Camarines Sur residents interact with digital government services step-by-step.
                </p>
            </div>

            <!-- Scenario Selection Grid -->
            <div v-if="!isRunning" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                <button v-for="scenario in scenarios" :key="scenario.id" 
                        @click="startScenario(scenario.id)"
                        class="group relative p-6 bg-white rounded-2xl border-2 border-slate-200 hover:border-blue-400 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl text-left">
                    <div class="text-4xl mb-3">{{ scenario.icon }}</div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">{{ scenario.title }}</h3>
                    <p class="text-sm text-slate-500 mb-2">{{ scenario.subtitle }}</p>
                    <p class="text-xs text-slate-400 mb-4 line-clamp-2">{{ scenario.userStory }}</p>
                    <div class="flex items-center gap-2 text-blue-600 text-sm font-semibold">
                        <span>Start Simulation</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </div>
                </button>
            </div>

            <!-- Active Simulation -->
            <div v-else class="max-w-5xl mx-auto">
                
                <!-- Progress Bar -->
                <div class="mb-8 bg-white rounded-2xl p-6 shadow-lg border border-slate-200">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-2xl font-bold text-slate-900">{{ activeConfig.title }}</h2>
                            <p class="text-sm text-slate-500">{{ activeConfig.subtitle }}</p>
                        </div>
                        <div class="text-4xl">{{ activeConfig.icon }}</div>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
                        <p class="text-sm text-slate-700"><strong>User Story:</strong> {{ activeConfig.userStory }}</p>
                    </div>
                    
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-sm font-semibold text-slate-700">Step {{ Math.min(currentStepIndex + 1, activeConfig.steps.length) }} of {{ activeConfig.steps.length }}</span>
                        <div class="flex-1 h-2 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r transition-all duration-300"
                                 :class="`from-${activeConfig.color}-500 to-${activeConfig.color}-400`"
                                 :style="{ width: `${((currentStepIndex / activeConfig.steps.length) * 100) + (progress / activeConfig.steps.length)}%` }"></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mt-4">
                        <button @click="isPaused ? resumeSimulation() : pauseSimulation()" 
                                class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 transition-colors text-sm font-medium">
                            {{ isPaused ? '▶ Resume' : '⏸ Pause' }}
                        </button>
                        <button @click="resetSimulation" 
                                class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 transition-colors text-sm font-medium">
                            ↺ Reset
                        </button>
                    </div>
                </div>

                <!-- Comic Panel Style Step Display -->
                <Transition name="slide-fade" mode="out-in">
                    <div v-if="!isComplete" :key="currentStepIndex" class="bg-white rounded-2xl shadow-xl border-4 border-slate-900 overflow-hidden">
                        
                        <!-- Panel Header -->
                        <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-xl font-bold">
                                    {{ currentStep.number }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg">{{ currentStep.title }}</h3>
                                    <p class="text-sm text-slate-300">{{ activeConfig.character.name }} • {{ activeConfig.character.role }}</p>
                                </div>
                            </div>
                            <div class="text-2xl">{{ activeConfig.character.avatar }}</div>
                        </div>

                        <!-- Comic Panel Content -->
                        <div class="p-8">
                            
                            <!-- Visual Flow -->
                            <div class="flex items-center justify-center gap-6 mb-8 bg-slate-50 rounded-xl p-8 border-2 border-slate-200">
                                <div class="text-6xl animate-bounce">{{ currentStep.visual }}</div>
                                <div class="text-5xl text-slate-400">→</div>
                                <div class="text-6xl animate-pulse">{{ currentStep.action }}</div>
                                <div class="text-5xl text-slate-400">→</div>
                                <div class="text-6xl animate-bounce delay-100">{{ currentStep.result }}</div>
                            </div>

                            <!-- Speech Bubble -->
                            <div class="relative bg-blue-50 border-3 border-blue-900 rounded-3xl p-6 mb-6">
                                <div class="absolute -top-3 left-8 w-0 h-0 border-l-[15px] border-l-transparent border-r-[15px] border-r-transparent border-b-[20px] border-b-blue-900"></div>
                                <div class="absolute -top-2 left-8.5 w-0 h-0 border-l-[13px] border-l-transparent border-r-[13px] border-r-transparent border-b-[18px] border-b-blue-50"></div>
                                <p class="text-xl font-medium text-slate-900 italic">"{{ currentStep.bubble }}"</p>
                            </div>

                            <!-- Description -->
                            <div class="mb-6">
                                <h4 class="font-bold text-slate-900 mb-2 flex items-center gap-2">
                                    <span class="text-blue-500">📖</span>
                                    What's Happening?
                                </h4>
                                <p class="text-slate-700 leading-relaxed">{{ currentStep.description }}</p>
                            </div>

                            <!-- Technical Details -->
                            <div class="bg-slate-900 text-green-400 rounded-xl p-4 font-mono text-sm">
                                <div class="flex items-start gap-2">
                                    <span class="text-green-500">⚙️</span>
                                    <div>
                                        <span class="text-slate-500">//</span> <span class="text-green-300">Technical Implementation:</span>
                                        <p class="mt-1">{{ currentStep.technical }}</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Completion Screen -->
                    <div v-else key="complete" class="bg-white rounded-2xl shadow-xl border-4 border-green-500 p-12 text-center">
                        <div class="text-7xl mb-6 animate-bounce">🎉</div>
                        <h2 class="text-4xl font-black text-slate-900 mb-4">Process Complete!</h2>
                        <p class="text-xl text-slate-600 mb-8 max-w-2xl mx-auto">{{ activeConfig.impact }}</p>
                        
                        <!-- Impact Stats -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 max-w-3xl mx-auto">
                            <div v-for="(stat, index) in activeConfig.stats" :key="index" 
                                 class="bg-slate-50 rounded-xl p-4 border-2 border-slate-200">
                                <div class="text-3xl mb-2">{{ stat.icon }}</div>
                                <div class="text-2xl font-black text-slate-900 mb-1">{{ stat.value }}{{ stat.suffix }}</div>
                                <div class="text-sm text-slate-500">{{ stat.label }}</div>
                            </div>
                        </div>

                        <button @click="resetSimulation" 
                                class="px-8 py-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg transition-colors shadow-lg">
                            Run Another Simulation
                        </button>
                    </div>
                </Transition>

            </div>

        </div>
    </div>
</template>

<style scoped>
.slide-fade-enter-active {
    transition: all 0.4s ease;
}

.slide-fade-leave-active {
    transition: all 0.3s ease;
}

.slide-fade-enter-from {
    transform: translateX(30px);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateX(-30px);
    opacity: 0;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.animate-bounce {
    animation: bounce 2s infinite;
}

.delay-100 {
    animation-delay: 0.1s;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>