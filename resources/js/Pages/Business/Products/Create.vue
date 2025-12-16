<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    categories: Array,
});

const form = useForm({
    name: '',
    description: '',
    price: '',
    stock: '',
    category: '',
    images: [],
});

const imagePreviews = ref([]);

const handleImageUpload = (event) => {
    const files = Array.from(event.target.files).slice(0, 5); // Max 5 images
    form.images = files;
    
    // Generate previews
    imagePreviews.value = files.map(file => URL.createObjectURL(file));
};

const removeImage = (index) => {
    const newImages = Array.from(form.images);
    newImages.splice(index, 1);
    form.images = newImages;
    imagePreviews.value.splice(index, 1);
};

const submit = () => {
    form.post(route('business.products.store'), {
        onSuccess: () => {
            form.reset();
            imagePreviews.value = [];
        },
    });
};
</script>

<template>
    <Head title="Add Product" />

    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">Add New Product</h1>
                    <p class="text-gray-400 mt-1">Fill in the details to add a new product</p>
                </div>
                <Link 
                    :href="route('business.products.index')" 
                    class="px-5 py-2.5 rounded-full bg-white/5 text-gray-300 border border-white/10 hover:bg-white/10 hover:text-white font-semibold text-sm transition-all"
                >
                    ← Back to Products
                </Link>
            </div>

            <!-- Form -->
            <div class="rounded-3xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-8">
                <div class="space-y-6">
                    
                    <!-- Product Name -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            Product Name <span class="text-red-400">*</span>
                        </label>
                        <input 
                            v-model="form.name"
                            type="text" 
                            placeholder="e.g., Premium Coffee Beans"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:bg-white/10 focus:border-blue-500/50 focus:ring-2 focus:ring-blue-500/20 transition-all"
                        />
                        <div v-if="form.errors.name" class="text-red-400 text-sm mt-2">{{ form.errors.name }}</div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            Description <span class="text-red-400">*</span>
                        </label>
                        <textarea 
                            v-model="form.description"
                            rows="4"
                            placeholder="Describe your product in detail..."
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:bg-white/10 focus:border-blue-500/50 focus:ring-2 focus:ring-blue-500/20 transition-all resize-none"
                        ></textarea>
                        <div v-if="form.errors.description" class="text-red-400 text-sm mt-2">{{ form.errors.description }}</div>
                        <p class="text-xs text-gray-500 mt-2">Maximum 1000 characters</p>
                    </div>

                    <!-- Price & Stock -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">
                                Price (₱) <span class="text-red-400">*</span>
                            </label>
                            <input 
                                v-model="form.price"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="0.00"
                                class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:bg-white/10 focus:border-blue-500/50 focus:ring-2 focus:ring-blue-500/20 transition-all"
                            />
                            <div v-if="form.errors.price" class="text-red-400 text-sm mt-2">{{ form.errors.price }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">
                                Stock Quantity <span class="text-red-400">*</span>
                            </label>
                            <input 
                                v-model="form.stock"
                                type="number"
                                min="0"
                                placeholder="0"
                                class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:bg-white/10 focus:border-blue-500/50 focus:ring-2 focus:ring-blue-500/20 transition-all"
                            />
                            <div v-if="form.errors.stock" class="text-red-400 text-sm mt-2">{{ form.errors.stock }}</div>
                        </div>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            Category
                        </label>
                        <select 
                            v-model="form.category"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:bg-white/10 focus:border-blue-500/50 focus:ring-2 focus:ring-blue-500/20 transition-all"
                        >
                            <option value="">Select a category</option>
                            <option v-for="cat in categories" :key="cat.value" :value="cat.value">
                                {{ cat.label }}
                            </option>
                        </select>
                        <div v-if="form.errors.category" class="text-red-400 text-sm mt-2">{{ form.errors.category }}</div>
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            Product Images (Max 5)
                        </label>
                        <input 
                            type="file"
                            @change="handleImageUpload"
                            accept="image/*"
                            multiple
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-500 file:text-white hover:file:bg-blue-600 transition-all"
                        />
                        <div v-if="form.errors.images" class="text-red-400 text-sm mt-2">{{ form.errors.images }}</div>
                        <p class="text-xs text-gray-500 mt-2">Supported formats: JPG, PNG, GIF. Max 5MB per image.</p>
                        
                        <!-- Image Previews -->
                        <div v-if="imagePreviews.length" class="grid grid-cols-3 md:grid-cols-5 gap-4 mt-4">
                            <div v-for="(preview, index) in imagePreviews" :key="index" class="relative group">
                                <img :src="preview" class="w-full h-24 object-cover rounded-xl border border-white/10" />
                                <button 
                                    @click="removeImage(index)"
                                    type="button"
                                    class="absolute top-2 right-2 w-6 h-6 bg-red-500 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                                >
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button 
                            @click="submit"
                            :disabled="form.processing"
                            class="flex-1 px-8 py-4 rounded-xl bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold text-lg shadow-[0_0_30px_rgba(34,197,94,0.4)] disabled:opacity-50 disabled:cursor-not-allowed transition-all active:scale-95"
                        >
                            {{ form.processing ? 'Adding Product...' : '✓ Add Product' }}
                        </button>
                        <Link 
                            :href="route('business.products.index')"
                            class="px-8 py-4 rounded-xl bg-white/5 border border-white/10 text-gray-300 font-bold text-lg hover:bg-white/10 transition-all text-center"
                        >
                            Cancel
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>