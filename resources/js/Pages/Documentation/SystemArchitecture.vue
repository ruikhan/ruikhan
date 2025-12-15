<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, onUnmounted, ref, watch } from 'vue';

// --- STATE MANAGEMENT ---
const activeScenarioId = ref(null);
const isRunning = ref(false);
const isPaused = ref(false);
const progress = ref(0); 
const phase = ref(0);
const showAnalogy = ref(true);

let simulationInterval = null;

// --- ENHANCED SCENARIO DATABASE ---
const scenarios = {
    chat: {
        id: 'chat', color: 'amber', icon: '🤖', title: 'AI Assistant',
        subtitle: 'Smart Conversational Help',
        userStory: 'Maria needs a Barangay Clearance but doesn\'t know where to start',
        targetName: 'AI BRAIN', targetIcon: '🧠',
        steps: {
            1: { 
                guideline: 'USER ASKS QUESTION',
                story: 'Maria types in Tagalog: "Paano kumuha ng barangay clearance?"',
                technical: 'Frontend captures input, sanitizes HTML/XSS, sends via secure REST API to backend',
                analogy: 'Like asking a librarian "Where can I find books about gardening?" in your own language',
                visual: '💬 → 📡',
                purpose: 'Removes language barriers and complex navigation',
                benefit: 'Users can ask naturally without learning the system'
            },
            3: { 
                guideline: 'AI UNDERSTANDS INTENT',
                story: 'The system recognizes Maria wants a "Barangay Clearance" document',
                technical: 'NLP engine tokenizes text, identifies intent (document request) and entity (clearance type) using trained models',
                analogy: 'Like a translator understanding "I want food" means finding a restaurant, not a grocery',
                visual: '🧠 ⚡ 📖',
                purpose: 'Extracts meaning from casual human language',
                benefit: 'No need for exact keywords or formal phrases'
            },
            5: { 
                guideline: 'SMART RESPONSE DELIVERED',
                story: 'Maria receives step-by-step instructions with a direct "Start Request" button',
                technical: 'LLM generates contextual response with embedded action links, formatted in markdown, delivered via JSON',
                analogy: 'Like receiving a highlighted instruction manual with sticky notes on important pages',
                visual: '📋 ✅ 🚀',
                purpose: 'Provides actionable guidance, not just information',
                benefit: '24/7 assistance that feels personal and helpful'
            }
        },
        outro: {
            summary: 'Maria got instant help in her language, understood what to do, and started her request immediately—no staff needed.',
            impact: 'Reduces 80% of helpdesk calls and serves unlimited users simultaneously',
            stats: [ 
                { label: 'Response Time', value: 2, suffix: 's', icon: '⚡' }, 
                { label: 'Languages', value: 3, suffix: '+', icon: '🌐' }, 
                { label: 'Availability', value: 24, suffix: '/7', icon: '🕐' } 
            ]
        }
    },
    docs: {
        id: 'docs', color: 'cyan', icon: '📄', title: 'E-Documents',
        subtitle: 'Digital Certificates',
        userStory: 'Juan needs a Business Permit but is tired of waiting in lines',
        targetName: 'ADMIN', targetIcon: '🏛️',
        steps: {
            1: { 
                guideline: 'VERIFY IDENTITY',
                story: 'Juan uploads his valid ID and fills out the online form from home',
                technical: 'System validates ID format, performs facial recognition check against civil registry database via API',
                analogy: 'Like a bank teller checking your ID against their records before opening an account',
                visual: '🪪 → 🔍 → ✅',
                purpose: 'Prevents fraud and ensures only real residents get documents',
                benefit: 'No need to physically visit the office just to prove identity'
            },
            3: { 
                guideline: 'AUTO-GENERATE DOCUMENT',
                story: 'The system creates Juan\'s permit with all his verified information pre-filled',
                technical: 'Server pulls user data, populates PDF template, generates unique QR hash using SHA-256 encryption',
                analogy: 'Like a printer that automatically fills in your name on a form and stamps it with a security hologram',
                visual: '📝 + 🔐 = 📄',
                purpose: 'Creates tamper-proof documents instantly',
                benefit: 'Zero manual data entry errors or typos'
            },
            5: { 
                guideline: 'DIGITAL APPROVAL',
                story: 'A city official reviews and digitally signs the permit in 2 minutes',
                technical: 'Admin dashboard shows pending request, official applies digital signature using PKI certificate',
                analogy: 'Like a manager reviewing a report and putting their official stamp on it',
                visual: '👨‍💼 ✍️ ✅',
                purpose: 'Maintains legal validity with authorized approval',
                benefit: 'Juan receives his permit via email—ready to print'
            }
        },
        outro: {
            summary: 'Juan got his business permit from his phone in 5 minutes. No queues, no travel, no paperwork.',
            impact: 'Saves 2-3 office visits per document. Zero paper waste. Instant verification via QR code.',
            stats: [ 
                { label: 'Time Saved', value: 90, suffix: '%', icon: '⏱️' }, 
                { label: 'Paper Used', value: 0, suffix: '', icon: '🌳' }, 
                { label: 'Authenticity', value: 100, suffix: '%', icon: '🔒' } 
            ]
        }
    },
    sos: {
        id: 'sos', color: 'red', icon: '🚨', title: 'Emergency SOS',
        subtitle: 'Instant Distress Signal',
        userStory: 'Ana witnesses a road accident and needs help immediately',
        targetName: 'DISPATCH', targetIcon: '🚑',
        steps: {
            1: { 
                guideline: 'PANIC BUTTON PRESSED',
                story: 'Ana taps the big red "EMERGENCY" button on her phone',
                technical: 'App captures GPS coordinates (±3m accuracy), device ID, timestamp, and optional photo/note',
                analogy: 'Like pulling a fire alarm that automatically tells firefighters which building and which floor',
                visual: '🔴 → 📍 → 📸',
                purpose: 'Eliminates the need to explain "where" during a crisis',
                benefit: 'Help knows your exact location in 1 second'
            },
            3: { 
                guideline: 'PRIORITY ROUTING',
                story: 'Ana\'s alert jumps ahead of all other notifications in the system',
                technical: 'Request flagged with PRIORITY=1 header, bypasses normal queue, triggers audio alarm at control center',
                analogy: 'Like an ambulance turning on sirens—all traffic moves aside to let it pass first',
                visual: '🚨 ⚡ 🔝',
                purpose: 'Ensures zero-delay delivery of life-critical alerts',
                benefit: 'Emergency never gets buried under regular requests'
            },
            5: { 
                guideline: 'RAPID RESPONSE DEPLOYED',
                story: 'Nearest police patrol sees the alert with Ana\'s location on their GPS map',
                technical: 'Geo-fencing algorithm identifies closest units, pushes notification with coordinates and navigation link',
                analogy: 'Like a dispatcher radioing "Code Red at Main St & 5th Ave" to all nearby patrol cars',
                visual: '📡 → 🚓 → 🏃',
                purpose: 'Mobilizes help to the exact spot immediately',
                benefit: 'Response team arrives in minutes, not hours'
            }
        },
        outro: {
            summary: 'Help was dispatched to Ana\'s location in under 5 seconds. No phone calls needed. No confusion about the address.',
            impact: 'Reduces emergency response time by 70%. Saves lives through faster intervention.',
            stats: [ 
                { label: 'Alert Speed', value: 1, suffix: 's', icon: '⚡' }, 
                { label: 'GPS Accuracy', value: 3, suffix: 'm', icon: '🎯' }, 
                { label: 'Priority Level', value: 1, suffix: '', icon: '🚨' } 
            ]
        }
    },
    pay: {
        id: 'pay', color: 'emerald', icon: '💳', title: 'Bill Payments',
        subtitle: 'Cashless Transactions',
        userStory: 'Pedro wants to pay his city taxes but banks are far from his barangay',
        targetName: 'TREASURY', targetIcon: '🏦',
        steps: {
            1: { 
                guideline: 'ENTER PAYMENT DETAILS',
                story: 'Pedro selects "Real Property Tax" and enters his GCash number',
                technical: 'Frontend tokenizes card data (PCI-DSS compliant), never stores raw numbers, encrypts with TLS 1.3',
                analogy: 'Like putting cash in a locked armored bag before handing it to the bank teller',
                visual: '💰 → 🔒 → 📦',
                purpose: 'Protects financial data from hackers',
                benefit: 'Your credit card number never touches our servers'
            },
            3: { 
                guideline: 'PROCESS TRANSACTION',
                story: 'The system validates Pedro\'s payment with his bank in real-time',
                technical: 'Backend connects to payment gateway API, confirms funds, records transaction in immutable ledger (blockchain-style)',
                analogy: 'Like the accountant writing the transaction in permanent ink in an official logbook',
                visual: '🏦 ⚡ 📚',
                purpose: 'Creates permanent, auditable financial records',
                benefit: 'Corruption is impossible—every peso is tracked'
            },
            5: { 
                guideline: 'ISSUE DIGITAL RECEIPT',
                story: 'Pedro receives an Official Receipt via SMS and email instantly',
                technical: 'System generates OR with unique control number, stores copy in user account, emails PDF version',
                analogy: 'Like receiving a bank-stamped deposit slip as proof of payment',
                visual: '🧾 → 📧 → ✅',
                purpose: 'Provides legal proof of payment',
                benefit: 'No lost receipts—always accessible in your account'
            }
        },
        outro: {
            summary: 'Pedro paid his taxes from home at midnight. No bank hours. No lines. Instant proof of payment.',
            impact: 'Increases tax collection by 40%. Eliminates cash handling risks. Full transparency.',
            stats: [ 
                { label: 'Encryption', value: 256, suffix: '-bit', icon: '🔐' }, 
                { label: 'Audit Trail', value: 100, suffix: '%', icon: '📊' }, 
                { label: 'Convenience', value: 24, suffix: '/7', icon: '🕐' } 
            ]
        }
    },
    report: {
        id: 'report', color: 'orange', icon: '📢', title: 'Concerns',
        subtitle: 'Community Issues',
        userStory: 'Rosa sees a dangerous pothole on her street',
        targetName: 'ENGINEER', targetIcon: '👷',
        steps: {
            1: { 
                guideline: 'REPORT WITH PHOTO',
                story: 'Rosa takes a picture of the pothole and adds "Dangerous hole on Rizal St"',
                technical: 'App captures photo, compresses to optimal size, attaches GPS metadata (lat/long), uploads to secure storage',
                analogy: 'Like taking a polaroid picture and writing the address on the back before mailing it',
                visual: '📸 + 📍 → 📤',
                purpose: 'Provides undeniable visual evidence with location',
                benefit: 'No need to describe—the picture tells everything'
            },
            3: { 
                guideline: 'AUTO-ROUTE TO DEPARTMENT',
                story: 'The system recognizes it\'s a road issue and sends it to the Engineering Office',
                technical: 'AI classifier analyzes keywords and image, determines category, forwards to appropriate department queue',
                analogy: 'Like the post office reading the address and automatically putting it in the correct mailbox',
                visual: '🤖 → 🗂️ → 👷',
                purpose: 'Eliminates manual forwarding and delays',
                benefit: 'Right people see the problem immediately'
            },
            5: { 
                guideline: 'TRACK PROGRESS',
                story: 'Rosa gets a tracking number and sees updates: "Under Review" → "Scheduled" → "Fixed"',
                technical: 'System generates unique ticket ID, sends status notifications via push/SMS, maintains public timeline',
                analogy: 'Like tracking a package delivery—you see exactly where it is in the process',
                visual: '🎫 → 📈 → ✅',
                purpose: 'Creates accountability and transparency',
                benefit: 'Citizens see government actually responding'
            }
        },
        outro: {
            summary: 'Rosa\'s concern was documented, assigned to the right team, and fixed within 3 days. She was updated at every step.',
            impact: 'Increases civic engagement by 5x. Faster problem resolution. Builds trust in local government.',
            stats: [ 
                { label: 'Response Time', value: 2, suffix: ' hrs', icon: '⏱️' }, 
                { label: 'Transparency', value: 100, suffix: '%', icon: '👁️' }, 
                { label: 'Completion Rate', value: 95, suffix: '%', icon: '✅' } 
            ]
        }
    },
    health: {
        id: 'health', color: 'sky', icon: '🩺', title: 'E-Health',
        subtitle: 'Medical Appointments',
        userStory: 'Liza needs a prenatal checkup but the clinic is always overcrowded',
        targetName: 'CLINIC', targetIcon: '🏥',
        steps: {
            1: { 
                guideline: 'SELECT SERVICE',
                story: 'Liza chooses "Prenatal Care" and sees available dates and times',
                technical: 'System queries doctor schedules, shows only unbooked slots, handles concurrent reservations',
                analogy: 'Like looking at a restaurant reservation app showing available tables',
                visual: '🗓️ → 🔍 → ✅',
                purpose: 'Shows real-time availability',
                benefit: 'No wasted trips to find out the doctor is fully booked'
            },
            3: { 
                guideline: 'BOOK APPOINTMENT',
                story: 'Liza books Wednesday at 10 AM and the slot is instantly reserved',
                technical: 'Database transaction locks the timeslot, prevents double-booking using mutex, sends confirmation',
                analogy: 'Like reserving a seat in a movie theater—once it\'s yours, nobody else can take it',
                visual: '🎟️ → 🔒 → 📧',
                purpose: 'Guarantees your slot',
                benefit: 'No more "first come, first served" chaos'
            },
            5: { 
                guideline: 'RECEIVE QR TICKET',
                story: 'Liza gets a QR code appointment ticket on her phone',
                technical: 'System generates unique QR containing patient ID, appointment details, verification hash',
                analogy: 'Like getting a boarding pass with a barcode for easy check-in',
                visual: '📱 → QR → 🏥',
                purpose: 'Fast verification at the clinic',
                benefit: 'Just scan and you\'re checked in—no paperwork'
            }
        },
        outro: {
            summary: 'Liza booked her checkup from home, avoided the waiting room crowd, and walked straight to her appointment.',
            impact: 'Reduces waiting time by 90%. Better crowd management. Safer during health crises.',
            stats: [ 
                { label: 'Wait Reduction', value: 90, suffix: '%', icon: '⏳' }, 
                { label: 'Slot Efficiency', value: 100, suffix: '%', icon: '📊' }, 
                { label: 'Patient Safety', value: 10, suffix: '/10', icon: '🛡️' } 
            ]
        }
    }
};

