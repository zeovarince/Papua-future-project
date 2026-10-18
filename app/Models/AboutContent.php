<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Cache;
use PDO;

final class AboutContent
{
    public static function current(): ?array
    {
        return Cache::remember('about:current', 60, static function (): ?array {
            $statement = Database::connection()->query('SELECT id, story, vision, mission, updated_at FROM about_content WHERE id = 1 LIMIT 1');
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            return is_array($row) ? $row : null;
        });
    }

    public static function save(array $values): void
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO about_content (id, story, vision, mission) VALUES (1, :story, :vision, :mission) '
            . 'ON DUPLICATE KEY UPDATE story = VALUES(story), vision = VALUES(vision), mission = VALUES(mission)'
        );
        $statement->execute([
            'story' => $values['story'],
            'vision' => $values['vision'],
            'mission' => $values['mission'],
        ]);
        Cache::clearPublic();
    }
}
