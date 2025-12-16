<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    business: Object,
    products: Array,
    categories: Array
});

// ✅ NEW: Helper function to safely get the first image or a placeholder
const getProductImage = (product) => {
    if (product && Array.isArray(product.images) && product.images.length > 0) {
        return product.images[0];
    }
    // Return a placeholder or a default image if none exists
    return 'https://images.unsplash.com/photo-1550989460-0adf9ea622e2?q=80&w=2000&auto=format&fit=crop';
};

const isScrolled = ref(false);
const activeProduct = ref(null);
const showMobileMenu = ref(false);
const currentHeroIndex = ref(0);

// Use a safety check for products in case the array is empty
const heroProducts = computed(() => (props.products ? props.products.slice(0, 3) : []));
const isHeroTransitioning = ref(false);

// Handle Navbar background on scroll with smooth transition
const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    startHeroCarousel();
    
    // Initialize Intersection Observer (Moved here for clarity)
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);

    // Wait for DOM update before selecting elements
    setTimeout(() => {
        document.querySelectorAll('.observe-scroll').forEach(el => {
            observer.observe(el);
        });
    }, 100);
});

let heroInterval;
const startHeroCarousel = () => {
    heroInterval = setInterval(() => {
        nextHero();
    }, 8000);
};

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    if (heroInterval) clearInterval(heroInterval);
});

const nextHero = () => {
    if (isHeroTransitioning.value) return;
    isHeroTransitioning.value = true;
    currentHeroIndex.value = (currentHeroIndex.value + 1) % heroProducts.value.length;
    setTimeout(() => {
        isHeroTransitioning.value = false;
    }, 800);
};

const prevHero = () => {
    if (isHeroTransitioning.value) return;
    isHeroTransitioning.value = true;
    currentHeroIndex.value = currentHeroIndex.value === 0 ? heroProducts.value.length - 1 : currentHeroIndex.value - 1;
    setTimeout(() => {
        isHeroTransitioning.value = false;
    }, 800);
};

const currentHero = computed(() => heroProducts.value[currentHeroIndex.value] || {});

// Smooth horizontal scroll with easing
const scrollRow = (id, direction) => {
    const row = document.getElementById(id);
    if (row) {
        const scrollAmount = window.innerWidth * 0.75;
        row.scrollBy({
            left: direction === 'right' ? scrollAmount : -scrollAmount,
            behavior: 'smooth'
        });
    }
};

// Product rows (Safety checks added)
const trendingRow = computed(() => props.products ? props.products.slice(0, 8) : []);
const newArrivalsRow = computed(() => props.products ? props.products.slice(3, 11) : []);
const popularRow = computed(() => props.products ? props.products.slice(6, 14) : []);
const allProductsRow = computed(() => props.products || []);

// Quick view modal
const openQuickView = (product) => {
    activeProduct.value = product;
    document.body.style.overflow = 'hidden';
};

const closeQuickView = () => {
    activeProduct.value = null;
    document.body.style.overflow = 'auto';
};
</script>

