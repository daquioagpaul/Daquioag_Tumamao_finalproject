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

// Prompt the user to input their weight
$weight = (float)prompt("Enter your weight (in kg): ");

// Prompt the user to input up to 22 step entries for today
$entries = [];
for ($i = 0; $i < 22; $i++) {
    $stepsToday = prompt("Enter the number of steps for entry " . ($i + 1) . ": ");
    if (empty($stepsToday)) {
        break;
    }
    $stepsToday = (int)$stepsToday;
    $caloriesBurnedToday = calculateCaloriesBurned($stepsToday, $weight);

    // Append today's step and calculated calories burned to the entries
    $entries[] = [
        'steps' => $stepsToday,
        'calories' => $caloriesBurnedToday
    ];
}

// Merge today's entries into the data
$data = array_merge($data, $entries);

// Store the updated data in the session
$_SESSION['data'] = $data;

// Keep only the last 21-22 days of data
$uniqueDates = array_unique(array_column($data, 'date'));
if (count($uniqueDates) > 22) {
    $datesToKeep = array_slice($uniqueDates, -22);
    $data = array_filter($data, function($entry) use ($datesToKeep) {
        return in_array($entry['date'], $datesToKeep);
    });
    $data = array_values($data); // Reindex array
}

// Calculate total average daily steps over the past 21-22 days
$totalSteps = array_sum(array_column($data, 'steps'));
$averageDailySteps = $totalSteps / count($data);

// Calculate total calories burned over the past 21-22 days
$totalCalories = array_sum(array_column($data, 'calories'));

// Provide feedback for the last entry of today's steps
$lastEntry = end($entries);
$quotaMet = $lastEntry['steps'] >= 8000;

if ($quotaMet) {
    echo "\nGreat job! You've met your daily step goal of 8,000 steps.\n";
} else {
    echo "\nYou haven't met your daily step goal of 8,000 steps. Keep moving!\n";
}

// Display summary of past 21-22 days
echo "Average daily steps over the past 21-22 days: " . round($averageDailySteps, 2) . "\n";
echo "Total calories burned over the past 21-22 days: " . round($totalCalories, 2) . " calories\n";

// Display today's summary
$totalStepsToday = array_sum(array_column($entries, 'steps'));
$totalCaloriesToday = array_sum(array_column($entries, 'calories'));

echo "Today's total steps: $totalStepsToday\n";
echo "Total calories burned today: " . round($totalCaloriesToday, 2) . " calories\n";
?>