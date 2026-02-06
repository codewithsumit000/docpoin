<?php
session_start();
include('./connect.php');

if (!isset($_SESSION['doctor_id'])) {
    header("Location: ../login.html");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

/* DELETE schedule if requested */
if (isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];
    
    // First get the day from the schedule to be deleted
    $get_day_sql = "SELECT day FROM doctor_schedule WHERE schedule_id='$delete_id' AND doctor_id='$doctor_id'";
    $day_result = mysqli_query($conn, $get_day_sql);
    
    if(mysqli_num_rows($day_result) > 0) {
        $day_row = mysqli_fetch_assoc($day_result);
        $day_to_delete = $day_row['day'];
        
        // Check if there are any FUTURE appointments for this doctor on this specific day
        $today = date('Y-m-d');
        $check_sql = "SELECT COUNT(*) AS count
                      FROM appointments 
                      WHERE doctor_id='$doctor_id' 
                      AND status != 'cancelled'
                      AND DAYNAME(appointment_date) = '$day_to_delete'
                      AND appointment_date >= '$today'";
        $res = mysqli_query($conn, $check_sql);
        $row_check = mysqli_fetch_assoc($res);

        if ($row_check['count'] == 0) {
            // No future appointments, safe to delete
            $delete_query = "DELETE FROM doctor_schedule WHERE schedule_id='$delete_id' AND doctor_id='$doctor_id'";
            if(mysqli_query($conn, $delete_query)) {
                $message = "Schedule deleted successfully.";
            } else {
                $message = "Error deleting schedule.";
            }
        } else {
            $message = "Cannot cancel: There are booked appointments on " . $day_to_delete . " days.";
        }
    } else {
        $message = "Schedule not found.";
    }
}

/* Add schedule */
if (isset($_POST['add_schedule'])) {
    $day = $_POST['day'];
    $max_patients = $_POST['max_patients'];

    // Prevent duplicate day schedule
    $check_duplicate = "SELECT * FROM doctor_schedule WHERE doctor_id='$doctor_id' AND day='$day'";
    $res_dup = mysqli_query($conn, $check_duplicate);

    if(mysqli_num_rows($res_dup) == 0){
        $insert_query = "INSERT INTO doctor_schedule (doctor_id, day, max_patients)
                         VALUES ('$doctor_id', '$day', '$max_patients')";
        if(mysqli_query($conn, $insert_query)) {
            $message = "Schedule added successfully.";
        } else {
            $message = "Error adding schedule.";
        }
    } else {
        $message = "Schedule for this day already exists.";
    }
}

