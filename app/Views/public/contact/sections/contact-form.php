<section class="pfp-section pfp-section-alt" id="contact-form">
<div class="pfp-container">
<div data-inline-style="contact-281"><span class="pfp-tag">Kirim pesan</span><h2 class="pfp-title">Ceritakan kebutuhanmu kepada kami.</h2><div class="pfp-divider" data-inline-style="contact-282"></div></div>
<div class="form-wrap" data-inline-style="contact-283">
<?php if (!empty($success)): ?><p class="contact-notice contact-success" role="status"><?=e($success)?></p><?php endif; ?>
<?php if (!empty($flashError)): ?><p class="contact-notice contact-error" role="alert"><?=e($flashError)?></p><?php endif; ?>
<form action="<?=url('/kontak')?>" method="post" class="contact-form" data-inline-style="contact-284">
<?=csrf_field()?>
<div class="contact-honeypot" aria-hidden="true"><label for="contact-website">Website</label><input id="contact-website" name="website" type="text" tabindex="-1" autocomplete="off"></div>
<div class="contact-fields">
<div><label for="contact-name">Nama Lengkap *</label><input id="contact-name" name="full_name" type="text" maxlength="100" autocomplete="name" required value="<?=e($old['full_name']??'')?>" placeholder="Masukkan nama lengkapmu"><?php if(isset($errors['full_name'])):?><small class="contact-field-error"><?=e($errors['full_name'])?></small><?php endif;?></div>
<div><label for="contact-email">Email Aktif *</label><input id="contact-email" name="email" type="email" maxlength="190" autocomplete="email" required value="<?=e($old['email']??'')?>" placeholder="contoh@email.com"><?php if(isset($errors['email'])):?><small class="contact-field-error"><?=e($errors['email'])?></small><?php endif;?></div>
<div><label for="contact-whatsapp">Nomor WhatsApp</label><input id="contact-whatsapp" name="whatsapp" type="tel" maxlength="20" autocomplete="tel" value="<?=e($old['whatsapp']??'')?>" placeholder="08xxxxxxxxxx"><?php if(isset($errors['whatsapp'])):?><small class="contact-field-error"><?=e($errors['whatsapp'])?></small><?php endif;?></div>
<div><label for="contact-message">Pesan *</label><textarea id="contact-message" name="message" maxlength="2000" rows="5" required placeholder="Ceritakan kebutuhanmu..." data-inline-style="contact-285"><?=e($old['message']??'')?></textarea><?php if(isset($errors['message'])):?><small class="contact-field-error"><?=e($errors['message'])?></small><?php endif;?></div>
</div>
<button class="submit-btn" type="submit">Kirim Pesan</button>
<p data-inline-style="contact-286">Dengan mengirim pesan, kamu menyetujui bahwa data yang diberikan akan digunakan untuk keperluan komunikasi dengan Papua Future Project.</p>
</form>
</div></div></section>
