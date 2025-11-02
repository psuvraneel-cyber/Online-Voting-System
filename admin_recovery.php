<?php
// admin_recovery.php - Admin password recovery with form
include 'config/database.php';

$message = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $new_password = trim($_POST['new_password']);
    
    if(empty($new_password)) {
        $error = "Please enter a new password";
    } elseif(strlen($new_password) < 4) {
        $error = "Password must be at least 4 characters";
    } else {
        try {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            if($action == 'reset') {
                // Reset existing admin password
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
                $stmt->execute([$hashed_password]);
                $message = "✅ Admin password reset successfully!";
            } else {
                // Create new admin
                $new_username = trim($_POST['new_username']);
                $new_email = trim($_POST['new_email']);
                
                if(empty($new_username) || empty($new_email)) {
                    $error = "Username and email are required for new admin";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
                    $stmt->execute([$new_username, $new_email, $hashed_password]);
                    $message = "✅ New admin user created successfully!";
                }
            }
        } catch(PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Recovery - Voting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .recovery-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="recovery-card p-4">
        <h2 class="text-center mb-4">🔐 Admin Account Recovery</h2>
        
        <?php if($message): ?>
            <div class="alert alert-success">
                <?php echo $message; ?>
                <?php if(isset($new_password)): ?>
                    <hr>
                    <p><strong>New Password:</strong> <?php echo htmlspecialchars($new_password); ?></p>
                    <a href="login.php" class="btn btn-success">Go to Login</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5>Reset Existing Admin</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="reset">
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="text" name="new_password" class="form-control" 
                                       placeholder="Enter new password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                Reset Admin Password
                            </button>
                        </form>
                        <small class="text-muted">
                            This will reset the password for the existing 'admin' user.
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5>Create New Admin</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="create">
                            <div class="mb-2">
                                <label class="form-label">Username</label>
                                <input type="text" name="new_username" class="form-control" 
                                       placeholder="New username" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Email</label>
                                <input type="email" name="new_email" class="form-control" 
                                       placeholder="New email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="text" name="new_password" class="form-control" 
                                       placeholder="New password" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                Create New Admin
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-warning mt-3">
            <strong>⚠️ Security Notice:</strong> 
            <ul class="mb-0 mt-2">
                <li>Delete this file after use</li>
                <li>Change passwords regularly</li>
                <li>Keep admin credentials secure</li>
            </ul>
        </div>
    </div>
</body>
</html>