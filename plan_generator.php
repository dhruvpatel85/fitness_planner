<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user data for calculations
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = $conn->query($user_query);
$user = $user_result->fetch_assoc();

$plan_generated = false;
$plan_data = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $goal = $conn->real_escape_string($_POST['goal']);
    $duration = intval($_POST['duration']);
    $preference = $conn->real_escape_string($_POST['preference']);
    
    // Calculate BMR and daily calories based on goal
    $height = $user['height'] ?: 170;
    $weight = $user['current_weight'] ?: 70;
    $age = $user['age'] ?: 30;
    $gender = $user['gender'] ?: 'male';
    
    // BMR calculation (Mifflin-St Jeor Equation)
    if ($gender === 'male') {
        $bmr = 10 * $weight + 6.25 * $height - 5 * $age + 5;
    } else {
        $bmr = 10 * $weight + 6.25 * $height - 5 * $age - 161;
    }
    
    // Activity multiplier
    $activity_multipliers = [
        'sedentary' => 1.2,
        'light' => 1.375,
        'moderate' => 1.55,
        'active' => 1.725,
        'very_active' => 1.9
    ];
    
    $activity_level = $user['activity_level'] ?: 'moderate';
    $tdee = $bmr * ($activity_multipliers[$activity_level] ?? 1.55);
    
    // Adjust calories based on goal
    switch ($goal) {
        case 'weight_loss':
            $daily_calories = $tdee - 500;
            break;
        case 'muscle_gain':
            $daily_calories = $tdee + 300;
            break;
        case 'bulking':
            $daily_calories = $tdee + 500;
            break;
        case 'endurance':
            $daily_calories = $tdee;
            break;
        default: // maintenance
            $daily_calories = $tdee;
    }
    
    // Macronutrient distribution
    switch ($goal) {
        case 'weight_loss':
            $protein_ratio = 0.30;
            $carbs_ratio = 0.40;
            $fat_ratio = 0.30;
            break;
        case 'muscle_gain':
        case 'bulking':
            $protein_ratio = 0.35;
            $carbs_ratio = 0.45;
            $fat_ratio = 0.20;
            break;
        default:
            $protein_ratio = 0.25;
            $carbs_ratio = 0.50;
            $fat_ratio = 0.25;
    }
    
    $protein_grams = round(($daily_calories * $protein_ratio) / 4);
    $carbs_grams = round(($daily_calories * $carbs_ratio) / 4);
    $fat_grams = round(($daily_calories * $fat_ratio) / 9);
    
    // Generate workout days based on preference
    $workout_days = [];
    if ($preference === 'beginner') {
        $workout_days = ['Monday', 'Wednesday', 'Friday'];
    } elseif ($preference === 'intermediate') {
        $workout_days = ['Monday', 'Tuesday', 'Thursday', 'Friday'];
    } else { // advanced
        $workout_days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    }
    
    // Generate meal plan based on preference
    $meals = [];
    if ($preference === 'vegetarian') {
        $meals = [
            'breakfast' => 'Oatmeal with fruits and nuts',
            'lunch' => 'Vegetable stir-fry with tofu and brown rice',
            'dinner' => 'Lentil curry with quinoa',
            'snacks' => 'Greek yogurt, Protein shake'
        ];
    } elseif ($preference === 'high_protein') {
        $meals = [
            'breakfast' => 'Egg whites omelette with vegetables',
            'lunch' => 'Grilled chicken breast with sweet potato',
            'dinner' => 'Salmon with broccoli and quinoa',
            'snacks' => 'Protein shake, Cottage cheese'
        ];
    } else { // balanced
        $meals = [
            'breakfast' => 'Whole grain toast with avocado and eggs',
            'lunch' => 'Grilled chicken salad with mixed vegetables',
            'dinner' => 'Lean beef with roasted vegetables',
            'snacks' => 'Fruits, Nuts'
        ];
    }
    
    // Generate exercises based on goal
    $exercises = [];
    if ($goal === 'weight_loss') {
        $exercises = [
            'cardio' => ['Running', 'Cycling', 'Jump rope', 'HIIT'],
            'strength' => ['Squats', 'Push-ups', 'Lunges', 'Plank']
        ];
    } elseif ($goal === 'muscle_gain' || $goal === 'bulking') {
        $exercises = [
            'chest' => ['Bench press', 'Incline press', 'Chest flyes'],
            'back' => ['Pull-ups', 'Rows', 'Lat pulldowns'],
            'legs' => ['Squats', 'Deadlifts', 'Leg press'],
            'shoulders' => ['Overhead press', 'Lateral raises', 'Front raises'],
            'arms' => ['Bicep curls', 'Tricep extensions', 'Hammer curls']
        ];
    } else { // endurance
        $exercises = [
            'cardio' => ['Long distance running', 'Swimming', 'Cycling', 'Rowing'],
            'strength' => ['Bodyweight exercises', 'Light weights with high reps']
        ];
    }
    
    $plan_data = [
        'goal' => $goal,
        'duration' => $duration,
        'daily_calories' => round($daily_calories),
        'protein_grams' => $protein_grams,
        'carbs_grams' => $carbs_grams,
        'fat_grams' => $fat_grams,
        'workout_days' => $workout_days,
        'meals' => $meals,
        'exercises' => $exercises,
        'tdee' => round($tdee),
        'bmr' => round($bmr)
    ];
    
    $plan_generated = true;
}

