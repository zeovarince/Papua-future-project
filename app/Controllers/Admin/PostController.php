<?php
declare(strict_types=1);
namespace App\Controllers\Admin;
use App\Core\Flash;use App\Core\Request;use App\Core\Response;use App\Core\SecurityLogger;use App\Core\View;use App\Models\Post;use App\Services\PostImageService;use App\Validators\PostValidator;use PDOException;use Throwable;
final class PostController
{
    public function index(): Response { return View::render('admin/post/index',['title'=>'Berita & Kegiatan','page'=>'post','breadcrumb'=>['Admin','Berita & Kegiatan'],'posts'=>Post::adminList()],'admin'); }
    public function create(): Response { return $this->form(null); }
    public function edit(int $id): Response { $p=Post::find($id);return $p===null?Response::redirect('/admin/berita'):$this->form($p); }
    private function form(?array $post): Response
    {
        $defaults=$post??['category'=>'ACTIVITY','author_name'=>'Tim Papua Future Project','is_published'=>1,'published_at'=>date('Y-m-d H:i:s')];
        return View::render('admin/post/form',['title'=>$post===null?'Tambah Berita':'Ubah Berita','page'=>'post','breadcrumb'=>['Admin','Berita & Kegiatan',$post===null?'Tambah':'Ubah'],'post'=>$post,'photos'=>$post===null?[]:Post::photos((int)$post['id']),'values'=>Flash::get('old',$defaults),'errors'=>Flash::get('errors',[])],'admin');
    }
    public function store(Request $r): Response { return $this->save($r,null); }
    public function update(Request $r,int $id): Response { return $this->save($r,$id); }
    private function save(Request $r,?int $id): Response
    {
        $current=$id===null?null:Post::find($id);if($id!==null&&$current===null)return Response::redirect('/admin/berita');
        [$values,$errors]=PostValidator::validate($r->post());if(Post::slugExists($values['slug'],$id))$errors['slug']='Slug sudah digunakan berita lain.';
        if($errors!==[])return $this->invalid($values,$errors,$id);
        $images=new PostImageService();$cover=null;$savedId=null;
        try {
            if($id===null){$values['cover_image']=null;$savedId=Post::save($values);$cover=$images->store($r->file('cover_image'),$savedId);if($cover!==null){$values['cover_image']=$cover;Post::save($values,$savedId);}}
            else{$cover=$images->store($r->file('cover_image'),$id);$values['cover_image']=$cover??$current['cover_image'];$savedId=Post::save($values,$id);if($cover!==null)$images->delete($current['cover_image']);}
            Flash::set('success','Berita berhasil disimpan.');return Response::redirect('/admin/berita/'.$savedId.'/ubah');
        }catch(PDOException $e){if($cover!==null)$images->delete($cover);if($id===null&&$savedId!==null)Post::delete($savedId);$errors['slug']=(string)$e->getCode()==='23000'?'Slug sudah digunakan berita lain.':'Data berita gagal disimpan.';return $this->invalid($values,$errors,$id);}
        catch(Throwable $e){if($cover!==null)$images->delete($cover);if($id===null&&$savedId!==null)Post::delete($savedId);$errors['cover_image']=$e->getMessage();return $this->invalid($values,$errors,$id);}
    }
    private function invalid(array $values,array $errors,?int $id): Response { Flash::set('old',$values);Flash::set('errors',$errors);Flash::set('error','Periksa kembali data berita.');return Response::redirect($id===null?'/admin/berita/tambah':'/admin/berita/'.$id.'/ubah'); }
    public function destroy(Request $r,int $id): Response { $p=Post::find($id);if($p!==null){$photos=Post::photos($id);Post::delete($id);(new PostImageService())->deleteItemFiles($id,$p['cover_image'],$photos);SecurityLogger::record('post_deleted',$r,$id);Flash::set('success','Berita dan dokumentasinya dihapus.');}return Response::redirect('/admin/berita'); }
    public function uploadPhotos(Request $r,int $id): Response
    {
        if(Post::find($id)===null)return Response::redirect('/admin/berita');$service=new PostImageService();$stored=[];
        try{$stored=$service->storeMultiple($r->file('photos'),$id,30-count(Post::photos($id)));Post::addPhotos($id,$stored);Flash::set('success',count($stored).' foto dokumentasi ditambahkan.');}
        catch(Throwable $e){foreach($stored as $path)$service->delete($path);Flash::set('error',$e->getMessage());}
        return Response::redirect('/admin/berita/'.$id.'/ubah#galeri');
    }
    public function updatePhoto(Request $r,int $id,int $photoId): Response { if(Post::photo($photoId,$id)!==null){$caption=$r->post('caption','');$order=filter_var($r->post('sort_order',0),FILTER_VALIDATE_INT);Post::updatePhoto($photoId,$id,is_scalar($caption)?trim((string)$caption):'', $order===false?0:max(-10000,min(10000,$order)));Flash::set('success','Foto diperbarui.');}return Response::redirect('/admin/berita/'.$id.'/ubah#galeri'); }
    public function deletePhoto(Request $r,int $id,int $photoId): Response { $photo=Post::photo($photoId,$id);if($photo!==null){Post::deletePhoto($photoId,$id);(new PostImageService())->delete($photo['file_path']);Flash::set('success','Foto dokumentasi dihapus.');}return Response::redirect('/admin/berita/'.$id.'/ubah#galeri'); }
}
