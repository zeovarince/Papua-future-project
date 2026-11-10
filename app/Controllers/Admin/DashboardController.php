<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\ContactMessage;
use App\Models\Post;
use PDO;

final class DashboardController
{
    public function index(Request $request): Response
    {
        $admin = Session::get('admin', []);
        $adminName = (string) ($admin['name'] ?? 'Admin');
        $db = Database::connection();
        $totalMessagesCount = (int) $db->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
        $draftPostsCount = (int) $db->query(
            'SELECT COUNT(*) FROM posts WHERE is_published = 0 OR published_at > CURRENT_TIMESTAMP'
        )->fetchColumn();
        $latestMessages = $db->query(
            'SELECT id, full_name, email, message, is_read, created_at '
            . 'FROM contact_messages ORDER BY created_at DESC, id DESC LIMIT 5'
        )->fetchAll(PDO::FETCH_ASSOC);

        return View::render('admin/dashboard/index', [
            'title' => "Selamat datang kembali, {$adminName}",
            'subtitle' => 'Semoga harimu menyenangkan.',
            'page' => 'dashboard',
            'admin' => $admin,
            'breadcrumb' => ['Admin', 'Ringkasan'],
            'metrics' => [
                'unread_messages' => [
                    'label' => 'Pesan belum dibaca',
                    'value' => ContactMessage::unreadCount(),
                    'note' => "dari {$totalMessagesCount} pesan",
                    'url' => url('/admin/pesan'),
                ],
                'published_posts' => [
                    'label' => 'Berita terbit',
                    'value' => Post::countPublished(),
                    'note' => "{$draftPostsCount} draf",
                    'url' => url('/admin/berita'),
                ],
            ],
            'latestMessages' => $latestMessages,
        ], 'admin');
    }
}
