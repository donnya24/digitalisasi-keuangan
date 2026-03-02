@extends('layouts.page')

@section('title', 'Syarat & Ketentuan')

@section('content')
<div class="prose mx-auto">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Syarat & Ketentuan</h1>
    <p class="text-sm text-gray-500 mb-8">Terakhir diperbarui: {{ date('d F Y') }}</p>

    <div class="bg-blue-50 p-4 rounded-lg mb-8">
        <p class="text-sm text-blue-800">
            <i class="fas fa-info-circle mr-2"></i>
            Mohon baca Syarat & Ketentuan ini dengan seksama sebelum menggunakan aplikasi KeuanganKu.
        </p>
    </div>

    <h2>1. Penerimaan Ketentuan</h2>
    <p>
        Dengan mengakses atau menggunakan aplikasi KeuanganKu ("aplikasi", "kami", "kita"), Anda menyetujui untuk terikat oleh Syarat & Ketentuan ini. Jika Anda tidak menyetujui bagian manapun dari ketentuan ini, Anda tidak diperbolehkan menggunakan aplikasi kami.
    </p>

    <h2>2. Deskripsi Layanan</h2>
    <p>
        KeuanganKu adalah aplikasi manajemen keuangan yang dirancang khusus untuk UMKM, khususnya usaha warung kopi (warkop) dan usaha kecil lainnya. Fitur-fitur yang tersedia meliputi:
    </p>
    <ul>
        <li>Pencatatan pemasukan dan pengeluaran</li>
        <li>Manajemen kategori transaksi</li>
        <li>Pembuatan laporan keuangan harian, mingguan, dan bulanan</li>
        <li>Fitur prive (pemisahan uang usaha dan pribadi)</li>
        <li>Ekspor laporan ke PDF</li>
        <li>Dashboard dengan grafik perkembangan usaha</li>
    </ul>

    <h2>3. Pendaftaran Akun</h2>
    <p>
        Untuk menggunakan layanan kami, Anda harus mendaftar dan membuat akun. Informasi yang Anda berikan harus akurat, lengkap, dan terkini. Anda bertanggung jawab penuh atas keamanan akun dan kata sandi Anda. Kami tidak bertanggung jawab atas kerugian yang timbul akibat penggunaan akun Anda oleh pihak lain.
    </p>

    <h2>4. Privasi dan Keamanan Data</h2>
    <p>
        Privasi data Anda sangat penting bagi kami. Semua data keuangan yang Anda masukkan ke dalam aplikasi akan disimpan dengan aman di database kami. Kami menggunakan enkripsi dan protokol keamanan standar industri untuk melindungi data Anda. Untuk informasi lebih lanjut, silakan lihat <a href="{{ route('privacy') }}">Kebijakan Privasi</a> kami.
    </p>

    <h2>5. Kewajiban Pengguna</h2>
    <p>Sebagai pengguna KeuanganKu, Anda setuju untuk:</p>
    <ul>
        <li>Tidak menyalahgunakan layanan untuk tujuan ilegal</li>
        <li>Tidak mencoba mengakses data pengguna lain</li>
        <li>Tidak melakukan aktivitas yang dapat merusak atau mengganggu sistem kami</li>
        <li>Bertanggung jawab atas semua aktivitas yang terjadi dalam akun Anda</li>
        <li>Menggunakan data yang dimasukkan sesuai dengan ketentuan hukum yang berlaku</li>
    </ul>

    <h2>6. Hak Kekayaan Intelektual</h2>
    <p>
        Seluruh konten, fitur, dan fungsionalitas aplikasi KeuanganKu (termasuk namun tidak terbatas pada kode, desain, logo, dan antarmuka) adalah milik kami dan dilindungi oleh hak cipta, merek dagang, dan hukum kekayaan intelektual lainnya. Anda tidak diperbolehkan menyalin, memodifikasi, mendistribusikan, atau membuat karya turunan tanpa izin tertulis dari kami.
    </p>

    <h2>7. Batasan Tanggung Jawab</h2>
    <p>
        KeuanganKu disediakan "sebagaimana adanya" tanpa jaminan apapun. Kami tidak menjamin bahwa aplikasi akan bebas dari gangguan, aman, atau bebas dari kesalahan. Dalam hal apapun, kami tidak bertanggung jawab atas kerugian langsung, tidak langsung, insidental, atau konsekuensial yang timbul dari penggunaan atau ketidakmampuan menggunakan aplikasi ini.
    </p>

    <h2>8. Penggunaan Data Keuangan</h2>
    <p>
        Data keuangan yang Anda masukkan adalah milik Anda. Kami hanya menggunakan data tersebut untuk:
    </p>
    <ul>
        <li>Memberikan layanan pencatatan keuangan</li>
        <li>Menghasilkan laporan dan grafik</li>
        <li>Meningkatkan kualitas layanan kami</li>
    </ul>
    <p>
        Kami tidak akan menjual atau membagikan data keuangan Anda kepada pihak ketiga tanpa persetujuan Anda, kecuali diwajibkan oleh hukum.
    </p>

    <h2>9. Pembatalan dan Penghapusan Akun</h2>
    <p>
        Anda dapat menghapus akun Anda kapan saja melalui menu pengaturan. Kami juga berhak untuk menangguhkan atau menghentikan akses Anda ke aplikasi jika kami mendeteksi pelanggaran terhadap ketentuan ini. Setelah akun dihapus, data Anda akan dihapus dari sistem kami dalam waktu 30 hari.
    </p>

    <h2>10. Perubahan Ketentuan</h2>
    <p>
        Kami dapat memperbarui Syarat & Ketentuan ini dari waktu ke waktu. Perubahan akan diumumkan melalui aplikasi atau email. Dengan terus menggunakan aplikasi setelah perubahan, Anda dianggap menyetujui perubahan tersebut.
    </p>

    <h2>11. Hukum yang Berlaku</h2>
    <p>
        Syarat & Ketentuan ini tunduk pada dan ditafsirkan sesuai dengan hukum yang berlaku di Republik Indonesia. Setiap sengketa yang timbul akan diselesaikan melalui musyawarah untuk mufakat, dan jika tidak tercapai, akan diselesaikan di Pengadilan Negeri setempat.
    </p>

    <div class="bg-gray-50 p-4 rounded-lg mt-8">
        <p class="text-sm text-gray-600">
            <i class="fas fa-check-circle text-green-600 mr-2"></i>
            Dengan menggunakan aplikasi KeuanganKu, Anda menyatakan telah membaca, memahami, dan menyetujui seluruh Syarat & Ketentuan yang berlaku.
        </p>
    </div>
</div>
@endsection