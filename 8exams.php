<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('location: login.php');
    exit(); // Ensure script execution stops after redirection
}
require_once './includes/header.php';
require_once './includes/sidebar.php';
include 'includes/conn.php';

// Array of advertisement text for each subject
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
                        <div class="row">
                            <div class="edit-options">
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