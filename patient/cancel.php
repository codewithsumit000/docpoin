<?php
session_start();
include('connect.php');

if (!isset($_SESSION['patient_id'])) {
    exit("Please login first.");
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $patient_id = $_SESSION['patient_id'];

    // Delete the appointment for this patient
    mysqli_query($conn, "DELETE FROM appointments WHERE appointment_id='$id' AND patient_id='$patient_id'");

    // Go back to My Appointments
    header("Location: my_appointment.php");
    exit;
} else {
    header("Location: my_appointment.php");
    exit;
}
?>
