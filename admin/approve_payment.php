<?php
// Include your database connection
include './includes/conn.php';

// Check if payment_id is provided via POST
if (isset($_POST['payment_id'])) {
    // Sanitize the input
    $payment_id = mysqli_real_escape_string($conn, $_POST['payment_id']);

    // Fetch the current status of the payment
    $query = "SELECT status FROM payment WHERE payment_id = '$payment_id'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $current_status = $row['status'];

        // Toggle status between 'Approved' and 'Pending'
        $new_status = ($current_status == 'Approved') ? 'Pending' : 'Approved';

        // Update the status in the database
        $update_query = "UPDATE payment SET status = '$new_status' WHERE payment_id = '$payment_id'";
        $update_result = mysqli_query($conn, $update_query);

        if ($update_result) {
            // Return the updated status as JSON
            echo json_encode(['status' => $new_status]);
        } else {
            echo json_encode(['status' => 'Error']);
        }
    } else {
        echo json_encode(['status' => 'Error']);
    }
} else {
    echo json_encode(['status' => 'Error']);
}
