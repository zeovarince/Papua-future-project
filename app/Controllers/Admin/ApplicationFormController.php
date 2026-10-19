<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Flash;
use App\Core\Request;
use App\Core\Response;
use App\Core\SecurityLogger;
use App\Core\View;
use App\Models\ApplicationFormSetting;
use App\Models\ApplicationQuestion;
use App\Validators\ApplicationQuestionValidator;

final class ApplicationFormController
{
    public function index(): Response
    {
        return View::render('admin/application-form/index', [
            'title' => 'Form Pendaftaran',
            'page' => 'application-form',
            'breadcrumb' => ['Admin', 'Form Pendaftaran'],
            'settings' => ApplicationFormSetting::all(),
        ], 'admin');
    }

    public function toggle(Request $request, string $typeSlug): Response
    {
        $type = $this->type($typeSlug);
        if ($type === null) {
            return $this->notFound();
        }
        if (ApplicationFormSetting::toggle($type)) {
            SecurityLogger::record('application_form_toggled', $request);
            Flash::set('success', 'Status formulir ' . $this->typeLabel($type) . ' berhasil diubah.');
        } else {
            Flash::set('error', 'Status formulir tidak ditemukan. Jalankan pembaruan database terlebih dahulu.');
        }
        return Response::redirect('/admin/pendaftaran');
    }

    public function questions(string $typeSlug): Response
    {
        $type = $this->type($typeSlug);
        if ($type === null) {
            return $this->notFound();
        }
        return View::render('admin/application-form/questions', [
            'title' => 'Pertanyaan ' . $this->typeLabel($type),
            'page' => 'application-form',
            'breadcrumb' => ['Admin', 'Form Pendaftaran', $this->typeLabel($type)],
            'type' => $type,
            'typeSlug' => $typeSlug,
            'typeLabel' => $this->typeLabel($type),
            'questions' => ApplicationQuestion::allByType($type),
        ], 'admin');
    }

    public function create(string $typeSlug): Response
    {
        $type = $this->type($typeSlug);
        if ($type === null) {
            return $this->notFound();
        }
        return $this->questionForm($typeSlug, $type, null);
    }

    public function store(Request $request, string $typeSlug): Response
    {
        $type = $this->type($typeSlug);
        if ($type === null) {
            return $this->notFound();
        }
        if (ApplicationQuestion::countByType($type) >= 25) {
            Flash::set('error', 'Maksimal 25 pertanyaan tambahan untuk setiap jenis pendaftaran.');
            return Response::redirect('/admin/pendaftaran/' . $typeSlug . '/pertanyaan');
        }
        return $this->saveQuestion($request, $typeSlug, $type, null);
    }

    public function edit(string $typeSlug, int $id): Response
    {
        $type = $this->type($typeSlug);
        if ($type === null) {
            return $this->notFound();
        }
        $question = ApplicationQuestion::find($id, $type);
        return $question === null ? $this->notFound() : $this->questionForm($typeSlug, $type, $question);
    }

    public function update(Request $request, string $typeSlug, int $id): Response
    {
        $type = $this->type($typeSlug);
        if ($type === null) {
            return $this->notFound();
        }
        if (ApplicationQuestion::find($id, $type) === null) {
            return $this->notFound();
        }
        return $this->saveQuestion($request, $typeSlug, $type, $id);
    }

    public function destroy(Request $request, string $typeSlug, int $id): Response
    {
        $type = $this->type($typeSlug);
        if ($type === null) {
            return $this->notFound();
        }
        if (ApplicationQuestion::delete($id, $type)) {
            SecurityLogger::record('application_question_deleted', $request, $id);
            Flash::set('success', 'Pertanyaan berhasil dihapus. Jawaban lama pendaftar tetap tersimpan.');
        } else {
            Flash::set('error', 'Pertanyaan tidak ditemukan.');
        }
        return Response::redirect('/admin/pendaftaran/' . $typeSlug . '/pertanyaan');
    }

    private function saveQuestion(Request $request, string $typeSlug, string $type, ?int $id): Response
    {
        [$values, $errors] = ApplicationQuestionValidator::validate($request->post());
        if ($errors !== []) {
            Flash::set('errors', $errors);
            Flash::set('old', $values);
            Flash::set('error', 'Periksa kembali pertanyaan formulir.');
            return Response::redirect($id === null
                ? '/admin/pendaftaran/' . $typeSlug . '/pertanyaan/tambah'
                : '/admin/pendaftaran/' . $typeSlug . '/pertanyaan/' . $id . '/ubah');
        }

        $questionId = ApplicationQuestion::save($type, $values, $id);
        SecurityLogger::record($id === null ? 'application_question_created' : 'application_question_updated', $request, $questionId);
        Flash::set('success', 'Pertanyaan formulir ' . $this->typeLabel($type) . ' berhasil disimpan.');
        return Response::redirect('/admin/pendaftaran/' . $typeSlug . '/pertanyaan');
    }

    private function questionForm(string $typeSlug, string $type, ?array $question): Response
    {
        return View::render('admin/application-form/question-form', [
            'title' => ($question === null ? 'Tambah' : 'Ubah') . ' Pertanyaan ' . $this->typeLabel($type),
            'page' => 'application-form',
            'breadcrumb' => ['Admin', 'Form Pendaftaran', $this->typeLabel($type), $question === null ? 'Tambah Pertanyaan' : 'Ubah Pertanyaan'],
            'typeSlug' => $typeSlug,
            'typeLabel' => $this->typeLabel($type),
            'question' => $question,
            'values' => Flash::get('old', $question ?? [
                'question' => '',
                'field_type' => 'TEXTAREA',
                'is_required' => 1,
                'is_active' => 1,
                'sort_order' => 0,
            ]),
            'errors' => Flash::get('errors', []),
        ], 'admin');
    }

    private function type(string $slug): ?string
    {
        return match ($slug) {
            'internship' => 'INTERN',
            'relawan' => 'VOLUNTEER',
            default => null,
        };
    }

    private function typeLabel(string $type): string
    {
        return $type === 'INTERN' ? 'Internship' : 'Relawan';
    }

    private function notFound(): Response
    {
        return new Response('Halaman tidak ditemukan.', 404, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
