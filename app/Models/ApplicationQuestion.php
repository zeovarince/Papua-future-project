<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class ApplicationQuestion
{
    public static function allByType(string $type): array
    {
        $statement = Database::connection()->prepare(
            'SELECT * FROM application_questions WHERE application_type = :application_type '
            . 'ORDER BY sort_order ASC, id ASC'
        );
        $statement->execute(['application_type' => $type]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function activeByType(string $type): array
    {
        $statement = Database::connection()->prepare(
            'SELECT * FROM application_questions WHERE application_type = :application_type AND is_active = 1 '
            . 'ORDER BY sort_order ASC, id ASC'
        );
        $statement->execute(['application_type' => $type]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id, string $type): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT * FROM application_questions WHERE id = :id AND application_type = :application_type LIMIT 1'
        );
        $statement->execute(['id' => $id, 'application_type' => $type]);
        $question = $statement->fetch(PDO::FETCH_ASSOC);
        return is_array($question) ? $question : null;
    }

    public static function countByType(string $type): int
    {
        $statement = Database::connection()->prepare(
            'SELECT COUNT(*) FROM application_questions WHERE application_type = :application_type'
        );
        $statement->execute(['application_type' => $type]);
        return (int) $statement->fetchColumn();
    }

    public static function save(string $type, array $values, ?int $id = null): int
    {
        $parameters = [
            'application_type' => $type,
            'question' => $values['question'],
            'field_type' => $values['field_type'],
            'is_required' => $values['is_required'],
            'is_active' => $values['is_active'],
            'sort_order' => $values['sort_order'],
        ];
        if ($id === null) {
            $statement = Database::connection()->prepare(
                'INSERT INTO application_questions '
                . '(application_type, question, field_type, is_required, is_active, sort_order) '
                . 'VALUES (:application_type, :question, :field_type, :is_required, :is_active, :sort_order)'
            );
            $statement->execute($parameters);
            return (int) Database::connection()->lastInsertId();
        }

        $parameters['id'] = $id;
        $statement = Database::connection()->prepare(
            'UPDATE application_questions SET question = :question, field_type = :field_type, '
            . 'is_required = :is_required, is_active = :is_active, sort_order = :sort_order '
            . 'WHERE id = :id AND application_type = :application_type'
        );
        $statement->execute($parameters);
        return $id;
    }

    public static function delete(int $id, string $type): bool
    {
        $statement = Database::connection()->prepare(
            'DELETE FROM application_questions WHERE id = :id AND application_type = :application_type'
        );
        $statement->execute(['id' => $id, 'application_type' => $type]);
        return $statement->rowCount() > 0;
    }
}
