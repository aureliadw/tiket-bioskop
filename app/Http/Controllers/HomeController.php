<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Film;

class HomeController extends Controller
{
    public function index()
    {
        $nowPlaying = Film::where('status', 'sedang_tayang')->take(4)->get();
        $comingSoon = Film::where('status', 'akan_tayang')->take(4)->get();

        return view('pelanggan.home', compact('nowPlaying', 'comingSoon'));
    }
}


