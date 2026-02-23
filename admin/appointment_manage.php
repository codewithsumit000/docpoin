<?php
session_start();
include "../patient/connect.php";

// Pending Appointments
$sql_pending = "SELECT a.*, p.name AS patient_name, d.name AS doctor_name
        FROM appointments a
        JOIN patients p ON a.patient_id = p.patient_id
        JOIN doctors d ON a.doctor_id = d.doctor_id
        WHERE a.status = 'Pending'
        ORDER BY a.appointment_date ASC, a.appointment_time ASC";
$result_pending = mysqli_query($conn, $sql_pending);

// Today's Appointments Summary
$sql_today = "SELECT d.name AS doctor_name, COUNT(*) AS total_appointments
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.doctor_id
        WHERE a.appointment_date = CURDATE()
        AND a.status='Approved'
        GROUP BY d.doctor_id";
$result_today = mysqli_query($conn, $sql_today);

// All Appointments with Patient Info
$sql_all = "SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status,
            p.name AS patient_name, p.email AS patient_email, p.phone AS patient_phone,
            d.name AS doctor_name, d.department AS doctor_department
            FROM appointments a
            JOIN patients p ON a.patient_id = p.patient_id
            JOIN doctors d ON a.doctor_id = d.doctor_id
            ORDER BY a.appointment_date ASC, a.appointment_time ASC";
$result_all = mysqli_query($conn, $sql_all);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments</title>
    <link rel="stylesheet" href="admin_sidebar.css">
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

        .main-contain {
            margin-left: 250px;
            padding: 30px;
            width: calc(100% - 250px);
            min-height: 100vh;
            background: #f5f8ff;
        }

        .main-contain h1 {
            margin-bottom: 20px;
            color: #0077b6;
        }

        .main-contain table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .main-contain th,
        .main-contain td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .main-contain th {
            background: linear-gradient(135deg, #0077b6, #00b4d8);
            color: white;
        }

        .main-contain tr:hover {
            background-color: #e0f7fa;
        }

        .main-contain a {
            text-decoration: none;
            display: inline-block;
            width: 28px;
            height: 28px;
            text-align: center;
            line-height: 28px;
            border-radius: 50%;
            color: #fff;
            font-size: 16px;
            margin-right: 5px;
            transition: 0.3s;
        }

        .main-contain a.approve {
            background-color: #00b300;
        }

        .main-contain a.approve:hover {
            background-color: #007700;
        }

        .main-contain a.reject {
            background-color: #ff5252;
        }

        .main-contain a.reject:hover {
            background-color: #c40000;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="logo">🩺 DocPoint</div>
        <div class="menu">
            <a href="../admin/index.php" class="active">Dashboard</a>
            <a href="../admin/appointment_manage.php">Appointments</a>
            <a href="../admin/doctor_manage.php">Doctors</a>
            <a href="../patient/logout.php" class="logout">Logout</a>
        </div>
    </div>

    <div class="main-contain">
        <!-- Pending Appointments Table -->
        <h1>Pending Appointments</h1>
        <table border="1" cellpadding="10">
            <tr>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Appointment Date</th>
                <th>Appointment Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php if (mysqli_num_rows($result_pending) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result_pending)): ?>
                    <tr>
                        <td><?php echo $row['patient_name']; ?></td>
                        <td><?php echo $row['doctor_name']; ?></td>
                        <td><?php echo $row['appointment_date']; ?></td>
                        <td><?php echo $row['appointment_time']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td>
                            <a href='approve_appointment.php?appointment_id=<?php echo $row['appointment_id']; ?>' class='approve' title='Approve'>&#10004;</a>
                            <a href='reject_appointment.php?appointment_id=<?php echo $row['appointment_id']; ?>' class='reject' title='Reject'>&#10006;</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; color:#666;">No pending appointments.</td>
                </tr>
            <?php endif; ?>
        </table>

        <!-- Today's Appointments Summary Table -->
        <h1>Today's Appointments Summary</h1>
        <table border="1" cellpadding="10">
            <tr>
                <th>Doctor</th>
                <th>Total Appointments Today</th>
            </tr>
            <?php if (mysqli_num_rows($result_today) > 0): ?>
                <?php while ($row_today = mysqli_fetch_assoc($result_today)): ?>
                    <tr>
                        <td><?php echo $row_today['doctor_name']; ?></td>
                        <td><?php echo $row_today['total_appointments']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2" style="text-align:center; color:#666;">No appointments scheduled for today.</td>
                </tr>
            <?php endif; ?>
        </table>

        <!-- All Appointments Table -->
        <h1>All Appointments</h1>
        <table border="1" cellpadding="10">
            <tr>
                <th>Patient Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Doctor</th>
                <th>Department</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
            <?php if (mysqli_num_rows($result_all) > 0): ?>
                <?php while ($row_all = mysqli_fetch_assoc($result_all)): ?>
                    <tr>
                        <td><?php echo $row_all['patient_name']; ?></td>
                        <td><?php echo $row_all['patient_email']; ?></td>
                        <td><?php echo $row_all['patient_phone']; ?></td>
                        <td><?php echo $row_all['doctor_name']; ?></td>
                        <td><?php echo $row_all['doctor_department']; ?></td>
                        <td><?php echo $row_all['appointment_date']; ?></td>
                        <td><?php echo $row_all['appointment_time']; ?></td>
                        <td><?php echo $row_all['status']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align:center; color:#666;">No appointments found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
