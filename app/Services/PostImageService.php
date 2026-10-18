<?php
declare(strict_types=1);
namespace App\Services;
final class PostImageService
{
    private ProgramImageService $images;
    public function __construct(){ $this->images=new ProgramImageService(); }
    public function store(?array $file,int $postId): ?string { return $this->images->store($file,$postId,'posts'); }
    public function storeMultiple(?array $files,int $postId,int $available): array { return $this->images->storeMultiple($files,$postId,$available,'posts'); }
    public function delete(?string $path): void { $this->images->delete($path); }
    public function deleteItemFiles(int $postId,?string $cover,array $photos): void { $this->images->deleteItemFiles('posts',$postId,$cover,$photos); }
}
