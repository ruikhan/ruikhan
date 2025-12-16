<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    cart: Object,
    total: Number
});

const removeItem = (id) => {
    router.delete(route('cart.remove', id));
};

const getImageUrl = (path) => path ? (path.startsWith('http') ? path : `/storage/${path}`) : null;
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Your Cart" />

        <div class="py-12 bg-gray-900 min-h-screen text-white">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                
                <h1 class="text-3xl font-bold mb-8 flex items-center gap-3">
                    <span>🛒</span> Shopping Cart
                </h1>

                <div v-if="Object.keys(cart).length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <div class="md:col-span-2 space-y-4">
                        <div v-for="item in cart" :key="item.id" class="bg-gray-800 rounded-xl p-4 flex gap-4 items-center border border-gray-700 shadow-lg">
                            <div class="w-20 h-20 bg-gray-700 rounded-lg overflow-hidden shrink-0">
                                <img v-if="item.image" :src="getImageUrl(item.image)" class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full flex items-center justify-center text-2xl">📦</div>
                            </div>

                            <div class="flex-1">
                                <h3 class="font-bold text-lg">{{ item.name }}</h3>
                                <p class="text-sm text-gray-400">Sold by {{ item.business_name }}</p>
                                <div class="text-blue-400 font-bold mt-1">₱{{ item.price }} x {{ item.quantity }}</div>
                            </div>

                            <div class="flex flex-col items-end gap-2">
                                <span class="font-bold text-xl">₱{{ (item.price * item.quantity).toFixed(2) }}</span>
                                <button @click="removeItem(item.id)" class="text-xs text-red-400 hover:text-red-300 underline">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-1">
                        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg sticky top-8">
                            <h2 class="text-xl font-bold mb-4 border-b border-gray-700 pb-2">Summary</h2>
                            
                            <div class="flex justify-between mb-2 text-gray-400">
                                <span>Subtotal</span>
                                <span>₱{{ total.toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between mb-6 text-gray-400">
                                <span>Tax</span>
                                <span>₱0.00</span>
                            </div>

                            <div class="flex justify-between mb-6 text-xl font-bold text-white">
                                <span>Total</span>
                                <span>₱{{ total.toFixed(2) }}</span>
                            </div>

                            <button class="w-full py-3 bg-blue-600 hover:bg-blue-500 rounded-lg font-bold shadow-lg transition-all active:scale-95">
                                Checkout Now
                            </button>
                            
                            <Link :href="route('marketplace.index')" class="block text-center mt-4 text-sm text-gray-400 hover:text-white">
                                or Continue Shopping
                            </Link>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-20 bg-gray-800/50 rounded-2xl border border-gray-700 border-dashed">
                    <div class="text-6xl mb-4 opacity-50">🛒</div>
                    <h2 class="text-2xl font-bold mb-2">Your cart is empty</h2>
                    <p class="text-gray-400 mb-6">Looks like you haven't added anything yet.</p>
                    <Link :href="route('marketplace.index')" class="px-6 py-2 bg-white text-black font-bold rounded-full hover:bg-gray-200 transition">
                        Start Shopping
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>