<script setup>
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const activeSection = ref('overview');
const selectedModule = ref(null);
const acceptedTerms = ref(false);
const scrollProgress = ref(0);
const isTransitioning = ref(false);
const mouseX = ref(0);
const mouseY = ref(0);
const hoveredCard = ref(null);

const sections = [
    { id: 'overview', label: 'Overview', icon: '○' },
    { id: 'problem', label: 'Challenge', icon: '△' },
    { id: 'solution', label: 'Solution', icon: '◇' },
    { id: 'modules', label: 'Modules', icon: '□' },
    { id: 'features', label: 'Features', icon: '⬡' },
    { id: 'impact', label: 'Impact', icon: '⬢' }
];

const modules = [
    {
        id: 'documents',
        name: 'E-Documents',
        icon: '📄',
        color: 'from-blue-500/20 via-cyan-500/10 to-transparent',
        accentColor: 'bg-blue-500',
        purpose: 'Digital issuance of barangay and municipal certificates',
        workflow: 'Request → Verify → Generate → Approve → Deliver',
        benefits: ['Zero paper waste', '90% faster processing', '24/7 accessibility'],
        status: 'Ready'
    },
    {
        id: 'chat',
        name: 'AI Assistant',
        icon: '🤖',
        color: 'from-amber-500/20 via-yellow-500/10 to-transparent',
        accentColor: 'bg-amber-500',
        purpose: 'Conversational help for navigating services',
        workflow: 'Ask → Understand → Respond',
        benefits: ['Multilingual support', 'Instant responses', 'Reduces helpdesk load'],
        status: 'Ready'
    },
    {
        id: 'emergency',
        name: 'Emergency SOS',
        icon: '🚨',
        color: 'from-red-500/20 via-pink-500/10 to-transparent',
        accentColor: 'bg-red-500',
        purpose: 'Real-time distress alert with GPS coordination',
        workflow: 'Alert → GPS → Dispatch',
        benefits: ['Sub-second response', '3m GPS accuracy', 'Life-saving speed'],
        status: 'Ready'
    },
    {
        id: 'payments',
        name: 'Bill Payments',
        icon: '💳',
        color: 'from-teal-500/20 via-emerald-500/10 to-transparent',
        accentColor: 'bg-teal-500',
        purpose: 'Cashless municipal payments and receipts',
        workflow: 'Select → Pay → Verify → Receipt',
        benefits: ['Eliminates cash', 'Complete audit trail', 'Instant confirmation'],
        status: 'Ready'
    },
    {
        id: 'health',
        name: 'E-Health',
        icon: '🩺',
        color: 'from-sky-500/20 via-cyan-500/10 to-transparent',
        accentColor: 'bg-sky-500',
        purpose: 'Digital appointment booking and health records',
        workflow: 'Select → Check → Book → Ticket',
        benefits: ['90% less wait time', 'Prevents crowding', 'Better allocation'],
        status: 'Ready'
    },
    {
        id: 'concerns',
        name: 'Issue Reporting',
        icon: '📢',
        color: 'from-indigo-500/20 via-blue-500/10 to-transparent',
        accentColor: 'bg-indigo-500',
        purpose: 'Crowdsourced community problem reporting',
        workflow: 'Capture → Route → Assign → Track',
        benefits: ['Geo-tagged evidence', 'Transparent tracking', 'Faster resolution'],
        status: 'Ready'
    }
];

const keyFeatures = [
    {
        category: 'User Experience',
        icon: '✨',
        color: 'from-blue-500/10 to-purple-500/10',
        features: [
            { name: 'Responsive Design', desc: 'Seamless across all devices', icon: '📱' },
            { name: 'Intuitive Navigation', desc: 'Clear visual hierarchy', icon: '🧭' },
            { name: 'Real-time Feedback', desc: 'Instant status updates', icon: '⚡' },
            { name: 'Accessibility', desc: 'WCAG 2.1 compliant', icon: '♿' }
        ]
    },
    {
        category: 'Security',
        icon: '🔐',
        color: 'from-emerald-500/10 to-teal-500/10',
        features: [
            { name: 'End-to-End Encryption', desc: 'TLS 1.3 protection', icon: '🔒' },
            { name: 'Access Control', desc: 'Granular permissions', icon: '🎯' },
            { name: 'Data Protection', desc: 'GDPR-inspired privacy', icon: '🛡️' },
            { name: 'Audit Logging', desc: 'Complete traceability', icon: '📋' }
        ]
    },
    {
        category: 'Governance',
        icon: '⚖️',
        color: 'from-amber-500/10 to-orange-500/10',
        features: [
            { name: 'DICT Compliant', desc: 'Cloud First Policy aligned', icon: '☁️' },
            { name: 'Transparency', desc: 'Public dashboards', icon: '📊' },
            { name: 'Data Sovereignty', desc: 'Philippine jurisdiction', icon: '🇵🇭' },
            { name: 'Open Standards', desc: 'Vendor-neutral', icon: '🔓' }
        ]
    }
];

