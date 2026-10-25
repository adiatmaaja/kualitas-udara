@extends('layouts.app')

@section('title', $lokasi ? 'Ubah lokasi' : 'Tambah lokasi')

@section('content')
    <div class="page-head">
        <h1>{{ $lokasi ? 'Ubah lokasi' : 'Tambah lokasi' }}</h1>
    </div>

    <form method="post"
          action="{{ $lokasi ? route('lokasi.update', $lokasi) : route('lokasi.store') }}"
          class="form">
        @csrf
        @if ($lokasi)
            @method('PUT')
        @endif

        <label for="nama">Nama lokasi</label>
        <input type="text" id="nama" name="nama" value="{{ old('nama', $lokasi->nama ?? '') }}" required>

        <label for="latitude">Latitude</label>
        <input type="number" id="latitude" name="latitude" step="any" min="-90" max="90"
               value="{{ old('latitude', $lokasi->latitude ?? '') }}" placeholder="-6.2088" required>

        <label for="longitude">Longitude</label>
        <input type="number" id="longitude" name="longitude" step="any" min="-180" max="180"
               value="{{ old('longitude', $lokasi->longitude ?? '') }}" placeholder="106.8456" required>

        <div class="form-actions">
            <button type="submit" class="btn">{{ $lokasi ? 'Simpan perubahan' : 'Tambah lokasi' }}</button>
            <a href="{{ route('lokasi.index') }}" class="link">Batal</a>
        </div>
    </form>
@endsection