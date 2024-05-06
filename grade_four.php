<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('location: login.php');
    exit(); // Ensure script execution stops after redirection
}
require_once './includes/header.php';
require_once './includes/sidebar.php';
include 'includes/conn.php';

$permissions = array(
    "Science" => false,
    "Social" => false,
    "Somali4" => false,
    "English4" => false
);


// Fetch subject IDs for the student from payment table
$stmt = $conn->prepare("SELECT subject_id FROM payment WHERE status = 'Approved' AND student_id = ?");
$stmt->bind_param("i", $_SESSION['student_id']);
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
        if ($allFull || ($singleSubject && in_array($subject, $subjectNames))) {
            $permissions[$subject] = true;
        } else {
            $permissions[$subject] = false;
        }
    }
}

// Hardcoded advertisements for demonstration purposes
$advertisements = array(
    "Science" => "Master the elements of success with our final Science exam prep. Build a strong foundation for your future in Science.",
    "Social" => "Unlock your potential in social with our expertly crafted study guides. Ace your final social exams effortlessly.",
    "Somali4" => "Solve the equation to success with our final Mathematics exam resources. Boost your confidence and skills in Somali.",
    "English4" => "Reach new heights in English with our comprehensive study materials. Prepare to conquer your final English exams."
);
?>

<style>
    /* Add this CSS to center the buttons */
    .edit-options {
        display: flex;
        justify-content: center;
    }

    .edit-delete-btn,
    .text-end {
        margin: 5px;
        /* Adjust margin as needed */
    }
</style>
<div class="py-5">
    <div class="content container-fluid">
        <div class="row">
            <?php foreach ($advertisements as $subject => $advertisement) :
                $capitalizedSubject = ucfirst($subject);
                $imageURL = "assets/img/{$subject}.png";
            ?>
                <div class="col-md-6 col-xl-4 col-sm-12">
                    <div class="blog grid-blog flex-fill">
                        <div class="blog-image">
                            <img class="img-fluid rounded" style="height: 200px; object-fit: cover;" src="<?php echo $imageURL; ?>" alt="<?php echo $capitalizedSubject; ?>"></a>
                            <div class="blog-views">
                                <i class="feather-eye me-1"></i> <?php echo $subject; ?>
                            </div>
                        </div>
                        <div class="blog-content">
                            <h3 class="blog-title"><?php echo $capitalizedSubject; ?></h3>
                            <p><?php echo $advertisement; ?></p>
                        </div>
                        <div class="row justify-content-center"> <!-- Added justify-content-center class here -->
                            <div class="edit-options">
                                <?php if ($permissions[$subject]) : ?>
                                    <div class="edit-delete-btn">
                                        <form action="enter_course.php" method="post">
                                            <input type="hidden" name="course" value="<?php echo $subject; ?>">
                                            <button type="submit" name="course_name" class="btn btn-primary"><i class="feather-shopping-cart me-1"></i>Enter</button>
                                        </form>
                                    </div>
                                <?php else : ?>
                                    <div class="edit-delete-btn">
                                        <form action="payment.php" method="post">
                                            <input type="hidden" name="course" value="<?php echo $subject; ?>">
                                            <button type="submit" class="btn btn-primary"><i class="feather-shopping-cart me-1"></i>Buy</button>
                                        </form>
                                    </div>
                                    <div class="text-end inactive-style">
                                        <form action="testing_exam.php" method="post">
                                            <input type="hidden" name="course" value="<?php echo $subject; ?>">
                                            <button type="submit" class="btn btn-danger"><i class="feather-play-circle me-1"></i>Try</button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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