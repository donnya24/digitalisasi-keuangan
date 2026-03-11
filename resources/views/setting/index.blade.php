@extends('components.layout.app')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan Akun')

@section('content')
<div class="space-y-6">
    <!-- Tab Navigation -->
    @include('setting.partials.nav-tabs', ['activeTab' => $activeTab ?? 'profile'])

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            @foreach($errors->all() as $error)
                <p class="flex items-center text-sm"><i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Tab Contents -->
    <div id="tab-profile" class="tab-content {{ $activeTab == 'profile' ? '' : 'hidden' }}">
        @include('setting.partials.profile-form', ['user' => $user])
    </div>

    <div id="tab-business" class="tab-content {{ $activeTab == 'business' ? '' : 'hidden' }}">
        @include('setting.partials.business-form', ['business' => $business])
    </div>

    <div id="tab-password" class="tab-content {{ $activeTab == 'password' ? '' : 'hidden' }}">
        @include('setting.partials.password-form')
    </div>

    <div id="tab-account" class="tab-content {{ $activeTab == 'account' ? '' : 'hidden' }}">
        @include('setting.partials.account-info', ['user' => $user])
    </div>
</div>

<!-- Delete Account Modal -->
@include('setting.partials.delete-modal')

<script>
    // Tab switching
// Tab switching based on query string
function showTab(tabName) {
    // Update URL query string without reload
    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.pushState({}, '', url);
    
    // Update tab buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white');
        btn.classList.add('bg-gray-100', 'text-gray-700');
        
        if (btn.dataset.tab === tabName) {
            btn.classList.remove('bg-gray-100', 'text-gray-700');
            btn.classList.add('active', 'bg-blue-600', 'text-white');
        }
    });
    
    // Show selected tab content
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    document.getElementById(`tab-${tabName}`).classList.remove('hidden');
}

// On page load, read tab from query string
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab') || 'profile';
    showTab(tab);
});

    // Fungsi untuk preview gambar
    function previewImage(input) {
        const preview = document.getElementById('avatar-preview');
        const placeholder = document.getElementById('avatar-placeholder');
        const file = input.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (preview) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
            }
            reader.readAsDataURL(file);
        }
    }

    // Fungsi untuk preview logo
    function previewLogo(input) {
        const preview = document.getElementById('logo-preview');
        const placeholder = document.getElementById('logo-placeholder');
        const file = input.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (preview) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
            }
            reader.readAsDataURL(file);
        }
    }

    // Fungsi untuk suggested password
    function useSuggestedPassword(password) {
        const newPass = document.getElementById('new_password');
        const confirmPass = document.getElementById('new_password_confirmation');
        
        if (newPass && confirmPass) {
            newPass.value = password;
            confirmPass.value = password;
        }
    }

    // Fungsi untuk delete account modal
    function confirmDeleteAccount() {
        const modal = document.getElementById('deleteAccountModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function hideDeleteModal() {
        const modal = document.getElementById('deleteAccountModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    // Toggle password visibility (untuk modal delete)
    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(inputId + '_icon');
        
        if (input && icon) {
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideDeleteModal();
        }
    });

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('deleteAccountModal');
        if (modal && e.target === modal) {
            hideDeleteModal();
        }
    });
</script>

<style>
    .tab-button.active {
        background-color: #2563eb;
        color: white;
    }
    .tab-button:not(.active) {
        background-color: #f3f4f6;
        color: #374151;
    }
    .tab-button:not(.active):hover {
        background-color: #e5e7eb;
    }
</style>
@endsection