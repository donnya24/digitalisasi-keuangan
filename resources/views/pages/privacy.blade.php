@extends('layouts.page')

@section('title', 'Kebijakan Privasi')

@section('content')
<div class="prose mx-auto">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Kebijakan Privasi</h1>
    <p class="text-sm text-gray-500 mb-8">Terakhir diperbarui: {{ date('d F Y') }}</p>

    <div class="bg-blue-50 p-4 rounded-lg mb-8">
        <p class="text-sm text-blue-800">
            <i class="fas fa-shield-alt mr-2"></i>
            Privasi Anda adalah prioritas kami. Kebijakan ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda.
        </p>
    </div>

    <h2>1. Informasi yang Kami Kumpulkan</h2>
    <p>Kami mengumpulkan beberapa jenis informasi untuk memberikan layanan terbaik kepada Anda:</p>
    
    <h3>a. Informasi Akun</h3>
    <ul>
        <li>Nama lengkap</li>
        <li>Alamat email</li>
        <li>Nama bisnis (warkop/usaha Anda)</li>
        <li>Nomor telepon (opsional)</li>
        <li>Kata sandi (dienkripsi)</li>
        <li>Foto profil (opsional)</li>
    </ul>

    <h3>b. Informasi Keuangan</h3>
    <ul>
        <li>Data pemasukan dan pengeluaran</li>
        <li>Kategori transaksi</li>
        <li>Catatan transaksi</li>
        <li>Data prive (penarikan pribadi)</li>
        <li>Saldo usaha</li>
    </ul>

    <h3>c. Informasi Teknis</h3>
    <ul>
        <li>Alamat IP</li>
        <li>Jenis browser dan perangkat</li>
        <li>Waktu akses</li>
        <li>Halaman yang dikunjungi</li>
        <li>Data cookie</li>
    </ul>

    <h2>2. Cara Kami Menggunakan Informasi</h2>
    <p>Informasi yang kami kumpulkan digunakan untuk:</p>
    <ul>
        <li><strong>Menyediakan Layanan:</strong> Memproses transaksi, membuat laporan, dan menampilkan dashboard keuangan.</li>
        <li><strong>Personalisasi:</strong> Menyesuaikan pengalaman Anda dengan menampilkan data dan grafik yang relevan.</li>
        <li><strong>Keamanan:</strong> Melindungi akun Anda dari akses tidak sah dan mencegah penipuan.</li>
        <li><strong>Komunikasi:</strong> Mengirim notifikasi, pembaruan layanan, dan informasi penting lainnya.</li>
        <li><strong>Peningkatan Layanan:</strong> Menganalisis penggunaan aplikasi untuk meningkatkan fitur dan kinerja.</li>
    </ul>

    <h2>3. Perlindungan Data</h2>
    <p>Kami menerapkan langkah-langkah keamanan untuk melindungi data Anda:</p>
    <ul>
        <li><i class="fas fa-lock text-green-600 mr-2"></i> <strong>Enkripsi:</strong> Semua data sensitif dienkripsi menggunakan algoritma standar industri.</li>
        <li><i class="fas fa-database text-green-600 mr-2"></i> <strong>Database Aman:</strong> Data disimpan di server dengan keamanan berlapis (Supabase).</li>
        <li><i class="fas fa-user-shield text-green-600 mr-2"></i> <strong>Akses Terbatas:</strong> Hanya pengguna yang memiliki akun yang dapat mengakses data mereka sendiri.</li>
    </ul>

    <h2>4. Penggunaan Cookie</h2>
    <p>
        Kami menggunakan cookie untuk meningkatkan pengalaman Anda. Cookie adalah file kecil yang disimpan di perangkat Anda yang membantu kami:
    </p>
    <ul>
        <li>Mengingat sesi login Anda</li>
        <li>Menyimpan preferensi tampilan</li>
        <li>Menganalisis lalu lintas pengguna</li>
    </ul>
    <p>Anda dapat mengatur browser untuk menolak cookie, namun beberapa fitur mungkin tidak berfungsi optimal.</p>

    <h2>5. Berbagi Data dengan Pihak Ketiga</h2>
    <p><strong>Kami TIDAK akan menjual, menyewakan, atau membagikan data pribadi Anda</strong> kepada pihak ketiga untuk tujuan pemasaran. Namun, kami mungkin membagikan data dalam situasi berikut:</p>
    <ul>
        <li><strong>Penyedia Layanan:</strong> Kami menggunakan layanan pihak ketiga (Supabase, Laravel) untuk hosting dan infrastruktur. Mereka hanya mengakses data untuk menyediakan layanan.</li>
        <li><strong>Kepatuhan Hukum:</strong> Jika diwajibkan oleh hukum, peraturan, atau proses hukum yang sah.</li>
        <li><strong>Perlindungan Hak:</strong> Untuk melindungi hak, properti, atau keselamatan kami atau orang lain.</li>
    </ul>

    <h2>6. Google OAuth</h2>
    <p>
        Kami menyediakan opsi login menggunakan Google OAuth. Saat Anda menggunakan fitur ini, Google akan membagikan informasi profil dasar (nama, email, foto profil) kepada kami. Data ini hanya digunakan untuk membuat dan mengelola akun Anda. Kami tidak menyimpan kata sandi Google Anda.
    </p>

    <h2>7. Retensi Data</h2>
    <p>
        Kami akan menyimpan data Anda selama akun Anda aktif. Jika Anda menghapus akun, data Anda akan dihapus secara permanen dalam waktu 30 hari. Data laporan keuangan yang telah diekspor menjadi tanggung jawab Anda setelah diunduh.
    </p>

    <h2>8. Hak Anda</h2>
    <p>Sebagai pengguna, Anda memiliki hak untuk:</p>
    <ul>
        <li><strong>Mengakses:</strong> Melihat semua data yang kami simpan tentang Anda.</li>
        <li><strong>Memperbaiki:</strong> Memperbarui informasi yang tidak akurat melalui menu profil.</li>
        <li><strong>Menghapus:</strong> Meminta penghapusan akun dan data Anda.</li>
        <li><strong>Mengekspor:</strong> Mendapatkan salinan data Anda dalam format PDF.</li>
    </ul>

    <h2>9. Keamanan Anak-anak</h2>
    <p>
        Layanan kami tidak ditujukan untuk anak-anak di bawah usia 13 tahun. Kami tidak sengaja mengumpulkan informasi dari anak-anak. Jika Anda orang tua/wali dan mengetahui anak Anda memberikan data kepada kami, hubungi kami untuk menghapus data tersebut.
    </p>

    <h2>10. Perubahan Kebijakan Privasi</h2>
    <p>
        Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Jika ada perubahan signifikan, kami akan memberitahu Anda melalui email atau notifikasi di aplikasi. Dengan terus menggunakan aplikasi setelah perubahan, Anda menyetujui kebijakan yang diperbarui.
    </p>

    <h2>11. Transfer Data Internasional</h2>
    <p>
        Data Anda disimpan di server Supabase yang berlokasi di Singapura (ap-southeast-1). Dengan menggunakan aplikasi kami, Anda menyetujui transfer data ke luar wilayah Anda.
    </p>

    <h2>12. Dasar Hukum</h2>
    <p>
        Kebijakan Privasi ini dibuat sesuai dengan:
    </p>
    <ul>
        <li>Undang-Undang No. 11 Tahun 2008 tentang Informasi dan Transaksi Elektronik (ITE)</li>
        <li>Undang-Undang No. 27 Tahun 2022 tentang Perlindungan Data Pribadi</li>
        <li>Peraturan Pemerintah No. 71 Tahun 2019 tentang Penyelenggaraan Sistem dan Transaksi Elektronik</li>
    </ul>

    <div class="bg-green-50 p-4 rounded-lg mt-8">
        <p class="text-sm text-green-800">
            <i class="fas fa-shield-alt text-green-600 mr-2"></i>
            Dengan menggunakan aplikasi KeuanganKu, Anda mempercayakan data Anda kepada kami. Kami berkomitmen untuk menjaga privasi Anda dengan standar keamanan tertinggi.
        </p>
    </div>
</div>
@endsection