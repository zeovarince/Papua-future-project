<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Application;
use App\Models\ContactMessage;

/**
 * Single source of truth for admin navigation (rail, flyout, mobile drawer).
 */
final class AdminMenu
{
    /**
     * Retrieve the unified menu items hierarchy.
     */
    public static function items(?string $currentUri = null, ?string $currentPage = null): array
    {
        $uri = $currentUri ?? (string) ($_SERVER['REQUEST_URI'] ?? '/admin');
        $path = (string) (parse_url($uri, PHP_URL_PATH) ?? '/admin');

        // Lazy badge loaders (safe if models fail or table empty)
        $newApplicationsCount = 0;
        $unreadMessagesCount = 0;
        try {
            $newApplicationsCount = Application::countNew();
        } catch (\Throwable) {
            $newApplicationsCount = 0;
        }

        try {
            $unreadMessagesCount = ContactMessage::unreadCount();
        } catch (\Throwable) {
            $unreadMessagesCount = 0;
        }

        $dataBadgeTotal = $newApplicationsCount + $unreadMessagesCount;

        $isActive = static function (string $currentPath, string $route, ?string $currentPage, ?string $pageKey): bool {
            if ($currentPage !== null && $pageKey !== null && $currentPage === $pageKey) {
                return true;
            }
            if ($currentPath === $route) {
                return true;
            }
            return str_starts_with($currentPath, rtrim($route, '/') . '/');
        };

        $groups = [
            [
                'id' => 'dashboard',
                'label' => 'Ringkasan',
                'icon' => 'layout-dashboard',
                'description' => 'Ringkasan aktivitas dan data terbaru.',
                'route' => '/admin',
                'badge' => 0,
                'is_single' => true,
                'items' => [
                    [
                        'id' => 'dashboard',
                        'label' => 'Ringkasan',
                        'route' => '/admin',
                        'description' => 'Ringkasan aktivitas dan data terbaru.',
                        'icon' => 'layout-dashboard',
                        'badge' => 0,
                    ],
                ],
                'is_active' => ($path === '/admin' || $currentPage === 'dashboard'),
            ],
            [
                'id' => 'about',
                'label' => 'Tentang Kami',
                'icon' => 'info',
                'description' => 'Cerita, visi, dan misi organisasi.',
                'route' => '/admin/tentang',
                'badge' => 0,
                'is_single' => true,
                'items' => [
                    [
                        'id' => 'about',
                        'label' => 'Tentang Kami',
                        'route' => '/admin/tentang',
                        'description' => 'Cerita, visi, dan misi organisasi.',
                        'icon' => 'info',
                        'badge' => 0,
                    ],
                ],
                'is_active' => $isActive($path, '/admin/tentang', $currentPage, 'about'),
            ],
            [
                'id' => 'team',
                'label' => 'Tim',
                'icon' => 'users',
                'description' => 'Pengurus, magang, relawan, dan batch.',
                'route' => '/admin/tim/penggerak',
                'badge' => 0,
                'is_single' => false,
                'items' => [
                    [
                        'id' => 'team-penggerak',
                        'label' => 'Tim Penggerak',
                        'route' => '/admin/tim/penggerak',
                        'description' => 'Pengurus dan founder.',
                        'icon' => 'user-check',
                        'badge' => 0,
                        'is_active' => $isActive($path, '/admin/tim/penggerak', $currentPage, 'team-penggerak'),
                    ],
                    [
                        'id' => 'team-internship',
                        'label' => 'Internship',
                        'route' => '/admin/tim/internship',
                        'description' => 'Anggota magang per batch.',
                        'icon' => 'graduation-cap',
                        'badge' => 0,
                        'is_active' => $isActive($path, '/admin/tim/internship', $currentPage, 'team-internship'),
                    ],
                    [
                        'id' => 'team-volunteer',
                        'label' => 'Volunteer',
                        'route' => '/admin/tim/volunteer',
                        'description' => 'Relawan per batch.',
                        'icon' => 'hand-heart',
                        'badge' => 0,
                        'is_active' => $isActive($path, '/admin/tim/volunteer', $currentPage, 'team-volunteer'),
                    ],
                    [
                        'id' => 'batch',
                        'label' => 'Batch',
                        'route' => '/admin/batch',
                        'description' => 'Periode Intern dan Volunteer.',
                        'icon' => 'layers',
                        'badge' => 0,
                        'is_active' => $isActive($path, '/admin/batch', $currentPage, 'batch'),
                    ],
                ],
            ],
            [
                'id' => 'content',
                'label' => 'Konten',
                'icon' => 'newspaper',
                'description' => 'Program, berita, dan pertanyaan umum.',
                'route' => '/admin/program',
                'badge' => 0,
                'is_single' => false,
                'items' => [
                    [
                        'id' => 'program',
                        'label' => 'Program',
                        'route' => '/admin/program',
                        'description' => 'Program, kegiatan, dan foto dokumentasi.',
                        'icon' => 'book-open',
                        'badge' => 0,
                        'is_active' => $isActive($path, '/admin/program', $currentPage, 'program'),
                    ],
                    [
                        'id' => 'post',
                        'label' => 'Berita',
                        'route' => '/admin/berita',
                        'description' => 'Berita, liputan, dan penghargaan.',
                        'icon' => 'newspaper',
                        'badge' => 0,
                        'is_active' => $isActive($path, '/admin/berita', $currentPage, 'post'),
                    ],
                    [
                        'id' => 'faq',
                        'label' => 'FAQ',
                        'route' => '/admin/faq',
                        'description' => 'Pertanyaan yang sering diajukan.',
                        'icon' => 'help-circle',
                        'badge' => 0,
                        'is_active' => $isActive($path, '/admin/faq', $currentPage, 'faq'),
                    ],
                ],
            ],
            [
                'id' => 'data',
                'label' => 'Data',
                'icon' => 'inbox',
                'description' => 'Pendaftar, pesan masuk, dan form pendaftaran.',
                'route' => '/admin/pendaftar',
                'badge' => $dataBadgeTotal,
                'is_single' => false,
                'items' => [
                    [
                        'id' => 'application',
                        'label' => 'Pendaftar',
                        'route' => '/admin/pendaftar',
                        'description' => 'Tinjau dan ubah status pendaftar.',
                        'icon' => 'clipboard-list',
                        'badge' => $newApplicationsCount,
                        'is_active' => $isActive($path, '/admin/pendaftar', $currentPage, 'application'),
                    ],
                    [
                        'id' => 'message',
                        'label' => 'Pesan Masuk',
                        'route' => '/admin/pesan',
                        'description' => 'Pesan dari formulir kontak.',
                        'icon' => 'mail',
                        'badge' => $unreadMessagesCount,
                        'is_active' => $isActive($path, '/admin/pesan', $currentPage, 'message'),
                    ],
                    [
                        'id' => 'application-form',
                        'label' => 'Form Pendaftaran',
                        'route' => '/admin/pendaftaran',
                        'description' => 'Buka atau tutup form dan atur pertanyaan.',
                        'icon' => 'file-cog',
                        'badge' => 0,
                        'is_active' => $isActive($path, '/admin/pendaftaran', $currentPage, 'application-form'),
                    ],
                ],
            ],
            [
                'id' => 'settings',
                'label' => 'Pengaturan',
                'icon' => 'settings',
                'description' => 'Kelola akun dan keamanan admin.',
                'route' => '/admin/akun',
                'badge' => 0,
                'is_single' => false,
                'is_active' => in_array($currentPage, ['admin-accounts', 'admin-password'], true),
                'items' => [
                    [
                        'id' => 'admin-accounts',
                        'label' => 'Pengelola Admin',
                        'route' => '/admin/akun',
                        'description' => 'Buat akun admin baru.',
                        'icon' => 'users',
                        'badge' => 0,
                        'is_active' => $currentPage === 'admin-accounts',
                    ],
                    [
                        'id' => 'admin-password',
                        'label' => 'Ubah Kata Sandi',
                        'route' => '/admin/akun/password',
                        'description' => 'Perbarui kata sandi akun sendiri.',
                        'icon' => 'lock',
                        'badge' => 0,
                        'is_active' => $currentPage === 'admin-password',
                    ],
                ],
            ],
        ];

        // Resolve active group based on child items
        foreach ($groups as &$group) {
            if (!isset($group['is_active'])) {
                $group['is_active'] = false;
                foreach ($group['items'] as $item) {
                    if (!empty($item['is_active'])) {
                        $group['is_active'] = true;
                        break;
                    }
                }
            }
        }
        unset($group);

        return $groups;
    }
}

