-- Jalankan SATU KALI pada database yang sudah diimpor dari database/pfp.sql versi lama.
-- Skrip ini hanya mengubah indeks; tidak mengubah atau menghapus isi tabel.

ALTER TABLE batches
  ADD KEY idx_batches_type_id (batch_type, id),
  ADD KEY idx_batches_created (created_at);

ALTER TABLE team_members
  DROP INDEX idx_team_group_sort,
  ADD KEY idx_team_public_order (member_group, is_published, batch_id, sort_order, name);

ALTER TABLE programs
  DROP INDEX idx_programs_published_sort,
  DROP INDEX idx_programs_featured,
  ADD KEY idx_programs_public_order (is_published, sort_order, title),
  ADD KEY idx_programs_featured_order (is_published, is_featured, sort_order, title);

ALTER TABLE program_photos
  DROP INDEX idx_program_photos_created;

ALTER TABLE applications
  DROP INDEX idx_applications_batch_status,
  ADD KEY idx_applications_batch_status_created (batch_id, status, created_at),
  ADD KEY idx_applications_status_created (status, created_at);
