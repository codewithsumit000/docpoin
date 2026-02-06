<?php
session_start();
include "../patient/connect.php";

// Total appointments
$total_appointments = mysqli_query($conn, "SELECT COUNT(*) as total FROM appointments");
$total_appointments_count = mysqli_fetch_assoc($total_appointments)['total'];

// Pending appointments
$pending_appointments = mysqli_query($conn, "SELECT COUNT(*) as total FROM appointments WHERE status='Pending'");
$pending_count = mysqli_fetch_assoc($pending_appointments)['total'];

// Approved appointments
$approved_appointments = mysqli_query($conn, "SELECT COUNT(*) as total FROM appointments WHERE status='Approved'");
$approved_count = mysqli_fetch_assoc($approved_appointments)['total'];

// Total doctors
$total_doctors = mysqli_query($conn, "SELECT COUNT(*) as total FROM doctors");
$total_doctors_count = mysqli_fetch_assoc($total_doctors)['total'];

// Today's date
$today = date('Y-m-d');

// Approved today
$approved_today_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM appointments WHERE status='Approved' AND appointment_date='$today'");
$approved_today_count = mysqli_fetch_assoc($approved_today_query)['total'];

// Pending today
$pending_today_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM appointments WHERE status='Pending' AND appointment_date='$today'");
$pending_today_count = mysqli_fetch_assoc($pending_today_query)['total'];

// Doctors available today (based on their schedule)
$doctors_today_query = mysqli_query($conn, "SELECT COUNT(DISTINCT d.doctor_id) as total
    FROM doctors d
    JOIN doctor_schedule s ON d.doctor_id = s.doctor_id
    WHERE s.day = '" . date('l') . "'"
);
$doctors_today_count = mysqli_fetch_assoc($doctors_today_query)['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin dashboard</title>
<link rel="stylesheet" href="admin_sidebar.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="sidebar">
    <div class="logo">🩺 DocPoint</div>
    <div class="menu">
      <a href="#" class="active">Dashboard</a>
      <a href="../admin/appointment_manage.php">Appointments</a>
      <a href="../admin/doctor_manage.php">Doctors</a>
      <a href="../patient/logout.php" class="logout">Logout</a>
    </div>
  </div>

<div class="main-content">
    <h1>Welcome back, Admin 👋</h1>
    
    <div class="cards">
        <div class="card">
            <h3>Total Appointments</h3>
            <p><?php echo $total_appointments_count; ?></p>
        </div>

        <div class="card">
            <h3>Pending Appointments</h3>
            <p><?php echo $pending_count; ?></p>
        </div>

        <div class="card">
            <h3>Approved Appointments</h3>
            <p><?php echo $approved_count; ?></p>
        </div>

        <div class="card">
            <h3>Total Doctors</h3>
            <p><?php echo $total_doctors_count; ?></p>
        </div>
    </div>

    <!-- Today's Stats Chart -->
    <h2 style="margin-top: 40px; color: #0077b6;">Today's Overview</h2>
    <canvas id="todayChart" style="max-width:700px;"></canvas>
</div>

<script>
const ctx = document.getElementById('todayChart').getContext('2d');
const todayChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Approved Patients', 'Pending Patients', 'Doctors Available'],
        datasets: [{
            label: 'Count',
            data: [
                <?php echo $approved_today_count; ?>,
                <?php echo $pending_today_count; ?>,
                <?php echo $doctors_today_count; ?>
            ],
            backgroundColor: ['#38b000', '#ffba08', '#0077b6']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            title: {
                display: true,
                text: "Today's Stats"
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                precision: 0
            }
        }
    }
});
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Poppins", sans-serif;
}

body {
  display: flex;
  background: #f5f8ff;
}

/* Main content area */
.main-content {
    margin-left: 250px; 
    padding: 30px;
    width: calc(100% - 250px);
    background: #f5f8ff;
    min-height: 100vh;
}

/* Welcome message */
.main-content h1 {
    margin-bottom: 30px;
    color: #0077b6;
}

/* Cards container */
.cards {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

/* Individual card */
.card {
    background: white;
    flex: 1 1 200px;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    text-align: center;
    transition: 0.3s;
}

.card h3 {
    color: #0077b6;
    margin-bottom: 10px;
}

.card p {
    font-size: 24px;
    font-weight: 700;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
</style>
</body>
</html>
