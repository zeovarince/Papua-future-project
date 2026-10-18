<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Response;
use App\Models\Application;
use App\Support\Labels;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;

final class ApplicationExportService
{
    private const CSV_MAX_ROWS = 5000;
    private const XLSX_MAX_ROWS = 2000;
    private const PDF_MAX_ROWS = 100;

    public function download(string $format, array $filters): Response
    {
        $limit = match ($format) {
            'csv' => self::CSV_MAX_ROWS,
            'xlsx' => self::XLSX_MAX_ROWS,
            'pdf' => self::PDF_MAX_ROWS,
            default => throw new RuntimeException('Format ekspor tidak tersedia.'),
        };
        $applications = Application::exportRows($filters, $limit + 1);
        if (count($applications) > $limit) {
            throw new RuntimeException('Hasil ekspor melebihi batas ' . number_format($limit, 0, ',', '.') . ' pendaftar. Persempit filter terlebih dahulu.');
        }
        if ($applications === []) {
            throw new RuntimeException('Tidak ada data pendaftar yang sesuai dengan filter ekspor.');
        }

        return match ($format) {
            'csv' => $this->csv($applications),
            'xlsx' => $this->xlsx($applications),
            'pdf' => $this->pdf($applications),
        };
    }

    private function csv(array $applications): Response
    {
        $columns = $this->columns();
        $rows = array_map(fn (array $application): array => $this->exportValues($application), $applications);
        $filename = 'pfp-pendaftar-' . date('Ymd-His') . '.csv';
        return Response::stream(static function () use ($columns, $rows): void {
            $output = fopen('php://output', 'wb');
            if ($output === false) {
                return;
            }
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, array_column($columns, 'label'), ';', '"', '');
            foreach ($rows as $row) {
                fputcsv($output, array_map([self::class, 'safeCsvValue'], $row), ';', '"', '');
            }
            fclose($output);
        }, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function xlsx(array $applications): Response
    {
        if (!class_exists(Spreadsheet::class) || !class_exists(Xlsx::class)) {
            throw new RuntimeException('Paket ekspor Excel belum terpasang. Jalankan composer install dan pastikan ekstensi PHP ZIP aktif.');
        }

        $columns = $this->columns();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pendaftar');
        foreach ($columns as $index => $column) {
            $coordinate = Coordinate::stringFromColumnIndex($index + 1) . '1';
            $sheet->setCellValueExplicit($coordinate, $column['label'], DataType::TYPE_STRING);
        }

        $hyperlinkColumns = ['photo_url' => 17, 'cv_url' => 18];
        foreach ($applications as $rowIndex => $application) {
            $values = $this->exportValues($application);
            $excelRow = $rowIndex + 2;
            foreach ($values as $columnIndex => $value) {
                $coordinate = Coordinate::stringFromColumnIndex($columnIndex + 1) . $excelRow;
                $sheet->setCellValueExplicit($coordinate, (string) $value, DataType::TYPE_STRING);
                $key = $columns[$columnIndex]['key'];
                if (isset($hyperlinkColumns[$key]) && (string) $value !== '') {
                    $sheet->getCell($coordinate)->getHyperlink()->setUrl((string) $value);
                    $sheet->getStyle($coordinate)->getFont()->getColor()->setARGB('FF2563EB');
                    $sheet->getStyle($coordinate)->getFont()->setUnderline(true);
                    $sheet->setCellValueExplicit($coordinate, $key === 'photo_url' ? 'Lihat foto' : 'Lihat CV', DataType::TYPE_STRING);
                }
            }
        }

        $lastRow = count($applications) + 1;
        $lastColumn = Coordinate::stringFromColumnIndex(count($columns));
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FF17483E');
        $sheet->getStyle('A1:' . $lastColumn . $lastRow)->getAlignment()->setVertical('top')->setWrapText(true);
        $sheet->getAutoFilter()->setRange('A1:' . $lastColumn . $lastRow);
        $sheet->freezePane('A2');
        $widths = [14, 22, 28, 15, 32, 20, 25, 25, 26, 16, 42, 32, 58, 42, 16, 20, 18, 18];
        foreach ($widths as $index => $width) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($index + 1))->setWidth($width);
        }
        foreach (['M', 'N'] as $column) {
            for ($row = 2; $row <= $lastRow; $row++) {
                $sheet->getRowDimension($row)->setRowHeight(-1);
                $sheet->getStyle($column . $row)->getAlignment()->setWrapText(true);
            }
        }

