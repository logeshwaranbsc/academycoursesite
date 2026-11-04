<?php

use App\Services\ImageUploadService;
use App\Services\FileUploadService;

if (!function_exists('upload_image')) {
    function upload_image(array $file, string $folder): string
    {
        $service = new ImageUploadService();
        return $service->upload($file, $folder);
    }
}

if (!function_exists('delete_image')) {
    function delete_image(?string $path): bool
    {
        $service = new ImageUploadService();
        return $service->delete($path);
    }
}

if (!function_exists('upload_document')) {
    function upload_document(array $file, string $folder = 'documents'): string
    {
        $service = new FileUploadService();
        return $service->upload($file, $folder);
    }
}
