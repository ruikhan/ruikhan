<script setup>
import { Link } from '@inertiajs/vue3'; // Assuming you are using Inertia/Laravel
import { computed, onMounted, onUnmounted, ref } from 'vue';

// --- State Management ---
const showMobileMenu = ref(false);
const isScrolled = ref(false);
const currentHeroIndex = ref(0);
const activeProduct = ref(null);

// --- Mock Data (Replace with your Props) ---
const business = ref({ name: 'NEXUS' });

const heroProducts = ref([
    {
        id: 1,
        name: 'CyberPunk Edge Runner',
        description: 'Immerse yourself in the future of gaming with high-fidelity audio and zero latency.',
        price: '12,999',
        image: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=2000&auto=format&fit=crop'
    },
    {
        id: 2,
        name: 'Neo-Tokyo Streetwear',
        description: 'Urban fashion redefined. Premium materials meet futuristic aesthetics.',
        price: '4,500',
        image: 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?q=80&w=2000&auto=format&fit=crop'
    },
    {
        id: 3,
        name: 'Quantum Mechanical Keyboards',
        description: 'Tactile perfection for the professional developer and gamer alike.',
        price: '8,200',
        image: 'https://images.unsplash.com/photo-1595225476474-87563907a212?q=80&w=2000&auto=format&fit=crop'
    }
]);

// Helper to generate mock products
const generateProducts = (count) => {
    return Array.from({ length: count }, (_, i) => ({
        id: i,
        name: `Premium Item ${i + 1}`,
        price: (Math.random() * 10000).toFixed(0),
        description: 'High-quality item with premium finish.',
        image: `https://source.unsplash.com/random/400x600?tech,fashion&sig=${i}`
    }));
};

const trendingRow = ref(generateProducts(8));
const newArrivalsRow = ref(generateProducts(8));
const popularRow = ref(generateProducts(8));
const allProductsRow = ref(generateProducts(8));

// --- Computed ---
const currentHero = computed(() => heroProducts.value[currentHeroIndex.value]);

// --- Methods ---
const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

const nextHero = () => {
    currentHeroIndex.value = (currentHeroIndex.value + 1) % heroProducts.value.length;
};

const prevHero = () => {
    currentHeroIndex.value = (currentHeroIndex.value - 1 + heroProducts.value.length) % heroProducts.value.length;
};

// Horizontal Scroll Logic
const scrollRow = (elementId, direction) => {
    const container = document.getElementById(elementId);
    if (container) {
        const scrollAmount = direction === 'left' ? -500 : 500;
        container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
};

const openQuickView = (product) => {
    activeProduct.value = product;
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
};

const closeQuickView = () => {
    activeProduct.value = null;
    document.body.style.overflow = 'auto';
};

// --- Lifecycle ---
let autoPlayInterval;

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    // Auto-rotate hero every 5 seconds
    autoPlayInterval = setInterval(nextHero, 5000);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    clearInterval(autoPlayInterval);
});
</script>