// --- COMPUTED PROPERTIES ---
const activeConfig = computed(() => activeScenarioId.value ? scenarios[activeScenarioId.value] : null);

const currentInfo = computed(() => {
    if (!activeConfig.value || phase.value === 0) return null;
    if (phase.value === 1 || phase.value === 2) return activeConfig.value.steps[1];
    if (phase.value === 3 || phase.value === 4) return activeConfig.value.steps[3];
    if (phase.value === 5) return activeConfig.value.steps[5];
    return null; 
});

const phaseLabel = computed(() => {
    if (phase.value === 0) return 'READY';
    if (phase.value === 1 || phase.value === 2) return 'STEP 1 of 3';
    if (phase.value === 3 || phase.value === 4) return 'STEP 2 of 3';
    if (phase.value === 5) return 'STEP 3 of 3';
    if (phase.value === 6) return 'COMPLETE';
    return '';
});

// --- ANIMATED COUNTER ---
const displayedStats = ref([0, 0, 0]);

const animateStats = () => {
    if (!activeConfig.value) return;
    const targets = activeConfig.value.outro.stats.map(s => s.value);
    const duration = 1500;
    const steps = 30;
    const intervalTime = duration / steps;
    let currentStep = 0;

    const timer = setInterval(() => {
        currentStep++;
        displayedStats.value = targets.map(target => Math.round((target / steps) * currentStep));
        if (currentStep >= steps) clearInterval(timer);
    }, intervalTime);
};

