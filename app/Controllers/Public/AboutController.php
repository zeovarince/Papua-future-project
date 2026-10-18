<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Response;
use App\Core\View;
use App\Models\AboutContent;
use App\Models\Batch;
use App\Models\TeamMember;

final class AboutController
{
    public function index(): Response
    {
        return View::render('public/about/index', [
            'title' => 'Tentang Kami — Papua Future Project',
            'page' => 'about',
            'stylesheet' => 'css/pages/about.css',
            'script' => 'js/pages/about.js',
            'about' => AboutContent::current(),
            'coreMembers' => TeamMember::allByGroup('CORE'),
            'internBatches' => Batch::publicByType('INTERN'),
            'internMembers' => TeamMember::allByGroup('INTERN'),
            'volunteerBatches' => Batch::publicByType('VOLUNTEER'),
            'volunteerMembers' => TeamMember::allByGroup('VOLUNTEER'),
        ]);
    }
}
