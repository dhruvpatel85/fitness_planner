<?php
require_once 'config/database.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);
    
    // In a real app, you would send an email here
    // For this demo, we'll just show a success message
    $success = "Thank you for your message! We'll get back to you within 24 hours.";
}

require_once 'includes/header.php';
?>

<div class="container">
    <div style="background: white; padding: 40px; border-radius: 15px; margin: 30px 0;">
        <h1>Contact Us</h1>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px; margin-top: 30px;">
            <div>
                <h2>Get in Touch</h2>
                <p style="margin-top: 15px; color: #666;">
                    Have questions about your fitness plan? Need help with your account? 
                    We're here to help you achieve your fitness goals.
                </p>
                
                <form method="POST" action="" style="margin-top: 30px;">
                    <div class="form-group">
                        <label class="form-label" for="name">Your Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="subject">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="message">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-accent">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
            
            <div>
                <div style="background: #f8f9fa; padding: 30px; border-radius: 12px;">
                    <h3><i class="fas fa-info-circle"></i> Contact Information</h3>
                    
                    <div style="margin-top: 25px;">
                        <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px;">
                            <div style="background: var(--primary); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h4 style="margin-bottom: 5px;">Address</h4>
                                <p style="color: #666;">123 Fitness Street<br>Health City, HC 12345</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px;">
                            <div style="background: var(--primary); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <h4 style="margin-bottom: 5px;">Phone</h4>
                                <p style="color: #666;">+1 (555) 123-4567</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: flex-start; gap: 15px;">
                            <div style="background: var(--primary); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h4 style="margin-bottom: 5px;">Email</h4>
                                <p style="color: #666;">support@fitnessplanner.com</p>
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin-top: 40px;">
                        <h4><i class="fas fa-clock"></i> Business Hours</h4>
                        <div style="margin-top: 15px; background: white; padding: 15px; border-radius: 8px;">
                            <p><strong>Monday - Friday:</strong> 9:00 AM - 6:00 PM</p>
                            <p><strong>Saturday:</strong> 10:00 AM - 4:00 PM</p>
                            <p><strong>Sunday:</strong> Closed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>