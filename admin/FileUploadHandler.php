<?php
/**
 * Secure File Upload Handler
 * Provides safe file upload functionality with validation
 */
class FileUploadHandler {
    
    /**
     * Validate and upload file
     */
    public static function uploadImage($file, $uploadPath = UPLOAD_PATH) {
        $errors = [];
        
        // Check if file was uploaded
        if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            $errors[] = "No file uploaded";
            return ['success' => false, 'errors' => $errors];
        }
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = self::getUploadErrorMessage($file['error']);
            return ['success' => false, 'errors' => $errors];
        }
        
        // Validate file size
        if ($file['size'] > MAX_FILE_SIZE) {
            $errors[] = "File size exceeds maximum allowed size (" . (MAX_FILE_SIZE / 1024 / 1024) . "MB)";
            return ['success' => false, 'errors' => $errors];
        }
        
        // Validate file type using multiple methods
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
            $errors[] = "Invalid file type. Only JPG, PNG, and WebP images are allowed";
            return ['success' => false, 'errors' => $errors];
        }
        
        // Validate file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_EXTENSIONS)) {
            $errors[] = "Invalid file extension";
            return ['success' => false, 'errors' => $errors];
        }
        
        // Validate image dimensions (optional - prevents zip bombs)
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            $errors[] = "Invalid image file";
            return ['success' => false, 'errors' => $errors];
        }
        
        // Check image dimensions to prevent extremely large images
        if ($imageInfo[0] > 5000 || $imageInfo[1] > 5000) {
            $errors[] = "Image dimensions too large. Maximum 5000x5000 pixels";
            return ['success' => false, 'errors' => $errors];
        }
        
        // Generate secure filename
        $newFilename = self::generateSecureFilename($file['name']);
        $targetPath = $uploadPath . $newFilename;
        
        // Ensure upload directory exists and is writable
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $errors[] = "Failed to move uploaded file";
            return ['success' => false, 'errors' => $errors];
        }
        
        // Set proper permissions
        chmod($targetPath, 0644);
        
        return [
            'success' => true,
            'filename' => $newFilename,
            'filepath' => $targetPath
        ];
    }
    
    /**
     * Generate secure filename with timestamp and random string
     */
    private static function generateSecureFilename($originalName) {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $timestamp = time();
        $randomString = bin2hex(random_bytes(8));
        return "{$timestamp}_{$randomString}.{$extension}";
    }
    
    /**
     * Delete file securely
     */
    public static function deleteFile($filename, $uploadPath = UPLOAD_PATH) {
        // Sanitize filename to prevent directory traversal
        $filename = basename($filename);
        $filepath = $uploadPath . $filename;
        
        // Check if file exists
        if (!file_exists($filepath)) {
            return ['success' => false, 'error' => 'File does not exist'];
        }
        
        // Check if path is within allowed directory
        $realPath = realpath($filepath);
        $allowedPath = realpath($uploadPath);
        
        if (strpos($realPath, $allowedPath) !== 0) {
            error_log("Attempted directory traversal: $filepath");
            return ['success' => false, 'error' => 'Invalid file path'];
        }
        
        // Delete file
        if (unlink($filepath)) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Failed to delete file'];
        }
    }
    
    /**
     * Get upload error message
     */
    private static function getUploadErrorMessage($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return "File is too large";
            case UPLOAD_ERR_PARTIAL:
                return "File was only partially uploaded";
            case UPLOAD_ERR_NO_TMP_DIR:
                return "Missing temporary folder";
            case UPLOAD_ERR_CANT_WRITE:
                return "Failed to write file to disk";
            case UPLOAD_ERR_EXTENSION:
                return "File upload stopped by extension";
            default:
                return "Unknown upload error";
        }
    }
    
    /**
     * Resize image (optional feature for optimization)
     */
    public static function resizeImage($filepath, $maxWidth = 1920, $maxHeight = 1080, $quality = 85) {
        list($width, $height, $type) = getimagesize($filepath);
        
        // Only resize if image is larger than max dimensions
        if ($width <= $maxWidth && $height <= $maxHeight) {
            return true;
        }
        
        // Calculate new dimensions
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = intval($width * $ratio);
        $newHeight = intval($height * $ratio);
        
        // Create new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Load source image
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($filepath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($filepath);
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                break;
            case IMAGETYPE_WEBP:
                $source = imagecreatefromwebp($filepath);
                break;
            default:
                return false;
        }
        
        // Resize
        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Save resized image
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($newImage, $filepath, $quality);
                break;
            case IMAGETYPE_PNG:
                imagepng($newImage, $filepath, 9);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($newImage, $filepath, $quality);
                break;
        }
        
        imagedestroy($source);
        imagedestroy($newImage);
        
        return true;
    }
}
?>
