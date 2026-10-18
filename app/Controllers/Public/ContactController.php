<?php
declare(strict_types=1);
namespace App\Controllers\Public;
use App\Core\Flash;use App\Core\Request;use App\Core\Response;use App\Core\View;use App\Models\ContactMessage;use App\Models\Faq;use App\Services\RateLimiter;use App\Validators\ContactValidator;
final class ContactController
{
    public function index(): Response
    {
        return View::render('public/contact/index',['title'=>'Kontak — Papua Future Project','page'=>'contact','stylesheet'=>'css/pages/contact.css','script'=>'js/pages/contact.js','faqs'=>Faq::published(),'errors'=>Flash::get('errors',[]),'old'=>Flash::get('old',[]),'success'=>Flash::get('success'),'flashError'=>Flash::get('error')]);
    }
    public function store(Request $request): Response
    {
        $trap=$request->post('website','');
        if(is_scalar($trap)&&trim((string)$trap)!==''){Flash::set('success','Pesan berhasil dikirim. Terima kasih sudah menghubungi kami.');return Response::redirect('/kontak#contact-form');}
        [$values,$errors]=ContactValidator::validate($request->post());
        if($errors!==[]){Flash::set('old',$values);Flash::set('errors',$errors);Flash::set('error','Periksa kembali data pesan.');return Response::redirect('/kontak#contact-form');}
        $limiter=new RateLimiter();$identifier=hash('sha256',$request->ipAddress());
        if($limiter->isLimited('contact',$identifier,5,3600)){Flash::set('error','Batas pengiriman pesan tercapai. Silakan coba lagi nanti.');return Response::redirect('/kontak#contact-form');}
        ContactMessage::create($values);$limiter->hit('contact',$identifier);
        Flash::set('success','Pesan berhasil dikirim. Terima kasih sudah menghubungi kami.');
        return Response::redirect('/kontak#contact-form');
    }
}
