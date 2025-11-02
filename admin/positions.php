<?php
include '../includes/header.php';
include '../includes/auth_check.php';

// Check if user is admin
if($_SESSION['role'] != 'admin') {
    header("Location: ../dashboard.php");
    exit();
}

include '../config/database.php';

// Handle form submission to update positions
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_positions'])) {
    $old_position = $_POST['old_position'];
    $new_position = $_POST['new_position'];
    
    if(!empty($new_position)) {
        $stmt = $pdo->prepare("UPDATE candidates SET position = ? WHERE position = ?");
        $stmt->execute([$new_position, $old_position]);
        
        $stmt = $pdo->prepare("UPDATE votes SET position = ? WHERE position = ?");
        $stmt->execute([$new_position, $old_position]);
        
        $_SESSION['success'] = "Position updated successfully!";
    }
}

// Get all distinct positions
$positions_stmt = $pdo->query("SELECT DISTINCT position FROM candidates ORDER BY position");
$positions = $positions_stmt->fetchAll();
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4>Manage Positions</h4>
                </div>
                <div class="card-body">
                    <?php if(isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Current Position</th>
                                    <th>New Position Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($positions as $position): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($position['position']); ?></td>
                                    <td>
                                        <form method="POST" class="d-flex">
                                            <input type="hidden" name="old_position" value="<?php echo htmlspecialchars($position['position']); ?>">
                                            <input type="text" name="new_position" class="form-control me-2" 
                                                   value="<?php echo htmlspecialchars($position['position']); ?>" 
                                                   placeholder="Enter new name">
                                    </td>
                                    <td>
                                            <button type="submit" name="update_positions" class="btn btn-primary btn-sm">
                                                Update
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>