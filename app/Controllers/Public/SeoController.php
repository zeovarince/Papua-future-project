<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Response;
use App\Core\Seo;
use App\Models\Post;
use App\Models\Program;

final class SeoController
{
    public function sitemap(): Response
    {
        $paths = ['/', '/tentang', '/program', '/berita', '/bergabung', '/kontak'];
        foreach (Program::publicList() as $program) {
            $paths[] = '/program/' . rawurlencode((string) $program['slug']);
        }
        foreach (Post::publicList(null, 200) as $post) {
            $paths[] = '/berita/' . rawurlencode((string) $post['slug']);
        }

        $locations = '';
        foreach (array_values(array_unique($paths)) as $path) {
            $locations .= "  <url><loc>" . self::xml(Seo::absoluteUrl($path)) . "</loc></url>\n";
        }

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
            . "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n"
            . $locations
            . "</urlset>\n";

        return new Response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function robots(): Response
    {
        if (getenv('APP_ENV') !== 'production') {
            return new Response("User-agent: *\nDisallow: /\n", 200, [
                'Content-Type' => 'text/plain; charset=UTF-8',
                'Cache-Control' => 'public, max-age=3600',
            ]);
        }

        $adminUrl = Seo::absoluteUrl(function_exists('admin_path') ? (admin_path() ?? '/admin') : '/admin');
        $adminPath = (string) (parse_url($adminUrl, PHP_URL_PATH) ?: '/admin');
        $body = "User-agent: *\nAllow: /\nDisallow: " . rtrim($adminPath, '/') . "/\n"
            . "Sitemap: " . Seo::absoluteUrl('/sitemap.xml') . "\n";

        return new Response($body, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private static function xml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
