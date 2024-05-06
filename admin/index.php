<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('location: login.php');
    exit(); // Ensure script execution stops after redirection
}
require_once './includes/header.php';
require_once './includes/sidebar.php';
include './includes/conn.php';
$query = "SELECT payment_id, student_id, subject_id, payed_at, status FROM payment ORDER BY payed_at DESC";
$result = mysqli_query($conn, $query);
?>
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Alumni</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item active">Alumni</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a class="btn btn-primary filter-btn" href="javascript:void(0);" id="filter_search">
                        <i class="fas fa-filter"></i>
                    </a>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-stripped table-hover datatable" id="paymentTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Student ID</th>
                                        <th>Subject ID</th>
                                        <th>Payed At</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                        <tr>
                                            <!-- Display data from the database -->
                                            <td><?php echo $row['payment_id']; ?></td>
                                            <td><?php echo $row['student_id']; ?></td>
                                            <td><?php echo $row['subject_id']; ?></td>
                                            <td><?php echo $row['payed_at']; ?></td>
                                            <td><?php echo $row['status']; ?></td>
                                            <td>
                                                <?php

                                                if ($row['status'] == 'Approved') {
                                                ?>
                                                    <button class="btn btn-warning approveBtn" data-id="<?php echo $row['payment_id']; ?>">Disapprove</button>
                                                <?php
                                                } else {
                                                ?>
                                                    <button class="btn btn-success approveBtn" data-id="<?php echo $row['payment_id']; ?>">Approve</button>

                                                <?php

                                                } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
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
<script>
    $(document).ready(function() {
        // AJAX request to update status
        $('.approveBtn').click(function() {
            var paymentId = $(this).data('id');
            var button = $(this); // Store the button element for later use

            $.ajax({
                url: 'approve_payment.php', // PHP script to handle the approval
                method: 'POST',
                data: {
                    payment_id: paymentId
                },
                success: function(response) {
                    // Update status in the table
                    var status = JSON.parse(response).status;
                    var row = button.closest('tr');
                    row.find('td:eq(4)').text(status);


                }
            });
        });
    });
</script>
<script src="./assets/js/jquery-3.6.0.min.js"></script>
<script src="./assets/js/bootstrap.bundle.min.js"></script>
<script src="./assets/js/feather.min.js"></script>
<script src="./assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="./assets/plugins/select2/js/select2.min.js"></script>
<script src="./assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="./assets/plugins/datatables/datatables.min.js"></script>
<script src="./assets/js/script.js"></script>
<script src="../assets/js/select2.min.js"></script>