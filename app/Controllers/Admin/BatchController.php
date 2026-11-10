<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Flash;
use App\Core\Request;
use App\Core\Response;
use App\Core\SecurityLogger;
use App\Core\View;
use App\Models\Batch;
use App\Validators\BatchValidator;
use PDOException;

final class BatchController
{
    public function index(): Response
    {
        return View::render('admin/batch/index', [
            'title' => 'Batch Intern & Volunteer',
            'page' => 'batch',
            'breadcrumb' => ['Admin', 'Batch'],
            'batches' => Batch::all(),
            'errors' => Flash::get('errors', []),
        ], 'admin');
    }

    public function create(): Response
    {
        return $this->form(null);
    }

    public function edit(int $id): Response
    {
        $batch = Batch::find($id);
        return $batch === null ? Response::redirect('/admin/batch') : $this->form($batch);
    }

    private function form(?array $batch): Response
    {
        return View::render('admin/batch/form', [
            'title' => $batch === null ? 'Tambah Batch' : 'Ubah Batch',
            'page' => 'batch',
            'breadcrumb' => ['Admin', 'Batch', $batch === null ? 'Tambah' : 'Ubah'],
            'batch' => $batch,
            'values' => Flash::get('old', $batch ?? ['batch_type' => 'INTERN', 'name' => '', 'google_form_url' => '']),
            'errors' => Flash::get('errors', []),
        ], 'admin');
    }

    public function store(Request $request): Response
    {
        return $this->save($request, null);
    }

    public function update(Request $request, int $id): Response
    {
        return $this->save($request, $id);
    }

    private function save(Request $request, ?int $id): Response
    {
        $current = $id === null ? null : Batch::find($id);
        if ($id !== null && $current === null) {
            return Response::redirect('/admin/batch');
        }
        [$values, $errors] = BatchValidator::validate($request->post());
        if ($current !== null && $current['batch_type'] !== $values['batch_type'] && (int) $current['member_count'] > 0) {
            $errors['batch_type'] = 'Jenis batch tidak dapat diubah karena masih digunakan anggota tim.';
        }
        if ($errors !== []) {
            Flash::set('errors', $errors);
            Flash::set('old', $values);
            Flash::set('error', 'Periksa kembali data batch.');
            return Response::redirect($id === null ? '/admin/batch/tambah' : '/admin/batch/' . $id . '/ubah');
        }
        try {
            Batch::save($values['batch_type'], $values['name'], $id, $values['google_form_url']);
            Flash::set('success', 'Batch berhasil disimpan.');
            return Response::redirect('/admin/batch');
        } catch (PDOException) {
            Flash::set('errors', ['name' => 'Nama batch tersebut sudah digunakan untuk jenis ini.']);
            Flash::set('old', $values);
            Flash::set('error', 'Batch gagal disimpan.');
            return Response::redirect($id === null ? '/admin/batch/tambah' : '/admin/batch/' . $id . '/ubah');
        }
    }

    public function toggle(Request $request, int $id): Response
    {
        $result = Batch::toggle($id);
        if (!$result['updated']) {
            Flash::set('error', $result['reason'] === 'missing_form_url'
                ? 'Isi link pendaftaran melalui Ubah Batch sebelum membuka pendaftaran.'
                : 'Batch tidak ditemukan.');
            return Response::redirect('/admin/batch');
        }

        SecurityLogger::record('batch_registration_toggled', $request, $id);
        Flash::set('success', $result['is_open'] ? 'Pendaftaran batch berhasil dibuka.' : 'Pendaftaran batch berhasil ditutup.');
        return Response::redirect('/admin/batch');
    }

    public function destroy(Request $request, int $id): Response
    {
        $result = Batch::deleteIfNoMembers($id);
        if (!$result['deleted']) {
            if ($result['reason'] === 'has_members') {
                Flash::set('error', 'Batch tidak dapat dihapus karena sudah memiliki ' . $result['member_count'] . ' anggota tim.');
            } else {
                Flash::set('error', 'Batch tidak ditemukan atau sudah dihapus.');
            }
            return Response::redirect('/admin/batch');
        }

        SecurityLogger::record('batch_deleted', $request, $id);
        Flash::set('success', 'Batch berhasil dihapus.');
        return Response::redirect('/admin/batch');
    }
}
