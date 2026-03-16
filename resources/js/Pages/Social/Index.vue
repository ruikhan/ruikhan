<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    programs:       { type: Array, default: () => [] },
    myApplications: { type: Array, default: () => [] },
});

const activeTab       = ref('programs');
const selectedProgram = ref(null);
const showForm        = ref(false);

const openForm = (program) => {
    selectedProgram.value = program;
    form.reset();
    form.program_type  = program.type;
    form.program_title = program.title;
    form.application_data = {};
    showForm.value = true;
};
const closeForm = () => { showForm.value = false; selectedProgram.value = null; };

const form = useForm({
    program_type: '', program_title: '', application_data: {}, attachments: [],
});
const fieldValues = ref({});

const submit = () => {
    form.application_data = { ...fieldValues.value };
    form.post(route('social.store'), {
        preserveScroll: true,
        onSuccess: () => { closeForm(); activeTab.value = 'myapps'; fieldValues.value = {}; },
    });
};

const statusCfg = {
    pending:      { label: 'Pending Review', badge: 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',   icon: '⏳' },
    under_review: { label: 'Under Review',   badge: 'bg-blue-500/20 text-blue-400 border-blue-500/30',         icon: '🔍' },
    approved:     { label: 'Approved',       badge: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',icon: '✅' },
    rejected:     { label: 'Not Approved',   badge: 'bg-red-500/20 text-red-400 border-red-500/30',             icon: '❌' },
    released:     { label: 'Released',       badge: 'bg-cyan-500/20 text-cyan-400 border-cyan-500/30',          icon: '🎉' },
};
const getStatus = s => statusCfg[s] ?? { label: s, badge: 'bg-slate-500/20 text-slate-400 border-slate-500/30', icon: '📋' };

const colorMap = {
    blue:    { btn: 'bg-blue-600 hover:bg-blue-500',       accent: 'text-blue-400',    ring: 'ring-blue-500',    glow: 'from-blue-500/20 to-blue-600/5',    border: 'border-blue-500/30' },
    emerald: { btn: 'bg-emerald-600 hover:bg-emerald-500', accent: 'text-emerald-400', ring: 'ring-emerald-500', glow: 'from-emerald-500/20 to-emerald-600/5',border: 'border-emerald-500/30' },
    purple:  { btn: 'bg-purple-600 hover:bg-purple-500',   accent: 'text-purple-400',  ring: 'ring-purple-500',  glow: 'from-purple-500/20 to-purple-600/5', border: 'border-purple-500/30' },
    rose:    { btn: 'bg-rose-600 hover:bg-rose-500',       accent: 'text-rose-400',    ring: 'ring-rose-500',    glow: 'from-rose-500/20 to-rose-600/5',    border: 'border-rose-500/30' },
    orange:  { btn: 'bg-orange-600 hover:bg-orange-500',   accent: 'text-orange-400',  ring: 'ring-orange-500',  glow: 'from-orange-500/20 to-orange-600/5', border: 'border-orange-500/30' },
};
const getColor = color => colorMap[color] ?? colorMap.blue;

const programIcons = { 'Student Assistance':'🎓', 'Senior Citizen':'👴', 'PWD Assistance':'♿', 'Solo Parent':'👩‍👧', 'Calamity Aid':'🆘' };
const getProgramIcon = type => programIcons[type] ?? '🤝';

const pendingCount  = computed(() => props.myApplications.filter(a => a.status === 'pending').length);
const approvedCount = computed(() => props.myApplications.filter(a => a.status === 'approved').length);
</script>

<template>
    <Head title="Social Aid Programs" />
    <AuthenticatedLayout>

        <!-- Background -->
        <div class="fixed inset-0 pointer-events-none z-0">
            <div class="absolute inset-0 bg-black"></div>
            <div class="absolute top-[-20%] left-[-10%] w-[80vw] h-[80vw] bg-rose-600/15 rounded-full blur-[180px]"></div>
            <div class="absolute bottom-[-20%] right-[-10%] w-[80vw] h-[80vw] bg-blue-600/15 rounded-full blur-[180px]"></div>
        </div>

        <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 py-6 space-y-5">

            <!-- ══════════════════════════════════════
                 MOBILE HEADER — banking-app style
            ══════════════════════════════════════ -->
            <div class="block lg:hidden space-y-4">

                <!-- Title row -->
                <div>
                    <p class="text-xs font-700 text-white/40 uppercase tracking-widest mb-1">MSWDO</p>
                    <h1 class="text-2xl font-900 text-white tracking-tight leading-tight">
                        Social Aid
                        <span class="ml-1">🤝</span>
                    </h1>
                    <p class="text-sm text-white/45 mt-1 leading-snug">Apply for government assistance programs</p>
                </div>

                <!-- Stats strip — matches hero card stat row -->
                <div v-if="myApplications.length" class="s-stats-strip">
                    <div class="s-stat-item">
                        <span class="s-stat-val">{{ myApplications.length }}</span>
                        <span class="s-stat-lbl">Applied</span>
                    </div>
                    <div class="s-stat-sep"></div>
                    <div class="s-stat-item">
                        <span class="s-stat-val text-yellow-400">{{ pendingCount }}</span>
                        <span class="s-stat-lbl">Pending</span>
                    </div>
                    <div class="s-stat-sep"></div>
                    <div class="s-stat-item">
                        <span class="s-stat-val text-emerald-400">{{ approvedCount }}</span>
                        <span class="s-stat-lbl">Approved</span>
                    </div>
                </div>

                <!-- Tab switcher — pill style like banking app -->
                <div class="s-tab-bar">
                    <button @click="activeTab='programs'"
                        :class="['s-tab', activeTab==='programs' ? 's-tab-active' : '']">
                        🏛 Programs
                    </button>
                    <button @click="activeTab='myapps'"
                        :class="['s-tab', activeTab==='myapps' ? 's-tab-active' : '']">
                        📋 My Apps
                        <span v-if="pendingCount" class="s-tab-badge">{{ pendingCount }}</span>
                    </button>
                </div>
            </div>

            <!-- ══════════════════════════════════════
                 DESKTOP HEADER (unchanged)
            ══════════════════════════════════════ -->
            <div class="hidden lg:block text-center pt-4">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-bold uppercase tracking-widest mb-4">
                    🤝 MSWDO — Social Welfare Programs
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-white tracking-tight mb-3">Social Aid Programs</h1>
                <p class="text-slate-400 text-lg max-w-xl mx-auto">Apply for government assistance programs. Your application will be reviewed and you will be notified of the result.</p>
            </div>

            <!-- Desktop stats -->
            <div v-if="myApplications.length" class="hidden lg:grid grid-cols-3 gap-4">
                <div class="bg-slate-900/60 backdrop-blur border border-slate-700/50 rounded-2xl p-4 text-center">
                    <p class="text-3xl font-bold text-white">{{ myApplications.length }}</p>
                    <p class="text-xs text-slate-500 mt-1 uppercase font-bold tracking-widest">Total Applied</p>
                </div>
                <div class="bg-yellow-950/30 backdrop-blur border border-yellow-500/20 rounded-2xl p-4 text-center">
                    <p class="text-3xl font-bold text-yellow-400">{{ pendingCount }}</p>
                    <p class="text-xs text-yellow-600 mt-1 uppercase font-bold tracking-widest">Pending</p>
                </div>
                <div class="bg-emerald-950/30 backdrop-blur border border-emerald-500/20 rounded-2xl p-4 text-center">
                    <p class="text-3xl font-bold text-emerald-400">{{ approvedCount }}</p>
                    <p class="text-xs text-emerald-600 mt-1 uppercase font-bold tracking-widest">Approved</p>
                </div>
            </div>

            <!-- Desktop tabs -->
            <div class="hidden lg:flex gap-2 bg-slate-900/60 backdrop-blur border border-slate-700/50 rounded-2xl p-1.5 w-fit">
                <button @click="activeTab='programs'" :class="['px-5 py-2.5 rounded-xl text-sm font-bold transition-all', activeTab==='programs' ? 'bg-white text-black shadow-lg' : 'text-slate-400 hover:text-white']">
                    🏛 Programs
                </button>
                <button @click="activeTab='myapps'" :class="['px-5 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2', activeTab==='myapps' ? 'bg-white text-black shadow-lg' : 'text-slate-400 hover:text-white']">
                    📋 My Applications
                    <span v-if="pendingCount" class="bg-yellow-500 text-black text-[10px] font-black px-1.5 py-0.5 rounded-full">{{ pendingCount }}</span>
                </button>
            </div>

            <!-- ══════════════════════════════════════
                 PROGRAMS TAB
            ══════════════════════════════════════ -->
            <div v-if="activeTab === 'programs'">

                <!-- Mobile: stacked cards like banking app menu items -->
                <div class="flex flex-col gap-4 lg:hidden">
                    <div v-for="program in programs" :key="program.id"
                        class="s-program-card"
                        :class="program.status !== 'Open' ? 'opacity-50 pointer-events-none' : ''"
                        @click="program.status === 'Open' && openForm(program)">

                        <div class="s-program-card-shine"></div>

                        <!-- Top row: icon + title + status badge -->
                        <div class="flex items-start gap-3 mb-3">
                            <div :class="`s-program-icon-wrap ${getColor(program.color).ring}`">
                                <span class="text-2xl">{{ program.icon }}</span>
                                <div class="s-program-icon-glow" :class="`bg-gradient-to-br ${getColor(program.color).glow}`"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 mb-0.5">
                                    <p class="text-[10px] text-white/40 uppercase tracking-widest font-700">{{ program.type }}</p>
                                    <span :class="['s-open-badge', program.status==='Open' ? 'text-emerald-400 bg-emerald-500/15 border-emerald-500/30' : 'text-red-400 bg-red-500/15 border-red-500/30']">
                                        {{ program.status }}
                                    </span>
                                </div>
                                <h3 class="text-base font-800 text-white leading-tight">{{ program.title }}</h3>
                            </div>
                        </div>

                        <!-- Description -->
                        <p class="text-sm text-white/50 leading-relaxed mb-3 line-clamp-2">{{ program.description }}</p>

                        <!-- Bottom row: benefit amount + apply button -->
                        <div class="flex items-center justify-between pt-3 border-t border-white/8">
                            <div>
                                <p class="text-[10px] text-white/35 uppercase font-700 tracking-wider mb-0.5">Benefit</p>
                                <p class="text-lg font-800 text-white font-mono leading-none">{{ program.amount }}</p>
                            </div>
                            <button @click.stop="openForm(program)"
                                :class="['s-apply-btn', getColor(program.color).btn]">
                                Apply →
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Desktop: original grid -->
                <div class="hidden lg:grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="program in programs" :key="program.id"
                        class="group relative bg-slate-900/60 backdrop-blur border border-white/8 rounded-[2rem] p-7 hover:-translate-y-1 hover:shadow-2xl transition-all duration-300 overflow-hidden cursor-pointer"
                        :class="program.status !== 'Open' ? 'opacity-60 pointer-events-none' : ''"
                        @click="openForm(program)">
                        <div class="absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none rounded-[2rem]" :class="getColor(program.color).glow"></div>
                        <div class="absolute top-5 right-5">
                            <span :class="['text-[10px] font-bold px-2.5 py-1 rounded-full border', program.status==='Open' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-red-500/20 text-red-400 border-red-500/30']">{{ program.status }}</span>
                        </div>
                        <div class="text-4xl mb-5">{{ program.icon }}</div>
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold mb-1">{{ program.type }}</p>
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-rose-300 transition-colors leading-tight">{{ program.title }}</h3>
                        <p class="text-sm text-slate-400 leading-relaxed mb-4">{{ program.description }}</p>
                        <div class="flex items-center justify-between border-t border-white/5 pt-4 mt-auto">
                            <div>
                                <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Benefit</p>
                                <p class="text-lg font-bold font-mono text-white">{{ program.amount }}</p>
                            </div>
                            <button @click.stop="openForm(program)" :class="['px-5 py-2.5 rounded-xl text-white text-sm font-bold transition-all hover:scale-105 active:scale-95 shadow-lg', getColor(program.color).btn]">Apply Now →</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════
                 MY APPLICATIONS TAB
            ══════════════════════════════════════ -->
            <div v-else>
                <!-- Empty -->
                <div v-if="!myApplications.length" class="s-empty-state">
                    <span class="text-5xl opacity-20 mb-3 block">📋</span>
                    <p class="text-white/50 font-700 mb-1">No applications yet</p>
                    <p class="text-white/30 text-sm mb-4">Browse programs and submit your first application.</p>
                    <button @click="activeTab='programs'" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 rounded-xl text-white text-sm font-bold transition-colors">
                        Browse Programs →
                    </button>
                </div>

                <!-- Mobile: application cards -->
                <div v-else class="flex flex-col gap-4 lg:hidden">
                    <div v-for="app in myApplications" :key="app.id" class="s-app-card">

                        <!-- Header row -->
                        <div class="flex items-start gap-3 mb-3">
                            <div class="s-app-icon-wrap">
                                <span class="text-2xl">{{ getProgramIcon(app.program_type) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] text-white/35 uppercase tracking-widest font-700 mb-0.5">{{ app.program_type }}</p>
                                <h3 class="text-sm font-800 text-white leading-snug">{{ app.program_title }}</h3>
                                <p class="text-[10px] font-mono text-white/30 mt-0.5">{{ app.tracking_code }}</p>
                            </div>
                            <span :class="['s-status-badge', getStatus(app.status).badge]">
                                {{ getStatus(app.status).icon }}
                                <span class="ml-1">{{ getStatus(app.status).label }}</span>
                            </span>
                        </div>

                        <!-- Date -->
                        <p class="text-[10px] text-white/30 font-600 mb-3">Submitted: {{ app.created_at }}</p>

                        <!-- Approved details -->
                        <div v-if="app.status==='approved' || app.status==='released'"
                            class="s-approved-panel">
                            <p class="text-[10px] text-emerald-400 font-800 uppercase tracking-widest mb-2">✅ Approved — Proceed to claim</p>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <p class="text-[9px] text-white/35 uppercase font-700 mb-0.5">Amount</p>
                                    <p class="text-base font-800 text-emerald-400">₱{{ Number(app.approved_amount).toLocaleString('en-PH') }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] text-white/35 uppercase font-700 mb-0.5">Date</p>
                                    <p class="text-xs text-white font-600">{{ app.release_date }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] text-white/35 uppercase font-700 mb-0.5">Venue</p>
                                    <p class="text-xs text-white font-600 line-clamp-2">{{ app.release_venue }}</p>
                                </div>
                            </div>
                            <p class="text-[10px] text-emerald-300/60 mt-2">⚠ Bring valid ID and tracking number when claiming.</p>
                        </div>
                    </div>
                </div>

                <!-- Desktop: original list -->
                <div v-if="myApplications.length" class="hidden lg:block space-y-4">
                    <div v-for="app in myApplications" :key="app.id"
                        class="bg-slate-900/60 backdrop-blur border border-slate-700/50 rounded-2xl p-6 hover:border-slate-600/50 transition-colors">
                        <div class="flex flex-col sm:flex-row items-start justify-between gap-4">
                            <div class="flex items-start gap-4 flex-1">
                                <div class="text-3xl">{{ getProgramIcon(app.program_type) }}</div>
                                <div>
                                    <p class="text-xs text-slate-500 uppercase font-bold tracking-widest">{{ app.program_type }}</p>
                                    <h3 class="text-lg font-bold text-white">{{ app.program_title }}</h3>
                                    <p class="text-xs font-mono text-slate-500 mt-1">{{ app.tracking_code }} · {{ app.created_at }}</p>
                                </div>
                            </div>
                            <span :class="['inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-bold', getStatus(app.status).badge]">
                                {{ getStatus(app.status).icon }} {{ getStatus(app.status).label }}
                            </span>
                        </div>
                        <div v-if="app.status==='approved' || app.status==='released'" class="mt-4 pt-4 border-t border-emerald-500/20 bg-emerald-950/20 rounded-xl p-4">
                            <p class="text-emerald-400 text-xs font-bold uppercase tracking-widest mb-3">✅ Application Approved — Please proceed to claim</p>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div><p class="text-[10px] text-slate-500 uppercase font-bold">Amount</p><p class="text-xl font-bold text-emerald-400">₱{{ Number(app.approved_amount).toLocaleString('en-PH') }}</p></div>
                                <div><p class="text-[10px] text-slate-500 uppercase font-bold">Release Date</p><p class="text-sm text-white font-semibold">📅 {{ app.release_date }}</p></div>
                                <div><p class="text-[10px] text-slate-500 uppercase font-bold">Venue</p><p class="text-sm text-white">📍 {{ app.release_venue }}</p></div>
                            </div>
                            <p class="text-xs text-emerald-300/70 mt-3">⚠ Please bring a valid ID and this tracking number when you claim your assistance.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ════════ APPLICATION FORM MODAL (unchanged) ════════ -->
        <Transition enter-active-class="transition duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
            leave-active-class="transition duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showForm && selectedProgram" class="fixed inset-0 z-50 flex items-start justify-center bg-black/80 backdrop-blur-md overflow-y-auto p-4 pt-8">
                <div class="w-full max-w-2xl bg-slate-900 border border-white/10 rounded-3xl shadow-2xl mb-8">
                    <div class="flex items-start justify-between p-6 pb-5 border-b border-white/5">
                        <div>
                            <div class="text-3xl mb-2">{{ selectedProgram.icon }}</div>
                            <h2 class="text-xl font-bold text-white">{{ selectedProgram.title }}</h2>
                            <p class="text-sm text-slate-400 mt-1">Fill out the form completely. All fields are required.</p>
                        </div>
                        <button @click="closeForm" class="w-9 h-9 rounded-full bg-white/5 hover:bg-white/10 border border-white/10 flex items-center justify-center text-slate-400 hover:text-white transition-all text-lg">✕</button>
                    </div>
                    <div class="mx-6 mt-5 p-4 bg-amber-950/30 border border-amber-500/20 rounded-xl">
                        <p class="text-amber-400 text-xs font-bold uppercase tracking-widest mb-2">📎 Required Documents</p>
                        <ul class="space-y-1">
                            <li v-for="req in selectedProgram.requirements" :key="req" class="text-xs text-amber-200/70 flex items-start gap-2">
                                <span class="text-amber-500 mt-0.5">•</span> {{ req }}
                            </li>
                        </ul>
                    </div>
                    <form @submit.prevent="submit" class="p-6 space-y-5">
                        <div v-for="field in selectedProgram.fields" :key="field.name">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                                {{ field.label }} <span class="text-red-400">*</span>
                            </label>
                            <input v-if="['text','number','date'].includes(field.type)" :type="field.type" v-model="fieldValues[field.name]" required
                                class="w-full bg-black/40 border border-white/15 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/50 transition-all placeholder-slate-600"
                                :placeholder="field.label"/>
                            <textarea v-else-if="field.type==='textarea'" v-model="fieldValues[field.name]" required rows="3"
                                class="w-full bg-black/40 border border-white/15 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/50 transition-all resize-none placeholder-slate-600"
                                :placeholder="field.label"></textarea>
                            <select v-else-if="field.type==='select'" v-model="fieldValues[field.name]" required
                                class="w-full bg-black/40 border border-white/15 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/50 transition-all">
                                <option value="" disabled selected>Select {{ field.label }}</option>
                                <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
                            </select>
                        </div>
                        <div class="p-4 bg-slate-800/50 rounded-xl border border-slate-700/50">
                            <p class="text-xs text-slate-400 leading-relaxed">By submitting this application, I certify that all information provided is true and accurate. I understand that providing false information may result in disqualification.</p>
                        </div>
                        <div v-if="Object.keys(form.errors).length" class="p-3 bg-red-950/40 border border-red-500/20 rounded-xl">
                            <p v-for="(error, key) in form.errors" :key="key" class="text-red-400 text-xs">{{ error }}</p>
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="closeForm" class="flex-1 py-3 bg-slate-800 hover:bg-slate-700 border border-slate-600 rounded-xl text-slate-300 font-bold transition-colors">Cancel</button>
                            <button type="submit" :disabled="form.processing" :class="['flex-1 py-3 rounded-xl text-white font-bold transition-all disabled:opacity-50', getColor(selectedProgram.color).btn]">
                                {{ form.processing ? 'Submitting...' : '✅ Submit Application' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>

    </AuthenticatedLayout>
</template>

<style scoped>
/* ── Mobile stats strip ── */
.s-stats-strip {
    display: flex;
    align-items: center;
    padding: 1rem 1.25rem;
    border-radius: 1.5rem;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.09);
    backdrop-filter: blur(20px);
}
.s-stat-item  { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 0.25rem; }
.s-stat-val   { font-size: 1.5rem; font-weight: 800; color: white; line-height: 1; letter-spacing: -0.02em; }
.s-stat-lbl   { font-size: 0.5625rem; font-weight: 700; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 0.06em; }
.s-stat-sep   { width: 1px; height: 2rem; background: rgba(255,255,255,0.12); flex-shrink: 0; }

/* ── Tab bar ── */
.s-tab-bar {
    display: flex; gap: 0.5rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.09);
    border-radius: 1.25rem;
    padding: 0.3rem;
    width: fit-content;
}
.s-tab {
    display: flex; align-items: center; gap: 0.375rem;
    padding: 0.625rem 1.125rem;
    border-radius: 1rem;
    font-size: 0.8125rem; font-weight: 700;
    color: rgba(255,255,255,0.45);
    transition: all 0.2s;
    border: none; background: none;
}
.s-tab:active { transform: scale(0.96); }
.s-tab-active { background: white; color: black; box-shadow: 0 4px 16px rgba(0,0,0,0.3); }
.s-tab-badge {
    min-width: 1.25rem; height: 1.25rem; padding: 0 0.25rem;
    border-radius: 9999px; font-size: 0.5625rem; font-weight: 900;
    background: #eab308; color: black;
    display: flex; align-items: center; justify-content: center;
}

/* ── Program card (mobile) ── */
.s-program-card {
    position: relative;
    padding: 1.125rem;
    border-radius: 1.5rem;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.09);
    backdrop-filter: blur(20px);
    overflow: hidden;
    cursor: pointer;
    transition: background 0.2s, border-color 0.2s, transform 0.15s;
}
.s-program-card:active { transform: scale(0.97); }
.s-program-card:hover  { background: rgba(255,255,255,0.09); border-color: rgba(255,255,255,0.14); }

.s-program-card-shine {
    position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.04), transparent);
    pointer-events: none;
}

