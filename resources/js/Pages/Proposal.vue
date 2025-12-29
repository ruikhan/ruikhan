<script setup>
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

// --- STATE MANAGEMENT ---
const activeSection = ref('overview'); // overview, documents, tools, procedures
const activePhaseIndex = ref(0);
const expandedDocs = ref([]);
const checkedTools = ref([]);
const showPhaseDetail = ref(false);

// --- PROPOSAL DOCUMENTS ---
const documents = [
    {
        id: 'executive',
        icon: '📊',
        title: 'Executive Summary',
        pages: '2-3 pages',
        color: 'blue',
        purpose: 'High-level overview of system, benefits, and goals',
        contents: [
            'Vision statement for digital barangay transformation',
            'Key features and capabilities overview',
            'Expected benefits and impact metrics',
            'Implementation timeline snapshot',
            'Call to action for barangay adoption'
        ],
        status: 'Critical'
    },
    {
        id: 'implementation',
        icon: '📋',
        title: 'Barangay Implementation Guide',
        pages: '8-10 pages',
        color: 'green',
        purpose: 'Step-by-step process for local adoption',
        contents: [
            'Prerequisites and readiness checklist',
            'Technical infrastructure requirements',
            'Staff roles and responsibilities matrix',
            'Deployment phases and milestones',
            'Support and maintenance procedures'
        ],
        status: 'Critical'
    },
    {
        id: 'cost',
        icon: '💰',
        title: 'Cost & Resource Sheet',
        pages: '3-4 pages',
        color: 'orange',
        purpose: 'Estimated budget, manpower, and technical requirements',
        contents: [
            'Hardware and software costs breakdown',
            'Internet connectivity requirements',
            'Staff training budget allocation',
            'Ongoing operational expenses',
            'ROI projections and cost savings'
        ],
        status: 'Critical'
    },
    {
        id: 'training',
        icon: '🎓',
        title: 'Training Plan',
        pages: '5-6 pages',
        color: 'purple',
        purpose: 'Outline of workshops for staff and citizens',
        contents: [
            'Staff training curriculum (3-day workshop)',
            'Citizen digital literacy programs',
            'Training schedule and venue logistics',
            'Trainer qualifications and materials',
            'Post-training support mechanisms'
        ],
        status: 'Important'
    },
    {
        id: 'legal',
        icon: '⚖️',
        title: 'Legal & Compliance Note',
        pages: '2-3 pages',
        color: 'red',
        purpose: 'Assurance of alignment with Philippine laws',
        contents: [
            'RA 10173 (Data Privacy Act) compliance',
            'RA 9184 (Procurement Law) adherence',
            'Local Government Code compliance',
            'Data security certifications',
            'Liability and risk management'
        ],
        status: 'Critical'
    },
    {
        id: 'awareness',
        icon: '📢',
        title: 'Citizen Awareness Materials',
        pages: 'Multi-format',
        color: 'pink',
        purpose: 'Flyers, posters, FAQs for rollout',
        contents: [
            'Infographic posters for barangay hall',
            'Tri-fold brochures for distribution',
            'FAQ document (Tagalog and English)',
            'Social media content templates',
            'Video tutorial scripts'
        ],
        status: 'Important'
    }
];

// --- TOOLS & MATERIALS ---
const toolsCategories = [
    {
        category: 'Presentation Materials',
        icon: '📊',
        color: 'blue',
        items: [
            { name: 'PowerPoint/PDF Presentation Deck', description: 'For barangay assemblies (30-40 slides)', required: true },
            { name: 'Demo Portal Access', description: 'Sandbox version with sample data', required: true },
            { name: 'Success Stories & Case Studies', description: 'Examples from pilot barangays', required: false }
        ]
    },
    {
        category: 'Physical Materials',
        icon: '📄',
        color: 'green',
        items: [
            { name: 'Printed Handouts', description: 'SOPs, FAQs, citizen guides (50 copies)', required: true },
            { name: 'Feedback Forms', description: 'For officials and residents', required: true },
            { name: 'Training Kits', description: 'Manuals and schedules', required: true }
        ]
    },
    {
        category: 'Digital Assets',
        icon: '💻',
        color: 'purple',
        items: [
            { name: 'System Demo Videos', description: '3-5 minute feature walkthroughs', required: false },
            { name: 'Interactive Prototypes', description: 'Clickable mockups', required: false },
            { name: 'Email Templates', description: 'Follow-up communications', required: true }
        ]
    },
    {
        category: 'Support Resources',
        icon: '🛠️',
        color: 'orange',
        items: [
            { name: 'Technical Specifications', description: 'Hardware/software requirements', required: true },
            { name: 'Implementation Timeline', description: 'Gantt chart visualization', required: true },
            { name: 'Support Hotline Info', description: 'Contact numbers and emails', required: true }
        ]
    }
];

