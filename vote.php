<?php
include 'config/database.php';
include 'includes/auth_check.php';

$user_id = $_SESSION['user_id'];

// Check if user has already voted
$user_stmt = $pdo->prepare("SELECT has_voted FROM users WHERE id = ?");
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch();

if($user['has_voted']) {
    $_SESSION['error'] = "You have already cast your vote!";
    header("Location: dashboard.php");
    exit();
}

// Get positions and candidates
$positions_stmt = $pdo->query("SELECT DISTINCT position FROM candidates ORDER BY position");
$positions = $positions_stmt->fetchAll();

$errors = [];

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $votes = $_POST['vote'];
    
    // Validation: Ensure that one candidate is selected per position
    foreach ($positions as $position) {
        $pos = $position['position'];
        if (!isset($votes[$pos]) || empty($votes[$pos])) {
            $errors[] = "Please select a candidate for " . $pos;
        }
    }

    if (empty($errors)) {
        $pdo->beginTransaction();
        try {
            foreach ($votes as $position => $candidate_ids) {
                $stmt = $pdo->prepare("INSERT INTO votes (voter_id, candidate_id, position) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $candidate_ids, $position]);

                // Update candidate vote count
                $update_stmt = $pdo->prepare("UPDATE candidates SET votes = votes + 1 WHERE id = ?");
                $update_stmt->execute([$candidate_ids]);
            }

            // Mark user as voted
            $update_user = $pdo->prepare("UPDATE users SET has_voted = TRUE WHERE id = ?");
            $update_user->execute([$user_id]);

            $pdo->commit();
            $_SESSION['has_voted'] = true;
            $_SESSION['success'] = "Your vote has been cast successfully!";
            header("Location: dashboard.php");
            exit();

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "An error occurred while processing your vote. Please try again.";
        }
    }
}

// Get all candidates grouped by position
$all_candidates = [];
foreach($positions as $position) {
    $pos = $position['position'];
    $candidates_stmt = $pdo->prepare("SELECT * FROM candidates WHERE position = ? ORDER BY name");
    $candidates_stmt->execute([$pos]);
    $all_candidates[$pos] = $candidates_stmt->fetchAll();
}

