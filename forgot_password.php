<?php
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    
    // Check if email exists
    $query = "SELECT id, username FROM users WHERE email = '$email'";
    $result = $conn->query($query);
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Generate reset token (in a real app, you would generate a secure token)
        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Store token in database (you need a password_resets table)
        // For simplicity, we'll just show a message
        $success = "Password reset instructions have been sent to your email.";
    } else {
        $error = "No account found with that email address.";
    }
}

require_once 'includes/header.php';
?>

<div class="form-container">
    <h2 class="form-title">Forgot Password</h2>
    
    <?php if($success): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
            <p style="margin-top: 10px;">
                <a href="login.php">Return to login</a>
            </p>
        </div>
    <?php else: ?>
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label" for="email">Enter your email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <button type="submit" class="btn btn-block btn-accent">
                <i class="fas fa-key"></i> Reset Password
            </button>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="login.php">Back to Login</a>
            </div>
        </form>
    <?php endif; ?>
    
    <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
        <h4><i class="fas fa-info-circle"></i> Note</h4>
        <p style="margin-top: 10px; font-size: 0.9rem;">
            In a production environment, this would send a password reset link to your email. 
            For this demo, please remember your password or contact the administrator.
        </p>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>