<?php

namespace App\Services;

use App\Core\Config;
use RuntimeException;

class FileUploadService
{
    private string $privateStorageBase;
    private int $maxSize;
    private array $allowedMimes;
    private array $allowedExtensions;

    public function __construct()
    {
        $this->privateStorageBase = Config::get('storage.private_documents', dirname(__DIR__, 2) . '/storage/private/documents');
        $this->maxSize = Config::get('storage.max_size', 5242880);
        $this->allowedMimes = Config::get('storage.allowed_document_mimes', [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]);
        $this->allowedExtensions = Config::get('storage.allowed_document_extensions', ['pdf', 'doc', 'docx']);
    }

    public function validate(array $file): ?string
    {
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return 'File upload error code: ' . ($file['error'] ?? 'UNKNOWN');
        }

        if ($file['size'] > $this->maxSize) {
            $maxMb = round($this->maxSize / 1048576, 1);
            return "File size exceeds maximum limit of {$maxMb}MB.";
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $this->allowedMimes, true)) {
            return "Invalid document format. Allowed: PDF, DOC, DOCX.";
        }

        $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExtensions, true)) {
            return "Invalid document extension. Allowed: " . implode(', ', $this->allowedExtensions);
        }

        return null;
    }

    public function upload(array $file, string $subfolder = 'documents'): string
    {
        $error = $this->validate($file);
        if ($error !== null) {
            throw new RuntimeException($error);
        }

        $targetDir = $this->privateStorageBase;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0700, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $destination = $targetDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            if (file_exists($file['tmp_name']) && copy($file['tmp_name'], $destination)) {
                @unlink($file['tmp_name']);
            } else {
                throw new RuntimeException("Failed to store private document.");
            }
        }

        return 'private/documents/' . $filename;
    }

    public function download(string $storedPath, string $downloadFilename): void
    {
        $cleanPath = str_replace(['../', '..\\'], '', $storedPath);
        $cleanPath = str_replace('\\', '/', $cleanPath);
        $filename = basename($cleanPath);
        $base = rtrim(str_replace('\\', '/', $this->privateStorageBase), '/');
        $fullPath = $base . '/' . $filename;

        if (!file_exists($fullPath) || !is_file($fullPath)) {
            http_response_code(404);
            echo "Document not found.";
            exit;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fullPath);
        finfo_close($finfo);

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . basename($downloadFilename) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fullPath));
        readfile($fullPath);
        exit;
    }
}
