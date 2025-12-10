<template>
    <div class="bg-gradient-to-br from-lime-600 to-green-700 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://upload.wikimedia.org/wikipedia/commons/e/ec/World_map_blank_without_borders.svg')] bg-cover"></div>
        
        <div class="relative z-10">
            <h3 class="font-bold text-lg mb-1 flex items-center gap-2">
                🚛 Waste Collection
            </h3>
            <p class="text-lime-200 text-xs mb-6">Zone 4: Pili Centro</p>

            <div class="relative pt-4 pb-2">
                <div class="h-1 bg-white/30 rounded-full"></div>
                <div class="absolute top-0 left-[70%] transform -translate-x-1/2 flex flex-col items-center">
                    <span class="text-2xl animate-bounce">🚛</span>
                    <div class="px-2 py-0.5 bg-white text-green-800 text-[10px] font-bold rounded-full shadow-sm mt-1">
                        Arr. 5 mins
                    </div>
                </div>
            </div>
            
            <div class="flex justify-between text-[10px] font-bold uppercase tracking-widest mt-2 text-lime-200">
                <span>Depot</span>
                <span>Your Home</span>
            </div>
        </div>
    </div>
</template>