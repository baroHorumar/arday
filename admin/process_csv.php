<?php
// Database connection
require './conn.php';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
        $file_name = $_FILES["file"]["name"];
        $file_tmp = $_FILES["file"]["tmp_name"];
        $file_size = $_FILES["file"]["size"];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if ($file_ext == "csv") {
            $csv_data = array_map("str_getcsv", file($file_tmp));
            $exam_id = $_POST["examYear"];
            $stmt_question = $conn->prepare("INSERT INTO Questions (exam_id, question_text, question_type, num_of_failure) VALUES (?, ?, ?, 0)");
            $stmt_question->bind_param("iss", $exam_id, $questionText, $quizType);
            $stmt_option = $conn->prepare("INSERT INTO Options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
            $stmt_option->bind_param("iss", $question_id, $optionText, $isCorrect);
            foreach ($csv_data as $row) {
                $quizType = $row[0];
                $questionText = $row[1];
                $correctAnswer = $row[2];
                $stmt_question->execute();
                $question_id = $conn->insert_id;
                if ($quizType == 'Multiple Choice') {
                    if (count($row) >= 4) {
                        $options = explode(", ", $correctAnswer);
                        foreach ($options as $option) {
                            $isCorrect = ($option == $row[3]) ? 1 : 0;
                            $optionText = $option;
                            $stmt_option->execute();
                        }
                    } else {
                    }
                } elseif ($quizType == 'True/False') {
                    // Insert True/False options
                    $trueOption = 'True';
                    $falseOption = 'False';
                    $stmt_option->execute();
                    $stmt_option->execute();
                }
            }

            echo "Quizzes inserted successfully.";
        } else {
            echo "Error: File must be a CSV.";
        }
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "No file uploaded.";
}

$stmt_question->close();
$stmt_option->close();

$conn->close();