watch(phase, (newPhase) => { if (newPhase === 6) animateStats(); });

// --- SIMULATION ENGINE ---
const startScenario = (id) => {
    resetSimulation();
    activeScenarioId.value = id;
    isRunning.value = true;
    isPaused.value = false;
    
    simulationInterval = setInterval(() => {
        if (isPaused.value || phase.value === 6) return;
        progress.value += 0.5; 
        if (progress.value < 5) phase.value = 0; 
        else if (progress.value < 25) phase.value = 1; 
        else if (progress.value < 40) phase.value = 2; 
        else if (progress.value < 60) phase.value = 3; 
        else if (progress.value < 75) phase.value = 4; 
        else if (progress.value < 95) phase.value = 5; 
        else { phase.value = 6; progress.value = 100; }
    }, 50);
};

const pauseSimulation = () => { isPaused.value = true; };
const resumeSimulation = () => { isPaused.value = false; };
const resetSimulation = () => {
    clearInterval(simulationInterval);
    isRunning.value = false;
    isPaused.value = false;
    progress.value = 0;
    phase.value = 0;
    activeScenarioId.value = null;
    displayedStats.value = [0, 0, 0];
};

onUnmounted(() => clearInterval(simulationInterval));
</script>

<template>
    <Head title="Interactive Workflow Simulator" />

    <div class="min-h-screen bg-black text-white relative overflow-hidden py-8 px-4 sm:py-12 sm:px-6">
        
        <!-- Premium Background -->
        <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-black to-slate-950"></div>
            
            <!-- Mesh gradients -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 left-1/4 w-[600px] h-[600px] bg-blue-500/20 rounded-full blur-[150px] animate-float-slow"></div>
                <div class="absolute bottom-1/4 right-1/4 w-[500px] h-[500px] bg-purple-500/20 rounded-full blur-[130px] animate-float-delayed"></div>
            </div>
            
            <!-- Noise texture -->
            <div class="absolute inset-0 opacity-[0.015] bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzMDAiIGhlaWdodD0iMzAwIj48ZmlsdGVyIGlkPSJhIiB4PSIwIiB5PSIwIj48ZmVUdXJidWxlbmNlIGJhc2VGcmVxdWVuY3k9Ii43NSIgc3RpdGNoVGlsZXM9InN0aXRjaCIgdHlwZT0iZnJhY3RhbE5vaXNlIi8+PGZlQ29sb3JNYXRyaXggdHlwZT0ic2F0dXJhdGUiIHZhbHVlcz0iMCIvPjwvZmlsdGVyPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbHRlcj0idXJsKCNhKSIgb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] mix-blend-overlay"></div>
            
            <!-- Grid pattern -->
            <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.015)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.015)_1px,transparent_1px)] bg-[size:80px_80px] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_50%,black,transparent)]"></div>
        </div>

        <!-- Content Container -->
        <div class="relative z-10 max-w-7xl mx-auto flex flex-col items-center">

            <!-- Header -->
            <div class="text-center mb-10 space-y-4">
                <div class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-full apple-glass-light text-sm font-medium animate-scale-fade shadow-lg shadow-blue-500/10">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75" v-if="isRunning && !isPaused && phase !== 6"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 shadow-lg shadow-blue-500/50" :class="isRunning && !isPaused && phase !== 6 ? 'bg-emerald-500' : 'bg-blue-500'"></span>
                    </span>
                    <span class="text-white/90 font-semibold">Interactive Workflow Simulator</span>
                </div>
                
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight bg-gradient-to-b from-white via-white to-white/70 bg-clip-text text-transparent" style="letter-spacing: -0.03em;">
                    E-PILI System Workflows
                </h1>
                
                <p class="text-base sm:text-lg text-white/50 max-w-3xl mx-auto font-light" style="letter-spacing: -0.01em;">
                    See how each module works through real-world scenarios. Watch the data flow from citizen to government in real-time.
                </p>
            </div>

            <!-- Main Content Card -->
            <div class="w-full mb-10">
                <Transition name="fade-scale-smooth" mode="out-in">
                    
                    <!-- COMPLETION STATE -->
                    <div v-if="phase === 6" key="outro" 
                         class="apple-card-completion">
                        <!-- Gradient overlay -->
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 via-purple-500/5 to-pink-500/5 rounded-[2.5rem]"></div>
                        
                        <div class="relative z-10">
                            <!-- Success Icon -->
                            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl mx-auto mb-8 flex items-center justify-center text-6xl sm:text-7xl apple-glass-light shadow-2xl transform hover:scale-105 transition-transform duration-500">
                                {{ activeConfig.icon }}
                            </div>

                            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-center mb-6 bg-gradient-to-r from-emerald-400 via-green-400 to-emerald-500 bg-clip-text text-transparent">
                                Mission Accomplished!
                            </h2>
                            
                            <!-- Summary -->
                            <div class="apple-glass-light p-6 sm:p-8 mb-8 rounded-3xl">
                                <p class="text-white/80 text-base sm:text-lg text-center leading-relaxed">{{ activeConfig.outro.summary }}</p>
                            </div>

                            <!-- Impact -->
                            <div class="apple-glass-light p-6 sm:p-8 mb-10 rounded-3xl border border-blue-500/20">
                                <div class="flex items-center justify-center gap-2 mb-4">
                                    <span class="text-2xl">💡</span>
                                    <h3 class="text-sm font-bold uppercase tracking-wider text-blue-400">Real-World Impact</h3>
                                </div>
                                <p class="text-white/70 text-sm sm:text-base text-center leading-relaxed">{{ activeConfig.outro.impact }}</p>
                            </div>
                            
                            <!-- Stats -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-10">
                                <div v-for="(stat, index) in activeConfig.outro.stats" :key="index" 
                                     class="metric-card-simulator">
                                    <div class="text-4xl sm:text-5xl mb-4">{{ stat.icon }}</div>
                                    <div class="text-4xl sm:text-5xl font-black bg-gradient-to-br from-white to-white/60 bg-clip-text text-transparent mb-2">
                                        {{ displayedStats[index] }}<span class="text-xl sm:text-2xl">{{ stat.suffix }}</span>
                                    </div>
                                    <span class="text-xs text-white/50 uppercase tracking-wider font-semibold">{{ stat.label }}</span>
                                </div>
                            </div>
                            
                            <button @click="resetSimulation" 
                                    class="apple-button-primary mx-auto px-10 py-4 text-base">
                                <span class="font-bold">Try Another Module</span>
                            </button>
                        </div>
                    </div>

                    <!-- ACTIVE STEP STATE -->
                    <div v-else-if="currentInfo" :key="phase" 
                         class="apple-card-step">
                        
                        <!-- Phase Badge -->
                        <div class="absolute top-6 right-6 px-4 py-2 rounded-full apple-glass-light">
                            <span class="text-xs font-bold uppercase tracking-wider text-white/90">{{ phaseLabel }}</span>
                        </div>

                        <!-- Step Title -->
                        <div class="mb-8">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="relative flex h-3 w-3">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
                                        :class="`bg-${activeConfig.color}-400`"></span>
                                  <span class="relative inline-flex rounded-full h-3 w-3"
                                        :class="`bg-${activeConfig.color}-500`"></span>
                                </span>
                                <h3 class="font-bold tracking-wider uppercase text-sm"
                                    :class="`text-${activeConfig.color}-400`">
                                    {{ currentInfo.guideline }}
                                </h3>
                            </div>
                            
                            <!-- User Story -->
                            <div class="flex items-start gap-4 apple-glass-light p-5 sm:p-6 rounded-2xl mb-6">
                                <div class="text-3xl sm:text-4xl flex-shrink-0">👤</div>
                                <div class="flex-grow">
                                    <h4 class="text-xs uppercase tracking-wider text-white/50 font-semibold mb-2">User Story</h4>
                                    <p class="text-white/90 text-base sm:text-lg leading-relaxed">"{{ currentInfo.story }}"</p>
                                </div>
                            </div>

                            <!-- Visual Flow -->
                            <div class="flex items-center justify-center mb-8">
                                <div class="px-8 py-4 rounded-2xl apple-glass-light text-2xl sm:text-3xl font-mono border"
                                     :class="`border-${activeConfig.color}-500/30`">
                                    {{ currentInfo.visual }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Toggle View Button -->
                        <div class="flex justify-center mb-8">
                            <button @click="showAnalogy = !showAnalogy" 
                                    class="apple-button-toggle">
                                <span v-if="showAnalogy">🎯 Show Technical Details</span>
                                <span v-else>💡 Show Simple Analogy</span>
                            </button>
                        </div>

                        <!-- Content Toggle -->
                        <Transition name="fade-slide" mode="out-in">
                            <!-- Analogy View -->
                            <div v-if="showAnalogy" key="analogy" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="apple-glass-light p-6 sm:p-8 rounded-3xl border border-blue-500/20">
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="text-4xl">💡</div>
                                        <h3 class="text-blue-400 text-sm font-bold uppercase tracking-wider">Simple Explanation</h3>
                                    </div>
                                    <p class="text-white/90 text-base sm:text-lg leading-relaxed mb-6">{{ currentInfo.analogy }}</p>
                                    <div class="flex items-start gap-3 apple-glass-light p-4 rounded-xl">
                                        <span class="text-xl flex-shrink-0">🎯</span>
                                        <div>
                                            <h4 class="text-xs uppercase tracking-wider text-white/50 font-semibold mb-2">Why This Matters</h4>
                                            <p class="text-white/80 text-sm leading-relaxed"
                                               :class="`text-${activeConfig.color}-300`">{{ currentInfo.purpose }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="apple-glass-light p-6 sm:p-8 rounded-3xl border border-emerald-500/20">
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="text-4xl">🎁</div>
                                        <h3 class="text-emerald-400 text-sm font-bold uppercase tracking-wider">Citizen Benefit</h3>
                                    </div>
                                    <p class="text-white/90 text-base sm:text-lg leading-relaxed">{{ currentInfo.benefit }}</p>
                                </div>
                            </div>

                            <!-- Technical View -->
                            <div v-else key="technical" class="apple-glass-light p-6 sm:p-8 rounded-3xl border border-purple-500/20">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="text-4xl">⚙️</div>
                                    <h3 class="text-purple-400 text-sm font-bold uppercase tracking-wider">Technical Implementation</h3>
                                </div>
                                <p class="text-white/80 text-sm sm:text-base leading-relaxed font-mono mb-6">{{ currentInfo.technical }}</p>
                                <div class="flex items-start gap-3 apple-glass-light p-4 rounded-xl">
                                    <span class="text-xl flex-shrink-0">🎯</span>
                                    <div>
                                        <h4 class="text-xs uppercase tracking-wider text-white/50 font-semibold mb-2">System Purpose</h4>
                                        <p class="text-white/80 text-sm leading-relaxed"
                                           :class="`text-${activeConfig.color}-300`">{{ currentInfo.purpose }}</p>
                                    </div>
                                </div>
                            </div>
                        </Transition>
                        
                    </div>
                    
                    <!-- IDLE STATE -->
                    <div v-else class="apple-card-idle">
                        <div class="text-7xl sm:text-8xl mb-6 opacity-50 animate-float-slow">🎬</div>
                        <h3 class="text-xl sm:text-2xl font-bold text-white/80 mb-3 uppercase tracking-wide">Ready to Begin</h3>
                        <p class="text-sm sm:text-base text-white/50 max-w-md mx-auto">Select a module below to see how it works step-by-step</p>
                    </div>
                </Transition>
            </div>

            <!-- Workflow Visualization -->
            <div class="relative w-full apple-card-workflow mb-10">
                
                <!-- Progress Bar -->
                <div class="absolute top-0 left-0 h-1 bg-white/5 w-full rounded-t-[2.5rem] overflow-hidden">
                    <div class="h-full transition-all duration-100 ease-linear shadow-[0_0_20px_currentColor]"
                         :class="`bg-${activeConfig?.color || 'blue'}-500`"
                         :style="{ width: `${progress}%` }"></div>
                </div>

                <!-- Flow Diagram -->
                <div class="flex flex-col md:flex-row items-center justify-between gap-12 md:gap-8 lg:gap-12 relative py-12 px-6 sm:px-10">
                    
                    <!-- Connection Line -->
                    <div class="absolute top-1/2 left-0 right-0 h-[2px] bg-white/5 -z-10 hidden md:block"></div>
                    <div class="absolute left-1/2 top-0 bottom-0 w-[2px] bg-white/5 -z-10 md:hidden"></div>

                    <!-- CITIZEN -->
                    <div class="workflow-node" 
                         :class="{ 'workflow-node-active': phase >= 1 }">
                        <div class="workflow-node-icon"
                             :class="phase >= 1 && activeConfig ? `border-${activeConfig.color}-500 shadow-[0_0_40px_currentColor]` : 'border-white/10'">
                            <span class="text-5xl sm:text-6xl relative z-10">👤</span>
                            <div v-if="phase === 1" :class="`absolute inset-0 bg-${activeConfig.color}-500/20 animate-pulse rounded-3xl`"></div>
                        </div>
                        <div class="workflow-node-label">
                            <h3 class="font-bold text-white text-sm uppercase tracking-wider">Citizen</h3>
                            <p v-if="activeConfig" class="text-xs text-white/50 mt-1">{{ activeConfig.userStory.split(' ')[0] }}</p>
                        </div>
                    </div>

                    <!-- Data Packet Animation -->
                    <div v-if="phase === 2 && !isPaused" 
                         :class="`workflow-packet bg-${activeConfig.color}-500 shadow-[0_0_30px_currentColor]`"
                         style="left: 25%;"></div>

                    <!-- CLOUD SERVER -->
                    <div class="workflow-node" 
                         :class="{ 'workflow-node-active': phase >= 3 && phase <= 4 }">
                        <div class="workflow-node-icon-large"
                             :class="phase >= 3 && activeConfig ? `border-${activeConfig.color}-500 shadow-[0_0_60px_currentColor]` : 'border-white/10'">
                            <span class="text-6xl sm:text-7xl relative z-10">☁️</span>
                            <div v-if="phase === 3 || phase === 4" :class="`absolute inset-0 bg-${activeConfig.color}-500/20 animate-pulse rounded-full`"></div>
                            <div v-if="phase === 3 || phase === 4" class="absolute inset-0 flex items-center justify-center">
                                <div :class="`w-24 h-24 sm:w-28 sm:h-28 border-4 rounded-full animate-spin border-${activeConfig.color}-500 border-t-transparent`"></div>
                            </div>
                        </div>
                        <div class="workflow-node-label mt-6">
                            <h3 class="font-bold text-white text-sm uppercase tracking-wider">E-PILI Server</h3>
                            <p v-if="phase >= 3" :class="`text-xs mt-1 font-mono text-${activeConfig?.color || 'blue'}-400`">PROCESSING...</p>
                        </div>
                    </div>

                    <!-- Data Packet Animation -->
                    <div v-if="phase === 4 && !isPaused" 
                         :class="`workflow-packet bg-${activeConfig.color}-500 shadow-[0_0_30px_currentColor]`"
                         style="right: 25%;"></div>

                    <!-- TARGET -->
                    <div class="workflow-node" 
                         :class="{ 'workflow-node-active': phase >= 5 }">
                        <div class="workflow-node-icon"
                             :class="phase >= 5 && activeConfig ? `border-${activeConfig.color}-500 shadow-[0_0_40px_currentColor]` : 'border-white/10'">
                            <span class="text-5xl sm:text-6xl relative z-10">{{ activeConfig ? activeConfig.targetIcon : '🎯' }}</span>
                            <div v-if="phase === 5" :class="`absolute inset-0 bg-${activeConfig.color}-500/20 animate-pulse rounded-3xl`"></div>
                        </div>
                        <div class="workflow-node-label">
                            <h3 class="font-bold text-white text-sm uppercase tracking-wider">{{ activeConfig ? activeConfig.targetName : 'Target' }}</h3>
                            <p v-if="phase >= 5" class="text-xs text-emerald-400 mt-1 font-semibold">RECEIVED ✓</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Controls -->
            <div class="flex gap-3 sm:gap-4 mb-10 flex-wrap justify-center">
                <button @click="isPaused ? resumeSimulation() : pauseSimulation()" 
                        :disabled="!isRunning || phase === 6"
                        class="apple-button-control"
                        :class="{ 'opacity-40 cursor-not-allowed': !isRunning || phase === 6 }">
                    <span>{{ isPaused ? '▶ Resume' : '⏸ Pause' }}</span>
                </button>
                <button @click="resetSimulation" 
                        :disabled="!isRunning && phase !== 6"
                        class="apple-button-control-danger"
                        :class="{ 'opacity-40 cursor-not-allowed': !isRunning && phase !== 6 }">
                    ⏹ Reset
                </button>
            </div>

            <!-- Module Selection Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4 w-full mb-16">
                <button v-for="scenario in scenarios" :key="scenario.id" 
                        @click="startScenario(scenario.id)"
                        :disabled="isRunning && activeScenarioId !== scenario.id"
                        class="module-selector"
                        :class="[
                            activeScenarioId === scenario.id 
                                ? `module-selector-active border-${scenario.color}-500` 
                                : '',
                            isRunning && activeScenarioId !== scenario.id ? 'module-selector-disabled' : ''
                        ]">
                    
                    <!-- Active Indicator -->
                    <div v-if="activeScenarioId === scenario.id" 
                         :class="`absolute inset-0 bg-gradient-to-br rounded-3xl animate-pulse from-${scenario.color}-500/10 to-transparent`"></div>
                    
                    <span class="text-4xl sm:text-5xl transform transition-transform duration-300 group-hover:scale-110 relative z-10 mb-3">{{ scenario.icon }}</span>
                    <div class="text-center relative z-10 px-2">
                        <span class="text-xs sm:text-sm font-bold block leading-tight text-white mb-1">{{ scenario.title }}</span>
                        <span class="text-[10px] text-white/50 block">{{ scenario.subtitle }}</span>
                    </div>
                    
                    <!-- Active ring -->
                    <div v-if="activeScenarioId === scenario.id" 
                         :class="`absolute inset-0 rounded-3xl border-2 animate-pulse border-${scenario.color}-500`"></div>
                </button>
            </div>

            <!-- Back Button -->
            <Link :href="route('dashboard')" 
                  class="apple-button-back">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span class="font-semibold">Back to Dashboard</span>
            </Link>

        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=SF+Mono:wght@400;500;600;700&display=swap');

* {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    letter-spacing: -0.01em;
}

/* ==================== ANIMATIONS ==================== */

@keyframes float-slow {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}
.animate-float-slow { animation: float-slow 8s ease-in-out infinite; }

@keyframes float-delayed {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(20px); }
}
.animate-float-delayed { animation: float-delayed 10s ease-in-out infinite; }