const impactMetrics = [
    { label: 'Time Saved', value: '85', suffix: '%', icon: '⚡', color: 'from-blue-500 to-cyan-500' },
    { label: 'Visits Eliminated', value: '2-3', suffix: '', icon: '🏢', color: 'from-purple-500 to-pink-500' },
    { label: 'Paper Saved', value: '10K', suffix: '+', icon: '🌳', color: 'from-emerald-500 to-teal-500' },
    { label: 'Availability', value: '24/7', suffix: '', icon: '⏰', color: 'from-amber-500 to-orange-500' },
    { label: 'Satisfaction', value: '95', suffix: '%', icon: '⭐', color: 'from-yellow-500 to-amber-500' },
    { label: 'Cost Reduction', value: '40', suffix: '%', icon: '💰', color: 'from-green-500 to-emerald-500' }
];

const scrollToSection = (sectionId) => {
    activeSection.value = sectionId;
    const element = document.getElementById(sectionId);
    if (element) {
        const offset = 100;
        const elementPosition = element.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - offset;
        
        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }
};

const canProceed = computed(() => acceptedTerms.value);

const handleScroll = () => {
    const winScroll = document.documentElement.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = (winScroll / height) * 100;
    scrollProgress.value = scrolled;

    sections.forEach(section => {
        const element = document.getElementById(section.id);
        if (element) {
            const rect = element.getBoundingClientRect();
            if (rect.top <= 200 && rect.bottom >= 200) {
                activeSection.value = section.id;
            }
        }
    });
};

const handleMouseMove = (e) => {
    mouseX.value = e.clientX;
    mouseY.value = e.clientY;
};

const proceedToDashboard = () => {
    isTransitioning.value = true;
    
    setTimeout(() => {
        window.location.href = route('dashboard');
    }, 1800);
};

const setupIntersectionObserver = () => {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-visible');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-on-scroll').forEach(section => {
        observer.observe(section);
    });
};

onMounted(() => {
    window.scrollTo(0, 0);
    window.addEventListener('scroll', handleScroll);
    window.addEventListener('mousemove', handleMouseMove);
    setupIntersectionObserver();
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('mousemove', handleMouseMove);
});
</script>