<template>
    <div class="min-h-screen bg-[#0a0a0a] text-white font-sans selection:bg-red-600 selection:text-white overflow-x-hidden">
        
        <!-- Enhanced Navigation -->
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
                    <button class="relative group py-2">
                        <span class="group-hover:text-white transition-colors duration-300">New & Popular</span>
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-white group-hover:w-full transition-all duration-300"></span>
                    </button>
                    <button class="relative group py-2">
                        <span class="group-hover:text-white transition-colors duration-300">My List</span>
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-white group-hover:w-full transition-all duration-300"></span>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-5 text-sm">
                <button class="hover:text-gray-300 transform hover:scale-110 transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
                <Link :href="route('cart.index')" class="hover:text-gray-300 relative transform hover:scale-110 transition-all duration-300 group">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold group-hover:scale-125 transition-transform duration-300">3</span>
                </Link>
                <div class="w-9 h-9 rounded bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center cursor-pointer transform hover:scale-110 transition-all duration-300 shadow-lg hover:shadow-blue-500/50">
                    <span class="text-sm">👤</span>
                </div>
                <button @click="showMobileMenu = !showMobileMenu" class="lg:hidden transform hover:scale-110 transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>
        </nav>

        <!-- Enhanced Hero Section with Carousel -->
        <header class="relative w-full h-[90vh] md:h-screen overflow-hidden">
            <!-- Hero Background Images -->
            <div v-for="(product, index) in heroProducts" :key="index" 
                :class="['absolute inset-0 transition-all duration-1000 ease-in-out', currentHeroIndex === index ? 'opacity-100 scale-100' : 'opacity-0 scale-105']">
                <img :src="getProductImage(product)" class="w-full h-full object-cover" />
                
                <!-- Enhanced Gradients -->
                <div class="absolute inset-0 bg-gradient-to-r from-[#0a0a0a] via-[#0a0a0a]/70 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-[#0a0a0a] via-transparent to-transparent"></div>
                <div class="absolute inset-0 bg-[#0a0a0a]/20"></div>
            </div>

            <!-- Hero Content -->
            <div class="absolute top-[25%] md:top-[30%] left-4 md:left-12 max-w-3xl z-10 space-y-6 animate-fade-slide-up">
                
                <!-- Enhanced Badge -->
                <div class="flex items-center gap-3 mb-3">
                    <span class="bg-gradient-to-r from-red-600 to-red-500 text-white text-xs font-bold px-3 py-1 rounded-md shadow-lg shadow-red-600/50 animate-pulse-slow">
                        FEATURED
                    </span>
                    <span class="text-gray-300 text-sm tracking-widest uppercase font-semibold">Premium Selection</span>
                </div>

                <!-- Animated Title -->
                <h1 class="text-5xl md:text-8xl font-black tracking-tight leading-none text-white animate-title-reveal">
                    <span class="bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent">
                        {{ currentHero.name || 'Premium Collection' }}
                    </span>
                </h1>
                
                <!-- Enhanced Description -->
                <p class="text-base md:text-xl text-gray-200 leading-relaxed max-w-2xl drop-shadow-2xl line-clamp-3">
                    {{ currentHero.description || 'Experience the finest quality products curated specifically for your taste. Order now for exclusive deals and premium benefits.' }}
                </p>

                <!-- Enhanced Action Buttons -->
                <div class="flex flex-wrap gap-4 pt-6">
                    <button class="group relative flex items-center gap-3 bg-white text-black px-8 py-3.5 rounded-md font-bold hover:bg-gray-100 transition-all duration-300 text-lg shadow-2xl hover:shadow-white/20 hover:scale-105 transform overflow-hidden">
                        <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></span>
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform duration-300" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        Buy Now
                    </button>
                    <button class="group flex items-center gap-3 bg-gray-800/60 backdrop-blur-md text-white px-8 py-3.5 rounded-md font-bold hover:bg-gray-700/60 transition-all duration-300 text-lg border border-gray-600/50 hover:border-gray-500 hover:scale-105 transform shadow-2xl">
                        <svg class="w-6 h-6 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        More Info
                    </button>
                </div>

                <!-- Hero Stats -->
                <div class="flex gap-6 pt-4 text-sm text-gray-400">
                    <div class="flex items-center gap-2">
                        <span class="text-green-400 font-bold">★ 4.8</span>
                        <span>Rating</span>
                    </div>
                    <div class="h-4 w-px bg-gray-600"></div>
                    <div>
                        <span class="font-bold text-white">₱{{ currentHero.price || '999' }}</span>
                    </div>
                    <div class="h-4 w-px bg-gray-600"></div>
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <span>Bestseller</span>
                    </div>
                </div>
            </div>

            <!-- Carousel Controls -->
            <div class="absolute bottom-12 left-4 md:left-12 z-20 flex gap-3">
                <button @click="prevHero" class="group w-12 h-12 rounded-full bg-gray-800/60 backdrop-blur-md hover:bg-gray-700/80 flex items-center justify-center transition-all duration-300 border border-gray-600/50 hover:border-gray-500 hover:scale-110 transform">
                    <svg class="w-6 h-6 group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button @click="nextHero" class="group w-12 h-12 rounded-full bg-gray-800/60 backdrop-blur-md hover:bg-gray-700/80 flex items-center justify-center transition-all duration-300 border border-gray-600/50 hover:border-gray-500 hover:scale-110 transform">
                    <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>

            <!-- Carousel Indicators -->
            <div class="absolute bottom-12 right-4 md:right-12 z-20 flex gap-2">
                <button v-for="(_, index) in heroProducts" :key="index" 
                        @click="currentHeroIndex = index"
                        :class="['h-1 transition-all duration-500 rounded-full', currentHeroIndex === index ? 'w-12 bg-red-600' : 'w-8 bg-gray-600 hover:bg-gray-500']">
                </button>
            </div>

            <!-- Scroll Indicator -->
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20 animate-bounce-slow hidden md:block">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </div>
        </header>

        <!-- Enhanced Product Rows -->
        <main class="relative z-20 -mt-24 pb-20 space-y-16 overflow-hidden">
            
            <div v-for="(row, title) in {'Trending Now': trendingRow, 'New Arrivals': newArrivalsRow, 'Popular Picks': popularRow, 'All Products': allProductsRow}" 
                 :key="title" 
                 class="space-y-4 group observe-scroll opacity-0 translate-y-8 transition-all duration-1000 pl-4 md:pl-12">
                
                <!-- Enhanced Row Header -->
                <div class="flex items-center justify-between pr-4 md:pr-12">
                    <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent group-hover:from-white group-hover:to-white transition-all duration-500 cursor-pointer flex items-center gap-3">
                        {{ title }}
                        <span class="text-sm text-cyan-400 opacity-0 group-hover:opacity-100 transition-all duration-500 translate-x-[-10px] group-hover:translate-x-0 font-normal">
                            Explore All →
                        </span>
                    </h2>
                </div>

                <!-- Enhanced Scrollable Row -->
                <div class="relative group/slider">
                    <!-- Left Scroll Button -->
                    <button @click="scrollRow('row-' + title, 'left')" 
                            class="absolute left-0 top-0 bottom-0 z-40 bg-gradient-to-r from-black via-black/90 to-transparent w-16 md:w-20 hover:from-black hover:via-black flex items-center justify-center opacity-0 group-hover/slider:opacity-100 transition-all duration-500">
                        <div class="w-12 h-12 rounded-full bg-gray-800/80 backdrop-blur-sm flex items-center justify-center transform hover:scale-110 transition-all duration-300 border border-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                        </div>
                    </button>

                    <div :id="'row-' + title" class="flex gap-3 overflow-x-auto scrollbar-hide py-6 pr-12 scroll-smooth">
                        
                        <!-- Enhanced Product Card -->
                       <div v-for="(product, index) in row" :key="product.id" 
                            @click="openQuickView(product)"
                            :style="`animation-delay: ${index * 50}ms`"
                            class="flex-none w-[180px] md:w-[280px] aspect-video relative rounded-lg overflow-hidden cursor-pointer transition-all duration-500 hover:z-50 md:hover:scale-110 origin-center group/card shadow-2xl hover:shadow-red-600/20 card-fade-in">
                            
                            <!-- Product Image -->
                            <img :src="getProductImage(product)" 
                            class="w-full h-full object-cover transition-transform duration-700 group-hover/card:scale-110" />
                            
                            <!-- Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-60 group-hover/card:opacity-80 transition-opacity duration-300"></div>
                            
                            <!-- Quick Info (Always Visible on Mobile) -->
                            <div class="absolute bottom-0 left-0 right-0 p-4 md:opacity-0 md:group-hover/card:opacity-100 transition-opacity duration-300">
                                <p class="text-white text-sm font-bold truncate mb-1">{{ product.name }}</p>
                                <p class="text-green-400 font-bold text-xs">₱{{ product.price }}</p>
                            </div>

                            <!-- Enhanced Hover Card (Desktop Only) -->
                            <div class="hidden md:block absolute inset-0 bg-gradient-to-b from-[#1a1a1a] to-[#0a0a0a] rounded-lg shadow-2xl opacity-0 group-hover/card:opacity-100 transition-all duration-500 p-4 flex flex-col justify-between border border-gray-800 transform scale-95 group-hover/card:scale-100">
                                
                                <!-- Action Buttons -->
                                <div class="flex items-center gap-2">
                                    <button class="w-9 h-9 bg-white text-black rounded-full flex items-center justify-center hover:bg-gray-200 transition-all duration-300 shadow-lg hover:scale-110 transform group/btn">
                                        <svg class="w-5 h-5 group-hover/btn:scale-110 transition-transform duration-300" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </button>
                                    <button class="w-9 h-9 border-2 border-gray-500 rounded-full flex items-center justify-center hover:border-white hover:bg-white/10 transition-all duration-300 hover:scale-110 transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </button>
                                    <button class="w-9 h-9 border-2 border-gray-500 rounded-full flex items-center justify-center hover:border-white hover:bg-white/10 transition-all duration-300 ml-auto hover:scale-110 transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                    </button>
                                </div>

                                <!-- Product Details -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-green-400 font-bold text-sm">95% Match</span>
                                        <span class="border border-gray-600 px-2 py-0.5 rounded text-xs text-gray-300">HD</span>
                                        <span class="text-gray-400 text-xs">2024</span>
                                    </div>
                                    <p class="text-white text-base font-bold line-clamp-2 leading-tight">{{ product.name }}</p>
                                    <p class="text-gray-400 text-xs line-clamp-2">{{ product.description || 'Premium quality product with excellent features' }}</p>
                                    <div class="flex items-center gap-2 pt-1">
                                        <span class="text-yellow-400 text-sm">★★★★★</span>
                                        <span class="text-gray-500 text-xs">(128 reviews)</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Scroll Button -->
                    <button @click="scrollRow('row-' + title, 'right')" 
                            class="absolute right-0 top-0 bottom-0 z-40 bg-gradient-to-l from-black via-black/90 to-transparent w-16 md:w-20 hover:from-black hover:via-black flex items-center justify-center opacity-0 group-hover/slider:opacity-100 transition-all duration-500">
                        <div class="w-12 h-12 rounded-full bg-gray-800/80 backdrop-blur-sm flex items-center justify-center transform hover:scale-110 transition-all duration-300 border border-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </button>
                </div>
            </div>
            
        </main>

        <!-- Quick View Modal -->
        <Transition name="modal">
            <div v-if="activeProduct" @click="closeQuickView" 
                 class="fixed inset-0 z-[100] bg-black/90 backdrop-blur-md flex items-center justify-center p-4 animate-fade-in">
                <div @click.stop class="bg-gradient-to-b from-[#1a1a1a] to-[#0a0a0a] rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-gray-800 animate-scale-in">
                    
                    <!-- Modal Header Image -->
                    <div class="relative h-[40vh] overflow-hidden">
                        <img :src="getProductImage(activeProduct)" class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#0a0a0a] via-transparent to-transparent"></div>
                        <button @click="closeQuickView" class="absolute top-4 right-4 w-10 h-10 rounded-full bg-black/60 backdrop-blur-sm flex items-center justify-center hover:bg-black/80 transition-all duration-300 hover:scale-110 transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Modal Content -->
                    <div class="p-8 space-y-6">
                        <div>
                            <h2 class="text-4xl font-black text-white mb-2">{{ activeProduct.name }}</h2>
                            <p class="text-gray-400 text-lg">{{ activeProduct.description }}</p>
                        </div>

                        <div class="flex items-center gap-6 text-sm">
                            <span class="text-green-400 font-bold text-2xl">₱{{ activeProduct.price }}</span>
                            <div class="flex items-center gap-1">
                                <span class="text-yellow-400">★★★★★</span>
                                <span class="text-gray-500">(4.8)</span>
                            </div>
                            <span class="bg-gray-800 px-3 py-1 rounded-full text-gray-300">In Stock</span>
                        </div>

                        <div class="flex gap-4">
                            <button class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-lg transition-all duration-300 hover:scale-105 transform shadow-lg shadow-red-600/50">
                                Add to Cart
                            </button>
                            <button class="px-6 bg-gray-800 hover:bg-gray-700 text-white font-bold py-4 rounded-lg transition-all duration-300 hover:scale-105 transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            </button>
                        </div>

                        <div class="border-t border-gray-800 pt-6 space-y-4">
                            <div>
                                <h4 class="text-gray-400 text-sm font-semibold mb-2">Features</h4>
                                <ul class="space-y-2 text-gray-300">
                                    <li class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        Premium Quality Materials
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        Fast & Free Shipping
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        30-Day Money Back Guarantee
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Enhanced Footer -->
        <footer class="border-t border-gray-900 bg-gradient-to-b from-transparent to-black/50 mt-20">
            <div class="max-w-7xl mx-auto py-16 px-4 md:px-12">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-12 mb-12 text-gray-400 text-sm">
                    <div class="space-y-4">
                        <h4 class="text-white font-bold text-base mb-4">Company</h4>
                        <ul class="space-y-3">
                            <li class="hover:text-white transition-colors duration-300 cursor-pointer">About Us</li>
                            <li class="hover:text-white transition-colors duration-300 cursor-pointer">Careers</li>
                            <li class="hover:text-white transition-colors duration-300 cursor-pointer">Press</li>
                        </ul>
                    </div>
                    <div class="space-y-4">
                        <h4 class="text-white font-bold text-base mb-4">Support</h4>
                        <ul class="space-y-3">
                            <li class="hover:text-white transition-colors duration-300 cursor-pointer">Help Center</li>
                            <li class="hover:text-white transition-colors duration-300 cursor-pointer">Contact Us</li>
                            <li class="hover:text-white transition-colors duration-300 cursor-pointer">FAQ</li>
                        </ul>
                    </div>
                    <div class="space-y-4">
                        <h4 class="text-white font-bold text-base mb-4">Legal</h4>
                        <ul class="space-y-3">
                            <li class="hover:text-white transition-colors duration-300 cursor-pointer">Privacy Policy</li>
                            <li class="hover:text-white transition-colors duration-300 cursor-pointer">Terms of Use</li>
                            <li class="hover:text-white transition-colors duration-300 cursor-pointer">Cookie Policy</li>
                        </ul>
                    </div>
                    <div class="space-y-4">
                        <h4 class="text-white font-bold text-base mb-4">Connect</h4>
                        <div class="flex gap-4">
                            <button class="w-10 h-10 rounded-full bg-gray-800 hover:bg-gray-700 flex items-center justify-center transition-all duration-300 hover:scale-110 transform">
                                <span>📘</span>
                            </button>
                            <button class="w-10 h-10 rounded-full bg-gray-800 hover:bg-gray-700 flex items-center justify-center transition-all duration-300 hover:scale-110 transform">
                                <span>📸</span>
                            </button>
                            <button class="w-10 h-10 rounded-full bg-gray-800 hover:bg-gray-700 flex items-center justify-center transition-all duration-300 hover:scale-110 transform">
                                <span>🐦</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-900 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-gray-500 text-sm">
                    <p>&copy; 2025 {{ business.name }}. All rights reserved.</p>
                    <div class="flex gap-6">
                        <button class="hover:text-white transition-colors duration-300">Language</button>
                        <button class="hover:text-white transition-colors duration-300">Region</button>
                    </div>
                </div>
            </div>
        </footer>

    </div>