<template>
    <div class="min-h-screen bg-[#0a0a0a] text-white font-sans selection:bg-red-600 selection:text-white overflow-x-hidden">
        
        <nav :class="['fixed top-0 w-full z-50 transition-all duration-700 px-4 md:px-12 py-4 flex items-center justify-between', isScrolled ? 'bg-[#0a0a0a]/95 backdrop-blur-xl shadow-2xl' : 'bg-gradient-to-b from-black/80 via-black/40 to-transparent']">
            
            <div class="flex items-center gap-8">
                <Link :href="route('marketplace.index')" class="relative group">
                    <span class="text-red-600 text-3xl font-black tracking-tighter uppercase transform group-hover:scale-110 transition-transform duration-300 inline-block">
                        {{ business.name }}
                    </span>
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-500"></span>
                </Link>

                <div class="hidden lg:flex gap-6 text-sm font-medium text-gray-400">
                    <button class="relative group py-2">
                        <span class="group-hover:text-white transition-colors duration-300">Home</span>
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-white group-hover:w-full transition-all duration-300"></span>
                    </button>
                    <button class="relative group py-2">
                        <span class="group-hover:text-white transition-colors duration-300">Products</span>
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-white group-hover:w-full transition-all duration-300"></span>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-5 text-sm">
                <Link :href="route('cart.index')" class="hover:text-gray-300 relative transform hover:scale-110 transition-all duration-300 group">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold group-hover:scale-125 transition-transform duration-300">3</span>
                </Link>
                <div class="w-9 h-9 rounded bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center cursor-pointer transform hover:scale-110 transition-all duration-300 shadow-lg hover:shadow-blue-500/50">
                    <span class="text-sm">👤</span>
                </div>
            </div>
        </nav>

        <header class="relative w-full h-[90vh] md:h-screen overflow-hidden">
            <div v-for="(product, index) in heroProducts" :key="index" 
                 :class="['absolute inset-0 transition-all duration-1000 ease-in-out', currentHeroIndex === index ? 'opacity-100 scale-100' : 'opacity-0 scale-105']">
                <img :src="product.image" class="w-full h-full object-cover" />
                
                <div class="absolute inset-0 bg-gradient-to-r from-[#0a0a0a] via-[#0a0a0a]/70 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-[#0a0a0a] via-transparent to-transparent"></div>
            </div>

            <div class="absolute top-[25%] md:top-[30%] left-4 md:left-12 max-w-3xl z-10 space-y-6 animate-fade-slide-up">
                
                <div class="flex items-center gap-3 mb-3">
                    <span class="bg-gradient-to-r from-red-600 to-red-500 text-white text-xs font-bold px-3 py-1 rounded-md shadow-lg shadow-red-600/50 animate-pulse-slow">
                        FEATURED
                    </span>
                    <span class="text-gray-300 text-sm tracking-widest uppercase font-semibold">Premium Selection</span>
                </div>

                <h1 class="text-5xl md:text-8xl font-black tracking-tight leading-none text-white animate-title-reveal">
                    <span class="bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent">
                        {{ currentHero?.name || 'Premium Collection' }}
                    </span>
                </h1>
                
                <p class="text-base md:text-xl text-gray-200 leading-relaxed max-w-2xl drop-shadow-2xl line-clamp-3">
                    {{ currentHero?.description }}
                </p>

                <div class="flex flex-wrap gap-4 pt-6">
                    <button class="group relative flex items-center gap-3 bg-white text-black px-8 py-3.5 rounded-md font-bold hover:bg-gray-100 transition-all duration-300 text-lg shadow-2xl hover:shadow-white/20 hover:scale-105 transform overflow-hidden">
                        <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></span>
                        Buy Now
                    </button>
                    <button class="group flex items-center gap-3 bg-gray-800/60 backdrop-blur-md text-white px-8 py-3.5 rounded-md font-bold hover:bg-gray-700/60 transition-all duration-300 text-lg border border-gray-600/50 hover:border-gray-500 hover:scale-105 transform shadow-2xl">
                        More Info
                    </button>
                </div>

                <div class="flex gap-6 pt-4 text-sm text-gray-400">
                    <div class="flex items-center gap-2">
                        <span class="text-green-400 font-bold">★ 4.8</span>
                        <span>Rating</span>
                    </div>
                    <div class="h-4 w-px bg-gray-600"></div>
                    <div>
                        <span class="font-bold text-white">₱{{ currentHero?.price }}</span>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-12 left-4 md:left-12 z-20 flex gap-3">
                <button @click="prevHero" class="group w-12 h-12 rounded-full bg-gray-800/60 backdrop-blur-md hover:bg-gray-700/80 flex items-center justify-center transition-all duration-300 border border-gray-600/50 hover:border-gray-500 hover:scale-110 transform">
                    ←
                </button>
                <button @click="nextHero" class="group w-12 h-12 rounded-full bg-gray-800/60 backdrop-blur-md hover:bg-gray-700/80 flex items-center justify-center transition-all duration-300 border border-gray-600/50 hover:border-gray-500 hover:scale-110 transform">
                    →
                </button>
            </div>
        </header>

        <main class="relative z-20 -mt-24 pb-20 space-y-16 overflow-hidden">
            <div v-for="(row, title) in {'Trending Now': trendingRow, 'New Arrivals': newArrivalsRow, 'Popular Picks': popularRow, 'All Products': allProductsRow}" 
                 :key="title" 
                 class="space-y-4 group pl-4 md:pl-12">
                
                <div class="flex items-center justify-between pr-4 md:pr-12">
                    <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent group-hover:from-white group-hover:to-white transition-all duration-500 cursor-pointer flex items-center gap-3">
                        {{ title }}
                    </h2>
                </div>

                <div class="relative group/slider">
                    <button @click="scrollRow('row-' + title, 'left')" 
                            class="absolute left-0 top-0 bottom-0 z-40 bg-gradient-to-r from-black via-black/90 to-transparent w-16 flex items-center justify-center opacity-0 group-hover/slider:opacity-100 transition-all duration-500">
                        <div class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center">←</div>
                    </button>

                    <div :id="'row-' + title" class="flex gap-3 overflow-x-auto scrollbar-hide py-6 pr-12 scroll-smooth">
                        <div v-for="(product, index) in row" :key="product.id" 
                             @click="openQuickView(product)"
                             class="flex-none w-[180px] md:w-[280px] aspect-video relative rounded-lg overflow-hidden cursor-pointer transition-all duration-500 md:hover:scale-110 group/card shadow-2xl">
                            
                            <img :src="product.image || 'https://via.placeholder.com/300'" class="w-full h-full object-cover" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent opacity-80"></div>
                            
                            <div class="absolute bottom-0 left-0 right-0 p-4">
                                <p class="text-white text-sm font-bold truncate">{{ product.name }}</p>
                                <p class="text-green-400 font-bold text-xs">₱{{ product.price }}</p>
                            </div>
                        </div>
                    </div>

                    <button @click="scrollRow('row-' + title, 'right')" 
                            class="absolute right-0 top-0 bottom-0 z-40 bg-gradient-to-l from-black via-black/90 to-transparent w-16 flex items-center justify-center opacity-0 group-hover/slider:opacity-100 transition-all duration-500">
                        <div class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center">→</div>
                    </button>
                </div>
            </div>
        </main>

        <Transition name="modal">
            <div v-if="activeProduct" @click="closeQuickView" 
                 class="fixed inset-0 z-[100] bg-black/90 backdrop-blur-md flex items-center justify-center p-4">
                <div @click.stop class="bg-[#1a1a1a] rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto border border-gray-800">
                    <div class="relative h-[40vh] overflow-hidden">
                        <img :src="activeProduct.image" class="w-full h-full object-cover" />
                        <button @click="closeQuickView" class="absolute top-4 right-4 w-10 h-10 rounded-full bg-black/60 flex items-center justify-center hover:bg-red-600 transition-colors">
                            ✕
                        </button>
                    </div>
                    <div class="p-8 space-y-6">
                        <h2 class="text-4xl font-black text-white">{{ activeProduct.name }}</h2>
                        <p class="text-gray-400">{{ activeProduct.description }}</p>
                        <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-8 rounded-lg w-full">
                            Add to Cart - ₱{{ activeProduct.price }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <footer class="border-t border-gray-900 bg-black py-12 px-4 text-center text-gray-500">
            <p>&copy; 2025 {{ business.name }}. All rights reserved.</p>
        </footer>
    </div>
</template>

<style scoped>
/* Custom Scrollbar Hiding */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Animations */
@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-slide-up {
    animation: fadeSlideUp 0.8s ease-out forwards;
}

/* Modal Transitions */
.modal-enter-active,
.modal-leave-active {
    transition: all 0.3s ease;
}
.modal-enter-from,
.modal-leave-to {
    opacity: 0;
    transform: scale(0.95);
}
</style>