        $filename = 'pfp-pendaftar-' . date('Ymd-His') . '.xlsx';
        return Response::stream(static function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function pdf(array $applications): Response
    {
        if (!class_exists(Dompdf::class)) {
            throw new RuntimeException('Paket ekspor PDF belum terpasang. Jalankan composer install terlebih dahulu.');
        }
        $fileService = new ApplicationFileService();
        $records = [];
        foreach ($applications as $application) {
            $record = $this->displayRecord($application);
            $record['photo_data_uri'] = $this->photoDataUri($fileService->pathFor($application, 'photo'));
            $record['cv_url'] = $this->absoluteUrl('/admin/berkas/' . (int) $application['id'] . '/cv');
            $records[] = $record;
        }
        $generatedAt = date('d M Y H:i') . ' WIT';
        ob_start();
        require APP_ROOT . '/app/Views/admin/application/export-pdf.php';
        $html = (string) ob_get_clean();

        $temporaryDirectory = STORAGE_DIR . '/cache/dompdf';
        if (!is_dir($temporaryDirectory) && !mkdir($temporaryDirectory, 0750, true) && !is_dir($temporaryDirectory)) {
            throw new RuntimeException('Folder sementara ekspor PDF tidak dapat disiapkan.');
        }
        $options = new Options();
        $options->setDefaultFont('DejaVu Sans');
        $options->setIsRemoteEnabled(false);
        $options->setIsPhpEnabled(false);
        $options->setChroot(STORAGE_DIR);
        $options->setTempDir($temporaryDirectory);
        $options->setFontCache($temporaryDirectory);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="pfp-laporan-pendaftar-' . date('Ymd-His') . '.pdf"',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function columns(): array
    {
        return [
            ['key' => 'type', 'label' => 'Tipe'],
            ['key' => 'batch_name', 'label' => 'Batch'],
            ['key' => 'full_name', 'label' => 'Nama Lengkap'],
            ['key' => 'birth_date', 'label' => 'Tanggal Lahir'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'whatsapp', 'label' => 'WhatsApp'],
            ['key' => 'domicile', 'label' => 'Domisili'],
            ['key' => 'occupation', 'label' => 'Status Pekerjaan'],
            ['key' => 'university', 'label' => 'Universitas / Instansi'],
            ['key' => 'duration', 'label' => 'Durasi'],
            ['key' => 'interests', 'label' => 'Bidang Kontribusi'],
            ['key' => 'referral', 'label' => 'Sumber Informasi'],
            ['key' => 'motivation', 'label' => 'Motivasi'],
            ['key' => 'special_skills', 'label' => 'Keahlian Khusus'],
            ['key' => 'status', 'label' => 'Status Pendaftaran'],
            ['key' => 'created_at', 'label' => 'Tanggal Daftar'],
            ['key' => 'photo_url', 'label' => 'Foto Pendaftar'],
            ['key' => 'cv_url', 'label' => 'CV'],
        ];
    }

    private function exportValues(array $application): array
    {
        $record = $this->displayRecord($application);
        return [
            $record['type'], $record['batch_name'], $record['full_name'], $record['birth_date'],
            $record['email'], $record['whatsapp'], $record['domicile'], $record['occupation'],
            $record['university'], $record['duration'], $record['interests'], $record['referral'],
            $record['motivation'], $record['special_skills'], $record['status'], $record['created_at'],
            $this->absoluteUrl('/admin/berkas/' . (int) $application['id'] . '/foto'),
            $this->absoluteUrl('/admin/berkas/' . (int) $application['id'] . '/cv'),
        ];
    }

    private function displayRecord(array $application): array
    {
        $interestKeys = json_decode((string) ($application['interest_areas'] ?? '[]'), true);
        $interestKeys = is_array($interestKeys) ? $interestKeys : [];
        $interestLabels = Labels::interests();
        $interestText = [];
        foreach ($interestKeys as $key) {
            if (is_string($key) && isset($interestLabels[$key])) {
                $interestText[] = $interestLabels[$key];
            }
        }
        return [
            'id' => (int) $application['id'],
            'type' => Labels::applicationTypes()[$application['batch_type']] ?? (string) $application['batch_type'],
            'batch_name' => (string) $application['batch_name'],
            'full_name' => (string) $application['full_name'],
            'birth_date' => (string) $application['birth_date'],
            'email' => (string) $application['email'],
            'whatsapp' => (string) $application['whatsapp'],
            'domicile' => (string) $application['domicile'],
            'occupation' => Labels::occupations()[$application['occupation_status']] ?? (string) $application['occupation_status'],
            'university' => (string) ($application['university'] ?? ''),
            'duration' => Labels::durations()[$application['duration']] ?? (string) $application['duration'],
            'interests' => implode(', ', $interestText),
            'referral' => Labels::referralSources()[$application['referral_source']] ?? (string) $application['referral_source'],
            'motivation' => (string) $application['motivation'],
            'special_skills' => (string) ($application['special_skills'] ?? ''),
            'status' => Labels::applicationStatuses()[$application['status']] ?? (string) $application['status'],
            'created_at' => (string) $application['created_at'],
        ];
    }

    private function photoDataUri(?string $path): ?string
    {
        if ($path === null || !function_exists('imagecreatefromwebp')) {
            return null;
        }
        $source = @imagecreatefromwebp($path);
        if (!$source) {
            return null;
        }
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $canvas = imagecreatetruecolor(160, 160);
        $background = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $background);
        $scale = min(150 / max($sourceWidth, 1), 150 / max($sourceHeight, 1));
        $width = max(1, (int) round($sourceWidth * $scale));
        $height = max(1, (int) round($sourceHeight * $scale));
        imagecopyresampled($canvas, $source, (int) ((160 - $width) / 2), (int) ((160 - $height) / 2), 0, 0, $width, $height, $sourceWidth, $sourceHeight);
        ob_start();
        imagejpeg($canvas, null, 78);
        $jpeg = (string) ob_get_clean();
        imagedestroy($source);
        imagedestroy($canvas);
        return 'data:image/jpeg;base64,' . base64_encode($jpeg);
    }

    private function absoluteUrl(string $path): string
    {
        $configuredUrl = rtrim((string) (getenv('APP_URL') ?: ''), '/');
        if ($configuredUrl !== '') {
            return $configuredUrl . '/' . ltrim($path, '/');
        }
        $host = (string) ($_SERVER['SERVER_NAME'] ?? 'localhost');
        $host = preg_replace('/[^A-Za-z0-9.\-:\[\]]/', '', $host) ?: 'localhost';
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        return $scheme . '://' . $host . '/' . ltrim($path, '/');
    }

    private static function safeCsvValue(mixed $value): string
    {
        $text = (string) $value;
        if (preg_match('/^[\x00-\x20]*[=+\-@]/u', $text) === 1) {
            $text = "'" . $text;
        }
        return $text;
    }
}
