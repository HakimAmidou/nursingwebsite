<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

session_start();

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username/email and password';
    } else {
        $db = Database::getInstance();
        $sql = "SELECT id, username, email, full_name, password, status FROM users WHERE username = ? OR email = ?";
        $user = $db->getOne($sql, [$username, $username]);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['status'] = $user['status'];
            
            // Update last login
            $db->query("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);
            logUserActivity($user['id'], 'User logged in');
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username/email or password';
            logUserActivity(null, "Failed login attempt for: $username");
        }
    }
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NCSBN3 Nursing Examination</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <i class="fas fa-stethoscope" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                <h1>Welcome Back</h1>
                <p>Login to NCSBN3 Nursing Exam Portal</p>
            </div>
            <div class="auth-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="form-group">
                        <label for="username">
                            <i class="fas fa-user"></i> Username or Email
                        </label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                               placeholder="Enter your username or email" required autofocus>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter your password" required>
                    </div>
                    
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="remember"> 
                            <span><i class="fas fa-memory"></i> Remember Me</span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
                
                <div style="text-align: center; margin-top: 1.5rem;">
                    <p>Don't have an account? <a href="register.php" style="color: #667eea; font-weight: 600;">Register here</a></p>
                    <p style="margin-top: 1rem; font-size: 0.875rem;">
                        <a href="#" style="color: #666;">Forgot Password?</a>
                    </p>
                </div>
                
                <hr style="margin: 1.5rem 0; border-color: #e5e7eb;">
                
                <div style="text-align: center;">
                    <p style="color: #666; font-size: 0.875rem;">Demo Credentials:</p>
                    <div style="display: flex; gap: 1rem; justify-content: center; font-size: 0.75rem; color: #999; flex-wrap: wrap;">
                        <div><strong>Passed:</strong> john_doe / password123</div>
                        <div><strong>Not Passed:</strong> jane_smith / password123</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/main.js"></script>
</body>
</html>