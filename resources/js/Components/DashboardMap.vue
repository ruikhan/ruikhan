<script setup>
import { LMap, LMarker, LPopup, LTileLayer } from "@vue-leaflet/vue-leaflet";
import 'leaflet/dist/leaflet.css';
import { ref } from 'vue';

// Coordinates for Pili, Camarines Sur (Approximate)
const center = ref([13.58, 123.28]);
const zoom = ref(13);

// Mock Data for Incidents (In real app, pass this as props)
const incidents = [
    { id: 1, lat: 13.585, lng: 123.285, title: 'Road Repair', type: 'Infrastructure' },
    { id: 2, lat: 13.575, lng: 123.275, title: 'Uncollected Trash', type: 'Sanitation' },
];
</script>

<template>
    <div class="h-full w-full rounded-2xl overflow-hidden relative z-0">
        <l-map ref="map" v-model:zoom="zoom" :center="center" :use-global-leaflet="false">
            <l-tile-layer
                url="https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png"
                layer-type="base"
                name="Dark Map"
            ></l-tile-layer>

            <l-marker v-for="inc in incidents" :key="inc.id" :lat-lng="[inc.lat, inc.lng]">
                <l-popup>
                    <div class="text-slate-800">
                        <strong>{{ inc.title }}</strong><br>
                        Type: {{ inc.type }}
                    </div>
                </l-popup>
            </l-marker>
        </l-map>
        
        <div class="absolute top-4 left-4 z-[400] bg-slate-900/80 backdrop-blur px-3 py-1 rounded-lg border border-white/10 text-xs font-mono text-emerald-400">
            LIVE GIS FEED
        </div>
    </div>
</template>

<style>
/* Fix for Leaflet z-index issues in modals/dashboards */
.leaflet-pane { z-index: 0 !important; }
.leaflet-control-container .leaflet-top, .leaflet-bottom { z-index: 400 !important; }
</style>