<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Flash;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
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
        return View::render('public/join/index', [
            'title' => 'Bergabung — Papua Future Project',
            'page' => 'join',
            'stylesheet' => 'css/pages/join.css',
            'openInternBatches' => Batch::openByType('INTERN'),
            'openVolunteerBatches' => Batch::openByType('VOLUNTEER'),
            'errors' => Flash::get('errors', []),
            'old' => Flash::get('old', []),
            'flashError' => Flash::get('error'),
        ]);
    }

    public function store(Request $request): Response
    {
        $website = $request->post('website', '');
        if (is_scalar($website) && trim((string) $website) !== '') {
            Flash::set('application_submitted', true);
            return Response::redirect('/bergabung/berhasil');
        }

        $limiter = new RateLimiter();
        $identifier = hash('sha256', $request->ipAddress());
        if ($limiter->isLimited('join', $identifier, 5, 3600)) {
            Flash::set('error', 'Batas pengiriman pendaftaran tercapai. Silakan coba lagi nanti.');
            Flash::set('old', $request->post());
            return Response::redirect('/bergabung#form-daftar');
        }
        $limiter->hit('join', $identifier);

        [$values, $errors] = JoinApplicationValidator::validate(
            $request->post(),
            $request->file('photo'),
            $request->file('cv')
        );
        if ($errors !== []) {
            Flash::set('old', $values);
            Flash::set('errors', $errors);
            Flash::set('error', 'Periksa kembali data dan berkas pendaftaran.');
            return Response::redirect('/bergabung#form-daftar');
        }

        try {
            (new ApplicationService())->submit($values, $request->file('photo'), $request->file('cv'));
            Flash::set('application_submitted', true);
            return Response::redirect('/bergabung/berhasil');
        } catch (PDOException $exception) {
            if ((int) ($exception->errorInfo[1] ?? 0) === 1062) {
                Flash::set('errors', ['email' => 'Email ini sudah digunakan untuk mendaftar pada batch tersebut.']);
                Flash::set('old', $values);
                Flash::set('error', 'Email tersebut sudah terdaftar pada batch yang dipilih.');
                return Response::redirect('/bergabung#form-daftar');
            }
            Logger::error($exception);
        } catch (RuntimeException $exception) {
            if (str_contains($exception->getMessage(), 'Batch ini sudah ditutup')) {
                Flash::set('errors', ['batch_id' => $exception->getMessage()]);
                Flash::set('old', $values);
                Flash::set('error', 'Batch yang dipilih sudah ditutup saat pendaftaran dikirim.');
                return Response::redirect('/bergabung#form-daftar');
            }
            Logger::error($exception);
        } catch (Throwable $exception) {
            Logger::error($exception);
        }

        Flash::set('old', $values);
        Flash::set('error', 'Pendaftaran belum berhasil disimpan. Coba lagi atau hubungi tim Papua Future Project.');
        return Response::redirect('/bergabung#form-daftar');
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
}
