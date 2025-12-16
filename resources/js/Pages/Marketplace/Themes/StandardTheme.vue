<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    business: Object,
    products: Array,
    categories: Array
});

const getProductImage = (product) => {
    if (product && Array.isArray(product.images) && product.images.length > 0) {
        return product.images[0];
    }
    return 'https://images.unsplash.com/photo-1550989460-0adf9ea622e2?q=80&w=2000&auto=format&fit=crop';
};

const isScrolled = ref(false);
const activeProduct = ref(null);
const showMobileMenu = ref(false);
const currentHeroIndex = ref(0);
const isHeroTransitioning = ref(false);
const mouseX = ref(0);
const mouseY = ref(0);

const heroProducts = computed(() => (props.products ? props.products.slice(0, 3) : []));
const featuredProducts = computed(() => props.products ? props.products.slice(0, 6) : []);
const trendingProducts = computed(() => props.products ? props.products.slice(6, 12) : []);
const newArrivals = computed(() => props.products ? props.products.slice(12, 18) : []);
const allProducts = computed(() => props.products || []);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

const handleMouseMove = (e) => {
    mouseX.value = e.clientX / window.innerWidth;
    mouseY.value = e.clientY / window.innerHeight;
};

let heroInterval;
const startHeroCarousel = () => {
    heroInterval = setInterval(() => {
        nextHero();
    }, 6000);
};

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

const openQuickView = (product) => {
    activeProduct.value = product;
    document.body.style.overflow = 'hidden';
};

const closeQuickView = () => {
    activeProduct.value = null;
    document.body.style.overflow = 'auto';
};

const scrollToSection = (id) => {
    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth' });
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    window.addEventListener('mousemove', handleMouseMove);
    startHeroCarousel();
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
            }
        });
    }, observerOptions);

    setTimeout(() => {
        document.querySelectorAll('.scroll-reveal').forEach(el => {
            observer.observe(el);
        });
    }, 100);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('mousemove', handleMouseMove);
    if (heroInterval) clearInterval(heroInterval);
});

