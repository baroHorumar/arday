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
    "Biology" => "Unlock your potential in Biology with our expertly crafted study guides. Ace your GCSE Biology exams effortlessly.",
    "Chemistry" => "Master the elements of success with our GCSE Chemistry exam prep. Build a strong foundation for your future in Chemistry.",
    "Maths" => "Solve the equation to success with our GCSE Mathematics exam resources. Boost your confidence and skills in Maths.",
    "Physics" => "Reach new heights in Physics with our comprehensive study materials. Prepare to conquer your GCSE Physics exams.",
    "English" => "Excel in English with our GCSE English exam resources. Enhance your reading and writing skills for top-notch results.",
    "Somali" => "Empower your learning journey with our GCSE Somali exam prep. Navigate the language with ease and confidence.",
    "History" => "Uncover the past and triumph in your GCSE History exams. Dive deep into key events and concepts with our insightful resources.",
    "Tarbiya" => "Cultivate success with our GCSE Tarbiya exam resources. Strengthen your understanding and excel in Tarbiya studies.",
    "Arabic" => "Embark on a journey of linguistic excellence with our GCSE Arabic exam prep. Master the language with confidence.",
    "Geography" => "Explore the world of Geography and ace your GCSE exams. From maps to ecosystems, we've got you covered."
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
                $imageURL = "assets/img/{$subject}.jpg";
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