<script setup>
import { Head } from '@inertiajs/vue3';
import { computed, onUnmounted, ref } from 'vue';

// --- STATE MANAGEMENT ---
const activeModuleId = ref(null);
const isRunning = ref(false);
const isPaused = ref(false);
const progress = ref(0);
const currentLessonIndex = ref(0);
const quizScore = ref(0);
const showQuiz = ref(false);

let simulationInterval = null;

// --- TRAINING MODULES DATABASE ---
const modules = {
    staffTraining: {
        id: 'staffTraining',
        color: 'blue',
        icon: '👨‍💼',
        title: 'Staff Training Manual',
        subtitle: 'Barangay Operations Excellence',
        character: { name: 'Staff Jenny', avatar: '👩‍💼', role: 'Barangay Staff Trainee' },
        purpose: 'Equip barangay staff with skills to operate the portal efficiently',
        lessons: [
            {
                number: 1,
                title: 'Logging In & Authentication',
                description: 'Jenny learns how to securely access the system and manage her account.',
                visual: '🔐',
                action: '👆',
                result: '✅',
                content: 'Use your assigned username and password. Enable two-factor authentication for security. Remember to log out after each session.',
                keyPoints: ['Secure password management', 'Two-factor authentication', 'Session timeout awareness'],
                practical: 'Practice: Log in 3 times successfully'
            },
            {
                number: 2,
                title: 'Document Validation Workflows',
                description: 'Jenny discovers how to review, approve, or reject resident document requests.',
                visual: '📄',
                action: '🔍',
                result: '✔️',
                content: 'Open pending requests, verify attachments against requirements, check resident records, and approve or flag with reasons.',
                keyPoints: ['Checklist verification', 'Quality control standards', 'Reason codes for rejection'],
                practical: 'Exercise: Validate 5 sample requests'
            },
            {
                number: 3,
                title: 'Payment Confirmation',
                description: 'Jenny learns to verify payment receipts and update transaction status.',
                visual: '💰',
                action: '✅',
                result: '📜',
                content: 'Match payment reference numbers, verify amounts, confirm transaction IDs, and update status to "Paid".',
                keyPoints: ['Reference number matching', 'Amount verification', 'Status updates'],
                practical: 'Task: Process 3 payment confirmations'
            },
            {
                number: 4,
                title: 'Handling Resident Queries',
                description: 'Jenny practices responding to common questions and escalating complex issues.',
                visual: '💬',
                action: '🤝',
                result: '😊',
                content: 'Use templates for common queries, provide clear timelines, escalate technical issues to IT, and document all interactions.',
                keyPoints: ['Response templates', 'Escalation protocols', 'Documentation standards'],
                practical: 'Role-play: Answer 5 resident questions'
            }
        ],
        assessment: {
            type: 'Practical Quiz',
            questions: 10,
            passingScore: 80,
            certification: 'Certified Barangay Portal Operator'
        },
        materials: ['Step-by-step guides', 'Video tutorials', 'Quick reference cards', 'FAQ document'],
        impact: 'Staff become confident system operators, reducing errors by 90% and improving resident satisfaction.',
        stats: [
            { label: 'Modules', value: '4', icon: '📚' },
            { label: 'Duration', value: '3hrs', icon: '⏱️' },
            { label: 'Pass Rate', value: '95%', icon: '🎯' }
        ]
    },
    adminTraining: {
        id: 'adminTraining',
        color: 'purple',
        icon: '👨‍💻',
        title: 'Administrator Training Guide',
        subtitle: 'LGU Oversight & Compliance',
        character: { name: 'Admin Carlos', avatar: '👨‍💻', role: 'LGU Administrator Trainee' },
        purpose: 'Train LGU admins on system oversight, reporting, and compliance management',
        lessons: [
            {
                number: 1,
                title: 'Monitoring Dashboards',
                description: 'Carlos learns to read real-time analytics and identify trends.',
                visual: '📊',
                action: '👀',
                result: '📈',
                content: 'Navigate the admin dashboard, filter data by barangay/date, export reports, and set up alerts for anomalies.',
                keyPoints: ['Dashboard navigation', 'Data filtering', 'Alert configuration'],
                practical: 'Activity: Generate 3 different reports'
            },
            {
                number: 2,
                title: 'Generating Reports',
                description: 'Carlos masters creating comprehensive reports for stakeholders.',
                visual: '📋',
                action: '🖨️',
                result: '📊',
                content: 'Select report type (daily, weekly, monthly), customize parameters, export to PDF/Excel, and schedule automated delivery.',
                keyPoints: ['Report templates', 'Customization options', 'Scheduling automation'],
                practical: 'Exercise: Create monthly compliance report'
            },
            {
                number: 3,
                title: 'Managing User Roles',
                description: 'Carlos learns to create accounts and assign appropriate permissions.',
                visual: '👥',
                action: '🔑',
                result: '✅',
                content: 'Create user accounts, assign role-based permissions (viewer, editor, approver), deactivate inactive users, and audit access logs.',
                keyPoints: ['Role hierarchy', 'Permission matrices', 'Access auditing'],
                practical: 'Task: Set up 5 staff accounts with correct roles'
            },
            {
                number: 4,
                title: 'Escalation Protocols',
                description: 'Carlos understands how to handle critical issues and system emergencies.',
                visual: '🚨',
                action: '📞',
                result: '🛠️',
                content: 'Identify severity levels, follow escalation matrix, contact technical support, and document incident resolution.',
                keyPoints: ['Severity classification', 'Escalation matrix', 'Incident documentation'],
                practical: 'Scenario: Handle 3 escalation cases'
            }
        ],
        assessment: {
            type: 'Case Studies & Scenarios',
            questions: 8,
            passingScore: 85,
            certification: 'Certified LGU System Administrator'
        },
        materials: ['Admin handbook', 'Video walkthroughs', 'Decision trees', 'Escalation flowcharts'],
        impact: 'Admins gain full system oversight capabilities, ensuring compliance and proactive issue resolution.',
        stats: [
            { label: 'Modules', value: '4', icon: '📚' },
            { label: 'Duration', value: '4hrs', icon: '⏱️' },
            { label: 'Success Rate', value: '92%', icon: '🎯' }
        ]
    },
    citizenLiteracy: {
        id: 'citizenLiteracy',
        color: 'green',
        icon: '👥',
        title: 'Citizen Digital Literacy',
        subtitle: 'Empowering Residents',
        character: { name: 'Maria Santos', avatar: '👩', role: 'Resident Learner' },
        purpose: 'Empower residents to confidently use digital government services',
        lessons: [
            {
                number: 1,
                title: 'Account Registration',
                description: 'Maria learns to create her account and set up her profile.',
                visual: '📝',
                action: '✍️',
                result: '🎉',
                content: 'Visit the portal, click "Register", fill in required information, verify via SMS, and set a strong password.',
                keyPoints: ['Email/phone verification', 'Strong password creation', 'Profile completion'],
                practical: 'Hands-on: Complete registration process'
            },
            {
                number: 2,
                title: 'Requesting Documents',
                description: 'Maria discovers how to request barangay clearance and other documents online.',
                visual: '📄',
                action: '📤',
                result: '✅',
                content: 'Select document type, upload required attachments (ID, photos), review requirements checklist, and submit request.',
                keyPoints: ['Document types', 'Attachment requirements', 'Tracking request status'],
                practical: 'Practice: Submit a sample clearance request'
            },
            {
                number: 3,
                title: 'Making Secure Payments',
                description: 'Maria learns to pay fees safely using GCash, PayMaya, or bank transfer.',
                visual: '💳',
                action: '📱',
                result: '✔️',
                content: 'Choose payment method, scan QR code or copy reference number, complete payment in banking app, and save receipt.',
                keyPoints: ['Payment options', 'QR code scanning', 'Receipt storage'],
                practical: 'Demo: Complete test payment transaction'
            },
            {
                number: 4,
                title: 'Emergency Features',
                description: 'Maria understands how to use the panic button and receive emergency alerts.',
                visual: '🚨',
                action: '🆘',
                result: '🚑',
                content: 'Locate emergency button, tap to send distress signal with GPS location, and subscribe to receive typhoon/flood alerts.',
                keyPoints: ['Panic button location', 'GPS permissions', 'Alert notifications'],
                practical: 'Tutorial: Navigate emergency features'
            }
        ],
        assessment: {
            type: 'Interactive Quiz',
            questions: 12,
            passingScore: 75,
            certification: 'Digital Citizen Certificate'
        },
        materials: ['Illustrated handbook (Tagalog, English)', 'Video guides', 'FAQs', 'Helpdesk contacts'],
        impact: 'Residents gain confidence to access government services digitally, reducing in-person visits by 70%.',
        stats: [
            { label: 'Languages', value: '3', icon: '🌐' },
            { label: 'Completion', value: '2hrs', icon: '⏱️' },
            { label: 'Satisfaction', value: '4.8/5', icon: '⭐' }
        ]
    },
    workshops: {
        id: 'workshops',
        color: 'orange',
        icon: '🎓',
        title: 'Workshop Modules',
        subtitle: 'Interactive Learning Sessions',
        character: { name: 'Trainer Alex', avatar: '👨‍🏫', role: 'Training Facilitator' },
        purpose: 'Conduct engaging workshops for all stakeholders',
        lessons: [
            {
                number: 1,
                title: 'Introduction to e-Governance',
                description: 'Participants learn about digital transformation in local government.',
                visual: '🏛️',
                action: '💡',
                result: '🌟',
                content: 'Understand benefits of digital services, explore global best practices, discuss transparency and accountability, and envision local impact.',
                keyPoints: ['Digital transformation benefits', 'Case studies', 'Vision setting'],
                practical: 'Group discussion: Local e-governance opportunities'
            },
            {
                number: 2,
                title: 'Portal Navigation Basics',
                description: 'Hands-on practice navigating the E-PILI portal interface.',
                visual: '🖱️',
                action: '🧭',
                result: '✅',
                content: 'Explore main menu, search for services, access frequently used features, and customize user preferences.',
                keyPoints: ['Menu structure', 'Search functionality', 'Personalization options'],
                practical: 'Lab session: Navigate to 10 key features'
            },
            {
                number: 3,
                title: 'Cybersecurity Awareness',
                description: 'Learn to protect personal information and recognize online threats.',
                visual: '🔒',
                action: '🛡️',
                result: '🔐',
                content: 'Identify phishing attempts, create strong passwords, enable two-factor auth, and report suspicious activity.',
                keyPoints: ['Password security', 'Phishing recognition', 'Safe browsing practices'],
                practical: 'Exercise: Spot phishing examples'
            },
            {
                number: 4,
                title: 'Feedback & Participation',
                description: 'Discover how to provide feedback and engage with local government.',
                visual: '💬',
                action: '✍️',
                result: '🤝',
                content: 'Submit feedback through portal, participate in surveys, report issues, and track government responses.',
                keyPoints: ['Feedback channels', 'Survey participation', 'Issue tracking'],
                practical: 'Activity: Submit practice feedback'
            }
        ],
        assessment: {
            type: 'Workshop Completion',
            questions: 0,
            passingScore: 0,
            certification: 'Workshop Attendance Certificate'
        },
        materials: ['Presentation slides', 'Handouts', 'Activity worksheets', 'Evaluation forms'],
        schedules: {
            barangay: 'Monthly workshops at each barangay hall',
            municipal: 'Quarterly seminars at city hall auditorium',
            online: 'On-demand webinars accessible 24/7'
        },
        impact: 'Structured learning builds community capacity and ensures inclusive digital adoption.',
        stats: [
            { label: 'Sessions/Mo', value: '12+', icon: '📅' },
            { label: 'Participants', value: '500+', icon: '👥' },
            { label: 'Rating', value: '4.7/5', icon: '⭐' }
        ]
    },
    evaluation: {
        id: 'evaluation',
        color: 'pink',
        icon: '📋',
        title: 'Evaluation & Feedback',
        subtitle: 'Measuring Training Effectiveness',
        character: { name: 'Coordinator Lisa', avatar: '👩‍💼', role: 'Training Coordinator' },
        purpose: 'Measure training effectiveness and continuously improve programs',
        lessons: [
            {
                number: 1,
                title: 'Pre-Training Survey',
                description: 'Assess baseline knowledge and skill levels before training begins.',
                visual: '📝',
                action: '❓',
                result: '📊',
                content: 'Participants complete survey about current digital skills, system familiarity, expectations, and learning goals.',
                keyPoints: ['Baseline assessment', 'Skill inventory', 'Expectation setting'],
                practical: 'Sample: Complete pre-training assessment'
            },
            {
                number: 2,
                title: 'Post-Training Evaluation',
                description: 'Measure knowledge gained and skills developed after training.',
                visual: '✅',
                action: '📈',
                result: '🎯',
                content: 'Evaluate learning outcomes, confidence levels, practical application ability, and areas needing reinforcement.',
                keyPoints: ['Knowledge retention', 'Skill confidence', 'Gap identification'],
                practical: 'Test: Complete post-training quiz'
            },
            {
                number: 3,
                title: 'Feedback Collection',
                description: 'Gather suggestions for improving training content and delivery.',
                visual: '💬',
                action: '✍️',
                result: '💡',
                content: 'Rate instructor effectiveness, content relevance, materials quality, and suggest improvements for future sessions.',
                keyPoints: ['Trainer evaluation', 'Content feedback', 'Improvement suggestions'],
                practical: 'Activity: Provide detailed feedback'
            },
            {
                number: 4,
                title: 'Metrics Analysis',
                description: 'Review aggregated data to identify trends and optimize programs.',
                visual: '📊',
                action: '🔍',
                result: '📈',
                content: 'Analyze pass rates, satisfaction scores, common challenges, and track long-term skill retention.',
                keyPoints: ['Data visualization', 'Trend identification', 'Action planning'],
                practical: 'Case study: Interpret training metrics'
            }
        ],
        assessment: {
            type: 'Continuous Improvement',
            questions: 0,
            passingScore: 0,
            certification: 'N/A'
        },
        materials: ['Survey templates', 'Evaluation forms', 'Analytics dashboard', 'Reports'],
        metrics: {
            retention: 'Track knowledge retention at 30, 60, 90 days',
            confidence: 'Measure confidence improvement pre/post training',
            satisfaction: 'Monitor trainer and content satisfaction scores'
        },
        impact: 'Data-driven evaluation ensures training programs evolve based on learner needs and outcomes.',
        stats: [
            { label: 'Response', value: '88%', icon: '📊' },
            { label: 'Retention', value: '85%', icon: '🧠' },
            { label: 'Satisfaction', value: '4.6/5', icon: '⭐' }
        ]
    },
    roadmap: {
        id: 'roadmap',
        color: 'cyan',
        icon: '🗺️',
        title: 'Capacity Building Roadmap',
        subtitle: '5-Phase Implementation',
        character: { name: 'Director Ramon', avatar: '👨‍💼', role: 'Program Director' },
        purpose: 'Strategic plan for systematic capacity building across the municipality',
        lessons: [
            {
                number: 1,
                title: 'Phase 1: Initial Training',
                description: 'Launch comprehensive training for all staff and administrators.',
                visual: '🚀',
                action: '👥',
                result: '✅',
                content: 'Train all barangay staff (200+), certify LGU administrators (50+), establish help desk, and create resource library.',
                keyPoints: ['Staff onboarding', 'Admin certification', 'Support structure'],
                practical: 'Timeline: 3 months'
            },
            {
                number: 2,
                title: 'Phase 2: Citizen Campaigns',
                description: 'Roll out digital literacy programs for residents across all barangays.',
                visual: '📣',
                action: '🎓',
                result: '🌟',
                content: 'Monthly workshops at each barangay, mobile training units for seniors, multilingual materials, and awareness campaigns.',
                keyPoints: ['Community outreach', 'Inclusive access', 'Marketing campaigns'],
                practical: 'Timeline: 6 months'
            },
            {
                number: 3,
                title: 'Phase 3: Continuous Development',
                description: 'Establish ongoing professional development and advanced training.',
                visual: '📚',
                action: '🔄',
                result: '📈',
                content: 'Quarterly refresher courses, advanced modules, peer learning groups, and certification renewals.',
                keyPoints: ['Skill enhancement', 'Knowledge updates', 'Peer networks'],
                practical: 'Timeline: Ongoing'
            },
            {
                number: 4,
                title: 'Phase 4: Regional Expansion',
                description: 'Share knowledge and expand training to neighboring municipalities.',
                visual: '🌐',
                action: '🤝',
                result: '🏆',
                content: 'Train-the-trainer programs, inter-municipal workshops, best practice sharing, and regional conferences.',
                keyPoints: ['Knowledge transfer', 'Regional collaboration', 'Scale-up strategy'],
                practical: 'Timeline: 12-18 months'
            },
            {
                number: 5,
                title: 'Phase 5: Annual Review',
                description: 'Evaluate impact and update training materials based on lessons learned.',
                visual: '🔄',
                action: '📊',
                result: '✨',
                content: 'Assess training effectiveness, update curriculum, refresh materials, and plan next-year improvements.',
                keyPoints: ['Impact evaluation', 'Material updates', 'Strategic planning'],
                practical: 'Timeline: Annual cycle'
            }
        ],
        assessment: {
            type: 'Strategic Milestone Tracking',
            questions: 0,
            passingScore: 0,
            certification: 'N/A'
        },
        materials: ['Roadmap document', 'Gantt charts', 'KPI dashboards', 'Progress reports'],
        milestones: {
            phase1: 'Q1 2025: 100% staff trained',
            phase2: 'Q3 2025: 5000+ citizens trained',
            phase3: 'Q1 2026: Continuous learning established',
            phase4: 'Q3 2026: 3 municipalities trained',
            phase5: 'Q4 2026: Annual review completed'
        },
        impact: 'Systematic capacity building ensures sustainable digital transformation across the entire municipality.',
        stats: [
            { label: 'Phases', value: '5', icon: '🎯' },
            { label: 'Timeline', value: '24mo', icon: '📅' },
            { label: 'Reach', value: '10K+', icon: '👥' }
        ]
    }
};

