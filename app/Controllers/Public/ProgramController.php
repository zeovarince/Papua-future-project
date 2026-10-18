<?php
declare(strict_types=1);
namespace App\Controllers\Public;
use App\Core\Response; use App\Core\View; use App\Models\Program;
final class ProgramController
{
    public function index(): Response { return View::render('public/program/index',['title'=>'Program — Papua Future Project','page'=>'program','stylesheet'=>'css/pages/program.css','programs'=>Program::publicList()]); }
    public function show(string $slug): Response
    {
        $program=Program::findPublicBySlug($slug);
        if($program===null)return View::render('public/errors/404',['title'=>'Program tidak ditemukan','page'=>'not-found'],'public',404);
        return View::render('public/program/detail',['title'=>$program['title'].' — Papua Future Project','page'=>'program-detail','stylesheet'=>'css/pages/program-detail.css','program'=>$program,'photos'=>Program::photos((int)$program['id'])]);
    }
}
