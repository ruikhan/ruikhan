<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';

const props = defineProps({
  user: {
    type: Object,
    default: () => ({
      name: 'Juan Dela Cruz',
      business_name: 'Tindahan ni Juan',
      subscription_tier: 'basic', // Changed default to basic to test upgrade logic
      subscription_expires: '2025-02-15',
      verified: true,
      business_type: 'retail'
    })
  },
  stats: {
    type: Object,
    default: () => ({
      today_revenue: 1250.50,
      weekly_revenue: 8750.00,
      monthly_revenue: 35420.00,
      total_orders: 142,
      pending_orders: 8,
      completed_orders: 134,
      inventory_count: 24,
      low_stock_items: 3,
      total_reviews: 87,
      average_rating: 4.7,
      total_likes: 234
    })
  }
});

const page = usePage();
const businessStatus = ref('Open');
const statusOptions = ['Available', 'Open', 'Closed', 'Full'];

// --- TABS LOGIC ---
const activeTab = ref('overview');
const tabs = ['overview', 'sales', 'inventory', 'reviews', 'messages', 'settings']; 

// --- UPGRADE MODAL LOGIC ---
const showUpgradeModal = ref(false);
const upgradeForm = useForm({
    plan_selected: 'premium',
    payment_proof: null
});

const handleFileChange = (e) => {
    upgradeForm.payment_proof = e.target.files[0];
};

const submitUpgrade = () => {
    // In a real app: upgradeForm.post(route('subscription.upgrade'));
    alert(`Request submitted for ${upgradeForm.plan_selected.toUpperCase()} plan! Receipt: ${upgradeForm.payment_proof ? 'Attached' : 'Pending'}`);
    showUpgradeModal.value = false;
    upgradeForm.reset();
};

// --- STORE CUSTOMIZATION LOGIC ---
const storeSettings = reactive({
    theme: 'neon_nights',
    layout: 'grid'
});
const themes = [
    { id: 'classic', name: 'Classic Clean', bg: 'bg-white', border: 'border-gray-200', text: 'text-gray-900' },
    { id: 'neon_nights', name: 'Neon Nights', bg: 'bg-slate-900', border: 'border-blue-500/50', text: 'text-blue-400' },
    { id: 'tropical', name: 'Tropical Vibe', bg: 'bg-teal-900', border: 'border-teal-400/50', text: 'text-teal-200' }
];

// --- SMS BRIDGE LOGIC ---
const smsInput = ref('');
const smsMessages = ref([
    { id: 1, name: 'Maria Santos', text: 'Open pa po ba kayo?', time: '10:00 AM', type: 'incoming' },
    { id: 2, name: 'You', text: 'Yes po mam, until 8pm.', time: '10:05 AM', type: 'outgoing' },
]);

const sendSms = () => {
    if(!smsInput.value) return;
    smsMessages.value.push({
        id: Date.now(),
        name: 'You',
        text: smsInput.value,
        time: 'Just now',
        type: 'outgoing'
    });
    // In real app: router.post('/sms/send', { message: smsInput.value })
    smsInput.value = '';
};

// --- TIER & DATA LOGIC ---
const tierBenefits = {
  basic: {
    name: 'Basic', price: '₱100.00', duration: '1 month',
    features: ['Standardized Dashboard', 'Basic Sales Tracking', 'Inventory Management'],
    gradient: 'from-blue-500/20 via-blue-600/10 to-transparent'
  },
  standard: {
    name: 'Standard', price: '₱150.00', duration: '1 month',
    features: ['Customizable Dashboard', 'Advanced Analytics', 'Priority Support'],
    gradient: 'from-purple-500/20 via-purple-600/10 to-transparent'
  },
  premium: {
    name: 'Premium', price: '₱200.00', duration: '2 months',
    features: ['All Features', 'Verified Badge', 'Featured Listings', 'SMS Blast'],
    gradient: 'from-amber-500/20 via-amber-600/10 to-transparent'
  }
};

const currentTier = computed(() => tierBenefits[props.user.subscription_tier] || tierBenefits.basic);
const daysUntilExpiry = computed(() => {
  const today = new Date();
  const expiry = new Date(props.user.subscription_expires);
  return Math.ceil((expiry - today) / (1000 * 60 * 60 * 24));
});

