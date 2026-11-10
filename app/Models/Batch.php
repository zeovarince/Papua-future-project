<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Cache;
use PDO;
use Throwable;

final class Batch
{
    public static function all(?string $type = null): array
    {
        $sql = 'SELECT b.* FROM batches b';
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

    public static function byType(string $type): array
    {
        $statement = Database::connection()->prepare('SELECT * FROM batches WHERE batch_type = :batch_type ORDER BY id DESC');
        $statement->execute(['batch_type' => $type]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function publicByType(string $type): array
    {
        return Cache::remember('batches:public:' . $type, 60, static function () use ($type): array {
            $statement = Database::connection()->prepare('SELECT * FROM batches WHERE batch_type = :batch_type ORDER BY id ASC');
            $statement->execute(['batch_type' => $type]);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    /** @return array{INTERN: int, VOLUNTEER: int} */
    public static function openCountsByType(): array
    {
        $counts = ['INTERN' => 0, 'VOLUNTEER' => 0];
        $statement = Database::connection()->query(
            "SELECT batch_type, COUNT(*) AS total FROM batches "
            . "WHERE is_open = 1 AND google_form_url IS NOT NULL AND google_form_url <> '' GROUP BY batch_type"
        );
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $counts[(string) $row['batch_type']] = (int) $row['total'];
        }
        return $counts;
    }

    public static function openForAdmin(): array
    {
        $statement = Database::connection()->query(
            'SELECT id, batch_type, name, is_open, google_form_url FROM batches ORDER BY batch_type, id DESC'
        );
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function publicFind(int $id, string $type): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, batch_type, name, is_open, google_form_url FROM batches WHERE id = :id AND batch_type = :batch_type LIMIT 1'
        );
        $statement->execute(['id' => $id, 'batch_type' => $type]);
        $batch = $statement->fetch(PDO::FETCH_ASSOC);
        return is_array($batch) ? $batch : null;
    }

    public static function find(int $id): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT b.*, (SELECT COUNT(*) FROM team_members tm WHERE tm.batch_id = b.id) AS member_count '
            . 'FROM batches b WHERE b.id = :id LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $batch = $statement->fetch(PDO::FETCH_ASSOC);
        return is_array($batch) ? $batch : null;
    }

    public static function save(string $type, string $name, ?int $id = null, ?string $formUrl = null): void
    {
        $formUrl = $formUrl === null || trim($formUrl) === '' ? null : trim($formUrl);
        if ($id === null) {
            $statement = Database::connection()->prepare('INSERT INTO batches (batch_type, name, google_form_url) VALUES (:batch_type, :name, :google_form_url)');
            $statement->execute(['batch_type' => $type, 'name' => $name, 'google_form_url' => $formUrl]);
            Cache::clearPublic();
            return;
        }

        $statement = Database::connection()->prepare(
            'UPDATE batches SET batch_type = :batch_type, name = :name, google_form_url = :google_form_url, '
            . 'closed_at = IF(:has_form_url_close = 0 AND is_open = 1, CURRENT_TIMESTAMP, closed_at), '
            . 'is_open = IF(:has_form_url_state = 0, 0, is_open) WHERE id = :id'
        );
        $statement->execute([
            'batch_type' => $type,
            'name' => $name,
            'google_form_url' => $formUrl,
            'has_form_url_close' => $formUrl === null || trim($formUrl) === '' ? 0 : 1,
            'has_form_url_state' => $formUrl === null || trim($formUrl) === '' ? 0 : 1,
            'id' => $id,
        ]);
        Cache::clearPublic();
    }

    /** @return array{updated: bool, reason: ?string, is_open: bool} */
    public static function toggle(int $id): array
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $statement = $pdo->prepare('SELECT is_open, google_form_url FROM batches WHERE id = :id FOR UPDATE');
            $statement->execute(['id' => $id]);
            $batch = $statement->fetch(PDO::FETCH_ASSOC);
            if (!is_array($batch)) {
                $pdo->rollBack();
                return ['updated' => false, 'reason' => 'not_found', 'is_open' => false];
            }

            $currentlyOpen = (int) $batch['is_open'] === 1;
            $hasFormUrl = trim((string) ($batch['google_form_url'] ?? '')) !== '';
            if (!$currentlyOpen && !$hasFormUrl) {
                $pdo->rollBack();
                return ['updated' => false, 'reason' => 'missing_form_url', 'is_open' => false];
            }

            $update = $pdo->prepare(
                'UPDATE batches SET '
                . 'opened_at = IF(is_open = 0, CURRENT_TIMESTAMP, opened_at), '
                . 'closed_at = IF(is_open = 1, CURRENT_TIMESTAMP, NULL), '
                . 'is_open = IF(is_open = 1, 0, 1) WHERE id = :id'
            );
            $update->execute(['id' => $id]);
            $pdo->commit();
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }
        Cache::clearPublic();
        return ['updated' => true, 'reason' => null, 'is_open' => !$currentlyOpen];
    }

    /**
     * Delete a batch only when no team members refer to it.
     *
     * @return array{deleted: bool, reason: ?string, member_count: int}
     */
    public static function deleteIfNoMembers(int $id): array
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $batchStatement = $pdo->prepare('SELECT id FROM batches WHERE id = :id FOR UPDATE');
            $batchStatement->execute(['id' => $id]);
            if ($batchStatement->fetchColumn() === false) {
                $pdo->rollBack();
                return ['deleted' => false, 'reason' => 'not_found', 'member_count' => 0];
            }

            $memberStatement = $pdo->prepare('SELECT COUNT(*) FROM team_members WHERE batch_id = :id');
            $memberStatement->execute(['id' => $id]);
            $memberCount = (int) $memberStatement->fetchColumn();
            if ($memberCount > 0) {
                $pdo->rollBack();
                return ['deleted' => false, 'reason' => 'has_members', 'member_count' => $memberCount];
            }

            $deleteBatch = $pdo->prepare('DELETE FROM batches WHERE id = :id');
            $deleteBatch->execute(['id' => $id]);
            $pdo->commit();
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }

        Cache::clearPublic();
        return ['deleted' => true, 'reason' => null, 'member_count' => 0];
    }
}
