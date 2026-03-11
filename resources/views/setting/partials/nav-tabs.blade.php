@props(['activeTab' => 'profile'])

<div class="bg-white rounded-lg shadow-sm p-1 flex flex-wrap gap-1">
    <a href="{{ route('setting.index', ['tab' => 'profile']) }}" 
       class="tab-button px-4 py-2 text-sm font-medium rounded-lg transition-colors inline-flex items-center
              {{ $activeTab == 'profile' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
       onclick="event.preventDefault(); showTab('profile');">
        <i class="fas fa-user mr-2"></i> Profil Saya
    </a>
    
    <a href="{{ route('setting.index', ['tab' => 'business']) }}" 
       class="tab-button px-4 py-2 text-sm font-medium rounded-lg transition-colors inline-flex items-center
              {{ $activeTab == 'business' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
       onclick="event.preventDefault(); showTab('business');">
        <i class="fas fa-store mr-2"></i> Profil Usaha
    </a>
    
    <a href="{{ route('setting.index', ['tab' => 'password']) }}" 
       class="tab-button px-4 py-2 text-sm font-medium rounded-lg transition-colors inline-flex items-center
              {{ $activeTab == 'password' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
       onclick="event.preventDefault(); showTab('password');">
        <i class="fas fa-lock mr-2"></i> Ubah Password
    </a>
    
    <a href="{{ route('setting.index', ['tab' => 'account']) }}" 
       class="tab-button px-4 py-2 text-sm font-medium rounded-lg transition-colors inline-flex items-center
              {{ $activeTab == 'account' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
       onclick="event.preventDefault(); showTab('account');">
        <i class="fas fa-shield-alt mr-2"></i> Informasi Akun
    </a>
</div>