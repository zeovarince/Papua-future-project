<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use RuntimeException;

final class Application
{
    public static function page(array $filters, int $page, int $perPage = 20): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        [$where, $parameters] = self::where($filters);
        $count = Database::connection()->prepare(
            'SELECT COUNT(*) FROM applications a INNER JOIN batches b ON b.id = a.batch_id' . $where
        );
        $count->execute($parameters);
        $total = (int) $count->fetchColumn();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);

        $statement = Database::connection()->prepare(
            'SELECT a.*, b.name AS batch_name, b.batch_type '
            . 'FROM applications a INNER JOIN batches b ON b.id = a.batch_id'
            . $where . ' ORDER BY a.created_at DESC, a.id DESC LIMIT ' . $perPage . ' OFFSET ' . (($page - 1) * $perPage)
        );
        $statement->execute($parameters);
        return ['items' => $statement->fetchAll(PDO::FETCH_ASSOC), 'total' => $total, 'page' => $page, 'per_page' => $perPage];
    }

    public static function exportRows(array $filters, int $limit): array
    {
        $limit = max(1, min(10001, $limit));
        [$where, $parameters] = self::where($filters);
        $statement = Database::connection()->prepare(
            'SELECT a.*, b.name AS batch_name, b.batch_type '
            . 'FROM applications a INNER JOIN batches b ON b.id = a.batch_id'
            . $where . ' ORDER BY a.created_at DESC, a.id DESC LIMIT ' . $limit
        );
        $statement->execute($parameters);
        return self::attachAnswers($statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function find(int $id): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT a.*, b.name AS batch_name, b.batch_type '
            . 'FROM applications a INNER JOIN batches b ON b.id = a.batch_id WHERE a.id = :id LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $application = $statement->fetch(PDO::FETCH_ASSOC);
        if (!is_array($application)) {
            return null;
        }
        return self::attachAnswers([$application])[0];
    }

    public static function createForOpenBatch(array $values, array $files, string $token): int
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $batch = $pdo->prepare(
                'SELECT b.is_open, b.batch_type, s.is_active AS form_is_active '
                . 'FROM batches b INNER JOIN application_form_settings s ON s.application_type = b.batch_type '
                . 'WHERE b.id = :id FOR UPDATE'
            );
            $batch->execute(['id' => $values['batch_id']]);
            $batchRow = $batch->fetch(PDO::FETCH_ASSOC);
            if (!is_array($batchRow) || (int) $batchRow['is_open'] !== 1 || (int) $batchRow['form_is_active'] !== 1) {
                throw new RuntimeException('Pendaftaran untuk batch ini sedang ditutup. Pilih batch yang masih dibuka.');
            }

            $statement = $pdo->prepare(
                'INSERT INTO applications '
                . '(batch_id, file_token, full_name, birth_date, email, whatsapp, domicile, occupation_status, university, duration, interest_areas, referral_source, motivation, special_skills, photo_path, cv_path) '
                . 'VALUES (:batch_id, :file_token, :full_name, :birth_date, :email, :whatsapp, :domicile, :occupation_status, :university, :duration, :interest_areas, :referral_source, :motivation, :special_skills, :photo_path, :cv_path)'
            );
            $statement->execute([
                'batch_id' => $values['batch_id'],
                'file_token' => $token,
                'full_name' => $values['full_name'],
                'birth_date' => $values['birth_date'],
                'email' => $values['email'],
                'whatsapp' => $values['whatsapp'],
                'domicile' => $values['domicile'],
                'occupation_status' => $values['occupation_status'],
                'university' => $values['university'],
                'duration' => $values['duration'],
                'interest_areas' => json_encode($values['interest_areas'], JSON_THROW_ON_ERROR),
                'referral_source' => $values['referral_source'],
                'motivation' => $values['motivation'],
                'special_skills' => $values['special_skills'],
                'photo_path' => $files['photo_path'],
                'cv_path' => $files['cv_path'],
            ]);
            $id = (int) $pdo->lastInsertId();

            $questionStatement = $pdo->prepare(
                'SELECT id, question, field_type, is_required FROM application_questions '
                . 'WHERE application_type = :application_type AND is_active = 1 ORDER BY sort_order, id FOR UPDATE'
            );
            $questionStatement->execute(['application_type' => $batchRow['batch_type']]);
            $questions = $questionStatement->fetchAll(PDO::FETCH_ASSOC);
            $answerStatement = $pdo->prepare(
                'INSERT INTO application_answers (application_id, question_id, question_label, answer) '
                . 'VALUES (:application_id, :question_id, :question_label, :answer)'
            );
            $submittedAnswers = is_array($values['custom_answers'] ?? null) ? $values['custom_answers'] : [];
            foreach ($questions as $question) {
                $questionId = (int) $question['id'];
                $answer = trim((string) ($submittedAnswers[$questionId] ?? ''));
                if ($answer === '' && (int) $question['is_required'] === 1) {
                    throw new RuntimeException('Pertanyaan formulir berubah. Muat ulang halaman dan isi kembali jawaban yang diminta.');
                }
                if ($answer === '') {
                    continue;
                }
                $maxLength = $question['field_type'] === 'TEXT' ? 200 : 2000;
                if (mb_strlen($answer) > $maxLength) {
                    throw new RuntimeException('Jawaban pertanyaan formulir terlalu panjang.');
                }
                $answerStatement->execute([
                    'application_id' => $id,
                    'question_id' => $questionId,
                    'question_label' => $question['question'],
                    'answer' => $answer,
                ]);
            }
            $pdo->commit();
            return $id;
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }
    }

    public static function updateStatus(int $id, string $status): bool
    {
        $statement = Database::connection()->prepare('UPDATE applications SET status = :status WHERE id = :id');
        $statement->execute(['status' => $status, 'id' => $id]);
        return self::find($id) !== null;
    }

    public static function countNew(): int
    {
        return (int) Database::connection()->query("SELECT COUNT(*) FROM applications WHERE status = 'NEW'")->fetchColumn();
    }

    private static function where(array $filters): array
    {
        $conditions = [];
        $parameters = [];
        if (in_array($filters['type'] ?? '', ['INTERN', 'VOLUNTEER'], true)) {
            $conditions[] = 'b.batch_type = :batch_type';
            $parameters['batch_type'] = $filters['type'];
        }
        if (isset($filters['batch_id']) && (int) $filters['batch_id'] > 0) {
            $conditions[] = 'a.batch_id = :batch_id';
            $parameters['batch_id'] = (int) $filters['batch_id'];
        }
        if (in_array($filters['status'] ?? '', ['NEW', 'REVIEWED', 'ACCEPTED', 'REJECTED'], true)) {
            $conditions[] = 'a.status = :status';
            $parameters['status'] = $filters['status'];
        }
        $search = trim((string) ($filters['q'] ?? ''));
        if ($search !== '') {
            $conditions[] = 'a.full_name LIKE :search';
            $parameters['search'] = '%' . mb_substr($search, 0, 100) . '%';
        }
        return [$conditions === [] ? '' : ' WHERE ' . implode(' AND ', $conditions), $parameters];
    }

    private static function attachAnswers(array $applications): array
    {
        if ($applications === []) {
            return $applications;
        }
        $ids = array_values(array_unique(array_map(static fn (array $application): int => (int) $application['id'], $applications)));
        $placeholders = [];
        $parameters = [];
        foreach ($ids as $index => $id) {
            $key = 'application_' . $index;
            $placeholders[] = ':' . $key;
            $parameters[$key] = $id;
        }
        $statement = Database::connection()->prepare(
            'SELECT application_id, question_label, answer FROM application_answers '
            . 'WHERE application_id IN (' . implode(', ', $placeholders) . ') ORDER BY id ASC'
        );
        $statement->execute($parameters);
        $answersByApplication = [];
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $answer) {
            $answersByApplication[(int) $answer['application_id']][] = [
                'question' => (string) $answer['question_label'],
                'answer' => (string) $answer['answer'],
            ];
        }
        foreach ($applications as &$application) {
            $application['custom_answers'] = $answersByApplication[(int) $application['id']] ?? [];
        }
        unset($application);
        return $applications;
    }
}
