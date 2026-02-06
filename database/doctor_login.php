<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "doctor_appointment_db");
if (!$conn) {
    die("Database connection failed");
}

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM doctors WHERE email='$email' AND password='$password'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {

    $row = mysqli_fetch_assoc($result);
    $_SESSION['doctor_id'] = $row['doctor_id'];
    $_SESSION['doctor_email'] = $row['email'];

    header("Location: ../doctor/index.php");
    exit;

} else {

    $_SESSION['doctor_error'] = "Invalid Doctor Email or Password!";
    $_SESSION['active_form'] = "doctor";

    header("Location: ../login.php");
    exit;
}
