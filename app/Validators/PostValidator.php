<?php
declare(strict_types=1);
namespace App\Validators;
final class PostValidator
{
    public static function validate(array $input): array
    {
        $str=static fn(string $key):string=>isset($input[$key])&&is_scalar($input[$key])?trim((string)$input[$key]):'';
        $title=$str('title');$slug=strtolower(trim((string)preg_replace('/[^a-z0-9]+/','-',$str('slug')?:$title),'-'));
        $category=strtoupper($str('category'));$published=$str('published_at');$date=null;
        if($published!==''){$parsed=\DateTimeImmutable::createFromFormat('!Y-m-d\TH:i',$published);$dateErrors=\DateTimeImmutable::getLastErrors();$clean=$dateErrors===false||($dateErrors['warning_count']===0&&$dateErrors['error_count']===0);$date=$parsed&&$clean?$parsed->format('Y-m-d H:i:s'):null;}
        $data=['slug'=>$slug,'category'=>$category,'title'=>$title,'excerpt'=>$str('excerpt'),'content'=>$str('content'),'author_name'=>$str('author_name')?:'Tim Papua Future Project','meta_label'=>$str('meta_label')?:null,'source_name'=>$str('source_name')?:null,'source_url'=>$str('source_url')?:null,'published_at'=>$date??date('Y-m-d H:i:s'),'is_published'=>!empty($input['is_published'])?1:0];
        $errors=[];
        if($title===''||mb_strlen($title)>190)$errors['title']='Judul wajib diisi, maksimal 190 karakter.';
        if($slug===''||strlen($slug)>190)$errors['slug']='Slug harus berupa alamat yang valid, maksimal 190 karakter.';
        if(!in_array($category,['ACTIVITY','UPDATE','MEDIA','AWARD'],true))$errors['category']='Pilih kategori yang tersedia.';
        if($data['excerpt']===''||mb_strlen($data['excerpt'])>300)$errors['excerpt']='Ringkasan wajib diisi, maksimal 300 karakter.';
        if($data['content']==='')$errors['content']='Isi berita wajib diisi.';
        if(mb_strlen($data['author_name'])>100)$errors['author_name']='Nama penulis maksimal 100 karakter.';
        if($data['meta_label']!==null&&mb_strlen($data['meta_label'])>120)$errors['meta_label']='Keterangan maksimal 120 karakter.';
        if($data['source_name']!==null&&mb_strlen($data['source_name'])>120)$errors['source_name']='Nama sumber maksimal 120 karakter.';
        if($data['source_url']!==null&&(!filter_var($data['source_url'],FILTER_VALIDATE_URL)||!in_array(strtolower((string)parse_url($data['source_url'],PHP_URL_SCHEME)),['https','http'],true)||mb_strlen($data['source_url'])>500))$errors['source_url']='Masukkan tautan HTTP atau HTTPS yang valid (maksimal 500 karakter).';
        if($date===null)$errors['published_at']='Tanggal dan waktu terbit tidak valid.';
        return [$data,$errors];
    }
}
