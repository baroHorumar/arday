<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('location: login.php');
    exit(); // Ensure script execution stops after redirection
}
require_once './includes/header.php';
// require_once './includes/sidebar.php';
include 'includes/conn.php';
if (isset($_POST['upload_id'])) {
    $exam_id = $_POST['exam_id'];
    echo 'uplpoad' . $exam_id;
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
        $file_name = $_FILES["file"]["name"];
        $file_tmp = $_FILES["file"]["tmp_name"];
        $file_size = $_FILES["file"]["size"];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if ($file_ext == "csv") {
            $csv_data = array_map("str_getcsv", file($file_tmp));
            $exam_id = $_POST["exam_id"];
            echo 'exam_id' .  $exam_id;
            $stmt_question = $conn->prepare("INSERT INTO Questions (exam_id, question_text, question_type, num_of_failure) VALUES (?, ?, ?, 0)");
            $stmt_question->bind_param("iss", $exam_id, $questionText, $quizType);
            $stmt_option = $conn->prepare("INSERT INTO Options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
            $stmt_option->bind_param("iss", $question_id, $optionText, $isCorrect);
            foreach ($csv_data as $row) {
                $quizType = $row[0];
                $questionText = $row[2];
                $correctAnswer = $row[3];
                $stmt_question->execute();
                $question_id = $conn->insert_id;
                if ($quizType == 'Multiple Choice') {
                    if (count($row) >= 4) {
                        $options = array_slice($row, 3);
                        $correctAnswerExists = false;
                        foreach ($options as $option) {
                            $isCorrect = ($option == $correctAnswer) ? 1 : 0;
                            $optionText = $option;
                            $stmt_option->execute();
                            if ($isCorrect) {
                                $correctAnswerExists = true; // Set flag to true if correct answer is found
                            }
                        }
                        // If correct answer is not among the options, add it separately
                        if (!$correctAnswerExists) {
                            $isCorrect = 1; // Mark correct answer
                            $optionText = $correctAnswer;
                            $stmt_option->execute();
                        }
                    }
                } elseif ($quizType == 'True/False') {
                    $trueOption = 'True';
                    $falseOption = 'False';
                    $stmt_option->execute();
                    $stmt_option->execute();
                    $stmt_answer = $conn->prepare("INSERT INTO true_false_answers (answer, question_id) VALUES (?, ?)");
                    $stmt_answer->bind_param("si", $correctAnswer, $question_id);
                    $stmt_answer->execute();
                } elseif ($quizType == 'Direct') {
                    $stmt_answer = $conn->prepare("INSERT INTO direct_answers (answer, question_id) VALUES (?, ?)");
                    $stmt_answer->bind_param("si", $correctAnswer, $question_id);
                    $stmt_answer->execute();
                }
            }

            echo "Quizzes inserted successfully.";
            header("Location: exams.php");
        } else {
            echo "Error: File must be a CSV.";
        }
    } else {
        echo "Error uploading file.";
    }
}
?>
<style>
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .card {
        width: 100%;
        padding: 20px;
    }

    @media (max-width: 992px) {
        .form-group {
            flex-basis: 100%;
            margin-bottom: 10px;
        }
    }
</style>
</head>

<body>
    <div class="container col-lg">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Upload CSV File</h5>
                <form action="upload.php" method="post" enctype="multipart/form-data">
                    <input type="text" class="form-control-file" name="exam_id" id="exam_id" value="<?php echo $exam_id; ?>" required>
                    <div class="form-group">
                        <label for="file">Choose CSV File</label>
                        <input type="file" class="form-control-file" name="file" id="file" accept=".csv" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>