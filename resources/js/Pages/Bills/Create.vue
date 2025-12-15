<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const user = usePage().props.auth.user;
const activeTab = ref('pay'); // 'pay' or 'history'

// Mock History Data (Replace with prop from controller in real app)
const history = [
    { id: 1, biller: 'Meralco', date: 'Oct 24, 2023', amount: '2,450.00', status: 'Completed', ref: 'TRX-883920' },
    { id: 2, biller: 'PLDT', date: 'Sep 15, 2023', amount: '1,699.00', status: 'Completed', ref: 'TRX-774821' },
    { id: 3, biller: 'Maynilad', date: 'Aug 05, 2023', amount: '450.00', status: 'Processing', ref: 'TRX-663910' },
];

const form = useForm({
    biller_name: '',
    account_number: '',
    amount: '',
});

const submit = () => {
    form.post(route('bills.store'));
};
</script>

<template>
    <Head title="Secure Payment" />

    <AuthenticatedLayout>
        <div class="fixed inset-0 z-0 pointer-events-none">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-[#0f172a] via-[#050505] to-black"></div>
            <div class="absolute inset-0 opacity-[0.03] bg-[url('https://grainy-gradients.vercel.app/noise.svg')] brightness-100 contrast-150"></div>
            <div class="absolute bottom-0 right-0 w-[60vw] h-[60vw] bg-emerald-600/5 rounded-full blur-[120px]"></div>
        </div>

        <div class="relative z-10 py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row justify-between items-center mb-12 gap-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-emerald-500/10 rounded-2xl border border-emerald-500/20 text-emerald-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h2 class="font-black text-3xl text-white tracking-tight">Payment Gateway</h2>
                        <p class="text-slate-400 text-sm">Secure Municipal Transactions</p>
                    </div>
                </div>

                <div class="bg-black/40 p-1 rounded-xl border border-white/10 flex relative">
                    <div class="absolute top-1 bottom-1 w-[calc(50%-4px)] bg-emerald-600 rounded-lg transition-all duration-300 shadow-lg shadow-emerald-500/20"
                         :class="activeTab === 'pay' ? 'left-1' : 'left-[calc(50%+4px)]'"></div>
                    
                    <button @click="activeTab = 'pay'" class="relative z-10 px-6 py-2 text-xs font-bold uppercase tracking-widest transition-colors w-32"
                            :class="activeTab === 'pay' ? 'text-white' : 'text-slate-400 hover:text-white'">
                        Pay Bill
                    </button>
                    <button @click="activeTab = 'history'" class="relative z-10 px-6 py-2 text-xs font-bold uppercase tracking-widest transition-colors w-32"
                            :class="activeTab === 'history' ? 'text-white' : 'text-slate-400 hover:text-white'">
                        History
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
                
                <div class="relative perspective-1000 animate-fade-in-up">
                    <div class="relative w-full aspect-[1.586/1] rounded-[2rem] overflow-hidden shadow-2xl transition-transform duration-500 hover:rotate-1 hover:scale-[1.02] group border border-white/10">
                        
                        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-[#0a0a0a] to-emerald-950"></div>
                        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20"></div>
                        
                        <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/5 to-transparent -translate-x-[100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>

                        <div class="absolute inset-0 p-8 flex flex-col justify-between text-white font-mono z-10">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-[10px] opacity-60 tracking-[0.2em] uppercase block mb-1">Digital Resident ID</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                        <span class="text-xs font-bold text-emerald-400">ACTIVE</span>
                                    </div>
                                </div>
                                <span class="font-bold text-xl italic tracking-widest opacity-30">EPILI</span>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="flex gap-4 items-center">
                                    <div class="w-14 h-9 bg-gradient-to-br from-yellow-200 to-yellow-500 rounded-lg shadow-lg relative overflow-hidden">
                                        <div class="absolute inset-0 bg-black/10"></div>
                                    </div>
                                    <div class="text-2xl md:text-3xl tracking-widest flex items-center gap-4 drop-shadow-md">
                                        <span class="opacity-50">••••</span>
                                        <span class="opacity-50">••••</span>
                                        <span class="opacity-50">••••</span>
                                        <span class="text-white">{{ user.id.toString().padStart(4, '0') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between items-end">
                                <div>
                                    <p class="text-[9px] opacity-60 uppercase tracking-widest mb-1">Card Holder</p>
                                    <p class="font-bold tracking-wide uppercase text-sm md:text-base">{{ user.name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[9px] opacity-60 uppercase tracking-widest mb-1">Expires</p>
                                    <p class="font-bold tracking-wide">12/28</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-3 gap-4">
                        <div class="bg-white/5 border border-white/10 rounded-xl p-4 text-center">
                            <div class="text-emerald-400 text-xl mb-1">🔒</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">SSL Secure</div>
                        </div>
                        <div class="bg-white/5 border border-white/10 rounded-xl p-4 text-center">
                            <div class="text-blue-400 text-xl mb-1">⚡</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Instant</div>
                        </div>
                        <div class="bg-white/5 border border-white/10 rounded-xl p-4 text-center">
                            <div class="text-purple-400 text-xl mb-1">🛡️</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Protected</div>
                        </div>
                    </div>
                </div>

                <div class="relative min-h-[500px]">
                    <Transition name="fade" mode="out-in">
                        
                        <div v-if="activeTab === 'pay'" class="bg-[#0f1115]/60 backdrop-blur-xl border border-white/10 rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden">
                            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
                            
                            <h3 class="font-bold text-xl text-white mb-8 flex items-center gap-2">
                                <span class="text-emerald-400">💸</span> Transaction Details
                            </h3>
                            
                            <form @submit.prevent="submit" class="space-y-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Biller</label>
                                    <div class="relative group">
                                        <select v-model="form.biller_name" class="w-full bg-white/5 border border-white/10 text-white rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-emerald-500/50 focus:border-transparent transition-all appearance-none cursor-pointer hover:bg-white/10">
                                            <option disabled value="" class="text-slate-500">Select Provider...</option>
                                            <option value="Meralco" class="bg-slate-900">⚡ Meralco (Electric)</option>
                                            <option value="Maynilad" class="bg-slate-900">💧 Maynilad (Water)</option>
                                            <option value="PLDT" class="bg-slate-900">🌐 PLDT (Internet)</option>
                                            <option value="Globe" class="bg-slate-900">📱 Globe Telecom</option>
                                        </select>
                                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500 group-hover:text-emerald-400 transition-colors">▼</div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Account No.</label>
                                    <input v-model="form.account_number" type="number" placeholder="0000 0000 0000" class="w-full bg-white/5 border border-white/10 text-white font-mono rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-emerald-500/50 focus:border-transparent transition-all placeholder-slate-600 hover:bg-white/10">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Amount</label>
                                    <div class="relative group">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xl font-bold text-emerald-500 transition-transform group-focus-within:scale-110">₱</span>
                                        <input v-model="form.amount" type="number" step="0.01" placeholder="0.00" class="w-full bg-emerald-500/10 border border-emerald-500/20 text-2xl font-bold text-emerald-400 rounded-xl pl-10 pr-4 py-3.5 focus:ring-2 focus:ring-emerald-500/50 focus:border-transparent transition-all placeholder-emerald-500/30">
                                    </div>
                                </div>

                                <div class="pt-4">
                                    <button :disabled="form.processing" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-4 rounded-xl shadow-[0_0_20px_rgba(16,185,129,0.3)] transform hover:-translate-y-0.5 transition-all flex justify-center items-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span v-if="!form.processing">Confirm Payment</span>
                                        <span v-else>Processing...</span>
                                        <svg v-if="!form.processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </button>
                                    <p class="text-center text-[10px] text-slate-500 mt-4 uppercase tracking-widest">
                                        Secure Encrypted Transaction
                                    </p>
                                </div>
                            </form>
                        </div>

                        <div v-else class="bg-[#0f1115]/60 backdrop-blur-xl border border-white/10 rounded-[2.5rem] p-8 shadow-2xl h-full flex flex-col">
                            <h3 class="font-bold text-xl text-white mb-6 flex items-center gap-2">
                                <span class="text-blue-400">📜</span> Recent Transactions
                            </h3>

                            <div class="space-y-4 overflow-y-auto custom-scrollbar pr-2 flex-1 max-h-[450px]">
                                <div v-for="item in history" :key="item.id" class="group bg-white/5 border border-white/5 rounded-2xl p-4 hover:bg-white/10 hover:border-white/10 transition-all flex justify-between items-center">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-lg shadow-inner group-hover:scale-110 transition-transform">
                                            {{ item.biller === 'Meralco' ? '⚡' : item.biller === 'Maynilad' ? '💧' : '🌐' }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-white text-sm">{{ item.biller }}</h4>
                                            <p class="text-[10px] text-slate-400 font-mono">{{ item.date }} • {{ item.ref }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-white text-sm">₱{{ item.amount }}</div>
                                        <span :class="['text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded border', item.status === 'Completed' ? 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20' : 'text-blue-400 bg-blue-500/10 border-blue-500/20']">
                                            {{ item.status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6 pt-4 border-t border-white/10 text-center">
                                <button class="text-xs text-slate-400 hover:text-white uppercase tracking-widest font-bold transition-colors">View All History</button>
                            </div>
                        </div>

                    </Transition>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.perspective-1000 { perspective: 1000px; }
.animate-fade-in-up { animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Fade Transition for Tabs */
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease, transform 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: scale(0.98); }

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
</style>