<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

// --- TABS CONFIGURATION ---
const currentTab = ref('subscription'); // Tabs: subscription, customization, sms, reviews

// --- 1. SUBSCRIPTION & QR LOGIC ---
const showQrModal = ref(false);
const subscriptionForm = useForm({
    payment_proof: null,
    plan: 'premium'
});

const submitPayment = () => {
    // In a real app, you would post this to your backend
    alert("Receipt Uploaded! Admin will verify your Premium status.");
    showQrModal.value = false;
};

// --- 2. STORE CUSTOMIZATION LOGIC ---
const storeSettings = reactive({
    theme: 'modern',
    previewMode: true
});
const themes = {
    classic: 'bg-amber-50 border-amber-200 text-amber-900',
    modern: 'bg-white border-gray-200 text-gray-900',
    premium: 'bg-slate-900 border-indigo-500 text-white shadow-indigo-500/20 shadow-xl'
};

// --- 3. SMS BRIDGE DATA (Mock Data) ---
const messages = ref([
    { id: 1, from: '0917-123-4567', name: 'Maria Santos', text: 'Hm po sa Bicol Express jar?', time: '10:00 AM', type: 'incoming' },
    { id: 2, from: 'Me', name: 'Store Owner', text: '150 pesos po mam.', time: '10:05 AM', type: 'outgoing' },
]);
const smsInput = ref('');

const sendSms = () => {
    if(!smsInput.value) return;
    // Push to UI
    messages.value.push({
        id: Date.now(),
        from: 'Me',
        name: 'Store Owner',
        text: smsInput.value,
        time: 'Now',
        type: 'outgoing'
    });
    
    // LOGIC: Here you would make an API call to Twilio/Semaphore
    console.log("Sending SMS payload to Gateway:", smsInput.value);
    
    smsInput.value = '';
};

// --- 4. REVIEWS DATA (Mock Data) ---
const reviews = ref([
    { id: 1, product: 'Spicy Laing', user: 'Juan D.', rating: 5, comment: 'Sobrang sarap! Authentic Bicol taste.', likes: 12 },
    { id: 2, product: 'Pili Tart', user: 'Ana R.', rating: 4, comment: 'Good packaging but delivery was slow.', likes: 3 },
]);
</script>

