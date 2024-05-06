<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('location: ../login.php');
    exit(); // Ensure script execution stops after redirection
}
require './includes/conn.php';
require './includes/header.php';
require './includes/sidebar.php';

// Fetch student's name from the students table
$student_id = $_SESSION['student_id'];
$sql_student = "SELECT * FROM students WHERE student_id = '$student_id'";
$result_student = mysqli_query($conn, $sql_student);
$student_data = mysqli_fetch_assoc($result_student);

// Fetch attempts from the exam_attempts table
$sql_attempts = "SELECT * FROM exam_attempts WHERE student_id = '$student_id'";
$result_attempts = mysqli_query($conn, $sql_attempts);

$total_attempts = mysqli_num_rows($result_attempts);
$total_score = 0;
$total_time = 0;
$total_attempted_questions = 0; // Initialize total attempted questions counter

while ($attempt = mysqli_fetch_assoc($result_attempts)) {
    $attempt_id = $attempt['attempt_id'];

    // Fetch answers for the attempt from the student_answers table
    $sql_answers = "SELECT * FROM student_answers WHERE attempt_id = '$attempt_id'";
    $result_answers = mysqli_query($conn, $sql_answers);

    // Variables to store attempt-specific data
    $attempt_score = 0;
    $attempt_time = strtotime($attempt['end_time']) - strtotime($attempt['start_time']);

    // Iterate through each answer
    while ($answer = mysqli_fetch_assoc($result_answers)) {
        // Check if the student's answer matches the correct answer
        if ($answer['student_answers'] === $answer['correct_answer']) {
            $attempt_score++;
        }
        // Count each question attempted by the user
        $total_attempted_questions++;
    }

    // Add attempt-specific data to totals
    $total_score += $attempt_score;
    $total_time += $attempt_time;
}

// Calculate averages
$average_score = $total_attempts > 0 ? round($total_score / $total_attempted_questions, 2) : 0; // Change to total_attempted_questions
$average_time = $total_attempts > 0 ? gmdate("H:i", $total_time / $total_attempts) : '00:00';

?>





<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="row justify-content-lg-center">
            <div class="col-lg-10">
                <div class="page-header">
                    <div class="row">
                        <div class="col">
                            <h3 class="page-title">Profile</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                <li class="breadcrumb-item active">Profile</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="text-center mb-5">
                    <h2><?php echo $student_data['student_name']; ?><i class="fas fa-certificate text-primary small" data-toggle="tooltip" data-placement="top" title="" data-original-title="Verified"></i></h2>
                    <ul class="list-inline">
                        <li class="list-inline-item">
                            <i class="fas fa-check"></i> <span>Correct Answers: <?php echo $total_score; ?>/<?php
                                                                                                            // Query the database to count the total number of questions attempted by the student
                                                                                                            $sql_question_count = "SELECT COUNT(question_id) AS total_questions FROM student_answers WHERE student_id = '$student_id'";
                                                                                                            $result_question_count = mysqli_query($conn, $sql_question_count);
                                                                                                            $row = mysqli_fetch_assoc($result_question_count);
                                                                                                            $total_questions_attempted = $row['total_questions'];
                                                                                                            echo $total_questions_attempted;
                                                                                                            ?></span>
                        </li>
                        <li class="list-inline-item">
                            <i class="fas fa-chart-line"></i> Average Score: <?php echo $average_score; ?>
                        </li>
                        <li class="list-inline-item">
                            <i class="far fa-clock"></i> Average Time: <?php echo $average_time; ?>
                        </li>
                    </ul>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title d-flex justify-content-between">
                                    <span>Profile</span>
                                    <form action="edit_student.php" method="post">
                                        <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                                        <button name="edit_student" class="btn btn-sm btn-white">Edit</button>
                                    </form>
                                </h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li class="py-0">
                                        <h6>About Student</h6>
                                    </li>
                                    <li>
                                        <?php echo $student_data['student_name']; ?> </li>
                                    <li>
                                        <?php echo $student_data['address']; ?> </li>
                                    </li>
                                    <li class="pt-2 pb-0">
                                        <h6>Contacts</h6>
                                    </li>

                                    <li>
                                        <?php echo $student_data['phone_number']; ?> </li>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php
                    // Check if the session is started
                    if (!isset($_SESSION)) {
                        session_start();
                    }

                    // Fetch exams from exam_attempts where student_id matches $_SESSION['student_id']
                    $student_id = $_SESSION['student_id'];
                    $sql = "SELECT exam_id, AVG(TIMESTAMPDIFF(MINUTE, start_time, end_time)) AS avg_time, AVG(score) AS avg_score FROM exam_attempts WHERE student_id = $student_id GROUP BY exam_id";
                    $result = $conn->query($sql);

                    ?>
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Exam Activities</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="examTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Subject</th>
                                                <th>Avg Time</th>
                                                <th>Avg Score</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($row = $result->fetch_assoc()) {
                                                $exam_id = $row['exam_id'];
                                                // Fetch subject from exams table based on exam_id
                                                $subject_sql = "SELECT subject FROM exams WHERE exam_id=$exam_id";
                                                $subject_result = $conn->query($subject_sql);
                                                $subject_row = $subject_result->fetch_assoc();
                                            ?>
                                                <tr>
                                                    <td><?php echo $subject_row['subject']; ?></td>
                                                    <td><?php echo round($row['avg_time'], 2) . ' Minite'; ?></td>
                                                    <td><?php echo round($row['avg_score'], 2); ?></td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>

</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script data-cfasync="false" src="../../../../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/feather.min.js"></script>
<script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="assets/js/script.js"></script>
</body>

</html>