const inventoryItems = ref([
  { id: 1, name: 'Premium Coffee Beans', stock: 5, price: 299.00, status: 'Low Stock', category: 'Beverages' },
  { id: 2, name: 'Organic Rice 5kg', stock: 50, price: 150.00, status: 'In Stock', category: 'Groceries' },
  { id: 3, name: 'Homemade Chili Garlic', stock: 12, price: 120.00, status: 'In Stock', category: 'Condiments' },
]);

const recentOrders = ref([
  { id: '#ORD-001', customer: 'Maria Santos', amount: 450.00, status: 'Completed', date: '2025-12-15', time: '10:30 AM' },
  { id: '#ORD-002', customer: 'Juan Dela Cruz', amount: 1200.00, status: 'Pending', date: '2025-12-15', time: '11:15 AM' },
]);

const getStatusColor = (status) => {
  const colors = { 'Available': 'bg-green-500/20 text-green-300', 'Open': 'bg-blue-500/20 text-blue-300', 'Closed': 'bg-gray-500/20 text-gray-300', 'Full': 'bg-red-500/20 text-red-300' };
  return colors[status] || 'bg-gray-500/20 text-gray-300';
};

const updateBusinessStatus = (newStatus) => { businessStatus.value = newStatus; };

const getOrderStatusColor = (status) => {
  const colors = { 'Completed': 'bg-green-500/20 text-green-300', 'Pending': 'bg-yellow-500/20 text-yellow-300', 'Processing': 'bg-blue-500/20 text-blue-300' };
  return colors[status] || 'bg-gray-500/20 text-gray-300';
};
</script>

