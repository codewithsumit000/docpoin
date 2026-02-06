<?php
session_start();
include ('connect.php');

$patient_id = $_SESSION['patient_id'];

// Upcoming appointments
$upcoming_sql = "
SELECT a.*, d.name AS doctor_name, d.department
FROM appointments a
JOIN doctors d ON a.doctor_id = d.doctor_id
WHERE a.patient_id = '$patient_id'
AND a.appointment_date >= CURDATE()
AND a.status IN ('pending','approved')
ORDER BY a.appointment_date ASC
";

$upcoming = mysqli_query($conn, $upcoming_sql);

// Past appointments
$past_sql = "
SELECT a.*, d.name AS doctor_name, d.department
FROM appointments a
JOIN doctors d ON a.doctor_id = d.doctor_id
WHERE a.patient_id = '$patient_id'
AND a.appointment_date < CURDATE()
ORDER BY a.appointment_date DESC
";

$past = mysqli_query($conn, $past_sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <link rel="stylesheet" href="sidebar.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">🩺 DocPoint</div>

    <div class="menu">
      <a href="../patient/index.php">Dashboard</a>
      <a href="../patient/my_appointment.php" class="active">My Appointments</a>
      <a href="../patient/book_appointment.php">Book Appointment</a>
      <a href="#">Settings</a>
      <a href="../patient/logout.php" class="logout">Logout</a>
    </div>
</div>

<div class="container">
    <h1>My Appointments</h1>

    <!-- Upcoming -->
    <h2>Upcoming Appointments</h2>
    <div class="table-container">
        <?php if(mysqli_num_rows($upcoming) > 0): ?>
        <table>
            <tr>
                <th>Doctor</th>
                <th>Department</th>
                <th>Date</th>
                <th>Action</th>
            </tr>

            <?php while($row = mysqli_fetch_assoc($upcoming)): ?>
            <tr>
                <td><?= $row['doctor_name'] ?></td>
                <td><?= $row['department'] ?></td>
                <td><?= $row['appointment_date'] ?></td>
               
                
                <td>
                    <a href="cancel.php?id=<?= $row['appointment_id'] ?>" class="cancel-btn">Cancel</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
            <p class="empty">No upcoming appointments.</p>
        <?php endif; ?>
    </div>

    <!-- Past -->
    <h2>Past Appointments</h2>
    <div class="table-container">
        <?php if(mysqli_num_rows($past) > 0): ?>
        <table>
            <tr>
                <th>Doctor</th>
                <th>Department</th>
                <th>Date</th>
                
                
            </tr>

            <?php while($row = mysqli_fetch_assoc($past)): ?>
            <tr>
                <td><?= $row['doctor_name'] ?></td>
                <td><?= $row['department'] ?></td>
                <td><?= $row['appointment_date'] ?></td>
                
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
            <p class="empty">No past appointments.</p>
        <?php endif; ?>
    </div>
</div>

</body>

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
    /* Main content */
.container {
   /* keep as per your layout */
  padding: 30px;
  width: calc(100% - 260px);
  margin-left: 250px; /* leave space for fixed sidebar */
 
}

.container h1 {
  font-size: 28px;
  color: #0077b6;
  margin-bottom: 25px;
}

.container h2 {
  font-size: 22px;
  color: #0077b6;
  margin-top: 30px;
}

/* Table container */
.table-container {
  background: white;
  border-radius: 12px;
  padding: 20px;
  margin-top: 15px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

/* Table styling */
table {
  width: 100%;
  border-collapse: collapse;
}

table th {
  background: linear-gradient(135deg, #0077b6, #00b4d8);
  color: white;
  padding: 12px;
  text-align: left;
  font-size: 15px;
  border-radius: 5px;
}

table td {
  padding: 12px;
  border-bottom: 1px solid #e5e5e5;
  font-size: 14px;
}

/* Status badges */
.status {
  padding: 5px 10px;
  border-radius: 6px;
  font-weight: 500;
  color: white;
  display: inline-block;
}

.status.pending {
  background: #ffb703;
}

.status.approved {
  background: #38b000;
}

.status.cancelled {
  background: #d00000;
}

/* Cancel button */
.cancel-btn {
  background: #d00000;
  color: white;
  padding: 6px 12px;
  border-radius: 6px;
  text-decoration: none;
  font-size: 13px;
  transition: background 0.3s;
}

.cancel-btn:hover {
  background: #9b0000;
}

/* Empty message */
.empty {
  padding: 10px;
  color: gray;
  font-style: italic;
}

</style>

</html>
