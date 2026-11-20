<?php
// ==============================================================================
// Smart Gram - Secure File Upload Service
// Strictly enforces MIME type sniffing, extension whitelisting, and safe hashing.
// ==============================================================================

namespace App\Services;

use Exception;
use finfo;

class FileUploadService {
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png'  => ['png'],
        'image/webp' => ['webp']
    ];

    private const MAX_FILE_SIZE_BYTES = 5 * 1024 * 1024; // 5 MB

    /**
     * Validate and store an uploaded image safely
     *
     * @param array $file The $_FILES['photo'] array
     * @return array Metadata of the stored file
     * @throws Exception If validation or upload fails
     */
    public static function uploadImage(array $file): array {
        // 1. Check for standard PHP upload errors
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new Exception("Invalid file upload parameters.");
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw match ($file['error']) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => new Exception("The uploaded file exceeds the 5MB size limit."),
                UPLOAD_ERR_PARTIAL   => new Exception("The file was only partially uploaded. Please try again."),
                UPLOAD_ERR_NO_FILE   => new Exception("No file was attached."),
                default              => new Exception("An error occurred during file upload (Error code: {$file['error']}).")
            };
        }

        // 2. Validate upload integrity (prevent local file inclusion attacks)
        $tmpPath = $file['tmp_name'];
        if (!is_uploaded_file($tmpPath)) {
            throw new Exception("Security Alert: The specified file is not a valid uploaded file.");
        }

        // 3. Validate file size
        $fileSize = (int)$file['size'];
        if ($fileSize > self::MAX_FILE_SIZE_BYTES) {
            throw new Exception("The uploaded photo exceeds the 5MB limit. Please upload a smaller image.");
        }

        // 4. Validate true MIME type using PHP fileinfo (never trust $_FILES['type'])
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($tmpPath);

        if (!array_key_exists($mimeType, self::ALLOWED_MIME_TYPES)) {
            throw new Exception("Invalid file type ({$mimeType}). Only JPG, PNG, and WEBP images are allowed.");
        }

        // 5. Validate and sanitize extension
        $originalName = basename($file['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($extension, self::ALLOWED_MIME_TYPES[$mimeType], true)) {
            // Correct extension based on MIME type to prevent extension spoofing
            $extension = self::ALLOWED_MIME_TYPES[$mimeType][0];
        }

        // 6. Generate collision-free cryptographic filename
        $safeFileName = hash('sha256', uniqid('', true) . random_bytes(16)) . '.' . $extension;

        // 7. Ensure destination directory exists and is protected
        $uploadDir = BASE_PATH . '/storage/uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destination = $uploadDir . '/' . $safeFileName;

        // 8. Move uploaded file to secure storage
        if (!move_uploaded_file($tmpPath, $destination)) {
            throw new Exception("Failed to move the uploaded file to secure storage.");
        }

        return [
            'file_path'     => 'storage/uploads/' . $safeFileName,
            'original_name' => substr($originalName, 0, 250),
            'file_size'     => $fileSize,
            'mime_type'     => $mimeType
        ];
    }
}
