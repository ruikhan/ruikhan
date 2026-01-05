<script setup>
import { LMap, LMarker, LPopup, LTileLayer } from "@vue-leaflet/vue-leaflet";
import 'leaflet/dist/leaflet.css';
import { onMounted, ref } from 'vue';

// Coordinates for Pili, Camarines Sur (Approximate)
const center = ref([13.58, 123.28]);
const zoom = ref(13);
const isLoading = ref(true);
const activeIncidents = ref(0);

// Mock Data for Incidents (In real app, pass this as props)
const incidents = ref([
    { id: 1, lat: 13.585, lng: 123.285, title: 'Road Repair', type: 'Infrastructure', severity: 'medium', status: 'active' },
    { id: 2, lat: 13.575, lng: 123.275, title: 'Uncollected Trash', type: 'Sanitation', severity: 'low', status: 'active' },
    { id: 3, lat: 13.580, lng: 123.290, title: 'Street Light Out', type: 'Utilities', severity: 'low', status: 'resolved' },
]);

// Calculate active incidents
activeIncidents.value = incidents.value.filter(i => i.status === 'active').length;

onMounted(() => {
    // Simulate loading
    setTimeout(() => {
        isLoading.value = false;
    }, 1000);
});

const getSeverityColor = (severity) => {
    switch (severity) {
        case 'high': return 'bg-red-500';
        case 'medium': return 'bg-yellow-500';
        case 'low': return 'bg-blue-500';
        default: return 'bg-gray-500';
    }
};
</script>

<template>
    <div class="map-container">
        <!-- Enhanced Glass Container -->
        <div class="map-wrapper">
            <div class="map-glass"></div>
            <div class="map-border-glow"></div>
            
            <!-- Loading State -->
            <Transition name="loading">
                <div v-if="isLoading" class="map-loading">
                    <div class="loading-spinner">
                        <div class="spinner-ring"></div>
                        <div class="spinner-ring"></div>
                        <div class="spinner-ring"></div>
                    </div>
                    <p class="loading-text">Loading Map Data...</p>
                </div>
            </Transition>
            
            <!-- Map Content -->
            <div class="map-content" :class="{ 'map-hidden': isLoading }">
                <l-map 
                    ref="map" 
                    v-model:zoom="zoom" 
                    :center="center" 
                    :use-global-leaflet="false"
                    class="leaflet-map"
                >
                    <l-tile-layer
                        url="https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png"
                        layer-type="base"
                        name="Dark Map"
                        attribution="&copy; CartoDB"
                    ></l-tile-layer>

                    <l-marker 
                        v-for="inc in incidents" 
                        :key="inc.id" 
                        :lat-lng="[inc.lat, inc.lng]"
                    >
                        <l-popup>
                            <div class="popup-content">
                                <div class="popup-header">
                                    <h4>{{ inc.title }}</h4>
                                    <span :class="['status-badge', inc.status === 'active' ? 'status-active' : 'status-resolved']">
                                        {{ inc.status }}
                                    </span>
                                </div>
                                <div class="popup-body">
                                    <div class="popup-row">
                                        <span class="popup-label">Type:</span>
                                        <span class="popup-value">{{ inc.type }}</span>
                                    </div>
                                    <div class="popup-row">
                                        <span class="popup-label">Severity:</span>
                                        <span :class="['severity-badge', getSeverityColor(inc.severity)]">
                                            {{ inc.severity }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </l-popup>
                    </l-marker>
                </l-map>
            </div>
            
            <!-- Enhanced Live Badge -->
            <div class="live-badge">
                <div class="badge-bg"></div>
                <div class="badge-content">
                    <span class="live-pulse">
                        <span class="pulse-ring"></span>
                        <span class="pulse-dot"></span>
                    </span>
                    <span class="badge-text">LIVE GIS FEED</span>
                </div>
            </div>
            
            <!-- Statistics Card -->
            <div class="stats-card">
                <div class="stats-bg"></div>
                <div class="stats-content">
                    <div class="stat-item">
                        <span class="stat-icon">📍</span>
                        <div class="stat-info">
                            <span class="stat-value">{{ incidents.length }}</span>
                            <span class="stat-label">Total</span>
                        </div>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <span class="stat-icon">🚨</span>
                        <div class="stat-info">
                            <span class="stat-value">{{ activeIncidents }}</span>
                            <span class="stat-label">Active</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Map Controls -->
            <div class="map-controls">
                <button class="control-btn" title="Refresh">
                    <svg class="control-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
                <button class="control-btn" title="Center Map">
                    <svg class="control-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.map-container {
    height: 100%;
    width: 100%;
    position: relative;
}

.map-wrapper {
    position: relative;
    height: 100%;
    width: 100%;
    border-radius: 1.5rem;
    overflow: hidden;
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.8),
        0 0 80px rgba(59, 130, 246, 0.1);
}

/* Glass morphism */
.map-glass {
    position: absolute;
    inset: 0;
    background: rgba(10, 10, 12, 0.4);
    backdrop-filter: blur(10px);
    z-index: 1;
    pointer-events: none;
}

.map-border-glow {
    position: absolute;
    inset: -2px;
    border-radius: 1.5rem;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(168, 85, 247, 0.2));
    filter: blur(12px);
    opacity: 0.4;
    z-index: 0;
    animation: border-pulse 4s ease-in-out infinite;
}

@keyframes border-pulse {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.6; }
}

/* Loading State */
.map-loading {
    position: absolute;
    inset: 0;
    z-index: 500;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1.5rem;
    background: rgba(10, 10, 12, 0.95);
    backdrop-filter: blur(20px);
}

.loading-spinner {
    position: relative;
    width: 4rem;
    height: 4rem;
}

