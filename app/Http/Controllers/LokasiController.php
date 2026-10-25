<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    public function index()
    {
        $lokasi = Lokasi::orderBy('nama')->get();

        return view('lokasi.index', compact('lokasi'));
    }

    public function create()
    {
        return view('lokasi.form', ['lokasi' => null]);
    }

    public function store(Request $request)
    {
        Lokasi::create($this->aturan($request));

        return redirect()->route('lokasi.index')->with('success', 'Lokasi ditambahkan.');
    }

    public function edit(Lokasi $lokasi)
    {
        return view('lokasi.form', compact('lokasi'));
    }

    public function update(Request $request, Lokasi $lokasi)
    {
        $lokasi->update($this->aturan($request));

        return redirect()->route('lokasi.index')->with('success', 'Perubahan lokasi disimpan.');
    }

    public function destroy(Lokasi $lokasi)
    {
        $lokasi->delete();

        return redirect()->route('lokasi.index')->with('success', 'Lokasi dihapus.');
    }

    private function aturan(Request $request): array
    {
        return $request->validate([
            'nama' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);
    }
}