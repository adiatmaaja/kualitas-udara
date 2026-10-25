<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use App\Services\UdaraService;
use App\Support\Aqi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, UdaraService $udara)
    {
        $lokasi = Lokasi::orderBy('nama')->get();

        $data = $lokasi->isEmpty() ? null : $udara->ambil($lokasi);
        $kota = UdaraService::susun($data, $lokasi);
        $koleksi = collect($kota);

        // Kota yang dipilih lewat ?lokasi=ID, kalau tidak ada pakai yang pertama.
        $dipilih = $koleksi->firstWhere('id', $request->integer('lokasi')) ?? ($kota[0] ?? null);

        return view('dashboard', [
            'lokasiKosong' => $lokasi->isEmpty(),
            'galat' => $lokasi->isNotEmpty() && $kota === [],
            'kota' => $kota,
            'dipilih' => $dipilih,
            'skala' => Aqi::TINGKAT,
            'ambang' => collect(Aqi::TINGKAT)
                ->map(fn ($t) => ['maks' => $t['maks'], 'warna' => $t['warna']])
                ->values(),
            'namaKota' => $koleksi->pluck('nama')->values(),
            'aqiKota' => $koleksi->pluck('aqi')->values(),
            'warnaKota' => $koleksi->map(fn ($k) => $k['tingkat']['warna'])->values(),
            'diperbarui' => $dipilih
                ? Carbon::parse($dipilih['waktu'])->locale('id')->translatedFormat('j M Y, H:i').' WIB'
                : null,
        ]);
    }
}