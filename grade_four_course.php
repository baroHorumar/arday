<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('location: login.php');
    exit(); // Ensure script execution stops after redirection
}
require_once './includes/header.php';
require_once './includes/sidebar.php';
include 'includes/conn.php';

if (isset($_POST['course_name'])) {
    $course = $_POST['course'];
    $sql = "SELECT * FROM exams WHERE subject = '$course'";
    $result = mysqli_query($conn, $sql); // Execute the SQL query
}
?>
<style>
    /* Add CSS to center the button */
    .edit-options {
        display: flex;
        justify-content: center;
    }

    .edit-delete-btn {
        margin-top: 10px;
        /* Adjust top margin as needed */
    }
</style>

<div class="py-5">
    <div class="content container-fluid">
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($result)) : ?> <!-- Use mysqli_fetch_assoc() to fetch rows from the result set -->
                <div class="col-md-6 col-xl-4 col-sm-12">
                    <div class="blog grid-blog flex-fill">
                        <div class="blog-image">
                            <img class="img-fluid rounded" style="height: 200px; object-fit: cover;" src="assets/img/<?php echo strtolower($row['subject']); ?>.jpg" alt="<?php echo ucfirst($row['subject']); ?>">
                            <div class="blog-views">
                                <i class="feather-eye me-1"></i> <?php echo $row['subject']; ?>
                            </div>
                        </div>
                        <div class="blog-content">
                            <h3 class="blog-title"><?php echo ucfirst($row['subject']); ?></h3>
                            <p><?php echo $row['year']; ?></p>
                        </div>
                        <div class="row justify-content-center"> <!-- Added justify-content-center class here -->
                            <div class="edit-options">
                                <div class="edit-delete-btn">
                                    <form action="start_exam.php" method="post">
                                        <input type="hidden" name="exam_id" value="<?php echo $row['exam_id']; ?>">
                                        <button type="submit" name="start_exam" class="btn btn-primary"><i class="feather-play-circle me-1"></i>Start Exam</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>


<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/feather.min.js"></script>
<script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="assets/js/script.js"></script>
</body>

</html>

<?php
// There's no prepared statement to close here
?>