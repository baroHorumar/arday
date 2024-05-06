<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('location: login.php');
    exit(); // Ensure script execution stops after redirection
}
require_once './includes/header.php';
// require_once './includes/sidebar.php';
include 'includes/conn.php';
$directQuestions = [];
$multipleChoiceQuestions = [];
$trueFalseQuestions = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_all']) && isset($_SESSION['exam-id'])) {
    // Fetch questions data here
    $exam_id1 = $_SESSION['exam-id'];
    $student_id = $_SESSION['student_id'];
    $end_time = date("Y-m-d H:i:s");
    $start_time = $_SESSION['start_time'];
    unset($_SESSION['exam-id']);

    $sql = "SELECT * FROM questions where exam_id = '$exam_id1' ORDER BY question_type";
    $result = mysqli_query($conn, $sql);
    $userAnswers = [];
    $correctAnswers = [];
    $question_type = []; // Array to store question types

    // Loop through questions to retrieve user answers and correct answers
    while ($row = mysqli_fetch_assoc($result)) {
        // Store question type for each question ID
        $question_type[$row['question_id']] = $row['question_type'];

        switch ($row['question_type']) {
            case 'Direct':
                // Retrieve user's answer for direct question
                $userAnswers[$row['question_id']] = isset($_POST["direct_question_{$row['question_id']}"]) ? $_POST["direct_question_{$row['question_id']}"] : '';
                // Retrieve correct answer for direct question
                $sql = "SELECT * FROM direct_answers WHERE question_id = {$row['question_id']}";
                $answerResult = mysqli_query($conn, $sql);
                // Fetch the correct answer
                $correctAnswerRow = mysqli_fetch_assoc($answerResult);
                $correctAnswers[$row['question_id']] = $correctAnswerRow ? $correctAnswerRow['answer'] : ''; // Ensure the correct answer is properly fetched
                $directQuestions[] = $row;
                break;
            case 'Multiple Choice':
                $multipleChoiceQuestions[] = $row;
                // Retrieve user's answer for multiple choice question
                $userAnswers[$row['question_id']] = isset($_POST["multiple_choice_question_{$row['question_id']}"]) ? $_POST["multiple_choice_question_{$row['question_id']}"] : '';
                // Retrieve correct answer for multiple choice question
                $sql = "SELECT * FROM options WHERE question_id = {$row['question_id']} AND is_correct = 1";
                $answerResult = mysqli_query($conn, $sql);
                $correctAnswers[$row['question_id']] = mysqli_fetch_assoc($answerResult)['option_text'];
                break;
            case 'True/False':
                $trueFalseQuestions[] = $row;
                $userAnswers[$row['question_id']] = isset($_POST["true_false_question_{$row['question_id']}"]) ? $_POST["true_false_question_{$row['question_id']}"] : '';
                $sql = "SELECT * FROM true_false_answers WHERE question_id = {$row['question_id']}";
                $answerResult = mysqli_query($conn, $sql);
                $correctAnswers[$row['question_id']] = mysqli_fetch_assoc($answerResult)['answer'];
                break;
            default:
                break;
        }
    }
    $totalMarks = calculateTotalMarks($directQuestions, $multipleChoiceQuestions, $trueFalseQuestions, $userAnswers, $correctAnswers);
    $insert_attempt_sql = "INSERT INTO exam_attempts (exam_id, student_id, start_time, end_time, score) VALUES ('$exam_id1', '$student_id', '$start_time', '$end_time', $totalMarks)";
    mysqli_query($conn, $insert_attempt_sql);

    $attempt_id = mysqli_insert_id($conn);

    foreach ($userAnswers as $question_id => $userAnswer) {
        $question_id = mysqli_real_escape_string($conn, $question_id);
        $userAnswer = mysqli_real_escape_string($conn, $userAnswer);

        // Fetch correct answer based on question type
        $correctAnswer = '';
        switch ($question_type[$question_id]) {
            case 'Direct':
                // Fetch correct answer for Direct questions
                $sql = "SELECT answer FROM direct_answers WHERE question_id = '$question_id'";
                break;
            case 'Multiple Choice':
                // Fetch correct answer for Multiple Choice questions
                $sql = "SELECT option_text FROM options WHERE question_id = '$question_id' AND is_correct = 1";
                break;
            case 'True/False':
                // Fetch correct answer for True/False questions
                $sql = "SELECT answer FROM true_false_answers WHERE question_id = '$question_id'";
                break;
            default:
                // Handle unknown question types
                break;
        }

        if (!empty($sql)) {
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $correctAnswer = $row && isset($row['answer']) ? $row['answer'] : '';
        }

        $insert_answer_sql = "INSERT INTO student_answers (attempt_id, question_id, student_answers, correct_answer) VALUES ('$attempt_id', '$question_id', '$userAnswer', '$correctAnswer')";
        mysqli_query($conn, $insert_answer_sql);
    }

    echo "<h3>Total Marks: $totalMarks</h3>";
    echo "<div class=\"py-5\">";
    echo "<div class=\"content container-fluid\">";
    echo "<div class=\"row\">";
    echo "<div class=\"card\">";
    echo "<div class=\"card-header\">";
    echo "Questions";
    echo "</div>";
    echo "<div class=\"card-body\">";

    echo "<p class=\"question-type\">Question Type: Direct questions</p>";
    foreach ($directQuestions as $question) {
        echo "<div class=\"card mb-3\">";
        echo "<div class=\"card-body" . ($userAnswers[$question['question_id']] === $correctAnswers[$question['question_id']] ? " bg-success" : "") . "\">"; // Add class for correct answer
        echo "<h5 class=\"card-title\">{$question['question_text']}</h5>";
        echo "<p>User's Answer: {$userAnswers[$question['question_id']]}</p>"; // Always display user's answer
        echo "<p>Correct Answer: {$correctAnswers[$question['question_id']]}</p>";
        echo "</div>";
        echo "</div>";
    }

    echo "<p class=\"question-type\">Question Type: Multiple Choice</p>";
    foreach ($multipleChoiceQuestions as $question) {
        echo "<div class=\"card mb-3\">";
        echo "<div class=\"card-body" . ($userAnswers[$question['question_id']] === $correctAnswers[$question['question_id']] ? " bg-success" : "") . "\">"; // Add class for correct answer
        echo "<h5 class=\"card-title\">{$question['question_text']}</h5>";
        echo "<p>User's Answer: {$userAnswers[$question['question_id']]}</p>"; // Always display user's answer
        echo "<p>Correct Answer: {$correctAnswers[$question['question_id']]}</p>";
        echo "</div>";
        echo "</div>";
    }

    echo "<p class=\"question-type\">Question Type: True/False</p>";
    foreach ($trueFalseQuestions as $question) {
        echo "<div class=\"card mb-3\">";
        echo "<div class=\"card-body" . ($userAnswers[$question['question_id']] === $correctAnswers[$question['question_id']] ? " bg-success" : "") . "\">"; // Add class for correct answer
        echo "<h5 class=\"card-title\">{$question['question_text']}</h5>";
        echo "<p>User's Answer: {$userAnswers[$question['question_id']]}</p>"; // Always display user's answer
        echo "<p>Correct Answer: {$correctAnswers[$question['question_id']]}</p>";
        echo "</div>";
        echo "</div>";
    }

    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
} else {
    if ($_SESSION['grade'] == 4) {
        header("Location: 4exams.php");
    } else {
        header("Location: 8exams.php");
    }
}

