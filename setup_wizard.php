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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $age = intval($_POST['age']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $height = floatval($_POST['height']);
    $current_weight = floatval($_POST['current_weight']);
    $goal = $conn->real_escape_string($_POST['goal']);
    $activity_level = $conn->real_escape_string($_POST['activity_level']);
    
    $update_query = "UPDATE users SET 
                    age = $age,
                    gender = '$gender',
                    height = $height,
                    current_weight = $current_weight,
                    goal = '$goal',
                    activity_level = '$activity_level'
                    WHERE id = $user_id";
    
    if ($conn->query($update_query)) {
        $_SESSION['success'] = "Profile setup complete! Now you can create personalized plans.";
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Error updating profile: " . $conn->error;
    }
}

// Check if user already completed setup
if ($user['height'] && $user['current_weight'] && $user['goal']) {
    header("Location: dashboard.php");
    exit();
}

require_once 'includes/header.php';
?>

<div class="container">
    <div style="max-width: 800px; margin: 50px auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="width: 80px; height: 80px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <i class="fas fa-user-check" style="font-size: 2rem;"></i>
            </div>
            <h1>Complete Your Profile</h1>
            <p style="color: #666; margin-top: 10px;">We need a few details to create personalized fitness plans for you.</p>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label class="form-label" for="age">Age</label>
                    <input type="number" class="form-control" id="age" name="age" 
                           value="<?php echo $user['age'] ?? ''; ?>" min="15" max="100" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="gender">Gender</label>
                    <select class="form-control" id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="male" <?php echo ($user['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo ($user['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                        <option value="other" <?php echo ($user['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label class="form-label" for="height">Height (cm)</label>
                    <input type="number" class="form-control" id="height" name="height" step="0.1"
                           value="<?php echo $user['height'] ?? ''; ?>" min="100" max="250" required>
                    <small>Example: 175.5 cm</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="current_weight">Current Weight (kg)</label>
                    <input type="number" class="form-control" id="current_weight" name="current_weight" step="0.1"
                           value="<?php echo $user['current_weight'] ?? ''; ?>" min="30" max="200" required>
                    <small>Example: 72.5 kg</small>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label">What is your primary fitness goal?</label>
                <div class="goal-options" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));">
                    <label class="goal-card">
                        <input type="radio" name="goal" value="weight_loss" required style="display: none;">
                        <div class="goal-icon"><i class="fas fa-weight"></i></div>
                        <h4>Weight Loss</h4>
                        <p>Lose body fat</p>
                    </label>
                    
                    <label class="goal-card">
                        <input type="radio" name="goal" value="muscle_gain" required style="display: none;">
                        <div class="goal-icon"><i class="fas fa-dumbbell"></i></div>
                        <h4>Muscle Gain</h4>
                        <p>Build lean muscle</p>
                    </label>
                    
                    <label class="goal-card">
                        <input type="radio" name="goal" value="bulking" required style="display: none;">
                        <div class="goal-icon"><i class="fas fa-bolt"></i></div>
                        <h4>Bulking</h4>
                        <p>Gain size and strength</p>
                    </label>
                    
                    <label class="goal-card">
                        <input type="radio" name="goal" value="endurance" required style="display: none;">
                        <div class="goal-icon"><i class="fas fa-heartbeat"></i></div>
                        <h4>Endurance</h4>
                        <p>Improve stamina</p>
                    </label>
                    
                    <label class="goal-card">
                        <input type="radio" name="goal" value="maintenance" required style="display: none;">
                        <div class="goal-icon"><i class="fas fa-balance-scale"></i></div>
                        <h4>Maintenance</h4>
                        <p>Stay fit</p>
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="activity_level">How active are you?</label>
                <select class="form-control" id="activity_level" name="activity_level" required>
                    <option value="">Select Activity Level</option>
                    <option value="sedentary" <?php echo ($user['activity_level'] ?? '') === 'sedentary' ? 'selected' : ''; ?>>Sedentary (office job, little exercise)</option>
                    <option value="light" <?php echo ($user['activity_level'] ?? '') === 'light' ? 'selected' : ''; ?>>Light (exercise 1-3 days/week)</option>
                    <option value="moderate" <?php echo ($user['activity_level'] ?? '') === 'moderate' ? 'selected' : ''; ?>>Moderate (exercise 3-5 days/week)</option>
                    <option value="active" <?php echo ($user['activity_level'] ?? '') === 'active' ? 'selected' : ''; ?>>Active (exercise 6-7 days/week)</option>
                    <option value="very_active" <?php echo ($user['activity_level'] ?? '') === 'very_active' ? 'selected' : ''; ?>>Very Active (athlete, intense exercise daily)</option>
                </select>
            </div>
            
            <div style="margin-top: 40px; text-align: center;">
                <button type="submit" class="btn btn-accent" style="padding: 15px 50px; font-size: 1.1rem;">
                    <i class="fas fa-check-circle"></i> Complete Setup
                </button>
                <a href="dashboard.php" class="btn" style="margin-left: 15px;">
                    Skip for Now
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Goal card selection
    const goalCards = document.querySelectorAll('.goal-card');
    goalCards.forEach(card => {
        card.addEventListener('click', function() {
            goalCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
        });
    });
    
    // Auto-select if already has value
    const currentGoal = '<?php echo $user["goal"] ?? ""; ?>';
    if (currentGoal) {
        const goalInput = document.querySelector(`input[value="${currentGoal}"]`);
        if (goalInput) {
            goalInput.checked = true;
            goalInput.closest('.goal-card').classList.add('selected');
        }
    }
});
</script>

<?php
require_once 'includes/footer.php';
?>