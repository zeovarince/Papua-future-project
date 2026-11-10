<?php

declare(strict_types=1);

namespace App\Core;

final class Seo
{
    private const NO_INDEX_PAGES = ['join-form', 'join-success', 'not-found', 'error'];
    private const DEFAULT_SOCIAL_IMAGE = 'https://res.cloudinary.com/crvzukvh/image/upload/f_auto,q_auto/herosectionbanner2';

    public static function forPage(array $data): array
    {
        $page = (string) ($data['page'] ?? '');
        $title = trim((string) ($data['title'] ?? 'Papua Future Project'));
        $description = self::description($data, $page);
        $path = self::currentPath();
        $canonical = self::absoluteUrl($path);
        $image = self::DEFAULT_SOCIAL_IMAGE;

        if ($page === 'program-detail' && !empty($data['program']['cover_image'])) {
            $image = self::absoluteUrl('/uploads/' . ltrim((string) $data['program']['cover_image'], '/'));
        } elseif ($page === 'news-detail' && !empty($data['post']['cover_image'])) {
            $image = self::absoluteUrl('/uploads/' . ltrim((string) $data['post']['cover_image'], '/'));
        }

        $organization = self::organizationSchema();
        $schemaPageType = match ($page) {
            'about' => 'AboutPage',
            'contact' => 'ContactPage',
            'program', 'news' => 'CollectionPage',
            default => 'WebPage',
        };
        $schemas = [[
            '@context' => 'https://schema.org',
            '@type' => $schemaPageType,
            'name' => $title,
            'description' => $description,
            'url' => $canonical,
            'inLanguage' => 'id-ID',
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => $organization['name'],
                'url' => self::absoluteUrl('/'),
            ],
        ]];

