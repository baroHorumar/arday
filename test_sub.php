<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('location: login.php');
    exit(); // Ensure script execution stops after redirection
}
require_once './includes/header.php';
//require_once './includes/sidebar.php';
include 'includes/conn.php';

$permissions = array(
    "Biology" => false,
    "Chemistry" => false,
    "Maths" => false,
    "Physics" => false,
    "English" => false,
    "Somali" => false,
    "History" => false,
    "Tarbiya" => false,
    "Arabic" => false,
    "Geography" => false
);
$user_id = 10;
// Fetch subject IDs for the student from payment table
$stmt = $conn->prepare("SELECT subject_id FROM payment WHERE status = 'Approved' AND student_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$subjects = array();

while ($row = $result->fetch_assoc()) {
    $subjects[] = $row['subject_id']; // Store subject IDs for further processing
}

// If no subjects found for the student, set all permissions to false
if (!empty($subjects)) {
    // Fetch corresponding subject names from subjects table
    $subjectNames = array();
    $query = "SELECT subject_name FROM subjects WHERE subject_id IN (" . implode(',', $subjects) . ")";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $subjectNames[] = $row['subject_name'];
    }

    // Check if all subjects are 'full' or there's only one subject purchased
    $allFull = count(array_unique($subjectNames)) === 1 && $subjectNames[0] === 'full';
    $singleSubject = count($subjectNames) === 1 && $subjectNames[0] !== 'full';

    // Set permissions based on fetched subject names
    foreach ($permissions as $subject => $value) {
        if ($allFull || ($singleSubject && in_array($subject, $subjectNames)) || in_array('full', $subjectNames)) {
            $permissions[$subject] = true;
        } else {
            $permissions[$subject] = false;
        }
    }
}

// Output the purchased status of each subject
foreach ($permissions as $subject => $purchased) {
    echo "$subject: " . ($purchased ? "true" : "false") . "<br>";
}
