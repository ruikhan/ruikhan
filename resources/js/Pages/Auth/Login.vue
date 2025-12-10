<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="bg-white rounded-[2rem] shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[650px] animate-fade-in-up">

            <div class="hidden md:flex md:w-5/12 bg-[#0a1e3b] relative p-8 flex-col justify-between overflow-hidden">
                
                <div class="absolute top-0 left-0 w-full h-full opacity-20 pointer-events-none">
                    <div class="absolute top-[-50%] left-[-50%] w-[200%] h-[200%] bg-gradient-to-tr from-blue-600 to-transparent rounded-full animate-slow-spin"></div>
                </div>

                <div class="relative z-10 mt-4">
                    <h1 class="text-3xl font-bold text-white tracking-wide">Welcome to E-PILI</h1>
                </div>

                <div class="relative z-10 space-y-4 mt-8">
                    <div class="group flex items-center gap-4 p-4 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all duration-300 cursor-pointer hover:translate-x-1">
                        <div class="text-2xl opacity-80 group-hover:opacity-100 transition">🏛️</div>
                        <div>
                            <h3 class="font-semibold text-white text-sm">Provincial Capital</h3>
                            <p class="text-[10px] text-blue-200 opacity-60">Center of governance in Camarines Sur</p>
                        </div>
                    </div>

                    <div class="group flex items-center gap-4 p-4 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all duration-300 cursor-pointer hover:translate-x-1">
                        <div class="text-2xl opacity-80 group-hover:opacity-100 transition">🌾</div>
                        <div>
                            <h3 class="font-semibold text-white text-sm">Agricultural Hub</h3>
                            <p class="text-[10px] text-blue-200 opacity-60">Known for Pili nuts and rice production</p>
                        </div>
                    </div>

                    <div class="group flex items-center gap-4 p-4 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all duration-300 cursor-pointer hover:translate-x-1">
                        <div class="text-2xl opacity-80 group-hover:opacity-100 transition">🎓</div>
                        <div>
                            <h3 class="font-semibold text-white text-sm">Educational Center</h3>
                            <p class="text-[10px] text-blue-200 opacity-60">Home to numerous institutions</p>
                        </div>
                    </div>

                    <div class="group flex items-center gap-4 p-4 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all duration-300 cursor-pointer hover:translate-x-1">
                        <div class="text-2xl opacity-80 group-hover:opacity-100 transition">🚀</div>
                        <div>
                            <h3 class="font-semibold text-white text-sm">Innovation & Progress</h3>
                            <p class="text-[10px] text-blue-200 opacity-60">First e-governance system in the region</p>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 mt-auto pt-6">
                    <div class="relative bg-gradient-to-r from-[#1e3a5f] to-[#162d4a] border border-yellow-500/30 rounded-xl p-5 shadow-lg group hover:scale-[1.02] transition-transform duration-300">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent skew-x-12 translate-x-[-150%] group-hover:translate-x-[150%] transition-transform duration-1000"></div>
                        
                        <div class="text-center">
                            <div class="flex justify-center mb-1">
                                <span class="text-yellow-400 text-xs font-bold uppercase tracking-widest">✨ New Services!</span>
                            </div>
                            <h3 class="text-white font-bold text-md mb-1">E-GOVERNANCE PORTAL</h3>
                            <p class="text-yellow-100/60 text-[10px] italic">Request documents online 24/7</p>
                            
                            <div class="mt-3 pt-3 border-t border-white/10 text-[10px] text-gray-300">
                                Fast, convenient, and secure <br>
                                <span class="text-yellow-400 font-semibold italic">Your trusted partner!</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full md:w-7/12 bg-white p-8 md:p-12 flex flex-col justify-center relative">
                
                <div class="max-w-md mx-auto w-full">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-[#0a1e3b] text-white rounded-full flex items-center justify-center font-serif font-bold text-xl mx-auto mb-3 shadow-lg">
                            PILI
                        </div>
                        <h2 class="text-[#0a1e3b] font-bold text-2xl tracking-wide">ENOTPILI</h2>
                        <p class="text-gray-400 text-xs uppercase tracking-widest mb-6">E-Governance Portal</p>

                        <h3 class="text-xl font-bold text-gray-800">Welcome Back</h3>
                        <p class="text-gray-500 text-sm mt-1">Login to continue</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">
                        
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-600 ml-1">Email Address</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-600 transition">
                                    📧
                                </span>
                                <input id="email" 
                                       type="email" 
                                       v-model="form.email" 
                                       required 
                                       placeholder="juan@example.com"
                                       class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 pl-10 pr-4 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all hover:bg-white" />
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-600 ml-1">Password</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-600 transition">
                                    🔒
                                </span>
                                <input id="password" 
                                       type="password" 
                                       v-model="form.password" 
                                       required 
                                       placeholder="••••••••"
                                       class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2.5 pl-10 pr-4 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all hover:bg-white" />
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <label class="flex items-center text-gray-600 cursor-pointer">
                                <input type="checkbox" v-model="form.remember" class="rounded border-gray-300 text-blue-900 focus:ring-blue-900 mr-2">
                                Remember me
                            </label>

                            <Link v-if="canResetPassword" :href="route('password.request')" class="text-blue-900 font-semibold hover:underline">
                                Forgot Password?
                            </Link>
                        </div>

                        <button :disabled="form.processing" class="w-full bg-[#0a1e3b] hover:bg-[#15325b] text-white font-bold py-3 rounded-lg shadow-lg transform transition hover:-translate-y-0.5 active:scale-[0.98] flex items-center justify-center gap-2 group">
                            Sign In 
                            <span class="group-hover:translate-x-1 transition-transform">→</span>
                        </button>
                    </form>

                    <div class="mt-8 text-center">
                        <p class="text-xs text-gray-500 mb-6">
                            Don't have an account? 
                            <Link :href="route('register')" class="text-[#0a1e3b] font-bold hover:underline">
                                Sign Up
                            </Link>
                        </p>

                        <div class="relative flex py-2 items-center">
                            <div class="flex-grow border-t border-gray-200"></div>
                            <span class="flex-shrink-0 mx-4 text-gray-400 text-[10px] uppercase">Or continue with</span>
                            <div class="flex-grow border-t border-gray-200"></div>
                        </div>

                        <div class="flex gap-4 mt-4 justify-center">
                            <button class="flex-1 max-w-[140px] border border-gray-200 rounded-lg py-2 hover:bg-gray-50 transition flex items-center justify-center gap-2 shadow-sm">
                                <span class="font-bold text-gray-600 text-sm"><span class="text-blue-500">G</span>oogle</span>
                            </button>
                            <button class="flex-1 max-w-[140px] border border-gray-200 rounded-lg py-2 hover:bg-gray-50 transition flex items-center justify-center gap-2 shadow-sm">
                                <span class="font-bold text-blue-800 text-lg">f</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </GuestLayout>
</template>

<style scoped>
/* Custom Animations */
@keyframes fade-in-up {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out forwards;
}

@keyframes slow-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.animate-slow-spin {
    animation: slow-spin 20s linear infinite;
}
</style>