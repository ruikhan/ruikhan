<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import mermaid from 'mermaid';
import { nextTick, onMounted, ref, watch } from 'vue';

// --- CONFIGURATION ---
mermaid.initialize({ 
    startOnLoad: false,
    theme: 'dark',
    securityLevel: 'loose',
    fontFamily: 'Inter, system-ui, sans-serif',
    flowchart: { 
        curve: 'basis',
        padding: 20
    }
});

const activeTab = ref('architecture');
const isLoading = ref(false);

// --- DATA DEFINITIONS ---
const tabs = [
    { 
        id: 'architecture', 
        label: 'System Architecture', 
        icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
        badge: 'Core',
        color: 'blue'
    },
    { 
        id: 'workflow', 
        label: 'User Workflows', 
        icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        badge: 'UX',
        color: 'purple'
    },
    { 
        id: 'database', 
        label: 'Data Schema', 
        icon: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4',
        badge: 'Data',
        color: 'emerald'
    },
    { 
        id: 'auth', 
        label: 'Security Flow', 
        icon: 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
        badge: 'Sec',
        color: 'red'
    },
    { 
        id: 'deployment', 
        label: 'Infrastructure', 
        icon: 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01',
        badge: 'Infra',
        color: 'cyan'
    },
];

const content = {
    architecture: {
        title: "Multi-Tier System Architecture",
        desc: "Layered architecture following MVC and separation of concerns principles.",
        standard: "ISO/IEC 25010 | DICT Cloud First Policy",
        details: [
            {
                title: "Presentation Layer",
                content: "Vue.js 3 with Composition API and Inertia.js for seamless SPA experience. Component-based architecture ensures maintainability.",
                icon: "🎨"
            },
            {
                title: "Application Layer",
                content: "Laravel 10 framework with SOLID principles. Controllers handle requests, Models manage data, Services contain business logic.",
                icon: "⚙️"
            },
            {
                title: "Data Layer",
                content: "MySQL 8.0 with InnoDB for ACID compliance. Proper indexing and foreign keys ensure data integrity.",
                icon: "💾"
            },
            {
                title: "Security Layer",
                content: "Laravel Sanctum authentication, CSRF protection, XSS prevention, and SQL injection protection via prepared statements.",
                icon: "🔒"
            }
        ],
        type: 'mermaid',
        code: `
        graph TB
            subgraph Client ["CLIENT TIER"]
                Browser[Web Browser] -->|HTTPS/TLS| CDN[CDN]
            end
            
            subgraph LB ["LOAD BALANCER"]
                CDN --> Nginx[Nginx LB]
            end
            
            subgraph App ["APPLICATION TIER"]
                Nginx --> Laravel[Laravel App]
                Laravel --> Router[Router]
                Router --> Auth[Auth]
                Auth --> Controllers[Controllers]
                Controllers --> Services[Services]
                Services --> Models[Models]
            end
            
            subgraph Cache ["CACHE"]
                Redis[(Redis)]
                Services -.-> Redis
            end
            
            subgraph Data ["DATA TIER"]
                Models --> DB[(MySQL)]
                Models --> Storage[File Storage]
            end
            
            classDef client fill:#3b82f6,stroke:#2563eb,stroke-width:2px,color:#fff
            classDef app fill:#8b5cf6,stroke:#7c3aed,stroke-width:2px,color:#fff
            classDef data fill:#10b981,stroke:#059669,stroke-width:2px,color:#fff
            
            class Browser,CDN client
            class Laravel,Router,Auth,Controllers,Services,Models app
            class DB,Storage data
        `
    },
    workflow: {
        title: "User Journey Flow",
        desc: "Complete lifecycle from authentication through service fulfillment.",
        standard: "ISO 9241-11 Usability Standards",
        details: [
            {
                title: "Authentication",
                content: "Secure login with bcrypt hashing (cost 12). Session management via HTTP-only cookies with SameSite strict policy.",
                icon: "🔐"
            },
            {
                title: "Request Process",
                content: "Multi-step wizard with validation. Unique 12-character tracking codes generated for status monitoring.",
                icon: "📋"
            },
            {
                title: "Status Tracking",
                content: "Real-time polling every 30 seconds. Push notifications for status changes with complete audit trail.",
                icon: "📊"
            },
            {
                title: "Payment Flow",
                content: "Multiple gateway support (PayMaya, GCash) with webhook verification and idempotency keys.",
                icon: "💳"
            }
        ],
        type: 'mermaid',
        code: `
        sequenceDiagram
            autonumber
            actor R as Resident
            participant UI as Frontend
            participant API as Backend
            participant DB as Database
            participant Email as Email
            
            rect rgb(30, 41, 59)
                note over R,Email: AUTHENTICATION
                R->>UI: Visit Login
                UI->>API: POST /login
                API->>DB: Verify User
                DB-->>API: User Data
                API->>API: Verify Password
                API-->>UI: Auth Token
                UI->>R: Redirect Dashboard
            end
            
            rect rgb(15, 23, 42)
                note over R,Email: DOCUMENT REQUEST
                R->>UI: Request Document
                UI->>API: GET /documents
                API-->>UI: Document Types
                R->>UI: Fill Form & Upload
                UI->>API: POST /documents/request
                API->>DB: Store Request
                DB-->>API: Tracking Code
                API->>Email: Send Confirmation
                API-->>UI: Success
                UI->>R: Show Tracking Code
            end
            
            loop Status Check
                R->>UI: Check Status
                UI->>API: GET /track/{code}
                API->>DB: Query Status
                DB-->>API: Current Status
                API-->>UI: Status Data
                UI->>R: Display Status
            end
        `
    },
    database: {
        title: "Database Schema (ERD)",
        desc: "Normalized relational model in 3NF with referential integrity.",
        standard: "3NF Compliance | ACID Transactions",
        details: [
            {
                title: "Normalization",
                content: "Schema designed in 3NF to eliminate redundancy. Each table represents single entity with atomic attributes.",
                icon: "📐"
            },
            {
                title: "Indexing Strategy",
                content: "Primary keys with AUTO_INCREMENT. Foreign keys indexed for JOIN optimization. Composite indexes on query columns.",
                icon: "🔍"
            },
            {
                title: "Constraints",
                content: "Foreign keys enforce integrity with CASCADE/RESTRICT. CHECK constraints validate ranges. UNIQUE prevents duplicates.",
                icon: "🔗"
            },
            {
                title: "Audit Trail",
                content: "All tables include timestamps. Soft deletes via deleted_at. Dedicated audit_logs table tracks user actions.",
                icon: "📝"
            }
        ],
        type: 'mermaid',
        code: `
        erDiagram
            USERS ||--o{ DOCUMENT_REQUESTS : submits
            USERS ||--o{ CONCERNS : reports
            USERS ||--o{ BILL_PAYMENTS : makes
            
            USERS {
                bigint id PK
                string name
                string email UK
                string password
                enum role
                timestamp created_at
            }
            
            DOCUMENT_REQUESTS ||--|| DOCUMENT_TYPES : belongs_to
            
            DOCUMENT_REQUESTS {
                bigint id PK
                bigint user_id FK
                string tracking_code UK
                enum status
                text purpose
                string valid_id_path
                timestamp created_at
            }
            
            DOCUMENT_TYPES {
                bigint id PK
                string name UK
                decimal processing_fee
                int processing_days
                json requirements
            }
            
            CONCERNS ||--|| CONCERN_CATEGORIES : categorized_by
            
            CONCERNS {
                bigint id PK
                bigint user_id FK
                string ticket_number UK
                string subject
                text description
                enum priority
                enum status
                timestamp created_at
            }
            
            CONCERN_CATEGORIES {
                bigint id PK
                string name UK
                text description
                boolean is_active
            }
            
            BILL_PAYMENTS {
                bigint id PK
                bigint user_id FK
                string bill_type
                decimal amount
                enum payment_method
                enum status
                timestamp paid_at
            }
        `
    },
    auth: {
        title: "Authentication Security Flow",
        desc: "Multi-layer security with Laravel Sanctum and RBAC.",
        standard: "NIST SP 800-63B | OWASP Compliance",
        details: [
            {
                title: "Password Security",
                content: "Min 8 chars with complexity. Bcrypt hashing (cost 12 = 4,096 rounds). Password history prevents reuse. Lockout after 5 failed attempts.",
                icon: "🔑"
            },
            {
                title: "Session Management",
                content: "Server-side sessions with 2-hour timeout. HTTP-only cookies prevent XSS. SameSite strict prevents CSRF.",
                icon: "⏱️"
            },
            {
                title: "Multi-Factor Auth",
                content: "TOTP-based 2FA with Google Authenticator. Backup codes and SMS fallback. Hardware token support for admins.",
                icon: "📱"
            },
            {
                title: "Access Control",
                content: "Role-Based Access Control with three tiers. Policy classes define permissions. Middleware gates protect routes.",
                icon: "🛡️"
            }
        ],
        type: 'mermaid',
        code: `
        flowchart TD
            Start([User Login]) --> Input[Enter Credentials]
            Input --> Validate{Valid Format?}
            Validate -->|No| Error1[Show Errors]
            Error1 --> Input
            
            Validate -->|Yes| Submit[POST /login]
            Submit --> CSRF{CSRF Valid?}
            CSRF -->|No| Error2[419 Expired]
            
            CSRF -->|Yes| RateLimit{Rate Limit OK?}
            RateLimit -->|No| Error3[429 Too Many]
            
            RateLimit -->|Yes| CheckUser{User Exists?}
            CheckUser -->|No| Error4[401 Unauthorized]
            
            CheckUser -->|Yes| VerifyPass{Password Match?}
            VerifyPass -->|No| Increment[Increment Fails]
            Increment --> Lock{Lock Account?}
            Lock -->|Yes| Error5[Account Locked]
            Lock -->|No| Error4
            
            VerifyPass -->|Yes| MFA{MFA Enabled?}
            MFA -->|Yes| PromptMFA[Enter Code]
            PromptMFA --> VerifyMFA{Code Valid?}
            VerifyMFA -->|No| Error6[Invalid Code]
            VerifyMFA -->|Yes| CreateSession
            
            MFA -->|No| CreateSession[Generate Token]
            CreateSession --> SetCookie[Set Cookie]
            SetCookie --> CheckRole{Check Role}
            
            CheckRole -->|Admin| AdminDash[Admin Dashboard]
            CheckRole -->|Staff| StaffDash[Staff Dashboard]
            CheckRole -->|Resident| UserDash[User Dashboard]
            
            AdminDash --> Success([Authenticated])
            StaffDash --> Success
            UserDash --> Success
            
            style Start fill:#3b82f6,stroke:#2563eb,stroke-width:2px,color:#fff
            style Success fill:#10b981,stroke:#059669,stroke-width:2px,color:#fff
            style Error2 fill:#ef4444,stroke:#dc2626,stroke-width:2px,color:#fff
            style Error3 fill:#ef4444,stroke:#dc2626,stroke-width:2px,color:#fff
            style Error4 fill:#ef4444,stroke:#dc2626,stroke-width:2px,color:#fff
            style Error5 fill:#ef4444,stroke:#dc2626,stroke-width:2px,color:#fff
        `
    },
    deployment: {
        title: "Cloud Infrastructure",
        desc: "Complete deployment with CI/CD, monitoring, and high availability.",
        standard: "DICT Cloud First | 99.9% Uptime SLA",
        details: [
            {
                title: "Containerization",
                content: "Docker containers for isolation. Multi-stage builds reduce image size to ~150MB. Environment configs via .env files.",
                icon: "📦"
            },
            {
                title: "CI/CD Pipeline",
                content: "Automated testing with 80%+ coverage. Static analysis (PHPStan). Zero-downtime blue-green deployment.",
                icon: "🚀"
            },
            {
                title: "Infrastructure as Code",
                content: "Terraform provisions resources. Ansible configures servers. Automated backups with point-in-time recovery.",
                icon: "💻"
            },
            {
                title: "Monitoring",
                content: "APM via New Relic/Datadog. Centralized logging with ELK Stack. Real-time alerting on errors and performance.",
                icon: "📈"
            }
        ],
        type: 'mermaid',
        code: `
        graph TB
            subgraph Dev ["DEVELOPMENT"]
                DevPC[Developer] -->|git push| Git[Git Repo]
            end
            
            subgraph CICD ["CI/CD PIPELINE"]
                Git -->|trigger| Pipeline[GitLab CI]
                Pipeline --> Test[Run Tests]
                Test -->|pass| Build[Build Image]
                Build --> Scan[Security Scan]
                Scan --> Registry[(Docker Registry)]
            end
            
            subgraph Prod ["PRODUCTION"]
                Registry --> LB[Load Balancer]
                LB --> Web1[Web Server 1]
                LB --> Web2[Web Server 2]
                Web1 --> App1[Laravel App]
                Web2 --> App2[Laravel App]
                App1 --> Cache[(Redis)]
                App2 --> Cache
                App1 --> DB[(MySQL)]
                App2 --> DB
            end
            
            subgraph Monitor ["MONITORING"]
                App1 -.-> Prometheus[Prometheus]
                App2 -.-> Prometheus
                Prometheus --> Grafana[Grafana]
            end
            
            subgraph CDN ["CDN"]
                LB --> CloudFront[CloudFront]
                CloudFront --> Users[End Users]
            end
            
            style DevPC fill:#3b82f6,stroke:#2563eb,stroke-width:2px,color:#fff
            style Pipeline fill:#8b5cf6,stroke:#7c3aed,stroke-width:2px,color:#fff
            style LB fill:#10b981,stroke:#059669,stroke-width:2px,color:#fff
            style Users fill:#06b6d4,stroke:#0891b2,stroke-width:2px,color:#fff
        `
    }
};

