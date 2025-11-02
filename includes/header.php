<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Voting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        /* Add spacing between header options */
        .navbar-nav .nav-item {
            margin: 0 8px; /* Adds space between each nav item */
        }
        
        .navbar-nav .nav-link {
            padding: 8px 16px; /* Increases clickable area */
            border-radius: 6px; /* Rounded corners for better appearance */
            transition: all 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1); /* Subtle hover background */
            transform: translateY(-2px);
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 8px 0;
        }
        
        .dropdown-item {
            padding: 8px 16px;
            border-radius: 5px;
            margin: 0 8px;
            width: auto;
            line-height: 1.4;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateX(5px);
        }

        /* Remove extra spacing from dropdown list items */
        .dropdown-menu li {
            margin: 0;
            padding: 0;
        }
        
        /* Fix for the user info section */
        .dropdown-item-text {
            padding: 8px 16px;
            margin: 0 8px;
            line-height: 1.4;
        }
        
        /* Adjust divider spacing */
        .dropdown-divider {
            margin: 8px 0;
        }

        /* Specific spacing for left and right nav sections */
        .navbar-nav.me-auto .nav-item {
            margin-right: 4px; /* Slightly less space on right side of left items */
        }
        
        .navbar-nav.ms-auto .nav-item {
            margin-left: 4px; /* Slightly less space on left side of right items */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-vote-yea me-2"></i>College Voting System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="vote.php">
                                <i class="fas fa-vote-yea me-1"></i>Vote
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="results.php">
                                <i class="fas fa-chart-pie me-1"></i>Results
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php if($_SESSION['role'] == 'admin'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog me-1"></i>Admin Panel
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="admin/index.php">
                                            <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="admin/candidates.php">
                                            <i class="fas fa-user-tie me-2"></i>Manage Candidates
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="admin/users.php">
                                            <i class="fas fa-users-cog me-2"></i>Manage Users
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="results.php">
                                            <i class="fas fa-chart-bar me-2"></i>View Results
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['username']); ?>
                                <?php if($_SESSION['role'] == 'admin'): ?>
                                    <span class="badge bg-warning ms-1">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary ms-1">Voter</span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <span class="dropdown-item-text">
                                        <small>Logged in as</small><br>
                                        <strong><?php echo htmlspecialchars($_SESSION['username'])?></strong>
                                    </span>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="dashboard.php">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="vote.php">
                                        <i class="fas fa-vote-yea me-2"></i>Cast Vote
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="results.php">
                                        <i class="fas fa-chart-pie me-2"></i>View Results
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="logout.php">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4"></div>