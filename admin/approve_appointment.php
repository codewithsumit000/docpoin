<?php
session_start();
include "../patient/connect.php";

if(isset($_GET['appointment_id'])){
    $id = $_GET['appointment_id'];
    mysqli_query($conn, "UPDATE appointments SET status='Approved' WHERE appointment_id='$id'");
}

header("Location: appointment_manage.php");
exit;
?>