/* Fetch schedule with booking count for FUTURE appointments */
$schedule_query = "
    SELECT s.*, 
           (SELECT COUNT(*) FROM appointments a 
            WHERE a.doctor_id = s.doctor_id 
              AND a.status != 'cancelled' 
              AND DAYNAME(a.appointment_date) = s.day
              AND a.appointment_date >= CURDATE()) AS future_booked_count
    FROM doctor_schedule s
    WHERE s.doctor_id='$doctor_id'
    ORDER BY FIELD(s.day,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')
";
$schedule_result = mysqli_query($conn, $schedule_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Doctor Schedule</title>
<link rel="stylesheet" href="sidebar.css">
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:"Poppins", sans-serif; }
body { background:#f5f8ff; display:flex; }
.container{padding:30px;margin-left:250px;width:calc(100% - 250px)}
h1{color:#0077b6;margin-bottom:25px}
.form-container,.schedule-container{background:#fff;padding:20px;border-radius:12px;margin-bottom:25px;box-shadow:0 2px 10px rgba(0,0,0,0.05);}
label{color:#0077b6;font-weight:500;margin-top:10px;display:block}
input,select{width:100%;padding:10px;margin-top:5px;border-radius:5px;border:1px solid #ddd}
button{background:linear-gradient(135deg,#0077b6,#00b4d8);color:#fff;border:none;padding:10px 20px;border-radius:6px;cursor:pointer}
button:hover{opacity:0.9}
table{width:100%;border-collapse:collapse;margin-top:15px}
th{background:linear-gradient(135deg,#0077b6,#00b4d8);color:#fff;padding:10px;text-align:left}
td{padding:10px;border-bottom:1px solid #eee}
.delete-btn{background:#d00000;color:white;padding:5px 10px;border:none;border-radius:4px;cursor:pointer}
.delete-btn:disabled{background:#ccc; cursor:not-allowed;}
.message{margin-bottom:15px;font-weight:bold;color:#0077b6;}
.empty{text-align:center;color:#666}
.info-box {
    background: #f0f8ff;
    padding: 15px;
    border-radius: 8px;
    margin: 15px 0;
    border-left: 4px solid #0077b6;
}
.info-box p {
    margin: 5px 0;
    font-size: 14px;
}
.booked-info {
    display: inline-block;
    padding: 3px 8px;
    background: #ffc107;
    color: #333;
    border-radius: 12px;
    font-size: 12px;
    margin-left: 10px;
    font-weight: bold;
}
</style>
</head>
<body>

<div class="sidebar">
    <div class="logo">🩺 DocPoint</div>
    <div class="menu">
        <a href="./index.php">Dashboard</a>
        <a href="./my_appointment.php">Appointments</a>
        <a href="./schedule.php" class="active">Schedule</a>
        <a href="../doctor/setting.php">Settings</a>
        <a href="./logout.php" class="logout">Logout</a>
    </div>
</div>

<div class="container">
<h1>Manage Schedule (Day-wise)</h1>

<?php if(isset($message)) { echo "<p class='message'>{$message}</p>"; } ?>

<div class="info-box">
    <p><strong>Note:</strong> You can only cancel a schedule if there are no booked appointments on that day.</p>
    <p>Appointments already passed today will not prevent schedule cancellation.</p>
</div>

<!-- Add Schedule Form -->
<div class="form-container">
<h2>Add Working Day</h2>
<form method="POST">
  <label>Day</label>
  <select name="day" required>
    <option value="">Select a day</option>
    <option>Monday</option>
    <option>Tuesday</option>
    <option>Wednesday</option>
    <option>Thursday</option>
    <option>Friday</option>
    <option>Saturday</option>
    <option>Sunday</option>
  </select>

  <label>Max Patients</label>
  <input type="number" name="max_patients" min="1" value="5" required>

  <br><br>
  <button name="add_schedule">Add Schedule</button>
</form>
</div>

<!-- Current Schedule Table -->
<div class="schedule-container">
<h2>Current Schedule</h2>

<?php if(mysqli_num_rows($schedule_result) > 0): ?>
<table>
<tr>
  <th>Day</th>
  <th>Max Patients</th>
  <th>Status</th>
  <th>Action</th>
</tr>
<?php 
while($row = mysqli_fetch_assoc($schedule_result)): 
    $has_bookings = $row['future_booked_count'] > 0;
?>
<tr>
  <td><?php echo $row['day']; ?></td>
  <td><?php echo $row['max_patients']; ?></td>
  <td>
    <?php if($has_bookings): ?>
        <span class="booked-info"><?php echo $row['future_booked_count']; ?> upcoming booking(s)</span>
    <?php else: ?>
        <span style="color:#28a745;">✓ Available to cancel</span>
    <?php endif; ?>
  </td>
  <td>
    <!-- Cancel schedule if no booked appointments -->
    <form method="POST" style="display:inline;" 
          onsubmit="return confirm('Are you sure you want to cancel schedule for <?php echo $row['day']; ?>?');">
        <input type="hidden" name="delete_id" value="<?php echo $row['schedule_id']; ?>">
        <button type="submit" class="delete-btn" <?php echo ($has_bookings) ? 'disabled' : ''; ?>>
            Cancel
        </button>
    </form>
  </td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p class="empty">No schedule added yet.</p>
<?php endif; ?>
</div>

</div>
</body>
</html>