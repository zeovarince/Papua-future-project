<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Flash;
use App\Core\Request;
use App\Core\Response;
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
            'values' => Flash::get('old', $batch ?? ['batch_type' => 'INTERN', 'name' => '']),
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
        if ($current !== null && $current['batch_type'] !== $values['batch_type'] && ((int) $current['member_count'] > 0 || (int) $current['application_count'] > 0)) {
            $errors['batch_type'] = 'Jenis batch tidak dapat diubah karena sudah memiliki anggota atau pendaftar.';
        }
        if ($errors !== []) {
            Flash::set('errors', $errors);
            Flash::set('old', $values);
            Flash::set('error', 'Periksa kembali data batch.');
            return Response::redirect($id === null ? '/admin/batch/tambah' : '/admin/batch/' . $id . '/ubah');
        }
        try {
            Batch::save($values['batch_type'], $values['name'], $id);
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
        if (Batch::toggle($id)) {
            Flash::set('success', 'Status pendaftaran batch berhasil diubah.');
        } else {
            Flash::set('error', 'Batch tidak ditemukan.');
        }
        return Response::redirect('/admin/batch');
    }

    public function destroy(Request $request, int $id): Response
    {
        $batch = Batch::find($id);
        if ($batch === null) {
            return Response::redirect('/admin/batch');
        }
        if ((int) $batch['member_count'] > 0 || (int) $batch['application_count'] > 0) {
            Flash::set('error', 'Batch tidak dapat dihapus: masih memiliki ' . $batch['member_count'] . ' anggota dan ' . $batch['application_count'] . ' pendaftar. Tutup batch bila pendaftaran selesai.');
            return Response::redirect('/admin/batch');
        }
        Batch::delete($id);
        Flash::set('success', 'Batch berhasil dihapus.');
        return Response::redirect('/admin/batch');
    }
}