// --- IMPLEMENTATION PHASES ---
const phases = [
    {
        number: 1,
        title: 'Preparation Phase',
        duration: '2-3 weeks',
        icon: '📝',
        color: 'blue',
        character: { name: 'Project Team', avatar: '👥', role: 'Municipal LGU' },
        description: 'Prepare comprehensive proposal package and secure municipal endorsement',
        tasks: [
            {
                task: 'Draft Barangay Proposal Package',
                details: 'Compile all 6 essential documents with local context',
                responsible: 'Project Manager',
                deliverable: 'Complete proposal package (PDF + printed copies)'
            },
            {
                task: 'Coordinate with Municipal Council',
                details: 'Present to council and request official endorsement letter',
                responsible: 'Municipal Admin',
                deliverable: 'Endorsement resolution/letter'
            },
            {
                task: 'Identify Pilot Barangays',
                details: 'Select 1-2 barangays with tech readiness and willing leadership',
                responsible: 'Selection Committee',
                deliverable: 'Pilot barangay shortlist with justification'
            }
        ],
        checklist: ['✓ All documents finalized', '✓ Municipal endorsement secured', '✓ Pilot barangays identified'],
        nextStep: 'Schedule barangay engagement meetings'
    },
    {
        number: 2,
        title: 'Barangay Engagement',
        duration: '3-4 weeks',
        icon: '🤝',
        color: 'green',
        character: { name: 'Barangay Captain', avatar: '👨‍💼', role: 'Local Leader' },
        description: 'Present proposal to barangay officials and conduct initial consultations',
        tasks: [
            {
                task: 'Submit Formal Letter of Intent',
                details: 'Send official letter to Barangay Captain and Council Secretary',
                responsible: 'Municipal Mayor/Admin',
                deliverable: 'Signed letter with proposal package attachment'
            },
            {
                task: 'Barangay Assembly Presentation',
                details: 'Present system demo, benefits, and implementation plan (1-2 hours)',
                responsible: 'Presentation Team',
                deliverable: 'Completed presentation with attendance sheet'
            },
            {
                task: 'Q&A and Concerns Session',
                details: 'Address questions about costs, training, privacy, and citizen adoption',
                responsible: 'Technical + Legal Team',
                deliverable: 'Minutes of meeting with concerns log'
            }
        ],
        checklist: ['✓ Letter officially received', '✓ Assembly conducted', '✓ All concerns documented'],
        nextStep: 'Wait for barangay council deliberation'
    },
    {
        number: 3,
        title: 'Approval & Resolution',
        duration: '1-2 weeks',
        icon: '✅',
        color: 'purple',
        character: { name: 'Barangay Council', avatar: '🏛️', role: 'Decision Makers' },
        description: 'Secure official barangay resolution and finalize agreement terms',
        tasks: [
            {
                task: 'Barangay Council Deliberation',
                details: 'Council reviews proposal and votes on adoption',
                responsible: 'Barangay Council',
                deliverable: 'Council resolution number and copy'
            },
            {
                task: 'Issue Resolution of Support',
                details: 'Official document endorsing system implementation',
                responsible: 'Council Secretary',
                deliverable: 'Signed and sealed resolution'
            },
            {
                task: 'Define Roles & Responsibilities',
                details: 'Agreement on staff validation duties, IT support, citizen awareness',
                responsible: 'Both parties',
                deliverable: 'Memorandum of Agreement (MOA)'
            }
        ],
        checklist: ['✓ Resolution approved', '✓ MOA signed', '✓ Implementation schedule set'],
        nextStep: 'Begin technical deployment preparations'
    },
    {
        number: 4,
        title: 'Pilot Implementation',
        duration: '8-12 weeks',
        icon: '🚀',
        color: 'orange',
        character: { name: 'Implementation Team', avatar: '👨‍💻', role: 'Technical Staff' },
        description: 'Deploy system in pilot barangays with training and citizen awareness',
        tasks: [
            {
                task: 'System Deployment',
                details: 'Install hardware, configure software, test connectivity',
                responsible: 'IT Team',
                deliverable: 'Live system with barangay data migrated'
            },
            {
                task: 'Staff Training Workshops',
                details: 'Conduct 3-day training for barangay staff (10-15 participants)',
                responsible: 'Training Team',
                deliverable: 'Trained staff with certificates'
            },
            {
                task: 'Citizen Awareness Campaign',
                details: 'Distribute flyers, conduct assemblies, post on social media',
                responsible: 'Communications Team',
                deliverable: 'Campaign materials distributed, reach metrics'
            },
            {
                task: 'Monitor & Support',
                details: 'Daily check-ins for first 2 weeks, weekly thereafter',
                responsible: 'Support Team',
                deliverable: 'Issue log and resolution tracker'
            }
        ],
        checklist: ['✓ System live', '✓ Staff trained', '✓ Citizens aware', '✓ Support active'],
        nextStep: 'Collect feedback and performance data'
    },
    {
        number: 5,
        title: 'Evaluation & Scaling',
        duration: '4-8 weeks',
        icon: '📈',
        color: 'red',
        character: { name: 'Evaluation Team', avatar: '📊', role: 'Analysts' },
        description: 'Review pilot results and prepare for municipality-wide scaling',
        tasks: [
            {
                task: 'Analyze Pilot Results',
                details: 'Review KPIs: adoption rate, turnaround time, user satisfaction',
                responsible: 'Data Analytics Team',
                deliverable: 'Pilot evaluation report with metrics'
            },
            {
                task: 'Gather Stakeholder Feedback',
                details: 'Surveys with staff, officials, and citizens',
                responsible: 'Feedback Team',
                deliverable: 'Consolidated feedback report'
            },
            {
                task: 'Refine SOPs and Training',
                details: 'Update procedures based on lessons learned',
                responsible: 'Process Improvement Team',
                deliverable: 'Updated SOPs v2.0 and training materials'
            },
            {
                task: 'Prepare Scaling Plan',
                details: 'Roadmap for rolling out to remaining barangays',
                responsible: 'Project Management Office',
                deliverable: 'Municipality-wide rollout plan'
            }
        ],
        checklist: ['✓ Evaluation complete', '✓ Feedback incorporated', '✓ Scaling plan approved'],
        nextStep: 'Begin rollout to additional barangays'
    }
];

