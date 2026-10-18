<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Cache;
use App\Core\Database;
use PDO;

final class Post
{
    public static function publicList(?string $category = null, int $limit = 100): array
    {
        $allowedCategories = ['ACTIVITY', 'UPDATE', 'MEDIA', 'AWARD'];
        if ($category !== null && !in_array($category, $allowedCategories, true)) {
            $category = null;
        }
        $limit = max(1, min(200, $limit));
        $cacheKey = 'posts:list:' . ($category ?? 'all') . ':' . $limit;

        return Cache::remember($cacheKey, 60, static function () use ($category, $limit): array {
            $sql = 'SELECT id, slug, category, title, excerpt, cover_image, author_name, meta_label, source_name, source_url, published_at '
                . 'FROM posts WHERE is_published = 1 AND published_at <= CURRENT_TIMESTAMP';
            $parameters = [];
            if ($category !== null) {
                $sql .= ' AND category = :category';
                $parameters['category'] = $category;
            }
            $sql .= ' ORDER BY published_at DESC, id DESC LIMIT :limit';
            $statement = Database::connection()->prepare($sql);
            foreach ($parameters as $key => $value) {
                $statement->bindValue($key, $value);
            }
            $statement->bindValue('limit', $limit, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    public static function featured(int $limit = 3): array
    {
        return self::publicList(null, $limit);
    }

    public static function adminList(): array
    {
        return Database::connection()->query(
            'SELECT p.id, p.slug, p.category, p.title, p.published_at, p.is_published, '
            . '(SELECT COUNT(*) FROM post_photos pp WHERE pp.post_id = p.id) AS photo_count '
            . 'FROM posts p ORDER BY p.published_at DESC, p.id DESC'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?array
    {
        $statement = Database::connection()->prepare('SELECT * FROM posts WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? $row : null;
    }

    public static function findPublicBySlug(string $slug): ?array
    {
        return Cache::remember('posts:slug:' . $slug, 60, static function () use ($slug): ?array {
            $statement = Database::connection()->prepare(
                'SELECT * FROM posts WHERE slug = :slug AND is_published = 1 AND published_at <= CURRENT_TIMESTAMP LIMIT 1'
            );
            $statement->execute(['slug' => $slug]);
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            return is_array($row) ? $row : null;
        });
    }

    public static function related(int $id, string $category, int $limit = 3): array
    {
        $limit = max(1, min(6, $limit));
        $cacheKey = 'posts:related:' . $id . ':' . $category . ':' . $limit;
        return Cache::remember($cacheKey, 60, static function () use ($id, $category, $limit): array {
            $statement = Database::connection()->prepare(
                'SELECT id, slug, category, title, excerpt, cover_image, published_at '
                . 'FROM posts WHERE id <> :id AND category = :category AND is_published = 1 '
                . 'AND published_at <= CURRENT_TIMESTAMP ORDER BY published_at DESC, id DESC LIMIT :limit'
            );
            $statement->bindValue('id', $id, PDO::PARAM_INT);
            $statement->bindValue('category', $category);
            $statement->bindValue('limit', $limit, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    public static function photos(int $id): array
    {
        return Cache::remember('posts:photos:' . $id, 60, static function () use ($id): array {
            $statement = Database::connection()->prepare('SELECT * FROM post_photos WHERE post_id = :id ORDER BY sort_order, id');
            $statement->execute(['id' => $id]);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    public static function photo(int $photoId, int $postId): ?array
    {
        $statement = Database::connection()->prepare('SELECT * FROM post_photos WHERE id = :photo AND post_id = :post LIMIT 1');
        $statement->execute(['photo' => $photoId, 'post' => $postId]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? $row : null;
    }

    public static function slugExists(string $slug, ?int $except = null): bool
    {
        $sql = 'SELECT 1 FROM posts WHERE slug = :slug' . ($except === null ? '' : ' AND id <> :id') . ' LIMIT 1';
        $statement = Database::connection()->prepare($sql);
        $parameters = ['slug' => $slug];
        if ($except !== null) {
            $parameters['id'] = $except;
        }
        $statement->execute($parameters);
        return (bool) $statement->fetchColumn();
    }

    public static function save(array $data, ?int $id = null): int
    {
        $sql = $id === null
            ? 'INSERT INTO posts (slug, category, title, excerpt, content, cover_image, author_name, meta_label, source_name, source_url, published_at, is_published) VALUES (:slug, :category, :title, :excerpt, :content, :cover_image, :author_name, :meta_label, :source_name, :source_url, :published_at, :is_published)'
            : 'UPDATE posts SET slug = :slug, category = :category, title = :title, excerpt = :excerpt, content = :content, cover_image = :cover_image, author_name = :author_name, meta_label = :meta_label, source_name = :source_name, source_url = :source_url, published_at = :published_at, is_published = :is_published WHERE id = :id';
        if ($id !== null) {
            $data['id'] = $id;
        }
        $statement = Database::connection()->prepare($sql);
        $statement->execute($data);
        Cache::clearPublic();
        return $id ?? (int) Database::connection()->lastInsertId();
    }

    public static function addPhoto(int $id, string $path): void
    {
        $statement = Database::connection()->prepare('INSERT INTO post_photos (post_id, file_path) VALUES (:id, :path)');
        $statement->execute(['id' => $id, 'path' => $path]);
        Cache::clearPublic();
    }

    public static function addPhotos(int $id, array $paths): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            foreach ($paths as $path) {
                $statement = $pdo->prepare('INSERT INTO post_photos (post_id, file_path) VALUES (:id, :path)');
                $statement->execute(['id' => $id, 'path' => $path]);
            }
            $pdo->commit();
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }
        Cache::clearPublic();
    }

    public static function updatePhoto(int $photoId, int $postId, string $caption, int $sort): void
    {
        $statement = Database::connection()->prepare('UPDATE post_photos SET caption = :caption, sort_order = :sort WHERE id = :photo AND post_id = :post');
        $statement->execute(['caption' => $caption !== '' ? $caption : null, 'sort' => $sort, 'photo' => $photoId, 'post' => $postId]);
        Cache::clearPublic();
    }

    public static function deletePhoto(int $photoId, int $postId): void
    {
        $statement = Database::connection()->prepare('DELETE FROM post_photos WHERE id = :photo AND post_id = :post');
        $statement->execute(['photo' => $photoId, 'post' => $postId]);
        Cache::clearPublic();
    }

    public static function delete(int $id): void
    {
        $statement = Database::connection()->prepare('DELETE FROM posts WHERE id = :id');
        $statement->execute(['id' => $id]);
        Cache::clearPublic();
    }

    public static function countPublished(): int
    {
        return Cache::remember('posts:count-published', 60, static function (): int {
            return (int) Database::connection()->query(
                'SELECT COUNT(*) FROM posts WHERE is_published = 1 AND published_at <= CURRENT_TIMESTAMP'
            )->fetchColumn();
        });
    }
}
