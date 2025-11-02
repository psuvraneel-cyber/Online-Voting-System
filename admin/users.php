<?php
include '../includes/header.php';
include '../includes/auth_check.php';
include '../config/database.php';

// Check if user is admin
if($_SESSION['role'] != 'admin') {
    header("Location: ../dashboard.php");
    exit();
}

$message = '';
$errors = [];

// Delete user
if(isset($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];
    
    // Prevent admin from deleting themselves
    if($user_id == $_SESSION['user_id']) {
        $errors[] = "You cannot delete your own account!";
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        if($stmt->execute([$user_id])) {
            $message = "User deleted successfully!";
        } else {
            $errors[] = "Failed to delete user";
        }
    }
}

// Get all users
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-users-cog me-2"></i>Manage Users</h5>
            <span class="badge bg-light text-dark"><?php echo count($users); ?> Users</span>
        </div>
        <div class="card-body">
            <?php if(!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach($errors as $error): ?>
                        <p class="mb-0"><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if($message): ?>
                <div class="alert alert-success">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Voting Status</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td>
                                    <?php echo htmlspecialchars($user['username']); ?>
                                    <?php if($user['id'] == $_SESSION['user_id']): ?>
                                        <span class="badge bg-info">You</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge <?php echo $user['role'] == 'admin' ? 'bg-danger' : 'bg-secondary'; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $user['has_voted'] ? 'bg-success' : 'bg-warning'; ?>">
                                        <?php echo $user['has_voted'] ? 'Voted' : 'Not Voted'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y g:i A', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php if($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="?delete=<?php echo $user['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Are you sure you want to delete user <?php echo addslashes($user['username']); ?>? This action cannot be undone.')"
                                           title="Delete User">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Current User</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if(empty($users)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No users found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.table th {
    border-top: none;
    font-weight: 600;
}
</style>

<?php include '../includes/footer.php'; ?>