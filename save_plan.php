<?php
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_plan'])) {
    $user_id = $_SESSION['user_id'];
    $plan_name = $conn->real_escape_string($_POST['plan_name']);
    $goal = $conn->real_escape_string($_POST['goal']);
    $duration = intval($_POST['duration']);
    $daily_calories = intval($_POST['daily_calories']);
    $protein_grams = intval($_POST['protein_grams']);
    $carbs_grams = intval($_POST['carbs_grams']);
    $fat_grams = intval($_POST['fat_grams']);
    $workout_days = $conn->real_escape_string($_POST['workout_days']);
    $meals = $conn->real_escape_string($_POST['meals']);
    $exercises = $conn->real_escape_string($_POST['exercises']);
    
    $query = "INSERT INTO plans (user_id, plan_name, goal, duration_weeks, daily_calories, 
              protein_grams, carbs_grams, fat_grams, workout_days, meals, exercises, created_at) 
              VALUES ('$user_id', '$plan_name', '$goal', $duration, $daily_calories, 
              $protein_grams, $carbs_grams, $fat_grams, '$workout_days', '$meals', '$exercises', NOW())";
    
    if ($conn->query($query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>