const currentHero = computed(() => heroProducts.value[currentHeroIndex.value] || {});
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-white font-sans overflow-x-hidden">
        
        <!-- Animated Background -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none opacity-30">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl animate-blob"></div>
            <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-1/4 left-1/2 w-96 h-96 bg-pink-500/20 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
        </div>

        <!-- Floating Navigation Bar -->
        <nav :class="['fixed top-0 left-1/2 -translate-x-1/2 z-50 transition-all duration-700 mt-4', isScrolled ? 'w-[95%] max-w-6xl' : 'w-[90%] max-w-7xl']">
            <div :class="['rounded-2xl px-6 py-4 flex items-center justify-between transition-all duration-500 border', isScrolled ? 'bg-slate-900/95 backdrop-blur-2xl border-slate-700/50 shadow-2xl' : 'bg-slate-900/40 backdrop-blur-xl border-slate-700/30']">
                
                <!-- Logo -->
                <Link :href="route('marketplace.index')" class="group flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <span class="text-xl font-black">{{ business.name.charAt(0) }}</span>
                    </div>
                    <span class="text-xl font-black bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent hidden md:block">
                        {{ business.name }}
                    </span>
                </Link>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center gap-8 text-sm font-medium">
                    <button @click="scrollToSection('featured')" class="text-gray-300 hover:text-white transition-colors relative group">
                        Featured
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-500 to-purple-500 group-hover:w-full transition-all duration-300"></span>
                    </button>
                    <button @click="scrollToSection('trending')" class="text-gray-300 hover:text-white transition-colors relative group">
                        Trending
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-500 to-purple-500 group-hover:w-full transition-all duration-300"></span>
                    </button>
                    <button @click="scrollToSection('new')" class="text-gray-300 hover:text-white transition-colors relative group">
                        New Arrivals
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-500 to-purple-500 group-hover:w-full transition-all duration-300"></span>
                    </button>
                    <button @click="scrollToSection('all')" class="text-gray-300 hover:text-white transition-colors relative group">
                        All Products
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-500 to-purple-500 group-hover:w-full transition-all duration-300"></span>
                    </button>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-4">
                    <button class="hidden md:flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800/50 hover:bg-slate-700/50 transition-all duration-300 border border-slate-700/50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <span class="text-sm text-gray-400">Search</span>
                    </button>
                    
                    <button class="p-2 hover:bg-slate-800/50 rounded-xl transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </button>
                    
                    <Link :href="route('cart.index')" class="relative p-2 hover:bg-slate-800/50 rounded-xl transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-xs font-bold">3</span>
                    </Link>

                    <button @click="showMobileMenu = !showMobileMenu" class="lg:hidden p-2 hover:bg-slate-800/50 rounded-xl transition-all duration-300">
                        <svg v-if="!showMobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <Transition name="mobile-menu">
            <div v-if="showMobileMenu" class="fixed inset-0 z-40 lg:hidden">
                <div class="absolute inset-0 bg-slate-950/95 backdrop-blur-2xl"></div>
                <div class="relative h-full flex flex-col justify-center items-center gap-8 text-2xl font-bold">
                    <button @click="scrollToSection('featured'); showMobileMenu = false" class="hover:text-blue-400 transition-colors">Featured</button>
                    <button @click="scrollToSection('trending'); showMobileMenu = false" class="hover:text-purple-400 transition-colors">Trending</button>
                    <button @click="scrollToSection('new'); showMobileMenu = false" class="hover:text-pink-400 transition-colors">New Arrivals</button>
                    <button @click="scrollToSection('all'); showMobileMenu = false" class="hover:text-blue-400 transition-colors">All Products</button>
                </div>
            </div>
        </Transition>

        <!-- Hero Section - Cinematic with Parallax -->
        <section class="relative min-h-screen flex items-center overflow-hidden">
            <!-- Background Images with Parallax -->
            <div v-for="(product, index) in heroProducts" :key="index" 
                :class="['absolute inset-0 transition-all duration-1000', currentHeroIndex === index ? 'opacity-100 scale-100' : 'opacity-0 scale-110']">
                <div class="absolute inset-0" :style="`transform: translate(${mouseX * 20}px, ${mouseY * 20}px)`">
                    <img :src="getProductImage(product)" class="w-full h-full object-cover scale-110" />
                </div>
                <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/80 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-slate-950/50"></div>
            </div>

            <!-- Hero Content -->
            <div class="relative z-10 w-full max-w-7xl mx-auto px-6 lg:px-12 py-32">
                <div class="max-w-3xl space-y-8">
                    
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-full border border-blue-500/30 backdrop-blur-xl">
                        <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                        <span class="text-sm font-semibold text-blue-300">PREMIUM COLLECTION</span>
                    </div>

                    <!-- Title -->
                    <h1 class="text-5xl md:text-7xl lg:text-8xl font-black leading-none">
                        <span class="block text-white mb-2">{{ currentHero.name || 'Discover' }}</span>
                        <span class="block bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                            Excellence
                        </span>
                    </h1>

                    <!-- Description -->
                    <p class="text-lg md:text-xl text-gray-300 leading-relaxed max-w-2xl">
                        {{ currentHero.description || 'Explore our curated collection of premium products designed for those who appreciate quality and innovation.' }}
                    </p>

                    <!-- Stats -->
                    <div class="flex flex-wrap gap-8 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="flex">
                                <svg v-for="i in 5" :key="i" class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            </div>
                            <span class="text-gray-400">4.9/5 Rating</span>
                        </div>
                        <div class="h-6 w-px bg-gray-700"></div>
                        <div class="text-gray-400">
                            <span class="font-bold text-white">₱{{ currentHero.price || '1,299' }}</span> Starting Price
                        </div>
                        <div class="h-6 w-px bg-gray-700"></div>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                            <span class="text-gray-400">In Stock</span>
                        </div>
                    </div>

                    <!-- CTAs -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <button class="group relative px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl font-bold overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-purple-500/50">
                            <span class="relative z-10 flex items-center justify-center gap-2">
                                Shop Now
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </span>
                        </button>
                        <button class="px-8 py-4 bg-slate-800/50 backdrop-blur-xl rounded-xl font-bold border border-slate-700/50 hover:bg-slate-700/50 transition-all duration-300 hover:scale-105">
                            Learn More
                        </button>
                    </div>
                </div>
            </div>

            <!-- Hero Navigation -->
            <div class="absolute bottom-12 left-0 right-0 z-20 flex items-center justify-between px-6 lg:px-12">
                <div class="flex gap-3">
                    <button @click="prevHero" class="w-12 h-12 rounded-full bg-slate-800/60 backdrop-blur-xl border border-slate-700/50 hover:bg-slate-700/60 flex items-center justify-center transition-all duration-300 hover:scale-110">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button @click="nextHero" class="w-12 h-12 rounded-full bg-slate-800/60 backdrop-blur-xl border border-slate-700/50 hover:bg-slate-700/60 flex items-center justify-center transition-all duration-300 hover:scale-110">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
                <div class="flex gap-2">
                    <button v-for="(_, index) in heroProducts" :key="index" 
                        @click="currentHeroIndex = index"
                        :class="['transition-all duration-500 rounded-full', currentHeroIndex === index ? 'w-12 h-2 bg-gradient-to-r from-blue-500 to-purple-500' : 'w-2 h-2 bg-slate-600 hover:bg-slate-500']">
                    </button>
                </div>
            </div>
        </section>

        <!-- Featured Products Grid -->
        <section id="featured" class="relative z-10 py-20 px-6 lg:px-12">
            <div class="max-w-7xl mx-auto">
                
                <!-- Section Header -->
                <div class="mb-12 scroll-reveal">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-1 h-8 bg-gradient-to-b from-blue-500 to-purple-500 rounded-full"></div>
                        <span class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Featured Collection</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-black mb-4">
                        <span class="bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent">
                            Handpicked For You
                        </span>
                    </h2>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="(product, index) in featuredProducts" :key="product.id"
                        @click="openQuickView(product)"
                        class="group scroll-reveal cursor-pointer"
                        :style="`animation-delay: ${index * 100}ms`">
                        
                        <div class="relative rounded-2xl overflow-hidden bg-gradient-to-br from-slate-900/50 to-slate-800/50 border border-slate-700/30 backdrop-blur-xl transition-all duration-500 hover:scale-[1.02] hover:border-blue-500/50 hover:shadow-2xl hover:shadow-blue-500/20">
                            
                            <!-- Image -->
                            <div class="relative aspect-square overflow-hidden">
                                <img :src="getProductImage(product)" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" :alt="product.name" />
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent opacity-60"></div>
                                
                                <!-- Quick Actions -->
                                <div class="absolute top-4 right-4 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                    <button class="w-10 h-10 bg-slate-900/80 backdrop-blur-xl rounded-full flex items-center justify-center hover:bg-blue-500 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                    </button>
                                </div>

                                <!-- Badge -->
                                <div class="absolute top-4 left-4">
                                    <span class="px-3 py-1.5 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full text-xs font-bold">
                                        NEW
                                    </span>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="p-6 space-y-4">
                                <div>
                                    <h3 class="font-bold text-lg mb-2 line-clamp-1 group-hover:text-blue-400 transition-colors">
                                        {{ product.name }}
                                    </h3>
                                    <p class="text-sm text-gray-400 line-clamp-2">
                                        {{ product.description || 'Premium quality product with innovative design and exceptional features.' }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-2">
                                    <div class="flex text-yellow-400">
                                        <svg v-for="i in 5" :key="i" class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    </div>
                                    <span class="text-sm text-gray-500">(248 reviews)</span>
                                </div>

                                <div class="flex items-center justify-between pt-2">
                                    <span class="text-2xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                                        ₱{{ product.price }}
                                    </span>
                                    <button class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl font-bold text-sm hover:scale-105 transition-transform duration-300 shadow-lg shadow-purple-500/30">
                                        Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Trending - Bento Grid -->
        <section id="trending" class="relative z-10 py-20 px-6 lg:px-12 bg-gradient-to-b from-transparent via-slate-900/30 to-transparent">
            <div class="max-w-7xl mx-auto">
                
                <div class="mb-12 scroll-reveal">
                    <div class="flex items-center gap-3 mb-4">
                        <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        <span class="text-sm font-semibold text-gray-400 uppercase tracking-wider">What's Hot</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-black bg-gradient-to-r from-pink-400 to-orange-400 bg-clip-text text-transparent">
                        Trending Now
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div v-for="(product, index) in trendingProducts" :key="product.id"
                        @click="openQuickView(product)"
                        :class="['group scroll-reveal relative rounded-2xl overflow-hidden cursor-pointer transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:shadow-pink-500/20', index === 0 ? 'md:col-span-2 md:row-span-2' : '']">
                        
                        <div :class="['relative overflow-hidden', index === 0 ? 'aspect-[4/3]' : 'aspect-square']">
                            <img :src="getProductImage(product)" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" :alt="product.name" />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/50 to-transparent"></div>
                        </div>

                        <div class="absolute inset-0 flex flex-col justify-end p-6">
                            <div class="mb-2">
                                <div class="inline-block px-3 py-1 bg-pink-500/20 backdrop-blur-xl rounded-full border border-pink-500/30 text-xs font-semibold text-pink-300 mb-3">
                                    🔥 Trending
                                </div>
                            </div>
                            <h3 class="font-bold text-lg mb-2 line-clamp-2">{{ product.name }}</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-xl font-black text-pink-400">₱{{ product.price }}</span>
                                <button class="px-4 py-2 bg-white/10 backdrop-blur-xl rounded-lg text-sm font-semibold hover:bg-white/20 transition-all border border-white/20">
                                    View
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- New Arrivals - Horizontal Scroll -->
        <section id="new" class="relative z-10 py-20">
            <div class="px-6 lg:px-12 mb-12">
                <div class="max-w-7xl mx-auto scroll-reveal">
                    <div class="flex items-center gap-3 mb-4">
                        <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <span class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Just Dropped</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-black bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">
                        New Arrivals
                    </h2>
                </div>
            </div>

            <div class="flex gap-6 px-6 lg:px-12 overflow-x-auto scrollbar-hide pb-6">
                <div v-for="product in newArrivals" :key="product.id"
                    @click="openQuickView(product)"
                    class="flex-none w-80 group cursor-pointer">
                    
                    <div class="relative rounded-2xl overflow-hidden mb-4 bg-gradient-to-br from-slate-900/50 to-slate-800/50 border border-slate-700/30 backdrop-blur-xl transition-all duration-500 hover:border-cyan-500/50 hover:shadow-2xl hover:shadow-cyan-500/20">
                        <img :src="getProductImage(product)" class="w-full h-96 object-cover transition-transform duration-700 group-hover:scale-110" :alt="product.name" />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent"></div>
                        
                        <div class="absolute bottom-0 left-0 right-0 p-6">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="flex text-yellow-400">
                                    <svg v-for="i in 5" :key="i" class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                </div>
                                <span class="text-sm font-semibold">4.8</span>
                            </div>
                            <h3 class="font-bold text-lg mb-2 line-clamp-2">{{ product.name }}</h3>
                            <span class="text-2xl font-black bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">
                                ₱{{ product.price }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- All Products -->
        <section id="all" class="relative z-10 py-20 px-6 lg:px-12">
            <div class="max-w-7xl mx-auto">
                
                <div class="mb-12 scroll-reveal">
                    <h2 class="text-4xl md:text-5xl font-black mb-4">
                        <span class="bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent">
                            All Products
                        </span>
                    </h2>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div v-for="product in allProducts" :key="product.id"
                        @click="openQuickView(product)"
                        class="group scroll-reveal cursor-pointer">
                        
                        <div class="relative rounded-xl overflow-hidden bg-slate-900/50 border border-slate-700/30 backdrop-blur-xl transition-all duration-300 hover:border-blue-500/50 hover:shadow-lg hover:shadow-blue-500/20">
                            <div class="aspect-square overflow-hidden">
                                <img :src="getProductImage(product)" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" :alt="product.name" />
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-sm mb-2 line-clamp-2 group-hover:text-blue-400 transition-colors">
                                    {{ product.name }}
                                </h3>
                                <span class="text-lg font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                                    ₱{{ product.price }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick View Modal -->
        <Transition name="modal">
            <div v-if="activeProduct" @click="closeQuickView" 
                class="fixed inset-0 z-[100] bg-slate-950/90 backdrop-blur-2xl flex items-center justify-center p-4">
                <div @click.stop class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl max-w-4xl w-full max-h-[90vh] overflow-y-auto border border-slate-700/50 shadow-2xl">
                    
                    <div class="relative h-96 overflow-hidden">
                        <img :src="getProductImage(activeProduct)" class="w-full h-full object-cover" :alt="activeProduct.name" />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent"></div>
                        <button @click="closeQuickView" class="absolute top-6 right-6 w-12 h-12 bg-slate-900/80 backdrop-blur-xl rounded-full flex items-center justify-center hover:bg-slate-800 transition-all hover:scale-110 border border-slate-700/50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-8 space-y-6">
                        <div>
                            <h2 class="text-4xl font-black mb-4">{{ activeProduct.name }}</h2>
                            <p class="text-gray-400 text-lg leading-relaxed">{{ activeProduct.description }}</p>
                        </div>

                        <div class="flex items-center gap-6">
                            <div class="flex items-center gap-2">
                                <div class="flex text-yellow-400">
                                    <svg v-for="i in 5" :key="i" class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                </div>
                                <span class="font-bold text-xl">4.8</span>
                                <span class="text-gray-400">(328 reviews)</span>
                            </div>
                            <span class="px-4 py-2 bg-green-500/20 text-green-400 rounded-full text-sm font-semibold border border-green-500/30">
                                In Stock
                            </span>
                        </div>

                        <div class="text-4xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                            ₱{{ activeProduct.price }}
                        </div>

                        <div class="flex gap-4">
                            <button class="flex-1 py-4 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl font-bold text-lg hover:scale-105 transition-transform shadow-lg shadow-purple-500/50">
                                Add to Cart
                            </button>
                            <button class="px-8 py-4 bg-slate-800/50 backdrop-blur-xl rounded-xl border border-slate-700/50 hover:bg-slate-700/50 transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-3 gap-4 pt-6 border-t border-slate-700/50">
                            <div class="flex flex-col items-center gap-2 p-4 bg-slate-800/30 rounded-xl border border-slate-700/30">
                                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                <span class="text-sm text-gray-400">Free Shipping</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 p-4 bg-slate-800/30 rounded-xl border border-slate-700/30">
                                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <span class="text-sm text-gray-400">Fast Delivery</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 p-4 bg-slate-800/30 rounded-xl border border-slate-700/30">
                                <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-sm text-gray-400">Quality Assured</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Footer -->
        <footer class="relative z-10 border-t border-slate-800 bg-gradient-to-b from-transparent to-slate-950 mt-20">
            <div class="max-w-7xl mx-auto px-6 lg:px-12 py-16">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                    <div class="space-y-4">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                                <span class="text-xl font-black">{{ business.name.charAt(0) }}</span>
                            </div>
                            <span class="text-xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                                {{ business.name }}
                            </span>
                        </div>
                        <p class="text-gray-400 text-sm">Premium products for those who demand excellence.</p>
                    </div>
                    <div v-for="section in [
                        { title: 'Shop', items: ['Featured', 'Trending', 'New', 'All Products'] },
                        { title: 'Company', items: ['About', 'Careers', 'Press', 'Blog'] },
                        { title: 'Support', items: ['Help', 'Contact', 'Shipping', 'Returns'] }
                    ]" :key="section.title">
                        <h4 class="font-bold mb-4">{{ section.title }}</h4>
                        <ul class="space-y-3">
                            <li v-for="item in section.items" :key="item">
                                <button class="text-gray-400 hover:text-white transition-colors text-sm">{{ item }}</button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-400">
                    <p>&copy; 2025 {{ business.name }}. All rights reserved.</p>
                    <div class="flex gap-6">
                        <button class="hover:text-white transition-colors">Privacy</button>
                        <button class="hover:text-white transition-colors">Terms</button>
                        <button class="hover:text-white transition-colors">Cookies</button>
                    </div>
                </div>
            </div>
        </footer>

    </div>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Animations */
@keyframes blob {
    0%, 100% { transform: translate(0, 0) scale(1); }
    25% { transform: translate(20px, -50px) scale(1.1); }
    50% { transform: translate(-20px, 20px) scale(0.9); }
    75% { transform: translate(50px, 50px) scale(1.05); }
}

.animate-blob {
    animation: blob 20s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

/* Scroll Reveal */
.scroll-reveal {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.8s, transform 0.8s;
}

.scroll-reveal.in-view {
    opacity: 1;
    transform: translateY(0);
}

/* Modal Transitions */
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

/* Mobile Menu Transitions */
.mobile-menu-enter-active,
.mobile-menu-leave-active {
    transition: opacity 0.3s;
}

.mobile-menu-enter-from,
.mobile-menu-leave-to {
    opacity: 0;
}
</style>