<?php
include '../includes/header.php';
include '../includes/auth_check.php';
include '../config/database.php';

// Check if user is admin
if($_SESSION['role'] != 'admin') {
    header("Location: ../dashboard.php");
    exit();
}

// Get statistics
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_voters = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'voter'")->fetchColumn();
$voted_users = $pdo->query("SELECT COUNT(*) FROM users WHERE has_voted = TRUE")->fetchColumn();
$total_candidates = $pdo->query("SELECT COUNT(*) FROM candidates")->fetchColumn();
$total_votes = $pdo->query("SELECT SUM(votes) FROM candidates")->fetchColumn();
$positions_count = $pdo->query("SELECT COUNT(DISTINCT position) FROM candidates")->fetchColumn();

// Get recent votes
$recent_votes = $pdo->query("
    SELECT u.username, c.name as candidate_name, c.position, v.voted_at 
    FROM votes v 
    JOIN users u ON v.voter_id = u.id 
    JOIN candidates c ON v.candidate_id = c.id 
    ORDER BY v.voted_at DESC 
    LIMIT 5
")->fetchAll();

// Get vote distribution by position
$vote_distribution = $pdo->query("
    SELECT position, SUM(votes) as total_votes 
    FROM candidates 
    GROUP BY position 
    ORDER BY total_votes DESC
")->fetchAll();
?>

<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_users; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Voted Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $voted_users; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-vote-yea fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Candidates</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_candidates; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Votes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_votes ?: 0; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quick Actions -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="candidates.php" class="btn btn-success btn-block">
                            <i class="fas fa-user-plus me-2"></i>Manage Candidates
                        </a>
                        <a href="../results.php" class="btn btn-info btn-block">
                            <i class="fas fa-chart-pie me-2"></i>View Results
                        </a>
                        <a href="users.php" class="btn btn-warning btn-block">
                            <i class="fas fa-users-cog me-2"></i>Manage Users
                        </a>
                        <a href="../logout.php" class="btn btn-danger btn-block">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </div>
                </div>
            </div>

            <!-- Voting Statistics -->
            <div class="card shadow mt-4">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold">Voting Statistics</h6>
                </div>
                <div class="card-body">
                    <?php 
                    $voter_turnout = $total_voters > 0 ? round(($voted_users / $total_voters) * 100, 2) : 0;
                    ?>
                    <p>Voter Turnout: <strong><?php echo $voter_turnout; ?>%</strong></p>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: <?php echo $voter_turnout; ?>%"
                             aria-valuenow="<?php echo $voter_turnout; ?>" 
                             aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    
                    <p>Positions: <strong><?php echo $positions_count; ?></strong></p>
                    <p>Average Votes per Candidate: <strong><?php echo $total_candidates > 0 ? round($total_votes / $total_candidates, 2) : 0; ?></strong></p>
                </div>
            </div>
        </div>

        <!-- Recent Votes -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Recent Votes</h6>
                    <span class="badge bg-light text-dark">Latest 5</span>
                </div>
                <div class="card-body">
                    <?php if(empty($recent_votes)): ?>
                        <p class="text-muted">No votes cast yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Voter</th>
                                        <th>Candidate</th>
                                        <th>Position</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recent_votes as $vote): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($vote['username']); ?></td>
                                            <td><?php echo htmlspecialchars($vote['candidate_name']); ?></td>
                                            <td><?php echo htmlspecialchars($vote['position']); ?></td>
                                            <td><?php echo date('M j, g:i A', strtotime($vote['voted_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Vote Distribution -->
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h6 class="m-0 font-weight-bold">Vote Distribution by Position</h6>
                </div>
                <div class="card-body">
                    <?php if(empty($vote_distribution)): ?>
                        <p class="text-muted">No votes recorded yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Position</th>
                                        <th>Total Votes</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($vote_distribution as $distribution): ?>
                                        <?php
                                        $percentage = $total_votes > 0 ? round(($distribution['total_votes'] / $total_votes) * 100, 2) : 0;
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($distribution['position']); ?></td>
                                            <td><?php echo $distribution['total_votes']; ?></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar bg-info" role="progressbar" 
                                                         style="width: <?php echo $percentage; ?>%"
                                                         aria-valuenow="<?php echo $percentage; ?>" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        <?php echo $percentage; ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 10px;
}
.border-left-primary {
    border-left: 4px solid #4e73df !important;
}
.border-left-success {
    border-left: 4px solid #1cc88a !important;
}
.border-left-info {
    border-left: 4px solid #36b9cc !important;
}
.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}
</style>

<?php include '../includes/footer.php'; ?>