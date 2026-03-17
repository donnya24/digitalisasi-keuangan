<div id="loading-spinner" class="fixed inset-0 flex items-center justify-center z-[99999] hidden" style="background-color: rgba(0,0,0,0.5); backdrop-filter: blur(3px);">
    <div class="bg-white rounded-2xl p-6 sm:p-8 flex flex-col items-center shadow-2xl transform scale-90 sm:scale-100">
        <div class="w-16 h-16 sm:w-20 sm:h-20 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mb-3 sm:mb-4"></div>
        <p class="text-gray-700 font-medium text-sm sm:text-base">Memuat...</p>
        <p class="text-gray-400 text-xs mt-1 sm:mt-2">Mohon tunggu sebentar</p>
    </div>
</div>

<style>
    .animate-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Pastikan spinner selalu di atas */
    #loading-spinner {
        pointer-events: none;
    }

    #loading-spinner:not(.hidden) {
        pointer-events: auto;
    }
</style>
