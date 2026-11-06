<?php

declare(strict_types=1);

$old = is_array($old ?? null) ? $old : [];
$errors = is_array($errors ?? null) ? $errors : [];
$isOpen = $isFormActive && $batch !== null && (int) ($batch['is_open'] ?? 0) === 1;
$hasUploadQuestions = false;
foreach ($questions as $question) {
    if (in_array((string) ($question['field_key'] ?? ''), ['photo', 'cv'], true)) {
        $hasUploadQuestions = true;
        break;
    }
}
?>
<section class="join-form-intro">
    <div class="pfp-container">
        <nav class="join-breadcrumb" aria-label="Breadcrumb">
            <a href="<?= url('/') ?>">Beranda</a><span aria-hidden="true">/</span>
            <a href="<?= url('/bergabung') ?>">Bergabung</a><span aria-hidden="true">/</span>
            <span aria-current="page"><?= e($typeLabel) ?><?= $batch !== null ? ' · ' . e($batch['name']) : '' ?></span>
        </nav>
        <p class="join-eyebrow">Pendaftaran <?= e($typeLabel) ?></p>
        <h1>Daftar sebagai <?= e(strtolower($typeLabel)) ?>.</h1>
        <p>Isi formulir <?= e(strtolower($typeLabel)) ?> ini. Data dan berkasmu hanya dapat diakses oleh tim PFP untuk proses seleksi.</p>
    </div>
</section>

