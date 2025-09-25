<?php
// Hiển thị lỗi (nếu có)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kết nối cơ sở dữ liệu
include("../db_connect.php"); // File kết nối database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten_dang_nhap = trim($_POST["ten_dang_nhap"]);
    $mat_khau = password_hash(trim($_POST["mat_khau"]), PASSWORD_DEFAULT);
    $email = trim($_POST["email"]);
    $so_dien_thoai = trim($_POST["so_dien_thoai"]);
    $dia_chi = trim($_POST["dia_chi"]);

    // Kiểm tra trùng lặp username hoặc email
    $stmt_check = $conn->prepare("SELECT id FROM taikhoan WHERE ten_dang_nhap = ? OR email = ?"); //chuẩn bị câu lệnh tránh lỗi 
    $stmt_check->bind_param("ss", $ten_dang_nhap, $email); //gán giá trị cho tham số ? trong sql
    $stmt_check->execute();
    $stmt_check->store_result(); 

    if ($stmt_check->num_rows > 0) {
        echo "Tên đăng nhập hoặc email đã tồn tại. Vui lòng thử lại!";
    } else {
        // Thêm tài khoản mới vào database
        $stmt = $conn->prepare("INSERT INTO taikhoan (ten_dang_nhap, mat_khau, email, so_dien_thoai, dia_chi)
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $ten_dang_nhap, $mat_khau, $email, $so_dien_thoai, $dia_chi);

        if ($stmt->execute()) {
            // Điều hướng về trang User.html
            header("Location: User.php");
            exit;
        } else {
            echo "Lỗi khi đăng ký: " . $stmt->error;
        }

        $stmt->close();
    }

    $stmt_check->close();
}

$conn->close();
?>
