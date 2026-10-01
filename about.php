<?php
require_once 'config/database.php';
require_once 'includes/header.php';
?>

<div class="container">
    <div style="background: white; padding: 40px; border-radius: 15px; margin: 30px 0;">
        <h1>About FitnessPlanner</h1>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 30px;">
            <div>
                <h2>Our Mission</h2>
                <p style="margin-top: 15px; font-size: 1.1rem; line-height: 1.6;">
                    FitnessPlanner was created with one goal in mind: to make personalized fitness accessible to everyone. 
                    We believe that everyone deserves a fitness plan tailored to their unique goals, body type, and lifestyle.
                </p>
                
                <h2 style="margin-top: 30px;">How It Works</h2>
                <p style="margin-top: 15px; font-size: 1.1rem; line-height: 1.6;">
                    Our intelligent system calculates your daily calorie needs based on your goals (weight loss, muscle gain, etc.), 
                    activity level, and personal preferences. Then, it generates a complete nutrition and workout plan that you can follow.
                </p>
            </div>
            
            <div>
                <div style="background: #f8f9fa; padding: 25px; border-radius: 12px;">
                    <h3><i class="fas fa-check-circle"></i> Key Features</h3>
                    <ul style="margin-top: 15px; padding-left: 20px; line-height: 2;">
                        <li>Personalized diet plans</li>
                        <li>Custom workout routines</li>
                        <li>Progress tracking</li>
                        <li>BMI calculator</li>
                        <li>Nutritional breakdown</li>
                        <li>Workout schedule</li>
                        <li>Mobile-friendly design</li>
                        <li>Progress charts and analytics</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 50px;">
            <h2>Meet Our Team</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-top: 30px;">
                <div style="text-align: center; padding: 25px; background: #f8f9fa; border-radius: 12px;">
                    <div style="width: 100px; height: 100px; background: #e8f4fd; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user" style="font-size: 2.5rem; color: var(--primary);"></i>
                    </div>
                    <h3>Alex Johnson</h3>
                    <p style="color: #666; margin-top: 5px;">Certified Nutritionist</p>
                </div>
                
                <div style="text-align: center; padding: 25px; background: #f8f9fa; border-radius: 12px;">
                    <div style="width: 100px; height: 100px; background: #e8f4fd; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user" style="font-size: 2.5rem; color: var(--primary);"></i>
                    </div>
                    <h3>Sarah Miller</h3>
                    <p style="color: #666; margin-top: 5px;">Personal Trainer</p>
                </div>
                
                <div style="text-align: center; padding: 25px; background: #f8f9fa; border-radius: 12px;">
                    <div style="width: 100px; height: 100px; background: #e8f4fd; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user" style="font-size: 2.5rem; color: var(--primary);"></i>
                    </div>
                    <h3>Michael Chen</h3>
                    <p style="color: #666; margin-top: 5px;">Fitness Coach</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>