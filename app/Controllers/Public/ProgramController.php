<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Response;
use App\Core\View;
use App\Models\Program;

final class ProgramController
{
    public function index(): Response
    {
        $programs = Program::publicList();
        $photos = Program::publicPhotos(15);
        if ($photos === []) {
            foreach ($programs as $program) {
                if (!empty($program['cover_image'])) {
                    $photos[] = [
                        'file_path' => $program['cover_image'],
                        'caption' => $program['title'],
                        'program_title' => $program['title'],
                        'program_slug' => $program['slug'],
                    ];
                }
            }
        }

        return View::render('public/program/index', [
            'title' => 'Program Kami — Papua Future Project',
            'page' => 'program',
            'stylesheet' => 'css/pages/program.css',
            'programs' => $programs,
            'programPhotos' => $photos,
        ]);
    }

    public function show(string $slug): Response
    {
        $program = Program::findPublicBySlug($slug);
        if ($program === null) {
            return View::render('public/errors/404', [
                'title' => 'Program tidak ditemukan',
                'page' => 'not-found',
            ], 'public', 404);
        }

        $otherPrograms = array_values(array_filter(
            Program::publicList(),
            static fn (array $item): bool => (int) $item['id'] !== (int) $program['id']
        ));

        return View::render('public/program/detail', [
            'title' => $program['title'] . ' — Program Papua Future Project',
            'page' => 'program-detail',
            'stylesheet' => 'css/pages/program-detail.css',
            'program' => $program,
            'photos' => Program::photos((int) $program['id']),
            'otherPrograms' => $otherPrograms,
        ]);
    }
}
