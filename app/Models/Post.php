<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database; use PDO;
final class Post
{
    public static function publicList(?string $category = null, int $limit = 100): array
    {
        $sql='SELECT * FROM posts WHERE is_published=1 AND published_at<=CURRENT_TIMESTAMP';$params=[];
        if($category!==null&&in_array($category,['ACTIVITY','UPDATE','MEDIA','AWARD'],true)){$sql.=' AND category=:category';$params['category']=$category;}
        $sql.=' ORDER BY published_at DESC, id DESC LIMIT :limit';$stmt=Database::connection()->prepare($sql);foreach($params as $key=>$value)$stmt->bindValue($key,$value);$stmt->bindValue('limit',max(1,min(200,$limit)),PDO::PARAM_INT);$stmt->execute();return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function featured(int $limit=3): array { return self::publicList(null,$limit); }
    public static function adminList(): array { return Database::connection()->query('SELECT p.*,(SELECT COUNT(*) FROM post_photos pp WHERE pp.post_id=p.id) AS photo_count FROM posts p ORDER BY p.published_at DESC,p.id DESC')->fetchAll(PDO::FETCH_ASSOC); }
    public static function find(int $id): ?array { $s=Database::connection()->prepare('SELECT * FROM posts WHERE id=:id LIMIT 1');$s->execute(['id'=>$id]);$r=$s->fetch(PDO::FETCH_ASSOC);return is_array($r)?$r:null; }
    public static function findPublicBySlug(string $slug): ?array { $s=Database::connection()->prepare('SELECT * FROM posts WHERE slug=:slug AND is_published=1 AND published_at<=CURRENT_TIMESTAMP LIMIT 1');$s->execute(['slug'=>$slug]);$r=$s->fetch(PDO::FETCH_ASSOC);return is_array($r)?$r:null; }
    public static function related(int $id,string $category,int $limit=3): array { $s=Database::connection()->prepare('SELECT * FROM posts WHERE id<>:id AND category=:category AND is_published=1 AND published_at<=CURRENT_TIMESTAMP ORDER BY published_at DESC LIMIT :limit');$s->bindValue('id',$id,PDO::PARAM_INT);$s->bindValue('category',$category);$s->bindValue('limit',max(1,min(6,$limit)),PDO::PARAM_INT);$s->execute();return $s->fetchAll(PDO::FETCH_ASSOC); }
    public static function photos(int $id): array { $s=Database::connection()->prepare('SELECT * FROM post_photos WHERE post_id=:id ORDER BY sort_order,id');$s->execute(['id'=>$id]);return $s->fetchAll(PDO::FETCH_ASSOC); }
    public static function photo(int $photoId,int $postId): ?array { $s=Database::connection()->prepare('SELECT * FROM post_photos WHERE id=:photo AND post_id=:post LIMIT 1');$s->execute(['photo'=>$photoId,'post'=>$postId]);$r=$s->fetch(PDO::FETCH_ASSOC);return is_array($r)?$r:null; }
    public static function slugExists(string $slug,?int $except=null): bool { $sql='SELECT 1 FROM posts WHERE slug=:slug'.($except===null?'':' AND id<>:id').' LIMIT 1';$s=Database::connection()->prepare($sql);$p=['slug'=>$slug];if($except!==null)$p['id']=$except;$s->execute($p);return (bool)$s->fetchColumn(); }
    public static function save(array $d,?int $id=null): int
    {
        $sql=$id===null?'INSERT INTO posts (slug,category,title,excerpt,content,cover_image,author_name,meta_label,source_name,source_url,published_at,is_published) VALUES (:slug,:category,:title,:excerpt,:content,:cover_image,:author_name,:meta_label,:source_name,:source_url,:published_at,:is_published)':'UPDATE posts SET slug=:slug,category=:category,title=:title,excerpt=:excerpt,content=:content,cover_image=:cover_image,author_name=:author_name,meta_label=:meta_label,source_name=:source_name,source_url=:source_url,published_at=:published_at,is_published=:is_published WHERE id=:id';
        if($id!==null)$d['id']=$id;$s=Database::connection()->prepare($sql);$s->execute($d);return $id??(int)Database::connection()->lastInsertId();
    }
    public static function addPhoto(int $id,string $path): void { $s=Database::connection()->prepare('INSERT INTO post_photos (post_id,file_path) VALUES (:id,:path)');$s->execute(['id'=>$id,'path'=>$path]); }
    public static function addPhotos(int $id,array $paths): void { $pdo=Database::connection();$pdo->beginTransaction();try{foreach($paths as $path){$s=$pdo->prepare('INSERT INTO post_photos (post_id,file_path) VALUES (:id,:path)');$s->execute(['id'=>$id,'path'=>$path]);}$pdo->commit();}catch(\Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;} }
    public static function updatePhoto(int $photoId,int $postId,string $caption,int $sort): void { $s=Database::connection()->prepare('UPDATE post_photos SET caption=:caption,sort_order=:sort WHERE id=:photo AND post_id=:post');$s->execute(['caption'=>$caption!==''?$caption:null,'sort'=>$sort,'photo'=>$photoId,'post'=>$postId]); }
    public static function deletePhoto(int $photoId,int $postId): void { $s=Database::connection()->prepare('DELETE FROM post_photos WHERE id=:photo AND post_id=:post');$s->execute(['photo'=>$photoId,'post'=>$postId]); }
    public static function delete(int $id): void { $s=Database::connection()->prepare('DELETE FROM posts WHERE id=:id');$s->execute(['id'=>$id]); }
    public static function countPublished(): int { return (int)Database::connection()->query('SELECT COUNT(*) FROM posts WHERE is_published=1 AND published_at<=CURRENT_TIMESTAMP')->fetchColumn(); }
}
