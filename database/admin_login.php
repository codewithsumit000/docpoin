<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "doctor_appointment_db");

if (!$conn) {
    die("Database connection failed!");
}

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM admin WHERE email='$email' AND password='$password'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    $_SESSION['admin_email'] = $row['email'];
    $_SESSION['admin_id'] = $row['admin_id'];

    header("Location: ../admin/index.php");
    exit;
} else {
    // store error in session
    $_SESSION['admin_error'] = "Invalid Email or Password!";
    header("Location: ../admin_login.php");
    exit;
}
?>
