<?php
session_start();
include('connect.php');

if (!isset($_SESSION['patient_id'])) {
  header("Location: ../login.php");
  exit();
}

$patient_id = $_SESSION['patient_id'];
$error = "";
$success = "";
$show_modal = false;
$doctor_id = "";

// Fetch doctors with schedules
$sql = "SELECT d.doctor_id, d.name, d.department, d.qualification, d.photo, 
               GROUP_CONCAT(ds.day SEPARATOR ', ') AS days,
               GROUP_CONCAT(ds.max_patients SEPARATOR ', ') AS max_patients
        FROM doctors d
        LEFT JOIN doctor_schedule ds ON d.doctor_id = ds.doctor_id
        GROUP BY d.doctor_id";
$result = mysqli_query($conn, $sql);


$form_date = "";
$form_time = "";

if (isset($_POST['book'])) {
  $doctor_id = $_POST['doctor_id'];
  $date = $_POST['appointment_date'];
  $time = $_POST['appointment_time'];
  $form_date = $date;
  $form_time = $time;
  $show_modal = true;

  $appointment_datetime = $date . ' ' . $time;
  $current_datetime = date('Y-m-d H:i');

  $today = date('Y-m-d');
  $max_date = date('Y-m-d', strtotime('+7 days'));

  // Validation
  if ($date < $today || $date > $max_date) {
    $error = "You can only book appointments within the next 7 days.";
  } elseif (strtotime($appointment_datetime) <= strtotime($current_datetime)) {
    $error = "You cannot book an appointment in the past.";
  } else {
    $dayName = date('l', strtotime($date));
    $schedule_sql = "SELECT * FROM doctor_schedule WHERE doctor_id='$doctor_id' AND day='$dayName'";
    $schedule_result = mysqli_query($conn, $schedule_sql);

    if (mysqli_num_rows($schedule_result) == 0) {
      $error = "Doctor is not available on $dayName.";
    } else {
      $schedule_row = mysqli_fetch_assoc($schedule_result);

      // Check max patients
      $count_sql = "SELECT COUNT(*) AS total FROM appointments 
                          WHERE doctor_id='$doctor_id' AND appointment_date='$date'";
      $count_result = mysqli_query($conn, $count_sql);
      $count_row = mysqli_fetch_assoc($count_result);

      if ($count_row['total'] >= $schedule_row['max_patients']) {
        $error = "Doctor has reached the maximum patients for $dayName.";
      } else {

        // Check if patient already has an appointment at the same date and time
        $patient_check_sql = "SELECT * FROM appointments 
                              WHERE patient_id='$patient_id' 
                                AND appointment_date='$date' 
                                AND appointment_time='$time'";
        $patient_check_result = mysqli_query($conn, $patient_check_sql);

        if (mysqli_num_rows($patient_check_result) > 0) {
            $error = "You already have an appointment at this time. Please choose another time.";
        } else {

            // Check duplicate time for the doctor
            $check_sql = "SELECT * FROM appointments 
                                  WHERE doctor_id='$doctor_id' AND appointment_date='$date' AND appointment_time='$time'";
            $check_result = mysqli_query($conn, $check_sql);

            if (mysqli_num_rows($check_result) > 0) {
              $error = "This time slot is already booked for this doctor. Please choose another time.";
            } else {
              // Insert appointment
              $insert_sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time)
                                       VALUES ('$patient_id','$doctor_id','$date','$time')";
              if (mysqli_query($conn, $insert_sql)) {
                $success = "Appointment booked successfully!";
                $form_date = $form_time = "";
              } else {
                $error = "Error booking appointment: " . mysqli_error($conn);
              }
            }

        }

      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Appointment</title>
  <link rel="stylesheet" href="sidebar.css">
  <style>
    /* your existing CSS */
    * {margin: 0; padding: 0; box-sizing: border-box; font-family: "Poppins", sans-serif;}
    body {display: flex; background: #f5f8ff;}
    .doctor {padding: 30px; width: calc(100% - 260px); min-height: 100vh; background: white; margin-left: 250px;}
    .doctor h2 {color: #0077b6; font-size: 26px; margin-bottom: 25px;}
    .doctor-card {width: 250px; background: linear-gradient(135deg, #0077b6, #00b4d8); border-radius: 12px; padding: 18px; display: inline-block; margin: 10px; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.15);}
    .doctor-card img {width: 110px; height: 110px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; border: 4px solid #dfebedff;}
    .doctor-card h3 {color: #f9fafbff; font-size: 16px; margin-bottom: 5px;}
    .doctor-card p {font-size: 14px; color: #f7f1f1ff; margin: 3px 0;}
    .doctor-card button {margin-top: 10px; background: #0077b6; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-size: 13px; transition: opacity 0.3s;}
    .doctor-card button:hover {opacity: 0.9;}
    .modal {display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 999;}
    .modal-content {background: white; width: 350px; padding: 20px; border-radius: 12px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);}
    .modal-content h3 {color: #0077b6; margin-bottom: 15px;}
    .modal-content input {width: 100%; padding: 8px; margin: 8px 0;}
    .modal-content button {background: linear-gradient(135deg, #0077b6, #00b4d8); color: white; border: none; padding: 8px; border-radius: 6px; width: 100%; cursor: pointer;}
    .close {float: right; font-size: 20px; cursor: pointer;}
    .message {font-weight: bold; margin-bottom: 10px;}
    .message.error {color: red;}
    .message.success {color: green;}
  </style>
</head>

<body>

  <div class="sidebar">
    <div class="logo">🩺 DocPoint</div>
    <div class="menu">
      <a href="../patient/index.php" class="active">Dashboard</a>
      <a href="../patient/my_appointment.php">My Appointments</a>
      <a href="../patient/book_appointment.php">Book Appointment</a>
      <a href="../patient/setting.php">Settings</a>
      <a href="../patient/logout.php" class="logout">Logout</a>
    </div>
  </div>

  <div class="doctor">
    <h2>Available Doctors</h2>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <div class="doctor-card">
        <img src="../doctor_photo/<?php echo $row['photo']; ?>" alt="Doctor Photo">
        <h3><?php echo $row['name']; ?></h3>
        <p><?php echo $row['qualification']; ?></p>
        <p><?php echo $row['department']; ?></p>
        <p><strong>Available Days:</strong> <?php echo $row['days'] ?: 'No schedule'; ?></p>
        <p><strong>Max Patients:</strong> <?php echo $row['max_patients'] ?: '-'; ?></p>

        <button class="book-btn" onclick="openForm(<?php echo $row['doctor_id']; ?>)">Book</button>
      </div>
    <?php } ?>

    <!-- Appointment Modal -->
    <div id="appointmentModal" class="modal">
      <div class="modal-content">
        <span class="close" onclick="closeForm()">&times;</span>
        <h3>Book Appointment</h3>

        <form method="POST">
          <input type="hidden" name="doctor_id" id="doctor_id">

          <?php if (!empty($error)) { ?>
            <p class="message error"><?php echo $error; ?></p>
          <?php } elseif (!empty($success)) { ?>
            <p class="message success"><?php echo $success; ?></p>
          <?php } ?>

          <label>Date</label>
          <input type="date" name="appointment_date" required
            min="<?php echo date('Y-m-d'); ?>"
            max="<?php echo date('Y-m-d', strtotime('+7 days')); ?>"
            value="<?php echo htmlspecialchars($form_date); ?>">

          <label>Time</label>
          <input type="time" name="appointment_time" id="appointment_time" required
            value="<?php echo htmlspecialchars($form_time); ?>">

          <button type="submit" name="book">Confirm Booking</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    function openForm(doctorId) {
      document.getElementById("doctor_id").value = doctorId;
      document.getElementById("appointmentModal").style.display = "block";
    }

    function closeForm() {
      document.getElementById("appointmentModal").style.display = "none";
    }
    <?php if ($show_modal) { ?>
      document.getElementById("doctor_id").value = <?php echo $doctor_id; ?>;
      document.getElementById("appointmentModal").style.display = "block";
    <?php } ?>

    // Block past times for today
    const dateInput = document.querySelector('input[name="appointment_date"]');
    const timeInput = document.querySelector('input[name="appointment_time"]');

    function blockPastTime() {
      const today = new Date().toISOString().split("T")[0];
      const maxDate = new Date();
      maxDate.setDate(maxDate.getDate() + 7);
      const maxDateStr = maxDate.toISOString().split("T")[0];
      dateInput.setAttribute("max", maxDateStr);

      if (dateInput.value === today) {
        const now = new Date();
        now.setMinutes(now.getMinutes() + 10); // buffer
        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        timeInput.min = `${hh}:${mm}`;
      } else {
        timeInput.min = "00:00";
      }
    }

    dateInput.addEventListener("change", blockPastTime);
    blockPastTime(); // initial
  </script>

</body>

</html>
