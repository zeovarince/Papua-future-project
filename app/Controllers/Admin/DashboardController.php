<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\Application;
use App\Models\Batch;
use App\Models\ContactMessage;
use App\Models\Post;

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
                ['label' => 'Pendaftar baru', 'value' => (string) Application::countNew(), 'note' => 'Menunggu ditinjau'],
                ['label' => 'Pesan belum dibaca', 'value' => (string) ContactMessage::unreadCount(), 'note' => 'Pesan dari pengunjung'],
                ['label' => 'Batch dibuka', 'value' => (string) Batch::countOpen(), 'note' => 'Intern dan relawan'],
                ['label' => 'Berita terbit', 'value' => (string) Post::countPublished(), 'note' => 'Termasuk kegiatan, penghargaan, dan liputan'],
            ],
        ], 'admin');
    }
}
