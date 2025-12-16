<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    product: Object,
    categories: Array
});

// Initialize form with existing product data
const form = useForm({
    name: props.product.name,
    description: props.product.description,
    price: props.product.price,
    stock: props.product.stock,
    category: props.product.category,
    is_available: Boolean(props.product.is_available),
    _method: 'PATCH', // Important for Laravel file uploads on update
});

const submit = () => {
    form.post(route('business.products.update', props.product.id), {
        onSuccess: () => console.log('✅ Product updated'),
    });
};

// Preview helper (for existing images)
const getImageUrl = (path) => {
    return path.startsWith('http') ? path : `/storage/${path}`;
};
</script>

<template>
    <Head title="Edit Product" />

    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">Edit Product</h1>
                    <p class="text-gray-400 text-sm">Update details for {{ product.name }}</p>
                </div>
                <Link 
                    :href="route('business.products.index')" 
                    class="px-4 py-2 rounded-lg bg-white/5 text-gray-300 border border-white/10 hover:bg-white/10 transition-colors"
                >
                    &larr; Back to List
                </Link>
            </div>

            <div class="rounded-3xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-8 shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                
                <form @submit.prevent="submit" class="relative space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-300">Product Name</label>
                            <input 
                                v-model="form.name"
                                type="text" 
                                class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all outline-none"
                                placeholder="e.g. Premium Coffee Beans"
                            />
                            <p v-if="form.errors.name" class="text-red-400 text-xs">{{ form.errors.name }}</p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-300">Category</label>
                            <select 
                                v-model="form.category"
                                class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all outline-none appearance-none"
                            >
                                <option value="" disabled>Select a category</option>
                                <option v-for="cat in categories" :key="cat.value" :value="cat.value">
                                    {{ cat.label }}
                                </option>
                            </select>
                            <p v-if="form.errors.category" class="text-red-400 text-xs">{{ form.errors.category }}</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-300">Description</label>
                        <textarea 
                            v-model="form.description"
                            rows="4"
                            class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all outline-none resize-none"
                            placeholder="Describe your product..."
                        ></textarea>
                        <p v-if="form.errors.description" class="text-red-400 text-xs">{{ form.errors.description }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-300">Price (₱)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3 text-gray-500">₱</span>
                                <input 
                                    v-model="form.price"
                                    type="number" 
                                    step="0.01"
                                    class="w-full bg-black/20 border border-white/10 rounded-xl pl-8 pr-4 py-3 text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all outline-none"
                                />
                            </div>
                            <p v-if="form.errors.price" class="text-red-400 text-xs">{{ form.errors.price }}</p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-300">Stock Quantity</label>
                            <input 
                                v-model="form.stock"
                                type="number" 
                                class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all outline-none"
                            />
                            <p v-if="form.errors.stock" class="text-red-400 text-xs">{{ form.errors.stock }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 rounded-xl bg-white/5 border border-white/10">
                        <div>
                            <h4 class="text-white font-medium">Available for Sale</h4>
                            <p class="text-xs text-gray-400">Turn this off to hide the product without deleting it.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" v-model="form.is_available" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                    </div>

                    <div v-if="product.images && product.images.length > 0" class="space-y-2">
                        <label class="text-sm font-medium text-gray-300">Current Images</label>
                        <div class="flex gap-4 overflow-x-auto pb-2">
                            <div v-for="(img, index) in product.images" :key="index" class="relative w-24 h-24 flex-shrink-0 rounded-lg overflow-hidden border border-white/20">
                                <img :src="getImageUrl(img)" class="w-full h-full object-cover">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 italic">*To change images, please delete and re-create the product (Image editing coming in v2).</p>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-white/10">
                        <Link 
                            :href="route('business.products.index')"
                            class="px-6 py-2.5 rounded-xl text-gray-300 hover:text-white hover:bg-white/5 transition-colors"
                        >
                            Cancel
                        </Link>
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="px-8 py-2.5 rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-semibold shadow-lg shadow-purple-500/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all active:scale-95"
                        >
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Save Changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>