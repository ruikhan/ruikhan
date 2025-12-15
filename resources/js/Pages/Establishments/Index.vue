<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ establishments: Array, myBookings: Array });

// --- FORM LOGIC ---
const form = useForm({
    name: '', type: 'food', location: '', contact_number: '', image: null,
});
const submit = () => {
    form.post(route('establishments.store'), { onSuccess: () => form.reset() });
};

// --- BOOKING LOGIC ---
const showBookingModal = ref(false);
const selectedShop = ref(null);
const bookingForm = useForm({
    establishment_id: '', service_type: 'Table Reservation', scheduled_at: '', notes: ''
});

const openBooking = (shop) => {
    selectedShop.value = shop;
    bookingForm.establishment_id = shop.id;
    showBookingModal.value = true;
};

const submitBooking = () => {
    bookingForm.post(route('bookings.store'), {
        onSuccess: () => {
            showBookingModal.value = false;
            bookingForm.reset();
        }
    });
};

const getImageUrl = (path) => path ? `/storage/${path}` : 'https://via.placeholder.com/600x400?text=No+Image';
</script>

<template>
    <Head title="Places" />

    <AuthenticatedLayout>
        <div class="fixed inset-0 bg-black -z-50"></div>
        <div class="fixed inset-0 overflow-hidden -z-40 pointer-events-none">
            <div class="absolute top-[-10%] right-[-5%] w-[900px] h-[900px] bg-orange-500/20 rounded-full blur-[120px] opacity-40 mix-blend-screen animate-float"></div>
            <div class="absolute bottom-[-10%] left-[-10%] w-[900px] h-[900px] bg-indigo-600/30 rounded-full blur-[120px] opacity-40 mix-blend-screen animate-float" style="animation-delay: -4s;"></div>
        </div>

        <template #header>
            <div class="flex items-end justify-between relative z-10 px-2">
                <div>
                    <h2 class="text-4xl font-extrabold text-white tracking-tight drop-shadow-sm">
                        Discover
                    </h2>
                    <p class="text-white/60 text-lg font-medium mt-1">Local dining, stays, and services.</p>
                </div>
                <div class="hidden md:block bg-white/10 backdrop-blur-md border border-white/10 px-4 py-2 rounded-full">
                    <span class="text-xs font-bold text-white uppercase tracking-wider">
                         {{ new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric'}) }}
                    </span>
                </div>
            </div>
        </template>

        <div class="py-12 relative z-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    
                    <div class="lg:col-span-7 bg-gray-800/40 backdrop-blur-2xl border border-white/10 rounded-[2rem] p-6 shadow-2xl animate-slide-up">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-500 shadow-[0_0_10px_#3b82f6]"></span>
                                Active Orders
                            </h3>
                            <span class="text-xs font-medium text-white/40 bg-white/5 px-2 py-1 rounded-lg">{{ myBookings.length }} items</span>
                        </div>

                        <div v-if="myBookings.length === 0" class="h-32 flex items-center justify-center text-white/30 text-sm">
                            No active bookings.
                        </div>

                        <div class="space-y-3 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                            <div v-for="booking in myBookings" :key="booking.id" 
                                class="group flex items-center gap-4 bg-black/20 hover:bg-black/30 border border-white/5 p-4 rounded-2xl transition-all duration-300">
                                
                                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center text-2xl shadow-inner">
                                    {{ booking.establishment ? booking.establishment.name.charAt(0) : '?' }}
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-bold text-white truncate">{{ booking.establishment ? booking.establishment.name : 'Unknown' }}</h4>
                                        <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded-full"
                                            :class="{
                                                'bg-yellow-500/20 text-yellow-200': booking.status === 'pending',
                                                'bg-green-500/20 text-green-200': booking.status === 'confirmed',
                                                'bg-gray-500/20 text-gray-300': booking.status !== 'pending' && booking.status !== 'confirmed'
                                            }">
                                            {{ booking.status }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-blue-300">{{ booking.service_type }}</p>
                                    <p class="text-xs text-white/40 mt-0.5">
                                        {{ new Date(booking.scheduled_at).toLocaleString([], {month:'short', day:'numeric', hour:'2-digit', minute:'2-digit'}) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-5 bg-white/5 backdrop-blur-2xl border border-white/10 rounded-[2rem] p-6 shadow-2xl relative overflow-hidden group animate-slide-up" style="animation-delay: 0.1s;">
                        <div class="absolute -right-10 -top-10 w-32 h-32 bg-purple-500/30 rounded-full blur-[50px] group-hover:bg-purple-500/50 transition-colors duration-700"></div>

                        <h3 class="text-xl font-bold text-white mb-6">Create Listing</h3>
                        
                        <form @submit.prevent="submit" class="space-y-3">
                            <input v-model="form.name" type="text" placeholder="Business Name" 
                                class="w-full bg-black/20 border-transparent focus:border-purple-500/50 focus:ring-0 rounded-xl text-white placeholder-white/30 h-10 px-4 text-sm transition-all">
                            
                            <div class="flex gap-3">
                                <select v-model="form.type" class="flex-1 bg-black/20 border-transparent focus:border-purple-500/50 focus:ring-0 rounded-xl text-white h-10 px-3 text-sm appearance-none">
                                    <option value="food">🍔 Food</option>
                                    <option value="hotel">🏨 Stay</option>
                                    <option value="service">💇 Service</option>
                                    <option value="transport">🛵 Transport</option>
                                </select>
                                <input v-model="form.location" type="text" placeholder="Address" 
                                    class="flex-1 bg-black/20 border-transparent focus:border-purple-500/50 focus:ring-0 rounded-xl text-white placeholder-white/30 h-10 px-4 text-sm">
                            </div>

                            <input type="file" @input="form.image = $event.target.files[0]" 
                                class="w-full text-xs text-white/50 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-white/10 file:text-white hover:file:bg-white/20 transition-all cursor-pointer">

                            <button type="submit" class="w-full h-10 mt-2 bg-white text-black font-bold rounded-xl hover:bg-gray-100 transition-all shadow-lg active:scale-95 text-sm">
                                Submit Application
                            </button>
                        </form>
                    </div>

                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 animate-slide-up" style="animation-delay: 0.2s;">
                    
                    <div v-for="shop in establishments" :key="shop.id" 
                        class="group relative h-[400px] rounded-[2rem] overflow-hidden shadow-2xl transition-all duration-500 hover:scale-[1.02] hover:shadow-orange-500/20 cursor-pointer border border-white/5">
                        
                        <img :src="getImageUrl(shop.image)" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent opacity-90"></div>
                        
                        <div class="absolute inset-0 p-8 flex flex-col justify-end">
                            <div class="absolute top-6 right-6">
                                <span class="px-3 py-1 bg-white/10 backdrop-blur-md border border-white/20 rounded-full text-xs font-bold text-white uppercase tracking-wider shadow-lg">
                                    {{ shop.type }}
                                </span>
                            </div>

                            <div class="transform transition-all duration-300 translate-y-2 group-hover:translate-y-0">
                                <h3 class="text-3xl font-extrabold text-white leading-tight mb-2">{{ shop.name }}</h3>
                                <p class="text-white/70 text-sm font-medium mb-6 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ shop.location }}
                                </p>
                                
                                <button @click="openBooking(shop)" class="w-full h-12 bg-white text-black font-bold rounded-xl opacity-0 group-hover:opacity-100 transition-all duration-300 translate-y-4 group-hover:translate-y-0 shadow-lg hover:bg-gray-100 flex items-center justify-center gap-2">
                                    <span>Book Now</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <div v-if="showBookingModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-md transition-opacity" @click="showBookingModal = false"></div>
            
            <div class="relative w-full max-w-md bg-gray-900 border border-white/10 rounded-[2rem] shadow-2xl overflow-hidden animate-scale-in">
                <div class="h-32 bg-gradient-to-r from-orange-500 to-pink-600 relative overflow-hidden">
                    <div class="absolute inset-0 bg-black/20"></div>
                    <div class="absolute bottom-4 left-6">
                        <h3 class="text-2xl font-bold text-white shadow-sm">Booking</h3>
                        <p class="text-white/80 text-sm font-medium">{{ selectedShop.name }}</p>
                    </div>
                </div>

                <form @submit.prevent="submitBooking" class="p-6 space-y-5">
                    <div class="space-y-4">
                        <div class="bg-white/5 rounded-2xl p-1 border border-white/5">
                             <select v-model="bookingForm.service_type" class="w-full bg-transparent border-none text-white focus:ring-0 text-sm px-4 py-3">
                                <option class="bg-gray-800">Table Reservation</option>
                                <option class="bg-gray-800">Food Delivery</option>
                                <option class="bg-gray-800">Room Booking</option>
                                <option class="bg-gray-800">Service Appointment</option>
                            </select>
                        </div>

                        <div class="bg-white/5 rounded-2xl p-1 border border-white/5">
                             <input v-model="bookingForm.scheduled_at" type="datetime-local" class="w-full bg-transparent border-none text-white focus:ring-0 text-sm px-4 py-3 [color-scheme:dark]">
                        </div>

                        <div class="bg-white/5 rounded-2xl p-1 border border-white/5">
                             <textarea v-model="bookingForm.notes" rows="3" placeholder="Add a note..." class="w-full bg-transparent border-none text-white focus:ring-0 text-sm px-4 py-3 placeholder-white/30 resize-none"></textarea>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-4">
                        <button type="button" @click="showBookingModal = false" class="flex-1 py-3 text-white/60 font-medium hover:text-white transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 py-3 bg-white text-black font-bold rounded-xl hover:bg-gray-100 transition-all shadow-lg active:scale-95">
                            Confirm
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<style scoped>
/* Custom Scrollbar for the My Orders widget */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
}

@keyframes float {
    0% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0, 0) scale(1); }
}
.animate-float { animation: float 15s ease-in-out infinite; }

@keyframes slide-up {
    from { opacity: 0; transform: translateY(30px); filter: blur(10px); }
    to { opacity: 1; transform: translateY(0); filter: blur(0); }
}
.animate-slide-up { animation: slide-up 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

@keyframes scale-in {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}
.animate-scale-in { animation: scale-in 0.3s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
</style>