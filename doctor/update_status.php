<?php
session_start();
include('./connect.php');

if(!isset($_SESSION['doctor_id'])){
    header("Location: ../login.html");
    exit();
}

if(isset($_GET['id']) && isset($_GET['status'])) {
    $appointment_id = (int)$_GET['id'];
    $status = $_GET['status'];
    $doctor_id = $_SESSION['doctor_id'];

    // Validate status
    $valid_statuses = ['approved', 'cancelled'];
    if(!in_array($status, $valid_statuses)){
        header("Location: ./my_appointment.php");
        exit();
    }

    // Verify the appointment belongs to this doctor
    $verify_query = "SELECT * FROM appointments WHERE appointment_id = '$appointment_id' AND doctor_id = '$doctor_id'";
    $verify_result = mysqli_query($conn, $verify_query);

    if(mysqli_num_rows($verify_result) > 0) {
        // Update the status
        $update_query = "UPDATE appointments SET status = '$status' WHERE appointment_id = '$appointment_id'";
        mysqli_query($conn, $update_query);
    }
}

// Redirect back
header("Location: ./my_appointment.php");
exit();
?>
