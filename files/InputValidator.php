<?php
/**
 * Input Validation and Sanitization Class
 * Provides secure input handling and validation
 */
class InputValidator {
    
    /**
     * Sanitize string input
     */
    public static function sanitizeString($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitize email
     */
    public static function sanitizeEmail($email) {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * Validate email
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Sanitize integer
     */
    public static function sanitizeInt($input) {
        return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
    }
    
    /**
     * Validate integer
     */
    public static function validateInt($input, $min = null, $max = null) {
        $options = ['options' => []];
        if ($min !== null) $options['options']['min_range'] = $min;
        if ($max !== null) $options['options']['max_range'] = $max;
        
        return filter_var($input, FILTER_VALIDATE_INT, $options) !== false;
    }
    
    /**
     * Sanitize float
     */
    public static function sanitizeFloat($input) {
        return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
    
    /**
     * Validate float
     */
    public static function validateFloat($input) {
        return filter_var($input, FILTER_VALIDATE_FLOAT) !== false;
    }
    
    /**
     * Validate phone number (basic)
     */
    public static function validatePhone($phone) {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        // Check if it's 10 digits
        return strlen($phone) >= 10 && strlen($phone) <= 15;
    }
    
    /**
     * Validate required fields
     */
    public static function validateRequired($fields, $data) {
        $errors = [];
        foreach ($fields as $field => $label) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[] = "$label is required";
            }
        }
        return $errors;
    }
    
    /**
     * Validate string length
     */
    public static function validateLength($input, $min = 0, $max = PHP_INT_MAX, $fieldName = 'Field') {
        $length = strlen($input);
        if ($length < $min) {
            return "$fieldName must be at least $min characters";
        }
        if ($length > $max) {
            return "$fieldName must not exceed $max characters";
        }
        return true;
    }
    
    /**
     * Sanitize filename
     */
    public static function sanitizeFilename($filename) {
        // Remove any path information
        $filename = basename($filename);
        // Remove special characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        return $filename;
    }
    
    /**
     * Validate URL
     */
    public static function validateURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Prevent XSS attacks
     */
    public static function escapeOutput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'escapeOutput'], $data);
        }
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * Validate tenant type
     */
    public static function validateTenantType($type) {
        $validTypes = ['rent', 'sale', 'lease'];
        return in_array(strtolower($type), $validTypes);
    }
    
    /**
     * Validate city name
     */
    public static function validateCity($city) {
        // Only allow letters, spaces, hyphens
        return preg_match('/^[a-zA-Z\s-]+$/', $city);
    }
}
?>
