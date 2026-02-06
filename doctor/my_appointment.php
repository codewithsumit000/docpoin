<?php
session_start();
include ('./connect.php');

if(!isset($_SESSION['doctor_id'])){
    header("Location: ../login.html");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

// Get appointments
$appointments_query = "
    SELECT a.*, p.name AS patient_name, p.phone, p.email
    FROM appointments a
    JOIN patients p ON a.patient_id = p.patient_id
    WHERE a.doctor_id = '$doctor_id'
    ORDER BY a.appointment_date DESC
";
$appointments_result = mysqli_query($conn, $appointments_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
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

        .container {
            padding: 30px;
            width: calc(100% - 260px);
            margin-left: 250px;
        }

        .container h1 {
            font-size: 28px;
            color: #0077b6;
            margin-bottom: 25px;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-top: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        table th {
            background: linear-gradient(135deg, #0077b6, #00b4d8);
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 15px;
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid #e5e5e5;
            font-size: 14px;
        }

        .status {
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: 500;
            color: white;
            display: inline-block;
            min-width: 80px;
            text-align: center;
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

        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            transition: 0.3s;
            border: none;
            cursor: pointer;
            margin-right: 5px;
        }

        .approve-btn {
            background: #38b000;
            color: white;
        }

        .cancel-btn {
            background: #d00000;
            color: white;
        }

        .approve-btn:hover {
            background: #2d8c00;
        }

        .cancel-btn:hover {
            background: #9b0000;
        }

        .empty {
            padding: 10px;
            color: gray;
            font-style: italic;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">🩺 DocPoint</div>
        <div class="menu">
            <a href="./index.php">Dashboard</a>
            <a href="./my_appointment.php" class="active">Appointments</a>
            <a href="./schedule.php">Schedule</a>
            <a href="../doctor/setting.php">Settings</a>
            <a href="./logout.php" class="logout">Logout</a>
        </div>
    </div>

    <div class="container">
        <h1>My Appointments</h1>
        
        <div class="table-container">
            <?php if(mysqli_num_rows($appointments_result) > 0): ?>
            <table>
                <tr>
                    <th>Patient Name</th>
                    <th>Contact</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php while($row = mysqli_fetch_assoc($appointments_result)): ?>
                <tr>
                    <td><?php echo $row['patient_name']; ?></td>
                    <td><?php echo $row['phone']; ?><br><?php echo $row['email']; ?></td>
                    <td><?php echo $row['appointment_date']; ?></td>
                    <td><?php echo $row['appointment_time']; ?></td>
                    <td>
                        <span class="status <?php echo $row['status']; ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if($row['status'] == 'pending'): ?>
                            <a href="update_status.php?id=<?php echo $row['appointment_id']; ?>&status=approved" 
                               class="action-btn approve-btn">Approve</a>
                            <a href="update_status.php?id=<?php echo $row['appointment_id']; ?>&status=cancelled" 
                               class="action-btn cancel-btn">Cancel</a>
                        <?php elseif($row['status'] == 'approved'): ?>
                            <a href="update_status.php?id=<?php echo $row['appointment_id']; ?>&status=cancelled" 
                               class="action-btn cancel-btn">Cancel</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php else: ?>
                <p class="empty">No appointments found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>