// --- RENDER LOGIC ---
const renderDiagram = async () => {
    isLoading.value = true;
    await nextTick();
    
    const element = document.querySelector('.mermaid-container');
    if (element && content[activeTab.value].type === 'mermaid') {
        element.removeAttribute('data-processed');
        element.innerHTML = content[activeTab.value].code;
        try {
            await mermaid.run({ nodes: [element] });
        } catch (e) {
            console.error('Mermaid render error:', e);
            element.innerHTML = '<div class="text-red-400 p-4 text-sm">Error rendering diagram. Please refresh.</div>';
        }
    }
    
    setTimeout(() => {
        isLoading.value = false;
    }, 300);
};

onMounted(() => {
    renderDiagram();
});

watch(activeTab, () => {
    renderDiagram();
});
</script>

<template>
    <Head title="System Documentation" />

    <AuthenticatedLayout>
        <!-- Premium Background -->
        <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-black to-slate-950"></div>
            
            <!-- Mesh gradients -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-1/4 left-1/4 w-[600px] h-[600px] bg-blue-500/20 rounded-full blur-[150px] animate-float-slow"></div>
                <div class="absolute bottom-1/4 right-1/4 w-[500px] h-[500px] bg-purple-500/20 rounded-full blur-[130px] animate-float-delayed"></div>
            </div>
            
            <!-- Noise texture -->
            <div class="absolute inset-0 opacity-[0.015] bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzMDAiIGhlaWdodD0iMzAwIj48ZmlsdGVyIGlkPSJhIiB4PSIwIiB5PSIwIj48ZmVUdXJidWxlbmNlIGJhc2VGcmVxdWVuY3k9Ii43NSIgc3RpdGNoVGlsZXM9InN0aXRjaCIgdHlwZT0iZnJhY3RhbE5vaXNlIi8+PGZlQ29sb3JNYXRyaXggdHlwZT0ic2F0dXJhdGUiIHZhbHVlcz0iMCIvPjwvZmlsdGVyPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbHRlcj0idXJsKCNhKSIgb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] mix-blend-overlay"></div>
            
            <!-- Grid pattern -->
            <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.015)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.015)_1px,transparent_1px)] bg-[size:80px_80px] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_50%,black,transparent)]"></div>
        </div>

        <div class="relative z-10 py-6 sm:py-8 lg:py-12 px-4 sm:px-6 lg:px-8 max-w-[1800px] mx-auto">
            
            <!-- Header -->
            <div class="mb-8 space-y-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="space-y-2">
                        <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full apple-glass-light text-xs font-medium animate-scale-fade shadow-lg shadow-blue-500/10">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500 shadow-lg shadow-blue-500/50"></span>
                            </span>
                            <span class="text-white/90 font-semibold">System Documentation</span>
                        </div>
                        
                        <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight bg-gradient-to-b from-white via-white to-white/70 bg-clip-text text-transparent" style="letter-spacing: -0.03em;">
                            Technical Diagrams
                        </h2>
                        
                        <p class="text-base sm:text-lg text-white/50 font-light max-w-2xl" style="letter-spacing: -0.01em;">
                            Interactive visual documentation of the E-PILI platform architecture and workflows
                        </p>
                    </div>
                    
                    <div class="flex gap-2 flex-wrap">
                        <span class="status-badge status-live">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse-slow"></span>
                            <span class="font-bold">LIVE</span>
                        </span>
                        <span class="status-badge status-tech">
                            <span class="font-semibold">Laravel • Vue • Mermaid</span>
                        </span>
                        <span class="status-badge status-version">
                            <span class="font-semibold">v1.0.5</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">
                
                <!-- Sidebar -->
                <div class="w-full lg:w-80 xl:w-96 flex-shrink-0 space-y-6">
                    <!-- Navigation -->
                    <div class="apple-card-sidebar">
                        <h3 class="sidebar-title">Navigation</h3>
                        <div class="space-y-2">
                                <button 
                                    v-for="tab in tabs" 
                                    :key="tab.id" 
                                    @click="activeTab = tab.id"
                                    :class="['nav-tab-button group', activeTab === tab.id ? 'nav-tab-active' : '']"
                                >
                                <svg class="nav-tab-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" :d="tab.icon"></path>
                                </svg>
                                <div class="flex-grow min-w-0">
                                    <span class="nav-tab-label">{{ tab.label }}</span>
                                </div>
                                <span :class="['nav-tab-badge', activeTab === tab.id ? 'badge-active' : '']">
                                    {{ tab.badge }}
                                </span>
                                
                                <!-- Active indicator -->
                                <div v-if="activeTab === tab.id" class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-gradient-to-b from-blue-500 to-purple-500 rounded-r-full"></div>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="apple-card-sidebar">
                        <h3 class="sidebar-title">Quick Actions</h3>
                        <div class="space-y-3">
                            <button 
                                onclick="window.print()" 
                                class="action-button action-primary"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                <span class="font-bold">Export PDF</span>
                            </button>
                            
                            <button 
                                @click="renderDiagram()" 
                                class="action-button action-secondary"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span class="font-bold">Refresh Diagram</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Info Card -->
                    <div class="apple-card-sidebar border-blue-500/20">
                        <div class="flex items-start gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl apple-glass-light flex items-center justify-center text-xl flex-shrink-0">
                                ℹ️
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-white mb-1">About Diagrams</h3>
                                <p class="text-xs text-white/60 leading-relaxed">
                                    Interactive Mermaid.js diagrams showing system architecture and data flows
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-grow flex flex-col apple-card-main min-h-[600px] max-h-[calc(100vh-200px)]">
                    
                    <!-- Header -->
                    <div class="content-header">
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                            <div class="flex-grow min-w-0 space-y-2">
                                <div class="flex items-center gap-3">
                                    <h3 class="text-2xl sm:text-3xl font-bold text-white">
                                        {{ content[activeTab].title }}
                                    </h3>
                                    <Transition name="fade-scale">
                                        <div v-if="isLoading" class="flex items-center gap-2 text-blue-400">
                                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span class="text-xs font-semibold hidden sm:inline">Rendering...</span>
                                        </div>
                                    </Transition>
                                </div>
                                <p class="text-sm sm:text-base text-white/60 leading-relaxed">
                                    {{ content[activeTab].desc }}
                                </p>
                            </div>
                            
                            <div class="flex-shrink-0">
                                <div class="compliance-badge">
                                    <div class="text-xs uppercase tracking-wider text-blue-400 font-bold mb-1">Compliance</div>
                                    <div class="text-xs text-white/90 font-semibold">
                                        {{ content[activeTab].standard }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex-grow overflow-auto premium-scrollbar bg-black/40">
                        
                        <!-- Diagram -->
                        <Transition name="fade" mode="out-in">
                            <div v-if="content[activeTab].type === 'mermaid'" :key="activeTab" class="p-6 sm:p-8 lg:p-12">
                                <div class="diagram-container" :class="{'opacity-50 blur-sm': isLoading}">
                                    <div class="mermaid-container"></div>
                                </div>
                            </div>
                        </Transition>

                        <!-- Detail Cards -->
                        <div v-if="content[activeTab].details" class="p-6 sm:p-8 lg:p-12 pt-0">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                                <Transition
                                    v-for="(detail, index) in content[activeTab].details" 
                                    :key="index"
                                    name="slide-up"
                                    appear
                                >
                                    <div 
                                        class="detail-card group"
                                        :style="{ animationDelay: `${index * 0.1}s` }"
                                    >
                                        <div class="flex items-start gap-4 mb-4">
                                            <div class="detail-icon-wrap">
                                                <span class="text-3xl">{{ detail.icon }}</span>
                                            </div>
                                            <div class="flex-grow min-w-0">
                                                <h4 class="detail-title">{{ detail.title }}</h4>
                                            </div>
                                        </div>
                                        <p class="detail-content">{{ detail.content }}</p>
                                        
                                        <!-- Gradient overlay on hover -->
                                        <div class="detail-gradient"></div>
                                    </div>
                                </Transition>
                            </div>
                        </div>

                    </div>
                    
                    <!-- Footer -->
                    <div class="content-footer">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                            <p class="text-xs text-white/40 font-medium">
                                E-PILI Digital Governance Platform
                            </p>
                            <div class="flex items-center gap-4 text-xs text-white/40">
                                <span>{{ new Date().getFullYear() }}</span>
                                <span>•</span>
                                <span>Thesis Documentation</span>
                                <span>•</span>
                                <span>{{ new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }}</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
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

@keyframes pulse-slow {
    0%, 100% { opacity: 0.4; }
    50% { opacity: 1; }
}
.animate-pulse-slow {
    animation: pulse-slow 3s ease-in-out infinite;
}

/* ==================== TRANSITIONS ==================== */

.fade-scale-enter-active,
.fade-scale-leave-active {
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.fade-scale-enter-from,
.fade-scale-leave-to {
    opacity: 0;
    transform: scale(0.95);
}

.fade-enter-active {
    transition: opacity 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}
.fade-leave-active {
    transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.slide-up-enter-active {
    transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-up-enter-from {
    opacity: 0;
    transform: translateY(30px);
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

.apple-card-sidebar {
    @apply p-5 rounded-3xl;
    background: rgba(255, 255, 255, 0.02);
    backdrop-filter: blur(40px) saturate(180%);
    -webkit-backdrop-filter: blur(40px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.06);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

.apple-card-main {
    @apply overflow-hidden rounded-[2.5rem];
    background: rgba(255, 255, 255, 0.02);
    backdrop-filter: blur(60px) saturate(180%);
    -webkit-backdrop-filter: blur(60px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 
        0 20px 80px rgba(0, 0, 0, 0.5),
        inset 0 1px 0 rgba(255, 255, 255, 0.08);
}

.detail-card {
    @apply relative p-6 rounded-3xl transition-all duration-500 overflow-hidden;
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.06);
    box-shadow: 
        0 4px 24px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

.detail-card:hover {
    transform: translateY(-4px);
    border-color: rgba(59, 130, 246, 0.3);
    box-shadow: 
        0 12px 48px rgba(0, 0, 0, 0.4),
        0 0 0 1px rgba(59, 130, 246, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.detail-gradient {
    @apply absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-3xl;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(147, 51, 234, 0.05));
    pointer-events: none;
}

/* ==================== STATUS BADGES ==================== */

.status-badge {
    @apply inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs transition-all duration-300;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.3);
}

.status-live {
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.3);
    color: rgba(52, 211, 153, 1);
}

.status-tech {
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.3);
    color: rgba(96, 165, 250, 1);
}

.status-version {
    background: rgba(139, 92, 246, 0.1);
    border: 1px solid rgba(139, 92, 246, 0.3);
    color: rgba(167, 139, 250, 1);
}

/* ==================== SIDEBAR ==================== */

.sidebar-title {
    @apply text-xs uppercase tracking-wider text-white/40 font-bold mb-4 px-2;
}

.nav-tab-button {
    @apply relative w-full flex items-center gap-3 px-4 py-3.5 rounded-2xl text-left transition-all duration-300;
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.05);
    color: rgba(255, 255, 255, 0.6);
}

.nav-tab-button:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.9);
    transform: translateX(2px);
}

.nav-tab-active {
    background: rgba(59, 130, 246, 0.1);
    border-color: rgba(59, 130, 246, 0.3);
    color: rgba(255, 255, 255, 1);
    box-shadow: 
        0 4px 24px rgba(59, 130, 246, 0.2),
        inset 0 1px 0 rgba(59, 130, 246, 0.2);
}

.nav-tab-icon {
    @apply w-5 h-5 transition-transform duration-300 group-hover:scale-110 flex-shrink-0;
}

.nav-tab-label {
    @apply font-semibold text-sm block truncate;
}

.nav-tab-badge {
    @apply px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wider flex-shrink-0 transition-all duration-300;
    background: rgba(255, 255, 255, 0.05);
    color: rgba(255, 255, 255, 0.5);
}

.badge-active {
    background: rgba(59, 130, 246, 0.2);
    color: rgba(96, 165, 250, 1);
}

/* ==================== ACTIONS ==================== */

.action-button {
    @apply w-full flex items-center justify-center gap-3 py-3.5 px-4 rounded-2xl text-xs font-semibold uppercase tracking-wider transition-all duration-300;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
}

.action-primary {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(147, 51, 234, 0.2));
    border: 1px solid rgba(59, 130, 246, 0.3);
    color: rgba(147, 197, 253, 1);
    box-shadow: 0 4px 24px rgba(59, 130, 246, 0.2);
}

.action-primary:hover {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(147, 51, 234, 0.3));
    border-color: rgba(59, 130, 246, 0.4);
    transform: translateY(-2px);
    box-shadow: 0 8px 32px rgba(59, 130, 246, 0.3);
}

