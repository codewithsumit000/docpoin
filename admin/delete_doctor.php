<?php
include "../patient/connect.php";

if(isset($_GET['id'])){
    $id = (int)$_GET['id'];

    // Get the photo name
    $res = mysqli_query($conn, "SELECT photo FROM doctors WHERE doctor_id='$id'");
    if(mysqli_num_rows($res) > 0){
        $row = mysqli_fetch_assoc($res);
        if(!empty($row['photo']) && file_exists("../doctor_photo/".$row['photo'])){
            unlink("../doctor_photo/".$row['photo']); // delete photo
        }
    }

    // Delete doctor from DB
    mysqli_query($conn, "DELETE FROM doctors WHERE doctor_id='$id'");

    header("Location: doctor_manage.php"); // redirect back to manage page
    exit();
}
?>