<template>
  <Head title="Business Dashboard" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto space-y-6 pb-20">
      
      <div class="relative overflow-hidden rounded-3xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 shadow-[0_20px_60px_-10px_rgba(0,0,0,0.5)] ring-1 ring-white/5">
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-[0.03] mix-blend-overlay"></div>
        <div :class="`absolute inset-0 bg-gradient-to-br ${currentTier.gradient} opacity-50`"></div>
        
        <div class="relative p-8">
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
              <div class="flex items-center gap-3 mb-2">
                <h1 class="text-3xl font-bold bg-gradient-to-br from-white via-gray-200 to-gray-400 bg-clip-text text-transparent">
                  {{ user.business_name || 'My Business' }}
                </h1>
                <span v-if="user.verified" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-amber-500/20 to-yellow-500/20 text-amber-300 border border-amber-500/30 shadow-[0_0_20px_rgba(245,158,11,0.3)]">
                  <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  VERIFIED
                </span>
              </div>
              <p class="text-gray-400 text-sm">{{ user.business_type === 'retail' ? 'Retail & Resale' : 'Service & Hospitality' }}</p>
              <div class="mt-3 flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/5 text-gray-300 border border-white/10">
                  {{ currentTier.name }} Plan • {{ daysUntilExpiry }} days left
                </span>
                <span :class="`inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border ${getStatusColor(businessStatus)}`">
                  {{ businessStatus }}
                </span>
              </div>
            </div>
            <button 
              @click="showUpgradeModal = true"
              class="px-6 py-2.5 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold text-sm shadow-[0_0_20px_rgba(59,130,246,0.4)] transition-all duration-300 active:scale-95 border border-blue-400/20">
              Upgrade Plan
            </button>
          </div>
        </div>
      </div>

      <div class="flex flex-wrap gap-2 px-1">
        <button 
          v-for="tab in tabs" 
          :key="tab"
          @click="activeTab = tab"
          :class="[
            'px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300',
            activeTab === tab 
              ? 'bg-white/10 text-white border border-white/20 shadow-[inset_0_1px_0_rgba(255,255,255,0.1),0_4px_12px_rgba(0,0,0,0.4)]' 
              : 'bg-white/5 text-gray-400 border border-white/5 hover:bg-white/10 hover:text-gray-200 hover:border-white/10'
          ]"
        >
          {{ tab.charAt(0).toUpperCase() + tab.slice(1) }}
        </button>
      </div>
      
      <div v-if="activeTab === 'overview'" class="space-y-6">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="group relative overflow-hidden rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-6 shadow-lg hover:shadow-[0_0_30px_rgba(34,197,94,0.2)] transition-all duration-500 hover:scale-[1.02]">
            <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative">
              <div class="flex justify-between items-start mb-3">
                <div class="p-2 rounded-xl bg-green-500/20 border border-green-500/30">
                  <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              </div>
              <p class="text-xs font-medium text-gray-400 mb-1">Today's Revenue</p>
              <p class="text-2xl font-bold text-green-400 mb-2">₱{{ stats.today_revenue.toFixed(2) }}</p>
              <p class="text-xs text-green-400/70">↑ 12.5% from yesterday</p>
            </div>
          </div>

          <div class="group relative overflow-hidden rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-6 shadow-lg hover:shadow-[0_0_30px_rgba(59,130,246,0.2)] transition-all duration-500 hover:scale-[1.02]">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative">
              <div class="flex justify-between items-start mb-3">
                <div class="p-2 rounded-xl bg-blue-500/20 border border-blue-500/30">
                  <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                  </svg>
                </div>
              </div>
              <p class="text-xs font-medium text-gray-400 mb-1">Total Orders</p>
              <p class="text-2xl font-bold text-blue-400 mb-2">{{ stats.total_orders }}</p>
              <p class="text-xs text-blue-400/70">{{ stats.pending_orders }} pending</p>
            </div>
          </div>

          <div class="group relative overflow-hidden rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-6 shadow-lg hover:shadow-[0_0_30px_rgba(168,85,247,0.2)] transition-all duration-500 hover:scale-[1.02]">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative">
              <div class="flex justify-between items-start mb-3">
                <div class="p-2 rounded-xl bg-purple-500/20 border border-purple-500/30">
                  <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                  </svg>
                </div>
              </div>
              <p class="text-xs font-medium text-gray-400 mb-1">Inventory Items</p>
              <p class="text-2xl font-bold text-purple-400 mb-2">{{ stats.inventory_count }}</p>
              <p class="text-xs text-red-400/70">⚠️ {{ stats.low_stock_items }} low stock</p>
            </div>
          </div>

          <div class="group relative overflow-hidden rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-6 shadow-lg hover:shadow-[0_0_30px_rgba(245,158,11,0.2)] transition-all duration-500 hover:scale-[1.02]">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-500/10 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative">
              <div class="flex justify-between items-start mb-3">
                <div class="p-2 rounded-xl bg-amber-500/20 border border-amber-500/30">
                  <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                  </svg>
                </div>
              </div>
              <p class="text-xs font-medium text-gray-400 mb-1">Average Rating</p>
              <p class="text-2xl font-bold text-amber-400 mb-2">{{ stats.average_rating }} ⭐</p>
              <p class="text-xs text-amber-400/70">{{ stats.total_reviews }} reviews • {{ stats.total_likes }} ❤️</p>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-6 shadow-lg">
            <h3 class="text-lg font-semibold text-white mb-4">Business Status Control</h3>
            <div class="flex flex-wrap gap-3 mb-4">
                <button
                v-for="status in statusOptions"
                :key="status"
                @click="updateBusinessStatus(status)"
                :class="[
                    'px-6 py-3 rounded-full font-medium text-sm transition-all duration-300',
                    businessStatus === status 
                    ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-[0_0_20px_rgba(59,130,246,0.5)] scale-105 border border-blue-400/30' 
                    : 'bg-white/5 text-gray-400 border border-white/10 hover:bg-white/10 hover:text-white hover:border-white/20'
                ]"
                >
                {{ status }}
                </button>
            </div>
            <p class="text-sm text-gray-400">
                Current: <span class="font-semibold text-white">{{ businessStatus }}</span> — Visible to all customers
            </p>
            </div>

            <Link 
                :href="route('business.products.index')" 
                class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-900 to-fuchsia-900 border border-white/20 p-6 transition-all duration-500 hover:scale-[1.02] hover:border-white/40 flex flex-col justify-center"
            >
                <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent"></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <div class="text-4xl mb-2">📦</div>
                        <h3 class="text-xl font-bold text-white mb-1">Manage Products</h3>
                        <p class="text-white/80 text-sm">Add or edit inventory</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center group-hover:bg-white/30 transition-colors">
                        <svg class="w-6 h-6 text-white group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </div>
                </div>
            </Link>
        </div>

        <div class="rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 overflow-hidden shadow-lg">
          <div class="px-6 py-4 border-b border-white/10">
            <h3 class="text-lg font-semibold text-white">Recent Orders</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-white/5 bg-white/5">
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Order ID</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Customer</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Amount</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="order in recentOrders" :key="order.id" class="border-b border-white/5 hover:bg-white/5 transition-colors">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-blue-400">{{ order.id }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ order.customer }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-400">₱{{ order.amount.toFixed(2) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="`inline-flex px-3 py-1 text-xs font-semibold rounded-full border ${getOrderStatusColor(order.status)}`">
                      {{ order.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ order.date }} • {{ order.time }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div v-if="activeTab === 'sales'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-6 shadow-lg">
            <p class="text-xs font-medium text-gray-400 mb-2">Weekly Revenue</p>
            <p class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-blue-600 bg-clip-text text-transparent">₱{{ stats.weekly_revenue.toFixed(2) }}</p>
          </div>
          <div class="rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-6 shadow-lg">
            <p class="text-xs font-medium text-gray-400 mb-2">Monthly Revenue</p>
            <p class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-purple-600 bg-clip-text text-transparent">₱{{ stats.monthly_revenue.toFixed(2) }}</p>
          </div>
          <div class="rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-6 shadow-lg">
            <p class="text-xs font-medium text-gray-400 mb-2">Completed Orders</p>
            <p class="text-3xl font-bold bg-gradient-to-r from-green-400 to-green-600 bg-clip-text text-transparent">{{ stats.completed_orders }}</p>
          </div>
        </div>
        <div class="rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-12 text-center shadow-lg min-h-[300px] flex flex-col items-center justify-center border-dashed">
          <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
          <p class="text-white font-semibold mb-2">Sales Analytics Coming Soon</p>
          <p class="text-sm text-gray-400">Detailed charts and CSV exports will be available in the next update.</p>
        </div>
      </div>

      <div v-if="activeTab === 'inventory'" class="space-y-6">
        <div class="flex justify-between items-center">
          <h3 class="text-xl font-semibold text-white">Inventory Management</h3>
          <button class="px-5 py-2.5 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold text-sm shadow-[0_0_20px_rgba(59,130,246,0.4)] transition-all duration-300 active:scale-95">
            + Add Product
          </button>
        </div>
        <div class="space-y-3">
          <div v-for="item in inventoryItems" :key="item.id" class="group rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-6 shadow-lg hover:shadow-[0_0_20px_rgba(59,130,246,0.2)] hover:border-white/20 transition-all duration-300">
            <div class="flex justify-between items-start">
              <div>
                <h4 class="font-semibold text-white mb-1">{{ item.name }}</h4>
                <p class="text-sm text-gray-400 mb-2">{{ item.category }}</p>
                <div class="flex items-center gap-4 text-sm">
                  <span class="text-gray-300">Stock: <span class="font-semibold text-white">{{ item.stock }}</span> units</span>
                  <span class="text-gray-300">Price: <span class="font-semibold text-green-400">₱{{ item.price.toFixed(2) }}</span></span>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <span :class="[
                  'px-3 py-1 text-xs font-semibold rounded-full border',
                  item.status === 'Low Stock' ? 'bg-red-500/20 text-red-300 border-red-500/30' : 'bg-green-500/20 text-green-300 border-green-500/30'
                ]">
                  {{ item.status }}
                </span>
                <button class="text-blue-400 hover:text-blue-300 font-medium text-sm transition-colors">Edit</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="activeTab === 'reviews'" class="rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-12 text-center shadow-lg">
        <svg class="w-16 h-16 text-amber-500/50 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
        </svg>
        <p class="text-white font-semibold mb-2">Customer Reviews & Ratings</p>
        <p class="text-sm text-gray-400 mb-1">{{ stats.total_reviews }} customer reviews • Average: {{ stats.average_rating }} ⭐</p>
        <p class="text-sm text-gray-400">❤️ {{ stats.total_likes }} customers liked your business</p>
      </div>

      <div v-if="activeTab === 'messages'" class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[600px]">
        <div class="rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-4 flex flex-col">
          <h3 class="text-white font-bold mb-4 px-2">Inbox</h3>
          <div class="space-y-2 overflow-y-auto custom-scrollbar">
              <div class="p-3 rounded-xl bg-white/10 border border-white/5 cursor-pointer hover:bg-white/20 transition">
                <div class="flex justify-between">
                    <span class="text-sm font-bold text-white">Maria Santos</span>
                    <span class="text-xs text-gray-400">10:05 AM</span>
                </div>
                <p class="text-xs text-gray-300 truncate">Open pa po ba kayo?</p>
              </div>
              <div class="p-3 rounded-xl bg-transparent border border-transparent cursor-pointer hover:bg-white/5 transition">
                <div class="flex justify-between">
                    <span class="text-sm font-bold text-gray-400">Pedro Penduko</span>
                    <span class="text-xs text-gray-500">Yesterday</span>
                </div>
                <p class="text-xs text-gray-500 truncate">How much for delivery?</p>
              </div>
          </div>
        </div>

        <div class="lg:col-span-2 rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 flex flex-col overflow-hidden">
          <div class="p-4 border-b border-white/10 bg-white/5 flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">M</div>
              <div>
                  <h3 class="text-white font-bold text-sm">Maria Santos</h3>
                  <span class="text-xs text-green-400 flex items-center gap-1">● Online via SMS</span>
              </div>
          </div>

          <div class="flex-1 p-4 space-y-4 overflow-y-auto bg-black/20">
              <div v-for="msg in smsMessages" :key="msg.id" 
                  class="flex flex-col max-w-[80%]"
                  :class="msg.type === 'outgoing' ? 'ml-auto items-end' : 'mr-auto items-start'">
                  
                  <div class="px-4 py-2.5 rounded-2xl text-sm"
                        :class="msg.type === 'outgoing' 
                        ? 'bg-blue-600 text-white rounded-br-none shadow-[0_0_15px_rgba(37,99,235,0.3)]' 
                        : 'bg-white/10 text-gray-200 border border-white/10 rounded-bl-none'">
                        {{ msg.text }}
                  </div>
                  <span class="text-[10px] text-gray-500 mt-1">{{ msg.time }}</span>
              </div>
          </div>

          <div class="p-4 bg-white/5 border-t border-white/10 flex gap-2">
              <input v-model="smsInput" 
                     @keyup.enter="sendSms"
                     type="text" 
                     placeholder="Type SMS reply..." 
                     class="flex-1 bg-black/30 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition placeholder-gray-500 text-sm">
              <button @click="sendSms" class="bg-blue-600 hover:bg-blue-500 text-white px-6 rounded-xl font-bold transition shadow-lg flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
              </button>
          </div>
        </div>
      </div>

      <div v-if="activeTab === 'settings'" class="space-y-6">
        <div class="rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-6 shadow-lg">
          <h3 class="text-lg font-semibold text-white mb-6">Business Information</h3>
          <div class="space-y-4 max-w-2xl">
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Business Name</label>
              <input type="text" :value="user.business_name" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:bg-white/10 focus:border-blue-500/50 focus:ring-2 focus:ring-blue-500/20 transition-all outline-none">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Business Type</label>
              <select class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:bg-white/10 focus:border-blue-500/50 focus:ring-2 focus:ring-blue-500/20 transition-all outline-none">
                <option value="retail">Retail & Resale (Buy/Sell)</option>
                <option value="service">Service & Hospitality (Establishing)</option>
              </select>
            </div>
            <button class="px-6 py-3 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold shadow-[0_0_20px_rgba(59,130,246,0.4)] transition-all duration-300 active:scale-95">
              Save Changes
            </button>
          </div>
        </div>

        <div class="rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-6 shadow-lg">
          <div class="flex justify-between items-start mb-6">
             <div>
                <h3 class="text-lg font-semibold text-white">Dashboard Theme</h3>
                <p class="text-sm text-gray-400">Customize the look and feel of your admin panel.</p>
             </div>
             <span v-if="currentTier.name === 'Basic'" class="px-3 py-1 bg-gray-700 text-gray-300 text-xs rounded-full">Requires Standard Plan</span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <button 
                v-for="theme in themes" 
                :key="theme.id"
                @click="currentTier.name !== 'Basic' ? storeSettings.theme = theme.id : null"
                :disabled="currentTier.name === 'Basic'"
                :class="[
                    'relative h-24 rounded-xl border-2 transition-all duration-300 flex items-center justify-center overflow-hidden group',
                    storeSettings.theme === theme.id ? 'border-blue-500 ring-2 ring-blue-500/30' : 'border-white/10 hover:border-white/30',
                    currentTier.name === 'Basic' ? 'opacity-50 cursor-not-allowed grayscale' : 'cursor-pointer'
                ]"
              >
                 <div :class="`absolute inset-0 opacity-50 ${theme.bg}`"></div>
                 <span :class="`relative z-10 font-bold ${theme.text}`">{{ theme.name }}</span>
                 
                 <div v-if="currentTier.name === 'Basic'" class="absolute inset-0 bg-black/60 flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                 </div>
              </button>
          </div>
        </div>
      </div>

    </div>

    <div v-if="showUpgradeModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="showUpgradeModal = false"></div>
        
        <div class="relative w-full max-w-lg bg-[#1a1a1a] border border-white/10 rounded-3xl p-6 shadow-2xl animate-float">
            <div class="absolute -top-12 left-1/2 transform -translate-x-1/2">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-amber-400 to-orange-600 flex items-center justify-center shadow-[0_0_30px_rgba(245,158,11,0.5)]">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
            </div>

            <div class="mt-8 text-center">
                <h2 class="text-2xl font-bold text-white mb-2">Upgrade to Premium</h2>
                <p class="text-gray-400 text-sm mb-6">Unlock exclusive features, remove limits, and verify your business badge.</p>

                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div 
                        @click="upgradeForm.plan_selected = 'standard'"
                        :class="['p-3 rounded-xl border cursor-pointer transition', upgradeForm.plan_selected === 'standard' ? 'bg-purple-900/30 border-purple-500' : 'bg-white/5 border-white/10']"
                    >
                        <p class="font-bold text-purple-400">Standard</p>
                        <p class="text-white">₱150.00</p>
                    </div>
                    <div 
                        @click="upgradeForm.plan_selected = 'premium'"
                        :class="['p-3 rounded-xl border cursor-pointer transition', upgradeForm.plan_selected === 'premium' ? 'bg-amber-900/30 border-amber-500' : 'bg-white/5 border-white/10']"
                    >
                        <p class="font-bold text-amber-400">Premium</p>
                        <p class="text-white">₱200.00</p>
                    </div>
                </div>

                <div class="mb-6 text-left">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Upload GCash/Maya Receipt</label>
                    <div class="relative border-2 border-dashed border-white/20 rounded-xl p-4 hover:border-blue-500/50 transition bg-white/5">
                        <input type="file" @change="handleFileChange" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                        <div class="text-center">
                            <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="mt-1 text-sm text-gray-400">{{ upgradeForm.payment_proof ? upgradeForm.payment_proof.name : 'Click to upload image' }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button @click="showUpgradeModal = false" class="flex-1 px-4 py-2 rounded-xl border border-white/10 text-gray-300 hover:bg-white/5 transition">
                        Cancel
                    </button>
                    <button @click="submitUpgrade" class="flex-1 px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold shadow-lg hover:brightness-110 transition">
                        Submit Request
                    </button>
                </div>
            </div>
        </div>
    </div>

  </AuthenticatedLayout>
</template>

<style scoped>
/* Scoped styles for scrollbars and animations */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-5px); }
}

.animate-float {
  animation: float 4s ease-in-out infinite;
}

/* Smooth transitions */
* {
  transition-timing-function: cubic-bezier(0.16, 1, 0.3, 1);
}
</style>