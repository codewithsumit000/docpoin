<?php
session_start();

// Get errors from session if any
$doctor_error  = $_SESSION['doctor_error'] ?? "";
$patient_error = $_SESSION['patient_error'] ?? "";
$activeForm    = $_SESSION['active_form'] ?? "";

// Clear them from session so they disappear on refresh
unset($_SESSION['doctor_error']);
unset($_SESSION['patient_error']);
unset($_SESSION['active_form']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>

<style>
* {
  box-sizing: border-box;
}

body {
  margin: 0;
  font-family: Arial, sans-serif;
  background: #e0f4ff;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.container {
  width: 400px;
  background: #fff;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.switch-btns {
  display: flex;
  justify-content: space-between;
  margin-bottom: 15px;
}

.switch-btns button {
  width: 48%;
  padding: 10px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  background: #cce7ff;
  font-size: 14px;
}

.switch-btns .active {
  background: #0077b6;
  color: white;
}

.form-box {
  display: none;
}

.form-box.active {
  display: block;
}

h3 {
  text-align: center;
  margin-bottom: 10px;
}

input {
  width: 100%;
  padding: 9px;
  margin: 8px 0;
  border-radius: 6px;
  border: 1px solid #ccc;
}

button[type="submit"] {
  width: 100%;
  padding: 10px;
  border: none;
  background: #0077b6;
  color: white;
  border-radius: 6px;
  cursor: pointer;
  margin-top: 10px;
}

button[type="submit"]:hover {
  background: #005f8e;
}

.error-box {
  background: #ffe5e5;
  color: #c62323;
  padding: 8px;
  margin-bottom: 10px;
  border-radius: 6px;
  font-size: 13px;
}
</style>
</head>

<body>

<div class="container">

  <div class="switch-btns">
    <button id="docBtn" class="active">Doctor</button>
    <button id="patBtn">Patient</button>
  </div>

  <!-- Doctor Login -->
  <form action="./database/doctor_login.php" method="POST" id="doctorForm" class="form-box active">
    <h3>Doctor Login</h3>

    <?php if ($doctor_error) { ?>
      <div class="error-box"><?= $doctor_error ?></div>
    <?php } ?>

    <input type="email" name="email" required placeholder="Doctor Email">
    <input type="password" name="password" required placeholder="Password">
    <button type="submit">Login</button>
  </form>

  <!-- Patient Login -->
  <form action="./database/patient_login.php" method="POST" id="patientForm" class="form-box">
    <h3>Patient Login</h3>

    <?php if ($patient_error) { ?>
      <div class="error-box"><?= $patient_error ?></div>
    <?php } ?>

    <input type="email" name="email" required placeholder="Patient Email">
    <input type="password" name="password" required placeholder="Password">
    <button type="submit">Login</button>
  </form>

</div>

<script>
const docBtn = document.getElementById("docBtn");
const patBtn = document.getElementById("patBtn");
const doctorForm = document.getElementById("doctorForm");
const patientForm = document.getElementById("patientForm");

docBtn.onclick = () => {
  docBtn.classList.add("active");
  patBtn.classList.remove("active");
  doctorForm.classList.add("active");
  patientForm.classList.remove("active");
};

patBtn.onclick = () => {
  patBtn.classList.add("active");
  docBtn.classList.remove("active");
  patientForm.classList.add("active");
  doctorForm.classList.remove("active");
};

let activeForm = "<?= $activeForm ?>";
if (activeForm === "patient") patBtn.click();
</script>

</body>
</html>
