<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$progress_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Delete progress entry
if ($progress_id > 0) {
    $delete_query = "DELETE FROM progress WHERE id = $progress_id AND user_id = $user_id";
    if ($conn->query($delete_query)) {
        $_SESSION['success'] = "Progress entry deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting progress entry.";
    }
}

header("Location: progress.php");
exit();
?>