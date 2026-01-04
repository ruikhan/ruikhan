<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    business: Object,
    products: Array,
    categories: Array
});

const searchQuery = ref('');
const activeCategory = ref('All');
const hoveredProduct = ref(null);

const filteredProducts = computed(() => {
    let result = props.products || [];

    if (activeCategory.value !== 'All') {
        result = result.filter(product => 
            product.category && product.category.toLowerCase() === activeCategory.value.toLowerCase()
        );
    }

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(product => 
            (product.name || '').toLowerCase().includes(query) || 
            (product.description || '').toLowerCase().includes(query)
        );
    }

    return result;
});

const getProductImage = (product) => {
    if (Array.isArray(product.images) && product.images.length > 0) {
        const img = product.images[0];
        return img.startsWith('http') ? img : `/storage/${img}`;
    }
    if (product.image) {
        return product.image.startsWith('http') ? product.image : `/storage/${product.image}`;
    }
    return 'https://via.placeholder.com/400?text=No+Image';
};

const getBusinessAsset = (path, type) => {
    if (!path) {
        return type === 'banner' 
            ? 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=1000&auto=format&fit=crop'
            : 'https://via.placeholder.com/150';
    }
    return path.startsWith('http') ? path : `/storage/${path}`;
};
</script>

<template>
    <div class="min-h-screen bg-[#fafafa] font-sans text-gray-900 transition-colors duration-500">
        
        <!-- Premium Navigation -->
        <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-2xl border-b border-gray-200/50 shadow-sm transition-all duration-500">
            <div class="max-w-[1400px] mx-auto px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    
                    <!-- Logo -->
                    <div class="flex items-center gap-4 flex-shrink-0">
                        <div v-if="business?.logo" class="relative group">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-full blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <img :src="getBusinessAsset(business.logo, 'logo')" 
                                 class="relative h-9 w-9 rounded-full object-cover border border-gray-200 transform group-hover:scale-110 transition-transform duration-300 shadow-sm" 
                            />
                        </div>
                        <Link :href="route('marketplace.index')" class="text-xl font-semibold text-gray-900 tracking-tight hover:text-blue-600 transition-colors duration-300">
                            {{ business?.name || 'Store' }}
                        </Link>
                    </div>

                    <!-- Search Bar -->
                    <div class="hidden md:block flex-1 max-w-xl mx-12">
                        <div class="relative group">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-purple-500/10 rounded-2xl blur-xl opacity-0 group-focus-within:opacity-100 transition-opacity duration-500"></div>
                            <input v-model="searchQuery" 
                                   type="text" 
                                   placeholder="Search products..." 
                                   class="relative w-full bg-gray-100 border-none rounded-2xl py-2.5 px-5 pr-12 text-sm focus:ring-2 focus:ring-blue-500/30 focus:bg-white transition-all duration-300 shadow-sm placeholder:text-gray-400" />
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 transition-transform group-focus-within:scale-110 duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Cart Button -->
                    <div class="flex items-center gap-4">
                        <Link :href="route('cart.index')" class="relative group p-2 rounded-xl hover:bg-gray-100 transition-all duration-300">
                            <svg class="w-6 h-6 text-gray-600 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Banner -->
        <div class="relative bg-white border-b border-gray-200/50 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 opacity-50"></div>
            <div class="max-w-[1400px] mx-auto px-6 lg:px-8 py-20 lg:py-28 flex flex-col lg:flex-row items-center justify-between gap-12 relative">
                
                <!-- Hero Content -->
                <div class="space-y-6 max-w-xl z-10">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 rounded-full text-blue-700 text-sm font-semibold animate-fade-in">
                        <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                        New Collection
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-bold text-gray-900 leading-tight tracking-tight animate-slide-up">
                        {{ business?.name || 'Welcome' }}
                    </h1>
                    <p class="text-xl text-gray-600 leading-relaxed animate-slide-up" style="animation-delay: 0.1s">
                        Discover our curated collection of premium products designed for those who appreciate quality.
                    </p>
                    <div class="flex flex-wrap gap-4 animate-slide-up" style="animation-delay: 0.2s">
                        <button class="group relative px-8 py-3.5 bg-black text-white rounded-xl font-semibold overflow-hidden hover:scale-105 transition-all duration-300 shadow-lg shadow-black/10">
                            <span class="relative z-10">Shop Now</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        </button>
                        <button class="px-8 py-3.5 bg-gray-100 text-gray-900 rounded-xl font-semibold hover:bg-gray-200 hover:scale-105 transition-all duration-300">
                            Learn More
                        </button>
                    </div>
                </div>

                <!-- Hero Image -->
                <div class="w-full lg:w-1/2 h-80 lg:h-96 relative group animate-fade-in" style="animation-delay: 0.3s">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-400/20 to-purple-400/20 rounded-3xl blur-2xl group-hover:blur-3xl transition-all duration-700"></div>
                    <div class="relative h-full bg-white rounded-3xl overflow-hidden shadow-2xl transform group-hover:scale-105 transition-all duration-700 border border-gray-200/50">
                        <img :src="getBusinessAsset(business?.banner, 'banner')" 
                             class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="max-w-[1400px] mx-auto px-6 lg:px-8 py-16">
            <div class="flex flex-col lg:flex-row gap-12">
                
                <!-- Sidebar -->
                <aside class="w-full lg:w-72 flex-shrink-0 space-y-8">
                    <!-- Categories -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/50 hover:shadow-md transition-all duration-300">
                        <h3 class="font-bold text-gray-900 mb-6 text-lg">Categories</h3>
                        <ul class="space-y-2">
                            <li>
                                <button @click="activeCategory = 'All'" 
                                        :class="['w-full text-left px-4 py-3 rounded-xl transition-all duration-300', 
                                                activeCategory === 'All' 
                                                    ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold shadow-lg shadow-blue-500/20' 
                                                    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900']">
                                    All Products
                                </button>
                            </li>
                            <li v-for="cat in (categories || [])" :key="cat.value || cat">
                                <button @click="activeCategory = cat.value || cat" 
                                        :class="['w-full text-left px-4 py-3 rounded-xl transition-all duration-300', 
                                                activeCategory === (cat.value || cat) 
                                                    ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold shadow-lg shadow-blue-500/20' 
                                                    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900']">
                                    {{ cat.label || cat }}
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Filters -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/50 hover:shadow-md transition-all duration-300">
                        <h3 class="font-bold text-gray-900 mb-6 text-lg">Price Range</h3>
                        <div class="space-y-4">
                            <div class="relative">
                                <input type="range" min="0" max="10000" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>₱0</span>
                                <span>₱10,000+</span>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Products Grid -->
                <div class="flex-1">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ activeCategory }} Products</h2>
                            <p class="text-gray-600">{{ filteredProducts.length }} items available</p>
                        </div>
                        <select class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-600 focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500/50 transition-all shadow-sm cursor-pointer hover:border-gray-300">
                            <option>Sort by: Featured</option>
                            <option>Price: Low to High</option>
                            <option>Price: High to Low</option>
                            <option>Newest</option>
                        </select>
                    </div>

                    <!-- Empty State -->
                    <div v-if="filteredProducts.length === 0" class="flex flex-col items-center justify-center py-20 bg-white rounded-2xl shadow-sm border border-gray-200/50">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">No products found</h3>
                        <p class="text-gray-600 text-center max-w-md">
                            Try adjusting your search or filters to find what you're looking for.
                        </p>
                    </div>

                    <!-- Products Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="(product, index) in filteredProducts" :key="product.id" 
                             @mouseenter="hoveredProduct = product.id"
                             @mouseleave="hoveredProduct = null"
                             class="group bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-200/50 hover:shadow-xl hover:border-gray-300/50 transition-all duration-500 animate-fade-in-up"
                             :style="{ animationDelay: `${index * 0.05}s` }">
                            
                            <!-- Product Image -->
                            <div class="aspect-square bg-gray-50 relative overflow-hidden">
                                <img :src="getProductImage(product)" 
                                     class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700" 
                                     alt="Product" />
                                
                                <!-- Overlay Actions -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                                    <div class="absolute bottom-4 left-4 right-4 flex gap-2">
                                        <button class="flex-1 bg-white text-gray-900 py-3 rounded-xl font-semibold hover:bg-gray-100 transition-all duration-300 flex items-center justify-center gap-2 shadow-lg transform hover:scale-105">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                            </svg>
                                            Add
                                        </button>
                                        <button class="bg-white/10 backdrop-blur-md text-white p-3 rounded-xl hover:bg-white/20 transition-all duration-300 transform hover:scale-110">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Badge -->
                                <div v-if="product.is_new" class="absolute top-4 left-4 bg-blue-600 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                                    NEW
                                </div>
                            </div>

                            <!-- Product Info -->
                            <div class="p-5 space-y-3">
                                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">{{ product.category || 'General' }}</p>
                                <h3 class="font-semibold text-gray-900 text-lg leading-tight line-clamp-2 group-hover:text-blue-600 transition-colors duration-300">
                                    {{ product.name }}
                                </h3>
                                <div class="flex items-center justify-between pt-2">
                                    <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                        ₱{{ product.price }}
                                    </span>
                                    <div class="flex text-yellow-400 text-sm">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Premium Footer -->
        <footer class="bg-white border-t border-gray-200/50 mt-24">
            <div class="max-w-[1400px] mx-auto px-6 lg:px-8 py-16">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                    <div class="space-y-4">
                        <h4 class="font-bold text-gray-900 text-lg">Shop</h4>
                        <ul class="space-y-3 text-gray-600">
                            <li class="hover:text-blue-600 transition-colors cursor-pointer">All Products</li>
                            <li class="hover:text-blue-600 transition-colors cursor-pointer">New Arrivals</li>
                            <li class="hover:text-blue-600 transition-colors cursor-pointer">Best Sellers</li>
                        </ul>
                    </div>
                    <div class="space-y-4">
                        <h4 class="font-bold text-gray-900 text-lg">Support</h4>
                        <ul class="space-y-3 text-gray-600">
                            <li class="hover:text-blue-600 transition-colors cursor-pointer">Help Center</li>
                            <li class="hover:text-blue-600 transition-colors cursor-pointer">Contact Us</li>
                            <li class="hover:text-blue-600 transition-colors cursor-pointer">FAQ</li>
                        </ul>
                    </div>
                    <div class="space-y-4">
                        <h4 class="font-bold text-gray-900 text-lg">Company</h4>
                        <ul class="space-y-3 text-gray-600">
                            <li class="hover:text-blue-600 transition-colors cursor-pointer">About Us</li>
                            <li class="hover:text-blue-600 transition-colors cursor-pointer">Careers</li>
                            <li class="hover:text-blue-600 transition-colors cursor-pointer">Press</li>
                        </ul>
                    </div>
                    <div class="space-y-4">
                        <h4 class="font-bold text-gray-900 text-lg">Newsletter</h4>
                        <p class="text-gray-600 text-sm">Subscribe for exclusive offers</p>
                        <div class="flex gap-2">
                            <input type="email" placeholder="Enter your email" class="flex-1 px-4 py-2.5 bg-gray-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/30">
                            <button class="px-6 py-2.5 bg-black text-white rounded-xl font-semibold hover:bg-gray-800 transition-all">
                                Subscribe
                            </button>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-200 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-gray-600 text-sm">
                    <p>&copy; 2025 {{ business?.name }}. All rights reserved.</p>
                    <div class="flex gap-6">
                        <button class="hover:text-blue-600 transition-colors">Privacy Policy</button>
                        <button class="hover:text-blue-600 transition-colors">Terms of Service</button>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slide-up {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in { animation: fade-in 0.8s ease-out; }
.animate-slide-up { animation: slide-up 0.8s ease-out forwards; }
.animate-fade-in-up { animation: fade-in-up 0.6s ease-out forwards; }

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>