<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class TeamMember
{
    public static function founder(): ?array
    {
        $statement = Database::connection()->query(
            "SELECT * FROM team_members WHERE member_group = 'CORE' AND is_founder = 1 AND is_published = 1 LIMIT 1"
        );
        $member = $statement->fetch(PDO::FETCH_ASSOC);
        return is_array($member) ? $member : null;
    }

    public static function allByGroup(string $group): array
    {
        $statement = Database::connection()->prepare(
            'SELECT tm.*, b.name AS batch_name, b.batch_type, b.is_open '
            . 'FROM team_members tm LEFT JOIN batches b ON b.id = tm.batch_id '
            . 'WHERE tm.member_group = :member_group AND tm.is_published = 1 '
            . 'ORDER BY COALESCE(b.id, 0) ASC, tm.sort_order ASC, tm.name ASC'
        );
        $statement->execute(['member_group' => $group]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function adminList(string $group): array
    {
        $statement = Database::connection()->prepare(
            'SELECT tm.*, b.name AS batch_name FROM team_members tm '
            . 'LEFT JOIN batches b ON b.id = tm.batch_id '
            . 'WHERE tm.member_group = :member_group '
            . 'ORDER BY COALESCE(b.id, 0) DESC, tm.sort_order ASC, tm.name ASC'
        );
        $statement->execute(['member_group' => $group]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?array
    {
        $statement = Database::connection()->prepare('SELECT * FROM team_members WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $member = $statement->fetch(PDO::FETCH_ASSOC);
        return is_array($member) ? $member : null;
    }

    public static function save(array $values, ?int $id = null): int
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            if ($values['is_founder']) {
                $pdo->exec("UPDATE team_members SET is_founder = 0 WHERE member_group = 'CORE'");
            }

            if ($id === null) {
                $statement = $pdo->prepare(
                    'INSERT INTO team_members (member_group, batch_id, name, role, university, bio, long_bio, photo_path, is_founder, sort_order, is_published) '
                    . 'VALUES (:member_group, :batch_id, :name, :role, :university, :bio, :long_bio, :photo_path, :is_founder, :sort_order, :is_published)'
                );
            } else {
                $statement = $pdo->prepare(
                    'UPDATE team_members SET member_group = :member_group, batch_id = :batch_id, name = :name, role = :role, '
                    . 'university = :university, bio = :bio, long_bio = :long_bio, photo_path = :photo_path, '
                    . 'is_founder = :is_founder, sort_order = :sort_order, is_published = :is_published WHERE id = :id'
                );
            }

            $params = [
                'member_group' => $values['member_group'],
                'batch_id' => $values['batch_id'],
                'name' => $values['name'],
                'role' => $values['role'],
                'university' => $values['university'],
                'bio' => $values['bio'],
                'long_bio' => $values['long_bio'],
                'photo_path' => $values['photo_path'],
                'is_founder' => $values['is_founder'] ? 1 : 0,
                'sort_order' => $values['sort_order'],
                'is_published' => $values['is_published'] ? 1 : 0,
            ];
            if ($id !== null) {
                $params['id'] = $id;
            }
            $statement->execute($params);
            $savedId = $id ?? (int) $pdo->lastInsertId();
            $pdo->commit();
            return $savedId;
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }
    }

    public static function delete(int $id): void
    {
        $statement = Database::connection()->prepare('DELETE FROM team_members WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}
