<?php
require_once 'config/database.php';
require_once 'includes/header.php';
?>

<div class="container">
    <div style="background: white; padding: 40px; border-radius: 15px; margin: 30px 0;">
        <h1>Privacy Policy</h1>
        <p style="margin-top: 10px; color: #666;">Last updated: <?php echo date('F j, Y'); ?></p>
        
        <div style="margin-top: 30px;">
            <h2>1. Information We Collect</h2>
            <p style="margin-top: 15px; line-height: 1.6;">
                We collect information that you provide directly to us when you create an account, 
                complete your profile, generate fitness plans, or track your progress. This includes:
            </p>
            <ul style="margin-top: 10px; padding-left: 20px; line-height: 1.6;">
                <li>Personal information (name, email, username)</li>
                <li>Fitness data (height, weight, age, gender, goals)</li>
                <li>Activity level and preferences</li>
                <li>Progress tracking data</li>
            </ul>
            
            <h2 style="margin-top: 30px;">2. How We Use Your Information</h2>
            <p style="margin-top: 15px; line-height: 1.6;">
                We use the information we collect to:
            </p>
            <ul style="margin-top: 10px; padding-left: 20px; line-height: 1.6;">
                <li>Generate personalized fitness and nutrition plans</li>
                <li>Track your progress towards your goals</li>
                <li>Improve our services and develop new features</li>
                <li>Communicate with you about your account</li>
                <li>Provide customer support</li>
            </ul>
            
            <h2 style="margin-top: 30px;">3. Data Security</h2>
            <p style="margin-top: 15px; line-height: 1.6;">
                We implement appropriate security measures to protect your personal information 
                from unauthorized access, alteration, disclosure, or destruction. This includes:
            </p>
            <ul style="margin-top: 10px; padding-left: 20px; line-height: 1.6;">
                <li>Encryption of sensitive data</li>
                <li>Secure server infrastructure</li>
                <li>Regular security audits</li>
                <li>Access controls and authentication</li>
            </ul>
            
            <h2 style="margin-top: 30px;">4. Your Rights</h2>
            <p style="margin-top: 15px; line-height: 1.6;">
                You have the right to:
            </p>
            <ul style="margin-top: 10px; padding-left: 20px; line-height: 1.6;">
                <li>Access your personal information</li>
                <li>Correct inaccurate information</li>
                <li>Delete your account and associated data</li>
                <li>Export your data</li>
                <li>Opt-out of communications</li>
            </ul>
            
            <h2 style="margin-top: 30px;">5. Contact Us</h2>
            <p style="margin-top: 15px; line-height: 1.6;">
                If you have any questions about this Privacy Policy, please contact us at:
                <br><strong>Email:</strong> privacy@fitnessplanner.com
            </p>
            
            <div style="margin-top: 40px; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                <h3><i class="fas fa-shield-alt"></i> Data Protection Notice</h3>
                <p style="margin-top: 10px; line-height: 1.6;">
                    Your fitness data is used solely for providing you with personalized fitness planning services. 
                    We do not sell your personal information to third parties. All data is stored securely and 
                    retained only for as long as necessary to provide our services.
                </p>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>