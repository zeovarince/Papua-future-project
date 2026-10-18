<?php
declare(strict_types=1);
namespace App\Validators;
final class FaqValidator
{
    public static function validate(array $input): array
    {
        $q=isset($input['question'])&&is_scalar($input['question'])?trim((string)$input['question']):'';$a=isset($input['answer'])&&is_scalar($input['answer'])?trim((string)$input['answer']):'';$sort=filter_var($input['sort_order']??0,FILTER_VALIDATE_INT);
        $data=['question'=>$q,'answer'=>$a,'sort_order'=>$sort===false?0:max(-10000,min(10000,$sort)),'is_published'=>!empty($input['is_published'])?1:0];$errors=[];
        if($q===''||mb_strlen($q)>300)$errors['question']='Pertanyaan wajib diisi, maksimal 300 karakter.';
        if($a===''||mb_strlen($a)>65535)$errors['answer']='Jawaban wajib diisi.';
        return [$data,$errors];
    }
}
