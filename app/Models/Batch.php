<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class Batch
{
    public static function all(?string $type = null): array
    {
        $sql = 'SELECT b.*, (SELECT COUNT(*) FROM team_members tm WHERE tm.batch_id = b.id) AS member_count, '
            . '(SELECT COUNT(*) FROM applications a WHERE a.batch_id = b.id) AS application_count '
            . 'FROM batches b';
        $params = [];
        if ($type !== null) {
            $sql .= ' WHERE b.batch_type = :batch_type';
            $params['batch_type'] = $type;
        }
        $sql .= ' ORDER BY b.created_at DESC, b.id DESC';
        $statement = Database::connection()->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function allWithMembers(): array
    {
        return self::all();
    }

    public static function countOpen(): int
    {
        return (int) Database::connection()->query('SELECT COUNT(*) FROM batches WHERE is_open = 1')->fetchColumn();
    }

    public static function byType(string $type): array
    {
        $statement = Database::connection()->prepare('SELECT * FROM batches WHERE batch_type = :batch_type ORDER BY id DESC');
        $statement->execute(['batch_type' => $type]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function publicByType(string $type): array
    {
        $statement = Database::connection()->prepare('SELECT * FROM batches WHERE batch_type = :batch_type ORDER BY id ASC');
        $statement->execute(['batch_type' => $type]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT b.*, (SELECT COUNT(*) FROM team_members tm WHERE tm.batch_id = b.id) AS member_count, '
            . '(SELECT COUNT(*) FROM applications a WHERE a.batch_id = b.id) AS application_count '
            . 'FROM batches b WHERE b.id = :id LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $batch = $statement->fetch(PDO::FETCH_ASSOC);
        return is_array($batch) ? $batch : null;
    }

    public static function save(string $type, string $name, ?int $id = null): void
    {
        if ($id === null) {
            $statement = Database::connection()->prepare('INSERT INTO batches (batch_type, name) VALUES (:batch_type, :name)');
            $statement->execute(['batch_type' => $type, 'name' => $name]);
            return;
        }

        $statement = Database::connection()->prepare('UPDATE batches SET batch_type = :batch_type, name = :name WHERE id = :id');
        $statement->execute(['batch_type' => $type, 'name' => $name, 'id' => $id]);
    }

    public static function toggle(int $id): bool
    {
        $statement = Database::connection()->prepare(
            'UPDATE batches SET opened_at = IF(is_open = 0, CURRENT_TIMESTAMP, opened_at), '
            . 'closed_at = IF(is_open = 1, CURRENT_TIMESTAMP, NULL), is_open = 1 - is_open WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
        return $statement->rowCount() > 0;
    }

    public static function delete(int $id): void
    {
        $statement = Database::connection()->prepare('DELETE FROM batches WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}
