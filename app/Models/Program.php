<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Cache;
use PDO;

final class Program
{
    public static function publicList(): array
    {
        return Cache::remember('programs:list', 60, static function (): array {
            return Database::connection()->query(
                'SELECT id, slug, category_label, title, summary, cover_image, stat1_value, stat1_label, stat2_value, stat2_label '
                . 'FROM programs WHERE is_published = 1 ORDER BY sort_order, title, id'
            )->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    public static function featured(int $limit = 3): array
    {
        $limit = max(1, min(12, $limit));
        return Cache::remember('programs:featured:' . $limit, 60, static function () use ($limit): array {
            $statement = Database::connection()->prepare(
                'SELECT id, slug, category_label, title, summary, cover_image, stat1_value, stat1_label, stat2_value, stat2_label '
                . 'FROM programs WHERE is_published = 1 AND is_featured = 1 ORDER BY sort_order, title, id LIMIT :limit'
            );
            $statement->bindValue('limit', $limit, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    public static function adminList(): array
    {
        return Database::connection()->query(
            'SELECT p.id, p.slug, p.title, p.is_published, p.is_featured, p.sort_order, '
            . '(SELECT COUNT(*) FROM program_photos pp WHERE pp.program_id = p.id) AS photo_count '
            . 'FROM programs p ORDER BY p.sort_order, p.title, p.id'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?array
    {
        $statement = Database::connection()->prepare('SELECT * FROM programs WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? $row : null;
    }

    public static function findPublicBySlug(string $slug): ?array
    {
        return Cache::remember('programs:slug:' . $slug, 60, static function () use ($slug): ?array {
            $statement = Database::connection()->prepare('SELECT * FROM programs WHERE slug = :slug AND is_published = 1 LIMIT 1');
            $statement->execute(['slug' => $slug]);
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            return is_array($row) ? $row : null;
        });
    }

    public static function photos(int $id): array
    {
        return Cache::remember('programs:photos:' . $id, 60, static function () use ($id): array {
            $statement = Database::connection()->prepare('SELECT * FROM program_photos WHERE program_id = :id ORDER BY sort_order, id');
            $statement->execute(['id' => $id]);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    public static function photo(int $photoId, int $programId): ?array
    {
        $statement = Database::connection()->prepare('SELECT * FROM program_photos WHERE id = :photo AND program_id = :program LIMIT 1');
        $statement->execute(['photo' => $photoId, 'program' => $programId]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? $row : null;
    }

    public static function slugExists(string $slug, ?int $except = null): bool
    {
        $sql = 'SELECT 1 FROM programs WHERE slug = :slug' . ($except === null ? '' : ' AND id <> :id') . ' LIMIT 1';
        $statement = Database::connection()->prepare($sql);
        $params = ['slug' => $slug];
        if ($except !== null) $params['id'] = $except;
        $statement->execute($params);
        return (bool) $statement->fetchColumn();
    }

    public static function save(array $data, ?int $id = null): int
    {
        $pdo = Database::connection();
        if ($id === null) {
            $statement = $pdo->prepare('INSERT INTO programs (slug, category_label, title, summary, tagline, body, activities, partners, info_rows, stat1_value, stat1_label, stat2_value, stat2_label, cover_image, sort_order, is_featured, is_published) VALUES (:slug,:category_label,:title,:summary,:tagline,:body,:activities,:partners,:info_rows,:stat1_value,:stat1_label,:stat2_value,:stat2_label,:cover_image,:sort_order,:is_featured,:is_published)');
        } else {
            $statement = $pdo->prepare('UPDATE programs SET slug=:slug, category_label=:category_label, title=:title, summary=:summary, tagline=:tagline, body=:body, activities=:activities, partners=:partners, info_rows=:info_rows, stat1_value=:stat1_value, stat1_label=:stat1_label, stat2_value=:stat2_value, stat2_label=:stat2_label, cover_image=:cover_image, sort_order=:sort_order, is_featured=:is_featured, is_published=:is_published WHERE id=:id');
        }
        if ($id !== null) $data['id'] = $id;
        $statement->execute($data);
        Cache::clearPublic();
        return $id ?? (int) $pdo->lastInsertId();
    }

    public static function updatePhoto(int $photoId, int $programId, string $caption, int $sort): void
    {
        $statement = Database::connection()->prepare('UPDATE program_photos SET caption=:caption, sort_order=:sort WHERE id=:photo AND program_id=:program');
        $statement->execute(['caption' => $caption !== '' ? $caption : null, 'sort' => $sort, 'photo' => $photoId, 'program' => $programId]);
        Cache::clearPublic();
    }

    public static function addPhoto(int $programId, string $path): void
    {
        $statement = Database::connection()->prepare('INSERT INTO program_photos (program_id, file_path) VALUES (:program, :path)');
        $statement->execute(['program' => $programId, 'path' => $path]);
        Cache::clearPublic();
    }

    public static function addPhotos(int $programId, array $paths): void
    {
        $pdo=Database::connection();$pdo->beginTransaction();
        try{foreach($paths as $path){$statement=$pdo->prepare('INSERT INTO program_photos (program_id,file_path) VALUES (:program,:path)');$statement->execute(['program'=>$programId,'path'=>$path]);}$pdo->commit();}
        catch(\Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
        Cache::clearPublic();
    }

    public static function deletePhoto(int $photoId, int $programId): void
    {
        $statement = Database::connection()->prepare('DELETE FROM program_photos WHERE id=:photo AND program_id=:program');
        $statement->execute(['photo' => $photoId, 'program' => $programId]);
        Cache::clearPublic();
    }

    public static function delete(int $id): void
    {
        $statement = Database::connection()->prepare('DELETE FROM programs WHERE id=:id');
        $statement->execute(['id' => $id]);
        Cache::clearPublic();
    }
}
