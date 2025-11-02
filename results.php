<?php
include 'includes/header.php';
include 'config/database.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get positions and candidates with vote counts
$positions_stmt = $pdo->query("SELECT DISTINCT position FROM candidates");
$positions = $positions_stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <h4 class="text-center">Election Results</h4>
    </div>
    <div class="card-body">
        <?php foreach($positions as $position): ?>
            <?php
            $pos = $position['position'];
            $candidates_stmt = $pdo->prepare("
                SELECT *, 
                (SELECT COUNT(*) FROM votes WHERE candidate_id = candidates.id) as vote_count
                FROM candidates 
                WHERE position = ? 
                ORDER BY vote_count DESC
            ");
            $candidates_stmt->execute([$pos]);
            $candidates = $candidates_stmt->fetchAll();
            
            // Calculate total votes for this position
            $total_votes = 0;
            foreach($candidates as $candidate) {
                $total_votes += $candidate['vote_count'];
            }
            ?>
            
            <div class="mb-5">
                <h5><?php echo $pos; ?></h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Candidate</th>
                                <th>Votes</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($candidates as $candidate): ?>
                                <?php
                                $percentage = $total_votes > 0 ? round(($candidate['vote_count'] / $total_votes) * 100, 2) : 0;
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php echo $candidate['name']; ?></strong>
                                        <?php if($candidate['vote_count'] == max(array_column($candidates, 'vote_count')) && $candidate['vote_count'] > 0): ?>
                                            <span class="badge bg-success ms-2">Leading</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $candidate['vote_count']; ?></td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" 
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
                <p class="text-muted">Total Votes: <?php echo $total_votes; ?></p>
            </div>
            <hr>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>