// Include header AFTER all processing and redirects
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cast Your Vote - Online Voting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .vote-container {
            margin-top: 30px;
            margin-bottom: 50px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .position-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border-left: 5px solid var(--secondary);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .position-title {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--light);
        }

        .candidate-card {
            border: 2px solid #e3e6f0;
            border-radius: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
            background: white;
            height: 100%;
            margin-bottom: 20px;
        }

        .candidate-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-color: var(--secondary);
        }

        .candidate-card.selected {
            border-color: var(--secondary);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .candidate-card.selected .text-muted {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        .candidate-photo {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #e3e6f0;
            margin: 0 auto 15px;
            display: block;
            transition: all 0.3s ease;
        }

        .candidate-card.selected .candidate-photo {
            border-color: white;
            transform: scale(1.05);
        }

        .candidate-name {
            font-weight: 600;
            margin-bottom: 8px;
            text-align: center;
            font-size: 1.1em;
        }

        .candidate-bio {
            text-align: center;
            font-size: 0.9em;
            line-height: 1.4;
            min-height: 40px;
        }

        .btn-vote {
            background: linear-gradient(45deg, #e74c3c, #e67e22);
            border: none;
            color: white;
            border-radius: 25px;
            font-weight: 600;
            padding: 15px 50px;
            font-size: 1.2em;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }

        .btn-vote:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
        }

        .vote-progress {
            background: var(--light);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .progress-step {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #bdc3c7;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }

        .step-number.active {
            background: var(--secondary);
        }

        @media (max-width: 768px) {
            .candidate-card {
                margin-bottom: 15px;
            }

            .candidate-photo {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>
<body>
    <!-- Removed duplicate header include -->

    <div class="vote-container container">
        <div class="glass-card p-4">
            <!-- Header -->
            <div class="text-center mb-4">
                <h1 class="display-5 fw-bold text-dark mb-2">Cast Your Vote</h1>
                <p class="lead text-muted">Select your preferred candidate for each position</p>
                
                <!-- Progress Indicator -->
                <div class="vote-progress">
                    <div class="progress-step">
                        <div class="step-number active">1</div>
                        <div>Select candidates for each position</div>
                    </div>
                    <div class="progress-step">
                        <div class="step-number">2</div>
                        <div>Review and submit your vote</div>
                    </div>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                    <?php foreach ($errors as $error): ?>
                        <p class="mb-0">• <?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="voteForm">
                <?php foreach ($positions as $position): ?>
                    <?php
                    $pos = $position['position'];
                    $candidates = $all_candidates[$pos];
                    ?>
                    
                    <div class="position-section">
                        <h3 class="position-title">
                            <?php echo htmlspecialchars($pos); ?>
                        </h3>
                        <div class="row">
                            <?php foreach ($candidates as $candidate): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check">
                                        <input class="form-check-input candidate-radio" 
                                               type="radio" 
                                               name="vote[<?php echo htmlspecialchars($pos); ?>]" 
                                               value="<?php echo $candidate['id']; ?>" 
                                               id="candidate_<?php echo $candidate['id']; ?>"
                                               style="position: absolute; opacity: 0;">
                                        <label class="form-check-label w-100" for="candidate_<?php echo $candidate['id']; ?>">
                                            <div class="candidate-card">
                                                <div class="card-body text-center p-4">
                                                    <!-- ERROR-FREE Image Handling -->
                                                    <img src="<?php 
                                                        echo !empty($candidate['photo_url']) 
                                                            ? htmlspecialchars($candidate['photo_url']) 
                                                            : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150&h=150&fit=crop&crop=face';
                                                    ?>" 
                                                         class="candidate-photo mb-3" 
                                                         alt="<?php echo htmlspecialchars($candidate['name']); ?>"
                                                         onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150&h=150&fit=crop&crop=face'">
                                                    <h5 class="candidate-name"><?php echo htmlspecialchars($candidate['name']); ?></h5>
                                                    <p class="candidate-bio text-muted"><?php echo htmlspecialchars($candidate['bio']); ?></p>
                                                    <div class="text-primary small fw-bold">
                                                        <i class="fas fa-check-circle me-1"></i>Select Candidate
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="text-center mt-5">
                    <button type="submit" class="btn btn-vote btn-lg">
                        <i class="fas fa-paper-plane me-2"></i>Submit Your Vote
                    </button>
                    <p class="text-muted mt-3">
                        <i class="fas fa-info-circle me-1"></i>
                        Your vote is final and cannot be changed once submitted
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Add interactivity to candidate cards
        document.addEventListener('DOMContentLoaded', function() {
            const radioInputs = document.querySelectorAll('.candidate-radio');
            
            radioInputs.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Remove selected class from all cards in the same position
                    const positionName = this.name;
                    const allCards = document.querySelectorAll(`input[name="${positionName}"] + label .candidate-card`);
                    allCards.forEach(card => card.classList.remove('selected'));
                    
                    // Add selected class to the clicked card
                    if (this.checked) {
                        this.parentElement.querySelector('.candidate-card').classList.add('selected');
                    }
                });
                
                // Initialize selected state
                if (radio.checked) {
                    radio.parentElement.querySelector('.candidate-card').classList.add('selected');
                }
            });

            // Form validation
            const form = document.getElementById('voteForm');
            form.addEventListener('submit', function(e) {
                const positions = <?php echo json_encode(array_column($positions, 'position')); ?>;
                let allPositionsFilled = true;
                const unfilledPositions = [];

                positions.forEach(position => {
                    const positionVote = form.querySelector(`input[name="vote[${position}]"]:checked`);
                    if (!positionVote) {
                        allPositionsFilled = false;
                        unfilledPositions.push(position);
                    }
                });

                if (!allPositionsFilled) {
                    e.preventDefault();
                    alert('Please select a candidate for each position:\n\n• ' + unfilledPositions.join('\n• '));
                }
            });
        });
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
