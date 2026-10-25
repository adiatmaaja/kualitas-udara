<?php

namespace App\Support;

class Aqi
{
    // Skala US AQI. 'lebar' dipakai untuk menggambar bilah skala (total 400).
    public const TINGKAT = [
        [
            'maks' => 50, 'lebar' => 50, 'label' => 'Baik',
            'warna' => '#2f7d55', 'teks' => '#ffffff',
            'saran' => 'Udara baik. Aktivitas di luar ruangan aman dilakukan.',
        ],
        [
            'maks' => 100, 'lebar' => 50, 'label' => 'Sedang',
            'warna' => '#e3b23c', 'teks' => '#2b1a0a',
            'saran' => 'Udara cukup baik. Orang yang sangat sensitif sebaiknya mengurangi aktivitas berat di luar ruangan.',
        ],
        [
            'maks' => 150, 'lebar' => 50, 'label' => 'Tidak sehat bagi kelompok sensitif',
            'warna' => '#e8833a', 'teks' => '#2b1a0a',
            'saran' => 'Anak-anak, lansia, dan penderita asma atau penyakit jantung sebaiknya mengurangi aktivitas berat di luar ruangan. Pertimbangkan memakai masker.',
        ],
        [
            'maks' => 200, 'lebar' => 50, 'label' => 'Tidak sehat',
            'warna' => '#c0392b', 'teks' => '#ffffff',
            'saran' => 'Semua orang mulai bisa terdampak. Kurangi aktivitas berat di luar ruangan dan pakai masker saat keluar.',
        ],
        [
            'maks' => 300, 'lebar' => 100, 'label' => 'Sangat tidak sehat',
            'warna' => '#7d4e6b', 'teks' => '#ffffff',
            'saran' => 'Hindari aktivitas di luar ruangan. Tetap di dalam ruangan dengan jendela tertutup.',
        ],
        [
            'maks' => 500, 'lebar' => 100, 'label' => 'Berbahaya',
            'warna' => '#5a1a2b', 'teks' => '#ffffff',
            'saran' => 'Tetap di dalam ruangan dan hindari semua aktivitas di luar.',
        ],
    ];

    public static function tingkat(?int $aqi): array
    {
        if ($aqi === null) {
            return [
                'maks' => 0, 'lebar' => 0, 'label' => 'Tidak ada data',
                'warna' => '#8a99a5', 'teks' => '#ffffff',
                'saran' => 'Data untuk lokasi ini belum tersedia.',
            ];
        }

        foreach (self::TINGKAT as $t) {
            if ($aqi <= $t['maks']) {
                return $t;
            }
        }

        return self::TINGKAT[count(self::TINGKAT) - 1];
    }
}