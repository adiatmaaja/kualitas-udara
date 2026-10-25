@extends('layouts.app')

@section('title', 'Lokasi')

@section('content')
    <div class="page-head">
        <h1>Lokasi</h1>
        <a href="{{ route('lokasi.create') }}" class="btn">Tambah lokasi</a>
    </div>

    @if ($lokasi->isEmpty())
        <p class="empty">Belum ada lokasi. Tambahkan kota pertama, lengkap dengan koordinatnya.</p>
    @else
        <p class="catatan">Dashboard membaca daftar ini. Koordinat yang diubah langsung dipakai pada pemuatan berikutnya.</p>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th class="num">Latitude</th>
                        <th class="num">Longitude</th>
                        <th class="aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lokasi as $item)
                        <tr>
                            <td>{{ $item->nama }}</td>
                            <td class="num">{{ $item->latitude }}</td>
                            <td class="num">{{ $item->longitude }}</td>
                            <td class="aksi">
                                <a href="{{ route('lokasi.edit', $item) }}" class="link">Ubah</a>
                                <form method="post" action="{{ route('lokasi.destroy', $item) }}" class="inline"
                                      onsubmit="return confirm('Hapus lokasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="link link-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection