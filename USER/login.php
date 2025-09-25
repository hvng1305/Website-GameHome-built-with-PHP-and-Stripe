<?php
session_start();


// Kết nối cơ sở dữ liệu
include("../db_connect.php"); //nhúng nội dung của tệp khác vào 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten_dang_nhap = $_POST["ten_dang_nhap"];
    $mat_khau = $_POST["mat_khau"];

    // Chuẩn bị truy vấn SQL
    $sql = "SELECT * FROM taikhoan WHERE ten_dang_nhap = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ten_dang_nhap);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Kiểm tra mật khẩu
        if (password_verify($mat_khau, $row["mat_khau"])) {
            // Lưu thông tin vào session
            $_SESSION["id_khachhang"] = $row["id"];
            $_SESSION["ten_dang_nhap"] = $row["ten_dang_nhap"];
            $_SESSION["loai_tai_khoan"] = $row["loai_tai_khoan"];

            // Đóng statement và kết nối
            $stmt->close();
            $conn->close();

            // Điều hướng đến trang chủ sau khi đăng nhập thành công
            header("Location: /BTLPHP/HOME/Home.php");
            exit();
        } else {
            $_SESSION["error"] = "Sai mật khẩu!";

            // Đóng statement và kết nối
            $stmt->close();
            $conn->close();

            // Điều hướng trở lại trang đăng nhập
            header("Location: /BTLPHP/USER/User.php");
            exit();
        }
    } else {
        $_SESSION["error"] = "Tài khoản không tồn tại!";

        // Đóng statement và kết nối
        $stmt->close();
        $conn->close();

        // Điều hướng trở lại trang đăng nhập
        header("Location: /BTLPHP/USER/User.php");
        exit();
    }
}

// Đóng kết nối phòng trường hợp không có POST request
$conn->close();
?>