.action-secondary {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    color: rgba(255, 255, 255, 0.8);
}

.action-secondary:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
}

/* ==================== CONTENT ==================== */

.content-header {
    @apply p-6 sm:p-8 border-b border-white/10 flex-shrink-0;
    background: linear-gradient(to bottom, rgba(255, 255, 255, 0.03), transparent);
}

.content-footer {
    @apply p-4 sm:p-6 border-t border-white/10 flex-shrink-0;
    background: rgba(255, 255, 255, 0.02);
}

.compliance-badge {
    @apply px-4 py-3 rounded-2xl text-left;
    background: rgba(59, 130, 246, 0.1);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(59, 130, 246, 0.2);
}

/* ==================== DIAGRAM ==================== */

.diagram-container {
    @apply rounded-3xl p-8 transition-all duration-500;
    background: rgba(255, 255, 255, 0.02);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.06);
}

.mermaid-container {
    @apply flex justify-center items-center min-h-[400px];
}

.mermaid-container svg {
    max-width: 100%;
    height: auto;
}

/* ==================== DETAIL CARDS ==================== */

.detail-icon-wrap {
    @apply w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 transition-all duration-500;
    background: rgba(59, 130, 246, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(59, 130, 246, 0.2);
}

.detail-card:hover .detail-icon-wrap {
    transform: scale(1.1) rotate(5deg);
    background: rgba(59, 130, 246, 0.15);
    border-color: rgba(59, 130, 246, 0.3);
}

.detail-title {
    @apply text-base sm:text-lg font-bold text-white transition-colors duration-300 leading-tight;
}

.detail-card:hover .detail-title {
    color: rgba(96, 165, 250, 1);
}

.detail-content {
    @apply text-sm text-white/70 leading-relaxed;
}

/* ==================== SCROLLBAR ==================== */

.premium-scrollbar::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.premium-scrollbar::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.3);
    border-radius: 10px;
}

