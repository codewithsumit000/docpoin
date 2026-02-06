<?php
session_start();
include "./connect.php";

if(!isset($_SESSION['patient_id'])){
    header("Location: ../login.html");
    exit();
}

$patient_id = $_SESSION['patient_id'];
$error = "";
$success = "";

// Fetch patient data
$user_q = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id='$patient_id'");
$user = mysqli_fetch_assoc($user_q);

// Update profile
if(isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    // Update name
    if($name !== $user['name']) {
        mysqli_query($conn, "UPDATE patients SET name='$name' WHERE patient_id='$patient_id'");
        $success = "Profile updated successfully!";
    }

    // Change password (optional)
    if(!empty($old_password) && !empty($new_password)) {
        if($old_password === $user['password']) {
            mysqli_query($conn, "UPDATE patients SET password='$new_password' WHERE patient_id='$patient_id'");
            $success = "Password changed successfully!";
        } else {
            $error = "Old password is incorrect!";
        }
    }

    // Refresh data
    $user_q = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id='$patient_id'");
    $user = mysqli_fetch_assoc($user_q);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Settings</title>
<link rel="stylesheet" href="sidebar.css">

<style>
*{
  margin:0; padding:0; box-sizing:border-box;
  font-family:"Poppins", sans-serif;
}
body{
  background:#f5f8ff;
  display:flex;
}
.content{
  flex:1;
  padding:30px;
  margin-left:250px;
  min-height:100vh;
}
.title{
  font-size:26px;
  font-weight:600;
  margin-bottom:25px;
  color:#0077b6;
}
.profile-card{
  background:white;
  padding:25px;
  max-width:450px;
  border-radius:12px;
  box-shadow:0 4px 12px rgba(0,0,0,0.1);
}
.profile-card h3{
  color:#0077b6;
  margin-bottom:15px;
}
.profile-card label{
  font-size:14px;
  font-weight:500;
}
.profile-card input{
  width:100%;
  padding:10px;
  margin:8px 0 15px;
  border:1px solid #ddd;
  border-radius:8px;
}
.profile-card button{
  width:100%;
  padding:10px;
  border:none;
  border-radius:8px;
  background:linear-gradient(135deg,#0077b6,#00b4d8);
  color:white;
  font-size:15px;
  cursor:pointer;
}
.profile-card button:hover{ opacity:0.9; }
.message{
  margin-bottom:12px;
  font-weight:600;
}
.error{ color:red; }
.success{ color:green; }
hr{
  margin:20px 0;
  border:0;
  border-top:1px solid #eee;
}
</style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
  <div class="logo">🩺 DocPoint</div>
  <div class="menu">
    <a href="../patient/index.php">Dashboard</a>
    <a href="../patient/my_appointment.php">My Appointments</a>
    <a href="../patient/book_appointment.php">Book Appointment</a>
    <a href="#" class="active">Settings</a>
    <a href="../patient/logout.php" class="logout">Logout</a>
  </div>
</div>

<!-- Main Content -->
<div class="content">
  <div class="title">Account Settings</div>

  <div class="profile-card">
    <h3>Update Profile</h3>

    <?php if($error): ?>
      <div class="message error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if($success): ?>
      <div class="message success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Name</label>
      <input type="text" name="name" value="<?php echo $user['name']; ?>" required>

      <hr>

      <label>Old Password</label>
      <input type="password" name="old_password">

      <label>New Password</label>
      <input type="password" name="new_password">

      <button type="submit" name="update">Save Changes</button>
    </form>
  </div>
</div>

</body>
</html>
