<?php
declare(strict_types=1);
namespace App\Controllers\Admin;
use App\Core\Flash;use App\Core\Request;use App\Core\Response;use App\Core\View;use App\Models\ContactMessage;
final class MessageController
{
    public function index(): Response { return View::render('admin/message/index',['title'=>'Pesan Masuk','page'=>'message','breadcrumb'=>['Admin','Pesan Masuk'],'messages'=>ContactMessage::all()],'admin'); }
    public function show(int $id): Response { $message=ContactMessage::find($id);if($message===null)return Response::redirect('/admin/pesan');if((int)$message['is_read']===0){ContactMessage::markRead($id);$message['is_read']=1;}return View::render('admin/message/show',['title'=>'Detail Pesan','page'=>'message','breadcrumb'=>['Admin','Pesan Masuk','Detail'],'message'=>$message],'admin'); }
    public function markRead(Request $r,int $id): Response { if(ContactMessage::find($id)!==null){ContactMessage::markRead($id);Flash::set('success','Pesan ditandai sudah dibaca.');}return Response::redirect('/admin/pesan/'.$id); }
    public function destroy(Request $r,int $id): Response { if(ContactMessage::find($id)!==null){ContactMessage::delete($id);Flash::set('success','Pesan dihapus.');}return Response::redirect('/admin/pesan'); }
}
