<?php

namespace App\Services;

use App\Core\Config;
use RuntimeException;

class ImageUploadService
{
    private string $publicUploadsBase;
    private array $folderMap;
    private int $maxSize;
    private array $allowedMimes;
    private array $allowedExtensions;

    public function __construct()
    {
        $this->publicUploadsBase = Config::get('storage.public_uploads', dirname(__DIR__, 2) . '/public/uploads');
        $this->maxSize = Config::get('storage.max_size', 5242880); // 5MB default
        $this->allowedMimes = Config::get('storage.allowed_image_mimes', [
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/x-png',
            'image/webp',
            'image/svg+xml'
        ]);
        $this->allowedExtensions = Config::get('storage.allowed_image_extensions', ['jpg', 'jpeg', 'png', 'webp', 'svg']);

        $this->folderMap = [
            'banners' => 'banners',
            'hero-banners' => 'banners',
            'courses' => 'courses',
            'placements' => 'placements',
            'success-stories' => 'success-stories',
            'testimonials' => 'testimonials',
            'culture-videos' => 'culture-videos',
            'gallery' => 'gallery',
            'settings' => 'settings',
        ];
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

        // Validate actual MIME type using finfo_file
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $this->allowedMimes, true)) {
            return "Invalid file type. Detected MIME: {$mime}. Allowed: JPG, PNG, WEBP, SVG.";
        }

        // Validate extension
        $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExtensions, true)) {
            return "Invalid file extension (.{$ext}). Allowed: " . implode(', ', $this->allowedExtensions);
        }

        // Block executable signatures or PHP tags inside image
        $content = file_get_contents($file['tmp_name'], false, null, 0, 1024);
        if (preg_match('/<\?php|<\?=|<!DOCTYPE html|<script/i', $content)) {
            return "File contains prohibited executable or script code.";
        }

        return null;
    }

    /**
     * Upload an image and return the relative path (e.g. 'uploads/courses/a1b2c3d4.jpg')
     */
    public function upload(array $file, string $folder): string
    {
        $validationError = $this->validate($file);
        if ($validationError !== null) {
            throw new RuntimeException($validationError);
        }

        $subDir = $this->folderMap[$folder] ?? 'gallery';
        $targetDir = $this->publicUploadsBase . '/' . $subDir;

        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                throw new RuntimeException("Failed to create upload directory: {$targetDir}");
            }
        }

        // Generate safe unique filename
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $destination = $targetDir . '/' . $filename;

        // Prevent path traversal
        $realTargetDir = realpath($targetDir);
        if ($realTargetDir === false) {
            throw new RuntimeException("Failed to access upload directory: {$targetDir}");
        }

        $destination = $realTargetDir . DIRECTORY_SEPARATOR . $filename;

        // Prevent path traversal (normalized for Windows and Unix paths)
        $normRealTarget = strtolower(rtrim(str_replace('\\', '/', $realTargetDir), '/'));
        $normDestDir = strtolower(rtrim(str_replace('\\', '/', dirname($destination)), '/'));

        if (!str_starts_with($normDestDir, $normRealTarget)) {
            throw new RuntimeException("Security violation: path traversal detected.");
        }

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            if (file_exists($file['tmp_name']) && copy($file['tmp_name'], $destination)) {
                @unlink($file['tmp_name']);
            } else {
                throw new RuntimeException("Failed to save uploaded file to destination.");
            }
        }

        // Return relative path for database storage
        return 'uploads/' . $subDir . '/' . $filename;
    }

    /**
     * Replace an existing image: deletes old image and uploads new one
     */
    public function replace(array $newFile, string $folder, ?string $oldPath): string
    {
        $newPath = $this->upload($newFile, $folder);
        $this->delete($oldPath);
        return $newPath;
    }

    /**
     * Safely delete an image from disk by relative path
     */
    public function delete(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        // Clean path and prevent directory traversal
        $cleanPath = str_replace(['../', '..\\'], '', $path);
        $cleanPath = str_replace('\\', '/', $cleanPath);
        $cleanPath = ltrim($cleanPath, '/');

        // Only allow deleting within uploads directory
        if (!str_starts_with($cleanPath, 'uploads/')) {
            return false;
        }

        $subPath = substr($cleanPath, strlen('uploads/'));
        $base = rtrim(str_replace('\\', '/', $this->publicUploadsBase), '/');
        $fullPath = $base . '/' . ltrim($subPath, '/');

        if (file_exists($fullPath) && is_file($fullPath)) {
            return @unlink($fullPath);
        }

        return false;
    }
}
