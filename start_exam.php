<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('location: login.php');
    exit(); // Ensure script execution stops after redirection
}
require_once './includes/header.php';
require_once './includes/sidebar.php';
include 'includes/conn.php';

$directQuestions = [];
$multipleChoiceQuestions = [];
$matchQuestions = [];
$trueFalseQuestions = [];

if (isset($_POST['start_exam'])) {
    $exam_id = $_POST['exam_id'];
    echo 'Exam ID: ' . $exam_id;
    $_SESSION['exam-id'] = $exam_id;
    $start_time = date("Y-m-d H:i:s");
    $_SESSION['start_time'] = $start_time;
    $sql = "SELECT * FROM questions where exam_id = '$exam_id' ORDER BY question_type";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        switch ($row['question_type']) {
            case 'Direct':
                $directQuestions[] = $row;
                break;
            case 'Multiple Choice':
                $multipleChoiceQuestions[] = $row;
                break;
            case 'Match':
                $matchQuestions[] = $row;
                break;
            case 'True/False':
                $trueFalseQuestions[] = $row;
                break;
            default:
                break;
        }
    }
}
?>
<div class="py-5">
    <div class="content container-fluid">
        <div class="row">
            <div class="card">
                <div class="card-header">
                    Questions
                </div>
                <div class="card-body">
                    <form action="results.php" method="post">
                        <hr>
                        <p>Question Type: Direct questions</p>

                        <?php foreach ($directQuestions as $question) : ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $question['question_text']; ?></h5>
                                    <input type="text" class="form-control" name="direct_question_<?php echo $question['question_id']; ?>" placeholder="Enter your answer">
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <hr>
                        <p>Question Type: Multiple Choice</p>

                        <?php foreach ($multipleChoiceQuestions as $question) : ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $question['question_text']; ?></h5>
                                    <?php
                                    // Fetch options for this question
                                    $questionId = $question['question_id'];
                                    $sql = "SELECT * FROM options WHERE question_id = $questionId";
                                    $optionsResult = mysqli_query($conn, $sql);
                                    ?>
                                    <?php while ($option = mysqli_fetch_assoc($optionsResult)) : ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="multiple_choice_question_<?php echo $questionId; ?>" id="option_<?php echo $option['option_id']; ?>" value="<?php echo $option['option_text']; ?>">
                                            <label class="form-check-label" for="option_<?php echo $option['option_id']; ?>">
                                                <?php echo $option['option_text']; ?>
                                            </label>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>


                        <?php foreach ($trueFalseQuestions as $question) : ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $question['question_text']; ?></h5>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="true_false_question_<?php echo $question['question_id']; ?>" id="true_<?php echo $question['question_id']; ?>" value="True">
                                        <label class="form-check-label" for="true_<?php echo $question['question_id']; ?>">
                                            True
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="true_false_question_<?php echo $question['question_id']; ?>" id="false_<?php echo $question['question_id']; ?>" value="False">
                                        <label class="form-check-label" for="false_<?php echo $question['question_id']; ?>">
                                            False
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <button type="submit" class="btn btn-primary" name="submit_all">Submit All</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once './includes/footer.php'; ?>