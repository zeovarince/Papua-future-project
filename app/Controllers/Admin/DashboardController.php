<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\Batch;

final class DashboardController
{
    public function index(Request $request): Response
    {
        $admin = Session::get('admin', []);

        return View::render('admin/dashboard/index', [
            'title' => 'Ringkasan Admin',
            'page' => 'dashboard',
            'admin' => $admin,
            'breadcrumb' => ['Admin', 'Ringkasan'],
            'metrics' => [
                ['label' => 'Pendaftar baru', 'value' => 'Belum tersedia', 'note' => 'Modul pendaftar belum dibuat'],
                ['label' => 'Pesan belum dibaca', 'value' => 'Belum tersedia', 'note' => 'Modul pesan belum dibuat'],
                ['label' => 'Batch dibuka', 'value' => (string) Batch::countOpen(), 'note' => 'Intern dan relawan'],
                ['label' => 'Berita terbit', 'value' => 'Belum tersedia', 'note' => 'Modul berita belum dibuat'],
            ],
        ], 'admin');
    }
}
