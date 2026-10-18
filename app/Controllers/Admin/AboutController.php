<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Flash;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\AboutContent;
use App\Validators\AboutContentValidator;

final class AboutController
{
    public function edit(): Response
    {
        $content = Flash::get('old', AboutContent::current() ?? ['story' => '', 'vision' => '', 'mission' => '']);
        return View::render('admin/about/edit', [
            'title' => 'Tentang Kami',
            'page' => 'about',
            'breadcrumb' => ['Admin', 'Tentang Kami'],
            'contentData' => $content,
            'errors' => Flash::get('errors', []),
        ], 'admin');
    }

    public function update(Request $request): Response
    {
        $input = $request->post('content', []);
        [$values, $errors] = AboutContentValidator::validate(is_array($input) ? $input : []);
        if ($errors !== []) {
            Flash::set('errors', $errors);
            Flash::set('old', $values);
            Flash::set('error', 'Periksa kembali isian Tentang Kami.');
            return Response::redirect('/admin/tentang');
        }
        AboutContent::save($values);
        Flash::set('success', 'Konten Tentang Kami berhasil disimpan.');
        return Response::redirect('/admin/tentang');
    }
}
