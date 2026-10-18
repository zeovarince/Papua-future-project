<section class="min-h-[60vh] flex items-center justify-center px-6 py-20 text-center">
    <div class="max-w-xl">
        <h1 class="text-3xl font-extrabold text-slate-900">Maaf, halaman sedang bermasalah</h1>
        <p class="mt-4 text-slate-600">Silakan muat ulang halaman beberapa saat lagi.</p>
        <?php if (!empty($detail)): ?>
            <pre class="mt-5 overflow-auto rounded-xl bg-slate-100 p-4 text-left text-sm text-slate-700"><?= e($detail) ?></pre>
        <?php endif; ?>
        <a class="mt-7 inline-flex rounded-xl bg-primary px-5 py-3 font-semibold text-white" href="<?= url('/') ?>">Kembali ke Beranda</a>
    </div>
</section>