<template>
    <Head title="Welcome to E-PILI" />

    <div class="min-h-screen bg-[#000000] text-white relative overflow-x-hidden">
        
        <!-- Ultra Premium Background -->
        <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
            <!-- Base gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-black to-slate-950"></div>
            
            <!-- Mesh gradient overlay -->
            <div class="absolute inset-0 opacity-30">
                <div class="absolute top-0 left-1/4 w-[600px] h-[600px] bg-blue-500/20 rounded-full blur-[150px] animate-float-slow"></div>
                <div class="absolute top-1/3 right-1/4 w-[500px] h-[500px] bg-purple-500/20 rounded-full blur-[130px] animate-float-delayed"></div>
                <div class="absolute bottom-1/4 left-1/3 w-[450px] h-[450px] bg-cyan-500/15 rounded-full blur-[120px] animate-float-slower"></div>
            </div>
            
            <!-- Noise texture for depth -->
            <div class="absolute inset-0 opacity-[0.015] bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzMDAiIGhlaWdodD0iMzAwIj48ZmlsdGVyIGlkPSJhIiB4PSIwIiB5PSIwIj48ZmVUdXJidWxlbmNlIGJhc2VGcmVxdWVuY3k9Ii43NSIgc3RpdGNoVGlsZXM9InN0aXRjaCIgdHlwZT0iZnJhY3RhbE5vaXNlIi8+PGZlQ29sb3JNYXRyaXggdHlwZT0ic2F0dXJhdGUiIHZhbHVlcz0iMCIvPjwvZmlsdGVyPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbHRlcj0idXJsKCNhKSIgb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] mix-blend-overlay"></div>
            
            <!-- Grid pattern -->
            <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.02)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.02)_1px,transparent_1px)] bg-[size:100px_100px] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_50%,black,transparent)]"></div>
        </div>

        <!-- Scroll Progress -->
        <div class="fixed top-0 left-0 right-0 h-[2px] bg-white/[0.03] z-50 backdrop-blur-xl">
            <div class="h-full bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 transition-all duration-300 ease-out shadow-[0_0_20px_rgba(59,130,246,0.5)]" 
                 :style="{ width: scrollProgress + '%' }"></div>
        </div>

        <!-- Premium Floating Navigation -->
        <nav class="fixed top-8 left-1/2 -translate-x-1/2 z-40 transition-all duration-700 ease-out"
             :class="scrollProgress > 3 ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-8'">
            <div class="apple-glass px-3 py-2.5 rounded-full flex items-center gap-1.5 shadow-2xl shadow-black/20">
                <button 
                    v-for="section in sections" 
                    :key="section.id"
                    @click="scrollToSection(section.id)"
                    :class="[
                        'nav-dot group relative transition-all duration-300',
                        activeSection === section.id ? 'active scale-110' : 'hover:scale-105'
                    ]"
                >
                    <span class="dot-icon">{{ section.icon }}</span>
                    <div class="dot-tooltip">
                        <span class="text-xs font-semibold">{{ section.label }}</span>
                    </div>
                </button>
            </div>
        </nav>

        <!-- Apple-Style Transition Overlay -->
        <Transition name="dashboard-fade">
            <div v-if="isTransitioning" class="fixed inset-0 z-[200] flex items-center justify-center">
                <!-- Multi-layer background -->
                <div class="absolute inset-0 bg-black/98 backdrop-blur-3xl"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-slate-900/50 via-transparent to-blue-900/30"></div>
                
                <!-- Radial glow -->
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(59,130,246,0.15),transparent_70%)]"></div>
                
                <!-- Expanding rings -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="absolute w-32 h-32 rounded-full border-[3px] border-white/20 animate-expand-fade"></div>
                    <div class="absolute w-64 h-64 rounded-full border-[2px] border-white/10 animate-expand-fade-delay-1"></div>
                    <div class="absolute w-96 h-96 rounded-full border border-white/5 animate-expand-fade-delay-2"></div>
                </div>

                <!-- Content -->
                <div class="relative z-10 flex flex-col items-center">
                    <!-- Logo container with glow -->
                    <div class="relative mb-10">
                        <div class="absolute inset-0 w-48 h-48 -translate-x-1/2 -translate-y-1/2 left-1/2 top-1/2 bg-blue-500/30 rounded-full blur-[100px] animate-glow-breathe"></div>
                        <img src="/images/enotpili.png" 
                             class="h-32 w-auto relative drop-shadow-[0_20px_80px_rgba(59,130,246,0.6)] animate-logo-entrance" 
                             alt="E-PILI" />
                    </div>
                    
                    <!-- Text -->
                    <h2 class="text-5xl font-bold text-white mb-4 tracking-tight animate-fade-up" 
                        style="letter-spacing: -0.02em;">E-PILI</h2>
                    <p class="text-white/60 text-base font-medium tracking-wide animate-fade-up mb-12" 
                       style="animation-delay: 0.15s;">
                        Launching Dashboard
                    </p>
                    
                    <!-- Premium loading indicator -->
                    <div class="w-64 h-1 bg-white/[0.06] rounded-full overflow-hidden backdrop-blur-sm">
                        <div class="h-full bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 rounded-full animate-loading-slide shadow-[0_0_25px_rgba(59,130,246,0.6)]"></div>
                    </div>
                    
                    <!-- Activity dots -->
                    <div class="mt-10 flex gap-3">
                        <div v-for="i in 3" :key="i" 
                             class="w-2 h-2 bg-white/40 rounded-full animate-pulse-dot"
                             :style="{ animationDelay: `${(i - 1) * 0.2}s` }">
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Main Content -->
        <div class="relative z-10">
            
            <!-- Hero Section -->
            <section class="min-h-screen flex items-center justify-center px-6 py-20 relative">
                <div class="max-w-6xl mx-auto text-center space-y-10">
                    <!-- Status badge -->
                    <div class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-full apple-glass-light text-sm font-medium animate-scale-fade shadow-lg shadow-blue-500/10">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-500 shadow-lg shadow-blue-500/50"></span>
                        </span>
                        <span class="text-white/90 font-semibold">Digital Governance Platform</span>
                    </div>
                    
                    <!-- Main title -->
                    <div class="space-y-6">
                        <h1 class="animate-fade-up-slow">
                            <span class="block text-7xl sm:text-8xl lg:text-9xl font-black tracking-tight mb-6 bg-gradient-to-b from-white via-white to-white/70 bg-clip-text text-transparent" style="letter-spacing: -0.03em;">
                                E-PILI
                            </span>
                            <span class="block text-3xl sm:text-4xl lg:text-5xl font-semibold text-white/50" style="letter-spacing: -0.02em;">
                                Transforming Municipal Services
                            </span>
                        </h1>
                    </div>
                    
                    <!-- Description -->
                    <p class="text-lg sm:text-xl lg:text-2xl text-white/50 max-w-3xl mx-auto leading-relaxed animate-fade-up-slow font-light" style="animation-delay: 0.2s; letter-spacing: -0.01em;">
                        A comprehensive digital platform designed to reduce workflow burden while improving operational efficiency through modern, intuitive design.
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-wrap gap-4 justify-center pt-6 animate-fade-up-slow" style="animation-delay: 0.3s;">
                        <button @click="scrollToSection('solution')" 
                                class="apple-button-primary group">
                            <span class="font-semibold">Explore Platform</span>
                            <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </button>
                        <button @click="scrollToSection('modules')" 
                                class="apple-button-secondary">
                            <span class="font-semibold">View Modules</span>
                        </button>
                    </div>
                </div>
            </section>

            <!-- Overview Section -->
            <section id="overview" class="section-padding animate-on-scroll">
                <div class="container-premium space-y-16">
                    <div class="text-center space-y-4">
                        <div class="inline-flex items-center gap-2 mb-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse-slow"></div>
                            <span class="text-sm font-semibold text-blue-400 uppercase tracking-wider">Executive Summary</span>
                        </div>
                        <h2 class="section-title-premium">Platform Overview</h2>
                        <p class="text-xl text-white/40 max-w-2xl mx-auto font-light">Strategic digital transformation initiative</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                        <div v-for="(item, index) in [
                            { icon: '🎯', title: 'Vision', desc: 'Create a centralized digital command center empowering residents with anytime, anywhere access to municipal services.' },
                            { icon: '🚀', title: 'Mission', desc: 'Deliver implementation-ready solutions that reduce manual intervention and strengthen citizen trust through transparency.' },
                            { icon: '💡', title: 'Innovation', desc: 'Leverage modern web technologies and AI-powered assistance to build a scalable platform that evolves with needs.' }
                        ]" :key="index"
                             class="apple-card group"
                             :style="{ animationDelay: `${index * 0.1}s` }">
                            <div class="text-5xl mb-6 transform transition-all duration-500 group-hover:scale-110 group-hover:rotate-3">
                                {{ item.icon }}
                            </div>
                            <h3 class="text-2xl font-bold mb-4">{{ item.title }}</h3>
                            <p class="text-white/60 leading-relaxed">{{ item.desc }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Problem Section -->
            <section id="problem" class="section-padding animate-on-scroll">
                <div class="container-premium space-y-16">
                    <div class="text-center space-y-4">
                        <h2 class="section-title-premium">The Challenge</h2>
                        <p class="text-xl text-white/40 max-w-2xl mx-auto font-light">Current obstacles in service delivery</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="apple-card border-l-4 border-red-500/50">
                            <div class="flex items-center gap-4 mb-8">
                                <span class="text-4xl">👥</span>
                                <h3 class="text-2xl font-bold">Citizen Pain Points</h3>
                            </div>
                            <ul class="space-y-4">
                                <li v-for="item in [
                                    'Long waiting times - hours spent in queues',
                                    'Multiple office visits - 2-4 trips per transaction',
                                    'Limited hours - only during office hours',
                                    'Manual paperwork - risk of lost documents'
                                ]" :key="item"
                                    class="flex items-start gap-3 text-white/70 pl-4 border-l-2 border-white/10 hover:border-red-500/50 transition-colors duration-300">
                                    <span class="text-red-400 mt-1 flex-shrink-0">•</span>
                                    <span>{{ item }}</span>
                                </li>
                            </ul>
                        </div>

                        <div class="apple-card border-l-4 border-orange-500/50">
                            <div class="flex items-center gap-4 mb-8">
                                <span class="text-4xl">🏛️</span>
                                <h3 class="text-2xl font-bold">Government Challenges</h3>
                            </div>
                            <ul class="space-y-4">
                                <li v-for="item in [
                                    'High operational costs - paper and processing',
                                    'Limited visibility - no real-time tracking',
                                    'Data silos - fragmented information',
                                    'Accountability gaps - difficult to track actions'
                                ]" :key="item"
                                    class="flex items-start gap-3 text-white/70 pl-4 border-l-2 border-white/10 hover:border-orange-500/50 transition-colors duration-300">
                                    <span class="text-orange-400 mt-1 flex-shrink-0">•</span>
                                    <span>{{ item }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Solution Section -->
            <section id="solution" class="section-padding animate-on-scroll">
                <div class="container-premium space-y-16">
                    <div class="text-center space-y-4">
                        <h2 class="section-title-premium">The Solution</h2>
                        <p class="text-xl text-white/40 max-w-2xl mx-auto font-light">Comprehensive digital platform with proven implementation</p>
                    </div>

                    <div class="apple-card-large">
                        <div class="text-center max-w-3xl mx-auto space-y-8">
                            <h3 class="text-4xl lg:text-5xl font-bold bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                                E-PILI Digital Governance Platform
                            </h3>
                            <p class="text-xl text-white/60 leading-relaxed">
                                A centralized digital command center built on modern Vue.js architecture, enabling residents to access core services through clearly grouped, visually guided modules.
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-12">
                            <div v-for="item in [
                                { icon: '🎨', label: 'User-Centered' },
                                { icon: '🔧', label: 'Modular' },
                                { icon: '🔒', label: 'Secure' },
                                { icon: '📱', label: 'Cross-Platform' }
                            ]" :key="item.label"
                                 class="apple-badge group">
                                <span class="text-4xl mb-3 block transform transition-transform duration-300 group-hover:scale-110">{{ item.icon }}</span>
                                <span class="text-sm font-bold">{{ item.label }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Modules Section -->
            <section id="modules" class="section-padding animate-on-scroll">
                <div class="container-premium space-y-16">
                    <div class="text-center space-y-4">
                        <h2 class="section-title-premium">Core Modules</h2>
                        <p class="text-xl text-white/40 max-w-2xl mx-auto font-light">Six production-ready service modules</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                        <div 
                            v-for="(module, index) in modules" 
                            :key="module.id"
                            @click="selectedModule = selectedModule === module.id ? null : module.id"
                            @mouseenter="hoveredCard = module.id"
                            @mouseleave="hoveredCard = null"
                            :class="['module-card-premium group cursor-pointer', 
                                     selectedModule === module.id ? 'module-active' : '']"
                            :style="{ animationDelay: `${index * 0.1}s` }"
                        >
                            <!-- Gradient overlay -->
                            <div :class="['absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-3xl', module.color]"></div>
                            
                            <div class="relative z-10">
                                <!-- Header -->
                                <div class="flex items-start justify-between mb-6">
                                    <div class="w-14 h-14 rounded-2xl bg-white/5 backdrop-blur-xl border border-white/10 flex items-center justify-center text-3xl transform transition-all duration-500 group-hover:scale-110 group-hover:rotate-6 group-hover:bg-white/10">
                                        {{ module.icon }}
                                    </div>
                                    <span class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold uppercase backdrop-blur-xl">
                                        {{ module.status }}
                                    </span>
                                </div>
                                
                                <!-- Content -->
                                <h3 class="text-2xl font-bold mb-3">{{ module.name }}</h3>
                                <p class="text-white/60 text-sm leading-relaxed mb-6">{{ module.purpose }}</p>
                                
                                <!-- Expandable details -->
                                <Transition name="expand-smooth">
                                    <div v-if="selectedModule === module.id" class="space-y-5 pt-6 border-t border-white/10">
                                        <div>
                                            <h4 class="text-xs font-bold uppercase text-white/40 tracking-wider mb-2">Workflow</h4>
                                            <p class="text-sm text-white/70 font-mono">{{ module.workflow }}</p>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold uppercase text-white/40 tracking-wider mb-3">Benefits</h4>
                                            <ul class="space-y-2">
                                                <li v-for="(benefit, idx) in module.benefits" :key="idx"
                                                    class="flex items-center gap-2 text-sm text-white/70">
                                                    <span class="text-emerald-400 text-lg">✓</span>
                                                    <span>{{ benefit }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </Transition>
                                
                                <!-- Toggle button -->
                                <button class="apple-button-minimal group/btn mt-6">
                                    <span class="text-sm font-semibold">{{ selectedModule === module.id ? 'Show Less' : 'Learn More' }}</span>
                                    <svg class="w-4 h-4 transition-transform duration-300" 
                                         :class="selectedModule === module.id ? 'rotate-180' : ''"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section id="features" class="section-padding animate-on-scroll">
                <div class="container-premium space-y-16">
                    <div class="text-center space-y-4">
                        <h2 class="section-title-premium">Key Features</h2>
                        <p class="text-xl text-white/40 max-w-2xl mx-auto font-light">Comprehensive platform capabilities</p>
                    </div>

                    <div class="space-y-8">
                        <div v-for="(category, index) in keyFeatures" :key="category.category" 
                             class="apple-card-large group"
                             :style="{ animationDelay: `${index * 0.1}s` }">
                            <!-- Gradient background -->
                            <div :class="['absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-100 transition-opacity duration-700 rounded-3xl', category.color]"></div>
                            
                            <div class="relative z-10">
                                <div class="flex items-center gap-4 mb-10">
                                    <span class="text-5xl">{{ category.icon }}</span>
                                    <h3 class="text-3xl font-bold">{{ category.category }}</h3>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div v-for="feature in category.features" :key="feature.name" 
                                         class="flex items-start gap-4 p-5 rounded-2xl bg-white/[0.02] border border-white/5 hover:bg-white/[0.04] hover:border-white/10 transition-all duration-300">
                                        <div class="text-2xl flex-shrink-0">{{ feature.icon }}</div>
                                        <div>
                                            <h4 class="font-bold mb-1.5">{{ feature.name }}</h4>
                                            <p class="text-sm text-white/60">{{ feature.desc }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Impact Section -->
            <section id="impact" class="section-padding animate-on-scroll">
                <div class="container-premium space-y-16">
                    <div class="text-center space-y-4">
                        <h2 class="section-title-premium">Expected Impact</h2>
                        <p class="text-xl text-white/40 max-w-2xl mx-auto font-light">Measurable improvements across all metrics</p>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 lg:gap-6">
                        <div v-for="(metric, index) in impactMetrics" :key="metric.label" 
                             class="metric-card-premium group"
                             :style="{ animationDelay: `${index * 0.05}s` }">
                            <!-- Gradient top border -->
                            <div :class="['absolute top-0 left-4 right-4 h-1 rounded-full bg-gradient-to-r opacity-0 group-hover:opacity-100 transition-opacity duration-500', metric.color]"></div>
                            
                            <div class="text-4xl mb-4 transform transition-all duration-500 group-hover:scale-110">
                                {{ metric.icon }}
                            </div>
                            <div class="text-4xl font-black mb-2 bg-gradient-to-br from-white to-white/60 bg-clip-text text-transparent">
                                {{ metric.value }}<span class="text-2xl">{{ metric.suffix }}</span>
                            </div>
                            <p class="text-xs text-white/50 uppercase tracking-wider font-bold">{{ metric.label }}</p>
                        </div>
                    </div>

                    <div class="apple-card-large mt-12">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                            <div>
                                <h4 class="text-2xl font-bold mb-6 text-blue-400">For Citizens</h4>
                                <ul class="space-y-4">
                                    <li v-for="item in [
                                        '24/7 access from any device',
                                        'Real-time tracking with transparency',
                                        'Eliminate multiple office visits',
                                        'Instant confirmations and receipts'
                                    ]" :key="item"
                                        class="flex items-start gap-3 text-white/70">
                                        <span class="text-blue-400 text-xl flex-shrink-0">✓</span>
                                        <span>{{ item }}</span>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="text-2xl font-bold mb-6 text-purple-400">For Government</h4>
                                <ul class="space-y-4">
                                    <li v-for="item in [
                                        'Reduce operational costs significantly',
                                        'Real-time visibility into metrics',
                                        'Complete accountability with audit trails',
                                        'Data-driven decision making'
                                    ]" :key="item"
                                        class="flex items-start gap-3 text-white/70">
                                        <span class="text-purple-400 text-xl flex-shrink-0">✓</span>
                                        <span>{{ item }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section id="proceed" class="section-padding animate-on-scroll">
                <div class="container-premium">
                    <div class="apple-cta-card">
                        <div class="relative z-10 text-center space-y-8">
                            <h2 class="text-5xl lg:text-6xl font-bold mb-6">Ready to Get Started?</h2>
                            <p class="text-xl text-white/60 max-w-3xl mx-auto leading-relaxed">
                                By proceeding, you acknowledge understanding of the platform capabilities and intended use of the E-PILI Digital Governance System.
                            </p>
                            
                            <!-- Terms checkbox -->
                            <div class="apple-checkbox-container">
                                <div class="flex items-start gap-4">
                                    <input 
                                        type="checkbox" 
                                        id="terms" 
                                        v-model="acceptedTerms"
                                        class="apple-checkbox"
                                    />
                                    <label for="terms" class="text-left text-white/80 cursor-pointer select-none hover:text-white transition-colors">
                                        I understand and agree to use this platform responsibly in accordance with local regulations and guidelines.
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Action buttons -->
                            <div class="flex flex-wrap gap-4 justify-center pt-4">
                                <button 
                                    v-if="canProceed"
                                    @click="proceedToDashboard"
                                    class="apple-button-primary group px-12 py-5 text-lg"
                                >
                                    <span class="font-bold">Access Dashboard</span>
                                    <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </button>
                                <button 
                                    v-else
                                    disabled
                                    class="apple-button-disabled px-12 py-5 text-lg"
                                >
                                    <span class="font-bold">Please Accept Terms</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Premium Footer -->
        <footer class="border-t border-white/5 py-16 px-6 relative z-10 backdrop-blur-xl">
            <div class="container-premium">
                <div class="text-center space-y-4">
                    <p class="text-sm text-white/40 font-medium">
                        E-PILI Digital Governance Platform
                    </p>
                    <p class="text-xs text-white/30">
                        © 2025 • Built with Vue.js 3 & Laravel 10 • Designed in the Philippines
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=SF+Pro+Display:wght@300;400;500;600;700;800;900&display=swap');

* {
    font-family: 'SF Pro Display', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    letter-spacing: -0.01em;
}

/* ==================== ANIMATIONS ==================== */

@keyframes float-slow {
    0%, 100% { transform: translateY(0px) translateX(0px); }
    33% { transform: translateY(-30px) translateX(20px); }
    66% { transform: translateY(15px) translateX(-15px); }
}
.animate-float-slow { animation: float-slow 20s ease-in-out infinite; }

@keyframes float-delayed {
    0%, 100% { transform: translateY(0px) translateX(0px); }
    33% { transform: translateY(25px) translateX(-20px); }
    66% { transform: translateY(-15px) translateX(15px); }
}
.animate-float-delayed { animation: float-delayed 25s ease-in-out infinite; }

@keyframes float-slower {
    0%, 100% { transform: translateY(0px) translateX(0px); }
    50% { transform: translateY(-20px) translateX(10px); }
}
.animate-float-slower { animation: float-slower 30s ease-in-out infinite; }

@keyframes scale-fade {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}
.animate-scale-fade { 
    animation: scale-fade 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fade-up-slow {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-up-slow { 
    animation: fade-up-slow 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes expand-fade {
    0% { transform: scale(0.8); opacity: 0.8; }
    100% { transform: scale(2.5); opacity: 0; }
}
.animate-expand-fade { 
    animation: expand-fade 2s cubic-bezier(0.16, 1, 0.3, 1) infinite;
}
.animate-expand-fade-delay-1 { 
    animation: expand-fade 2s cubic-bezier(0.16, 1, 0.3, 1) 0.4s infinite;
}
.animate-expand-fade-delay-2 { 
    animation: expand-fade 2s cubic-bezier(0.16, 1, 0.3, 1) 0.8s infinite;
}

@keyframes glow-breathe {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.1); }
}
.animate-glow-breathe {
    animation: glow-breathe 3s ease-in-out infinite;
}

@keyframes logo-entrance {
    0% { 
        opacity: 0; 
        transform: scale(0.8) translateY(20px); 
    }
    60% { 
        opacity: 1; 
        transform: scale(1.05) translateY(0); 
    }
    100% { 
        opacity: 1; 
        transform: scale(1) translateY(0); 
    }
}
.animate-logo-entrance {
    animation: logo-entrance 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fade-up {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-up {
    animation: fade-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes loading-slide {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(500%); }
}
.animate-loading-slide {
    animation: loading-slide 2s cubic-bezier(0.65, 0, 0.35, 1) infinite;
}

@keyframes pulse-dot {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.2); }
}
.animate-pulse-dot {
    animation: pulse-dot 1.5s ease-in-out infinite;
}

@keyframes pulse-slow {
    0%, 100% { opacity: 0.4; }
    50% { opacity: 1; }
}
.animate-pulse-slow {
    animation: pulse-slow 3s ease-in-out infinite;
}

/* ==================== TRANSITIONS ==================== */

.dashboard-fade-enter-active {
    transition: opacity 1s cubic-bezier(0.16, 1, 0.3, 1);
}
.dashboard-fade-leave-active {
    transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
.dashboard-fade-enter-from,
.dashboard-fade-leave-to {
    opacity: 0;
}

.expand-smooth-enter-active {
    transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}
.expand-smooth-leave-active {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.expand-smooth-enter-from,
.expand-smooth-leave-to {
    opacity: 0;
    max-height: 0;
    transform: translateY(-10px);
}
.expand-smooth-enter-to,
.expand-smooth-leave-from {
    opacity: 1;
    max-height: 500px;
    transform: translateY(0);
}

/* ==================== APPLE GLASS MORPHISM ==================== */

.apple-glass {
    background: rgba(255, 255, 255, 0.04);
    backdrop-filter: blur(40px) saturate(180%);
    -webkit-backdrop-filter: blur(40px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.apple-glass-light {
    background: rgba(255, 255, 255, 0.06);
    backdrop-filter: blur(40px) saturate(180%);
    -webkit-backdrop-filter: blur(40px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.15);
}

.apple-card {
    @apply relative overflow-hidden p-8 lg:p-10 rounded-3xl transition-all duration-500;
    background: rgba(255, 255, 255, 0.02);
    backdrop-filter: blur(40px) saturate(180%);
    -webkit-backdrop-filter: blur(40px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.06);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

.apple-card:hover {
    transform: translateY(-4px);
    border-color: rgba(255, 255, 255, 0.12);
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.5),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.apple-card-large {
    @apply apple-card p-10 lg:p-16;
}

.apple-badge {
    @apply flex flex-col items-center justify-center p-6 rounded-2xl transition-all duration-500;
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.06);
}

.apple-badge:hover {
    transform: translateY(-2px) scale(1.02);
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.1);
}

.module-card-premium {
    @apply relative overflow-hidden p-8 rounded-3xl transition-all duration-500;
    background: rgba(255, 255, 255, 0.02);
    backdrop-filter: blur(40px) saturate(180%);
    -webkit-backdrop-filter: blur(40px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.06);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

.module-card-premium:hover {
    transform: translateY(-6px) scale(1.01);
    border-color: rgba(255, 255, 255, 0.15);
    box-shadow: 
        0 24px 72px rgba(0, 0, 0, 0.5),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.module-active {
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(255, 255, 255, 0.15);
    transform: scale(1.02);
}

.metric-card-premium {
    @apply relative overflow-hidden p-8 rounded-3xl text-center transition-all duration-500;
    background: rgba(255, 255, 255, 0.02);
    backdrop-filter: blur(40px) saturate(180%);
    -webkit-backdrop-filter: blur(40px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.06);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

.metric-card-premium:hover {
    transform: translateY(-6px);
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(255, 255, 255, 0.12);
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.5),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.apple-cta-card {
    @apply relative rounded-[2.5rem] p-12 lg:p-20 overflow-hidden;
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(60px) saturate(180%);
    -webkit-backdrop-filter: blur(60px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 
        0 20px 80px rgba(0, 0, 0, 0.5),
        inset 0 1px 0 rgba(255, 255, 255, 0.08);
}

.apple-cta-card::before {
    content: '';
    @apply absolute inset-0 bg-gradient-to-br from-blue-600/10 via-purple-600/5 to-pink-600/10;
}

.apple-checkbox-container {
    @apply max-w-3xl mx-auto p-8 rounded-3xl;
    background: rgba(255, 255, 255, 0.02);
    backdrop-filter: blur(40px);
    -webkit-backdrop-filter: blur(40px);
    border: 1px solid rgba(255, 255, 255, 0.06);
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

.apple-button-primary:active {
    transform: translateY(0) scale(0.98);
}

.apple-button-secondary {
    @apply inline-flex items-center gap-3 px-10 py-4 rounded-full transition-all duration-300;
    background: rgba(255, 255, 255, 0.04);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 
        0 4px 24px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.apple-button-secondary:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.apple-button-minimal {
    @apply w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl transition-all duration-300;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.06);
}

.apple-button-minimal:hover {
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(255, 255, 255, 0.12);
}

.apple-button-disabled {
    @apply inline-flex items-center gap-3 rounded-full cursor-not-allowed;
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.05);
    color: rgba(255, 255, 255, 0.3);
}

/* ==================== NAVIGATION ==================== */

.nav-dot {
    @apply relative w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300;
    background: transparent;
}

.nav-dot:hover {
    background: rgba(255, 255, 255, 0.08);
}

.nav-dot.active {
    background: rgba(255, 255, 255, 0.12);
}

.nav-dot.active .dot-icon {
    @apply text-white scale-110;
}

.dot-icon {
    @apply text-sm text-white/60 transition-all duration-300;
}

.dot-tooltip {
    @apply absolute top-full mt-3 px-4 py-2 rounded-xl opacity-0 pointer-events-none transition-all duration-300;
    background: rgba(0, 0, 0, 0.9);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
    white-space: nowrap;
}

.nav-dot:hover .dot-tooltip {
    @apply opacity-100 translate-y-1;
}

/* ==================== CUSTOM CHECKBOX ==================== */

.apple-checkbox {
    @apply w-6 h-6 rounded-lg cursor-pointer transition-all duration-300;
    appearance: none;
    background: rgba(255, 255, 255, 0.05);
    border: 2px solid rgba(255, 255, 255, 0.1);
    position: relative;
    flex-shrink: 0;
}

.apple-checkbox:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.2);
}

.apple-checkbox:checked {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    border-color: transparent;
}

.apple-checkbox:checked::after {
    content: '✓';
    @apply absolute inset-0 flex items-center justify-center text-white text-sm font-bold;
}

/* ==================== LAYOUT ==================== */

.section-padding {
    @apply py-32 lg:py-40 px-6;
}

.container-premium {
    @apply max-w-7xl mx-auto;
}

.section-title-premium {
    @apply text-5xl lg:text-6xl font-bold mb-4 bg-gradient-to-b from-white to-white/60 bg-clip-text text-transparent;
    letter-spacing: -0.03em;
}

/* ==================== SCROLL ANIMATIONS ==================== */

.animate-on-scroll {
    opacity: 0;
    transform: translateY(60px);
    transition: opacity 1s cubic-bezier(0.16, 1, 0.3, 1),
                transform 1s cubic-bezier(0.16, 1, 0.3, 1);
}

.animate-on-scroll.animate-visible {
    opacity: 1;
    transform: translateY(0);
}

/* ==================== SCROLLBAR ==================== */

::-webkit-scrollbar {
    width: 10px;
}

::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.2);
}

::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    border: 2px solid transparent;
    background-clip: padding-box;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.2);
    background-clip: padding-box;
}

html {
    scroll-behavior: smooth;
}

/* ==================== RESPONSIVE ==================== */

@media (max-width: 1024px) {
    .section-padding {
        @apply py-24 px-4;
    }
    
    .section-title-premium {
        @apply text-4xl;
    }
}

@media (max-width: 768px) {
    .section-padding {
        @apply py-20 px-4;
    }
    
    .section-title-premium {
        @apply text-3xl;
    }
    
    .apple-card,
    .apple-card-large {
        @apply p-6;
    }
    
    .module-card-premium {
        @apply p-6;
    }
    
    .metric-card-premium {
        @apply p-6;
    }
}

@media (max-width: 640px) {
    .apple-cta-card {
        @apply p-8;
    }
}
</style>