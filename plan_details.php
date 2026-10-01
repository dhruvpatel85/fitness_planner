<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$plan_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get plan details
$query = "SELECT * FROM plans WHERE id = $plan_id AND user_id = $user_id";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    header("Location: my_plans.php");
    exit();
}

$plan = $result->fetch_assoc();

// Decode JSON data
$workout_days = json_decode($plan['workout_days'], true);
$meals = json_decode($plan['meals'], true);
$exercises = json_decode($plan['exercises'], true);

require_once 'includes/header.php';
?>

<div class="container">
    <h1>Plan Details: <?php echo htmlspecialchars($plan['plan_name']); ?></h1>
    
    <div style="display: flex; gap: 15px; margin-bottom: 30px;">
        <a href="my_plans.php" class="btn">
            <i class="fas fa-arrow-left"></i> Back to My Plans
        </a>
        <a href="plan_generator.php?copy=<?php echo $plan['id']; ?>" class="btn">
            <i class="fas fa-copy"></i> Duplicate Plan
        </a>
        <button onclick="deletePlan(<?php echo $plan['id']; ?>)" class="btn btn-accent">
            <i class="fas fa-trash"></i> Delete Plan
        </button>
    </div>
    
    <div style="background: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h2 style="margin-bottom: 10px;"><?php echo htmlspecialchars($plan['plan_name']); ?></h2>
                <div style="display: flex; gap: 15px;">
                    <span style="background: #e8f4fd; padding: 5px 15px; border-radius: 20px; font-weight: 500;">
                        <?php echo ucfirst(str_replace('_', ' ', $plan['goal'])); ?>
                    </span>
                    <span style="background: #f0f7ff; padding: 5px 15px; border-radius: 20px;">
                        <?php echo $plan['duration_weeks']; ?> Weeks
                    </span>
                    <span style="color: #666;">
                        Created: <?php echo date('M d, Y', strtotime($plan['created_at'])); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
            <div>
                <h3 style="margin-bottom: 20px; color: var(--primary);">
                    <i class="fas fa-utensils"></i> Nutrition Plan
                </h3>
                
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                    <div style="text-align: center; margin-bottom: 25px;">
                        <div style="font-size: 2.5rem; font-weight: bold; color: var(--primary);">
                            <?php echo $plan['daily_calories']; ?>
                        </div>
                        <div style="color: #666;">Daily Calories</div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; text-align: center;">
                        <div>
                            <div style="font-size: 1.5rem; font-weight: bold; color: var(--success);">
                                <?php echo $plan['protein_grams']; ?>g
                            </div>
                            <div style="font-size: 0.9rem; color: #666;">Protein</div>
                        </div>
                        <div>
                            <div style="font-size: 1.5rem; font-weight: bold; color: var(--warning);">
                                <?php echo $plan['carbs_grams']; ?>g
                            </div>
                            <div style="font-size: 0.9rem; color: #666;">Carbs</div>
                        </div>
                        <div>
                            <div style="font-size: 1.5rem; font-weight: bold; color: var(--accent);">
                                <?php echo $plan['fat_grams']; ?>g
                            </div>
                            <div style="font-size: 0.9rem; color: #666;">Fat</div>
                        </div>
                    </div>
                </div>
                
                <h4 style="margin-top: 30px; margin-bottom: 15px;">Daily Meal Plan</h4>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                    <?php if(is_array($meals)): ?>
                        <?php foreach($meals as $meal => $description): ?>
                            <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #dee2e6;">
                                <strong style="color: var(--primary);"><?php echo ucfirst($meal); ?>:</strong>
                                <p style="margin-top: 5px;"><?php echo $description; ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p><?php echo $meals; ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div>
                <h3 style="margin-bottom: 20px; color: var(--primary);">
                    <i class="fas fa-running"></i> Workout Plan
                </h3>
                
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 25px;">
                    <h4>Workout Schedule</h4>
                    <p style="margin-top: 10px;">
                        <strong>Days per week:</strong> 
                        <?php if(is_array($workout_days)): ?>
                            <?php echo count($workout_days); ?> days (<?php echo implode(', ', $workout_days); ?>)
                        <?php else: ?>
                            <?php echo $workout_days; ?>
                        <?php endif; ?>
                    </p>
                    <p><strong>Duration:</strong> <?php echo $plan['duration_weeks']; ?> weeks</p>
                </div>
                
                <h4>Exercise Program</h4>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                    <?php if(is_array($exercises)): ?>
                        <?php foreach($exercises as $category => $exercise_list): ?>
                            <div style="margin-bottom: 20px;">
                                <strong style="color: var(--dark); display: block; margin-bottom: 8px;">
                                    <?php echo ucfirst(str_replace('_', ' ', $category)); ?>:
                                </strong>
                                <ul style="padding-left: 20px; margin: 0;">
                                    <?php if(is_array($exercise_list)): ?>
                                        <?php foreach($exercise_list as $exercise): ?>
                                            <li><?php echo $exercise; ?></li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li><?php echo $exercise_list; ?></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p><?php echo $exercises; ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #eee;">
            <h3>Plan Notes</h3>
            <p style="margin-top: 15px; color: #666;">
                This plan was generated for <?php echo ucfirst(str_replace('_', ' ', $plan['goal'])); ?> goal. 
                Follow the nutrition and workout guidelines consistently for best results.
            </p>
            
            <div style="display: flex; gap: 15px; margin-top: 25px;">
                <button onclick="startPlan(<?php echo $plan['id']; ?>)" class="btn btn-accent">
                    <i class="fas fa-play"></i> Start This Plan
                </button>
                <button onclick="printPlan()" class="btn">
                    <i class="fas fa-print"></i> Print Plan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function deletePlan(planId) {
    if (confirm('Are you sure you want to delete this plan? This action cannot be undone.')) {
        window.location.href = 'delete_plan.php?id=' + planId;
    }
}

function startPlan(planId) {
    if (confirm('Set this as your active plan? Your progress will be tracked against this plan.')) {
        // In a real app, you would save this to user's active_plan_id
        alert('Plan activated! You can now track your progress against this plan.');
    }
}

function printPlan() {
    window.print();
}
</script>

<style>
@media print {
    .navbar, .footer, .btn {
        display: none !important;
    }
    
    .container {
        padding: 0 !important;
        max-width: 100% !important;
    }
    
    body {
        background: white !important;
        color: black !important;
    }
}
</style>

<?php
require_once 'includes/footer.php';
?>