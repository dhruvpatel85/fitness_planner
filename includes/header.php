<?php
// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Planner - Your Personal Health Companion</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">
                <i class="fas fa-dumbbell"></i>
                <span>Fitness<span class="logo-highlight">Planner</span></span>
            </a>
            
            <div class="nav-menu">
                <a href="index.php" class="nav-link"><i class="fas fa-home"></i> Home</a>
                <?php if($isLoggedIn): ?>
                    <a href="dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a href="plan_generator.php" class="nav-link"><i class="fas fa-plus-circle"></i> Create Plan</a>
                    <a href="my_plans.php" class="nav-link"><i class="fas fa-clipboard-list"></i> My Plans</a>
                    <a href="progress.php" class="nav-link"><i class="fas fa-chart-line"></i> Progress</a>
                    <a href="quick_update.php" class="nav-link"><i class="fas fa-weight"></i> Quick Update</a>
                    <a href="profile.php" class="nav-link"><i class="fas fa-user"></i> Profile</a>
                    <a href="logout.php" class="nav-link btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    <span class="nav-user">Hi, <?php echo htmlspecialchars($username); ?></span>
                <?php else: ?>
                    <a href="about.php" class="nav-link"><i class="fas fa-info-circle"></i> About</a>
                    <a href="contact.php" class="nav-link"><i class="fas fa-envelope"></i> Contact</a>
                    <a href="login.php" class="nav-link"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <a href="register.php" class="nav-link btn-register">Register</a>
                <?php endif; ?>
            </div>
            
            <button class="nav-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>
    
    <div class="container">