<section class="pfp-section join-form-section" id="form-daftar" aria-labelledby="application-form-title">
    <div class="pfp-container join-form-container">
        <?php if (!empty($flashError)): ?>
            <p class="join-form-alert" role="alert"><?= e($flashError) ?></p>
        <?php endif; ?>

        <?php if (!$isOpen): ?>
            <div class="join-closed-state" role="status">
                <p class="join-eyebrow"><?= e($typeLabel) ?></p>
                <h2>Pendaftaran <?= e(strtolower($typeLabel)) ?> sedang ditutup.</h2>
                <?php if (!$isFormActive): ?>
                    <p>Tim PFP belum mengaktifkan formulir <?= e(strtolower($typeLabel)) ?>.</p>
                <?php elseif ($batch !== null && (int) $batch['is_open'] !== 1): ?>
                    <p><?= e($batch['name']) ?> sudah ditutup. Pilih batch lain yang masih dibuka.</p>
                <?php else: ?>
                    <p>Belum ada batch <?= e(strtolower($typeLabel)) ?> yang dibuka. Silakan kembali lagi nanti.</p>
                <?php endif; ?>
                <a class="join-path-link" href="<?= url('/bergabung') ?>">Lihat jalur lainnya</a>
            </div>
        <?php else: ?>
            <div class="join-form-heading">
                <p class="join-eyebrow">Formulir <?= e($typeLabel) ?></p>
                <h2 id="application-form-title">Data pendaftaran</h2>
                <p>Kolom bertanda * wajib diisi.</p>
            </div>

            <form class="application-form" action="<?= url('/bergabung/' . $typeSlug . '/' . (int) $batch['id']) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="join-honeypot" aria-hidden="true">
                    <label for="join-website">Kosongkan kolom ini</label>
                    <input id="join-website" name="website" type="text" tabindex="-1" autocomplete="off">
                </div>

                <input type="hidden" name="batch_id" value="<?= (int) $batch['id'] ?>">
                <p class="join-selected-batch application-field-full">Batch pilihan: <strong><?= e($batch['name']) ?></strong></p>
                <?php if (!empty($errors['batch_id'])): ?><p class="join-field-error application-field-full" role="alert"><?= e($errors['batch_id']) ?></p><?php endif; ?>

                <div class="application-form-section application-field-full"><h3>Pertanyaan pendaftaran</h3></div>
                <?php foreach ($questions as $question): ?>
                    <?php
                        $questionId = (int) $question['id'];
                        $fieldKey = (string) ($question['field_key'] ?? '');
                        $isSystem = $fieldKey !== '';
                        $requiredLabel = (int) $question['is_required'] === 1;
                        $type = (string) $question['field_type'];
                        $rawAnswer = $isSystem
                            ? ($old['question_answers'][$questionId] ?? $old[$fieldKey] ?? '')
                            : ($old['custom_answers'][$questionId] ?? '');
                        $answer = is_scalar($rawAnswer) ? (string) $rawAnswer : '';
                        if ($isSystem && $answer !== '') {
                            $legacyOptions = match ($fieldKey) {
                                'occupation_status' => \App\Support\Labels::occupations(),
                                'duration' => \App\Support\Labels::durations(),
                                'referral_source' => \App\Support\Labels::referralSources(),
                                default => [],
                            };
                            $answer = $legacyOptions[$answer] ?? $answer;
                        }
                        $options = array_values(array_filter(array_map('trim', explode("\n", (string) ($question['options'] ?? ''))), static fn (string $value): bool => $value !== ''));
                        if ($options === [] && $isSystem) {
                            $options = match ($fieldKey) {
                                'occupation_status' => array_values(\App\Support\Labels::occupations()),
                                'duration' => array_values(\App\Support\Labels::durations()),
                                'interest_areas' => array_values(\App\Support\Labels::interests()),
                                'referral_source' => array_values(\App\Support\Labels::referralSources()),
                                default => [],
                            };
                        }
                        $checkboxAnswers = is_array($rawAnswer) ? $rawAnswer : array_map('trim', explode(',', $answer));
                        $inputName = $isSystem ? 'question_answers[' . $questionId . ']' : 'custom_answers[' . $questionId . ']';
                        $errorKey = $isSystem
                            ? (in_array($fieldKey, ['photo', 'cv'], true) ? $fieldKey : 'question_answers_' . $questionId)
                            : 'custom_answers_' . $questionId;
                        $fullWidth = in_array($type, ['TEXTAREA', 'RADIO', 'CHECKBOX', 'SELECT', 'URL', 'FILE', 'IMAGE', 'PDF'], true);
                    ?>
                    <div class="application-field <?= $fullWidth ? 'application-field-full' : '' ?>">
                        <label class="join-form-label" for="question_<?= $questionId ?>"><?= e($question['question']) ?><?= $requiredLabel ? ' *' : '' ?></label>
                        <?php if ($type === 'TEXT' || $type === 'EMAIL' || $type === 'TEL' || $type === 'DATE' || $type === 'URL' || $type === 'FILE'): ?>
                            <?php
                                $inputType = match ($type) {
                                    'EMAIL' => 'email',
                                    'TEL' => 'tel',
                                    'DATE' => 'date',
                                    'URL', 'FILE' => 'url',
                                    default => 'text',
                                };
                                $inputName = $isSystem ? 'question_answers[' . $questionId . ']' : 'custom_answers[' . $questionId . ']';
                            ?>
                            <input class="join-form-input" id="question_<?= $questionId ?>" name="<?= e($inputName) ?>" type="<?= e($inputType) ?>" maxlength="<?= $type === 'TEXT' ? '200' : '250' ?>" value="<?= e($answer) ?>" <?= $type === 'URL' || $type === 'FILE' ? 'placeholder="https://"' : '' ?> <?= $requiredLabel ? 'required' : '' ?>>
                        <?php elseif ($type === 'IMAGE' || $type === 'PDF'): ?>
                            <?php $fileName = $fieldKey === 'photo' ? 'photo' : 'cv'; $accept = $type === 'IMAGE' ? 'image/jpeg,image/png,image/webp' : 'application/pdf,.pdf'; ?>
                            <input class="join-form-input application-file-input" id="question_<?= $questionId ?>" name="<?= e($fileName) ?>" type="file" accept="<?= e($accept) ?>" <?= $requiredLabel ? 'required' : '' ?>>
                            <small><?= $type === 'IMAGE' ? 'JPG, PNG, WebP. Maksimal 2 MB.' : 'PDF maksimal 5 MB.' ?></small>
                        <?php elseif ($type === 'RADIO'): ?>
                            <div class="custom-choice-list">
                                <?php foreach ($options as $optionIndex => $option): ?>
                                    <label><input type="radio" name="<?= e($inputName) ?>" value="<?= e($option) ?>" <?= $answer === $option ? 'checked' : '' ?> <?= $requiredLabel && $optionIndex === 0 ? 'required' : '' ?>><span><?= e($option) ?></span></label>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif ($type === 'CHECKBOX'): ?>
                            <div class="custom-choice-list">
                                <?php foreach ($options as $option): ?>
                                    <label><input type="checkbox" name="<?= e($inputName) ?>[]" value="<?= e($option) ?>" <?= in_array($option, $checkboxAnswers, true) ? 'checked' : '' ?>><span><?= e($option) ?></span></label>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif ($type === 'SELECT'): ?>
                            <select class="join-form-select" id="question_<?= $questionId ?>" name="<?= e($inputName) ?>" <?= $requiredLabel ? 'required' : '' ?>>
                                <option value="">Pilih salah satu</option>
                                <?php foreach ($options as $option): ?><option value="<?= e($option) ?>" <?= $answer === $option ? 'selected' : '' ?>><?= e($option) ?></option><?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <textarea class="join-form-textarea" id="question_<?= $questionId ?>" name="<?= e($inputName) ?>" maxlength="2000" rows="4" <?= $requiredLabel ? 'required' : '' ?>><?= e($answer) ?></textarea>
                        <?php endif; ?>
                        <?php if (!empty($errors[$errorKey])): ?><small class="join-field-error"><?= e($errors[$errorKey]) ?></small><?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="application-field application-field-full">
                    <button class="join-submit-btn" type="submit">Kirim pendaftaran <?= e(strtolower($typeLabel)) ?></button>
                    <?php if ($hasUploadQuestions): ?><p class="application-privacy-note">Berkas yang kamu unggah hanya dapat dilihat oleh admin PFP.</p><?php endif; ?>
                </div>
            </form>
        <?php endif; ?>
    </div>
</section>
