<?php

declare(strict_types=1);

namespace App\Support;

final class Labels
{
    public static function applicationTypes(): array
    {
        return ['INTERN' => 'Internship', 'VOLUNTEER' => 'Relawan'];
    }

    public static function applicationStatuses(): array
    {
        return ['NEW' => 'Baru', 'REVIEWED' => 'Ditinjau', 'ACCEPTED' => 'Diterima', 'REJECTED' => 'Ditolak'];
    }

    public static function occupations(): array
    {
        return [
            'STUDENT' => 'Mahasiswa',
            'FRESH_GRADUATE' => 'Fresh Graduate',
            'EMPLOYEE' => 'Karyawan / Profesional',
            'ENTREPRENEUR' => 'Wiraswasta',
            'OTHER' => 'Lainnya',
        ];
    }

    public static function durations(): array
    {
        return ['TWO_WEEKS' => '2 minggu', 'ONE_MONTH' => '1 bulan', 'FLEXIBLE' => 'Fleksibel'];
    }

    public static function interests(): array
    {
        return [
            'LITERACY' => 'Literasi & Bimbingan Belajar',
            'HEALTH' => 'Edukasi Kesehatan & Gizi',
            'CREATIVE' => 'Kegiatan Kreatif (seni, olahraga, permainan)',
            'MEDIA' => 'Dokumentasi & Media',
            'OTHER' => 'Lainnya / Sesuai kebutuhan tim',
        ];
    }

    public static function referralSources(): array
    {
        return [
            'INSTAGRAM' => 'Instagram @papuafutureproject',
            'FRIEND' => 'Diajak teman / kenalan',
            'ONLINE_ARTICLE' => 'Artikel atau berita online',
            'EVENT' => 'Event / seminar / pameran',
            'OTHER' => 'Lainnya',
        ];
    }
}
