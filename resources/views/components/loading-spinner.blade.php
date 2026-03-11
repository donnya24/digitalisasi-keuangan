<div id="loading-spinner" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-[9999] hidden transition-opacity duration-300">
    <div class="bg-white rounded-lg p-6 flex flex-col items-center shadow-xl">
        <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mb-4"></div>
        <p class="text-gray-700 font-medium">Memuat...</p>
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
</style>