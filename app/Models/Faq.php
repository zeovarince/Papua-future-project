<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database; use PDO;
final class Faq
{
    public static function published(): array { return Database::connection()->query('SELECT * FROM faqs WHERE is_published=1 ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC); }
    public static function all(): array { return Database::connection()->query('SELECT * FROM faqs ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC); }
    public static function find(int $id): ?array { $s=Database::connection()->prepare('SELECT * FROM faqs WHERE id=:id LIMIT 1');$s->execute(['id'=>$id]);$r=$s->fetch(PDO::FETCH_ASSOC);return is_array($r)?$r:null; }
    public static function save(array $d,?int $id=null): void { if($id===null){$s=Database::connection()->prepare('INSERT INTO faqs (question,answer,sort_order,is_published) VALUES (:question,:answer,:sort_order,:is_published)');}else{$s=Database::connection()->prepare('UPDATE faqs SET question=:question,answer=:answer,sort_order=:sort_order,is_published=:is_published WHERE id=:id');$d['id']=$id;}$s->execute($d); }
    public static function delete(int $id): void { $s=Database::connection()->prepare('DELETE FROM faqs WHERE id=:id');$s->execute(['id'=>$id]); }
}
