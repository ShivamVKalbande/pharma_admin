<?php
/**
 * Secure Session Management Class
 * Handles authentication, CSRF tokens, and session security
 */
class SecureSession {
    
    public static function init() {
        // Configure secure session settings
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        
        // Note: 'cookie_secure' should be 0 for localhost/HTTP and 1 for HTTPS
        ini_set('session.cookie_secure', 0); 
        ini_set('session.cookie_samesite', 'Strict');
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if constant exists, otherwise default to 30 mins
        $lifetime = defined('SESSION_LIFETIME') ? SESSION_LIFETIME : 1800;

        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > $lifetime) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
        
        self::validateFingerprint();
    }
    
    private static function validateFingerprint() {
        $fingerprint = md5(($_SERVER['HTTP_USER_AGENT'] ?? '') . ($_SERVER['REMOTE_ADDR'] ?? ''));
        
        if (!isset($_SESSION['fingerprint'])) {
            $_SESSION['fingerprint'] = $fingerprint;
        } else if ($_SESSION['fingerprint'] !== $fingerprint) {
            self::destroy();
            header('Location: login.php?error=session_hijack');
            exit();
        }
    }
    
    public static function login($username) {
        session_regenerate_id(true);
        $_SESSION['user'] = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
        // Calls the private database helper
        $_SESSION['user_id'] = self::fetchUserIdFromDb($username);
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['fingerprint'] = md5(($_SERVER['HTTP_USER_AGENT'] ?? '') . ($_SERVER['REMOTE_ADDR'] ?? ''));
    }
    
    // RENAMED to avoid conflict
    private static function fetchUserIdFromDb($username) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id FROM user WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        return $user ? $user['id'] : null;
    }
    
    public static function isLoggedIn() {
        if (!isset($_SESSION['user']) || !isset($_SESSION['last_activity'])) {
            return false;
        }
        
        $lifetime = defined('SESSION_LIFETIME') ? SESSION_LIFETIME : 1800;
        
        if (time() - $_SESSION['last_activity'] > $lifetime) {
            self::destroy();
            return false;
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: login.php?error=auth_required');
            exit();
        }
    }
    
    public static function destroy() {
        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        session_destroy();
    }
    
    public static function generateCSRFToken() {
        $expiry = defined('CSRF_TOKEN_EXPIRY') ? CSRF_TOKEN_EXPIRY : 3600;

        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time']) || 
            (time() - $_SESSION['csrf_token_time'] > $expiry)) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function validateCSRFToken($token) {
        $expiry = defined('CSRF_TOKEN_EXPIRY') ? CSRF_TOKEN_EXPIRY : 3600;

        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }
        
        if (time() - $_SESSION['csrf_token_time'] > $expiry) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function getCSRFField() {
        $token = self::generateCSRFToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    public static function getUsername() {
        return $_SESSION['user'] ?? null;
    }
    
    // THIS IS NOW UNIQUE
    public static function getSessionUserId() {
        return $_SESSION['user_id'] ?? null;
    }
}
?>