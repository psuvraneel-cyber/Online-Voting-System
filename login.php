<?php
include 'config/database.php';

$username = '';
$errors = [];

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if(empty($username) || empty($password)) {
        $errors[] = "Please enter both username and password";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['has_voted'] = $user['has_voted'];
            
            if($user['role'] == 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $errors[] = "Invalid username or password";
        }
    }
}

// Only include header.php AFTER processing the form and before outputting HTML
include 'includes/header.php';
?>

<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    
    .login-container {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px 0;
    }
    
    .login-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        width: 100%;
        max-width: 450px;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-align: center;
        padding: 30px 20px;
        border-bottom: none;
    }
    
    .card-body {
        padding: 40px;
    }
    
    .form-control {
        border-radius: 10px;
        padding: 12px 15px;
        border: 2px solid #e3e6f0;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .btn-login {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        border-radius: 10px;
        padding: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    
    .input-group-text {
        background: #f8f9fa;
        border: 2px solid #e3e6f0;
        border-right: none;
        border-radius: 10px 0 0 10px;
    }
    
    .form-control.with-icon {
        border-left: none;
        border-radius: 0 10px 10px 0;
    }
    
    .alert {
        border-radius: 10px;
        border: none;
    }
    
    .login-features {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 20px;
        margin-top: 20px;
    }
    
    .feature-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .feature-item i {
        color: #667eea;
        margin-right: 10px;
        width: 20px;
    }
</style>

<div class="login-container">
    <div class="login-card">
        <div class="card-header">
            <h3 class="mb-0"><i class="fas fa-vote-yea me-2"></i>Welcome Back</h3>
            <p class="mb-0 mt-2 opacity-75">Sign in to your voting account</p>
        </div>
        
        <div class="card-body">
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if(!empty($errors)): ?>
                <div class="alert alert-danger">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Login Failed</h6>
                    <?php foreach($errors as $error): ?>
                        <p class="mb-0">• <?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-4">
                    <label class="form-label fw-bold">Username or Email</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" class="form-control with-icon" id="username" name="username" 
                               value="<?php echo htmlspecialchars($username); ?>" 
                               placeholder="Enter your username or email" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control with-icon" id="password" name="password" 
                               placeholder="Enter your password" required>
                    </div>
                </div>
                
                <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-login btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                </div>
            </form>
            
            <div class="text-center">
                <p class="mb-0">Don't have an account? 
                    <a href="register.php" class="text-primary fw-bold text-decoration-none">Register here</a>
                </p>
            </div>
            
            <div class="login-features">
                <h6 class="text-center mb-3"><i class="fas fa-shield-alt me-2"></i>Secure Voting Features</h6>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>One vote per user</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Encrypted data protection</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Real-time results</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Admin monitoring</span>
                </div>
            </div>
            
            <!-- Demo Credentials -->
            <div class="mt-4 p-3 bg-light rounded">
                <h6 class="text-center mb-2"><i class="fas fa-info-circle me-2"></i>Demo Credentials</h6>
                <div class="row text-center">
                    <div class="col-6">
                        <small class="text-muted">Admin Account</small><br>
                        <strong>admin / password</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Voter Account</small><br>
                        <strong>Register new account</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add focus effects
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
    
    // Simple form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;
        
        if (!username || !password) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>