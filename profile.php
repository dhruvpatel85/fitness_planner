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

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $age = intval($_POST['age']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $height = floatval($_POST['height']);
    $current_weight = floatval($_POST['current_weight']);
    $goal = $conn->real_escape_string($_POST['goal']);
    $activity_level = $conn->real_escape_string($_POST['activity_level']);
    
    $update_query = "UPDATE users SET 
                    full_name = '$full_name',
                    age = $age,
                    gender = '$gender',
                    height = $height,
                    current_weight = $current_weight,
                    goal = '$goal',
                    activity_level = '$activity_level'
                    WHERE id = $user_id";
    
    if ($conn->query($update_query)) {
        $success = "Profile updated successfully!";
        // Refresh user data
        $user_result = $conn->query($user_query);
        $user = $user_result->fetch_assoc();
    } else {
        $error = "Error updating profile: " . $conn->error;
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    $check_query = "SELECT password FROM users WHERE id = $user_id";
    $check_result = $conn->query($check_query);
    $user_data = $check_result->fetch_assoc();
    
    if (password_verify($current_password, $user_data['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_pass_query = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
            
            if ($conn->query($update_pass_query)) {
                $success = "Password changed successfully!";
            } else {
                $error = "Error changing password: " . $conn->error;
            }
        } else {
            $error = "New passwords do not match!";
        }
    } else {
        $error = "Current password is incorrect!";
    }
}

require_once 'includes/header.php';
?>

<div class="container">
    <h1>Your Profile</h1>
    
    <?php if($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 30px;">
        <!-- Profile Update Form -->
        <div>
            <h2>Update Profile Information</h2>
            <form method="POST" action="" style="margin-top: 20px;">
                <input type="hidden" name="update_profile" value="1">
                
                <div class="form-group">
                    <label class="form-label" for="full_name">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" 
                           value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" class="form-control" id="username" 
                           value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                    <small style="color: #666;">Username cannot be changed</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" class="form-control" id="email" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    <small style="color: #666;">Email cannot be changed</small>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
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
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label class="form-label" for="height">Height (cm)</label>
                        <input type="number" class="form-control" id="height" name="height" step="0.1"
                               value="<?php echo $user['height'] ?? ''; ?>" min="100" max="250" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="current_weight">Current Weight (kg)</label>
                        <input type="number" class="form-control" id="current_weight" name="current_weight" step="0.1"
                               value="<?php echo $user['current_weight'] ?? ''; ?>" min="30" max="200" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="goal">Fitness Goal</label>
                    <select class="form-control" id="goal" name="goal" required>
                        <option value="">Select Goal</option>
                        <option value="weight_loss" <?php echo ($user['goal'] ?? '') === 'weight_loss' ? 'selected' : ''; ?>>Weight Loss</option>
                        <option value="muscle_gain" <?php echo ($user['goal'] ?? '') === 'muscle_gain' ? 'selected' : ''; ?>>Muscle Gain</option>
                        <option value="bulking" <?php echo ($user['goal'] ?? '') === 'bulking' ? 'selected' : ''; ?>>Bulking</option>
                        <option value="endurance" <?php echo ($user['goal'] ?? '') === 'endurance' ? 'selected' : ''; ?>>Endurance</option>
                        <option value="maintenance" <?php echo ($user['goal'] ?? '') === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="activity_level">Activity Level</label>
                    <select class="form-control" id="activity_level" name="activity_level" required>
                        <option value="">Select Activity Level</option>
                        <option value="sedentary" <?php echo ($user['activity_level'] ?? '') === 'sedentary' ? 'selected' : ''; ?>>Sedentary (little or no exercise)</option>
                        <option value="light" <?php echo ($user['activity_level'] ?? '') === 'light' ? 'selected' : ''; ?>>Light (exercise 1-3 days/week)</option>
                        <option value="moderate" <?php echo ($user['activity_level'] ?? '') === 'moderate' ? 'selected' : ''; ?>>Moderate (exercise 3-5 days/week)</option>
                        <option value="active" <?php echo ($user['activity_level'] ?? '') === 'active' ? 'selected' : ''; ?>>Active (exercise 6-7 days/week)</option>
                        <option value="very_active" <?php echo ($user['activity_level'] ?? '') === 'very_active' ? 'selected' : ''; ?>>Very Active (intense exercise daily)</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-block btn-accent">
                    <i class="fas fa-save"></i> Update Profile
                </button>
            </form>
        </div>
        
        <!-- Password Change Form -->
        <div>
            <h2>Change Password</h2>
            <form method="POST" action="" style="margin-top: 20px;">
                <input type="hidden" name="change_password" value="1">
                
                <div class="form-group">
                    <label class="form-label" for="current_password">Current Password</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="new_password">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    <small id="password-match" style="display: none; color: red;">Passwords do not match!</small>
                </div>
                
                <button type="submit" class="btn btn-block">
                    <i class="fas fa-key"></i> Change Password
                </button>
            </form>
            
            <!-- Health Metrics -->
            <?php if($user['current_weight'] && $user['height'] && $user['age']): ?>
                <div style="margin-top: 40px; padding: 25px; background: #f8f9fa; border-radius: 12px;">
                    <h3>Your Health Metrics</h3>
                    
                    <?php
                    // Calculate BMI
                    $height_m = $user['height'] / 100;
                    $bmi = $user['current_weight'] / ($height_m * $height_m);
                    $bmi_category = '';
                    
                    if ($bmi < 18.5) $bmi_category = 'Underweight';
                    elseif ($bmi < 25) $bmi_category = 'Normal';
                    elseif ($bmi < 30) $bmi_category = 'Overweight';
                    else $bmi_category = 'Obese';
                    
                    // Calculate BMR
                    if ($user['gender'] === 'male') {
                        $bmr = 10 * $user['current_weight'] + 6.25 * $user['height'] - 5 * $user['age'] + 5;
                    } else {
                        $bmr = 10 * $user['current_weight'] + 6.25 * $user['height'] - 5 * $user['age'] - 161;
                    }
                    
                    // Calculate daily calories for weight maintenance
                    $activity_multipliers = [
                        'sedentary' => 1.2,
                        'light' => 1.375,
                        'moderate' => 1.55,
                        'active' => 1.725,
                        'very_active' => 1.9
                    ];
                    
                    $activity = $user['activity_level'] ?? 'moderate';
                    $tdee = $bmr * ($activity_multipliers[$activity] ?? 1.55);
                    ?>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 20px;">
                        <div style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                            <h4>BMI</h4>
                            <div style="font-size: 1.8rem; font-weight: bold; color: var(--primary);">
                                <?php echo number_format($bmi, 1); ?>
                            </div>
                            <small><?php echo $bmi_category; ?></small>
                        </div>
                        
                        <div style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                            <h4>BMR</h4>
                            <div style="font-size: 1.8rem; font-weight: bold; color: var(--success);">
                                <?php echo number_format($bmr, 0); ?>
                            </div>
                            <small>Calories/day at rest</small>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px; padding: 15px; background: white; border-radius: 8px;">
                        <h4>Daily Calorie Needs</h4>
                        <p><strong>Maintenance:</strong> <?php echo number_format($tdee, 0); ?> calories/day</p>
                        <p><strong>Weight Loss (500 cal deficit):</strong> <?php echo number_format($tdee - 500, 0); ?> calories/day</p>
                        <p><strong>Muscle Gain (300 cal surplus):</strong> <?php echo number_format($tdee + 300, 0); ?> calories/day</p>
                    </div>
                </div>
            <?php else: ?>
                <div style="margin-top: 40px; padding: 25px; background: #f8f9fa; border-radius: 12px;">
                    <h3>Complete Your Profile</h3>
                    <p>Enter your height, weight, and age to see your health metrics and calorie calculations.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password match checker
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const passwordMatch = document.getElementById('password-match');
    
    function checkPasswordMatch() {
        if (newPassword.value !== confirmPassword.value && confirmPassword.value !== '') {
            passwordMatch.style.display = 'block';
            confirmPassword.style.borderColor = 'red';
        } else {
            passwordMatch.style.display = 'none';
            confirmPassword.style.borderColor = '';
        }
    }
    
    newPassword.addEventListener('input', checkPasswordMatch);
    confirmPassword.addEventListener('input', checkPasswordMatch);
});
</script>

<?php
require_once 'includes/footer.php';
?>