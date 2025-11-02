<?php
// Start output buffering to prevent header errors
ob_start();

include '../includes/auth_check.php';

// Check if user is admin - MUST be before any HTML output
if($_SESSION['role'] != 'admin') {
    header("Location: ../dashboard.php");
    exit();
}

include '../config/database.php';

$errors = [];
$success = '';

// Handle form submissions
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['add_candidate'])) {
        // Add new candidate
        $name = trim($_POST['name']);
        $position = trim($_POST['position']);
        $bio = trim($_POST['bio']);
        $photo_url = trim($_POST['photo_url']);

        // Validation
        if(empty($name)) {
            $errors[] = "Candidate name is required";
        }
        if(empty($position)) {
            $errors[] = "Position is required";
        }

        if(empty($errors)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO candidates (name, position, bio, photo_url) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $position, $bio, $photo_url]);
                $success = "Candidate added successfully!";
            } catch (PDOException $e) {
                $errors[] = "Error adding candidate: " . $e->getMessage();
            }
        }
    }
    elseif(isset($_POST['update_candidate'])) {
        // Update candidate
        $id = $_POST['candidate_id'];
        $name = trim($_POST['name']);
        $position = trim($_POST['position']);
        $bio = trim($_POST['bio']);
        $photo_url = trim($_POST['photo_url']);

        // Validation
        if(empty($name)) {
            $errors[] = "Candidate name is required";
        }
        if(empty($position)) {
            $errors[] = "Position is required";
        }

        if(empty($errors)) {
            try {
                $stmt = $pdo->prepare("UPDATE candidates SET name = ?, position = ?, bio = ?, photo_url = ? WHERE id = ?");
                $stmt->execute([$name, $position, $bio, $photo_url, $id]);
                $success = "Candidate updated successfully!";
            } catch (PDOException $e) {
                $errors[] = "Error updating candidate: " . $e->getMessage();
            }
        }
    }
    elseif(isset($_POST['delete_candidate'])) {
        // Delete candidate
        $id = $_POST['candidate_id'];
        
        try {
            // Check if candidate has votes
            $vote_check = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE candidate_id = ?");
            $vote_check->execute([$id]);
            $vote_count = $vote_check->fetchColumn();

            if($vote_count > 0) {
                $errors[] = "Cannot delete candidate who has received votes. Remove votes first.";
            } else {
                $stmt = $pdo->prepare("DELETE FROM candidates WHERE id = ?");
                $stmt->execute([$id]);
                $success = "Candidate deleted successfully!";
            }
        } catch (PDOException $e) {
            $errors[] = "Error deleting candidate: " . $e->getMessage();
        }
    }
}

// Get all candidates
$candidates_stmt = $pdo->query("SELECT * FROM candidates ORDER BY position, name");
$candidates = $candidates_stmt->fetchAll();

// Get distinct positions for dropdown
$positions_stmt = $pdo->query("SELECT DISTINCT position FROM candidates ORDER BY position");
$positions = $positions_stmt->fetchAll();

// Get candidate for editing (if edit_id is set)
$edit_candidate = null;
if(isset($_GET['edit_id'])) {
    $edit_stmt = $pdo->prepare("SELECT * FROM candidates WHERE id = ?");
    $edit_stmt->execute([$_GET['edit_id']]);
    $edit_candidate = $edit_stmt->fetch();
}

