<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $age = isset($_POST['age']) ? intval($_POST['age']) : NULL;
    $gender = isset($_POST['gender']) ? $conn->real_escape_string($_POST['gender']) : NULL;
    $height = isset($_POST['height']) ? floatval($_POST['height']) : NULL;
    $current_weight = isset($_POST['current_weight']) ? floatval($_POST['current_weight']) : NULL;
    $goal = isset($_POST['goal']) ? $conn->real_escape_string($_POST['goal']) : NULL;
    $activity_level = isset($_POST['activity_level']) ? $conn->real_escape_string($_POST['activity_level']) : NULL;
    
    // Check if user exists
    $check_query = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
    $check_result = $conn->query($check_query);
    
    if ($check_result->num_rows > 0) {
        $error = "Username or email already exists!";
    } else {
        // Insert new user with all fields
        $insert_query = "INSERT INTO users (username, email, password, full_name, age, gender, height, current_weight, goal, activity_level, created_at) 
                        VALUES ('$username', '$email', '$password', '$full_name', $age, '$gender', $height, $current_weight, '$goal', '$activity_level', NOW())";
        
        if ($conn->query($insert_query)) {
            $_SESSION['success'] = "Registration successful! Please login.";
            header("Location: index.php");
            exit();
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}

require_once 'includes/header.php';
?>

<div class="form-container">
    <h2 class="form-title">Create Your Account</h2>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label class="form-label" for="full_name">Full Name</label>
            <input type="text" class="form-control" id="full_name" name="full_name" required>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required minlength="6">
            <small>Minimum 6 characters</small>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="confirm_password">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            <small id="password-match" style="display: none; color: red;">Passwords do not match!</small>
        </div>
        
        <hr style="margin: 25px 0; border: none; border-top: 1px solid #dee2e6;">
        
        <h3 style="margin-bottom: 20px;">Personal Information (Optional)</h3>
        <p style="margin-bottom: 20px; color: #666;">Fill these now or complete later in your profile.</p>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label class="form-label" for="age">Age</label>
                <input type="number" class="form-control" id="age" name="age" min="15" max="100">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="gender">Gender</label>
                <select class="form-control" id="gender" name="gender">
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label class="form-label" for="height">Height (cm)</label>
                <input type="number" class="form-control" id="height" name="height" step="0.1" min="100" max="250">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="current_weight">Current Weight (kg)</label>
                <input type="number" class="form-control" id="current_weight" name="current_weight" step="0.1" min="30" max="200">
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="goal">Fitness Goal</label>
            <select class="form-control" id="goal" name="goal">
                <option value="">Select Goal</option>
                <option value="weight_loss">Weight Loss</option>
                <option value="muscle_gain">Muscle Gain</option>
                <option value="bulking">Bulking</option>
                <option value="endurance">Endurance</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="activity_level">Activity Level</label>
            <select class="form-control" id="activity_level" name="activity_level">
                <option value="">Select Activity Level</option>
                <option value="sedentary">Sedentary (little or no exercise)</option>
                <option value="light">Light (exercise 1-3 days/week)</option>
                <option value="moderate">Moderate (exercise 3-5 days/week)</option>
                <option value="active">Active (exercise 6-7 days/week)</option>
                <option value="very_active">Very Active (intense exercise daily)</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-block">Register</button>
        
        <div style="text-align: center; margin-top: 20px;">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const passwordMatch = document.getElementById('password-match');
    
    function checkPasswordMatch() {
        if (password.value !== confirmPassword.value) {
            passwordMatch.style.display = 'block';
            confirmPassword.style.borderColor = 'red';
        } else {
            passwordMatch.style.display = 'none';
            confirmPassword.style.borderColor = '';
        }
    }
    
    password.addEventListener('input', checkPasswordMatch);
    confirmPassword.addEventListener('input', checkPasswordMatch);
});
</script>

<?php
require_once 'includes/footer.php';
?>