</template>

<style scoped>
/* Hide Scrollbar */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Smooth Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeSlideUp {
    from { 
        opacity: 0; 
        transform: translateY(40px);
    }
    to { 
        opacity: 1; 
        transform: translateY(0);
    }
}

@keyframes scaleIn {
    from { 
        opacity: 0; 
        transform: scale(0.9);
    }
    to { 
        opacity: 1; 
        transform: scale(1);
    }
}

@keyframes titleReveal {
    from {
        opacity: 0;
        transform: translateY(30px);
        filter: blur(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
        filter: blur(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.6s ease-out;
}

.animate-fade-slide-up {
    animation: fadeSlideUp 1s ease-out;
}

.animate-scale-in {
    animation: scaleIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.animate-title-reveal {
    animation: titleReveal 1.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.animate-pulse-slow {
    animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animate-bounce-slow {
    animation: bounce 3s infinite;
}

/* Scroll Animation */
.observe-scroll {
    transition: opacity 1s, transform 1s;
}

.observe-scroll.animate-in {
    opacity: 1 !important;
    transform: translateY(0) !important;
}

/* Product Card Stagger Animation */
.card-fade-in {
    animation: fadeSlideUp 0.6s ease-out backwards;
}

/* Modal Transitions */
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .animate-scale-in,
.modal-leave-active .animate-scale-in {
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.modal-enter-from .animate-scale-in,
.modal-leave-to .animate-scale-in {
    transform: scale(0.9);
}
</style>