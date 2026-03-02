<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    /**
     * Menampilkan halaman Syarat & Ketentuan
     */
    public function terms()
    {
        return view('pages.terms');
    }

    /**
     * Menampilkan halaman Kebijakan Privasi
     */
    public function privacy()
    {
        return view('pages.privacy');
    }
}