function calculateTotalMarks($directQuestions, $multipleChoiceQuestions, $trueFalseQuestions, $userAnswers, $correctAnswers)
{
    $directMarks = calculateDirectMarks($directQuestions, $userAnswers, $correctAnswers);
    $multipleChoiceMarks = calculateMultipleChoiceMarks($multipleChoiceQuestions, $userAnswers, $correctAnswers);
    $trueFalseMarks = calculateTrueFalseMarks($trueFalseQuestions, $userAnswers, $correctAnswers);
    return $directMarks + $multipleChoiceMarks + $trueFalseMarks;
}

function calculateDirectMarks($directQuestions, $userAnswers, $correctAnswers)
{
    $marks = 0;
    foreach ($directQuestions as $question) {
        if ($userAnswers[$question['question_id']] === $correctAnswers[$question['question_id']]) {
            $marks++;
        }
    }
    return $marks;
}

function calculateMultipleChoiceMarks($multipleChoiceQuestions, $userAnswers, $correctAnswers)
{
    $marks = 0;
    foreach ($multipleChoiceQuestions as $question) {
        if ($userAnswers[$question['question_id']] === $correctAnswers[$question['question_id']]) {
            $marks++;
        }
    }
    return $marks;
}

function calculateTrueFalseMarks($trueFalseQuestions, $userAnswers, $correctAnswers)
{
    $marks = 0;
    foreach ($trueFalseQuestions as $question) {
        if ($userAnswers[$question['question_id']] === $correctAnswers[$question['question_id']]) {
            $marks++;
        }
    }
    return $marks;
}

?>

<style>
    /* Custom CSS for exam page */
    .card {
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 15px 20px;
        font-weight: bold;
    }

    .card-body {
        padding: 20px;
    }

    .card-title {
        font-size: 18px;
        margin-bottom: 10px;
    }

    .question p {
        margin-bottom: 5px;
    }

    .question h5 {
        margin-bottom: 15px;
    }

    .question-type {
        font-weight: bold;
        margin-top: 20px;
    }

    /* Highlight correct answers */
    .bg-success {
        background-color: #d4edda !important;
        /* Use a green background for correct answers */
    }

    /* Responsive styles */
    @media (max-width: 576px) {
        .card-body {
            padding: 15px;
        }

        .card-title {
            font-size: 16px;
        }
    }
</style>