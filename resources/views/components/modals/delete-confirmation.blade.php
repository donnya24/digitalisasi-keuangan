@props(['route', 'title' => 'Hapus Data', 'message' => 'Apakah Anda yakin ingin menghapus data ini?'])

<form method="POST" action="{{ $route }}" class="hidden delete-form">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    // Fungsi global untuk menampilkan modal delete
    window.showDeleteModal = function(route, title, message) {
        console.log('Delete button clicked!', { route, title, message });

        // Cari form dengan action yang sesuai
        const form = document.querySelector(`form[action="${route}"]`);

        if (!form) {
            console.error('Form not found for route:', route);
            alert('Error: Form tidak ditemukan!');
            return;
        }

        console.log('Form found:', form);

        // Cek apakah SweetAlert2 tersedia
        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 not loaded!');
            // Fallback ke confirm biasa
            if (confirm(title + '\n\n' + message)) {
                form.submit();
            }
            return;
        }

        Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise((resolve) => {
                    console.log('Submitting form...');
                    form.submit();
                    resolve();
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        });
    };

    // Debug: cek apakah fungsi tersedia
    console.log('showDeleteModal function defined:', typeof window.showDeleteModal);
</script>
@endpush
