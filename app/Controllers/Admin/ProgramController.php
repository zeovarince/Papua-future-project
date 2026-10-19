<?php
declare(strict_types=1);
namespace App\Controllers\Admin;
use App\Core\Flash; use App\Core\Request; use App\Core\Response; use App\Core\SecurityLogger; use App\Core\View;
use App\Models\Program; use App\Services\ProgramImageService; use App\Validators\ProgramValidator; use PDOException; use Throwable;
final class ProgramController
{
    public function index(): Response { return View::render('admin/program/index', ['title'=>'Program','page'=>'program','breadcrumb'=>['Admin','Program'],'programs'=>Program::adminList()], 'admin'); }
    public function create(): Response { return $this->form(null); }
    public function edit(int $id): Response { $p=Program::find($id); return $p===null?Response::redirect('/admin/program'):$this->form($p); }
    private function form(?array $program): Response { return View::render('admin/program/form', ['title'=>$program===null?'Tambah Program':'Ubah Program','page'=>'program','breadcrumb'=>['Admin','Program',$program===null?'Tambah':'Ubah'],'program'=>$program,'photos'=>$program===null?[]:Program::photos((int)$program['id']),'values'=>Flash::get('old',$program??['category_label'=>'Pendidikan','is_published'=>1,'is_featured'=>0,'sort_order'=>0]),'errors'=>Flash::get('errors',[])], 'admin'); }
    public function store(Request $r): Response { return $this->save($r,null); }
    public function update(Request $r,int $id): Response { return $this->save($r,$id); }
    private function save(Request $request,?int $id): Response
    {
        $current=$id===null?null:Program::find($id); if($id!==null&&$current===null)return Response::redirect('/admin/program');
        [$values,$errors]=ProgramValidator::validate($request->post(),$id); if(Program::slugExists($values['slug'],$id))$errors['slug']='Slug sudah digunakan program lain.';
        if($errors!==[])return $this->invalid($values,$errors,$id);
        $images=new ProgramImageService(); $newCover=null; $savedId=null;
        try {
            if($id===null){$values['cover_image']=null; $savedId=Program::save($values); $newCover=$images->store($request->file('cover_image'),$savedId); if($newCover!==null){$values['cover_image']=$newCover;Program::save($values,$savedId);}}
            else {$newCover=$images->store($request->file('cover_image'),$id);$values['cover_image']=$newCover??$current['cover_image'];$savedId=Program::save($values,$id);if($newCover!==null)$images->delete($current['cover_image']);}
            Flash::set('success','Program berhasil disimpan.'); return Response::redirect('/admin/program/'.$savedId.'/ubah');
        } catch(PDOException $e){if($newCover!==null)$images->delete($newCover);if($id===null&&$savedId!==null)Program::delete($savedId);$errors['slug']=(string)$e->getCode()==='23000'?'Slug sudah digunakan program lain.':'Data program gagal disimpan.';return $this->invalid($values,$errors,$id);}
        catch(Throwable $e){if($newCover!==null)$images->delete($newCover);if($id===null&&$savedId!==null)Program::delete($savedId);$errors['cover_image']=$e->getMessage();return $this->invalid($values,$errors,$id);}
    }
    private function invalid(array $values,array $errors,?int $id): Response { Flash::set('errors',$errors);Flash::set('old',$values);Flash::set('error','Periksa kembali data program.');return Response::redirect($id===null?'/admin/program/tambah':'/admin/program/'.$id.'/ubah'); }
    public function destroy(Request $r,int $id): Response { $p=Program::find($id);if($p!==null){$photos=Program::photos($id);Program::delete($id);(new ProgramImageService())->deleteProgramFiles($id,$p,$photos);SecurityLogger::record('program_deleted',$r,$id);Flash::set('success','Program dan foto dokumentasinya berhasil dihapus.');}return Response::redirect('/admin/program'); }
    public function uploadPhotos(Request $r,int $id): Response
    {
        if(Program::find($id)===null)return Response::redirect('/admin/program');$service=new ProgramImageService();$stored=[];
        try{$stored=$service->storeMultiple($r->file('photos'),$id,ProgramImageService::MAX_PHOTOS-count(Program::photos($id)));Program::addPhotos($id,$stored);Flash::set('success',count($stored).' foto dokumentasi berhasil ditambahkan.');}
        catch(Throwable $e){foreach($stored as $path)$service->delete($path);Flash::set('error',$e->getMessage());}
        return Response::redirect('/admin/program/'.$id.'/ubah#galeri');
    }
    public function updatePhoto(Request $r,int $id,int $photoId): Response { if(Program::photo($photoId,$id)!==null){$caption=$r->post('caption','');$sort=filter_var($r->post('sort_order',0),FILTER_VALIDATE_INT);Program::updatePhoto($photoId,$id,is_scalar($caption)?trim((string)$caption):'', $sort===false?0:max(-10000,min(10000,$sort)));Flash::set('success','Caption dan urutan foto diperbarui.');}return Response::redirect('/admin/program/'.$id.'/ubah#galeri'); }
    public function deletePhoto(Request $r,int $id,int $photoId): Response { $photo=Program::photo($photoId,$id);if($photo!==null){Program::deletePhoto($photoId,$id);(new ProgramImageService())->delete($photo['file_path']);Flash::set('success','Foto dokumentasi berhasil dihapus.');}return Response::redirect('/admin/program/'.$id.'/ubah#galeri'); }
}
