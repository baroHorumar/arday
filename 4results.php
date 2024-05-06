<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('location: login.php');
    exit(); // Ensure script execution stops after redirection
}
require_once './includes/header.php';
// require_once './includes/sidebar.php';
include 'includes/conn.php';

// Initialize arrays to store questions of different types
$directQuestions = [];
$multipleChoiceQuestions = [];
$matchQuestions = [];
$trueFalseQuestions = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_all'])) {
    $totalMarks = 0;
    $directMarks = 0;
    $multipleChoiceMarks = 0;
    $matchMarks = 0;
    $trueFalseMarks = 0;

    // Calculate marks and display answers after submission
    // Calculate marks for Direct questions
    foreach ($directQuestions as $question) {
        $questionId = $question['question_id'];
        if (isset($_POST["direct_question_$questionId"])) {
            $userAnswer = $_POST["direct_question_$questionId"];

            // Fetch the correct answer for the question
            $sql = "SELECT * FROM options WHERE question_id = $questionId AND is_correct = 1";
            $result = mysqli_query($conn, $sql);
            $correctAnswer = mysqli_fetch_assoc($result)['option_text'];

            // Check if user's answer matches the correct answer
            if ($userAnswer == $correctAnswer) {
                $directMarks++;
            }
        }
    }

    // Calculate marks for Multiple Choice questions
    foreach ($multipleChoiceQuestions as $question) {
        $questionId = $question['question_id'];
        if (isset($_POST["multiple_choice_question_$questionId"])) {
            $userAnswer = $_POST["multiple_choice_question_$questionId"];

            // Fetch the correct answer for the question
            $sql = "SELECT * FROM options WHERE question_id = $questionId AND is_correct = 1";
            $result = mysqli_query($conn, $sql);
            $correctAnswer = mysqli_fetch_assoc($result)['option_text'];

            // Check if user's answer matches the correct answer
            if ($userAnswer == $correctAnswer) {
                $multipleChoiceMarks++;
            }
        }
    }

    // Calculate marks for Match questions
    foreach ($matchQuestions as $question) {
        $questionId = $question['question_id'];
        if (isset($_POST["match_option_$questionId"])) {
            $userAnswers = $_POST["match_option_$questionId"];
            $matchPairs = $_POST["match_pair_$questionId"];

            // Fetch the correct match pairs for the question
            $sql = "SELECT * FROM match_options WHERE question_id = $questionId";
            $result = mysqli_query($conn, $sql);
            $correctPairs = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $correctPairs[$row['option_text']] = $row['match_pair'];
            }

            // Check if user's match pairs match the correct pairs
            $correct = true;
            foreach ($userAnswers as $key => $userAnswer) {
                if ($correctPairs[$userAnswer] != $matchPairs[$key]) {
                    $correct = false;
                    break;
                }
            }
            if ($correct) {
                $matchMarks++;
            }
        }
    }

    // Calculate marks for True/False questions
    foreach ($trueFalseQuestions as $question) {
        $questionId = $question['question_id'];
        if (isset($_POST["true_false_question_$questionId"])) {
            $userAnswer = $_POST["true_false_question_$questionId"];

            // Fetch the correct answer for the question
            $sql = "SELECT * FROM options WHERE question_id = $questionId AND is_correct = 1";
            $result = mysqli_query($conn, $sql);
            $correctAnswer = mysqli_fetch_assoc($result)['option_text'];

            // Check if user's answer matches the correct answer
            if ($userAnswer == $correctAnswer) {
                $trueFalseMarks++;
            }
        }
    }

    // Calculate total marks
    $totalMarks = $directMarks + $multipleChoiceMarks + $matchMarks + $trueFalseMarks;

    // Display marks for each section
    echo "<h3>Direct Questions Marks: $directMarks</h3>";
    echo "<h3>Multiple Choice Questions Marks: $multipleChoiceMarks</h3>";
    echo "<h3>Match Questions Marks: $matchMarks</h3>";
    echo "<h3>True/False Questions Marks: $trueFalseMarks</h3>";
    echo "<h3>Total Marks: $totalMarks</h3>";
}
