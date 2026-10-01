<?php
// Installation script for Fitness Planner
if (file_exists('config/database.php')) {
    die("Fitness Planner is already installed. Remove this file for security.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = $_POST['db_host'] ?? 'localhost';
    $db_name = $_POST['db_name'] ?? 'fitness_planner';
    $db_user = $_POST['db_user'] ?? 'root';
    $db_pass = $_POST['db_pass'] ?? '';
    
    // Test database connection
    try {
        $conn = new mysqli($db_host, $db_user, $db_pass);
        
        if ($conn->connect_error) {
            $error = "Database connection failed: " . $conn->connect_error;
        } else {
            // Create database
            $conn->query("CREATE DATABASE IF NOT EXISTS $db_name");
            $conn->select_db($db_name);
            
            // Read and execute schema
            $schema = file_get_contents('database_schema.sql');
            $queries = explode(';', $schema);
            
            foreach ($queries as $query) {
                $query = trim($query);
                if (!empty($query)) {
                    $conn->query($query . ';');
                }
            }
            
            // Create config file
            $config_content = "<?php
// Database configuration
define('DB_HOST', '$db_host');
define('DB_USER', '$db_user');
define('DB_PASS', '$db_pass');
define('DB_NAME', '$db_name');
define('DB_PORT', '3306');

// Create connection
\$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Check connection
if (\$conn->connect_error) {
    die(\"Connection failed: \" . \$conn->connect_error);
}

// Set charset to UTF-8
\$conn->set_charset(\"utf8mb4\");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>";
            
            file_put_contents('config/database.php', $config_content);
            
            $success = "Installation completed successfully!";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Planner - Installation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .install-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
        }
        
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input:focus {
            outline: none;
            border-color: #4a6fa5;
        }
        
        .btn {
            background: #4a6fa5;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #3a5a8a;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .requirements {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .requirements h3 {
            margin-bottom: 15px;
            color: #333;
        }
        
        .requirements ul {
            padding-left: 20px;
            color: #666;
        }
        
        .requirements li {
            margin-bottom: 8px;
        }
        
        .note {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            color: #0c5460;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <h1>Fitness Planner Installation</h1>
        <p class="subtitle">Complete the installation to get started</p>
        
        <?php if($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
                <div style="margin-top: 15px;">
                    <p>Next steps:</p>
                    <ol>
                        <li>Delete this installation file (install.php) for security</li>
                        <li>Go to <a href="index.php">Homepage</a> and register an account</li>
                        <li>Complete your profile setup</li>
                        <li>Start creating fitness plans!</li>
                    </ol>
                    <div style="margin-top: 20px;">
                        <a href="index.php" class="btn">Go to Website</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="requirements">
                <h3>Requirements</h3>
                <ul>
                    <li>PHP 7.4 or higher</li>
                    <li>MySQL 5.7 or higher</li>
                    <li>Apache web server (XAMPP/MAMP recommended)</li>
                    <li>Write permissions for config directory</li>
                </ul>
            </div>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="db_host">Database Host</label>
                    <input type="text" id="db_host" name="db_host" value="localhost" required>
                </div>
                
                <div class="form-group">
                    <label for="db_name">Database Name</label>
                    <input type="text" id="db_name" name="db_name" value="fitness_planner" required>
                </div>
                
                <div class="form-group">
                    <label for="db_user">Database Username</label>
                    <input type="text" id="db_user" name="db_user" value="root" required>
                </div>
                
                <div class="form-group">
                    <label for="db_pass">Database Password</label>
                    <input type="password" id="db_pass" name="db_pass" value="">
                    <small style="color: #666; margin-top: 5px; display: block;">Leave empty for default XAMPP/MAMP installation</small>
                </div>
                
                <div class="note">
                    <strong>Note:</strong> This will create the database and tables automatically. Make sure you have proper database permissions.
                </div>
                
                <button type="submit" class="btn" style="margin-top: 20px;">
                    Install Fitness Planner
                </button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>