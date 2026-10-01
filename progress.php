<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $conn->real_escape_string($_POST['date']);
    $weight = floatval($_POST['weight']);
    $body_fat = isset($_POST['body_fat']) ? floatval($_POST['body_fat']) : null;
    $chest = isset($_POST['chest']) ? floatval($_POST['chest']) : null;
    $waist = isset($_POST['waist']) ? floatval($_POST['waist']) : null;
    $hips = isset($_POST['hips']) ? floatval($_POST['hips']) : null;
    $notes = $conn->real_escape_string($_POST['notes']);
    
    // Check if entry already exists for this date
    $check_query = "SELECT id FROM progress WHERE user_id = $user_id AND date = '$date'";
    $check_result = $conn->query($check_query);
    
    if ($check_result->num_rows > 0) {
        $error = "You already have a progress entry for this date. Please edit the existing entry.";
    } else {
        $insert_query = "INSERT INTO progress (user_id, date, weight, body_fat, chest_cm, waist_cm, hips_cm, notes) 
                         VALUES ($user_id, '$date', $weight, $body_fat, $chest, $waist, $hips, '$notes')";
        
        if ($conn->query($insert_query)) {
            // Update user's current weight if this is the latest entry
            $update_user_query = "UPDATE users SET current_weight = $weight WHERE id = $user_id";
            $conn->query($update_user_query);
            
            $_SESSION['success'] = "Progress recorded successfully!";
            header("Location: progress.php");
            exit();
        } else {
            $error = "Error recording progress: " . $conn->error;
        }
    }
}

// Get progress history
$progress_query = "SELECT * FROM progress WHERE user_id = $user_id ORDER BY date DESC";
$progress_result = $conn->query($progress_query);

// Get user's weight goal
$user_query = "SELECT current_weight, goal FROM users WHERE id = $user_id";
$user_result = $conn->query($user_query);
$user = $user_result->fetch_assoc();

require_once 'includes/header.php';
?>

<div class="container">
    <h1>Progress Tracking</h1>
    
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 30px;">
        <div>
            <h2>Record New Progress</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="date">Date</label>
                    <input type="date" class="form-control" id="date" name="date" 
                           value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label class="form-label" for="weight">Weight (kg)</label>
                        <input type="number" class="form-control" id="weight" name="weight" 
                               step="0.1" min="30" max="200" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="body_fat">Body Fat %</label>
                        <input type="number" class="form-control" id="body_fat" name="body_fat" 
                               step="0.1" min="5" max="50">
                    </div>
                </div>
                
                <h3 style="margin: 20px 0 10px 0;">Measurements (cm)</h3>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                    <div class="form-group">
                        <label class="form-label" for="chest">Chest</label>
                        <input type="number" class="form-control" id="chest" name="chest" 
                               step="0.1" min="50" max="150">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="waist">Waist</label>
                        <input type="number" class="form-control" id="waist" name="waist" 
                               step="0.1" min="50" max="150">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="hips">Hips</label>
                        <input type="number" class="form-control" id="hips" name="hips" 
                               step="0.1" min="50" max="150">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3" 
                              placeholder="How are you feeling? Any changes in energy, strength, or mood?"></textarea>
                </div>
                
                <button type="submit" class="btn btn-block btn-accent">
                    <i class="fas fa-save"></i> Save Progress
                </button>
            </form>
        </div>
        
        <div>
            <h2>Progress History</h2>
            
            <?php if($progress_result->num_rows > 0): ?>
                <div style="max-height: 500px; overflow-y: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                                <th style="padding: 12px; text-align: left;">Date</th>
                                <th style="padding: 12px; text-align: left;">Weight</th>
                                <th style="padding: 12px; text-align: left;">Body Fat</th>
                                <th style="padding: 12px; text-align: left;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($progress = $progress_result->fetch_assoc()): ?>
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 12px;"><?php echo date('M d, Y', strtotime($progress['date'])); ?></td>
                                    <td style="padding: 12px; font-weight: 500;">
                                        <?php echo $progress['weight']; ?> kg
                                        <?php if($user['current_weight']): ?>
                                            <?php 
                                            $diff = $progress['weight'] - $user['current_weight'];
                                            if($diff > 0): ?>
                                                <span style="color: var(--danger); font-size: 0.9rem;">+<?php echo $diff; ?> kg</span>
                                            <?php elseif($diff < 0): ?>
                                                <span style="color: var(--success); font-size: 0.9rem;"><?php echo $diff; ?> kg</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 12px;">
                                        <?php echo $progress['body_fat'] ? $progress['body_fat'] . '%' : '-'; ?>
                                    </td>
                                    <td style="padding: 12px;">
                                        <button onclick="viewProgress(<?php echo $progress['id']; ?>)" 
                                                class="btn" style="padding: 5px 10px; font-size: 0.9rem;">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button onclick="deleteProgress(<?php echo $progress['id']; ?>)" 
                                                class="btn btn-accent" style="padding: 5px 10px; font-size: 0.9rem;">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if($progress_result->num_rows > 1): ?>
                    <div style="margin-top: 30px;">
                        <h3>Progress Chart</h3>
                        <div id="progressChart" style="height: 300px; background: #f8f9fa; border-radius: 10px; padding: 20px;">
                            <p style="text-align: center; color: #666;">Chart will appear here when enough data is available</p>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 10px;">
                    <i class="fas fa-chart-line" style="font-size: 3rem; color: #ddd; margin-bottom: 20px;"></i>
                    <h3>No Progress Records Yet</h3>
                    <p>Start tracking your progress to see your fitness journey unfold!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if($user['goal']): ?>
        <div style="margin-top: 40px; background: white; padding: 25px; border-radius: 12px;">
            <h2>Goal Progress</h2>
            <p>Your current goal: <strong><?php echo ucfirst(str_replace('_', ' ', $user['goal'])); ?></strong></p>
            
            <?php if($user['current_weight']): ?>
                <div style="margin-top: 20px;">
                    <?php
                    // In a real app, you would calculate progress based on user's starting weight and goal
                    $progress_percentage = 0;
                    if ($user['goal'] === 'weight_loss') {
                        $progress_percentage = 30; // Example value
                    } elseif ($user['goal'] === 'muscle_gain') {
                        $progress_percentage = 40; // Example value
                    }
                    ?>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span>Progress towards goal</span>
                        <span><?php echo $progress_percentage; ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $progress_percentage; ?>%"></div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning" style="margin-top: 15px;">
                    <p>Set your current weight to track progress towards your goal.</p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function viewProgress(progressId) {
    window.location.href = 'view_progress.php?id=' + progressId;
}

function deleteProgress(progressId) {
    if (confirm('Are you sure you want to delete this progress entry?')) {
        window.location.href = 'delete_progress.php?id=' + progressId;
    }
}

// Initialize date picker to today
document.getElementById('date').valueAsDate = new Date();
</script>

<?php
require_once 'includes/footer.php';
?>