.premium-scrollbar::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, rgba(59, 130, 246, 0.5), rgba(139, 92, 246, 0.5));
    border-radius: 10px;
    border: 2px solid transparent;
    background-clip: padding-box;
}

.premium-scrollbar::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, rgba(59, 130, 246, 0.7), rgba(139, 92, 246, 0.7));
    background-clip: padding-box;
}

/* ==================== PRINT STYLES ==================== */

@media print {
    .fixed, button, nav, .sidebar-title, .action-button { 
        display: none !important; 
    }
    
    body { 
        background: white !important; 
        color: black !important; 
    }
    
    .apple-card-main,
    .apple-card-sidebar { 
        background: white !important; 
        border: 1px solid #ccc !important; 
        box-shadow: none !important; 
        page-break-inside: avoid;
    }
    
    .text-white { color: black !important; }
    .text-white\/60 { color: #555 !important; }
    .text-white\/40 { color: #999 !important; }
}

/* ==================== RESPONSIVE ==================== */

@media (max-width: 1024px) {
    .mermaid-container svg {
        font-size: 11px !important;
    }
}

@media (max-width: 768px) {
    .mermaid-container svg {
        font-size: 10px !important;
    }
    
    .apple-card-main {
        @apply rounded-3xl;
    }
    
    .detail-card {
        @apply p-5;
    }
}

@media (max-width: 640px) {
    .content-header,
    .content-footer {
        @apply p-4;
    }
    
    .diagram-container {
        @apply p-4;
    }
}
</style>