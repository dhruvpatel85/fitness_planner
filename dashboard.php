<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user data
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = $conn->query($user_query);
$user = $user_result->fetch_assoc();

// Redirect to setup wizard if profile is incomplete
if (!$user['height'] || !$user['current_weight'] || !$user['goal']) {
    header("Location: setup_wizard.php");
    exit();
}

// Get user's plans count
$plans_query = "SELECT COUNT(*) as total_plans FROM plans WHERE user_id = $user_id";
$plans_result = $conn->query($plans_query);
$plans_data = $plans_result->fetch_assoc();

// Get latest progress
$progress_query = "SELECT * FROM progress WHERE user_id = $user_id ORDER BY date DESC LIMIT 1";
$progress_result = $conn->query($progress_query);
$progress = $progress_result->fetch_assoc();

require_once 'includes/header.php';
?>

<div class="dashboard-header">
    <h1>Welcome back, <?php echo htmlspecialchars($user['full_name'] ?: $user['username']); ?>!</h1>
    <p>Here's your fitness overview</p>
</div>

<div class="dashboard-cards">
    <div class="dashboard-card">
        <div class="card-icon">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <h3>My Plans</h3>
        <div class="card-value"><?php echo $plans_data['total_plans']; ?></div>
        <a href="my_plans.php" class="btn">View Plans</a>
    </div>
    
    <div class="dashboard-card">
        <div class="card-icon">
            <i class="fas fa-weight"></i>
        </div>
        <h3>Current Weight</h3>
        <div class="card-value">
            <?php echo $user['current_weight'] . ' kg'; ?>
        </div>
        <a href="progress.php" class="btn">Track</a>
    </div>
    
    <div class="dashboard-card">
        <div class="card-icon">
            <i class="fas fa-bullseye"></i>
        </div>
        <h3>Goal</h3>
        <div class="card-value">
            <?php echo ucfirst(str_replace('_', ' ', $user['goal'])); ?>
        </div>
        <a href="profile.php" class="btn">Change</a>
    </div>
    
    <div class="dashboard-card">
        <div class="card-icon">
            <i class="fas fa-fire"></i>
        </div>
        <h3>Activity Level</h3>
        <div class="card-value">
            <?php echo ucfirst($user['activity_level']); ?>
        </div>
        <a href="profile.php" class="btn">Update</a>
    </div>
</div>

<div style="background: white; padding: 30px; border-radius: 15px; margin: 30px 0;">
    <h2 style="margin-bottom: 20px;">Quick Actions</h2>
    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="plan_generator.php" class="btn btn-accent">
            <i class="fas fa-plus-circle"></i> Create New Plan
        </a>
        <a href="profile.php" class="btn">
            <i class="fas fa-user-edit"></i> Update Profile
        </a>
        <a href="progress.php" class="btn">
            <i class="fas fa-chart-line"></i> Track Progress
        </a>
        <a href="my_plans.php" class="btn">
            <i class="fas fa-history"></i> View History
        </a>
    </div>
</div>

