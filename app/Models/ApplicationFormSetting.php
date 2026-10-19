<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Cache;
use App\Core\Database;
use PDO;

final class ApplicationFormSetting
{
    public static function all(): array
    {
        $statement = Database::connection()->query(
            'SELECT s.application_type, s.is_active, s.updated_at, '
            . '(SELECT COUNT(*) FROM batches b WHERE b.batch_type = s.application_type AND b.is_open = 1) AS open_batch_count, '
            . '(SELECT COUNT(*) FROM application_questions q WHERE q.application_type = s.application_type AND q.is_active = 1) AS active_question_count '
            . 'FROM application_form_settings s '
            . "ORDER BY FIELD(s.application_type, 'INTERN', 'VOLUNTEER')"
        );
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function isActive(string $type, ?PDO $pdo = null): bool
    {
        $statement = ($pdo ?? Database::connection())->prepare(
            'SELECT is_active FROM application_form_settings WHERE application_type = :application_type LIMIT 1'
        );
        $statement->execute(['application_type' => $type]);
        return (int) $statement->fetchColumn() === 1;
    }

    public static function toggle(string $type): bool
    {
        $statement = Database::connection()->prepare(
            'UPDATE application_form_settings SET is_active = 1 - is_active WHERE application_type = :application_type'
        );
        $statement->execute(['application_type' => $type]);
        $updated = $statement->rowCount() > 0;
        if ($updated) {
            Cache::clearPublic();
        }
        return $updated;
    }
}
