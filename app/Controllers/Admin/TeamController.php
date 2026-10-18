<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Flash;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Batch;
use App\Models\TeamMember;
use App\Services\TeamPhotoService;
use App\Validators\TeamMemberValidator;
use PDOException;
use RuntimeException;

final class TeamController
{
    private const GROUPS = ['penggerak' => 'CORE', 'internship' => 'INTERN', 'volunteer' => 'VOLUNTEER'];

    public function index(string $group): Response
    {
        if (!isset(self::GROUPS[$group])) {
            return View::render('public/errors/404', ['title' => 'Halaman tidak ditemukan', 'page' => 'not-found'], 'public', 404);
        }
        return View::render('admin/team/index', [
            'title' => self::title($group),
            'page' => 'team-' . $group,
            'breadcrumb' => ['Admin', 'Tim', self::title($group)],
            'group' => $group,
            'members' => TeamMember::adminList(self::GROUPS[$group]),
        ], 'admin');
    }

    public function create(string $group): Response
    {
        return $this->form($group, null);
    }

    public function edit(string $group, int $id): Response
    {
        $member = TeamMember::find($id);
        if (!isset(self::GROUPS[$group]) || $member === null || $member['member_group'] !== self::GROUPS[$group]) {
            return Response::redirect('/admin/tim/' . rawurlencode($group));
        }
        return $this->form($group, $member);
    }

    private function form(string $group, ?array $member): Response
    {
        if (!isset(self::GROUPS[$group])) {
            return Response::redirect('/admin');
        }
        $old = Flash::get('old');
        $values = is_array($old) ? $old : ($member ?? [
            'name' => '', 'role' => '', 'university' => '', 'bio' => '', 'long_bio' => '',
            'batch_id' => '', 'photo_path' => null, 'is_founder' => 0, 'is_published' => 1, 'sort_order' => 0,
        ]);
        $batchType = self::GROUPS[$group] === 'INTERN' ? 'INTERN' : (self::GROUPS[$group] === 'VOLUNTEER' ? 'VOLUNTEER' : null);
        return View::render('admin/team/form', [
            'title' => ($member === null ? 'Tambah ' : 'Ubah ') . self::title($group),
            'page' => 'team-' . $group,
            'breadcrumb' => ['Admin', 'Tim', self::title($group), $member === null ? 'Tambah' : 'Ubah'],
            'group' => $group,
            'member' => $member,
            'values' => $values,
            'errors' => Flash::get('errors', []),
            'batches' => $batchType === null ? [] : Batch::byType($batchType),
        ], 'admin');
    }

    public function store(Request $request, string $group): Response
    {
        return $this->save($request, $group, null);
    }

    public function update(Request $request, string $group, int $id): Response
    {
        return $this->save($request, $group, $id);
    }

    private function save(Request $request, string $group, ?int $id): Response
    {
        if (!isset(self::GROUPS[$group])) {
            return Response::redirect('/admin');
        }
        $current = $id === null ? null : TeamMember::find($id);
        if ($id !== null && ($current === null || $current['member_group'] !== self::GROUPS[$group])) {
            return Response::redirect('/admin/tim/' . rawurlencode($group));
        }

        [$values, $errors] = TeamMemberValidator::validate($request->post(), $group);
        $photos = new TeamPhotoService();
        $newPhoto = null;
        try {
            if ($errors === []) {
                $newPhoto = $photos->store($request->file('photo'));
            }
        } catch (RuntimeException $exception) {
            $errors['photo'] = $exception->getMessage();
        }
        $values['photo_path'] = $newPhoto ?? ($current['photo_path'] ?? null);

        if ($errors !== []) {
            Flash::set('errors', $errors);
            Flash::set('old', $values + $request->post());
            Flash::set('error', 'Periksa kembali data anggota.');
            return Response::redirect('/admin/tim/' . rawurlencode($group) . ($id === null ? '/tambah' : '/' . $id . '/ubah'));
        }

        try {
            $savedId = TeamMember::save($values, $id);
            if ($newPhoto !== null && $current !== null) {
                $photos->delete($current['photo_path']);
            }
            Flash::set('success', 'Data anggota berhasil disimpan.');
            return Response::redirect('/admin/tim/' . rawurlencode($group));
        } catch (PDOException) {
            if ($newPhoto !== null) {
                $photos->delete($newPhoto);
            }
            Flash::set('error', 'Data anggota gagal disimpan. Periksa pilihan batch dan coba lagi.');
            Flash::set('old', $values);
            return Response::redirect('/admin/tim/' . rawurlencode($group) . ($id === null ? '/tambah' : '/' . $id . '/ubah'));
        }
    }

    public function destroy(Request $request, string $group, int $id): Response
    {
        if (!isset(self::GROUPS[$group])) {
            return Response::redirect('/admin');
        }
        $member = TeamMember::find($id);
        if ($member !== null && $member['member_group'] === self::GROUPS[$group]) {
            TeamMember::delete($id);
            (new TeamPhotoService())->delete($member['photo_path']);
            Flash::set('success', 'Anggota berhasil dihapus.');
        }
        return Response::redirect('/admin/tim/' . rawurlencode($group));
    }

    private static function title(string $group): string
    {
        return ['penggerak' => 'Tim Penggerak', 'internship' => 'Internship', 'volunteer' => 'Volunteer'][$group] ?? 'Tim';
    }
}
