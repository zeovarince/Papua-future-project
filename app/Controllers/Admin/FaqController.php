<?php
declare(strict_types=1);
namespace App\Controllers\Admin;
use App\Core\Flash;use App\Core\Request;use App\Core\Response;use App\Core\View;use App\Models\Faq;use App\Validators\FaqValidator;
final class FaqController
{
    public function index(): Response { return View::render('admin/faq/index',['title'=>'FAQ','page'=>'faq','breadcrumb'=>['Admin','FAQ'],'faqs'=>Faq::all()],'admin'); }
    public function create(): Response { return $this->form(null); }
    public function edit(int $id): Response { $faq=Faq::find($id);return $faq===null?Response::redirect('/admin/faq'):$this->form($faq); }
    private function form(?array $faq): Response { return View::render('admin/faq/form',['title'=>$faq===null?'Tambah FAQ':'Ubah FAQ','page'=>'faq','breadcrumb'=>['Admin','FAQ',$faq===null?'Tambah':'Ubah'],'faq'=>$faq,'values'=>Flash::get('old',$faq??['sort_order'=>0,'is_published'=>1]),'errors'=>Flash::get('errors',[])],'admin'); }
    public function store(Request $r): Response { return $this->save($r,null); }
    public function update(Request $r,int $id): Response { return $this->save($r,$id); }
    private function save(Request $r,?int $id): Response { if($id!==null&&Faq::find($id)===null)return Response::redirect('/admin/faq');[$values,$errors]=FaqValidator::validate($r->post());if($errors!==[]){Flash::set('old',$values);Flash::set('errors',$errors);Flash::set('error','Periksa pertanyaan FAQ.');return Response::redirect($id===null?'/admin/faq/tambah':'/admin/faq/'.$id.'/ubah');}Faq::save($values,$id);Flash::set('success','FAQ berhasil disimpan.');return Response::redirect('/admin/faq'); }
    public function destroy(Request $r,int $id): Response { if(Faq::find($id)!==null){Faq::delete($id);Flash::set('success','FAQ dihapus.');}return Response::redirect('/admin/faq'); }
}
