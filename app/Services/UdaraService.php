<?php

namespace App\Services;

use App\Support\Aqi;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class UdaraService
{
    private const URL = 'https://air-quality-api.open-meteo.com/v1/air-quality';

    /**
     * Ambil data semua lokasi dalam satu panggilan, disimpan 30 menit.
     * Mengembalikan null kalau Open-Meteo tidak bisa dihubungi.
     */
    public function ambil($lokasi): ?array
    {
        $lat = $lokasi->pluck('latitude')->implode(',');
        $lon = $lokasi->pluck('longitude')->implode(',');

        try {
            return Cache::remember(
                'udara_'.md5($lat.'|'.$lon),
                now()->addMinutes(30),
                function () use ($lat, $lon) {
                    $respons = Http::timeout(10)->get(self::URL, [
                        'latitude' => $lat,
                        'longitude' => $lon,
                        'current' => 'us_aqi,pm2_5,pm10,nitrogen_dioxide,ozone,carbon_monoxide,sulphur_dioxide',
                        'hourly' => 'us_aqi',
                        'past_days' => 1,
                        'forecast_days' => 1,
                        'timezone' => 'Asia/Jakarta',
                    ]);

                    $respons->throw();

                    return $respons->json();
                }
            );
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Ubah respons Open-Meteo menjadi daftar kota yang siap ditampilkan.
     * Urutan hasil API sama dengan urutan koordinat yang dikirim.
     */
    public static function susun(?array $data, iterable $lokasi): array
    {
        if (! $data) {
            return [];
        }

        // Satu lokasi dikembalikan sebagai objek, beberapa lokasi sebagai daftar.
        if (isset($data['current'])) {
            $data = [$data];
        }

        $kota = [];
        $i = 0;

        foreach ($lokasi as $l) {
            $d = $data[$i++] ?? null;

            if (! is_array($d) || ! isset($d['current'])) {
                continue;
            }

            $c = $d['current'];
            $aqi = isset($c['us_aqi']) ? (int) round($c['us_aqi']) : null;

            // Hanya jam yang sudah lewat, lalu ambil 24 terakhir.
            $jam = [];
            $nilai = [];
            foreach ($d['hourly']['time'] ?? [] as $k => $t) {
                if ($t <= $c['time']) {
                    $jam[] = substr($t, 11, 5);
                    $v = $d['hourly']['us_aqi'][$k] ?? null;
                    $nilai[] = $v === null ? null : (int) round($v);
                }
            }

            $kota[] = [
                'id' => $l->id,
                'nama' => $l->nama,
                'aqi' => $aqi,
                'tingkat' => Aqi::tingkat($aqi),
                'waktu' => $c['time'],
                'polutan' => [
                    'PM2.5' => $c['pm2_5'] ?? null,
                    'PM10' => $c['pm10'] ?? null,
                    'NO2' => $c['nitrogen_dioxide'] ?? null,
                    'Ozon' => $c['ozone'] ?? null,
                    'CO' => $c['carbon_monoxide'] ?? null,
                    'SO2' => $c['sulphur_dioxide'] ?? null,
                ],
                'jam' => array_slice($jam, -24),
                'nilaiJam' => array_slice($nilai, -24),
            ];
        }

        return $kota;
    }
}