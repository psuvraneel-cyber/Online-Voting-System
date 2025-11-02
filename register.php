<?php
include 'config/database.php';

$username = $email = '';
$errors = [];

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if(empty($username)) $errors[] = "Username is required";
    if(empty($email)) $errors[] = "Email is required";
    if(empty($password)) $errors[] = "Password is required";
    if(strlen($password) < 6) $errors[] = "Password must be at least 6 characters";
    if($password !== $confirm_password) $errors[] = "Passwords do not match";
    
    // Validate email format
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    
    // Check if user exists only if no errors so far
    if(empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if($stmt->rowCount() > 0) {
            $errors[] = "Username or email already exists";
        }
    }
    
    if(empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, has_voted) VALUES (?, ?, ?, 'voter', 0)");
        
        if($stmt->execute([$username, $email, $hashed_password])) {
            session_start();
            $_SESSION['success'] = "Registration successful! Please login.";
            header("Location: login.php");
            exit();
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
    }
}

// Include header AFTER processing form to avoid header errors
include 'includes/header.php';
?>

<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    
    .register-container {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px 0;
    }
    
    .register-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        width: 100%;
        max-width: 500px;
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
    
    .btn-register {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        border-radius: 10px;
        padding: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-register:hover {
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
    
    .register-features {
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
    
    .password-strength {
        height: 5px;
        border-radius: 5px;
        margin-top: 5px;
        background: #e9ecef;
        overflow: hidden;
    }
    
    .strength-bar {
        height: 100%;
        width: 0%;
        transition: all 0.3s ease;
        border-radius: 5px;
    }
    
    .strength-weak { background: #dc3545; width: 25%; }
    .strength-fair { background: #fd7e14; width: 50%; }
    .strength-good { background: #ffc107; width: 75%; }
    .strength-strong { background: #28a745; width: 100%; }
    
    .password-requirements {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 5px;
    }
</style>

<div class="register-container">
    <div class="register-card">
        <div class="card-header">
            <h3 class="mb-0"><i class="fas fa-user-plus me-2"></i>Join Our Voting Community</h3>
            <p class="mb-0 mt-2 opacity-75">Create your account to participate in elections</p>
        </div>
        
        <div class="card-body">
            <?php if(!empty($errors)): ?>
                <div class="alert alert-danger">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Registration Failed</h6>
                    <?php foreach($errors as $error): ?>
                        <p class="mb-0">• <?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="registerForm">
                <div class="mb-4">
                    <label class="form-label fw-bold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" class="form-control with-icon" id="username" name="username" 
                               value="<?php echo htmlspecialchars($username); ?>" 
                               placeholder="Choose a username" required>
                    </div>
                    <small class="text-muted">This will be your public display name</small>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" class="form-control with-icon" id="email" name="email" 
                               value="<?php echo htmlspecialchars($email); ?>" 
                               placeholder="Enter your email address" required>
                    </div>
                    <small class="text-muted">We'll never share your email with anyone else</small>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control with-icon" id="password" name="password" 
                               placeholder="Create a strong password" required>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar" id="passwordStrength"></div>
                    </div>
                    <div class="password-requirements">
                        <i class="fas fa-info-circle me-1"></i>
                        Must be at least 6 characters
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control with-icon" id="confirm_password" name="confirm_password" 
                               placeholder="Confirm your password" required>
                    </div>
                    <div id="passwordMatch" class="password-requirements"></div>
                </div>
                
                <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-register btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </button>
                </div>
            </form>
            
            <div class="text-center">
                <p class="mb-0">Already have an account? 
                    <a href="login.php" class="text-primary fw-bold text-decoration-none">Sign in here</a>
                </p>
            </div>
            
            <div class="register-features">
                <h6 class="text-center mb-3"><i class="fas fa-shield-alt me-2"></i>Why Register With Us?</h6>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Participate in secure online voting</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>One secure vote per account</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>View real-time election results</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Your data is encrypted and protected</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Easy and intuitive voting process</span>
                </div>
            </div>
            
            <!-- Voting Information -->
            <div class="mt-4 p-3 bg-light rounded">
                <h6 class="text-center mb-2"><i class="fas fa-info-circle me-2"></i>About Voting</h6>
                <p class="small text-muted text-center mb-0">
                    Once registered, you can participate in all ongoing elections. 
                    Each user gets one vote per position. Voting is anonymous and secure.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const passwordStrength = document.getElementById('passwordStrength');
    const passwordMatch = document.getElementById('passwordMatch');
    const form = document.getElementById('registerForm');
    
    // Password strength indicator
    password.addEventListener('input', function() {
        const strength = calculatePasswordStrength(this.value);
        updatePasswordStrength(strength);
    });
    
    // Password confirmation check
    confirmPassword.addEventListener('input', function() {
        checkPasswordMatch();
    });
    
    function calculatePasswordStrength(password) {
        let strength = 0;
        
        // Length check
        if (password.length >= 6) strength += 1;
        if (password.length >= 8) strength += 1;
        
        // Character variety checks
        if (/[a-z]/.test(password)) strength += 1;
        if (/[A-Z]/.test(password)) strength += 1;
        if (/[0-9]/.test(password)) strength += 1;
        if (/[^a-zA-Z0-9]/.test(password)) strength += 1;
        
        return Math.min(strength, 4); // Max strength of 4
    }
    
    function updatePasswordStrength(strength) {
        passwordStrength.className = 'strength-bar';
        
        switch(strength) {
            case 0:
                passwordStrength.classList.add('strength-weak');
                break;
            case 1:
            case 2:
                passwordStrength.classList.add('strength-fair');
                break;
            case 3:
                passwordStrength.classList.add('strength-good');
                break;
            case 4:
                passwordStrength.classList.add('strength-strong');
                break;
        }
    }
    
    function checkPasswordMatch() {
        if (confirmPassword.value === '') {
            passwordMatch.textContent = '';
            passwordMatch.className = 'password-requirements';
        } else if (password.value === confirmPassword.value) {
            passwordMatch.textContent = '✓ Passwords match';
            passwordMatch.className = 'password-requirements text-success';
        } else {
            passwordMatch.textContent = '✗ Passwords do not match';
            passwordMatch.className = 'password-requirements text-danger';
        }
    }
    
    // Form validation
    form.addEventListener('submit', function(e) {
        const username = document.getElementById('username').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        let errors = [];
        
        if (!username) errors.push('Username is required');
        if (!email) errors.push('Email is required');
        if (!password) errors.push('Password is required');
        if (password.length < 6) errors.push('Password must be at least 6 characters');
        if (password !== confirmPassword) errors.push('Passwords do not match');
        
        if (errors.length > 0) {
            e.preventDefault();
            alert('Please fix the following errors:\n\n• ' + errors.join('\n• '));
        }
    });
    
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
});
</script>

<?php include 'includes/footer.php'; ?>