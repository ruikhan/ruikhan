<script setup>
defineProps({
    show: Boolean
});
</script>

<template>
    <Transition name="nano-fade">
        <div v-if="show" class="fixed inset-0 z-[9999] bg-[#050b14] flex flex-col items-center justify-center overflow-hidden font-sans">
            
            <div class="absolute inset-0 opacity-20 pointer-events-none">
                <div class="absolute inset-0" style="background-image: radial-gradient(#3b82f6 1px, transparent 1px); background-size: 30px 30px; opacity: 0.3;"></div>
                
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-[100px] animate-pulse-slow"></div>
            </div>

            <div class="relative z-10 flex flex-col items-center">
                
                <div class="relative mb-8 p-6">
                    <div class="absolute inset-0 border border-blue-500/30 rounded-full animate-spin-slow"></div>
                    <div class="absolute inset-0 border-t-2 border-blue-400 rounded-full animate-spin-reverse"></div>
                    
                    <div class="relative w-32 h-auto overflow-hidden rounded-lg p-2 bg-slate-900/50 backdrop-blur-sm border border-white/5">
                        <img src="/images/enotpili.png" alt="Loading..." class="w-full h-full object-contain relative z-10" />
                        
                        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-transparent via-blue-400/20 to-transparent z-20 animate-scan"></div>
                    </div>
                </div>

                <h1 class="text-2xl font-bold text-white tracking-[0.2em] mb-2 animate-pulse">SYSTEM INITIALIZING</h1>
                <p class="text-blue-400 text-xs font-mono uppercase tracking-widest mb-6">Loading Modules...</p>

                <div class="w-64 h-1 bg-slate-800 rounded-full overflow-hidden relative">
                    <div class="absolute top-0 left-0 h-full w-full bg-gradient-to-r from-transparent via-blue-500 to-transparent animate-loading-bar"></div>
                </div>

                <div class="flex gap-2 mt-4">
                    <div class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                    <div class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                    <div class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                </div>

            </div>

        </div>
    </Transition>
</template>

<style scoped>
/* FADE IN/OUT for the Loader itself */
.nano-fade-enter-active,
.nano-fade-leave-active {
    transition: opacity 0.5s ease;
}
.nano-fade-enter-from,
.nano-fade-leave-to {
    opacity: 0;
}

/* ANIMATIONS */
@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.animate-spin-slow { animation: spin-slow 8s linear infinite; }

@keyframes spin-reverse {
    from { transform: rotate(360deg); }
    to { transform: rotate(0deg); }
}
.animate-spin-reverse { animation: spin-reverse 4s linear infinite; }

@keyframes scan {
    0% { transform: translateY(-100%); }
    50% { transform: translateY(100%); }
    100% { transform: translateY(-100%); }
}
.animate-scan { animation: scan 3s ease-in-out infinite; }

@keyframes loading-bar {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}
.animate-loading-bar { animation: loading-bar 1.5s cubic-bezier(0.4, 0, 0.2, 1) infinite; }

@keyframes pulse-slow {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(1.1); }
}
.animate-pulse-slow { animation: pulse-slow 4s ease-in-out infinite; }
</style>