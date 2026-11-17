-- Tambah kolom complaint_photo ke tabel service_requests
ALTER TABLE `service_requests`
ADD COLUMN `complaint_photo` VARCHAR(255) DEFAULT NULL
    COMMENT 'Foto kerusakan yang diupload customer saat booking'
AFTER `complaint`;
