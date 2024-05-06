<?php
require './includes/conn.php';
require './includes/header.php';
require './includes/sidebar.php';

$student_data = []; // Initialize $student_data as an empty array

if (isset($_POST['edit_student'])) {
    $student_id = $_POST['student_id'];
    $sql = "SELECT * FROM students WHERE student_id=$student_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $student_data = $result->fetch_assoc();
    } else {
        echo "No student found with ID: $student_id";
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_now'])) {
    $student_id = $_POST['student_id'];
    $student_name = $_POST['student_name'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];

    // Update student data in the database
    $sql = "UPDATE students SET student_name='$student_name', address='$address', phone_number='$phone_number' WHERE student_id=$student_id";

    if ($conn->query($sql) === TRUE) {
        header('location: profile.php');
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>

<div class="page-wrapper d-flex justify-content-center align-items-center">
    <div class="login-wrapper">
        <div class="container">
            <div class="loginbox">
                <div class="login-right">
                    <div class="login-right-wrap">
                        <h2>Edit Student</h2>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                            <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                            <div class="form-group">
                                <label for="student_name">Student Name:</label>
                                <input type="text" id="student_name" name="student_name" class="form-control" value="<?php echo isset($student_data['student_name']) ? $student_data['student_name'] : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="address">Address:</label>
                                <input type="text" id="address" name="address" class="form-control" value="<?php echo isset($student_data['address']) ? $student_data['address'] : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="phone_number">Phone Number:</label>
                                <input type="text" id="phone_number" name="phone_number" class="form-control" value="<?php echo isset($student_data['phone_number']) ? $student_data['phone_number'] : ''; ?>">
                            </div>
                            <button type="submit" name="edit_now" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/feather.min.js"></script>
<script src="assets/js/script.js"></script>
<script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
</body>

</html>