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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $weight = floatval($_POST['weight']);
    $date = $conn->real_escape_string($_POST['date']);
    $notes = $conn->real_escape_string($_POST['notes']);
    
    // Update user's current weight
    $update_user_query = "UPDATE users SET current_weight = $weight WHERE id = $user_id";
    
    // Check if entry exists for today
    $check_query = "SELECT id FROM progress WHERE user_id = $user_id AND date = '$date'";
    $check_result = $conn->query($check_query);
    
    if ($check_result->num_rows > 0) {
        // Update existing entry
        $update_query = "UPDATE progress SET weight = $weight, notes = '$notes' WHERE user_id = $user_id AND date = '$date'";
    } else {
        // Insert new entry
        $update_query = "INSERT INTO progress (user_id, date, weight, notes) VALUES ($user_id, '$date', $weight, '$notes')";
    }
    
    if ($conn->query($update_user_query) && $conn->query($update_query)) {
        $success = "Weight updated successfully!";
    } else {
        $error = "Error updating weight: " . $conn->error;
    }
}

require_once 'includes/header.php';
?>

<div class="container">
    <h1>Quick Weight Update</h1>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 30px;">
        <div>
            <?php if($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                    <div style="margin-top: 15px;">
                        <a href="progress.php" class="btn">View Full Progress</a>
                        <a href="dashboard.php" class="btn">Back to Dashboard</a>
                    </div>
                </div>
            <?php else: ?>
                <?php if($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div style="background: white; padding: 25px; border-radius: 12px;">
                    <h2>Update Your Weight</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label class="form-label" for="date">Date</label>
                            <input type="date" class="form-control" id="date" name="date" 
                                   value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="weight">Current Weight (kg)</label>
                            <input type="number" class="form-control" id="weight" name="weight" 
                                   step="0.1" min="30" max="200" value="<?php echo $user['current_weight'] ?? ''; ?>" required>
                            <small>Last recorded: <?php echo $user['current_weight'] ?? 'Not set'; ?> kg</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="notes">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="How are you feeling? Any changes in energy, strength, or mood?"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-block btn-accent">
                            <i class="fas fa-save"></i> Update Weight
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        
        <div>
            <div style="background: white; padding: 25px; border-radius: 12px; margin-bottom: 20px;">
                <h2>Your Current Stats</h2>
                <div style="display: grid; gap: 15px; margin-top: 20px;">
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee;">
                        <span>Height:</span>
                        <strong><?php echo $user['height'] ?? 'Not set'; ?> cm</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee;">
                        <span>Age:</span>
                        <strong><?php echo $user['age'] ?? 'Not set'; ?> years</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee;">
                        <span>Gender:</span>
                        <strong><?php echo ucfirst($user['gender'] ?? 'Not set'); ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee;">
                        <span>Goal:</span>
                        <strong><?php echo ucfirst(str_replace('_', ' ', $user['goal'] ?? 'Not set')); ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 10px 0;">
                        <span>Activity Level:</span>
                        <strong><?php echo ucfirst($user['activity_level'] ?? 'Not set'); ?></strong>
                    </div>
                </div>
                
                <?php if($user['height'] && $user['current_weight']): ?>
                    <div style="margin-top: 25px; padding-top: 20px; border-top: 2px solid #eee;">
                        <h3>Health Metrics</h3>
                        <?php
                        $height_m = $user['height'] / 100;
                        $bmi = $user['current_weight'] / ($height_m * $height_m);
                        ?>
                        <p>Your BMI: <strong><?php echo number_format($bmi, 1); ?></strong></p>
                        <div class="progress-bar" style="margin: 15px 0;">
                            <div class="progress-fill" style="width: <?php echo min(100, ($bmi / 40) * 100); ?>%"></div>
                        </div>
                        <small>
                            <?php 
                            if ($bmi < 18.5) echo 'Underweight';
                            elseif ($bmi < 25) echo 'Healthy weight';
                            elseif ($bmi < 30) echo 'Overweight';
                            else echo 'Obese';
                            ?>
                        </small>
                    </div>
                <?php endif; ?>
            </div>
            
            <div style="background: #f8f9fa; padding: 20px; border-radius: 12px;">
                <h3><i class="fas fa-lightbulb"></i> Tips</h3>
                <ul style="margin-top: 10px; padding-left: 20px;">
                    <li>Weigh yourself at the same time each day (morning is best)</li>
                    <li>Use the same scale for consistency</li>
                    <li>Track weekly averages rather than daily fluctuations</li>
                    <li>Don't worry about small daily changes - focus on the trend</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Set date to today by default
document.getElementById('date').valueAsDate = new Date();
</script>

<?php
require_once 'includes/footer.php';
?>