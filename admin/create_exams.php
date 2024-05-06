<?php
include './includes/conn.php';
include './includes/header.php';
include './includes/sidebar.php';
$permissions = array(
    "Biology",
    "Chemistry",
    "Maths Paper 1",
    "Maths Paper 2",
    "Physics",
    "English Paper 1",
    "English Paper 2",
    "Somali",
    "History",
    "Tarbiya",
    "Arabic",
    "Geography"
);
$year = '';
$marks = '';
$grade = '';
$selectedSubjects = array();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $year = $_POST['year'];
    $marks = $_POST['marks'];
    $grade = $_POST['grade'];
    $selectedSubjects = isset($_POST['subjects']) ? $_POST['subjects'] : array();
    $query = "INSERT INTO exams (year, subject, total_marks, grade) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssss", $year, $subject, $marks, $grade);
    foreach ($selectedSubjects as $subject) {
        mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);
    header("Location: exams.php?success=true");
    exit();
}
?>

<div class="main-wrapper login-body">
    <div class="login-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="loginbox">
                        <div class="login-right">
                            <div class="login-right-wrap">
                                <h1>Create Exam</h1>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                    <div class="form-group">
                                        <label class="form-control-label">Year</label>
                                        <select class="form-control" name="year">
                                            <?php
                                            $currentYear = date('Y');
                                            for ($i = $currentYear; $i >= 2010; $i--) {
                                                echo "<option value='$i'>$i</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label">Marks</label>
                                        <input class="form-control" type="number" name="marks" value="<?php echo htmlspecialchars($marks); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label">Grade</label>
                                        <select class="form-control" name="grade">
                                            <option value="4">4</option>
                                            <option value="8">8</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label">Subjects</label>
                                        <select class="form-control" name="subjects[]">
                                            <?php
                                            foreach ($permissions as $subject) {
                                            ?>
                                                <option value='<?php echo $subject ?>'><?php echo $subject ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0">
                                        <button class="btn btn-lg btn-block btn-primary" type="submit" name="create_exam">Create Exam</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="./assets/js/jquery-3.6.0.min.js"></script>
<script src="./assets/js/bootstrap.bundle.min.js"></script>
<script src="./assets/js/feather.min.js"></script>
<script src="./assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="./assets/plugins/select2/js/select2.min.js"></script>
<script src="./assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="./assets/plugins/datatables/datatables.min.js"></script>
<script src="./assets/js/script.js"></script>
<script src="../assets/js/select2.min.js"></script>
</body>

</html>