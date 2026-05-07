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
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $full_name = trim($_POST['full_name'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (empty($username) || empty($email) || empty($password)) {
            $error = 'Please fill in all required fields';
        } elseif (!validateEmail($email)) {
            $error = 'Please enter a valid email address';
        } elseif (!validateUsername($username)) {
            $error = 'Username must be 3-50 characters and contain only letters, numbers, and underscores';
        } elseif (!validatePassword($password)) {
            $error = 'Password must be at least 6 characters';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match';
        } elseif (isUsernameTaken($username)) {
            $error = 'Username is already taken';
        } elseif (isEmailTaken($email)) {
            $error = 'Email is already registered';
        } else {
            // Create user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $db = Database::getInstance();
            
            $sql = "INSERT INTO users (username, email, full_name, password, status) 
                    VALUES (?, ?, ?, ?, 0)";
            
            try {
                $userId = $db->insert($sql, [$username, $email, $full_name, $hashed_password]);
                logUserActivity($userId, 'User registered');
                $success = true;
            } catch (Exception $e) {
                $error = 'Registration failed. Please try again.';
            }
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
    <title>Register - NCSBN3 Nursing Examination</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .password-strength {
            margin-top: 5px;
            font-size: 12px;
        }
        .strength-weak { color: #ef4444; }
        .strength-medium { color: #f59e0b; }
        .strength-strong { color: #10b981; }
        
        .requirements-list {
            list-style: none;
            padding: 0;
            margin-top: 5px;
            font-size: 12px;
        }
        .requirements-list li {
            margin-bottom: 3px;
            color: #666;
        }
        .requirements-list li.valid {
            color: #10b981;
        }
        .requirements-list li.invalid {
            color: #ef4444;
        }
        .requirements-list li i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <i class="fas fa-user-plus" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                <h1>Create Account</h1>
                <p>Join NCSBN3 Nursing Examination Portal</p>
            </div>
            <div class="auth-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success === true): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Registration successful! 
                        <a href="login.php" style="color: #065f46; font-weight: 600;">Click here to login</a>
                    </div>
                <?php else: ?>
                <form method="POST" action="" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="form-group">
                        <label for="username">
                            <i class="fas fa-user"></i> Username *
                        </label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                               placeholder="Choose a username" required>
                        <small style="color: #666;">3-50 characters, letters, numbers, and underscores only</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Email Address *
                        </label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                               placeholder="your@email.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="full_name">
                            <i class="fas fa-id-card"></i> Full Name
                        </label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" 
                               placeholder="John Doe">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i> Password *
                        </label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Create a password" required>
                        <div id="passwordStrength" class="password-strength"></div>
                        <ul class="requirements-list" id="passwordRequirements">
                            <li id="req-length"><i class="fas fa-circle"></i> At least 6 characters</li>
                            <li id="req-lowercase"><i class="fas fa-circle"></i> At least one lowercase letter</li>
                            <li id="req-uppercase"><i class="fas fa-circle"></i> At least one uppercase letter</li>
                            <li id="req-number"><i class="fas fa-circle"></i> At least one number</li>
                        </ul>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">
                            <i class="fas fa-check-circle"></i> Confirm Password *
                        </label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                               placeholder="Confirm your password" required>
                        <div id="passwordMatch" class="password-strength"></div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </form>
                
                <div style="text-align: center; margin-top: 1.5rem;">
                    Already have an account? <a href="login.php" style="color: #667eea; font-weight: 600;">Login here</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="js/main.js"></script>
    <script>
        // Password strength checker with requirements
        const passwordInput = document.getElementById('password');
        const strengthDiv = document.getElementById('passwordStrength');
        
        const requirements = {
            length: document.getElementById('req-length'),
            lowercase: document.getElementById('req-lowercase'),
            uppercase: document.getElementById('req-uppercase'),
            number: document.getElementById('req-number')
        };
        
        function updateRequirements(password) {
            const checks = {
                length: password.length >= 6,
                lowercase: /[a-z]/.test(password),
                uppercase: /[A-Z]/.test(password),
                number: /[0-9]/.test(password)
            };
            
            for (const [key, element] of Object.entries(requirements)) {
                if (checks[key]) {
                    element.classList.add('valid');
                    element.classList.remove('invalid');
                    element.innerHTML = '<i class="fas fa-check-circle"></i> ' + element.innerHTML.split('>')[1];
                } else {
                    element.classList.add('invalid');
                    element.classList.remove('valid');
                    element.innerHTML = '<i class="fas fa-circle"></i> ' + element.innerHTML.split('>')[1];
                }
            }
            
            const strength = Object.values(checks).filter(Boolean).length;
            let strengthText = '';
            let strengthClass = '';
            
            if (password.length === 0) {
                strengthText = '';
            } else if (strength <= 2) {
                strengthText = 'Weak password';
                strengthClass = 'strength-weak';
            } else if (strength <= 3) {
                strengthText = 'Medium password';
                strengthClass = 'strength-medium';
            } else {
                strengthText = 'Strong password';
                strengthClass = 'strength-strong';
            }
            
            strengthDiv.textContent = strengthText;
            strengthDiv.className = 'password-strength ' + strengthClass;
        }
        
        passwordInput.addEventListener('input', function() {
            updateRequirements(this.value);
            checkPasswordMatch();
        });
        
        // Password match checker
        const confirmInput = document.getElementById('confirm_password');
        const matchDiv = document.getElementById('passwordMatch');
        
        function checkPasswordMatch() {
            if (confirmInput.value.length > 0) {
                if (passwordInput.value !== confirmInput.value) {
                    matchDiv.textContent = '✗ Passwords do not match';
                    matchDiv.className = 'password-strength strength-weak';
                } else {
                    matchDiv.textContent = '✓ Passwords match';
                    matchDiv.className = 'password-strength strength-strong';
                }
            } else {
                matchDiv.textContent = '';
            }
        }
        
        passwordInput.addEventListener('input', checkPasswordMatch);
        confirmInput.addEventListener('input', checkPasswordMatch);
    </script>
</body>
</html>