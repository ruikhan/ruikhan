<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({ polls: Array });

// --- CREATE POLL FORM LOGIC ---
const createForm = useForm({
    title: '',
    description: '',
    options: ['', ''] 
});

const addOptionField = () => {
    createForm.options.push('');
};

const removeOptionField = (index) => {
    if (createForm.options.length > 2) {
        createForm.options.splice(index, 1);
    }
};

const submitPoll = () => {
    createForm.post(route('polls.store'), {
        onSuccess: () => {
            createForm.reset();
            createForm.options = ['', '']; 
        }
    });
};

// --- VOTING LOGIC ---
const voteForm = useForm({
    poll_id: null,
    option_id: null
});

const castVote = (pollId, optionId) => {
    voteForm.poll_id = pollId;
    voteForm.option_id = optionId;
    voteForm.post(route('polls.vote'), {
        preserveScroll: true
    });
};

// Helper: Calculate Percentage
const getPercentage = (votes, total) => {
    if (total === 0) return 0;
    return Math.round((votes / total) * 100);
};
</script>

<template>
    <Head title="Voice - E-Voting" />

    <AuthenticatedLayout>
        <div class="fixed inset-0 z-0 pointer-events-none bg-black">
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-[0.03] brightness-200"></div>
            <div class="absolute top-[-10%] right-[10%] w-[800px] h-[800px] bg-indigo-600/20 rounded-full blur-[120px] mix-blend-screen animate-float"></div>
            <div class="absolute bottom-[-10%] left-[10%] w-[600px] h-[600px] bg-cyan-600/10 rounded-full blur-[100px] mix-blend-screen animate-float-delayed"></div>
        </div>

        <template #header>
            <div class="relative z-10 flex items-center justify-between px-2">
                <div>
                    <h2 class="text-4xl font-bold text-white tracking-tight">Voice</h2>
                    <p class="text-white/40 text-sm font-medium tracking-wide uppercase mt-1">Community Consensus</p>
                </div>
                <div class="hidden sm:flex items-center gap-3 px-4 py-2 bg-white/5 border border-white/10 rounded-full backdrop-blur-md">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span class="text-xs font-bold text-white/70 uppercase tracking-widest">System Online</span>
                </div>
            </div>
        </template>

        <div class="py-12 relative z-10">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-16">
                
                <section class="animate-slide-up" style="animation-delay: 0.1s;">
                    <div class="group relative bg-[#1c1c1e]/70 backdrop-blur-3xl border border-white/10 rounded-[2.5rem] p-1 shadow-2xl overflow-hidden">
                        
                        <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/20 via-purple-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>

                        <div class="relative bg-[#1c1c1e]/90 rounded-[2.3rem] p-8 lg:p-10">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">Start a New Poll</h3>
                                    <p class="text-white/40 text-sm">Ask the community for their opinion.</p>
                                </div>
                            </div>

                            <form @submit.prevent="submitPoll" class="space-y-6">
                                <div class="relative group/input">
                                    <input v-model="createForm.title" type="text" placeholder="What would you like to ask?" 
                                           class="w-full bg-white/5 border-none focus:ring-0 rounded-2xl text-white placeholder-white/20 px-6 py-5 text-xl font-medium transition-all group-hover/input:bg-white/10 focus:bg-white/10">
                                </div>

                                <div class="space-y-3 pl-4 border-l-2 border-white/5">
                                    <label class="text-xs font-bold text-white/30 uppercase tracking-widest mb-2 block">Poll Options</label>
                                    
                                    <transition-group name="list">
                                        <div v-for="(option, index) in createForm.options" :key="index" class="flex items-center gap-3">
                                            <div class="flex-1 relative">
                                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/20 font-mono text-sm">0{{ index + 1 }}</span>
                                                <input v-model="createForm.options[index]" type="text" placeholder="Enter option..." 
                                                       class="w-full bg-white/5 border-none focus:ring-1 focus:ring-indigo-500/50 rounded-xl text-white placeholder-white/20 pl-12 pr-4 py-3 transition-all focus:bg-white/10">
                                            </div>
                                            <button v-if="createForm.options.length > 2" type="button" @click="removeOptionField(index)" 
                                                    class="w-10 h-10 rounded-xl bg-white/5 hover:bg-rose-500/20 hover:text-rose-400 text-white/30 flex items-center justify-center transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </transition-group>
                                </div>

                                <div class="flex items-center justify-between pt-4">
                                    <button type="button" @click="addOptionField" class="text-sm font-bold text-indigo-400 hover:text-indigo-300 transition-colors flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-indigo-500/10">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Add Option
                                    </button>

                                    <button type="submit" class="px-8 py-3 bg-white text-black font-bold rounded-full hover:scale-105 active:scale-95 transition-all shadow-[0_0_20px_rgba(255,255,255,0.2)]">
                                        Publish Poll
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>

                <section class="space-y-8">
                    <div class="flex items-center justify-between px-2">
                        <h3 class="text-2xl font-bold text-white">Active Polls</h3>
                        <div class="text-sm text-white/40">{{ polls.length }} topics active</div>
                    </div>

                    <div v-if="polls.length === 0" class="text-center py-20 bg-white/5 rounded-[2.5rem] border border-dashed border-white/10">
                        <p class="text-white/30 text-lg">No active polls found.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-8">
                        <div v-for="poll in polls" :key="poll.id" class="group animate-slide-up">
                            
                            <div class="relative bg-[#1c1c1e]/60 backdrop-blur-xl border border-white/10 rounded-[2rem] p-8 transition-all duration-300 hover:bg-[#2c2c2e]/60 hover:shadow-2xl hover:shadow-black/50">
                                
                                <div class="flex justify-between items-start mb-8">
                                    <div class="max-w-2xl">
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="px-3 py-1 bg-white/5 rounded-full text-[10px] font-bold text-white/50 uppercase tracking-widest border border-white/5">
                                                Public
                                            </span>
                                            <span class="text-xs text-white/30">{{ new Date(poll.created_at).toLocaleDateString() }}</span>
                                        </div>
                                        <h3 class="text-3xl font-bold text-white leading-tight group-hover:text-indigo-200 transition-colors">
                                            {{ poll.title }}
                                        </h3>
                                    </div>
                                    <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-white/30">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path></svg>
                                    </div>
                                </div>

                                <div v-if="poll.user_has_voted" class="space-y-5">
                                    <div v-for="option in poll.options" :key="option.id" class="relative">
                                        <div class="flex justify-between items-end mb-2 px-1">
                                            <span class="font-bold text-white text-lg">{{ option.option_text }}</span>
                                            <div class="text-right">
                                                <span class="text-2xl font-bold text-white">{{ getPercentage(option.votes_count, poll.total_votes) }}%</span>
                                            </div>
                                        </div>
                                        
                                        <div class="h-4 bg-white/5 rounded-full overflow-hidden backdrop-blur-sm shadow-inner">
                                            <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 shadow-[0_0_15px_rgba(168,85,247,0.4)] transition-all duration-1000 ease-out relative"
                                                 :style="{ width: getPercentage(option.votes_count, poll.total_votes) + '%' }">
                                                <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                                            </div>
                                        </div>
                                        <p class="text-xs text-white/30 mt-1 pl-1 font-mono">{{ option.votes_count }} votes</p>
                                    </div>

                                    <div class="mt-8 pt-6 border-t border-white/5 flex items-center gap-2 text-emerald-400 justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span class="text-sm font-bold uppercase tracking-wider">Vote Recorded</span>
                                    </div>
                                </div>

                                <div v-else class="grid gap-4">
                                    <button v-for="option in poll.options" :key="option.id" 
                                            @click="castVote(poll.id, option.id)"
                                            class="relative group/btn flex items-center justify-between p-5 rounded-2xl bg-white/5 border border-white/5 hover:bg-indigo-600 hover:border-indigo-500 hover:shadow-[0_0_30px_rgba(79,70,229,0.3)] transition-all duration-300 active:scale-[0.98]">
                                        
                                        <span class="text-lg font-bold text-white group-hover/btn:translate-x-2 transition-transform duration-300">
                                            {{ option.option_text }}
                                        </span>
                                        
                                        <div class="w-8 h-8 rounded-full border-2 border-white/20 group-hover/btn:border-white group-hover/btn:bg-white text-indigo-600 flex items-center justify-center opacity-50 group-hover/btn:opacity-100 transition-all">
                                            <svg class="w-4 h-4 opacity-0 group-hover/btn:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                    </button>
                                </div>

                                <div class="mt-6 flex items-center justify-between pt-6 border-t border-white/5">
                                    <div class="flex -space-x-2">
                                        <div class="w-8 h-8 rounded-full border-2 border-[#1c1c1e] bg-gray-600"></div>
                                        <div class="w-8 h-8 rounded-full border-2 border-[#1c1c1e] bg-gray-500"></div>
                                        <div class="w-8 h-8 rounded-full border-2 border-[#1c1c1e] bg-gray-400 flex items-center justify-center text-[10px] text-black font-bold">+{{ poll.total_votes }}</div>
                                    </div>
                                    <span class="text-xs font-bold text-white/30 uppercase tracking-widest">Total Votes</span>
                                </div>

                            </div>
                        </div>
                    </div>
                </section>
                
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<style scoped>
/* Floating Animation for background orbs */
@keyframes float {
    0% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0, 0) scale(1); }
}
.animate-float { animation: float 20s ease-in-out infinite; }
.animate-float-delayed { animation: float 25s ease-in-out infinite reverse; }

/* Entry Animations */
@keyframes slide-up {
    from { opacity: 0; transform: translateY(30px); filter: blur(10px); }
    to { opacity: 1; transform: translateY(0); filter: blur(0); }
}
.animate-slide-up { animation: slide-up 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

/* List Transitions */
.list-enter-active, .list-leave-active { transition: all 0.4s ease; }
.list-enter-from, .list-leave-to { opacity: 0; transform: translateX(-20px); }
</style>