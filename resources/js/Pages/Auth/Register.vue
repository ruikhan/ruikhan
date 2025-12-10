<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    phone_number: '',
    role: 'resident', // Default role
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Create Account" />

        <div class="bg-white rounded-[2rem] shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[700px] animate-fade-in-up">

            <div class="hidden md:flex md:w-5/12 bg-[#0a1e3b] relative p-10 flex-col justify-between overflow-hidden">
                
                <div class="absolute top-0 left-0 w-full h-full">
                    <div class="absolute top-[-20%] right-[-20%] w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-[80px] animate-pulse-slow"></div>
                    <div class="absolute bottom-[-10%] left-[-10%] w-[400px] h-[400px] bg-purple-600/20 rounded-full blur-[80px] animate-pulse-slow" style="animation-delay: 2s;"></div>
                </div>

                <div class="relative z-10">
                    <h1 class="text-3xl font-bold text-white tracking-wide mb-2">Join E-PILI</h1>
                    <p class="text-blue-200 text-sm">Create your digital identity today.</p>
                </div>

                <div class="relative z-10 space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-xl shadow-inner">🚀</div>
                        <div>
                            <h3 class="font-bold text-white text-sm">Fast-Track Services</h3>
                            <p class="text-xs text-blue-200/70 mt-1">Skip the line. Request documents and permits online.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-xl shadow-inner">🔒</div>
                        <div>
                            <h3 class="font-bold text-white text-sm">Secure Data</h3>
                            <p class="text-xs text-blue-200/70 mt-1">Your data is encrypted and protected by government standards.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-xl shadow-inner">📱</div>
                        <div>
                            <h3 class="font-bold text-white text-sm">Real-Time Alerts</h3>
                            <p class="text-xs text-blue-200/70 mt-1">Get instant SMS and Email notifications on your request status.</p>
                        </div>
                    </div>
                </div>

                <div class="relative z-10">
                    <p class="text-[10px] text-white/40">
                        By signing up, you agree to the Provincial Data Privacy Act of 2025.
                    </p>
                </div>
            </div>

            <div class="w-full md:w-7/12 bg-white p-8 md:p-12 relative overflow-y-auto">
                
                <div class="max-w-md mx-auto">
                    <h2 class="text-[#0a1e3b] font-bold text-2xl mb-1">Get Started</h2>
                    <p class="text-gray-400 text-sm mb-8">Fill in your details to create an account.</p>

                    <form @submit.prevent="submit" class="space-y-5">
                        
                        <div class="grid grid-cols-2 gap-3 mb-6 p-1 bg-gray-100 rounded-xl">
                            <label class="cursor-pointer">
                                <input type="radio" v-model="form.role" value="resident" class="peer sr-only">
                                <div class="text-center py-2.5 rounded-lg text-sm font-bold text-gray-500 transition-all peer-checked:bg-white peer-checked:text-blue-700 peer-checked:shadow-sm">
                                    👤 Resident
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" v-model="form.role" value="business_owner" class="peer sr-only">
                                <div class="text-center py-2.5 rounded-lg text-sm font-bold text-gray-500 transition-all peer-checked:bg-white peer-checked:text-purple-700 peer-checked:shadow-sm">
                                    💼 Business Owner
                                </div>
                            </label>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-600 ml-1">Full Name</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </span>
                                <input v-model="form.name" type="text" required placeholder="Juan Dela Cruz" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 pl-10 pr-4 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all hover:bg-white">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-600 ml-1">Phone Number</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </span>
                                <input v-model="form.phone_number" type="text" required placeholder="0912 345 6789" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 pl-10 pr-4 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all hover:bg-white">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-600 ml-1">Email Address</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </span>
                                <input v-model="form.email" type="email" required placeholder="juan@example.com" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 pl-10 pr-4 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all hover:bg-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-600 ml-1">Password</label>
                                <input v-model="form.password" type="password" required class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all hover:bg-white">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-600 ml-1">Confirm Password</label>
                                <input v-model="form.password_confirmation" type="password" required class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 px-4 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all hover:bg-white">
                            </div>
                        </div>

                        <div v-if="form.errors.email" class="text-red-500 text-xs mt-2">{{ form.errors.email }}</div>
                        <div v-if="form.errors.password" class="text-red-500 text-xs mt-2">{{ form.errors.password }}</div>

                        <button :disabled="form.processing" class="w-full bg-[#0a1e3b] hover:bg-[#15325b] text-white font-bold py-3.5 rounded-xl shadow-lg transform transition hover:-translate-y-0.5 active:scale-[0.98] mt-4 flex justify-center items-center gap-2">
                            <span v-if="!form.processing">Create Account</span>
                            <span v-else>Creating...</span>
                            <svg v-if="!form.processing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </button>

                        <div class="text-center mt-6">
                            <p class="text-xs text-gray-500">
                                Already have an account? 
                                <Link :href="route('login')" class="text-[#0a1e3b] font-bold hover:underline">
                                    Sign In here
                                </Link>
                            </p>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </GuestLayout>
</template>

<style scoped>
@keyframes pulse-slow {
    0%, 100% { opacity: 0.2; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(1.1); }
}
.animate-pulse-slow { animation: pulse-slow 8s ease-in-out infinite; }

@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fade-in-up 0.6s ease-out; }
</style>