.s-program-icon-wrap {
    position: relative;
    width: 3.25rem; height: 3.25rem; flex-shrink: 0;
    border-radius: 1rem;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.12);
    display: flex; align-items: center; justify-content: center;
    ring-width: 2px;
}
.s-program-icon-glow {
    position: absolute; inset: -3px; border-radius: 1rem;
    filter: blur(10px); opacity: 0.5; z-index: -1;
}

.s-open-badge {
    flex-shrink: 0;
    font-size: 0.5625rem; font-weight: 800;
    padding: 0.2rem 0.5rem; border-radius: 9999px;
    border-width: 1px; border-style: solid;
    text-transform: uppercase; letter-spacing: 0.04em;
}

.s-apply-btn {
    padding: 0.625rem 1.25rem;
    border-radius: 0.875rem;
    font-size: 0.8125rem; font-weight: 800;
    color: white;
    transition: all 0.2s;
    transform-origin: center;
    border: none;
}
.s-apply-btn:active { transform: scale(0.94); }

/* ── Application card (mobile) ── */
.s-app-card {
    padding: 1rem;
    border-radius: 1.5rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.08);
    backdrop-filter: blur(20px);
}

.s-app-icon-wrap {
    width: 3rem; height: 3rem; flex-shrink: 0;
    border-radius: 0.875rem;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.1);
    display: flex; align-items: center; justify-content: center;
}

.s-status-badge {
    display: inline-flex; align-items: center; flex-shrink: 0;
    padding: 0.3rem 0.625rem;
    border-radius: 0.75rem;
    font-size: 0.625rem; font-weight: 800;
    border-width: 1px; border-style: solid;
    white-space: nowrap;
}

.s-approved-panel {
    margin-top: 0.75rem;
    padding: 0.875rem;
    border-radius: 1rem;
    background: rgba(5,46,22,0.4);
    border: 1px solid rgba(16,185,129,0.2);
}

/* ── Empty state ── */
.s-empty-state {
    text-align: center;
    padding: 3rem 1.5rem;
    border-radius: 1.5rem;
    background: rgba(255,255,255,0.04);
    border: 1px dashed rgba(255,255,255,0.1);
}

@media (prefers-reduced-motion: reduce) { *,*::before,*::after { animation-duration:0.01ms !important; transition-duration:0.01ms !important; } }
button:focus-visible,input:focus-visible,select:focus-visible,textarea:focus-visible { outline:2px solid rgb(59,130,246); outline-offset:2px; }
</style>