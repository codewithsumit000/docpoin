<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Dashboard</title>
  <link rel="stylesheet" href="sidebar.css">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }

    body {
      background: #f5f8ff;
      display: flex;
    }

    
    /* Main Dashboard */
    .content {
       flex: 1;
      padding: 30px;
       margin-left: 250px;
      min-height: 100vh;
      background: #f5f8ff;
    }

    .title {
      font-size: 26px;
      font-weight: 600;
      margin-bottom: 25px;
      color: #0077b6;
    }

    .box-container {
      display: flex;
      gap: 20px;
      margin-bottom: 25px;
    }

    .box {
      flex: 1;
      background: linear-gradient(135deg, #0077b6, #00b4d8);
      padding: 20px;
      border-radius: 12px;
      color: white;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .box h3 {
      font-size: 20px;
      margin-bottom: 15px;
    }

    .appointment {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      margin-top: 20px;
    }

    .appointment h3 {
      color: #0077b6;
      margin-bottom: 15px;
    }

    .appointment-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 15px;
      padding-bottom: 10px;
      border-bottom: 1px solid #eee;
    }

    .status {
      background: #0077b6;
      padding: 5px 12px;
      border-radius: 8px;
      color: white;
      font-size: 12px;
    }
  </style>
</head>

<body>
  

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="logo">🩺 DocPoint</div>

    <div class="menu">
      <a href="#" class="active">Dashboard</a>
      <a href="../patient/my_appointment.php">My Appointments</a>
      <a href="../patient/book_appointment.php">Book Appointment</a>
      <a href="../patient/setting.php">Settings</a>
      <a href="../patient/logout.php" class="logout">Logout</a>
    </div>
  </div>
<?php
session_start();
include "connect.php";

if(!isset($_SESSION['patient_id'])){
    header("Location: ../login.html");
    exit();
}
$patient_id = (int)$_SESSION['patient_id'];
$query = "SELECT * FROM patients where patient_id = '$patient_id' ";
$data = mysqli_query($conn,$query);
$app_count="SELECT COUNT(*) FROM appointments where patient_id='$patient_id'";
$data1= mysqli_query($conn,$app_count);
if(mysqli_num_rows($data) > 0){
    $result = mysqli_fetch_assoc($data);
    
   
    
}
$count_row = mysqli_fetch_array($data1);
$appointment_count = $count_row[0];

$today = date('Y-m-d');
$appointment_query = "
    SELECT a.*, d.name AS doctor_name, d.department,a.token
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.doctor_id
    WHERE a.patient_id='$patient_id' AND a.appointment_date >= '$today'AND a.status != 'cancelled'
    ORDER BY a.appointment_date ASC, a.appointment_time ASC
";
$appointment_result = mysqli_query($conn, $appointment_query);




?>

  <!-- Main Page -->
  <div class="content">
    
<div class="title">Welcome Back, <?php echo $result['name'] ;?>👋</div>

    <div class="box-container">
      <div class="box">
        <h3>Total Appointments</h3>
        <p><?php echo $appointment_count; ?></p>
      </div>

      
    </div>

    <!-- Appointment List -->
    <div class="appointment">
      <h3>Upcoming Appointments</h3>

      <?php if(mysqli_num_rows($appointment_result) > 0): ?>
          <?php while($row = mysqli_fetch_assoc($appointment_result)): ?>
              <div class="appointment-item">
                  <span>Dr. <?php echo $row['doctor_name']; ?> — <?php echo $row['department']; ?> (<?php echo $row['appointment_date']; ?> <?php echo $row['appointment_time']; ?>)</span>
                  <span class="status <?php echo ucfirst($row['status']); ?>"><?php echo ucfirst($row['status']); ?>
                <?php if ($row['status'] === 'approved' && !empty($row['token'])): ?>
                <span class="token">
                    Token number : <b><?php echo $row['token']; ?></b>
                </span>
            <?php endif; ?></span>
              </div>
          <?php endwhile; ?>
      <?php else: ?>
          <p>No upcoming appointments.</p>
      <?php endif; ?>
    </div>

  </div>


</body>
</html>
