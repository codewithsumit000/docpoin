<?php

$conn = mysqli_connect("localhost", "root", "", "doctor_appointment_db");
if (!$conn) die("Connection failed: " . mysqli_connect_error());


$name = $email = $phone = $password = "";
$nameError = $emailError = $phoneError = $passwordError = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    
    if (!preg_match("/^[A-Za-z\s]+$/", $name)) {
        $nameError = "Name can only contain letters and spaces";
    }

    if (!preg_match("/^[a-zA-Z0-9._%+-]+@gmail\.com$/", $email)) {
        $emailError = "Enter a valid Gmail address";
    }

    if (!preg_match("/^(98|97)[0-9]{8}$/", $phone)) {
        $phoneError = "Phone must start with 98 or 97 and be 10 digits";
    }

    if (strlen($password) < 6 || !preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $password)) {
        $passwordError = "Password must be at least 6 characters and include a special character";
    }

    
    if (!$nameError && !$emailError && !$phoneError && !$passwordError) {
        $sql = "INSERT INTO patients (name, email, phone, password) VALUES ('$name', '$email', '$phone', '$password')";
        if (mysqli_query($conn, $sql)) {
            header("Location: ./login.php");
            exit();
        } else {
            echo "Database error: " . mysqli_error($conn);
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration</title>
    <style>
        body {
            font-family: Arial;
            background: #f0f8ff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-box {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .form-box h2 {
            text-align: center;
            color: #0077b6;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 10px;
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 13px;
            margin-bottom: 3px;
            color: #333;
        }

        .form-group input {
            padding: 7px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 13px;
        }

        .form-group input:focus {
            border-color: #0077b6;
            outline: none;
        }

        .error {
            color: red;
            font-size: 12px;
            margin-top: 3px;
        }

        .btn {
            width: 100%;
            padding: 8px;
            background: #0077b6;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 5px;
        }

        .btn:hover {
            background: #023e8a;
        }

        .note {
            text-align: center;
            margin-top: 8px;
            font-size: 12px;
            color: #555;
        }

        .note a {
            color: #0077b6;
            text-decoration: none;
        }

        .note a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="form-box">
        <h2>Sign up</h2>
        <form action="" method="post">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($name) ?>">
                <div class="error"><?= $nameError ?></div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($email) ?>">
                <div class="error"><?= $emailError ?></div>
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($phone) ?>">
                <div class="error"><?= $phoneError ?></div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password">
                <div class="error"><?= $passwordError ?></div>
            </div>

            <button type="submit" class="btn">Register</button>
            <div class="note">
                Already have an account? <a href="login.php">Login</a>
            </div>
        </form>
    </div>
</body>

</html>