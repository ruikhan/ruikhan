<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;

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
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-100 rounded-lg text-emerald-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Secure Payment Gateway</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-start">
                    
                    <div class="relative perspective-1000 animate-fade-in-up">
                        <div class="relative w-full aspect-[1.586/1] rounded-3xl overflow-hidden shadow-2xl transition-transform duration-500 hover:rotate-1 hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-900"></div>
                            
                            <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl -mr-10 -mt-10"></div>
                            <div class="absolute bottom-0 left-0 w-64 h-64 bg-emerald-500 opacity-10 rounded-full blur-3xl -ml-10 -mb-10"></div>
                            
                            <div class="absolute inset-0 p-8 flex flex-col justify-between text-white font-mono">
                                <div class="flex justify-between items-start">
                                    <span class="text-xs opacity-70 tracking-widest">DIGITAL RESIDENT CARD</span>
                                    <span class="font-bold text-lg italic tracking-widest opacity-50">VISA</span>
                                </div>
                                
                                <div class="space-y-2">
                                    <div class="flex gap-4">
                                        <div class="w-12 h-8 bg-yellow-400 rounded-md opacity-80 flex relative overflow-hidden">
                                            <div class="absolute inset-0 bg-gradient-to-br from-transparent to-black/20"></div>
                                        </div>
                                        <div class="text-2xl tracking-widest flex items-center gap-4 drop-shadow-md">
                                            <span>****</span>
                                            <span>****</span>
                                            <span>****</span>
                                            <span>{{ user.id.toString().padStart(4, '0') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-between items-end">
                                    <div>
                                        <p class="text-[10px] opacity-70 uppercase">Card Holder</p>
                                        <p class="font-bold tracking-wide uppercase">{{ user.name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] opacity-70 uppercase">Valid Thru</p>
                                        <p class="font-bold tracking-wide">12/28</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/10 to-transparent pointer-events-none"></div>
                        </div>

                        <div class="mt-8 grid grid-cols-3 gap-4 text-center">
                            <div class="p-3 bg-white rounded-xl shadow-sm border border-slate-100">
                                <div class="text-2xl mb-1">🔒</div>
                                <div class="text-[10px] font-bold text-slate-500 uppercase">256-bit SSL</div>
                            </div>
                            <div class="p-3 bg-white rounded-xl shadow-sm border border-slate-100">
                                <div class="text-2xl mb-1">⚡</div>
                                <div class="text-[10px] font-bold text-slate-500 uppercase">Instant Post</div>
                            </div>
                            <div class="p-3 bg-white rounded-xl shadow-sm border border-slate-100">
                                <div class="text-2xl mb-1">🛡️</div>
                                <div class="text-[10px] font-bold text-slate-500 uppercase">Fraud Protect</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-100 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-emerald-400 to-teal-500"></div>
                        
                        <h3 class="font-bold text-xl text-slate-800 mb-6">Payment Details</h3>
                        
                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wide">Select Provider</label>
                                <div class="relative">
                                    <select v-model="form.biller_name" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all appearance-none">
                                        <option disabled value="">Choose Service...</option>
                                        <option value="Meralco">⚡ Meralco (Electric)</option>
                                        <option value="Maynilad">💧 Maynilad (Water)</option>
                                        <option value="PLDT">🌐 PLDT (Internet)</option>
                                        <option value="Globe">📱 Globe Telecom</option>
                                    </select>
                                    <div class="absolute right-4 top-3.5 pointer-events-none text-slate-400">▼</div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wide">Account Number</label>
                                <input v-model="form.account_number" type="number" placeholder="0000 0000 0000" class="w-full bg-slate-50 border border-slate-200 font-mono text-slate-700 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wide">Total Amount</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-2 text-2xl font-bold text-emerald-600">₱</span>
                                    <input v-model="form.amount" type="number" step="0.01" placeholder="0.00" class="w-full bg-emerald-50/50 border border-emerald-100 text-3xl font-bold text-emerald-700 rounded-xl pl-10 pr-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all placeholder-emerald-200">
                                </div>
                            </div>

                            <button :disabled="form.processing" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-4 rounded-xl shadow-lg shadow-slate-900/30 transform hover:-translate-y-0.5 transition-all flex justify-center items-center gap-3 mt-4">
                                <span v-if="!form.processing">Confirm Payment</span>
                                <span v-else>Processing...</span>
                                <svg v-if="!form.processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                            
                            <p class="text-center text-xs text-slate-400 mt-4">
                                By clicking Confirm, you authorize the secure transaction.
                            </p>
                        </form>
                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.perspective-1000 { perspective: 1000px; }
.animate-fade-in-up { animation: fadeUp 0.8s ease-out; }
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>