.spinner-ring {
    position: absolute;
    inset: 0;
    border: 3px solid transparent;
    border-radius: 50%;
    animation: spin 2s linear infinite;
}

.spinner-ring:nth-child(1) {
    border-top-color: #3b82f6;
    animation-duration: 1.5s;
}

.spinner-ring:nth-child(2) {
    border-right-color: #a855f7;
    animation-duration: 2s;
    animation-direction: reverse;
}

.spinner-ring:nth-child(3) {
    border-bottom-color: #06b6d4;
    animation-duration: 2.5s;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.loading-text {
    font-size: 0.875rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.7);
    letter-spacing: 0.05em;
}

.loading-enter-active,
.loading-leave-active {
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.loading-enter-from,
.loading-leave-to {
    opacity: 0;
}

/* Map Content */
.map-content {
    position: relative;
    height: 100%;
    width: 100%;
    z-index: 2;
    transition: opacity 0.5s;
}

.map-hidden {
    opacity: 0;
}

.leaflet-map {
    height: 100%;
    width: 100%;
    border-radius: 1.5rem;
}

/* Enhanced Live Badge */
.live-badge {
    position: absolute;
    top: 1rem;
    left: 1rem;
    z-index: 450;
    overflow: hidden;
    border-radius: 0.875rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
}

.badge-bg {
    position: absolute;
    inset: 0;
    background: rgba(10, 10, 12, 0.85);
    backdrop-filter: blur(40px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.badge-content {
    position: relative;
    z-index: 10;
    display: flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.625rem 1rem;
}

.live-pulse {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 0.75rem;
    height: 0.75rem;
}

.pulse-ring {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background: #10b981;
    animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}

@keyframes ping {
    0% { transform: scale(0.8); opacity: 1; }
    80%, 100% { transform: scale(2.5); opacity: 0; }
}

.pulse-dot {
    position: relative;
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 15px rgba(16, 185, 129, 1);
}

.badge-text {
    font-size: 0.6875rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: #6ee7b7;
    text-transform: uppercase;
}

/* Statistics Card */
.stats-card {
    position: absolute;
    bottom: 1rem;
    left: 1rem;
    z-index: 450;
    overflow: hidden;
    border-radius: 1rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
}

.stats-bg {
    position: absolute;
    inset: 0;
    background: rgba(10, 10, 12, 0.85);
    backdrop-filter: blur(40px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.stats-content {
    position: relative;
    z-index: 10;
    display: flex;
    gap: 1rem;
    padding: 0.75rem 1rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.stat-icon {
    font-size: 1.25rem;
}

.stat-info {
    display: flex;
    flex-direction: column;
}

.stat-value {
    font-size: 1.125rem;
    font-weight: 700;
    color: white;
    line-height: 1;
}

.stat-label {
    font-size: 0.625rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.5);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stat-divider {
    width: 1px;
    background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, 0.2), transparent);
}

/* Map Controls */
.map-controls {
    position: absolute;
    top: 1rem;
    right: 1rem;
    z-index: 450;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.control-btn {
    position: relative;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(10, 10, 12, 0.85);
    backdrop-filter: blur(40px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.7);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
    overflow: hidden;
}

.control-btn::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.05);
    opacity: 0;
    transition: opacity 0.3s;
}

.control-btn:hover::before {
    opacity: 1;
}

.control-btn:hover {
    color: white;
    border-color: rgba(255, 255, 255, 0.2);
    transform: scale(1.1);
}

.control-btn:active {
    transform: scale(0.95);
}

.control-icon {
    width: 1.125rem;
    height: 1.125rem;
    position: relative;
    z-index: 10;
}

/* Custom Popup Styling */
:deep(.leaflet-popup-content-wrapper) {
    background: rgba(10, 10, 12, 0.95);
    backdrop-filter: blur(40px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 1rem;
    padding: 0;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
}

:deep(.leaflet-popup-tip) {
    background: rgba(10, 10, 12, 0.95);
}

.popup-content {
    padding: 1rem;
}

.popup-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.popup-header h4 {
    font-size: 0.875rem;
    font-weight: 700;
    color: white;
}

.status-badge {
    padding: 0.25rem 0.625rem;
    border-radius: 9999px;
    font-size: 0.625rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-active {
    background: rgba(239, 68, 68, 0.15);
    color: rgb(252, 165, 165);
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.status-resolved {
    background: rgba(34, 197, 94, 0.15);
    color: rgb(134, 239, 172);
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.popup-body {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.popup-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.popup-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.6);
}

.popup-value {
    font-size: 0.75rem;
    font-weight: 600;
    color: white;
}

.severity-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.625rem;
    font-weight: 700;
    color: white;
    text-transform: uppercase;
}

/* Fix for Leaflet z-index issues */
:deep(.leaflet-pane) { 
    z-index: 0 !important; 
}

:deep(.leaflet-control-container .leaflet-top),
:deep(.leaflet-control-container .leaflet-bottom) { 
    z-index: 400 !important; 
}

:deep(.leaflet-popup-pane) {
    z-index: 700 !important;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .live-badge,
    .stats-card {
        left: 0.75rem;
    }
    
    .map-controls {
        right: 0.75rem;
        top: 0.75rem;
    }
    
    .control-btn {
        width: 2.25rem;
        height: 2.25rem;
    }
    
    .badge-content {
        padding: 0.5rem 0.75rem;
    }
    
    .badge-text {
        font-size: 0.625rem;
    }
}

/* Reduce motion */
@media (prefers-reduced-motion: reduce) {
    .pulse-ring,
    .spinner-ring {
        animation: none !important;
    }
}
</style>