@keyframes scale-fade {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}
.animate-scale-fade { 
    animation: scale-fade 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* ==================== TRANSITIONS ==================== */

.fade-scale-smooth-enter-active {
    transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}
.fade-scale-smooth-leave-active {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.fade-scale-smooth-enter-from,
.fade-scale-smooth-leave-to {
    opacity: 0;
    transform: scale(0.95);
}

.fade-slide-enter-active {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.fade-slide-leave-active {
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.fade-slide-enter-from {
    opacity: 0;
    transform: translateY(20px);
}
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}

/* ==================== APPLE GLASS ==================== */

.apple-glass-light {
    background: rgba(255, 255, 255, 0.04);
    backdrop-filter: blur(40px) saturate(180%);
    -webkit-backdrop-filter: blur(40px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

/* ==================== CARDS ==================== */

.apple-card-completion {
    @apply relative overflow-hidden p-10 sm:p-12 lg:p-16 rounded-[2.5rem];
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(60px) saturate(180%);
    -webkit-backdrop-filter: blur(60px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 
        0 20px 80px rgba(0, 0, 0, 0.5),
        inset 0 1px 0 rgba(255, 255, 255, 0.08);
}

.apple-card-step {
    @apply relative overflow-hidden p-8 sm:p-10 lg:p-12 rounded-[2.5rem];
    background: rgba(255, 255, 255, 0.02);
    backdrop-filter: blur(40px) saturate(180%);
    -webkit-backdrop-filter: blur(40px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.06);
    box-shadow: 
        0 12px 48px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

.apple-card-idle {
    @apply flex flex-col items-center justify-center text-center p-16 sm:p-20 rounded-[2.5rem] min-h-[400px];
    background: rgba(255, 255, 255, 0.015);
    backdrop-filter: blur(40px);
    -webkit-backdrop-filter: blur(40px);
    border: 2px dashed rgba(255, 255, 255, 0.08);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
}

.apple-card-workflow {
    @apply overflow-hidden rounded-[2.5rem];
    background: rgba(255, 255, 255, 0.02);
    backdrop-filter: blur(40px) saturate(180%);
    -webkit-backdrop-filter: blur(40px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.06);
    box-shadow: 
        0 12px 48px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

.metric-card-simulator {
    @apply flex flex-col items-center justify-center p-6 sm:p-8 rounded-3xl text-center transition-all duration-500 hover:scale-105;
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.08);
}

/* ==================== BUTTONS ==================== */

.apple-button-primary {
    @apply inline-flex items-center gap-3 px-10 py-4 rounded-full transition-all duration-300;
    background: linear-gradient(135deg, rgba(59, 130, 246, 1), rgba(147, 51, 234, 1));
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 
        0 8px 32px rgba(59, 130, 246, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.apple-button-primary:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 
        0 16px 48px rgba(59, 130, 246, 0.5),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.apple-button-toggle {
    @apply px-8 py-3 rounded-full text-sm font-semibold uppercase tracking-wider transition-all duration-300;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.3);
}

.apple-button-toggle:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.apple-button-control {
    @apply px-8 py-3 rounded-full text-sm font-bold uppercase tracking-wider transition-all duration-300;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.3);
}

.apple-button-control:hover:not(:disabled) {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-2px);
}

.apple-button-control-danger {
    @apply px-8 py-3 rounded-full text-sm font-bold uppercase tracking-wider transition-all duration-300;
    background: rgba(239, 68, 68, 0.1);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: rgba(252, 165, 165, 1);
    box-shadow: 0 4px 24px rgba(239, 68, 68, 0.2);
}

.apple-button-control-danger:hover:not(:disabled) {
    background: rgba(239, 68, 68, 0.15);
    border-color: rgba(239, 68, 68, 0.4);
    transform: translateY(-2px);
}

.apple-button-back {
    @apply flex items-center gap-3 px-6 py-3 rounded-full text-sm font-semibold transition-all duration-300;
    background: rgba(255, 255, 255, 0.04);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    color: rgba(255, 255, 255, 0.6);
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.3);
}

.apple-button-back:hover {
    background: rgba(255, 255, 255, 0.08);
    color: rgba(255, 255, 255, 1);
    transform: translateY(-2px);
}

/* ==================== WORKFLOW NODES ==================== */

.workflow-node {
    @apply relative flex flex-col items-center gap-4 transition-all duration-500;
    opacity: 0.4;
}

.workflow-node-active {
    opacity: 1;
    transform: scale(1.05);
}

.workflow-node-icon {
    @apply w-28 h-28 sm:w-32 sm:h-32 rounded-3xl flex items-center justify-center shadow-2xl relative overflow-hidden transition-all duration-500;
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 2px solid;
}

.workflow-node-icon-large {
    @apply w-36 h-36 sm:w-44 sm:h-44 rounded-full flex items-center justify-center shadow-2xl relative overflow-hidden transition-all duration-500;
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 2px solid;
}

.workflow-node-label {
    @apply text-center px-5 py-2.5 rounded-full transition-all duration-300;
    background: rgba(255, 255, 255, 0.04);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.workflow-packet {
    @apply w-8 h-8 rounded-full absolute top-1/2 -translate-y-1/2 z-20 animate-pulse;
}

/* ==================== MODULE SELECTOR ==================== */

.module-selector {
    backdrop-filter: blur(40px);
    -webkit-backdrop-filter: blur(40px);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

.module-selector:hover:not(:disabled) {
    transform: translateY(-4px) scale(1.02);
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(255, 255, 255, 0.15);
    box-shadow: 
        0 16px 48px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.08);
}

.module-selector-active {
    transform: scale(1.05);
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.5),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.module-selector-disabled {
    opacity: 0.2;
    filter: grayscale(1);
    cursor: not-allowed;
}

/* ==================== FORCE TAILWIND COLORS ==================== */

.border-cyan-500, .border-red-500, .border-emerald-500, .border-sky-500, .border-orange-500, .border-amber-500 {}
.text-cyan-400, .text-red-400, .text-emerald-400, .text-sky-400, .text-orange-400, .text-amber-400 {}
.text-cyan-300, .text-red-300, .text-emerald-300, .text-sky-300, .text-orange-300, .text-amber-300 {}
.bg-cyan-500, .bg-red-500, .bg-emerald-500, .bg-sky-500, .bg-orange-500, .bg-amber-500 {}
.from-cyan-500, .from-red-500, .from-emerald-500, .from-sky-500, .from-orange-500, .from-amber-500 {}

/* ==================== RESPONSIVE ==================== */

@media (max-width: 768px) {
    .apple-card-completion,
    .apple-card-step {
        @apply p-6;
    }
}
</style>