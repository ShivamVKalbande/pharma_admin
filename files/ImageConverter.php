<?php
/**
 * ImageConverter - Converts uploaded images to AVIF format with compression
 * Handles various input formats (JPG, PNG, WEBP, GIF) and outputs optimized AVIF
 */

class ImageConverter {
    
    private static $uploadDir = 'uploads/products/';
    private static $maxWidth = 1200;
    private static $maxHeight = 1200;
    private static $quality = 85; // AVIF quality (0-100, higher is better)
    
    /**
     * Initialize upload directory
     */
    public static function init() {
        if (!file_exists(self::$uploadDir)) {
            mkdir(self::$uploadDir, 0755, true);
        }
    }
    
    /**
     * Convert and save uploaded image to AVIF format
     * 
     * @param array $file The $_FILES array element
     * @return array Result with 'success', 'filename', 'errors'
     */
    public static function convertToAVIF($file) {
        $result = [
            'success' => false,
            'filename' => null,
            'errors' => []
        ];
        
        self::init();
        
        // Validate file upload
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $result['errors'][] = "No valid file uploaded";
            return $result;
        }
        
        // Get file info
        $fileInfo = getimagesize($file['tmp_name']);
        if ($fileInfo === false) {
            $result['errors'][] = "Uploaded file is not a valid image";
            return $result;
        }
        
        $mimeType = $fileInfo['mime'];
        $width = $fileInfo[0];
        $height = $fileInfo[1];
        
        // Validate file size (max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            $result['errors'][] = "File size must be less than 10MB";
            return $result;
        }
        
        // Create image resource from uploaded file
        $sourceImage = null;
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                $sourceImage = imagecreatefromjpeg($file['tmp_name']);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($file['tmp_name']);
                break;
            case 'image/webp':
                $sourceImage = imagecreatefromwebp($file['tmp_name']);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($file['tmp_name']);
                break;
            default:
                $result['errors'][] = "Unsupported image format. Supported: JPG, PNG, WEBP, GIF";
                return $result;
        }
        
        if ($sourceImage === false) {
            $result['errors'][] = "Failed to process image";
            return $result;
        }
        
        // Calculate new dimensions while maintaining aspect ratio
        list($newWidth, $newHeight) = self::calculateDimensions($width, $height);
        
        // Create new image with calculated dimensions
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
        $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
        imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);
        
        // Resize image
        imagecopyresampled(
            $resizedImage, $sourceImage,
            0, 0, 0, 0,
            $newWidth, $newHeight, $width, $height
        );
        
        // Generate unique filename
        $filename = self::generateFilename();
        $filepath = self::$uploadDir . $filename;
        
        // Check if AVIF is supported
        if (function_exists('imageavif')) {
            // Save as AVIF
            $saved = imageavif($resizedImage, $filepath, self::$quality);
        } else {
            // Fallback to WebP if AVIF not supported
            $filename = str_replace('.avif', '.webp', $filename);
            $filepath = self::$uploadDir . $filename;
            $saved = imagewebp($resizedImage, $filepath, self::$quality);
            $result['errors'][] = "AVIF not supported, saved as WebP instead";
        }
        
        // Free memory
        imagedestroy($sourceImage);
        imagedestroy($resizedImage);
        
        if ($saved) {
            // Optimize file permissions
            chmod($filepath, 0644);
            
            $result['success'] = true;
            $result['filename'] = $filename;
            $result['original_name'] = $file['name'];
            $result['width'] = $newWidth;
            $result['height'] = $newHeight;
            $result['filesize'] = filesize($filepath);
        } else {
            $result['errors'][] = "Failed to save converted image";
        }
        
        return $result;
    }
    
    /**
     * Calculate new dimensions maintaining aspect ratio
     */
    private static function calculateDimensions($width, $height) {
        $newWidth = $width;
        $newHeight = $height;
        
        // Calculate ratio
        if ($width > self::$maxWidth || $height > self::$maxHeight) {
            $ratio = $width / $height;
            
            if ($width > $height) {
                $newWidth = self::$maxWidth;
                $newHeight = round($newWidth / $ratio);
            } else {
                $newHeight = self::$maxHeight;
                $newWidth = round($newHeight * $ratio);
            }
        }
        
        return [$newWidth, $newHeight];
    }
    
    /**
     * Generate unique filename for AVIF image
     */
    private static function generateFilename() {
        $extension = function_exists('imageavif') ? '.avif' : '.webp';
        return 'product_' . uniqid() . '_' . time() . $extension;
    }
    
    /**
     * Delete image file
     */
    public static function deleteImage($filename) {
        if (empty($filename)) {
            return false;
        }
        
        $filepath = self::$uploadDir . $filename;
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        
        return false;
    }
    
    /**
     * Get image path
     */
    public static function getImagePath($filename) {
        return self::$uploadDir . $filename;
    }
    
    /**
     * Validate if file is an image
     */
    public static function isValidImage($file) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
        $fileInfo = getimagesize($file['tmp_name']);
        
        if ($fileInfo === false) {
            return false;
        }
        
        return in_array($fileInfo['mime'], $allowedTypes);
    }
}
