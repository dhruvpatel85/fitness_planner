<?php
session_start();

// Check for logout request
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Rest of your code...
?>

<?php
require_once 'config/database.php';
require_once 'includes/header.php';
?>

<div class="hero">
    <h1>Transform Your Fitness Journey</h1>
    <p>Get personalized diet and workout plans tailored to your goals - whether it's weight loss, muscle gain, or overall fitness improvement.</p>
    
    <?php if(isset($_SESSION['user_id'])): ?>
        <a href="plan_generator.php" class="btn btn-accent" style="font-size: 1.2rem; padding: 15px 40px;">
            <i class="fas fa-plus-circle"></i> Create Your Plan
        </a>
    <?php else: ?>
        <a href="register.php" class="btn btn-accent" style="font-size: 1.2rem; padding: 15px 40px;">
            <i class="fas fa-user-plus"></i> Get Started Free
        </a>
    <?php endif; ?>
</div>

<div class="features">
    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-utensils"></i>
        </div>
        <h3>Personalized Diet Plans</h3>
        <p>Custom meal plans based on your goals, dietary preferences, and calorie requirements.</p>
    </div>
    
    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-running"></i>
        </div>
        <h3>Workout Routines</h3>
        <p>Tailored exercise programs for home or gym, with video demonstrations and progress tracking.</p>
    </div>
    
    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <h3>Progress Tracking</h3>
        <p>Monitor your weight, measurements, and fitness achievements with detailed analytics.</p>
    </div>
</div>

<div class="goals-section" style="background: white; padding: 50px; border-radius: 15px; margin: 50px 0;">
    <h2 style="text-align: center; margin-bottom: 40px;">Choose Your Fitness Goal</h2>
    
    <div class="goal-options">
        <div class="goal-card">
            <div class="goal-icon">
                <i class="fas fa-weight"></i>
            </div>
            <h4>Weight Loss</h4>
            <p>Lose fat and get lean with calorie-controlled diets and cardio workouts.</p>
        </div>
        
        <div class="goal-card">
            <div class="goal-icon">
                <i class="fas fa-dumbbell"></i>
            </div>
            <h4>Muscle Gain</h4>
            <p>Build muscle mass with high-protein diets and strength training.</p>
        </div>
        
        <div class="goal-card">
            <div class="goal-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <h4>Bulking</h4>
            <p>Gain size and strength with calorie surplus and heavy lifting.</p>
        </div>
        
        <div class="goal-card">
            <div class="goal-icon">
                <i class="fas fa-heartbeat"></i>
            </div>
            <h4>Endurance</h4>
            <p>Improve stamina and cardiovascular health with endurance training.</p>
        </div>
        
        <div class="goal-card">
            <div class="goal-icon">
                <i class="fas fa-balance-scale"></i>
            </div>
            <h4>Maintenance</h4>
            <p>Maintain your current fitness level with balanced nutrition and exercise.</p>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>