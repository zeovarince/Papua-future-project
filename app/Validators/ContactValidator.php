<?php
declare(strict_types=1);
namespace App\Validators;
final class ContactValidator
{
    public static function validate(array $input): array
    {
        $name=isset($input['full_name'])&&is_scalar($input['full_name'])?trim((string)$input['full_name']):'';$email=isset($input['email'])&&is_scalar($input['email'])?trim((string)$input['email']):'';$wa=isset($input['whatsapp'])&&is_scalar($input['whatsapp'])?trim((string)$input['whatsapp']):'';$message=isset($input['message'])&&is_scalar($input['message'])?trim((string)$input['message']):'';
        $data=['full_name'=>$name,'email'=>$email,'whatsapp'=>$wa===''?null:$wa,'message'=>$message];$errors=[];
        if($name===''||mb_strlen($name)>100)$errors['full_name']='Nama wajib diisi, maksimal 100 karakter.';
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)||mb_strlen($email)>190)$errors['email']='Masukkan alamat email yang valid.';
        if($wa!==''&&(mb_strlen($wa)>20||!preg_match('/^(?:\\+62|62|0)8[0-9 .()-]{7,16}$/',$wa)))$errors['whatsapp']='Masukkan nomor WhatsApp Indonesia yang valid.';
        if($message===''||mb_strlen($message)<10||mb_strlen($message)>2000)$errors['message']='Pesan wajib diisi, 10 sampai 2.000 karakter.';
        return [$data,$errors];
    }
}