require_once 'includes/header.php';
?>

<div class="form-container">
    <h2 class="form-title">Create Your Personalized Fitness Plan</h2>
    
    <?php if(!$user['current_weight'] || !$user['height']): ?>
        <div class="alert alert-warning">
            <h3><i class="fas fa-exclamation-triangle"></i> Complete Your Profile First</h3>
            <p>Please update your weight, height, and age in your profile to get accurate plan calculations.</p>
            <a href="profile.php" class="btn">Update Profile</a>
        </div>
    <?php endif; ?>
    
    <?php if(!$plan_generated): ?>
        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label">Select Your Goal</label>
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
                        <p>Gain size</p>
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
                <label class="form-label" for="duration">Plan Duration (Weeks)</label>
                <select class="form-control" id="duration" name="duration" required>
                    <option value="4">4 Weeks</option>
                    <option value="8" selected>8 Weeks</option>
                    <option value="12">12 Weeks</option>
                    <option value="16">16 Weeks</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="preference">Diet Preference</label>
                <select class="form-control" id="preference" name="preference" required>
                    <option value="balanced">Balanced Diet</option>
                    <option value="high_protein">High Protein</option>
                    <option value="vegetarian">Vegetarian</option>
                    <option value="beginner">Beginner Friendly</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Your Current Stats</label>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                    <p><strong>Height:</strong> <?php echo $user['height'] ? $user['height'] . ' cm' : 'Not set'; ?></p>
                    <p><strong>Weight:</strong> <?php echo $user['current_weight'] ? $user['current_weight'] . ' kg' : 'Not set'; ?></p>
                    <p><strong>Age:</strong> <?php echo $user['age'] ? $user['age'] . ' years' : 'Not set'; ?></p>
                    <p><strong>Activity Level:</strong> <?php echo $user['activity_level'] ? ucfirst($user['activity_level']) : 'Not set'; ?></p>
                </div>
            </div>
            
            <button type="submit" class="btn btn-block btn-accent">
                <i class="fas fa-magic"></i> Generate Plan
            </button>
        </form>
    <?php else: ?>
        <div class="plan-results">
            <div class="alert alert-success">
                <h3><i class="fas fa-check-circle"></i> Plan Generated Successfully!</h3>
                <p>Your personalized <?php echo $plan_data['duration']; ?>-week plan for <?php echo ucfirst(str_replace('_', ' ', $plan_data['goal'])); ?> is ready.</p>
            </div>
            
            <div style="background: white; padding: 20px; border-radius: 10px; margin: 20px 0;">
                <h3>Nutrition Plan</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
                    <div style="text-align: center; padding: 15px; background: #e8f4fd; border-radius: 8px;">
                        <h4>Daily Calories</h4>
                        <div style="font-size: 2rem; font-weight: bold; color: var(--primary);">
                            <?php echo $plan_data['daily_calories']; ?>
                        </div>
                        <small>Calories/day</small>
                    </div>
                    
                    <div style="text-align: center; padding: 15px; background: #e8f4fd; border-radius: 8px;">
                        <h4>Protein</h4>
                        <div style="font-size: 2rem; font-weight: bold; color: var(--success);">
                            <?php echo $plan_data['protein_grams']; ?>
                        </div>
                        <small>Grams/day</small>
                    </div>
                    
                    <div style="text-align: center; padding: 15px; background: #e8f4fd; border-radius: 8px;">
                        <h4>Carbs</h4>
                        <div style="font-size: 2rem; font-weight: bold; color: var(--warning);">
                            <?php echo $plan_data['carbs_grams']; ?>
                        </div>
                        <small>Grams/day</small>
                    </div>
                    
                    <div style="text-align: center; padding: 15px; background: #e8f4fd; border-radius: 8px;">
                        <h4>Fat</h4>
                        <div style="font-size: 2rem; font-weight: bold; color: var(--accent);">
                            <?php echo $plan_data['fat_grams']; ?>
                        </div>
                        <small>Grams/day</small>
                    </div>
                </div>
                
                <h4>Sample Meal Plan</h4>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0;">
                    <?php foreach($plan_data['meals'] as $meal => $description): ?>
                        <p><strong><?php echo ucfirst($meal); ?>:</strong> <?php echo $description; ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div style="background: white; padding: 20px; border-radius: 10px; margin: 20px 0;">
                <h3>Workout Plan</h3>
                <p><strong>Workout Days:</strong> <?php echo implode(', ', $plan_data['workout_days']); ?></p>
                
                <h4>Exercises</h4>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0;">
                    <?php foreach($plan_data['exercises'] as $category => $exercise_list): ?>
                        <p><strong><?php echo ucfirst($category); ?>:</strong> <?php echo implode(', ', $exercise_list); ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div style="background: #e8f4fd; padding: 20px; border-radius: 10px; margin: 20px 0;">
                <h4>Your Calculations</h4>
                <p><strong>BMR (Basal Metabolic Rate):</strong> <?php echo $plan_data['bmr']; ?> calories/day</p>
                <p><strong>TDEE (Total Daily Energy Expenditure):</strong> <?php echo $plan_data['tdee']; ?> calories/day</p>
                <p><small>BMR is the calories your body needs at rest. TDEE includes your activity level.</small></p>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <button id="savePlan" class="btn btn-accent" style="padding: 15px 40px;">
                    <i class="fas fa-save"></i> Save This Plan
                </button>
                <a href="plan_generator.php" class="btn" style="margin-left: 10px;">
                    <i class="fas fa-redo"></i> Generate New Plan
                </a>
            </div>
        </div>
        
        <script>
        document.getElementById('savePlan').addEventListener('click', function() {
            const planName = prompt('Enter a name for this plan:', 'My <?php echo ucfirst(str_replace('_', ' ', $plan_data['goal'])); ?> Plan');
            
            if (planName) {
                const formData = new FormData();
                formData.append('save_plan', 'true');
                formData.append('plan_name', planName);
                formData.append('goal', '<?php echo $plan_data['goal']; ?>');
                formData.append('duration', <?php echo $plan_data['duration']; ?>);
                formData.append('daily_calories', <?php echo $plan_data['daily_calories']; ?>);
                formData.append('protein_grams', <?php echo $plan_data['protein_grams']; ?>);
                formData.append('carbs_grams', <?php echo $plan_data['carbs_grams']; ?>);
                formData.append('fat_grams', <?php echo $plan_data['fat_grams']; ?>);
                formData.append('workout_days', JSON.stringify(<?php echo json_encode($plan_data['workout_days']); ?>));
                formData.append('meals', JSON.stringify(<?php echo json_encode($plan_data['meals']); ?>));
                formData.append('exercises', JSON.stringify(<?php echo json_encode($plan_data['exercises']); ?>));
                
                fetch('save_plan.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Plan saved successfully!');
                        window.location.href = 'my_plans.php';
                    } else {
                        alert('Error saving plan: ' + data.error);
                    }
                })
                .catch(error => {
                    alert('Error saving plan');
                });
            }
        });
        </script>
    <?php endif; ?>
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
});
</script>

<?php
require_once 'includes/footer.php';
?>