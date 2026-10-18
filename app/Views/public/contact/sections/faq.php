<section class="pfp-section pfp-section-alt">
<div class="pfp-container"><div data-inline-style="contact-292"><span class="pfp-tag">Pertanyaan umum</span><h2 class="pfp-title">Hal-hal yang paling sering ditanyakan.</h2><div class="pfp-divider" data-inline-style="contact-293"></div></div>
<div class="contact-faq-list" data-inline-style="contact-294">
<?php foreach ($faqs as $faq): ?><article class="faq-item"><button class="faq-header" type="button" aria-expanded="false"><span><?=e($faq['question'])?></span><svg class="faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="m19 9-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg></button><div class="faq-body"><?=nl2br(e($faq['answer']))?></div></article><?php endforeach; ?>
<?php if($faqs===[]):?><p>Informasi pertanyaan umum akan segera tersedia.</p><?php endif;?>
</div></div></section>
