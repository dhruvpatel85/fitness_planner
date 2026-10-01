-- Fitness Planner Database Schema
-- Run this in phpMyAdmin to create the database and tables

CREATE DATABASE IF NOT EXISTS fitness_planner;
USE fitness_planner;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    age INT,
    gender ENUM('male', 'female', 'other'),
    height DECIMAL(5,2) COMMENT 'in centimeters',
    current_weight DECIMAL(5,2) COMMENT 'in kilograms',
    goal ENUM('weight_loss', 'muscle_gain', 'maintenance', 'bulking', 'endurance'),
    activity_level ENUM('sedentary', 'light', 'moderate', 'active', 'very_active'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Plans table
CREATE TABLE IF NOT EXISTS plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    plan_name VARCHAR(100),
    goal ENUM('weight_loss', 'muscle_gain', 'maintenance', 'bulking', 'endurance'),
    duration_weeks INT,
    daily_calories INT,
    protein_grams INT,
    carbs_grams INT,
    fat_grams INT,
    workout_days TEXT,
    meals JSON,
    exercises JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Progress tracking table
CREATE TABLE IF NOT EXISTS progress (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    date DATE,
    weight DECIMAL(5,2) COMMENT 'in kilograms',
    body_fat DECIMAL(5,2) COMMENT 'percentage',
    chest_cm DECIMAL(5,2),
    waist_cm DECIMAL(5,2),
    hips_cm DECIMAL(5,2),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_date (user_id, date),
    INDEX idx_user_id (user_id),
    INDEX idx_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data for testing
INSERT INTO users (username, email, password, full_name, age, gender, height, current_weight, goal, activity_level) VALUES
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 30, 'male', 175.5, 75.2, 'weight_loss', 'moderate'),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Smith', 28, 'female', 162.0, 58.5, 'muscle_gain', 'active');

-- Insert sample progress data
INSERT INTO progress (user_id, date, weight, body_fat, chest_cm, waist_cm, hips_cm, notes) VALUES
(1, CURDATE(), 75.2, 18.5, 98.0, 85.0, 95.0, 'Feeling good, energy levels are up'),
(2, CURDATE(), 58.5, 22.0, 88.0, 70.0, 92.0, 'Making good progress on strength training');

-- Insert sample plans
INSERT INTO plans (user_id, plan_name, goal, duration_weeks, daily_calories, protein_grams, carbs_grams, fat_grams, workout_days, meals, exercises) VALUES
(1, 'Summer Weight Loss Plan', 'weight_loss', 8, 2200, 165, 220, 73, '["Monday","Wednesday","Friday","Saturday"]', '{"breakfast":"Oatmeal with berries","lunch":"Grilled chicken salad","dinner":"Salmon with vegetables","snacks":"Apple, Greek yogurt"}', '{"cardio":["Running","Cycling"],"strength":["Squats","Push-ups","Rows"]}'),
(2, 'Muscle Gain Program', 'muscle_gain', 12, 2500, 188, 250, 83, '["Monday","Tuesday","Thursday","Friday","Saturday"]', '{"breakfast":"Eggs and whole grain toast","lunch":"Chicken breast with rice","dinner":"Lean beef with potatoes","snacks":"Protein shake, Nuts"}', '{"chest":["Bench Press","Incline Press"],"back":["Pull-ups","Rows"],"legs":["Squats","Deadlifts"]}');