// --- COMPUTED PROPERTIES ---
const currentPhase = computed(() => phases[activePhaseIndex.value]);

const documentsByCriticality = computed(() => {
    const critical = documents.filter(d => d.status === 'Critical');
    const important = documents.filter(d => d.status === 'Important');
    return { critical, important };
});

const toolsProgress = computed(() => {
    const totalRequired = toolsCategories.reduce((sum, cat) => 
        sum + cat.items.filter(i => i.required).length, 0);
    return Math.round((checkedTools.value.length / totalRequired) * 100);
});

// --- METHODS ---
const toggleDoc = (docId) => {
    const index = expandedDocs.value.indexOf(docId);
    if (index > -1) {
        expandedDocs.value.splice(index, 1);
    } else {
        expandedDocs.value.push(docId);
    }
};

const toggleTool = (toolName) => {
    const index = checkedTools.value.indexOf(toolName);
    if (index > -1) {
        checkedTools.value.splice(index, 1);
    } else {
        checkedTools.value.push(toolName);
    }
};

const nextPhase = () => {
    if (activePhaseIndex.value < phases.length - 1) {
        activePhaseIndex.value++;
        showPhaseDetail.value = true;
    }
};

const prevPhase = () => {
    if (activePhaseIndex.value > 0) {
        activePhaseIndex.value--;
        showPhaseDetail.value = true;
    }
};

const startProcedures = () => {
    activeSection.value = 'procedures';
    activePhaseIndex.value = 0;
    showPhaseDetail.value = true;
};
</script>

