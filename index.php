<?php
include 'includes/header.php';
?>

<!-- Main Content Section -->
<main class="main-content">
    <div class="container">
        <!-- Welcome Header -->
        <div class="welcome-header text-center py-5 mb-4">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="welcome-badge mb-3">
                        <span class="badge bg-primary rounded-pill px-3 py-2">
                            <i class="fas fa-shield-alt me-2"></i>Secure Digital Voting Platform
                        </span>
                    </div>
                    <h1 class="display-4 fw-bold text-dark mb-3">
                        College <span class="text-primary">Voting</span> System
                    </h1>
                    <p class="lead text-muted mb-4">
                        A modern, secure, and transparent platform for conducting college elections. 
                        Empower every student's voice with our cutting-edge digital voting solution.
                    </p>
                    <div class="welcome-actions">
                        <?php if(!isset($_SESSION['user_id'])): ?>
                            <a class="btn btn-primary btn-lg rounded-pill px-5 py-3 me-3 shadow-sm" href="register.php">
                                <i class="fas fa-user-plus me-2"></i>Get Started
                            </a>
                            <a class="btn btn-outline-primary btn-lg rounded-pill px-5 py-3" href="login.php">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                        <?php else: ?>
                            <a class="btn btn-primary btn-lg rounded-pill px-5 py-3 me-3 shadow-sm" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                            </a>
                            <a class="btn btn-outline-primary btn-lg rounded-pill px-5 py-3" href="vote.php">
                                <i class="fas fa-vote-yea me-2"></i>Cast Your Vote
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Bar -->
        <div class="stats-bar mb-5">
            <div class="row g-4">
                <div class="col-md-3 col-6">
                    <div class="stat-card text-center p-4 rounded-3 bg-light">
                        <div class="stat-icon text-primary mb-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h3 class="stat-number fw-bold text-dark mb-2">2,500+</h3>
                        <p class="stat-label text-muted mb-0">Active Students</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card text-center p-4 rounded-3 bg-light">
                        <div class="stat-icon text-success mb-3">
                            <i class="fas fa-vote-yea fa-2x"></i>
                        </div>
                        <h3 class="stat-number fw-bold text-dark mb-2">98%</h3>
                        <p class="stat-label text-muted mb-0">Voter Turnout</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card text-center p-4 rounded-3 bg-light">
                        <div class="stat-icon text-warning mb-3">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <h3 class="stat-number fw-bold text-dark mb-2">24/7</h3>
                        <p class="stat-label text-muted mb-0">Accessibility</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card text-center p-4 rounded-3 bg-light">
                        <div class="stat-icon text-info mb-3">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <h3 class="stat-number fw-bold text-dark mb-2">100%</h3>
                        <p class="stat-label text-muted mb-0">Secure</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Core Features -->
        <section class="core-features py-5">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-10 text-center">
                    <h2 class="section-title display-5 fw-bold text-dark mb-3">Why Choose Our Platform?</h2>
                    <p class="section-subtitle text-muted lead">Built with cutting-edge technology to ensure fair, secure, and transparent elections</p>
                </div>
            </div>
            
            <div class="row g-4 mb-5">
                <div class="col-lg-6">
                    <div class="feature-card card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="feature-header d-flex align-items-center mb-3">
                                <div class="feature-icon bg-primary text-white rounded-circle me-3">
                                    <i class="fas fa-shield-alt fa-lg"></i>
                                </div>
                                <h5 class="feature-title fw-bold text-dark mb-0">Bank-Level Security</h5>
                            </div>
                            <p class="feature-description text-muted mb-3">
                                Advanced encryption and security protocols ensure that every vote is protected 
                                and tamper-proof. Your vote remains confidential and secure throughout the entire process.
                            </p>
                            <div class="feature-badges">
                                <span class="badge bg-light text-dark border me-2">256-bit SSL</span>
                                <span class="badge bg-light text-dark border">End-to-End Encrypted</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="feature-card card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="feature-header d-flex align-items-center mb-3">
                                <div class="feature-icon bg-success text-white rounded-circle me-3">
                                    <i class="fas fa-bolt fa-lg"></i>
                                </div>
                                <h5 class="feature-title fw-bold text-dark mb-0">Lightning Fast</h5>
                            </div>
                            <p class="feature-description text-muted mb-3">
                                Cast your vote in seconds with our intuitive interface. Real-time processing 
                                means instant confirmation and immediate results tracking for complete transparency.
                            </p>
                            <div class="feature-badges">
                                <span class="badge bg-light text-dark border me-2">Instant Results</span>
                                <span class="badge bg-light text-dark border">Real-time Updates</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="feature-card card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="feature-header d-flex align-items-center mb-3">
                                <div class="feature-icon bg-warning text-white rounded-circle me-3">
                                    <i class="fas fa-chart-line fa-lg"></i>
                                </div>
                                <h5 class="feature-title fw-bold text-dark mb-0">Live Analytics</h5>
                            </div>
                            <p class="feature-description text-muted mb-3">
                                Watch real-time election results with beautiful data visualizations. 
                                Transparent reporting ensures trust and confidence in the entire electoral process.
                            </p>
                            <div class="feature-badges">
                                <span class="badge bg-light text-dark border me-2">Live Dashboard</span>
                                <span class="badge bg-light text-dark border">Visual Analytics</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="feature-card card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="feature-header d-flex align-items-center mb-3">
                                <div class="feature-icon bg-info text-white rounded-circle me-3">
                                    <i class="fas fa-mobile-alt fa-lg"></i>
                                </div>
                                <h5 class="feature-title fw-bold text-dark mb-0">Mobile Friendly</h5>
                            </div>
                            <p class="feature-description text-muted mb-3">
                                Access the voting system from any device. Our responsive design ensures 
                                a seamless experience whether you're on desktop, tablet, or smartphone.
                            </p>
                            <div class="feature-badges">
                                <span class="badge bg-light text-dark border me-2">Responsive Design</span>
                                <span class="badge bg-light text-dark border">Cross-Platform</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Process Section -->
        <section class="process-section bg-light rounded-4 py-5 mb-5">
            <div class="container">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center">
                        <h2 class="section-title display-5 fw-bold text-dark mb-3">How It Works</h2>
                        <p class="section-subtitle text-muted lead">Simple steps to make your voice heard in campus elections</p>
                    </div>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="process-step text-center p-4">
                            <div class="step-number-wrapper mb-4">
                                <div class="step-number bg-primary text-white rounded-circle mx-auto">1</div>
                            </div>
                            <h5 class="step-title fw-bold text-dark mb-3">Register & Verify</h5>
                            <p class="step-description text-muted mb-0">
                                Create your account and verify your student identity through our secure system.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="process-step text-center p-4">
                            <div class="step-number-wrapper mb-4">
                                <div class="step-number bg-success text-white rounded-circle mx-auto">2</div>
                            </div>
                            <h5 class="step-title fw-bold text-dark mb-3">Review Candidates</h5>
                            <p class="step-description text-muted mb-0">
                                Explore candidate profiles, read their manifestos, and understand their vision.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="process-step text-center p-4">
                            <div class="step-number-wrapper mb-4">
                                <div class="step-number bg-warning text-white rounded-circle mx-auto">3</div>
                            </div>
                            <h5 class="step-title fw-bold text-dark mb-3">Cast Your Vote</h5>
                            <p class="step-description text-muted mb-0">
                                Select your preferred candidates and submit your vote with one click.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="process-step text-center p-4">
                            <div class="step-number-wrapper mb-4">
                                <div class="step-number bg-info text-white rounded-circle mx-auto">4</div>
                            </div>
                            <h5 class="step-title fw-bold text-dark mb-3">View Results</h5>
                            <p class="step-description text-muted mb-0">
                                Watch live results and see democracy in action with real-time updates.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="final-cta text-center py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h2 class="cta-title display-6 fw-bold text-dark mb-4">Ready to Make Your Voice Heard?</h2>
                    <p class="cta-description text-muted lead mb-4">
                        Join thousands of students who are shaping the future of our college community. 
                        Every vote counts in building a better campus for everyone.
                    </p>
                    <?php if(!isset($_SESSION['user_id'])): ?>
                        <a href="register.php" class="btn btn-primary btn-lg rounded-pill px-5 py-3 me-3 shadow-sm">
                            <i class="fas fa-user-plus me-2"></i>Register Now
                        </a>
                        <a href="login.php" class="btn btn-outline-primary btn-lg rounded-pill px-5 py-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Login to Vote
                        </a>
                    <?php else: ?>
                        <a href="vote.php" class="btn btn-primary btn-lg rounded-pill px-5 py-3 me-3 shadow-sm">
                            <i class="fas fa-vote-yea me-2"></i>Cast Your Vote Now
                        </a>
                        <a href="results.php" class="btn btn-outline-primary btn-lg rounded-pill px-5 py-3">
                            <i class="fas fa-chart-pie me-2"></i>View Results
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</main>

