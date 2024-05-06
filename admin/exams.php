<?php
include './includes/header.php';
include './includes/sidebar.php';
include './includes/conn.php';

$query = "SELECT exam_id, year, subject, total_marks FROM exams";
$result = mysqli_query($conn, $query);
?>

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Exams</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item active">Exams</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <h5 class="card-title">Exam Management</h5>
                            </div>
                            <div class="col-auto">
                                <a href="create_exams.php" class="btn btn-sm btn-primary rounded-pill" "><button class=" btn btn-sm btn-primary rounded-pill">Add Exam</button></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-stripped table-hover datatable" id="examTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Year</th>
                                        <th>Subject</th>
                                        <th>Total Marks</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                        <tr>
                                            <td><?php echo $row['exam_id']; ?></td>
                                            <td><?php echo $row['year']; ?></td>
                                            <td><?php echo $row['subject']; ?></td>
                                            <td><?php echo $row['total_marks']; ?></td>
                                            <td>
                                                <div class="row gx-1">
                                                    <div class="col">
                                                        <form action="upload.php" method="post">
                                                            <input type="hidden" name="exam_id" value="<?php echo $row['exam_id']; ?>">
                                                            <button name="upload_id" type="submit" class="btn btn-sm btn-white text-primary me-2">
                                                                <i data-feather="upload"></i> Upload
                                                            </button>
                                                        </form>

                                                    </div>
                                                    <div class="col">
                                                        <form action="edit_exam.php">
                                                            <input type="hidden" name="exam_id" id="exam_id" value="<?php echo $row['exam_id']; ?>">
                                                            <button class="btn btn-sm btn-white text-success me-2"><i class="far fa-edit me-1"></i> Edit</button>
                                                        </form>
                                                    </div>
                                                    <div class="col">
                                                        <form action="edit_exam.php">
                                                            <input type="hidden" name="exam_id" id="exam_id" value="<?php echo $row['exam_id']; ?>">
                                                            <button class="btn btn-sm btn-white text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_paid"><i class="far fa-trash-alt me-1"></i>Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php
                                        $count++;
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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