<?php
require 'includes/header.php';
session_start();
require 'includes/conn.php';

if (isset($_POST['email']) && isset($_POST['password'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];
  $stmt = $conn->prepare("SELECT student_id, student_name, role,grade, password FROM students WHERE username = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
      $_SESSION['login'] = true;
      $_SESSION['student_id'] = $row['student_id'];
      $_SESSION['name'] = $row['student_name'];
      $_SESSION['grade'] = $row['grade'];
      // select * from permission where student_id = $_SESSION['student_id'];
      if ($row['role'] == 'admin') {
        header('Location: ./admin/index.php');
        exit;
      } else {
        if ($row['grade'] == 4) {
          header('Location: 4exams.php');
          exit;
        }
        if ($row['grade'] == 3) {
          header('Location: grade_four.php');
          exit;
        } else {
          header('Location: 8exams.php');
          exit;
        }
      }
    } else {
      $_SESSION['login'] = false;
      header('Location: login.php'); // Redirect back to login page
      exit;
    }
  } else {
    $_SESSION['login'] = false;
    header('Location: login.php'); // Redirect back to login page
    exit;
  }
}
?>

<style>
  .card {
    max-width: 400px;
    width: 100%;
    height: auto;
    margin: 0 auto;
  }
</style>
<main class="main" id="top">
  <div class="container-fluid">
    <div class="row min-vh-100 flex-center g-0">
      <div class="col-12 d-flex justify-content-center align-items-center">
        <div class="card overflow-hidden">
          <div class="card-body p-4">
            <h3 class="text-center mb-4">Account Login</h3>
            <form action="login.php" method="post">
              <div class="mb-3">
                <label class="form-label" for="card-email">Email address</label>
                <input class="form-control" name="email" id="card-email" type="text" />
              </div>
              <div class="mb-3">
                <label class="form-label" for="card-password">Password</label>
                <input class="form-control" name="password" id="card-password" type="password" />
              </div>
              <div class="mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="card-checkbox" checked="checked" />
                  <label class="form-check-label" for="card-checkbox">Remember me</label>
                </div>
              </div>
              <div class="mb-3">
                <button class="btn btn-primary d-block w-100 mt-3" type="submit" name="submit">Log in</button>
              </div>
            </form>
            <div class="text-center dont-have">don't have an account? <a href="register.php">Register Now</a></div>

          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php require 'includes/footer.php'; ?>