<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Flash;
use App\Core\Response;
use App\Core\View;
use App\Models\Batch;
use App\Validators\BatchValidator;

final class JoinController
{
    public function index(): Response
    {
        $types = [];
        foreach (['internship' => 'INTERN', 'relawan' => 'VOLUNTEER'] as $slug => $type) {
            $batches = Batch::publicByType($type);
            foreach ($batches as &$batch) {
                $batchUrl = trim((string) ($batch['google_form_url'] ?? ''));
                $batch['can_register'] = (int) ($batch['is_open'] ?? 0) === 1
                    && BatchValidator::isRegistrationUrl($batchUrl);
            }
            unset($batch);

            $types[$slug] = [
                'type' => $type,
                'slug' => $slug,
                'label' => $type === 'INTERN' ? 'Internship' : 'Relawan',
                'batches' => $batches,
            ];
        }

        return View::render('public/join/index', [
            'title' => 'Bergabung — Papua Future Project',
            'page' => 'join',
            'stylesheet' => 'css/pages/join.css',
            'types' => $types,
            'flashError' => Flash::get('error'),
        ]);
    }

    /** Redirects to the batch's configured registration URL while it is open. */
    public function form(string $typeSlug, int $batchId): Response
    {
        $type = $this->type($typeSlug);
        if ($type === null) {
            return View::render('public/errors/404', [
                'title' => 'Halaman tidak ditemukan',
                'page' => 'not-found',
            ], 'public', 404);
        }

        $batch = Batch::publicFind($batchId, $type);
        $formUrl = is_array($batch) ? trim((string) ($batch['google_form_url'] ?? '')) : '';
        if (is_array($batch) && (int) $batch['is_open'] === 1 && BatchValidator::isRegistrationUrl($formUrl)) {
            return Response::redirect($formUrl, 302);
        }

        Flash::set('error', 'Pendaftaran batch tersebut sedang ditutup atau link pendaftaran belum tersedia.');
        return Response::redirect('/bergabung#join-paths');
    }

    private function type(string $slug): ?string
    {
        return match ($slug) {
            'internship' => 'INTERN',
            'relawan' => 'VOLUNTEER',
            default => null,
        };
    }
}
