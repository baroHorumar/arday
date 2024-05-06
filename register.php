<?php
require './includes/conn.php';
$studentName  = $address = $school_name = $address = $username = $confirm_password = $Phone_number = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    function validateInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    $studentName = validateInput($_POST["student_name"]);
    $address = validateInput($_POST["address"]);
    $full_name = validateInput($_POST["student_name"]);
    $username = validateInput($_POST["username"]);
    $grade = validateInput($_POST["grade"]);
    $phoneNumber = validateInput($_POST["Phone_number"]);
    $password = validateInput($_POST["password"]);
    $confirmPassword = validateInput($_POST["confirm_password"]);
    $role = 'student';

    // Check if passwords match
    if ($password !== $confirmPassword) {
        $errorMessage = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Error!</strong> Passwords do not match.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT * FROM students WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errorMessage = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Error!</strong> Username already taken!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO students (student_name,  username, password, address, phone_number, role, grade) VALUES (?, ?, ?, ?, ?, ?,?)");
            $stmt->bind_param("sssssss", $full_name, $username, $hashedPassword, $address, $phoneNumber, $role, $grade);
            if ($stmt->execute()) {
                header('location: login.php');
            } else {
                $errorMessage = "error unable to insert information into the database";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="main-wrapper login-body">
        <div class="login-wrapper">
            <div class="container">
                <?php
                if (isset($errorMessage)) {
                    echo $errorMessage;
                }
                ?>
                <div class="loginbox">
                    <div class="login-right">
                        <div class="login-right-wrap">
                            <h1>Register</h1>
                            <p class="account-subtitle">Welcome to Arday-kaabe</p>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                <div class="form-group">
                                    <label class="form-control-label">Student Name</label>
                                    <input class="form-control" type="text" name="student_name" value="<?php echo htmlspecialchars($studentName); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label">Phone Number</label>
                                    <input class="form-control" type="text" name="Phone_number" value="<?php echo htmlspecialchars($Phone_number); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label">School Name </label>
                                    <input class="form-control" type="text" name="school_name" value="<?php echo htmlspecialchars($school_name); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label">Grade</label>
                                    <select class="form-control" name="grade">
                                        <option value="4">4</option>
                                        <option value="8">8</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">Address</label>
                                    <input class="form-control" type="text" name="address" value="<?php echo htmlspecialchars($address); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label">Username</label>
                                    <input class="form-control" type="text" name="username" value="<?php echo htmlspecialchars($username); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label">Password</label>
                                    <input class="form-control" type="password" name="password" value="<?php echo htmlspecialchars($password); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label">Confirm Password</label>
                                    <input class="form-control" type="password" name="confirm_password" value="<?php echo htmlspecialchars($confirm_password); ?>">
                                </div>
                                <div class="form-group mb-0">
                                    <button class="btn btn-lg btn-block btn-primary w-100" type="submit" name="register">Register</button>
                                </div>
                            </form>
                            <div class="text-center dont-have">Already have an account? <a href="login.php">Login</a></div>
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