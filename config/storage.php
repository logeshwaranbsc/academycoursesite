<?php

return [
    'public_uploads' => dirname(__DIR__) . '/public/uploads',
    'private_documents' => dirname(__DIR__) . '/storage/private/documents',
    'max_size' => (int)($_ENV['MAX_UPLOAD_SIZE'] ?? 5242880), // 5MB
    'allowed_image_mimes' => [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/svg+xml'
    ],
    'allowed_image_extensions' => ['jpg', 'jpeg', 'png', 'webp', 'svg'],
    'allowed_document_mimes' => [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ],
    'allowed_document_extensions' => ['pdf', 'doc', 'docx'],
    'directories' => [
        'banners' => 'uploads/banners',
        'courses' => 'uploads/courses',
        'placements' => 'uploads/placements',
        'success-stories' => 'uploads/success-stories',
        'testimonials' => 'uploads/testimonials',
        'culture-videos' => 'uploads/culture-videos',
        'gallery' => 'uploads/gallery',
        'settings' => 'uploads/settings',
        'documents' => 'storage/private/documents',
    ]
];
