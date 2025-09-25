<?php
session_start();
include("../db_connect.php"); // File kết nối database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten_dang_nhap = $_POST['ten_dang_nhap'];
    $mat_khau = $_POST['mat_khau'];

    $sql = "SELECT * FROM taikhoan WHERE ten_dang_nhap = ? AND loai_tai_khoan = 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ten_dang_nhap);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($mat_khau, $user['mat_khau'])) {
        $_SESSION['admin'] = $user['ten_dang_nhap'];
        header("Location: quanly_sanpham.php");
        exit();
    } else {
        $error = "Sai tài khoản hoặc mật khẩu!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 320px;
        }
        h2 {
            color: #333;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Đăng Nhập Admin</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="ten_dang_nhap" placeholder="Tên đăng nhập" required>
        <input type="password" name="mat_khau" placeholder="Mật khẩu" required>
        <button type="submit">Đăng Nhập</button>
    </form>
</div>

</body>
</html>
