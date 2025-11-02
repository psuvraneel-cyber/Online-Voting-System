<?php
// reset_admin.php - Run this once to reset admin password
include 'config/database.php';

try {
    $new_password = 'admin123'; // Change this to your desired password
    
    // Reset admin password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
    $stmt->execute([$hashed_password]);
    
    echo "<h3 style='color: green;'>✅ Admin Password Reset Successfully!</h3>";
    echo "<p><strong>New Login Details:</strong></p>";
    echo "<p>Username: <strong>admin</strong></p>";
    echo "<p>Password: <strong>{$new_password}</strong></p>";
    echo "<br>";
    echo "<a href='login.php' class='btn btn-primary'>Go to Login</a>";
    echo "&nbsp;";
    echo "<a href='admin/index.php' class='btn btn-success'>Go to Admin Panel</a>";
    
    // Security warning
    echo "<br><br>";
    echo "<div class='alert alert-warning'>";
    echo "<strong>⚠️ Security Notice:</strong> Delete this file after use!";
    echo "</div>";
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>