<!-- Health Metrics Summary -->
<div style="background: white; padding: 30px; border-radius: 15px; margin: 30px 0;">
    <h2 style="margin-bottom: 20px;">Health Summary</h2>
    
    <?php
    // Calculate health metrics
    $height_m = $user['height'] / 100;
    $bmi = $user['current_weight'] / ($height_m * $height_m);
    
    if ($user['gender'] === 'male') {
        $bmr = 10 * $user['current_weight'] + 6.25 * $user['height'] - 5 * $user['age'] + 5;
    } else {
        $bmr = 10 * $user['current_weight'] + 6.25 * $user['height'] - 5 * $user['age'] - 161;
    }
    
    $activity_multipliers = [
        'sedentary' => 1.2,
        'light' => 1.375,
        'moderate' => 1.55,
        'active' => 1.725,
        'very_active' => 1.9
    ];
    
    $activity = $user['activity_level'];
    $tdee = $bmr * ($activity_multipliers[$activity] ?? 1.55);
    
    // Determine calorie target based on goal
    switch ($user['goal']) {
        case 'weight_loss':
            $calorie_target = $tdee - 500;
            $calorie_note = '500 calorie deficit for weight loss';
            break;
        case 'muscle_gain':
            $calorie_target = $tdee + 300;
            $calorie_note = '300 calorie surplus for muscle gain';
            break;
        case 'bulking':
            $calorie_target = $tdee + 500;
            $calorie_note = '500 calorie surplus for bulking';
            break;
        default:
            $calorie_target = $tdee;
            $calorie_note = 'Maintenance calories';
    }
    ?>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
        <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px;">
            <h4>BMI</h4>
            <div style="font-size: 2rem; font-weight: bold; color: var(--primary);">
                <?php echo number_format($bmi, 1); ?>
            </div>
            <small>
                <?php 
                if ($bmi < 18.5) echo 'Underweight';
                elseif ($bmi < 25) echo 'Healthy';
                elseif ($bmi < 30) echo 'Overweight';
                else echo 'Obese';
                ?>
            </small>
        </div>
        
        <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px;">
            <h4>Daily Calories</h4>
            <div style="font-size: 2rem; font-weight: bold; color: var(--success);">
                <?php echo number_format($calorie_target, 0); ?>
            </div>
            <small><?php echo $calorie_note; ?></small>
        </div>
        
        <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px;">
            <h4>BMR</h4>
            <div style="font-size: 2rem; font-weight: bold; color: var(--warning);">
                <?php echo number_format($bmr, 0); ?>
            </div>
            <small>Calories at rest</small>
        </div>
        
        <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px;">
            <h4>TDEE</h4>
            <div style="font-size: 2rem; font-weight: bold; color: var(--accent);">
                <?php echo number_format($tdee, 0); ?>
            </div>
            <small>Maintenance calories</small>
        </div>
    </div>
    
    <div style="margin-top: 25px; padding: 20px; background: #e8f4fd; border-radius: 10px;">
        <h4><i class="fas fa-lightbulb"></i> Recommendation</h4>
        <p>Based on your goal to <strong><?php echo str_replace('_', ' ', $user['goal']); ?></strong>, aim for <strong><?php echo number_format($calorie_target, 0); ?> calories</strong> per day with a <strong><?php echo ucfirst($user['activity_level']); ?></strong> activity level.</p>
    </div>
</div>

<?php if($progress): ?>
<div style="background: white; padding: 30px; border-radius: 15px; margin: 30px 0;">
    <h2 style="margin-bottom: 20px;">Recent Progress</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
        <div>
            <h4>Weight</h4>
            <p style="font-size: 1.5rem; font-weight: bold;"><?php echo $progress['weight']; ?> kg</p>
            <?php 
            $weight_change = $progress['weight'] - $user['current_weight'];
            if ($weight_change != 0):
                $trend = $weight_change > 0 ? 'up' : 'down';
                $color = $weight_change > 0 ? ($user['goal'] === 'weight_loss' ? 'var(--danger)' : 'var(--success)') : ($user['goal'] === 'weight_loss' ? 'var(--success)' : 'var(--danger)');
            ?>
                <small style="color: <?php echo $color; ?>;">
                    <i class="fas fa-arrow-<?php echo $trend; ?>"></i>
                    <?php echo abs($weight_change); ?> kg from current
                </small>
            <?php endif; ?>
        </div>
        
        <div>
            <h4>Date</h4>
            <p style="font-size: 1.2rem;"><?php echo date('M d, Y', strtotime($progress['date'])); ?></p>
        </div>
        
        <div>
            <h4>Body Fat</h4>
            <p style="font-size: 1.5rem; font-weight: bold;">
                <?php echo $progress['body_fat'] ? $progress['body_fat'] . '%' : 'Not recorded'; ?>
            </p>
        </div>
        
        <div>
            <h4>Notes</h4>
            <p><?php echo htmlspecialchars($progress['notes'] ?: 'No notes'); ?></p>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
require_once 'includes/footer.php';
?>