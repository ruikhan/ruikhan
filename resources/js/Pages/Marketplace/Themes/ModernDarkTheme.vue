<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

// 1. Updated Props to match your working structure (adding categories)
const props = defineProps({
    business: Object,
    products: Array,
    categories: Array
});

const searchQuery = ref('');
const activeCategory = ref('All');

// 2. FILTER LOGIC (Copied from your working basis)
const filteredProducts = computed(() => {
    let result = props.products || [];

    // Filter by Category
    if (activeCategory.value !== 'All') {
        result = result.filter(product => 
            product.category && product.category.toLowerCase() === activeCategory.value.toLowerCase()
        );
    }

    // Filter by Search Query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(product => 
            (product.name || '').toLowerCase().includes(query) || 
            (product.description || '').toLowerCase().includes(query)
        );
    }

    return result;
});

// 3. IMAGE LOGIC (Copied & Enhanced for robustness)
const getProductImage = (product) => {
    // A. Check if it's an Array (like in your working code)
    if (Array.isArray(product.images) && product.images.length > 0) {
        const img = product.images[0];
        // Ensure we handle relative paths vs full URLs
        return img.startsWith('http') ? img : `/storage/${img}`;
    }
    
    // B. Fallback: Check if 'image' exists as a single string (just in case)
    if (product.image) {
         return product.image.startsWith('http') ? product.image : `/storage/${product.image}`;
    }

    // C. Final Fallback
    return 'https://via.placeholder.com/400?text=No+Image';
};

// Helper for Business Logo/Banner
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
    <div class="min-h-screen bg-gray-50 font-sans text-gray-800">
        
        <nav class="bg-white sticky top-0 z-40 border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    
                    <div class="flex-shrink-0 flex items-center gap-3">
                        <img v-if="business?.logo" 
                             :src="getBusinessAsset(business.logo, 'logo')" 
                             class="h-10 w-10 rounded-full object-cover border border-gray-200" 
                        />
                        <Link :href="route('marketplace.index')" class="text-2xl font-bold text-gray-900 tracking-tight">
                            {{ business?.name || 'Store' }}
                        </Link>
                    </div>

                    <div class="hidden md:block flex-1 max-w-lg mx-8">
                        <div class="relative">
                            <input v-model="searchQuery" 
                                   type="text" 
                                   placeholder="Search products..." 
                                   class="w-full bg-gray-100 border-none rounded-full py-2 px-6 focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors" />
                            <span class="absolute right-4 top-2.5 text-gray-400">🔍</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-6">
                        <Link :href="route('cart.index')" class="relative text-gray-500 hover:text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="space-y-4 max-w-lg">
                    <span class="text-blue-600 font-semibold tracking-wide uppercase text-sm">Welcome</span>
                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight">
                        {{ business?.name || 'Our Store' }}
                    </h1>
                    <p class="text-lg text-gray-500">
                        Browse our collection of high-quality products.
                    </p>
                </div>
                <div class="w-full md:w-1/2 h-64 md:h-80 bg-gray-100 rounded-xl overflow-hidden shadow-sm">
                    <img :src="getBusinessAsset(business?.banner, 'banner')" 
                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-700" />
                </div>
            </div>
        </div>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col md:flex-row gap-8">
                
                <aside class="w-full md:w-64 flex-shrink-0 space-y-8">
                    <div>
                        <h3 class="font-bold text-gray-900 mb-4">Categories</h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li>
                                <button @click="activeCategory = 'All'" 
                                        :class="['text-left w-full hover:text-blue-600 transition', activeCategory === 'All' ? 'font-bold text-blue-600' : '']">
                                    All Products
                                </button>
                            </li>
                            <li v-for="cat in (categories || [])" :key="cat.value || cat">
                                <button @click="activeCategory = cat.value || cat" 
                                        :class="['text-left w-full hover:text-blue-600 transition', activeCategory === (cat.value || cat) ? 'font-bold text-blue-600' : '']">
                                    {{ cat.label || cat }}
                                </button>
                            </li>
                        </ul>
                    </div>
                </aside>

                <div class="flex-1">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900">{{ activeCategory }} Products</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        
                        <div v-if="filteredProducts.length === 0" class="col-span-full text-center py-12 text-gray-500">
                            No products found matching your search.
                        </div>

                        <div v-for="product in filteredProducts" :key="product.id" 
                             class="group bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            
                            <div class="aspect-square bg-gray-100 relative overflow-hidden">
                                <img :src="getProductImage(product)" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                     alt="Product Image" />
                                
                                <button class="absolute bottom-4 right-4 bg-white p-3 rounded-full shadow-md translate-y-12 group-hover:translate-y-0 transition-transform duration-300 hover:text-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                </button>
                            </div>

                            <div class="p-4">
                                <p class="text-sm text-gray-500 mb-1">{{ product.category || 'General' }}</p>
                                <h3 class="font-bold text-gray-900 mb-2 truncate">{{ product.name }}</h3>
                                <div class="flex items-center justify-between">
                                    <span class="text-blue-600 font-bold">₱{{ product.price }}</span>
                                    <div class="flex text-yellow-400 text-xs">★★★★★</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="bg-white border-t border-gray-200 mt-20 py-12">
            <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm">
                <p>&copy; 2025 {{ business?.name }}. Powered by Nexus.</p>
            </div>
        </footer>
    </div>
</template>