// --- COMPUTED PROPERTIES ---
const activeModule = computed(() => activeModuleId.value ? modules[activeModuleId.value] : null);
const currentLesson = computed(() => {
    if (!activeModule.value) return null;
    return activeModule.value.lessons[currentLessonIndex.value];
});

const isComplete = computed(() => {
    return activeModule.value && currentLessonIndex.value >= activeModule.value.lessons.length;
});

const progressPercentage = computed(() => {
    if (!activeModule.value) return 0;
    const lessonProgress = (currentLessonIndex.value / activeModule.value.lessons.length) * 100;
    const stepProgress = (progress.value / activeModule.value.lessons.length);
    return lessonProgress + stepProgress;
});

// --- SIMULATION ENGINE ---
const startModule = (id) => {
    resetSimulation();
    activeModuleId.value = id;
    isRunning.value = true;
    isPaused.value = false;
    
    simulationInterval = setInterval(() => {
        if (isPaused.value) return;
        
        progress.value += 0.8;
        
        if (progress.value >= 100 && currentLessonIndex.value < activeModule.value.lessons.length - 1) {
            currentLessonIndex.value++;
            progress.value = 0;
        } else if (progress.value >= 100 && currentLessonIndex.value === activeModule.value.lessons.length - 1) {
            clearInterval(simulationInterval);
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
    currentLessonIndex.value = 0;
    activeModuleId.value = null;
    quizScore.value = 0;
    showQuiz.value = false;
};

const takeQuiz = () => {
    showQuiz.value = true;
    quizScore.value = Math.floor(Math.random() * 20) + 80; // Simulate score 80-100
};

onUnmounted(() => clearInterval(simulationInterval));
</script>

<template>
    <Head title="E-PILI Training & Capacity Building" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 py-8 px-4 sm:py-12 sm:px-6 font-sans">
        <div class="max-w-7xl mx-auto">

            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700 text-sm font-semibold mb-4 border-2 border-purple-200">
                    <span class="text-xl">🎓</span>
                    E-PILI Training & Capacity Building
                </div>
                
                <h1 class="text-4xl sm:text-5xl font-black text-slate-900 mb-4">
                    Interactive Learning Hub
                </h1>
                
                <p class="text-lg text-slate-600 max-w-3xl mx-auto">
                    Experience comprehensive training modules designed for staff, administrators, and citizens. Learn at your own pace!
                </p>
            </div>

            <!-- Module Selection Grid -->
            <div v-if="!isRunning" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                <button v-for="module in modules" :key="module.id" 
                        @click="startModule(module.id)"
                        class="group relative p-6 bg-white rounded-2xl border-2 hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 text-left"
                        :class="`border-${module.color}-200 hover:border-${module.color}-400`">
                    
                    <div class="absolute top-4 right-4 text-4xl opacity-20 group-hover:opacity-40 transition-opacity">
                        {{ module.icon }}
                    </div>
                    
                    <div class="text-4xl mb-4">{{ module.icon }}</div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">{{ module.title }}</h3>
                    <p class="text-sm text-slate-500 mb-4">{{ module.subtitle }}</p>
                    
                    <div class="flex items-center gap-2 text-sm font-semibold"
                         :class="`text-${module.color}-600`">
                        <span>Start Learning</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </div>
                </button>
            </div>

            <!-- Active Training Module -->
            <div v-else class="max-w-5xl mx-auto">
                
                <!-- Progress Header -->
                <div class="mb-8 bg-white rounded-2xl p-6 shadow-xl border-2"
                     :class="`border-${activeModule.color}-200`">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-4">
                            <div class="text-5xl">{{ activeModule.icon }}</div>
                            <div>
                                <h2 class="text-2xl font-bold text-slate-900">{{ activeModule.title }}</h2>
                                <p class="text-sm text-slate-500">{{ activeModule.subtitle }}</p>
                                <p class="text-xs text-slate-400 mt-1">{{ activeModule.character.name }} • {{ activeModule.character.role }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-slate-700">
                                Lesson {{ currentLessonIndex + 1 }} of {{ activeModule.lessons.length }}
                            </span>
                            <span class="text-sm font-bold"
                                  :class="`text-${activeModule.color}-600`">
                                {{ Math.round(progressPercentage) }}% Complete
                            </span>
                        </div>
                        <div class="h-3 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r transition-all duration-300 rounded-full"
                                 :class="`from-${activeModule.color}-400 to-${activeModule.color}-600`"
                                 :style="{ width: `${progressPercentage}%` }"></div>
                        </div>
                    </div>

                    <!-- Controls -->
                    <div class="flex items-center gap-2">
                        <button @click="isPaused ? resumeSimulation() : pauseSimulation()" 
                                class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 transition-colors text-sm font-medium flex items-center gap-2">
                            <span v-if="isPaused">▶️</span>
                            <span v-else>⏸️</span>
                            {{ isPaused ? 'Resume' : 'Pause' }}
                        </button>
                        <button @click="resetSimulation" 
                                class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 transition-colors text-sm font-medium">
                            ↺ Exit Training
                        </button>
                    </div>
                </div>

                <!-- Lesson Content -->
                <Transition name="slide-fade" mode="out-in">
                    
                    <!-- Quiz Screen -->
                    <div v-if="showQuiz" key="quiz" class="bg-white rounded-2xl shadow-xl border-4 border-green-500 p-12 text-center">
                        <div class="text-7xl mb-6">📝</div>
                        <h2 class="text-4xl font-black text-slate-900 mb-4">Assessment Complete!</h2>
                        <div class="text-6xl font-black mb-6"
                             :class="quizScore >= activeModule.assessment.passingScore ? 'text-green-500' : 'text-orange-500'">
                            {{ quizScore }}%
                        </div>
                        <p class="text-xl text-slate-600 mb-8">
                            {{ quizScore >= activeModule.assessment.passingScore ? 
                               `Congratulations! You passed the ${activeModule.assessment.type}!` : 
                               'Keep practicing! Review the materials and try again.' }}
                        </p>
                        
                        <div v-if="quizScore >= activeModule.assessment.passingScore" 
                             class="bg-green-50 border-2 border-green-200 rounded-xl p-6 mb-8">
                            <div class="text-4xl mb-3">🏆</div>
                            <h3 class="text-2xl font-bold text-green-700 mb-2">Certificate Earned!</h3>
                            <p class="text-green-600">{{ activeModule.assessment.certification }}</p>
                        </div>

                        <button @click="showQuiz = false" 
                                class="px-8 py-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg transition-colors shadow-lg mr-4">
                            Review Lessons
                        </button>
                        <button @click="resetSimulation" 
                                class="px-8 py-4 rounded-xl bg-slate-600 hover:bg-slate-700 text-white font-bold text-lg transition-colors shadow-lg">
                            Choose Another Module
                        </button>
                    </div>

                    <!-- Completion Screen -->
                    <div v-else-if="isComplete" key="complete" class="bg-white rounded-2xl shadow-xl border-4 p-12 text-center"
                         :class="`border-${activeModule.color}-500`">
                        <div class="text-7xl mb-6 animate-bounce">🎉</div>
                        <h2 class="text-4xl font-black text-slate-900 mb-4">Training Complete!</h2>
                        <p class="text-xl text-slate-600 mb-8 max-w-2xl mx-auto">{{ activeModule.impact }}</p>
                        
                        <!-- Impact Stats -->
                        <div class="grid grid-cols-3 gap-4 mb-8 max-w-2xl mx-auto">
                            <div v-for="(stat, index) in activeModule.stats" :key="index" 
                                 class="bg-slate-50 rounded-xl p-4 border-2 border-slate-200">
                                <div class="text-3xl mb-2">{{ stat.icon }}</div>
                                <div class="text-2xl font-black text-slate-900 mb-1">{{ stat.value }}</div>
                                <div class="text-sm text-slate-500">{{ stat.label }}</div>
                            </div>
                        </div>

                        <!-- Assessment Section -->
                        <div v-if="activeModule.assessment.questions > 0" class="mb-8">
                            <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6 mb-4">
                                <h3 class="text-xl font-bold text-blue-900 mb-2">📝 Ready for Assessment?</h3>
                                <p class="text-blue-700 mb-4">
                                    {{ activeModule.assessment.type }} • {{ activeModule.assessment.questions }} questions • 
                                    Passing Score: {{ activeModule.assessment.passingScore }}%
                                </p>
                                <button @click="takeQuiz" 
                                        class="px-8 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold transition-colors">
                                    Take Assessment
                                </button>
                            </div>
                        </div>

                        <button @click="resetSimulation" 
                                class="px-8 py-4 rounded-xl text-white font-bold text-lg transition-colors shadow-lg"
                                :class="`bg-${activeModule.color}-600 hover:bg-${activeModule.color}-700`">
                            Explore More Training
                        </button>
                    </div>

                    <!-- Active Lesson -->
                    <div v-else-if="currentLesson" :key="currentLessonIndex" 
                         class="bg-white rounded-2xl shadow-xl border-4 border-slate-900 overflow-hidden">
                        
                        <!-- Lesson Header -->
                        <div class="bg-gradient-to-r p-6 text-white"
                             :class="`from-${activeModule.color}-600 to-${activeModule.color}-500`">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-2xl font-black">
                                        {{ currentLesson.number }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-xl">{{ currentLesson.title }}</h3>
                                        <p class="text-sm text-white/80">{{ currentLesson.description }}</p>
                                    </div>
                                </div>
                                <div class="text-4xl">{{ activeModule.character.avatar }}</div>
                            </div>
                        </div>

                        <!-- Lesson Content -->
                        <div class="p-8">
                            
                            <!-- Visual Demonstration -->
                            <div class="flex items-center justify-center gap-8 mb-8 rounded-xl p-8 border-4"
                                 :class="`bg-${activeModule.color}-50 border-${activeModule.color}-200`">
                                <div class="text-6xl animate-bounce">{{ currentLesson.visual }}</div>
                                <div class="text-5xl text-slate-400">→</div>
                                <div class="text-6xl animate-pulse">{{ currentLesson.action }}</div>
                                <div class="text-5xl text-slate-400">→</div>
                                <div class="text-6xl animate-bounce delay-100">{{ currentLesson.result }}</div>
                            </div>

                            <!-- Learning Content -->
                            <div class="bg-slate-50 rounded-xl p-6 mb-6 border-2 border-slate-200">
                                <h4 class="font-bold text-slate-900 mb-3 flex items-center gap-2">
                                    <span class="text-2xl">📖</span>
                                    Lesson Content
                                </h4>
                                <p class="text-slate-700 leading-relaxed text-lg">{{ currentLesson.content }}</p>
                            </div>

                            <!-- Key Learning Points -->
                            <div class="mb-6">
                                <h4 class="font-bold text-slate-900 mb-3 flex items-center gap-2">
                                    <span class="text-2xl">🎯</span>
                                    Key Learning Points
                                </h4>
                                <ul class="space-y-2">
                                    <li v-for="(point, idx) in currentLesson.keyPoints" :key="idx"
                                        class="flex items-start gap-3 text-slate-700">
                                        <span class="text-green-500 font-bold text-xl">✓</span>
                                        <span>{{ point }}</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Practical Exercise -->
                            <div class="rounded-xl p-6 border-2"
                                 :class="`bg-${activeModule.color}-50 border-${activeModule.color}-300`">
                                <h4 class="font-bold text-slate-900 mb-3 flex items-center gap-2">
                                    <span class="text-2xl">💪</span>
                                    Practical Exercise
                                </h4>
                                <p class="text-slate-700 font-medium">{{ currentLesson.practical }}</p>
                            </div>

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
</style>