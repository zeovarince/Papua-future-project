<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Flash;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\ApplicationFormSetting;
use App\Models\ApplicationQuestion;
use App\Models\Batch;
use App\Services\ApplicationService;
use App\Services\RateLimiter;
use App\Validators\JoinApplicationValidator;
use PDOException;
use RuntimeException;
use Throwable;

final class JoinController
{
    public function index(): Response
    {
        $types = [];
        foreach (['internship' => 'INTERN', 'relawan' => 'VOLUNTEER'] as $slug => $type) {
            $isActive = ApplicationFormSetting::isActive($type);
            $batches = Batch::openByType($type);
            $types[$slug] = [
                'type' => $type,
                'slug' => $slug,
                'label' => $type === 'INTERN' ? 'Internship' : 'Relawan',
                'is_active' => $isActive,
                'batches' => $batches,
                'is_open' => $isActive && $batches !== [],
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

    public function form(string $typeSlug, ?int $batchId = null): Response
    {
        $type = $this->type($typeSlug);
        if ($type === null) {
            return View::render('public/errors/404', [
                'title' => 'Halaman tidak ditemukan',
                'page' => 'not-found',
            ], 'public', 404);
        }

        $typeLabel = $type === 'INTERN' ? 'Internship' : 'Relawan';
        $openBatches = Batch::openByType($type);
        if ($batchId === null && count($openBatches) === 1) {
            return Response::redirect('/bergabung/' . $typeSlug . '/' . (int) $openBatches[0]['id']);
        }
        if ($batchId === null && count($openBatches) > 1) {
            Flash::set('error', 'Pilih batch yang ingin kamu ikuti dari halaman Bergabung.');
            return Response::redirect('/bergabung#join-paths');
        }
        $batch = $batchId === null ? null : Batch::find($batchId);
        if ($batch !== null && $batch['batch_type'] !== $type) {
            return View::render('public/errors/404', [
                'title' => 'Halaman tidak ditemukan',
                'page' => 'not-found',
            ], 'public', 404);
        }
        $old = Flash::get('old', []);
        return View::render('public/join/form', [
            'title' => 'Pendaftaran ' . $typeLabel . ' — Papua Future Project',
            'page' => 'join-form',
            'stylesheet' => 'css/pages/join.css',
            'applicationType' => $type,
            'typeSlug' => $typeSlug,
            'typeLabel' => $typeLabel,
            'isFormActive' => ApplicationFormSetting::isActive($type),
            'batch' => $batch,
            'questions' => ApplicationQuestion::activeByType($type),
            'errors' => Flash::get('errors', []),
            'old' => is_array($old) ? $old : [],
            'flashError' => Flash::get('error'),
        ]);
    }

    public function store(Request $request, string $typeSlug, ?int $batchId = null): Response
    {
        $type = $this->type($typeSlug);
        if ($type === null) {
            return Response::redirect('/bergabung');
        }

        if ($batchId === null) {
            $postedBatchId = filter_var($request->post('batch_id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $batchId = $postedBatchId === false ? null : (int) $postedBatchId;
        }
        $batch = $batchId === null ? null : Batch::find($batchId);
        $validBatch = $batch !== null && $batch['batch_type'] === $type && (int) $batch['is_open'] === 1;
        $formUrl = $validBatch ? '/bergabung/' . $typeSlug . '/' . $batchId . '#form-daftar' : '/bergabung#join-paths';
        if (!ApplicationFormSetting::isActive($type) || !$validBatch) {
            Flash::set('error', 'Batch pendaftaran tidak tersedia atau pendaftarannya sudah ditutup. Pilih batch yang masih dibuka.');
            return Response::redirect($formUrl);
        }

        $website = $request->post('website', '');
        if (is_scalar($website) && trim((string) $website) !== '') {
            Flash::set('application_submitted', true);
            return Response::redirect('/bergabung/berhasil');
        }

        $limiter = new RateLimiter();
        $identifier = RateLimiter::identifier($request->ipAddress());
        if ($limiter->isLimited('join', $identifier, 5, 3600)) {
            Flash::set('error', 'Batas pengiriman pendaftaran tercapai. Silakan coba lagi nanti.');
            Flash::set('old', $request->post());
            return Response::redirect($formUrl);
        }
        $limiter->hit('join', $identifier);

        [$values, $errors] = JoinApplicationValidator::validate(
            array_replace($request->post(), ['batch_id' => $batchId]),
            $request->file('photo'),
            $request->file('cv')
        );

        $batchId = filter_var($values['batch_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($batchId !== false) {
            $batch = Batch::find((int) $batchId);
            if ($batch !== null && $batch['batch_type'] !== $type) {
                $errors['batch_id'] = 'Batch yang dipilih bukan untuk pendaftaran ' . ($type === 'INTERN' ? 'Internship.' : 'Relawan.');
            }
        }

        if ($errors !== []) {
            Flash::set('old', $values);
            Flash::set('errors', $errors);
            Flash::set('error', 'Periksa kembali data dan berkas pendaftaran.');
            return Response::redirect($formUrl);
        }

        try {
            (new ApplicationService())->submit(
                $values,
                !empty($values['photo_enabled']) ? $request->file('photo') : null,
                !empty($values['cv_enabled']) ? $request->file('cv') : null
            );
            Flash::set('application_submitted', true);
            return Response::redirect('/bergabung/berhasil');
        } catch (PDOException $exception) {
            if ((int) ($exception->errorInfo[1] ?? 0) === 1062) {
                $emailErrorKey = 'email';
                foreach (ApplicationQuestion::activeByType($type) as $question) {
                    if (($question['field_key'] ?? null) === 'email') {
                        $emailErrorKey = 'question_answers_' . (int) $question['id'];
                        break;
                    }
                }
                Flash::set('errors', [$emailErrorKey => 'Email ini sudah digunakan untuk mendaftar pada batch tersebut.']);
                Flash::set('old', $values);
                Flash::set('error', 'Email tersebut sudah terdaftar pada batch yang dipilih.');
                return Response::redirect($formUrl);
            }
            Logger::error($exception);
        } catch (RuntimeException $exception) {
            if (str_contains($exception->getMessage(), 'Batch ini sudah ditutup')
                || str_contains($exception->getMessage(), 'Pendaftaran untuk batch ini sedang ditutup')) {
                Flash::set('errors', ['batch_id' => $exception->getMessage()]);
                Flash::set('old', $values);
                Flash::set('error', 'Batch yang dipilih sudah ditutup saat pendaftaran dikirim.');
                return Response::redirect($formUrl);
            }
            Logger::error($exception);
        } catch (Throwable $exception) {
            Logger::error($exception);
        }

        Flash::set('old', $values);
        Flash::set('error', 'Pendaftaran belum berhasil disimpan. Coba lagi atau hubungi tim Papua Future Project.');
        return Response::redirect($formUrl);
    }

    public function success(): Response
    {
        if (!Flash::get('application_submitted', false)) {
            return Response::redirect('/bergabung');
        }
        return View::render('public/join/success', [
            'title' => 'Pendaftaran Diterima — Papua Future Project',
            'page' => 'join-success',
            'stylesheet' => 'css/pages/join.css',
        ]);
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
