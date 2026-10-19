<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Flash;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Response;
use App\Core\SecurityLogger;
use App\Core\View;
use App\Models\Application;
use App\Models\Batch;
use App\Services\ApplicationExportService;
use App\Services\ApplicationFileService;
use App\Support\Labels;
use RuntimeException;
use Throwable;

final class ApplicationController
{
    public function index(Request $request): Response
    {
        $filters = $this->filters($request);
        $pageValue = filter_var($request->query('page', 1), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $pagination = Application::page($filters, $pageValue === false ? 1 : $pageValue);
        $lastPage = max(1, (int) ceil($pagination['total'] / $pagination['per_page']));
        $pagination['last_page'] = $lastPage;

        return View::render('admin/application/index', [
            'title' => 'Pendaftar',
            'page' => 'application',
            'breadcrumb' => ['Admin', 'Pendaftar'],
            'applications' => $pagination['items'],
            'pagination' => $pagination,
            'filters' => $filters,
            'batches' => Batch::all(),
            'types' => Labels::applicationTypes(),
            'statuses' => Labels::applicationStatuses(),
        ], 'admin');
    }

    public function show(int $id): Response
    {
        $application = Application::find($id);
        if ($application === null) {
            return new Response('Pendaftar tidak ditemukan.', 404, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }
        $decoded = json_decode((string) $application['interest_areas'], true);
        $application['interest_areas'] = is_array($decoded) ? $decoded : [];

        return View::render('admin/application/show', [
            'title' => 'Detail Pendaftar',
            'page' => 'application',
            'breadcrumb' => ['Admin', 'Pendaftar', 'Detail'],
            'application' => $application,
            'types' => Labels::applicationTypes(),
            'statuses' => Labels::applicationStatuses(),
            'occupations' => Labels::occupations(),
            'durations' => Labels::durations(),
            'interests' => Labels::interests(),
            'referralSources' => Labels::referralSources(),
        ], 'admin');
    }

    public function updateStatus(Request $request, int $id): Response
    {
        $status = $request->post('status', '');
        if (!is_string($status) || !array_key_exists($status, Labels::applicationStatuses())) {
            Flash::set('error', 'Pilih status pendaftar yang valid.');
            return Response::redirect('/admin/pendaftar/' . $id);
        }
        if (!Application::updateStatus($id, $status)) {
            Flash::set('error', 'Pendaftar tidak ditemukan.');
            return Response::redirect('/admin/pendaftar');
        }
        SecurityLogger::record('application_status_changed', $request, $id);
        Flash::set('success', 'Status pendaftar berhasil diperbarui.');
        return Response::redirect('/admin/pendaftar/' . $id);
    }

    public function file(Request $request, int $id, string $kind): Response
    {
        $application = Application::find($id);
        if ($application === null || !in_array($kind, ['foto', 'cv'], true)) {
            return new Response('Berkas tidak ditemukan.', 404, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }
        $path = (new ApplicationFileService())->pathFor($application, $kind === 'foto' ? 'photo' : 'cv');
        if ($path === null) {
            return new Response('Berkas tidak ditemukan.', 404, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }

        $isPhoto = $kind === 'foto';
        $mime = $isPhoto ? 'image/webp' : 'application/pdf';
        $download = !$isPhoto && (string) $request->query('unduh', '') === '1';
        $filename = 'pendaftar-' . $id . ($isPhoto ? '-foto.webp' : '-cv.pdf');
        $disposition = $download ? 'attachment' : 'inline';
        $headers = [
            'Content-Type' => $mime,
            'Content-Disposition' => $disposition . '; filename="' . $filename . '"',
            'Content-Length' => (string) filesize($path),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
        ];
        return Response::stream(static function () use ($path): void {
            readfile($path);
        }, $headers);
    }

    public function export(Request $request, string $format): Response
    {
        $format = strtolower($format);
        if ($format === 'excel') {
            $format = 'xlsx';
        }
        if (!in_array($format, ['csv', 'xlsx', 'pdf'], true)) {
            return new Response('Format ekspor tidak tersedia.', 404, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }
        SecurityLogger::record('application_exported_' . $format, $request);
        try {
            return (new ApplicationExportService())->download($format, $this->filters($request));
        } catch (RuntimeException $exception) {
            Flash::set('error', $exception->getMessage());
        } catch (Throwable $exception) {
            Logger::error($exception);
            Flash::set('error', 'Ekspor gagal dibuat. Periksa konfigurasi server atau persempit filter.');
        }
        $query = http_build_query(array_filter($this->filters($request), static fn (mixed $value): bool => $value !== '' && $value !== null));
        return Response::redirect('/admin/pendaftar' . ($query === '' ? '' : '?' . $query));
    }

    private function filters(Request $request): array
    {
        $type = $request->query('tipe', '');
        $status = $request->query('status', '');
        $batch = $request->query('batch', '');
        $search = $request->query('q', '');
        return [
            'type' => is_string($type) && in_array($type, ['INTERN', 'VOLUNTEER'], true) ? $type : '',
            'batch_id' => is_scalar($batch) && filter_var($batch, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ? (int) $batch : null,
            'status' => is_string($status) && array_key_exists($status, Labels::applicationStatuses()) ? $status : '',
            'q' => is_scalar($search) ? mb_substr(trim((string) $search), 0, 100) : '',
        ];
    }
}
