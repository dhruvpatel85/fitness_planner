<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$progress_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get progress details
$query = "SELECT * FROM progress WHERE id = $progress_id AND user_id = $user_id";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    header("Location: progress.php");
    exit();
}

$progress = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $conn->real_escape_string($_POST['date']);
    $weight = floatval($_POST['weight']);
    $body_fat = isset($_POST['body_fat']) ? floatval($_POST['body_fat']) : null;
    $chest = isset($_POST['chest']) ? floatval($_POST['chest']) : null;
    $waist = isset($_POST['waist']) ? floatval($_POST['waist']) : null;
    $hips = isset($_POST['hips']) ? floatval($_POST['hips']) : null;
    $notes = $conn->real_escape_string($_POST['notes']);
    
    // Check if another entry already exists for this date (excluding current)
    $check_query = "SELECT id FROM progress WHERE user_id = $user_id AND date = '$date' AND id != $progress_id";
    $check_result = $conn->query($check_query);
    
    if ($check_result->num_rows > 0) {
        $error = "You already have another progress entry for this date.";
    } else {
        $update_query = "UPDATE progress SET 
                        date = '$date',
                        weight = $weight,
                        body_fat = $body_fat,
                        chest_cm = $chest,
                        waist_cm = $waist,
                        hips_cm = $hips,
                        notes = '$notes'
                        WHERE id = $progress_id AND user_id = $user_id";
        
        if ($conn->query($update_query)) {
            // Update user's current weight if this is the latest entry
            $latest_query = "SELECT date FROM progress WHERE user_id = $user_id ORDER BY date DESC LIMIT 1";
            $latest_result = $conn->query($latest_query);
            $latest = $latest_result->fetch_assoc();
            
            if ($latest['date'] == $date) {
                $update_user_query = "UPDATE users SET current_weight = $weight WHERE id = $user_id";
                $conn->query($update_user_query);
            }
            
            $_SESSION['success'] = "Progress updated successfully!";
            header("Location: view_progress.php?id=$progress_id");
            exit();
        } else {
            $error = "Error updating progress: " . $conn->error;
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container">
    <h1>Edit Progress Entry</h1>
    
    <a href="view_progress.php?id=<?php echo $progress_id; ?>" class="btn" style="margin-bottom: 20px;">
        <i class="fas fa-arrow-left"></i> Back to Details
    </a>
    
    <div class="form-container">
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label" for="date">Date</label>
                <input type="date" class="form-control" id="date" name="date" 
                       value="<?php echo $progress['date']; ?>" required>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label class="form-label" for="weight">Weight (kg)</label>
                    <input type="number" class="form-control" id="weight" name="weight" 
                           step="0.1" min="30" max="200" value="<?php echo $progress['weight']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="body_fat">Body Fat %</label>
                    <input type="number" class="form-control" id="body_fat" name="body_fat" 
                           step="0.1" min="5" max="50" value="<?php echo $progress['body_fat']; ?>">
                </div>
            </div>
            
            <h3 style="margin: 20px 0 10px 0;">Measurements (cm)</h3>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                <div class="form-group">
                    <label class="form-label" for="chest">Chest</label>
                    <input type="number" class="form-control" id="chest" name="chest" 
                           step="0.1" min="50" max="150" value="<?php echo $progress['chest_cm']; ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="waist">Waist</label>
                    <input type="number" class="form-control" id="waist" name="waist" 
                           step="0.1" min="50" max="150" value="<?php echo $progress['waist_cm']; ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="hips">Hips</label>
                    <input type="number" class="form-control" id="hips" name="hips" 
                           step="0.1" min="50" max="150" value="<?php echo $progress['hips_cm']; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="notes">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($progress['notes']); ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-block btn-accent">
                <i class="fas fa-save"></i> Update Progress
            </button>
        </form>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>