<style>
    .main-content {
        padding: 2rem 0;
    }

    /* Welcome Header */
    .welcome-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 20px;
        margin-top: 1rem;
    }

    .welcome-badge .badge {
        font-size: 0.9rem;
        font-weight: 500;
    }

    /* Stats Bar */
    .stat-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .stat-icon {
        transition: transform 0.3s ease;
    }

    .stat-card:hover .stat-icon {
        transform: scale(1.1);
    }

    .stat-number {
        font-size: 2.5rem;
    }

    /* Feature Cards */
    .feature-card {
        transition: all 0.3s ease;
        border-radius: 15px;
    }

    .feature-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
    }

    .feature-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .feature-header {
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .feature-badges .badge {
        font-size: 0.75rem;
        padding: 0.4em 0.8em;
    }

    /* Process Section */
    .process-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .process-step {
        transition: all 0.3s ease;
    }

    .process-step:hover {
        transform: translateY(-5px);
    }

    .step-number-wrapper {
        position: relative;
    }

    .step-number {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .process-step:hover .step-number {
        transform: scale(1.1);
    }

    .step-title {
        font-size: 1.2rem;
    }

    /* Final CTA */
    .final-cta {
        background: white;
        border-radius: 20px;
        margin: 2rem 0;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .welcome-header {
            padding: 2rem 1rem !important;
        }

        .display-4 {
            font-size: 2.5rem;
        }

        .stat-number {
            font-size: 2rem;
        }

        .welcome-actions .btn {
            display: block;
            width: 100%;
            margin-bottom: 1rem;
        }

        .welcome-actions .me-3 {
            margin-right: 0 !important;
        }

        .process-step {
            margin-bottom: 1rem;
        }
    }

    @media (max-width: 576px) {
        .stat-card {
            margin-bottom: 1rem;
        }

        .feature-card {
            margin-bottom: 1.5rem;
        }
    }
</style>

<?php include 'includes/footer.php'; ?>