<template>
    <Head title="Business Manager" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Business Management Hub</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    
                    <div class="bg-white p-4 rounded-lg shadow space-y-2 h-fit">
                        <button @click="currentTab = 'subscription'" :class="currentTab === 'subscription' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'" class="w-full text-left px-4 py-3 rounded-md font-bold transition flex items-center gap-2">
                            💎 Upgrade Plan
                        </button>
                        <button @click="currentTab = 'customization'" :class="currentTab === 'customization' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'" class="w-full text-left px-4 py-3 rounded-md font-bold transition flex items-center gap-2">
                            🎨 Store Layout
                        </button>
                        <button @click="currentTab = 'sms'" :class="currentTab === 'sms' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'" class="w-full text-left px-4 py-3 rounded-md font-bold transition flex items-center gap-2">
                            📱 SMS Inbox
                        </button>
                        <button @click="currentTab = 'reviews'" :class="currentTab === 'reviews' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'" class="w-full text-left px-4 py-3 rounded-md font-bold transition flex items-center gap-2">
                            ⭐ Feedbacks
                        </button>
                    </div>

                    <div class="md:col-span-3 bg-white p-6 rounded-lg shadow min-h-[500px]">
                        
                        <div v-if="currentTab === 'subscription'">
                            <h3 class="text-2xl font-bold mb-4">Upgrade to Premium</h3>
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 text-white mb-6">
                                <h4 class="text-lg font-bold">Premium Benefits:</h4>
                                <ul class="list-disc ml-5 mt-2 space-y-1">
                                    <li>Priority listing in Marketplace</li>
                                    <li>Custom Store Themes (Dark/Neon Mode)</li>
                                    <li>Unlimited SMS Replies to Residents</li>
                                    <li>Analytics Dashboard</li>
                                </ul>
                            </div>
                            
                            <div class="text-center py-8 border-2 border-dashed border-gray-300 rounded-xl">
                                <p class="text-gray-500 mb-4">Current Plan: <strong>Standard (Free)</strong></p>
                                <button @click="showQrModal = true" class="bg-blue-600 text-white px-6 py-3 rounded-full font-bold hover:bg-blue-700 transition shadow-lg">
                                    Pay via QR Code (₱500/mo)
                                </button>
                            </div>
                        </div>

                        <div v-if="currentTab === 'customization'">
                            <h3 class="text-2xl font-bold mb-6">Customize Storefront</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Select Theme</label>
                                    <div class="space-y-3">
                                        <div v-for="(classString, name) in themes" :key="name" 
                                             @click="storeSettings.theme = name"
                                             class="cursor-pointer border-2 p-4 rounded-lg flex items-center justify-between transition-all"
                                             :class="storeSettings.theme === name ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                            <span class="capitalize font-medium">{{ name }}</span>
                                            <div class="w-4 h-4 rounded-full border border-gray-300" :class="storeSettings.theme === name ? 'bg-blue-500' : 'bg-white'"></div>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-4">*Premium themes require subscription</p>
                                </div>

                                <div class="flex justify-center">
                                    <div class="w-[280px] h-[450px] border-8 border-gray-800 rounded-[2.5rem] overflow-hidden relative shadow-2xl bg-gray-100">
                                        <div class="h-full w-full p-4 flex flex-col gap-3 overflow-y-auto" :class="themes[storeSettings.theme]">
                                            <div class="h-8 w-3/4 bg-current opacity-20 rounded"></div>
                                            <div class="aspect-video w-full bg-current opacity-10 rounded-lg"></div>
                                            <div class="flex gap-2">
                                                <div class="h-20 w-1/2 bg-current opacity-10 rounded-lg"></div>
                                                <div class="h-20 w-1/2 bg-current opacity-10 rounded-lg"></div>
                                            </div>
                                            <div class="h-4 w-full bg-current opacity-10 rounded mt-auto"></div>
                                        </div>
                                        <div class="absolute top-0 left-1/2 -translate-x-1/2 h-6 w-32 bg-gray-800 rounded-b-xl"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="currentTab === 'sms'" class="flex flex-col h-[500px]">
                            <div class="mb-4">
                                <h3 class="text-2xl font-bold">SMS Bridge</h3>
                                <p class="text-sm text-gray-500">Messages sent here are forwarded to the resident's phone number.</p>
                            </div>
                            
                            <div class="flex-1 overflow-y-auto border rounded-xl p-4 bg-gray-50 space-y-4 mb-4">
                                <div v-for="msg in messages" :key="msg.id" 
                                     class="flex flex-col max-w-[80%]"
                                     :class="msg.type === 'outgoing' ? 'ml-auto items-end' : 'mr-auto items-start'">
                                    <div class="px-4 py-2 rounded-2xl text-sm"
                                         :class="msg.type === 'outgoing' ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white border shadow-sm text-gray-800 rounded-bl-none'">
                                        {{ msg.text }}
                                    </div>
                                    <span class="text-[10px] text-gray-400 mt-1">{{ msg.time }}</span>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <input v-model="smsInput" type="text" placeholder="Type an SMS reply..." class="flex-1 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <button @click="sendSms" class="bg-green-600 text-white px-6 rounded-lg font-bold hover:bg-green-700">Send</button>
                            </div>
                        </div>

                        <div v-if="currentTab === 'reviews'">
                            <h3 class="text-2xl font-bold mb-6">Product Feedback</h3>
                            <div class="space-y-4">
                                <div v-for="review in reviews" :key="review.id" class="border border-gray-100 p-4 rounded-xl hover:shadow-md transition">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-bold text-gray-800">{{ review.product }}</h4>
                                            <div class="flex items-center gap-1 text-yellow-400 text-sm my-1">
                                                <span v-for="n in 5" :key="n">{{ n <= review.rating ? '★' : '☆' }}</span>
                                            </div>
                                            <p class="text-gray-600 text-sm mt-1">"{{ review.comment }}"</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs font-bold text-gray-500 block">{{ review.user }}</span>
                                            <span class="text-xs text-pink-500 font-bold mt-2 block">❤️ {{ review.likes }} Likes</span>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex gap-2">
                                        <button class="text-xs bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded-full text-gray-600">Reply</button>
                                        <button class="text-xs bg-red-50 hover:bg-red-100 px-3 py-1 rounded-full text-red-600">Report</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div v-if="showQrModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl relative">
                <button @click="showQrModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">✕</button>
                
                <h3 class="text-xl font-bold text-center mb-2">Scan to Pay</h3>
                <p class="text-sm text-gray-500 text-center mb-6">Scan using GCash or Maya</p>
                
                <div class="bg-gray-100 p-4 rounded-xl border-2 border-gray-200 mb-6 flex items-center justify-center">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=LGU-PAYMENT-REF-12345" alt="QR Code" class="w-48 h-48 mix-blend-multiply">
                </div>

                <div class="space-y-3">
                    <label class="block text-sm font-bold text-gray-700">Upload Receipt / Screenshot</label>
                    <input type="file" @input="form.payment_proof = $event.target.files[0]" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                    
                    <button @click="submitPayment" class="w-full bg-black text-white py-3 rounded-lg font-bold mt-4">
                        Submit for Verification
                    </button>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>