        if ($page === 'home') {
            $schemas[] = $organization;
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => $organization['name'],
                'url' => self::absoluteUrl('/'),
                'inLanguage' => 'id-ID',
                'publisher' => ['@id' => self::absoluteUrl('/') . '#organization'],
            ];
        } elseif ($page === 'news-detail' && is_array($data['post'] ?? null)) {
            $post = $data['post'];
            $article = [
                '@context' => 'https://schema.org',
                '@type' => 'NewsArticle',
                'headline' => (string) ($post['title'] ?? $title),
                'description' => $description,
                'mainEntityOfPage' => $canonical,
                'author' => [
                    '@type' => 'Organization',
                    'name' => (string) ($post['author_name'] ?? $organization['name']),
                ],
                'publisher' => ['@id' => self::absoluteUrl('/') . '#organization'],
            ];
            $published = self::isoDate((string) ($post['published_at'] ?? ''));
            if ($published !== null) {
                $article['datePublished'] = $published;
            }
            if (!empty($post['updated_at'])) {
                $modified = self::isoDate((string) $post['updated_at']);
                if ($modified !== null) {
                    $article['dateModified'] = $modified;
                }
            }
            $article['image'] = $image;
            $schemas[] = $article;
            $schemas[] = self::breadcrumbSchema([
                ['name' => 'Beranda', 'path' => '/'],
                ['name' => 'Berita & Kegiatan', 'path' => '/berita'],
                ['name' => (string) ($post['title'] ?? $title), 'path' => $path],
            ]);
        } elseif ($page === 'program-detail' && is_array($data['program'] ?? null)) {
            $program = $data['program'];
            $schemas[] = self::breadcrumbSchema([
                ['name' => 'Beranda', 'path' => '/'],
                ['name' => 'Program', 'path' => '/program'],
                ['name' => (string) ($program['title'] ?? $title), 'path' => $path],
            ]);
        }

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'image' => $image,
            'imageAlt' => self::imageAlt($page, $data),
            'publishedAt' => $page === 'news-detail' ? self::isoDate((string) ($data['post']['published_at'] ?? '')) : null,
            'modifiedAt' => $page === 'news-detail' ? self::isoDate((string) ($data['post']['updated_at'] ?? '')) : null,
            'robots' => getenv('APP_ENV') === 'production'
                && !in_array($page, self::NO_INDEX_PAGES, true)
                ? 'index,follow,max-image-preview:large'
                : 'noindex,nofollow',
            'type' => $page === 'news-detail' ? 'article' : 'website',
            'schemas' => $schemas,
        ];
    }

    public static function absoluteUrl(string $path): string
    {
        $configuredUrl = trim((string) (getenv('APP_URL') ?: ''));
        $parts = parse_url($configuredUrl);
        if (!is_array($parts) || !in_array(strtolower((string) ($parts['scheme'] ?? '')), ['http', 'https'], true)
            || empty($parts['host']) || isset($parts['user']) || isset($parts['pass'])) {
            return $path;
        }

        $base = rtrim($configuredUrl, '/');
        if ($path === '/') {
            return $base . '/';
        }

        return $base . '/' . ltrim($path, '/');
    }

    private static function description(array $data, string $page): string
    {
        $custom = (string) ($data['seoDescription'] ?? '');
        $content = match ($page) {
            'program-detail' => (string) (($data['program']['summary'] ?? '') ?: ($data['program']['tagline'] ?? '')),
            'news-detail' => (string) (($data['post']['excerpt'] ?? '') ?: ($data['post']['content'] ?? '')),
            'about' => 'Kenali cerita Papua Future Project, visi dan misi, tim penggerak, intern, serta relawan yang bergerak bersama anak-anak di Papua Barat.',
            'program' => 'Kenali program Papua Future Project di Papua Barat: bimbingan belajar, literasi digital, pojok baca, kesehatan, dan pemberdayaan pemuda.',
            'news' => 'Berita dan kegiatan terbaru Papua Future Project, dokumentasi lapangan, penghargaan, serta liputan media.',
            'contact' => 'Hubungi Papua Future Project untuk pertanyaan, kolaborasi, informasi lokasi, dan dukungan bagi anak-anak di Papua Barat.',
            'join' => 'Lihat informasi pendaftaran internship dan relawan Papua Future Project di Manokwari, Papua Barat.',
            'join-success' => 'Konfirmasi penerimaan pendaftaran Papua Future Project.',
            'not-found' => 'Halaman yang Anda cari tidak ditemukan di website Papua Future Project.',
            'error' => 'Halaman Papua Future Project sedang mengalami kendala. Silakan coba kembali.',
            default => 'Papua Future Project bergerak mendukung pendidikan dan kesehatan anak di Papua Barat dengan semangat Every Child Matters.',
        };

        $text = trim(preg_replace('/\s+/u', ' ', strip_tags($custom !== '' ? $custom : $content)) ?? '');
        if (mb_strlen($text) > 160) {
            $text = rtrim(mb_substr($text, 0, 157), " \t\n\r\0\x0B.,;:-") . '...';
        }

        return $text;
    }

    private static function imageAlt(string $page, array $data): string
    {
        return match ($page) {
            'program-detail' => (string) (($data['program']['title'] ?? '') ?: 'Dokumentasi program Papua Future Project'),
            'news-detail' => (string) (($data['post']['title'] ?? '') ?: 'Berita Papua Future Project'),
            default => 'Anak-anak dan relawan Papua Future Project di Papua Barat',
        };
    }

    private static function currentPath(): string
    {
        $path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
        $path = is_string($path) && $path !== '' ? $path : '/';
        $basePath = function_exists('app_base_path') ? app_base_path() : '';
        if ($basePath !== '' && str_starts_with($path, $basePath . '/')) {
            $path = substr($path, strlen($basePath));
        } elseif ($basePath !== '' && $path === $basePath) {
            $path = '/';
        }

        $path = '/' . ltrim($path, '/');
        return $path === '//' ? '/' : $path;
    }

    private static function organizationSchema(): array
    {
        $name = 'Papua Future Project';
        return [
            '@context' => 'https://schema.org',
            '@type' => 'NGO',
            '@id' => self::absoluteUrl('/') . '#organization',
            'name' => $name,
            'url' => self::absoluteUrl('/'),
            'logo' => self::absoluteUrl('/assets/img/logo.png'),
            'description' => 'Papua Future Project bergerak dalam pendidikan dan kesehatan anak di Papua Barat.',
            'sameAs' => ['https://www.instagram.com/papuafutureproject/'],
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Manokwari',
                'addressRegion' => 'Papua Barat',
                'addressCountry' => 'ID',
            ],
        ];
    }

    private static function breadcrumbSchema(array $items): array
    {
        $elements = [];
        foreach (array_values($items) as $index => $item) {
            $elements[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => (string) $item['name'],
                'item' => self::absoluteUrl((string) $item['path']),
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $elements,
        ];
    }

    private static function isoDate(string $date): ?string
    {
        $timestamp = strtotime($date);
        return $timestamp === false ? null : gmdate('c', $timestamp);
    }
}
