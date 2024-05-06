<?php
require 'includes/header.php';
//require 'includes/sidebar.php';
session_start();

if (!isset($_SESSION['login'])) {
    header('location: login.php');
    exit(); // Ensure script execution stops after redirection
}

require 'includes/conn.php';

// Initialize variables for storing submitted values
$tixValue = isset($_POST['tix']) ? $_POST['tix'] : '';
$subjectValue = isset($_POST['subject']) ? $_POST['subject'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tix']) && isset($_POST['subject']) && isset($_POST['payment'])) {
    // Extract form data
    $tix = $_POST['tix'];
    $subject_id = $_POST['subject'];
    // echo $_SESSION['student_id'];
    // Check if Tix is 10 numbers
    if (strlen($tix) !== 10 || !ctype_digit($tix)) {
        echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Error!</strong> Tix must be exactly 10 numbers!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
    } else {
        // Check if the tix already exists in the database
        $check_query = "SELECT payment_id FROM payment WHERE payment_id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("i", $tix);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows < 0) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> Tix already used!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
        } else {
            echo $subject_id;
            $insert_query = "INSERT INTO payment (payment_id, student_id, subject_id, payed_at, status) VALUES (?, ?, ?, CURRENT_TIMESTAMP(), 'Pending')";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("iii", $tix, $_SESSION['student_id'], $subject_id);
            try {
                $insert_stmt->execute();
                if ($_SESSION['grade'] == 4) {
                    header('Location: 4exams.php');
                } else {
                    header('Location: 8exams.php');
                }
            } catch (mysqli_sql_exception $e) {
                // Display error message if duplicate entry occurs
                if ($e->getCode() == '1062') {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Duplicate entry for payment ID!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                } else {
                    throw $e; // Rethrow other exceptions
                }
            }
        }
    }
}

if (isset($subject_stmt)) {
    $subject_stmt->close();
}
if (isset($insert_stmt)) {
    $insert_stmt->close();
}
?>

<main class="main" id="top">
    <div class="container-fluid">
        <div class="row min-vh-100 flex-center g-0">
            <div class="col-12 d-flex justify-content-center align-items-center">
                <div class="card overflow-hidden">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4">Purchase</h3>
                        <p class="text-center mb-4">To complete your purchase, kindly send 4,000 SL Sh for a single subject or 10,000 SL Sh for all subjects to the following number: 4541822.</p>
                        <form action="payment.php" method="post">
                            <div class="form-group">
                                <label for="tix">Tix:</label>
                                <input type="number" class="form-control" id="tix" name="tix" placeholder="Enter your Tix" value="<?php echo $tixValue; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="subject">Select Subject:</label>
                                <select class="form-control" id="subject" name="subject">
                                    <?php
                                    $sql = "SELECT subject_id, subject_name FROM subjects where grade = 4 order by subject_id desc";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $subject_id = $row['subject_id'];
                                            $subject_name = $row['subject_name'];
                                    ?>
                                            <option value="<?php echo $subject_id; ?>"><?php echo $subject_name; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <button type="submit" name="payment" class="btn btn-primary">Submit Payment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require 'includes/footer.php'; ?>