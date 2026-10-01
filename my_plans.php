<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user's plans
$plans_query = "SELECT * FROM plans WHERE user_id = $user_id ORDER BY created_at DESC";
$plans_result = $conn->query($plans_query);

require_once 'includes/header.php';
?>

<div class="container">
    <h1>My Fitness Plans</h1>
    
    <?php if($plans_result->num_rows > 0): ?>
        <div style="display: grid; gap: 25px; margin-top: 30px;">
            <?php while($plan = $plans_result->fetch_assoc()): 
                $workout_days = json_decode($plan['workout_days'], true);
                $meals = json_decode($plan['meals'], true);
                $exercises = json_decode($plan['exercises'], true);
            ?>
                <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3><?php echo htmlspecialchars($plan['plan_name']); ?></h3>
                        <div>
                            <span style="background: #e8f4fd; padding: 5px 15px; border-radius: 20px; font-weight: 500;">
                                <?php echo ucfirst(str_replace('_', ' ', $plan['goal'])); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 20px;">
                        <div>
                            <h4>Nutrition</h4>
                            <p><strong>Calories:</strong> <?php echo $plan['daily_calories']; ?> kcal/day</p>
                            <p><strong>Protein:</strong> <?php echo $plan['protein_grams']; ?>g</p>
                            <p><strong>Carbs:</strong> <?php echo $plan['carbs_grams']; ?>g</p>
                            <p><strong>Fat:</strong> <?php echo $plan['fat_grams']; ?>g</p>
                        </div>
                        
                        <div>
                            <h4>Workout Schedule</h4>
                            <p><strong>Duration:</strong> <?php echo $plan['duration_weeks']; ?> weeks</p>
                            <p><strong>Days:</strong> 
                                <?php if(is_array($workout_days)): ?>
                                    <?php echo implode(', ', $workout_days); ?>
                                <?php else: ?>
                                    <?php echo $workout_days; ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <div>
                            <h4>Meal Plan</h4>
                            <?php if(is_array($meals)): ?>
                                <?php foreach(array_slice($meals, 0, 3) as $meal => $desc): ?>
                                    <p><small><?php echo ucfirst($meal); ?>: <?php echo substr($desc, 0, 30); ?>...</small></p>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button class="btn" onclick="viewPlanDetails(<?php echo $plan['id']; ?>)">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        <a href="plan_generator.php?copy=<?php echo $plan['id']; ?>" class="btn">
                            <i class="fas fa-copy"></i> Duplicate
                        </a>
                        <button class="btn btn-accent" onclick="startPlan(<?php echo $plan['id']; ?>)">
                            <i class="fas fa-play"></i> Start This Plan
                        </button>
                    </div>
                    
                    <div style="margin-top: 15px; color: #666; font-size: 0.9rem;">
                        Created: <?php echo date('M d, Y', strtotime($plan['created_at'])); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 50px; background: white; border-radius: 12px; margin-top: 30px;">
            <i class="fas fa-clipboard-list" style="font-size: 4rem; color: #ddd; margin-bottom: 20px;"></i>
            <h3>No Plans Yet</h3>
            <p>You haven't created any fitness plans yet.</p>
            <a href="plan_generator.php" class="btn btn-accent" style="margin-top: 20px;">
                <i class="fas fa-plus-circle"></i> Create Your First Plan
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
function viewPlanDetails(planId) {
    window.location.href = 'plan_details.php?id=' + planId;
}

function startPlan(planId) {
    if (confirm('Start this plan? Your progress will be tracked.')) {
        // In a real app, you would set this as the active plan
        alert('Plan started! You can track your progress in the dashboard.');
    }
}
</script>

<?php
require_once 'includes/footer.php';
?>