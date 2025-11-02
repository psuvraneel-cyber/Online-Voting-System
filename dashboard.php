<?php
include 'includes/header.php';
include 'includes/auth_check.php';
include 'config/database.php';

// Get user info
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get positions
$positions_stmt = $pdo->query("SELECT DISTINCT position FROM candidates");
$positions = $positions_stmt->fetchAll();
?>
<style>
    /* Global Styles */
body {
    font-family: 'Roboto', sans-serif;
    background-color: #f4f6f9;
    color: #333;
    margin: 0;
    padding: 0;
}

.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-10px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.card-header {
    background-color: #007bff;
    color: #fff;
    border-radius: 8px 8px 0 0;
    padding: 15px;
    font-size: 1.25rem;
}

.card-body {
    padding: 20px;
}

/* User Information */
.card .badge {
    font-size: 0.875rem;
    padding: 0.4em 0.8em;
    border-radius: 50px;
    text-transform: uppercase;
}

.badge.bg-success {
    background-color: #28a745;
    color: white;
}

.badge.bg-warning {
    background-color: #ffc107;
    color: white;
}

.alert {
    padding: 15px;
    border-radius: 8px;
    font-size: 1rem;
    margin-bottom: 20px;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
}

/* Buttons */
.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
    padding: 10px 20px;
    font-size: 1rem;
    border-radius: 25px;
    transition: background-color 0.3s ease;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.btn-outline-primary {
    color: #007bff;
    border-color: #007bff;
    padding: 10px 20px;
    font-size: 1rem;
    border-radius: 25px;
}

.btn-outline-primary:hover {
    background-color: #007bff;
    color: white;
}

ul {
    list-style-type: none;
    padding: 0;
}

ul li {
    padding: 8px 0;
    font-size: 1rem;
    color: #555;
}

@media (max-width: 768px) {
    .col-md-4, .col-md-8 {
        width: 100%;
        margin-bottom: 20px;
    }
}


</style>
<div class="container">
    <div class="row">
        <!-- User Information Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>User Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
                    <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
                    <p><strong>Voting Status:</strong> 
                        <?php if($user['has_voted']): ?>
                            <span class="badge bg-success">Voted</span>
                        <?php else: ?>
                            <span class="badge bg-warning">Not Voted</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Election Information Card -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Election Information</h5>
                </div>
                <div class="card-body">
                    <?php if($user['has_voted']): ?>
                        <div class="alert alert-success">
                            <h6>Thank you for voting!</h6>
                            <p>You have successfully cast your vote in this election.</p>
                            <a href="results.php" class="btn btn-outline-primary">View Results</a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <h6>Election is Open</h6>
                            <p>You haven't voted yet. Please cast your vote before the deadline.</p>
                            <a href="vote.php" class="btn btn-primary">Vote Now</a>
                        </div>
                    <?php endif; ?>
                    
                    <h6>Available Positions:</h6>
                    <ul>
                        <?php foreach($positions as $position): ?>
                            <li><?php echo $position['position']; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
