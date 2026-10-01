<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$plan_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Delete plan
if ($plan_id > 0) {
    $delete_query = "DELETE FROM plans WHERE id = $plan_id AND user_id = $user_id";
    if ($conn->query($delete_query)) {
        $_SESSION['success'] = "Plan deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting plan.";
    }
}

header("Location: my_plans.php");
exit();
?>