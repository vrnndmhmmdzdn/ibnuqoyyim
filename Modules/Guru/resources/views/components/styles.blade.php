<style>
    .custom-guru-marker {
        background: transparent !important;
        border: none !important;
    }
    
    .custom-popup .leaflet-popup-content-wrapper {
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .custom-popup .leaflet-popup-tip {
        background: white;
    }
    
    /* Fix untuk floating action button */
    #map-guru {
        position: relative;
        z-index: 1;
    }
    
    /* Pastikan floating action button tidak tertutup peta */
    .fi-floating-action-button,
    [data-floating-action-button] {
        z-index: 1000 !important;
        position: fixed !important;
    }
    
    /* Leaflet controls positioning */
    .leaflet-control-container {
        z-index: 999;
    }
    
    .leaflet-control {
        z-index: 999;
    }
    
    /* Dark mode untuk Leaflet map */
    .dark .leaflet-container {
        background: #374151;
    }
    
    .dark .leaflet-tile {
        filter: brightness(0.6) invert(1) contrast(3) hue-rotate(200deg) saturate(0.3) brightness(0.7);
    }
    
    .dark .leaflet-control-zoom a {
        background-color: #374151 !important;
        color: #f3f4f6 !important;
        border-color: #4b5563 !important;
    }
    
    .dark .leaflet-control-zoom a:hover {
        background-color: #4b5563 !important;
    }
    
    .dark .leaflet-popup-content-wrapper {
        background-color: #374151 !important;
        color: #f3f4f6 !important;
    }
    
    .dark .leaflet-popup-tip {
        background-color: #374151 !important;
    }
    
    /* Animasi glow orange untuk elemen premium */
    @keyframes glow-orange { 
        0%, 100% { box-shadow: 0 0 20px rgba(249, 115, 22, 0.5); } 
        50% { box-shadow: 0 0 30px rgba(249, 115, 22, 0.8); } 
    }
    
    @keyframes glow-orange-soft { 
        0%, 100% { box-shadow: 0 0 15px rgba(249, 115, 22, 0.3); } 
        50% { box-shadow: 0 0 25px rgba(249, 115, 22, 0.6); } 
    }
    
    @keyframes pulse-orange {
        0%, 100% { 
            background-color: rgba(249, 115, 22, 0.1);
            border-color: rgba(249, 115, 22, 0.3);
        }
        50% { 
            background-color: rgba(249, 115, 22, 0.2);
            border-color: rgba(249, 115, 22, 0.5);
        }
    }
    
    /* Efek glow untuk search button */
    .search-btn-glow {
        animation: glow-orange 2s ease-in-out infinite;
        background: linear-gradient(135deg, #f97316, #ea580c);
        border: none;
        transition: all 0.3s ease;
    }
    
    .search-btn-glow:hover {
        animation: glow-orange-soft 1s ease-in-out infinite;
        transform: translateY(-2px);
    }
    
    /* Efek glow untuk guru cards premium */
    .guru-card-premium {
        animation: pulse-orange 3s ease-in-out infinite;
        border: 2px solid rgba(249, 115, 22, 0.3);
        position: relative;
        overflow: hidden;
        background: white;
        transition: all 0.3s ease;
    }
    
    .guru-card-premium:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px rgba(249, 115, 22, 0.2);
        border-color: rgba(249, 115, 22, 0.6);
    }
    
    .guru-card-premium::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(45deg, #f97316, #ea580c, #f97316);
        z-index: -1;
        border-radius: inherit;
        animation: glow-orange-soft 2s ease-in-out infinite;
    }
    
    .guru-card-premium:hover::before {
        animation: glow-orange 1.5s ease-in-out infinite;
    }
    
    /* Badge premium dengan glow */
    .premium-badge {
        background: linear-gradient(135deg, #f97316, #ea580c);
        animation: glow-orange-soft 2s ease-in-out infinite;
        color: white;
        font-weight: bold;
        text-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    }
    
    /* Rating stars dengan efek orange */
    .rating-star-glow {
        color: #f97316;
        filter: drop-shadow(0 0 3px rgba(249, 115, 22, 0.6));
        animation: glow-orange-soft 3s ease-in-out infinite;
    }
    
    /* Map toggle button dengan glow */
    .map-toggle-glow {
        background: linear-gradient(135deg, #f97316, #ea580c);
        animation: glow-orange-soft 2.5s ease-in-out infinite;
        border: 2px solid rgba(249, 115, 22, 0.4);
    }
    
    .map-toggle-glow:hover {
        animation: glow-orange 1.5s ease-in-out infinite;
        transform: scale(1.05);
    }
    
    /* Price highlight dengan glow */
    .price-highlight {
        background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), rgba(234, 88, 12, 0.1));
        border: 1px solid rgba(249, 115, 22, 0.3);
        animation: pulse-orange 2.5s ease-in-out infinite;
        border-radius: 8px;
        padding: 8px 12px;
    }
    
    /* Floating action button dengan glow orange */
    .fab-glow {
        animation: glow-orange 2s ease-in-out infinite;
        background: linear-gradient(135deg, #f97316, #ea580c) !important;
    }
    
    /* Dark mode variants */
    .dark .guru-card-premium {
        background-color: #374151 !important;
        border-color: rgba(249, 115, 22, 0.4);
    }
    
    .dark .guru-card-premium:hover {
        background-color: #4b5563 !important;
        box-shadow: 0 20px 40px rgba(249, 115, 22, 0.3);
        border-color: rgba(249, 115, 22, 0.8);
    }
    
    .dark .guru-card-premium::before {
        background: linear-gradient(45deg, rgba(249, 115, 22, 0.6), rgba(234, 88, 12, 0.6), rgba(249, 115, 22, 0.6));
    }
    
    .dark .guru-card-premium:hover::before {
        background: linear-gradient(45deg, rgba(249, 115, 22, 0.8), rgba(234, 88, 12, 0.8), rgba(249, 115, 22, 0.8));
    }
    
    .dark .price-highlight {
        background: linear-gradient(135deg, rgba(249, 115, 22, 0.15), rgba(234, 88, 12, 0.15));
        border-color: rgba(249, 115, 22, 0.4);
    }
    
    .dark .price-highlight:hover {
        background: linear-gradient(135deg, rgba(249, 115, 22, 0.25), rgba(234, 88, 12, 0.25));
        border-color: rgba(249, 115, 22, 0.6);
    }
    
    /* Premium badge dark mode */
    .dark .premium-badge {
        background: linear-gradient(135deg, rgba(249, 115, 22, 0.8), rgba(234, 88, 12, 0.8)) !important;
        color: white !important;
    }
    
    /* Rating stars dark mode */
    .dark .rating-star-glow {
        color: #f97316;
        filter: drop-shadow(0 0 5px rgba(249, 115, 22, 0.8));
    }
</style>