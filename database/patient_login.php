<?php
$conn = mysqli_connect("localhost", "root", "", "doctor_appointment_db");

if (!$conn) {
    die("Database connection failed!");
}
session_start();

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM patients WHERE email='$email' AND password='$password'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {

    $row = mysqli_fetch_assoc($result);

    $_SESSION['patient_email'] = $row['email'];
    $_SESSION['patient_name']  = $row['name'];
    $_SESSION['patient_id']    = $row['patient_id'];

    header("Location: ../patient/index.php");
    exit;

} else {

    $_SESSION['patient_error'] = "Invalid Email or Password!";
    $_SESSION['active_form'] = "patient";

    header("Location: ../login.php");
    exit;
}
?>
