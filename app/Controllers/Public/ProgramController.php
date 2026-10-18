<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Response;
use App\Core\View;

final class ProgramController
{
    public function index(): Response
    {
        return View::render('public/program/index', [
            'title' => 'Program — Papua Future Project',
            'page' => 'program',
            'stylesheet' => 'css/pages/program.css',
        ]);
    }

    public function show(string $slug): Response
    {
        if ($slug === 'bimbel-literasi') {
            return View::render('public/program/show', [
                'title' => 'Bimbingan Belajar & Literasi — Papua Future Project',
                'page' => 'program-detail',
                'stylesheet' => 'css/pages/program-detail.css',
            ]);
        }

        $programs = [
            'literasi-digital' => [
                'title' => 'Literasi Digital',
                'summary' => 'Mendampingi anak dan pemuda agar mengenal teknologi digital dan menggunakannya dengan aman serta bermanfaat.',
            ],
            'pojok-baca-donasi-buku' => [
                'title' => 'Pojok Baca & Donasi Buku',
                'summary' => 'Menghadirkan akses buku dan ruang baca yang dekat dengan anak-anak di kampung.',
            ],
            'kesehatan-imunisasi' => [
                'title' => 'Kesehatan & Imunisasi',
                'summary' => 'Berkolaborasi untuk mendukung edukasi kesehatan anak dan akses layanan kesehatan di wilayah dampingan.',
            ],
            'pemberdayaan-pemuda' => [
                'title' => 'Pemberdayaan Pemuda Lokal',
                'summary' => 'Membuka ruang bagi pemuda lokal untuk mengembangkan kepemimpinan dan berkontribusi bagi komunitas.',
            ],
        ];

        if (!isset($programs[$slug])) {
            return View::render('public/errors/404', [
                'title' => 'Program tidak ditemukan',
                'page' => 'not-found',
            ], 'public', 404);
        }

        return View::render('public/program/generic', [
            'title' => $programs[$slug]['title'] . ' — Papua Future Project',
            'page' => 'program-detail',
            'stylesheet' => 'css/pages/program-detail.css',
            'program' => $programs[$slug],
        ]);
    }
}
