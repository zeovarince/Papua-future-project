<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database; use PDO;
final class ContactMessage
{
    public static function all(): array { return Database::connection()->query('SELECT * FROM contact_messages ORDER BY is_read ASC,created_at DESC,id DESC')->fetchAll(PDO::FETCH_ASSOC); }
    public static function find(int $id): ?array { $s=Database::connection()->prepare('SELECT * FROM contact_messages WHERE id=:id LIMIT 1');$s->execute(['id'=>$id]);$r=$s->fetch(PDO::FETCH_ASSOC);return is_array($r)?$r:null; }
    public static function create(array $data): void { $s=Database::connection()->prepare('INSERT INTO contact_messages (full_name,email,whatsapp,message) VALUES (:full_name,:email,:whatsapp,:message)');$s->execute($data); }
    public static function markRead(int $id): void { $s=Database::connection()->prepare('UPDATE contact_messages SET is_read=1 WHERE id=:id');$s->execute(['id'=>$id]); }
    public static function delete(int $id): void { $s=Database::connection()->prepare('DELETE FROM contact_messages WHERE id=:id');$s->execute(['id'=>$id]); }
    public static function unreadCount(): int { return (int)Database::connection()->query('SELECT COUNT(*) FROM contact_messages WHERE is_read=0')->fetchColumn(); }
}
