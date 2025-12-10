<script setup>
</script>

<template>
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden font-poppins">
        
        <div class="absolute inset-0 z-0">
            <img src="/images/bg.png" alt="Background" class="w-full h-full object-cover scale-105 blur-sm" />
            <div class="absolute inset-0 bg-gradient-to-r from-blue-900/95 to-blue-800/80 mix-blend-multiply"></div>
        </div>

        <div class="relative z-10 w-full max-w-[1100px] px-4">
            <slot />
        </div>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>