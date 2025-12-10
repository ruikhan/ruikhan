<script setup>
import { nextTick, ref } from 'vue';

const isOpen = ref(false);
const messages = ref([
    { id: 1, text: "Hello! I am PILI AI. How can I assist you with your government needs today?", isUser: false }
]);
const userInput = ref('');
const chatContainer = ref(null);

const toggleChat = () => isOpen.value = !isOpen.value;

const scrollToBottom = async () => {
    await nextTick();
    if (chatContainer.value) chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
};

const sendMessage = () => {
    if (!userInput.value.trim()) return;

    // 1. Add User Message
    messages.value.push({ id: Date.now(), text: userInput.value, isUser: true });
    const text = userInput.value.toLowerCase();
    userInput.value = '';
    scrollToBottom();

    // 2. AI Logic (Simulated Response)
    setTimeout(() => {
        let response = "I'm not sure about that. Please contact the hotline at 123-4567.";

        if (text.includes('hello') || text.includes('hi')) response = "Greetings! Would you like to request a document or report an issue?";
        else if (text.includes('document') || text.includes('clearance')) response = "To get a document, go to the 'Services' tab and select 'Request Documents'. We offer Barangay Clearance and Indigency certificates.";
        else if (text.includes('report') || text.includes('issue') || text.includes('complaint')) response = "You can report incidents via the 'Citizen Watch' module. Please have a photo ready for evidence.";
        else if (text.includes('job') || text.includes('work')) response = "Check the 'Job Fair' section to see openings from local businesses.";
        else if (text.includes('pay') || text.includes('bill')) response = "Our secure payment gateway handles Water, Electric, and Internet bills.";

        messages.value.push({ id: Date.now() + 1, text: response, isUser: false });
        scrollToBottom();
    }, 800); // Small delay to feel "real"
};
</script>

<template>
    <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end">
        
        <transition name="slide-up">
            <div v-if="isOpen" class="mb-4 w-80 md:w-96 bg-slate-900/90 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden flex flex-col h-[500px]">
                
                <div class="p-4 bg-gradient-to-r from-blue-600 to-purple-600 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center text-sm">🤖</div>
                        <div>
                            <h3 class="font-bold text-white text-sm">PILI AI Support</h3>
                            <p class="text-[10px] text-blue-100 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span> Online
                            </p>
                        </div>
                    </div>
                    <button @click="toggleChat" class="text-white/70 hover:text-white">✕</button>
                </div>

                <div ref="chatContainer" class="flex-1 overflow-y-auto p-4 space-y-4 bg-transparent">
                    <div v-for="msg in messages" :key="msg.id" :class="['flex', msg.isUser ? 'justify-end' : 'justify-start']">
                        <div :class="['max-w-[80%] p-3 rounded-2xl text-xs leading-relaxed', msg.isUser ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white/10 text-slate-200 border border-white/5 rounded-bl-none']">
                            {{ msg.text }}
                        </div>
                    </div>
                </div>

                <div class="p-3 border-t border-white/10 bg-black/20">
                    <form @submit.prevent="sendMessage" class="flex gap-2">
                        <input v-model="userInput" type="text" placeholder="Type a question..." class="flex-1 bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-white text-xs focus:ring-1 focus:ring-blue-500 transition-all placeholder-slate-500">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white p-2 rounded-xl transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </transition>

        <button @click="toggleChat" class="group relative flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full shadow-lg hover:scale-110 transition-all duration-300">
            <span v-if="!isOpen" class="absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75 animate-ping"></span>
            <span class="text-2xl group-hover:rotate-12 transition-transform">💬</span>
        </button>

    </div>
</template>

<style scoped>
.slide-up-enter-active, .slide-up-leave-active { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
.slide-up-enter-from, .slide-up-leave-to { opacity: 0; transform: translateY(20px) scale(0.95); }
</style>