<?php
session_start();
include("../db_connect.php"); // File kết nối database

include("../header.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User</title>
    <link rel="stylesheet" href="Usercss.css">
</head>
<body>
   

    <div class="user-container">
        <div class="user-box">
            <h2>Login</h2>
            <!-- xác định tệp sẽ hành động sử lý dữ liệu và gửi dữ liệu Post khác Get vì post dùng cho dữ liệu nhạy cảm-->
            <form action="login.php" method="POST"> 
                <label for="login-username">Username:</label>
                <input type="text" id="login-username" name="ten_dang_nhap" required>

                <label for="login-password">Password:</label>
                <input type="password" id="login-password" name="mat_khau" required>

                <button type="submit">Login</button>
            </form>
            <p>Don't have an account? <a href="Register.html">Register here</a></p>
        </div>
    </div>
</body>
</html>
