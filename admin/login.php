<?php
require_once 'pharma_config.php';
require_once 'db.php';
require_once 'SecureSession.php';
require_once 'InputValidator.php';

SecureSession::init();

if (SecureSession::isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$loginAttempts = $_SESSION['login_attempts'] ?? 0;
$lastAttemptTime = $_SESSION['last_attempt_time'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($loginAttempts >= MAX_LOGIN_ATTEMPTS) {
        $timeSinceLastAttempt = time() - $lastAttemptTime;
        if ($timeSinceLastAttempt < LOGIN_LOCKOUT_TIME) {
            $remainingTime = ceil((LOGIN_LOCKOUT_TIME - $timeSinceLastAttempt) / 60);
            $error = "Too many attempts. Try again in $remainingTime min.";
        } else {
            $_SESSION['login_attempts'] = 0;
            $loginAttempts = 0;
        }
    }

    if (empty($error)) {
        if (!isset($_POST['csrf_token']) || !SecureSession::validateCSRFToken($_POST['csrf_token'])) {
            $error = "Security token expired. Please refresh.";
        } else {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            // --- DEBUG LOGIC START ---
            try {
                $db = getDB();
                $stmt = $db->prepare("SELECT id, username, password FROM user WHERE username = ? LIMIT 1");
                $stmt->execute([$username]);
                $user = $stmt->fetch();

                if (!$user) {
                    $error = "DEBUG: Username '$username' not found in database.";
                } else {
                    if (password_verify($password, $user['password'])) {
                        $_SESSION['login_attempts'] = 0;
                        SecureSession::login($user['username']);
                        header('Location: dashboard.php');
                        exit();
                    } else {
                        // This helps us see if the hash in DB is actually what we expect
                        $db_hash_start = substr($user['password'], 0, 10);
                        $error = "DEBUG: Password verify failed. DB hash starts with: $db_hash_start";
                    }
                }
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
            // --- DEBUG LOGIC END ---
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Urban Nest Admin</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
        }
        .login-header { text-align: center; margin-bottom: 30px; }
        .login-header h1 { color: #333; font-size: 28px; margin-bottom: 10px; }
        .alert { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Urban Nest Admin</h1>
            <p class="text-muted">Secure Login Portal</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <?php echo SecureSession::getCSRFField(); ?>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted">
                Login attempts: <?php echo $_SESSION['login_attempts'] ?? 0; ?> / <?php echo MAX_LOGIN_ATTEMPTS; ?>
            </small>
        </div>
    </div>
</body>
</html>