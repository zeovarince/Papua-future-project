<section class="hero-mini px-4 py-20 text-center">
    <div class="mx-auto max-w-4xl">
        <p class="text-sm font-bold uppercase tracking-widest text-primary">Program PFP</p>
        <h1 class="mt-4 text-4xl font-extrabold text-slate-900"><?= e($program['title']) ?></h1>
        <p class="mx-auto mt-5 max-w-2xl text-lg leading-relaxed text-slate-600"><?= e($program['summary']) ?></p>
        <a class="mt-8 inline-flex rounded-xl bg-primary px-5 py-3 font-semibold text-white" href="<?= url('/program') ?>">Semua Program</a>
    </div>
</section>
