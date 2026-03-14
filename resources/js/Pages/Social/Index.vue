<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    programs:        { type: Array,  default: () => [] },
    myApplications:  { type: Array,  default: () => [] },
});

// ── View state ────────────────────────────────────────────────────
const activeTab     = ref('programs');   // 'programs' | 'myapps'
const selectedProgram = ref(null);
const showForm      = ref(false);

const openForm = (program) => {
    selectedProgram.value = program;
    form.reset();
    form.program_type  = program.type;
    form.program_title = program.title;
    form.application_data = {};
    showForm.value = true;
};
const closeForm = () => { showForm.value = false; selectedProgram.value = null; };

// ── Form ─────────────────────────────────────────────────────────
const form = useForm({
    program_type:     '',
    program_title:    '',
    application_data: {},
    attachments:      [],
});

const fieldValues = ref({});

const submit = () => {
    form.application_data = { ...fieldValues.value };
    form.post(route('social.apply'), {
        preserveScroll: true,
        onSuccess: () => { closeForm(); activeTab.value = 'myapps'; fieldValues.value = {}; },
    });
};

// ── Status display ────────────────────────────────────────────────
const statusCfg = {
    pending:      { label: 'Pending Review', badge: 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30', icon: '⏳' },
    under_review: { label: 'Under Review',   badge: 'bg-blue-500/20 text-blue-400 border-blue-500/30',       icon: '🔍' },
    approved:     { label: 'Approved ✅',    badge: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30', icon: '✅' },
    rejected:     { label: 'Not Approved',   badge: 'bg-red-500/20 text-red-400 border-red-500/30',           icon: '❌' },
    released:     { label: 'Released 🎉',    badge: 'bg-cyan-500/20 text-cyan-400 border-cyan-500/30',        icon: '🎉' },
};
const getStatus = s => statusCfg[s] ?? { label: s, badge: 'bg-slate-500/20 text-slate-400 border-slate-500/30', icon: '📋' };

const colorMap = {
    blue:   { ring: 'ring-blue-500', glow: 'from-blue-500/20 to-blue-600/5',   btn: 'bg-blue-600 hover:bg-blue-500',   accent: 'text-blue-400',   border: 'border-blue-500/30' },
    emerald:{ ring: 'ring-emerald-500', glow: 'from-emerald-500/20 to-emerald-600/5', btn: 'bg-emerald-600 hover:bg-emerald-500', accent: 'text-emerald-400', border: 'border-emerald-500/30' },
    purple: { ring: 'ring-purple-500', glow: 'from-purple-500/20 to-purple-600/5', btn: 'bg-purple-600 hover:bg-purple-500', accent: 'text-purple-400', border: 'border-purple-500/30' },
    rose:   { ring: 'ring-rose-500',  glow: 'from-rose-500/20 to-rose-600/5',   btn: 'bg-rose-600 hover:bg-rose-500',   accent: 'text-rose-400',   border: 'border-rose-500/30' },
    orange: { ring: 'ring-orange-500',glow: 'from-orange-500/20 to-orange-600/5',btn: 'bg-orange-600 hover:bg-orange-500',accent: 'text-orange-400',border: 'border-orange-500/30' },
};
const getColor = color => colorMap[color] ?? colorMap.blue;

const pendingCount  = computed(() => props.myApplications.filter(a => a.status === 'pending').length);
const approvedCount = computed(() => props.myApplications.filter(a => a.status === 'approved').length);
</script>

<template>
    <Head title="Social Aid Programs" />
    <AuthenticatedLayout>
        <div class="min-h-screen bg-slate-950 text-white font-sans">
            <!-- Background -->
            <div class="fixed inset-0 pointer-events-none z-0">
                <div class="absolute inset-0 bg-[#000]"></div>
                <div class="absolute top-[-20%] left-[-10%] w-[80vw] h-[80vw] bg-rose-600/15 rounded-full blur-[180px]"></div>
                <div class="absolute bottom-[-20%] right-[-10%] w-[80vw] h-[80vw] bg-blue-600/15 rounded-full blur-[180px]"></div>
            </div>

            <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 py-10 space-y-8">

                <!-- ── Hero Header ──────────────────────────────── -->
                <div class="text-center pt-4">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-bold uppercase tracking-widest mb-4">
                        🤝 MSWDO — Social Welfare Programs
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold text-white tracking-tight mb-3">Social Aid Programs</h1>
                    <p class="text-slate-400 text-lg max-w-xl mx-auto">Apply for government assistance programs. Your application will be reviewed and you will be notified of the result.</p>
                </div>

                <!-- ── My Stats (if has apps) ─────────────────── -->
                <div v-if="myApplications.length" class="grid grid-cols-3 gap-4">
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

                <!-- ── Tabs ──────────────────────────────────────── -->
                <div class="flex gap-2 bg-slate-900/60 backdrop-blur border border-slate-700/50 rounded-2xl p-1.5 w-fit">
                    <button @click="activeTab = 'programs'" :class="['px-5 py-2.5 rounded-xl text-sm font-bold transition-all', activeTab === 'programs' ? 'bg-white text-black shadow-lg' : 'text-slate-400 hover:text-white']">
                        🏛 Programs
                    </button>
                    <button @click="activeTab = 'myapps'" :class="['px-5 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2', activeTab === 'myapps' ? 'bg-white text-black shadow-lg' : 'text-slate-400 hover:text-white']">
                        📋 My Applications
                        <span v-if="pendingCount" class="bg-yellow-500 text-black text-[10px] font-black px-1.5 py-0.5 rounded-full">{{ pendingCount }}</span>
                    </button>
                </div>

                <!-- ── Programs Grid ─────────────────────────────── -->
                <div v-if="activeTab === 'programs'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="program in programs" :key="program.id"
                        class="group relative bg-slate-900/60 backdrop-blur border border-white/8 rounded-[2rem] p-7 hover:-translate-y-1 hover:shadow-2xl hover:shadow-black/40 transition-all duration-300 overflow-hidden cursor-pointer"
                        :class="program.status !== 'Open' ? 'opacity-60 pointer-events-none' : ''"
                    >
                        <!-- Glow -->
                        <div class="absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none rounded-[2rem]"
                            :class="getColor(program.color).glow"></div>

                        <!-- Status Badge -->
                        <div class="absolute top-5 right-5">
                            <span :class="['text-[10px] font-bold px-2.5 py-1 rounded-full border',
                                program.status === 'Open' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-red-500/20 text-red-400 border-red-500/30']">
                                {{ program.status }}
                            </span>
                        </div>

                        <!-- Icon -->
                        <div class="text-4xl mb-5">{{ program.icon }}</div>

                        <!-- Text -->
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold mb-1">{{ program.type }}</p>
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-rose-300 transition-colors leading-tight">{{ program.title }}</h3>
                        <p class="text-sm text-slate-400 leading-relaxed mb-4">{{ program.description }}</p>

                        <!-- Amount -->
                        <div class="flex items-center justify-between border-t border-white/5 pt-4 mt-auto">
                            <div>
                                <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Benefit</p>
                                <p class="text-lg font-bold font-mono text-white">{{ program.amount }}</p>
                            </div>
                            <button @click="openForm(program)"
                                :class="['px-5 py-2.5 rounded-xl text-white text-sm font-bold transition-all transform hover:scale-105 active:scale-95 shadow-lg', getColor(program.color).btn]">
                                Apply Now →
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ── My Applications ───────────────────────────── -->
                <div v-else>
                    <div v-if="!myApplications.length" class="text-center py-20 bg-slate-900/40 backdrop-blur border border-slate-800/50 rounded-2xl">
                        <div class="text-6xl mb-4 opacity-20">📋</div>
                        <p class="text-slate-400 font-semibold">No applications yet</p>
                        <p class="text-slate-600 text-sm mt-1">Browse our programs and submit your first application.</p>
                        <button @click="activeTab = 'programs'" class="mt-4 px-5 py-2.5 bg-rose-600 hover:bg-rose-500 rounded-xl text-white text-sm font-bold transition-colors">Browse Programs →</button>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="app in myApplications" :key="app.id"
                            class="bg-slate-900/60 backdrop-blur border border-slate-700/50 rounded-2xl p-6 hover:border-slate-600/50 transition-colors">
                            <div class="flex flex-col sm:flex-row items-start justify-between gap-4">
                                <div class="flex items-start gap-4 flex-1">
                                    <div class="text-3xl">{{ { 'Student Assistance':'🎓','Senior Citizen':'👴','PWD Assistance':'♿','Solo Parent':'👩‍👧','Calamity Aid':'🆘' }[app.program_type] ?? '🤝' }}</div>
                                    <div>
                                        <p class="text-xs text-slate-500 uppercase font-bold tracking-widest">{{ app.program_type }}</p>
                                        <h3 class="text-lg font-bold text-white">{{ app.program_title }}</h3>
                                        <p class="text-xs font-mono text-slate-500 mt-1">{{ app.tracking_code }} · {{ app.created_at }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                                    <span :class="['inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-bold', getStatus(app.status).badge]">
                                        {{ getStatus(app.status).icon }} {{ getStatus(app.status).label }}
                                    </span>
                                </div>
                            </div>

                            <!-- Approved details -->
                            <div v-if="app.status === 'approved' || app.status === 'released'"
                                class="mt-4 pt-4 border-t border-emerald-500/20 bg-emerald-950/20 rounded-xl p-4">
                                <p class="text-emerald-400 text-xs font-bold uppercase tracking-widest mb-3">✅ Application Approved — Please proceed to claim</p>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <p class="text-[10px] text-slate-500 uppercase font-bold">Amount</p>
                                        <p class="text-xl font-bold text-emerald-400">₱{{ Number(app.approved_amount).toLocaleString('en-PH') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-500 uppercase font-bold">Release Date</p>
                                        <p class="text-sm text-white font-semibold">📅 {{ app.release_date }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-500 uppercase font-bold">Venue</p>
                                        <p class="text-sm text-white">📍 {{ app.release_venue }}</p>
                                    </div>
                                </div>
                                <p class="text-xs text-emerald-300/70 mt-3">⚠ Please bring a valid ID and this tracking number when you claim your assistance.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ════════ APPLICATION FORM MODAL ════════ -->
        <Transition enter-active-class="transition duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
            leave-active-class="transition duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showForm && selectedProgram" class="fixed inset-0 z-50 flex items-start justify-center bg-black/80 backdrop-blur-md overflow-y-auto p-4 pt-8">
                <div class="w-full max-w-2xl bg-slate-900 border border-white/10 rounded-3xl shadow-2xl mb-8">

                    <!-- Modal Header -->
                    <div class="flex items-start justify-between p-6 pb-5 border-b border-white/5">
                        <div>
                            <div class="text-3xl mb-2">{{ selectedProgram.icon }}</div>
                            <h2 class="text-xl font-bold text-white">{{ selectedProgram.title }}</h2>
                            <p class="text-sm text-slate-400 mt-1">Fill out the form completely. All fields are required.</p>
                        </div>
                        <button @click="closeForm" class="w-9 h-9 rounded-full bg-white/5 hover:bg-white/10 border border-white/10 flex items-center justify-center text-slate-400 hover:text-white transition-all text-lg">✕</button>
                    </div>

                    <!-- Requirements notice -->
                    <div class="mx-6 mt-5 p-4 bg-amber-950/30 border border-amber-500/20 rounded-xl">
                        <p class="text-amber-400 text-xs font-bold uppercase tracking-widest mb-2">📎 Required Documents</p>
                        <ul class="space-y-1">
                            <li v-for="req in selectedProgram.requirements" :key="req" class="text-xs text-amber-200/70 flex items-start gap-2">
                                <span class="text-amber-500 mt-0.5">•</span> {{ req }}
                            </li>
                        </ul>
                    </div>

                    <!-- Form Fields -->
                    <form @submit.prevent="submit" class="p-6 space-y-5">
                        <div v-for="field in selectedProgram.fields" :key="field.name">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                                {{ field.label }} <span class="text-red-400">*</span>
                            </label>

                            <!-- Text/Number/Date -->
                            <input v-if="['text','number','date'].includes(field.type)"
                                :type="field.type"
                                v-model="fieldValues[field.name]"
                                required
                                class="w-full bg-black/40 border border-white/15 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/50 focus:border-rose-500/50 transition-all placeholder-slate-600"
                                :placeholder="field.label"
                            />

                            <!-- Textarea -->
                            <textarea v-else-if="field.type === 'textarea'"
                                v-model="fieldValues[field.name]"
                                required rows="3"
                                class="w-full bg-black/40 border border-white/15 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/50 focus:border-rose-500/50 transition-all resize-none placeholder-slate-600"
                                :placeholder="field.label"
                            ></textarea>

                            <!-- Select -->
                            <select v-else-if="field.type === 'select'"
                                v-model="fieldValues[field.name]"
                                required
                                class="w-full bg-black/40 border border-white/15 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/50 focus:border-rose-500/50 transition-all">
                                <option value="" disabled selected>Select {{ field.label }}</option>
                                <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
                            </select>
                        </div>

                        <!-- Disclaimer -->
                        <div class="p-4 bg-slate-800/50 rounded-xl border border-slate-700/50">
                            <p class="text-xs text-slate-400 leading-relaxed">
                                By submitting this application, I certify that all information provided is true and accurate.
                                I understand that providing false information may result in disqualification.
                            </p>
                        </div>

                        <!-- Errors -->
                        <div v-if="Object.keys(form.errors).length" class="p-3 bg-red-950/40 border border-red-500/20 rounded-xl">
                            <p v-for="(error, key) in form.errors" :key="key" class="text-red-400 text-xs">{{ error }}</p>
                        </div>

                        <!-- Submit -->
                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="closeForm" class="flex-1 py-3 bg-slate-800 hover:bg-slate-700 border border-slate-600 rounded-xl text-slate-300 font-bold transition-colors">Cancel</button>
                            <button type="submit" :disabled="form.processing"
                                :class="['flex-1 py-3 rounded-xl text-white font-bold transition-all disabled:opacity-50', getColor(selectedProgram.color).btn]">
                                {{ form.processing ? 'Submitting...' : '✅ Submit Application' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>