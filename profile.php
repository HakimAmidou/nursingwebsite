<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
requireLogin();

$userId = $_SESSION['user_id'];
$user = getUserById($userId);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if ($email !== $user['email'] && isEmailTaken($email, $userId)) {
        $message = '<div class="alert alert-danger">Email is already taken</div>';
    } else {
        $db = Database::getInstance();
        $sql = "UPDATE users SET full_name = ?, email = ? WHERE id = ?";
        $db->query($sql, [$full_name, $email, $userId]);
        $message = '<div class="alert alert-success">Profile updated successfully!</div>';
        $user = getUserById($userId);
        $_SESSION['full_name'] = $full_name;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - NCSBN3</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="dashboard.php" class="navbar-logo">📚 NCSBN3 Nursing Exam</a>
            <div class="navbar-menu">
                <a href="dashboard.php">Dashboard</a>
                <a href="profile.php">Profile</a>
                <span>👤 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="dashboard-container">
        <div class="welcome-card">
            <h1>My Profile</h1>
            <p>View and edit your profile information</p>
        </div>
        
        <?php echo $message; ?>
        
        <div style="background: white; border-radius: 0.75rem; padding: 2rem; max-width: 600px; margin: 0 auto;">
            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                    <small>Username cannot be changed</small>
                </div>
                
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Status</label>
                    <div><?php echo displayStatusBadge($user['status']); ?></div>
                </div>
                
                <div class="form-group">
                    <label>Member Since</label>
                    <div><?php echo formatDate($user['created_at']); ?></div>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Profile</button>
                <a href="dashboard.php" class="btn" style="background: #6b7280; color: white; margin-left: 0.5rem;">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>