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

require_once 'includes/header.php';
?>

<div class="container">
    <h1>Progress Details</h1>
    
    <a href="progress.php" class="btn" style="margin-bottom: 20px;">
        <i class="fas fa-arrow-left"></i> Back to Progress
    </a>
    
    <div style="background: white; padding: 30px; border-radius: 12px; margin-top: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2><?php echo date('F j, Y', strtotime($progress['date'])); ?></h2>
            <div>
                <a href="edit_progress.php?id=<?php echo $progress['id']; ?>" class="btn">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <button onclick="deleteProgress(<?php echo $progress['id']; ?>)" class="btn btn-accent">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px;">
            <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                <h3>Weight</h3>
                <div style="font-size: 2.5rem; font-weight: bold; color: var(--primary);">
                    <?php echo $progress['weight']; ?> kg
                </div>
            </div>
            
            <?php if($progress['body_fat']): ?>
                <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                    <h3>Body Fat</h3>
                    <div style="font-size: 2.5rem; font-weight: bold; color: var(--accent);">
                        <?php echo $progress['body_fat']; ?>%
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if($progress['chest_cm'] || $progress['waist_cm'] || $progress['hips_cm']): ?>
            <h3 style="margin-top: 30px; margin-bottom: 15px;">Body Measurements</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <?php if($progress['chest_cm']): ?>
                    <div style="text-align: center; padding: 15px; background: #e8f4fd; border-radius: 8px;">
                        <h4>Chest</h4>
                        <div style="font-size: 1.8rem; font-weight: bold;">
                            <?php echo $progress['chest_cm']; ?> cm
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if($progress['waist_cm']): ?>
                    <div style="text-align: center; padding: 15px; background: #e8f4fd; border-radius: 8px;">
                        <h4>Waist</h4>
                        <div style="font-size: 1.8rem; font-weight: bold;">
                            <?php echo $progress['waist_cm']; ?> cm
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if($progress['hips_cm']): ?>
                    <div style="text-align: center; padding: 15px; background: #e8f4fd; border-radius: 8px;">
                        <h4>Hips</h4>
                        <div style="font-size: 1.8rem; font-weight: bold;">
                            <?php echo $progress['hips_cm']; ?> cm
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if($progress['notes']): ?>
            <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                <h3>Notes</h3>
                <p style="margin-top: 10px; font-size: 1.1rem; line-height: 1.6;">
                    <?php echo nl2br(htmlspecialchars($progress['notes'])); ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function deleteProgress(progressId) {
    if (confirm('Are you sure you want to delete this progress entry?')) {
        window.location.href = 'delete_progress.php?id=' + progressId;
    }
}
</script>

<?php
require_once 'includes/footer.php';
?>