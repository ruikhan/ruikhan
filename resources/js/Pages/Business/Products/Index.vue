<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
  products: Object
});

const deleteProduct = (productId) => {
  if (confirm('Are you sure you want to delete this product?')) {
    router.delete(route('business.products.destroy', productId), {
      preserveScroll: true,
      onSuccess: () => {
        console.log('✅ Product deleted successfully');
      }
    });
  }
};

const toggleAvailability = (productId) => {
  router.patch(route('business.products.toggle', productId), {}, {
    preserveScroll: true,
    onSuccess: () => {
      console.log('✅ Product availability updated');
    }
  });
};

const getStatusColor = (isAvailable) => {
  return isAvailable 
    ? 'bg-green-500/20 text-green-300 border-green-500/30'
    : 'bg-gray-500/20 text-gray-300 border-gray-500/30';
};
</script>

<template>
  <Head title="My Products" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto space-y-6">
      
      <!-- Header Section -->
      <div class="relative overflow-hidden rounded-3xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 shadow-[0_20px_60px_-10px_rgba(0,0,0,0.5)]">
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-[0.03] mix-blend-overlay"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/20 via-purple-600/10 to-transparent opacity-50"></div>
        
        <div class="relative p-8">
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-br from-white via-gray-200 to-gray-400 bg-clip-text text-transparent mb-2">
                📦 My Products
              </h1>
              <p class="text-gray-400 text-sm">
                Manage your inventory and product listings
              </p>
            </div>
            <Link 
              :href="route('business.products.create')" 
              class="px-6 py-2.5 rounded-full bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-semibold text-sm shadow-[0_0_20px_rgba(168,85,247,0.4)] hover:shadow-[0_0_30px_rgba(168,85,247,0.6)] transition-all duration-300 active:scale-95 border border-purple-400/20"
            >
              + Add New Product
            </Link>
          </div>
        </div>
      </div>

      <!-- Stats Overview -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-6 shadow-lg">
          <p class="text-xs font-medium text-gray-400 mb-2">Total Products</p>
          <p class="text-3xl font-bold text-white">{{ products.total || 0 }}</p>
        </div>
        <div class="rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-6 shadow-lg">
          <p class="text-xs font-medium text-gray-400 mb-2">Available</p>
          <p class="text-3xl font-bold text-green-400">{{ products.data?.filter(p => p.is_available).length || 0 }}</p>
        </div>
        <div class="rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-6 shadow-lg">
          <p class="text-xs font-medium text-gray-400 mb-2">Unavailable</p>
          <p class="text-3xl font-bold text-gray-400">{{ products.data?.filter(p => !p.is_available).length || 0 }}</p>
        </div>
        <div class="rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-6 shadow-lg">
          <p class="text-xs font-medium text-gray-400 mb-2">Low Stock</p>
          <p class="text-3xl font-bold text-red-400">{{ products.data?.filter(p => p.stock < 10).length || 0 }}</p>
        </div>
      </div>

      <!-- Products Grid -->
      <div v-if="products.data && products.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="product in products.data" 
          :key="product.id"
          class="group relative overflow-hidden rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 shadow-lg hover:shadow-[0_0_30px_rgba(168,85,247,0.2)] hover:border-white/20 transition-all duration-300"
        >
          <!-- Product Image -->
          <div class="relative h-48 bg-gradient-to-br from-purple-500/20 to-blue-500/20 overflow-hidden">
            <img 
              v-if="product.images && product.images.length > 0"
              :src="`/storage/${product.images[0]}`" 
              :alt="product.name"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-6xl">
              📦
            </div>
            
            <!-- Status Badge -->
            <div class="absolute top-3 right-3">
              <span :class="`inline-flex px-3 py-1 text-xs font-semibold rounded-full border ${getStatusColor(product.is_available)}`">
                {{ product.is_available ? 'Available' : 'Unavailable' }}
              </span>
            </div>
          </div>

          <!-- Product Info -->
          <div class="p-6">
            <div class="mb-4">
              <h3 class="text-lg font-bold text-white mb-1 line-clamp-1">{{ product.name }}</h3>
              <p class="text-sm text-gray-400 line-clamp-2 mb-2">{{ product.description }}</p>
              
              <div class="flex items-center justify-between mb-3">
                <span class="text-2xl font-bold text-green-400">₱{{ parseFloat(product.price).toFixed(2) }}</span>
                <span :class="[
                  'text-sm font-medium',
                  product.stock < 10 ? 'text-red-400' : product.stock < 50 ? 'text-yellow-400' : 'text-green-400'
                ]">
                  Stock: {{ product.stock }}
                </span>
              </div>

              <div class="flex items-center gap-2 text-xs text-gray-400">
                <span class="px-2 py-1 rounded-full bg-white/5 border border-white/10">
                  {{ product.category || 'Uncategorized' }}
                </span>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
              <Link 
                :href="route('business.products.edit', product.id)"
                class="flex-1 px-4 py-2 rounded-lg bg-blue-500/20 text-blue-300 border border-blue-500/30 hover:bg-blue-500/30 transition-all duration-300 text-center text-sm font-medium"
              >
                Edit
              </Link>
              <button 
                @click="toggleAvailability(product.id)"
                class="flex-1 px-4 py-2 rounded-lg bg-purple-500/20 text-purple-300 border border-purple-500/30 hover:bg-purple-500/30 transition-all duration-300 text-sm font-medium"
              >
                {{ product.is_available ? 'Hide' : 'Show' }}
              </button>
              <button 
                @click="deleteProduct(product.id)"
                class="px-4 py-2 rounded-lg bg-red-500/20 text-red-300 border border-red-500/30 hover:bg-red-500/30 transition-all duration-300 text-sm font-medium"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-12 text-center shadow-lg">
        <div class="text-6xl mb-4">📦</div>
        <h3 class="text-xl font-bold text-white mb-2">No Products Yet</h3>
        <p class="text-gray-400 mb-6">Start building your inventory by adding your first product</p>
        <Link 
          :href="route('business.products.create')" 
          class="inline-flex px-6 py-3 rounded-full bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-semibold shadow-[0_0_20px_rgba(168,85,247,0.4)] transition-all duration-300 active:scale-95"
        >
          + Add Your First Product
        </Link>
      </div>

      <!-- Pagination -->
      <div v-if="products.data && products.data.length > 0 && (products.prev_page_url || products.next_page_url)" class="flex justify-center gap-2">
        <Link 
          v-if="products.prev_page_url"
          :href="products.prev_page_url"
          class="px-4 py-2 rounded-lg bg-white/5 text-gray-300 border border-white/10 hover:bg-white/10 hover:text-white transition-all"
        >
          ← Previous
        </Link>
        <span class="px-4 py-2 rounded-lg bg-white/10 text-white border border-white/20">
          Page {{ products.current_page }} of {{ products.last_page }}
        </span>
        <Link 
          v-if="products.next_page_url"
          :href="products.next_page_url"
          class="px-4 py-2 rounded-lg bg-white/5 text-gray-300 border border-white/10 hover:bg-white/10 hover:text-white transition-all"
        >
          Next →
        </Link>
      </div>

    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>