// Now include header after all processing and redirect checks
include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-user-tie me-2"></i>Manage Candidates
                        </h4>
                        <button class="btn btn-light btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#addCandidateModal">
                            <i class="fas fa-plus me-1"></i>Add Candidate
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Alerts -->
                    <?php if(!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                            <ul class="mb-0">
                                <?php foreach($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Candidates Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Bio</th>
                                    <th>Votes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($candidates)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="fas fa-users fa-2x mb-3 d-block"></i>
                                            No candidates found. Add your first candidate to get started.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($candidates as $candidate): ?>
                                        <tr>
                                            <td>
                                                <img src="<?php 
                                                    echo !empty($candidate['photo_url']) 
                                                        ? htmlspecialchars($candidate['photo_url']) 
                                                        : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100&h=100&fit=crop&crop=face';
                                                ?>" 
                                                     class="rounded-circle" 
                                                     width="50" 
                                                     height="50" 
                                                     alt="<?php echo htmlspecialchars($candidate['name']); ?>"
                                                     style="object-fit: cover;"
                                                     onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100&h=100&fit=crop&crop=face'">
                                            </td>
                                            <td class="fw-bold"><?php echo htmlspecialchars($candidate['name']); ?></td>
                                            <td>
                                                <span class="badge bg-primary"><?php echo htmlspecialchars($candidate['position']); ?></span>
                                            </td>
                                            <td>
                                                <?php if(!empty($candidate['bio'])): ?>
                                                    <small class="text-muted"><?php echo htmlspecialchars(substr($candidate['bio'], 0, 50)); ?><?php echo strlen($candidate['bio']) > 50 ? '...' : ''; ?></small>
                                                <?php else: ?>
                                                    <small class="text-muted">No bio provided</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-success"><?php echo $candidate['votes']; ?> votes</span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="?edit_id=<?php echo $candidate['id']; ?>" class="btn btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $candidate['id']; ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>

                                                <!-- Delete Confirmation Modal -->
                                                <div class="modal fade" id="deleteModal<?php echo $candidate['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Confirm Delete</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to delete <strong><?php echo htmlspecialchars($candidate['name']); ?></strong>?</p>
                                                                <?php if($candidate['votes'] > 0): ?>
                                                                    <div class="alert alert-warning">
                                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                                        This candidate has <?php echo $candidate['votes']; ?> vote(s). Deleting them will remove all associated votes.
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <form method="POST" style="display: inline;">
                                                                    <input type="hidden" name="candidate_id" value="<?php echo $candidate['id']; ?>">
                                                                    <button type="submit" name="delete_candidate" class="btn btn-danger">Delete</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Candidate Modal -->
<div class="modal fade" id="addCandidateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Candidate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Candidate Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required 
                                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="position" class="form-label">Position *</label>
                                <input type="text" class="form-control" id="position" name="position" required 
                                       list="positionSuggestions"
                                       value="<?php echo isset($_POST['position']) ? htmlspecialchars($_POST['position']) : ''; ?>">
                                <datalist id="positionSuggestions">
                                    <?php foreach($positions as $pos): ?>
                                        <option value="<?php echo htmlspecialchars($pos['position']); ?>">
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="photo_url" class="form-label">Photo URL</label>
                        <input type="url" class="form-control" id="photo_url" name="photo_url" 
                               placeholder="https://example.com/photo.jpg"
                               value="<?php echo isset($_POST['photo_url']) ? htmlspecialchars($_POST['photo_url']) : ''; ?>">
                        <div class="form-text">Leave empty to use default avatar</div>
                    </div>
                    <div class="mb-3">
                        <label for="bio" class="form-label">Biography</label>
                        <textarea class="form-control" id="bio" name="bio" rows="4" 
                                  placeholder="Brief description about the candidate..."><?php echo isset($_POST['bio']) ? htmlspecialchars($_POST['bio']) : ''; ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_candidate" class="btn btn-primary">Add Candidate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Candidate Modal -->
<?php if($edit_candidate): ?>
<div class="modal fade show" id="editCandidateModal" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Candidate</h5>
                <a href="candidates.php" class="btn-close"></a>
            </div>
            <form method="POST">
                <input type="hidden" name="candidate_id" value="<?php echo $edit_candidate['id']; ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Candidate Name *</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required 
                                       value="<?php echo htmlspecialchars($edit_candidate['name']); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_position" class="form-label">Position *</label>
                                <input type="text" class="form-control" id="edit_position" name="position" required 
                                       list="positionSuggestions"
                                       value="<?php echo htmlspecialchars($edit_candidate['position']); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_photo_url" class="form-label">Photo URL</label>
                        <input type="url" class="form-control" id="edit_photo_url" name="photo_url" 
                               placeholder="https://example.com/photo.jpg"
                               value="<?php echo htmlspecialchars($edit_candidate['photo_url']); ?>">
                        <div class="form-text">Leave empty to use default avatar</div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_bio" class="form-label">Biography</label>
                        <textarea class="form-control" id="edit_bio" name="bio" rows="4"><?php echo htmlspecialchars($edit_candidate['bio']); ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="candidates.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="update_candidate" class="btn btn-primary">Update Candidate</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    // Show edit modal automatically when page loads with edit_id parameter
    document.addEventListener('DOMContentLoaded', function() {
        const editModal = new bootstrap.Modal(document.getElementById('editCandidateModal'));
        editModal.show();
    });
</script>
<?php endif; ?>

<style>
    .table img {
        border: 2px solid #e9ecef;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem;
    }
    
    .card {
        border: none;
        border-radius: 15px;
    }
    
    .card-header {
        border-radius: 15px 15px 0 0 !important;
    }
    
    .table th {
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
    }
    
    .modal-content {
        border-radius: 15px;
        border: none;
    }
    
    .modal-header {
        border-bottom: 1px solid #dee2e6;
        border-radius: 15px 15px 0 0;
    }
</style>

<script>
    // Auto-close alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
    
    // Preview photo when URL changes
    document.addEventListener('DOMContentLoaded', function() {
        const photoUrlInput = document.getElementById('photo_url');
        const photoPreview = document.createElement('img');
        photoPreview.className = 'mt-2 rounded';
        photoPreview.style.maxWidth = '100px';
        photoPreview.style.display = 'none';
        
        if(photoUrlInput) {
            photoUrlInput.parentNode.appendChild(photoPreview);
            
            photoUrlInput.addEventListener('input', function() {
                if(this.value) {
                    photoPreview.src = this.value;
                    photoPreview.style.display = 'block';
                    photoPreview.onerror = function() {
                        photoPreview.style.display = 'none';
                    };
                } else {
                    photoPreview.style.display = 'none';
                }
            });
        }
    });
</script>

<?php 
// End output buffering and flush the output
ob_end_flush();
include '../includes/footer.php'; 
?>