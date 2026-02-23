<?php
session_start();
include "../patient/connect.php";

if(isset($_GET['appointment_id'])){
    $id = $_GET['appointment_id'];
    $token = rand(100,999);
    mysqli_query($conn, "UPDATE appointments SET status='Approved',token ='$token' WHERE appointment_id='$id'");
}

header("Location: appointment_manage.php");
exit;
?>
