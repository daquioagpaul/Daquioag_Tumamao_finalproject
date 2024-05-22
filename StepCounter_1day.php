<?php
session_start(); // Start or resume a session

// Function to calculate calories burned
function calculateCaloriesBurned($steps, $weight) {
    $caloriesPerStep = 0.035 * $weight / 2000;
    return $steps * $caloriesPerStep;
}

// Function to prompt user for input (simulated here as CLI input for simplicity)
function prompt($message) {
    echo $message;
    return trim(fgets(STDIN));
}

// Initialize or retrieve the data from the session
if (isset($_SESSION['data'])) {
    $data = $_SESSION['data'];
} else {
    $data = [];
}

// Prompt the user to input the number of steps taken today
$stepsToday = (int)prompt("Enter the number of steps taken today: ");

// Prompt the user to input their weight
$weight = (float)prompt("Enter your weight (in kg): ");

// Compute calories burned
$caloriesBurnedToday = calculateCaloriesBurned($stepsToday, $weight);

// Append today's step and calculated calories burned to the dataset
$data[] = [
    'steps' => $stepsToday,
    'calories' => $caloriesBurnedToday
];

// Store the updated data in the session
$_SESSION['data'] = $data;

// Keep only the last 21-22 days of data
if (count($data) > 22) {
    $data = array_slice($data, -22);
}

// Calculate total average daily steps over the past 21-22 days
$totalSteps = array_sum(array_column($data, 'steps'));
$averageDailySteps = $totalSteps / count($data);

// Calculate total calories burned over the past 21-22 days
$totalCalories = array_sum(array_column($data, 'calories'));

// Check if today's steps meet the daily quota of 8,000 steps
$quotaMet = $stepsToday >= 8000;

// Provide feedback to the user regarding their daily step count
if ($quotaMet) {
    echo "\n". "Great job! You've met your daily step goal of 8,000 steps.\n";
} else {
    echo "\n"."You haven't met your daily step goal of 8,000 steps. Keep moving!\n";
}

// Display summary of past 21-22 days
echo "Average daily steps over the past 21-22 days: " . round($averageDailySteps, 2) . "\n";
echo "Total calories burned over the past 21-22 days: " . round($totalCalories, 2) . " calories\n";

echo "Today's steps: $stepsToday\n";
echo "Calories burned today: " . round($caloriesBurnedToday, 2) . " calories\n";
?>