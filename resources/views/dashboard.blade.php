@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @if ($lokasiKosong)
        <p class="empty">Belum ada lokasi. <a href="{{ route('lokasi.create') }}" class="link">Tambah lokasi</a> dulu supaya data udaranya bisa diambil.</p>
    @elseif ($galat)
        <p class="notice notice-error" role="alert">Data dari Open-Meteo belum bisa diambil. Periksa koneksi internet, lalu muat ulang halaman.</p>
    @elseif ($dipilih)
        @php
            $posisi = min($dipilih['aqi'] ?? 0, 400) / 400 * 100;
        @endphp

        <section class="utama" aria-label="Kualitas udara saat ini">
            <div class="utama-kiri">
                <form method="get" action="{{ route('dashboard') }}" class="pilih">
                    <label for="lokasi">Lokasi</label>
                    <select id="lokasi" name="lokasi" onchange="this.form.submit()">
                        @foreach ($kota as $k)
                            <option value="{{ $k['id'] }}" @selected($k['id'] === $dipilih['id'])>{{ $k['nama'] }}</option>
                        @endforeach
                    </select>
                    <noscript><button type="submit" class="btn">Tampilkan</button></noscript>
                </form>

                <p class="aqi-angka">{{ $dipilih['aqi'] ?? '-' }}</p>
                <p class="aqi-baris">
                    <span class="badge" style="background: {{ $dipilih['tingkat']['warna'] }}; color: {{ $dipilih['tingkat']['teks'] }}">{{ $dipilih['tingkat']['label'] }}</span>
                    <span class="samar">US AQI</span>
                </p>

                <div class="skala" role="img" aria-label="Posisi AQI pada skala 0 sampai 400">
                    <div class="skala-bar">
                        @foreach ($skala as $t)
                            <span style="flex: {{ $t['lebar'] }}; background: {{ $t['warna'] }}"></span>
                        @endforeach
                    </div>
                    <span class="skala-penanda" style="left: {{ $posisi }}%"></span>
                </div>

                <p class="saran">{{ $dipilih['tingkat']['saran'] }}</p>
            </div>

            <dl class="polutan">
                @foreach ($dipilih['polutan'] as $nama => $nilai)
                    <div>
                        <dt>{{ $nama }}</dt>
                        <dd>{{ $nilai ?? '-' }} <small>µg/m³</small></dd>
                    </div>
                @endforeach
            </dl>
        </section>

        <div class="charts">
            <section class="panel">
                <h2>AQI 24 jam terakhir di {{ $dipilih['nama'] }}</h2>
                <p class="catatan">Warna garis mengikuti tingkat AQI pada jam itu</p>
                <div class="chart-holder">
                    <canvas id="chartJam" role="img" aria-label="Grafik garis AQI 24 jam terakhir"></canvas>
                </div>
            </section>

            <section class="panel">
                <h2>Perbandingan AQI antar kota</h2>
                <p class="catatan">Nilai saat ini, warna sesuai tingkat AQI</p>
                <div class="chart-holder">
                    <canvas id="chartKota" role="img" aria-label="Grafik batang AQI tiap kota"></canvas>
                </div>
            </section>
        </div>

        <p class="sumber">
            Sumber data: <a href="https://open-meteo.com/" class="link">Open-Meteo</a> (model CAMS, lisensi CC BY 4.0).
            Diperbarui {{ $diperbarui }}. Angka berasal dari model atmosfer, bukan sensor di tiap kota.
        </p>
    @endif
@endsection

@if ($dipilih)
    @push('scripts')
        <script src="{{ asset('assets/js/chart.js') }}"></script>
        <script>
            const ambang = @json($ambang);
            const warnaAqi = (v) => (ambang.find((t) => v <= t.maks) || ambang[ambang.length - 1]).warna;

            Chart.defaults.font.family = 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif';
            Chart.defaults.color = '#5b6b78';
            Chart.defaults.animation = window.matchMedia('(prefers-reduced-motion: reduce)').matches
                ? false
                : { duration: 600 };

            new Chart(document.getElementById('chartJam'), {
                type: 'line',
                data: {
                    labels: @json($dipilih['jam']),
                    datasets: [{
                        label: 'US AQI',
                        data: @json($dipilih['nilaiJam']),
                        borderColor: '#17405c',
                        borderWidth: 3,
                        tension: 0.3,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        spanGaps: true,
                        segment: { borderColor: (c) => warnaAqi(c.p1.parsed.y) },
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 8 } },
                        y: { beginAtZero: true, suggestedMax: 100, grid: { color: '#e3e9ed' } },
                    },
                },
            });

            new Chart(document.getElementById('chartKota'), {
                type: 'bar',
                data: {
                    labels: @json($namaKota),
                    datasets: [{
                        label: 'US AQI',
                        data: @json($aqiKota),
                        backgroundColor: @json($warnaKota),
                        borderRadius: 3,
                    }],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, grid: { color: '#e3e9ed' } },
                        y: { grid: { display: false } },
                    },
                },
            });
        </script>
    @endpush
@endif