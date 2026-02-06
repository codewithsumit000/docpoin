<?php
session_start();
$admin_error = $_SESSION['admin_error'] ?? '';
unset($_SESSION['admin_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>

    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #0077b6, #00b4d8);
            font-family: Arial, sans-serif;
        }

        .main_con {
            background: #ffffff;
            padding: 30px;
            width: 320px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .main_con h2 {
            margin-bottom: 20px;
            color: #0077b6;
        }

        .main_con input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        .main_con input:focus {
            border-color: #00b4d8;
        }

        .main_con button {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            border: none;
            border-radius: 5px;
            background: #0077b6;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        .main_con button:hover {
            background: #005f8d;
        }

        .error-box {
            background: #ece4e4;
            color: #c62323;
            padding: 8px;
            margin: 8px 0;
            border-radius: 6px;
            font-size: 13px;
            border: 1px solid #f5f0f0;
        }
    </style>
</head>
<body>

<form action="./database/admin_login.php" method="post">
    <div class="main_con">
        <h2>Admin Login</h2>

        <?php if (!empty($admin_error)) { ?>
            <div class="error-box">
                <?= $admin_error ?>
            </div>
        <?php } ?>

        <input type="email" name="email" placeholder="Admin email" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>
    </div>
</form>

</body>
</html>
