<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\Application;
use App\Models\Batch;
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

        // 2. Metrik utama (Section 5.1)
        $newApplicationsCount = Application::countNew();
        $unreadMessagesCount = ContactMessage::unreadCount();
        $totalMessagesCount = (int) $db->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();

        $openBatchesCount = Batch::countOpen();
        $openInternBatches = (int) $db->query(
            "SELECT COUNT(*) FROM batches b INNER JOIN application_form_settings s ON s.application_type = b.batch_type AND s.is_active = 1 WHERE b.batch_type = 'INTERN' AND b.is_open = 1"
        )->fetchColumn();
        $openVolunteerBatches = (int) $db->query(
            "SELECT COUNT(*) FROM batches b INNER JOIN application_form_settings s ON s.application_type = b.batch_type AND s.is_active = 1 WHERE b.batch_type = 'VOLUNTEER' AND b.is_open = 1"
        )->fetchColumn();

        $publishedPostsCount = Post::countPublished();
        $draftPostsCount = (int) $db->query(
            'SELECT COUNT(*) FROM posts WHERE is_published = 0 OR published_at > CURRENT_TIMESTAMP'
        )->fetchColumn();

        // 3. Dropdown periode untuk Tren Pendaftar
        $period = $request->query('periode', '30d');
        if (!in_array($period, ['30d', '90d', '12m'], true)) {
            $period = '30d';
        }

        $periodDays = match ($period) {
            '90d' => 90,
            '12m' => 365,
            default => 30,
        };
        $periodLabel = match ($period) {
            '90d' => '90 hari terakhir',
            '12m' => '12 bulan terakhir',
            default => '30 hari terakhir',
        };

        $startDate = date('Y-m-d H:i:s', strtotime("-{$periodDays} days"));

        // Tren total & status breakdown
        $trendStmt = $db->prepare(
            'SELECT status, COUNT(*) AS count FROM applications WHERE created_at >= :start GROUP BY status'
        );
        $trendStmt->execute(['start' => $startDate]);
        $statusCounts = [
            'NEW' => 0,
            'REVIEWED' => 0,
            'ACCEPTED' => 0,
            'REJECTED' => 0,
        ];
        $trendTotal = 0;
        foreach ($trendStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $st = (string) $row['status'];
            $cnt = (int) $row['count'];
            $statusCounts[$st] = $cnt;
            $trendTotal += $cnt;
        }

        // Tren time series points
        $chartPoints = [];
        if ($period === '12m') {
            // Group by month
            $ptsStmt = $db->prepare(
                "SELECT DATE_FORMAT(created_at, '%Y-%m') AS time_key, COUNT(*) AS count "
                . "FROM applications WHERE created_at >= :start GROUP BY time_key ORDER BY time_key ASC"
            );
            $ptsStmt->execute(['start' => $startDate]);
            $rawPoints = $ptsStmt->fetchAll(PDO::FETCH_KEY_PAIR);

            // Populate all 12 months
            for ($i = 11; $i >= 0; $i--) {
                $monthKey = date('Y-m', strtotime("-{$i} months"));
                $monthName = date('M', strtotime("-{$i} months"));
                $chartPoints[] = [
                    'label' => $monthName,
                    'key' => $monthKey,
                    'count' => (int) ($rawPoints[$monthKey] ?? 0),
                ];
            }
        } else {
            // Group by date
            $ptsStmt = $db->prepare(
                "SELECT DATE(created_at) AS time_key, COUNT(*) AS count "
                . "FROM applications WHERE created_at >= :start GROUP BY time_key ORDER BY time_key ASC"
            );
            $ptsStmt->execute(['start' => $startDate]);
            $rawPoints = $ptsStmt->fetchAll(PDO::FETCH_KEY_PAIR);

            $step = $period === '90d' ? 3 : 1;
            for ($i = $periodDays; $i >= 0; $i -= $step) {
                $dayKey = date('Y-m-d', strtotime("-{$i} days"));
                $dayLabel = date('d M', strtotime("-{$i} days"));
                $count = 0;
                if ($step > 1) {
                    for ($s = 0; $s < $step; $s++) {
                        $subKey = date('Y-m-d', strtotime("-{$i} days +{$s} days"));
                        $count += (int) ($rawPoints[$subKey] ?? 0);
                    }
                } else {
                    $count = (int) ($rawPoints[$dayKey] ?? 0);
                }

                $chartPoints[] = [
                    'label' => $dayLabel,
                    'key' => $dayKey,
                    'count' => $count,
                ];
            }
        }

        // 4. Pendaftar Terbaru (5 baris terakhir)
        $latestApplicationsStmt = $db->query(
            'SELECT a.id, a.full_name, a.status, a.created_at, b.name AS batch_name, b.batch_type '
            . 'FROM applications a INNER JOIN batches b ON b.id = a.batch_id '
            . 'ORDER BY a.created_at DESC, a.id DESC LIMIT 5'
        );
        $latestApplications = $latestApplicationsStmt->fetchAll(PDO::FETCH_ASSOC);

        // 5. Pesan Terbaru (3 baris terakhir)
        $latestMessagesStmt = $db->query(
            'SELECT id, full_name, email, message, is_read, created_at '
            . 'FROM contact_messages ORDER BY created_at DESC, id DESC LIMIT 3'
        );
        $latestMessages = $latestMessagesStmt->fetchAll(PDO::FETCH_ASSOC);

        // 6. Aktivitas Terbaru (gabungan pendaftar & pesan, maksimal 6 entri)
        $activities = [];
        foreach ($latestApplications as $app) {
            $activities[] = [
                'type' => 'application',
                'title' => 'Pendaftar baru',
                'name' => $app['full_name'],
                'time' => $app['created_at'],
                'url' => url('/admin/pendaftar/' . (int) $app['id']),
                'icon' => 'user-plus',
            ];
        }
        foreach ($latestMessages as $msg) {
            $activities[] = [
                'type' => 'message',
                'title' => 'Pesan masuk',
                'name' => $msg['full_name'],
                'time' => $msg['created_at'],
                'url' => url('/admin/pesan/' . (int) $msg['id']),
                'icon' => 'mail',
            ];
        }

        usort($activities, static function (array $a, array $b): int {
            return strcmp((string) $b['time'], (string) $a['time']);
        });
        $activities = array_slice($activities, 0, 6);

        // Relative time helper
        $formatRelativeTime = static function (string $timestamp): string {
            $time = strtotime($timestamp);
            $diff = time() - $time;
            if ($diff < 60) {
                return 'baru saja';
            }
            if ($diff < 3600) {
                $min = (int) floor($diff / 60);
                return "{$min} menit lalu";
            }
            if ($diff < 86400) {
                $hours = (int) floor($diff / 3600);
                return "{$hours} jam lalu";
            }
            if ($diff < 604800) {
                $days = (int) floor($diff / 86400);
                return "{$days} hari lalu";
            }
            return date('d M Y', $time);
        };

        return View::render('admin/dashboard/index', [
            'title' => "Selamat datang kembali, {$adminName}",
            'subtitle' => 'Semoga harimu menyenangkan.',
            'page' => 'dashboard',
            'admin' => $admin,
            'breadcrumb' => ['Admin', 'Ringkasan'],
            'period' => $period,
            'periodLabel' => $periodLabel,
            'metrics' => [
                'new_applications' => [
                    'label' => 'Pendaftar baru',
                    'value' => $newApplicationsCount,
                    'badge' => $newApplicationsCount > 0 ? 'Perlu ditinjau' : null,
                    'note' => 'status Baru',
                    'url' => url('/admin/pendaftar?status=NEW'),
                ],
                'unread_messages' => [
                    'label' => 'Pesan belum dibaca',
                    'value' => $unreadMessagesCount,
                    'note' => "dari {$totalMessagesCount} pesan",
                    'url' => url('/admin/pesan'),
                ],
                'open_batches' => [
                    'label' => 'Batch sedang dibuka',
                    'value' => $openBatchesCount,
                    'note' => "Intern {$openInternBatches}, Volunteer {$openVolunteerBatches}",
                    'url' => url('/admin/batch'),
                ],
                'published_posts' => [
                    'label' => 'Berita terbit',
                    'value' => $publishedPostsCount,
                    'note' => "{$draftPostsCount} draf",
                    'url' => url('/admin/berita'),
                ],
            ],
            'trend' => [
                'total' => $trendTotal,
                'counts' => $statusCounts,
                'points' => $chartPoints,
            ],
            'latestApplications' => $latestApplications,
            'latestMessages' => $latestMessages,
            'activities' => $activities,
            'formatRelativeTime' => $formatRelativeTime,
        ], 'admin');
    }
}