<template>
    <Head title="E-PILI Barangay Proposal Guide" />

    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-8 px-4 sm:py-12 sm:px-6 font-sans">
        <div class="max-w-7xl mx-auto">

            <!-- Header -->
            <div class="text-center mb-12">

                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-blue-100 to-purple-100 text-blue-700 text-sm font-semibold mb-4 border-2 border-blue-200">
                    <span class="text-xl">🏛️</span>
                    Barangay Implementation Guide
                </div>
                
                <h1 class="text-4xl sm:text-5xl font-black text-slate-900 mb-4">
                    E-PILI Proposal Toolkit
                </h1>
                
                <p class="text-lg text-slate-600 max-w-3xl mx-auto">
                    Everything you need to successfully present and implement the E-PILI system in barangays across your municipality.
                </p>
            </div>

            <!-- Navigation Tabs -->
            <div class="flex flex-wrap justify-center gap-3 mb-12">
                <button @click="activeSection = 'overview'" 
                        class="px-6 py-3 rounded-xl font-bold transition-all duration-300"
                        :class="activeSection === 'overview' ? 'bg-blue-600 text-white shadow-lg scale-105' : 'bg-white text-slate-600 hover:bg-blue-50'">
                    📌 Overview
                </button>
                <button @click="activeSection = 'documents'" 
                        class="px-6 py-3 rounded-xl font-bold transition-all duration-300"
                        :class="activeSection === 'documents' ? 'bg-green-600 text-white shadow-lg scale-105' : 'bg-white text-slate-600 hover:bg-green-50'">
                    📄 Documents
                </button>
                <button @click="activeSection = 'tools'" 
                        class="px-6 py-3 rounded-xl font-bold transition-all duration-300"
                        :class="activeSection === 'tools' ? 'bg-purple-600 text-white shadow-lg scale-105' : 'bg-white text-slate-600 hover:bg-purple-50'">
                    🛠️ Tools & Materials
                </button>
                <button @click="activeSection = 'procedures'" 
                        class="px-6 py-3 rounded-xl font-bold transition-all duration-300"
                        :class="activeSection === 'procedures' ? 'bg-orange-600 text-white shadow-lg scale-105' : 'bg-white text-slate-600 hover:bg-orange-50'">
                    🚀 Procedures
                </button>
            </div>

            <!-- OVERVIEW SECTION -->
            <div v-if="activeSection === 'overview'" class="space-y-8">
                
                <!-- Quick Start Card -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-3xl p-8 shadow-2xl">
                    <div class="flex items-start gap-6">
                        <div class="text-6xl">🎯</div>
                        <div class="flex-1">
                            <h2 class="text-3xl font-black mb-4">Quick Start Guide</h2>
                            <p class="text-lg text-white/90 mb-6">Follow these immediate action steps to begin your barangay proposal process:</p>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-2xl">1️⃣</span>
                                        <span class="font-bold">Prepare Documents</span>
                                    </div>
                                    <p class="text-sm text-white/80">Compile all 6 essential proposal documents</p>
                                </div>
                                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-2xl">2️⃣</span>
                                        <span class="font-bold">Schedule Meetings</span>
                                    </div>
                                    <p class="text-sm text-white/80">Meet with Barangay Captains for presentations</p>
                                </div>
                                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-2xl">3️⃣</span>
                                        <span class="font-bold">Pilot Barangays</span>
                                    </div>
                                    <p class="text-sm text-white/80">Start with 1-2 barangays for proof of concept</p>
                                </div>
                                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-2xl">4️⃣</span>
                                        <span class="font-bold">Demo & Support</span>
                                    </div>
                                    <p class="text-sm text-white/80">Use sandbox portal and provide ongoing support</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Overview -->
                <div class="grid md:grid-cols-4 gap-6">
                    <div class="bg-white rounded-2xl p-6 shadow-lg border-2 border-blue-200">
                        <div class="text-4xl mb-3">📄</div>
                        <div class="text-3xl font-black text-blue-600 mb-1">6</div>
                        <div class="text-sm text-slate-600">Essential Documents</div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-lg border-2 border-green-200">
                        <div class="text-4xl mb-3">🛠️</div>
                        <div class="text-3xl font-black text-green-600 mb-1">12+</div>
                        <div class="text-sm text-slate-600">Tools & Materials</div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-lg border-2 border-purple-200">
                        <div class="text-4xl mb-3">📅</div>
                        <div class="text-3xl font-black text-purple-600 mb-1">5</div>
                        <div class="text-sm text-slate-600">Implementation Phases</div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-lg border-2 border-orange-200">
                        <div class="text-4xl mb-3">⏱️</div>
                        <div class="text-3xl font-black text-orange-600 mb-1">3-4mo</div>
                        <div class="text-sm text-slate-600">Pilot Timeline</div>
                    </div>
                </div>

                <!-- Call to Action -->
                <div class="bg-white rounded-2xl p-8 shadow-lg border-2 border-slate-200">
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Ready to Begin? 🚀</h3>
                    <p class="text-slate-600 mb-6">Explore the sections above to access all documents, tools, and step-by-step procedures for a successful barangay implementation.</p>
                    <div class="flex flex-wrap gap-4">
                        <button @click="activeSection = 'documents'" class="px-6 py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-bold transition-colors">
                            View Documents →
                        </button>
                        <button @click="activeSection = 'tools'" class="px-6 py-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold transition-colors">
                            Check Tools →
                        </button>
                        <button @click="startProcedures" class="px-6 py-3 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold transition-colors">
                            Start Procedures →
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- DOCUMENTS SECTION -->
            <div v-if="activeSection === 'documents'" class="space-y-8">
                
                <!-- Critical Documents -->
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center text-2xl">🚨</div>
                        <div>
                            <h2 class="text-2xl font-black text-slate-900">Critical Documents</h2>
                            <p class="text-sm text-slate-500">Required for barangay presentation</p>
                        </div>
                    </div>

                    <div class="grid lg:grid-cols-2 gap-6">
                        <div v-for="doc in documentsByCriticality.critical" :key="doc.id"
                             class="bg-white rounded-2xl shadow-lg border-2 overflow-hidden transition-all duration-300 hover:shadow-xl"
                             :class="`border-${doc.color}-200 hover:border-${doc.color}-400`">
                            
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="text-4xl">{{ doc.icon }}</div>
                                        <div>
                                            <h3 class="text-xl font-bold text-slate-900">{{ doc.title }}</h3>
                                            <p class="text-sm text-slate-500">{{ doc.pages }}</p>
                                        </div>
                                    </div>
                                    <button @click="toggleDoc(doc.id)" 
                                            class="text-2xl transition-transform"
                                            :class="expandedDocs.includes(doc.id) ? 'rotate-180' : ''">
                                        ▼
                                    </button>
                                </div>

                                <div class="rounded-lg p-3 mb-4"
                                     :class="`bg-${doc.color}-50 border border-${doc.color}-200`">
                                    <p class="text-sm font-medium text-slate-700">{{ doc.purpose }}</p>
                                </div>

                                <Transition name="expand">
                                    <div v-if="expandedDocs.includes(doc.id)" class="space-y-2">
                                        <h4 class="font-bold text-slate-900 text-sm mb-2">Contents:</h4>
                                        <ul class="space-y-2">
                                            <li v-for="(content, idx) in doc.contents" :key="idx"
                                                class="flex items-start gap-2 text-sm text-slate-600">
                                                <span class="text-green-500 font-bold">✓</span>
                                                <span>{{ content }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </Transition>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Important Documents -->
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-2xl">📋</div>
                        <div>
                            <h2 class="text-2xl font-black text-slate-900">Important Documents</h2>
                            <p class="text-sm text-slate-500">Supporting materials for implementation</p>
                        </div>
                    </div>

                    <div class="grid lg:grid-cols-2 gap-6">
                        <div v-for="doc in documentsByCriticality.important" :key="doc.id"
                             class="bg-white rounded-2xl shadow-lg border-2 overflow-hidden transition-all duration-300 hover:shadow-xl"
                             :class="`border-${doc.color}-200 hover:border-${doc.color}-400`">
                            
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="text-4xl">{{ doc.icon }}</div>
                                        <div>
                                            <h3 class="text-xl font-bold text-slate-900">{{ doc.title }}</h3>
                                            <p class="text-sm text-slate-500">{{ doc.pages }}</p>
                                        </div>
                                    </div>
                                    <button @click="toggleDoc(doc.id)" 
                                            class="text-2xl transition-transform"
                                            :class="expandedDocs.includes(doc.id) ? 'rotate-180' : ''">
                                        ▼
                                    </button>
                                </div>

                                <div class="rounded-lg p-3 mb-4"
                                     :class="`bg-${doc.color}-50 border border-${doc.color}-200`">
                                    <p class="text-sm font-medium text-slate-700">{{ doc.purpose }}</p>
                                </div>

                                <Transition name="expand">
                                    <div v-if="expandedDocs.includes(doc.id)" class="space-y-2">
                                        <h4 class="font-bold text-slate-900 text-sm mb-2">Contents:</h4>
                                        <ul class="space-y-2">
                                            <li v-for="(content, idx) in doc.contents" :key="idx"
                                                class="flex items-start gap-2 text-sm text-slate-600">
                                                <span class="text-green-500 font-bold">✓</span>
                                                <span>{{ content }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </Transition>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- TOOLS & MATERIALS SECTION -->
            <div v-if="activeSection === 'tools'" class="space-y-8">
                
                <!-- Progress Indicator -->
                <div class="bg-white rounded-2xl p-6 shadow-lg border-2 border-purple-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-slate-900">Preparation Progress</h3>
                        <span class="text-2xl font-black text-purple-600">{{ toolsProgress }}%</span>
                    </div>
                    <div class="h-4 bg-slate-200 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-purple-500 to-purple-600 transition-all duration-500 rounded-full"
                             :style="{ width: `${toolsProgress}%` }"></div>
                    </div>
                    <p class="text-sm text-slate-500 mt-2">Check off required items as you prepare them</p>
                </div>

                <!-- Tools Categories -->
                <div v-for="category in toolsCategories" :key="category.category" class="space-y-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl"
                             :class="`bg-${category.color}-100`">
                            {{ category.icon }}
                        </div>
                        <h2 class="text-2xl font-black text-slate-900">{{ category.category }}</h2>
                    </div>

                    <div class="grid lg:grid-cols-2 gap-4">
                        <div v-for="item in category.items" :key="item.name"
                             class="bg-white rounded-xl p-5 shadow border-2 transition-all duration-300"
                             :class="checkedTools.includes(item.name) ? `border-${category.color}-400 bg-${category.color}-50` : 'border-slate-200 hover:border-slate-300'">
                            
                            <label class="flex items-start gap-4 cursor-pointer">
                                <input type="checkbox" 
                                       :checked="checkedTools.includes(item.name)"
                                       @change="toggleTool(item.name)"
                                       class="mt-1 w-5 h-5 rounded border-2 border-slate-300 text-purple-600 focus:ring-purple-500">
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-2">
                                        <h4 class="font-bold text-slate-900">{{ item.name }}</h4>
                                        <span v-if="item.required" 
                                              class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">
                                            REQUIRED
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-600">{{ item.description }}</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

            </div>

            <!-- PROCEDURES SECTION -->
            <div v-if="activeSection === 'procedures'" class="space-y-8">
                
                <!-- Phase Overview Timeline -->
                <div class="bg-white rounded-2xl p-6 shadow-lg border-2 border-orange-200">
                    <h2 class="text-2xl font-black text-slate-900 mb-6">5-Phase Implementation Timeline</h2>
                    
                    <div class="relative">
                        <!-- Timeline Line -->
                        <div class="absolute top-8 left-0 right-0 h-1 bg-gradient-to-r from-blue-200 via-green-200 via-purple-200 via-orange-200 to-red-200"></div>
                        
                        <!-- Phase Buttons -->
                        <div class="relative flex justify-between">
                            <button v-for="(phase, idx) in phases" :key="phase.number"
                                    @click="activePhaseIndex = idx; showPhaseDetail = true"
                                    class="flex flex-col items-center group">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl font-black transition-all duration-300 border-4 bg-white"
                                     :class="activePhaseIndex === idx ? `border-${phase.color}-500 shadow-lg scale-110` : 'border-slate-300 group-hover:border-slate-400'">
                                    {{ phase.icon }}
                                </div>
                                <div class="mt-3 text-center">
                                    <div class="font-bold text-sm text-slate-900">Phase {{ phase.number }}</div>
                                    <div class="text-xs text-slate-500 max-w-[100px]">{{ phase.duration }}</div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Phase Detail -->
                <Transition name="slide-fade" mode="out-in">
                    <div v-if="showPhaseDetail" :key="activePhaseIndex" 
                         class="bg-white rounded-2xl shadow-xl border-4 overflow-hidden"
                         :class="`border-${currentPhase.color}-500`">
                        
                        <!-- Phase Header -->
                        <div class="bg-gradient-to-r p-8 text-white"
                             :class="`from-${currentPhase.color}-600 to-${currentPhase.color}-500`">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-4xl">
                                        {{ currentPhase.icon }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-white/80 mb-1">Phase {{ currentPhase.number }} of 5</div>
                                        <h2 class="text-3xl font-black">{{ currentPhase.title }}</h2>
                                        <p class="text-white/90 mt-1">{{ currentPhase.duration }} • {{ currentPhase.character.name }}</p>
                                    </div>
                                </div>
                                <div class="text-6xl">{{ currentPhase.character.avatar }}</div>
                            </div>
                        </div>

                        <!-- Phase Content -->
                        <div class="p-8">
                            
                            <!-- Description -->
                            <div class="rounded-xl p-6 mb-8"
                                 :class="`bg-${currentPhase.color}-50 border-2 border-${currentPhase.color}-200`">
                                <p class="text-lg text-slate-700">{{ currentPhase.description }}</p>
                            </div>

                            <!-- Tasks -->
                            <h3 class="text-xl font-bold text-slate-900 mb-4 flex items-center gap-2">
                                <span class="text-2xl">📋</span>
                                Key Tasks
                            </h3>
                            <div class="space-y-4 mb-8">
                                <div v-for="(task, idx) in currentPhase.tasks" :key="idx"
                                     class="bg-slate-50 rounded-xl p-5 border-2 border-slate-200">
                                    <div class="flex items-start gap-4">
                                        <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold flex-shrink-0">
                                            {{ idx + 1 }}
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-bold text-slate-900 mb-2">{{ task.task }}</h4>
                                            <p class="text-sm text-slate-600 mb-3">{{ task.details }}</p>
                                            <div class="flex flex-wrap gap-4 text-xs">
                                                <div class="flex items-center gap-1">
                                                    <span class="font-semibold text-slate-700">Responsible:</span>
                                                    <span class="text-slate-600">{{ task.responsible }}</span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <span class="font-semibold text-slate-700">Deliverable:</span>
                                                    <span class="text-slate-600">{{ task.deliverable }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Checklist -->
                            <h3 class="text-xl font-bold text-slate-900 mb-4 flex items-center gap-2">
                                <span class="text-2xl">✅</span>
                                Success Checklist
                            </h3>
                            <div class="space-y-2 mb-8">
                                <div v-for="(item, idx) in currentPhase.checklist" :key="idx"
                                     class="flex items-center gap-3 p-3 bg-green-50 rounded-lg border border-green-200">
                                    <span class="text-green-600 text-xl">{{ item.split(' ')[0] }}</span>
                                    <span class="text-slate-700">{{ item.substring(2) }}</span>
                                </div>
                            </div>

                            <!-- Next Step -->
                            <div class="rounded-xl p-6"
                                 :class="`bg-${currentPhase.color}-100 border-2 border-${currentPhase.color}-300`">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-2xl">🎯</span>
                                    <span class="font-bold text-slate-900">Next Step:</span>
                                </div>
                                <p class="text-slate-700 font-medium">{{ currentPhase.nextStep }}</p>
                            </div>

                        </div>

                        <!-- Navigation -->
                        <div class="bg-slate-50 p-6 flex justify-between items-center border-t-2 border-slate-200">
                            <button @click="prevPhase" 
                                    :disabled="activePhaseIndex === 0"
                                    class="px-6 py-3 rounded-lg font-bold transition-all"
                                    :class="activePhaseIndex === 0 ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : 'bg-slate-700 hover:bg-slate-800 text-white'">
                                ← Previous Phase
                            </button>
                            <div class="text-sm text-slate-600 font-medium">
                                Phase {{ activePhaseIndex + 1 }} of {{ phases.length }}
                            </div>
                            <button @click="nextPhase" 
                                    :disabled="activePhaseIndex === phases.length - 1"
                                    class="px-6 py-3 rounded-lg font-bold transition-all"
                                    :class="activePhaseIndex === phases.length - 1 ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : `bg-${currentPhase.color}-600 hover:bg-${currentPhase.color}-700 text-white`">
                                Next Phase →
                            </button>
                        </div>

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
    transform: translateY(20px);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateY(-20px);
    opacity: 0;
}

.expand-enter-active,
.expand-leave-active {
    transition: all 0.3s ease;
    overflow: hidden;
}

.expand-enter-from,
.expand-leave-to {
    max-height: 0;
    opacity: 0;
}

.expand-enter-to,
.expand-leave-from {
    max-height: 500px;
    opacity: 1;
}
</style>