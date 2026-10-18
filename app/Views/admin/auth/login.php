<main class="login-shell">
    <section class="login-desktop" aria-label="Masuk ke panel admin">
        <div class="login-slider" data-login-slider>
            <div class="login-slides" aria-hidden="true">
                <div class="login-slide is-active"><img src="<?= asset('img/hero/herobanner.png') ?>" alt=""></div>
                <div class="login-slide"><img src="<?= asset('img/about/profilsingkat.png') ?>" alt=""></div>
                <div class="login-slide"><img src="<?= asset('img/team/Founder.jpg') ?>" alt=""></div>
            </div>
            <div class="login-slider-shade" aria-hidden="true"></div>

            <a class="login-brand-on-photo" href="<?= url('/') ?>">
                <img src="<?= asset('img/logo.png') ?>" alt="">
                <span>Papua Future Project</span>
            </a>

            <h1 class="login-photo-heading" data-login-heading>Setiap anak berhak atas masa depan yang cerah.</h1>

            <div class="login-slider-dots" role="group" aria-label="Foto kegiatan Papua Future Project">
                <button class="is-active" type="button" data-slide-to="0" aria-label="Foto kegiatan belajar" aria-pressed="true"></button>
                <button type="button" data-slide-to="1" aria-label="Foto Pojok Baca" aria-pressed="false"></button>
                <button type="button" data-slide-to="2" aria-label="Foto founder" aria-pressed="false"></button>
            </div>
        </div>

        <section class="login-card-panel" aria-labelledby="desktop-login-title">
            <div class="login-card-content">
                <header class="login-card-header">
                    <a class="login-card-brand" href="<?= url('/') ?>">
                        <img src="<?= asset('img/logo.png') ?>" alt="">
                        <span>Papua Future Project</span>
                    </a>
                    <span class="login-card-label">Panel Admin</span>
                </header>

                <div class="login-card-center">
                    <div class="login-form-content">
                        <h2 id="desktop-login-title">Masuk ke Panel Admin</h2>
                        <p class="login-description">Masukkan email dan kata sandi untuk melanjutkan.</p>

                        <?php if (!empty($error)): ?>
                            <div class="login-error" id="desktop-login-error" role="alert"><?= e($error) ?></div>
                        <?php endif; ?>

                        <form action="<?= url('/admin/login') ?>" method="post" class="login-form">
                            <?= csrf_field() ?>
                            <div class="login-field">
                                <label for="login-email">Email</label>
                                <div class="login-input-box">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><rect x="2" y="4" width="20" height="16" rx="2" stroke="currentColor" stroke-width="2"/></svg>
                                    <input id="login-email" name="email" type="email" placeholder="nama@email.com" autocomplete="username" inputmode="email" required maxlength="190" value="<?= e($email ?? '') ?>">
                                </div>
                            </div>

                            <div class="login-field">
                                <label for="login-pass">Kata Sandi</label>
                                <div class="login-input-box">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect width="18" height="11" x="3" y="11" rx="2" stroke="currentColor" stroke-width="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                    <input id="login-pass" name="password" type="password" placeholder="••••••••" autocomplete="current-password" required maxlength="4096">
                                    <button class="login-eye-button" type="button" data-password-toggle="login-pass" aria-label="Tampilkan kata sandi" aria-pressed="false">
                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0Z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg>
                                    </button>
                                </div>
                            </div>

                            <div class="login-forgot-row"><button type="button" data-open-forgot>Lupa kata sandi?</button></div>
                            <button class="login-submit" type="submit">Masuk</button>
                        </form>

                        <div class="login-divider"><span></span><small>Atau</small><span></span></div>
                        <button class="login-google" type="button" data-google-notice>
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18A11 11 0 0 0 1 12c0 1.78.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                            <span>Masuk dengan Google</span>
                        </button>
                    </div>
                </div>

                <footer class="login-card-footer">Kembali ke <a href="<?= url('/') ?>">Website Utama PFP</a></footer>
            </div>
        </section>
    </section>

    <section class="login-mobile" aria-label="Masuk ke panel admin">
        <header class="mobile-login-top">
            <img class="mobile-login-photo" src="<?= asset('img/hero/herobanner.png') ?>" alt="">
            <div class="mobile-login-shade" aria-hidden="true"></div>
            <div class="mobile-login-toolbar">
                <a class="mobile-website-link" href="<?= url('/') ?>">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Website
                </a>
                <a class="mobile-login-brand" href="<?= url('/') ?>">
                    <img src="<?= asset('img/logo.png') ?>" alt="">
                    <span>Admin PFP</span>
                </a>
            </div>
            <div class="mobile-login-title">
                <span>Every Child Matters</span>
                <h1>Masuk ke Panel Admin</h1>
            </div>
        </header>

        <div class="mobile-login-card">
            <div class="mobile-login-content">
                <div class="mobile-login-intro">
                    <h2>Masuk ke Panel Admin</h2>
                    <p>Masukkan email dan kata sandi untuk melanjutkan.</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="login-error" id="mobile-login-error" role="alert"><?= e($error) ?></div>
                <?php endif; ?>

                <form action="<?= url('/admin/login') ?>" method="post" class="login-form mobile-login-form">
                    <?= csrf_field() ?>
                    <div class="login-field">
                        <label for="m-login-email">Email</label>
                        <div class="login-input-box">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><rect x="2" y="4" width="20" height="16" rx="2" stroke="currentColor" stroke-width="2"/></svg>
                            <input id="m-login-email" name="email" type="email" placeholder="nama@email.com" autocomplete="username" inputmode="email" required maxlength="190" value="<?= e($email ?? '') ?>">
                        </div>
                    </div>

                    <div class="login-field">
                        <div class="mobile-password-heading">
                            <label for="m-login-pass">Kata Sandi</label>
                            <button type="button" data-open-forgot>Lupa kata sandi?</button>
                        </div>
                        <div class="login-input-box">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect width="18" height="11" x="3" y="11" rx="2" stroke="currentColor" stroke-width="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            <input id="m-login-pass" name="password" type="password" placeholder="••••••••" autocomplete="current-password" required maxlength="4096">
                            <button class="login-eye-button" type="button" data-password-toggle="m-login-pass" aria-label="Tampilkan kata sandi" aria-pressed="false">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0Z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg>
                            </button>
                        </div>
                    </div>

                    <button class="login-submit" type="submit">Masuk</button>
                </form>

                <div class="mobile-login-divider"><span></span><small>Atau</small><span></span></div>
                <button class="login-google" type="button" data-google-notice>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18A11 11 0 0 0 1 12c0 1.78.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    <span>Masuk dengan Google</span>
                </button>
                <p class="mobile-login-back">Kembali ke <a href="<?= url('/') ?>">Beranda Utama</a></p>
            </div>
        </div>
    </section>

    <div class="forgot-modal" data-forgot-modal hidden>
        <div class="forgot-dialog" role="dialog" aria-modal="true" aria-labelledby="forgot-title" aria-describedby="forgot-description">
            <button class="forgot-close" type="button" data-close-forgot aria-label="Tutup">&times;</button>
            <span class="forgot-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none"><rect width="18" height="11" x="3" y="11" rx="2" stroke="currentColor" stroke-width="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </span>
            <h2 id="forgot-title">Reset Kata Sandi</h2>
            <p id="forgot-description">Fitur reset otomatis belum tersedia. Hubungi Product Owner Papua Future Project untuk bantuan mengatur ulang kata sandi.</p>
            <button class="forgot-confirm" type="button" data-close-forgot>Mengerti</button>
        </div>
    </div>
    <div class="login-toast-container" data-login-toast role="status" aria-live="polite" aria-atomic="true"></div>
</main>
