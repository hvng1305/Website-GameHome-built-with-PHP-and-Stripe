<?php
// Kết nối MySQL
include("../db_connect.php"); // File kết nối database

// Xử lý thêm, sửa, xóa tài khoản
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add"])) {
        // Thêm tài khoản
        $ten_dang_nhap = $_POST["ten_dang_nhap"];
        $mat_khau = password_hash($_POST["mat_khau"], PASSWORD_DEFAULT); // Mã hóa mật khẩu
        $email = $_POST["email"];
        $so_dien_thoai = $_POST["so_dien_thoai"];
        $dia_chi = $_POST["dia_chi"];
        $loai_tai_khoan = $_POST["loai_tai_khoan"];

        $query = "INSERT INTO taikhoan (ten_dang_nhap, mat_khau, email, so_dien_thoai, dia_chi, loai_tai_khoan) 
                  VALUES ('$ten_dang_nhap', '$mat_khau', '$email', '$so_dien_thoai', '$dia_chi', '$loai_tai_khoan')";
        $conn->query($query);
    } elseif (isset($_POST["update"])) {
        // Sửa tài khoản
        $id = $_POST["id"];
        $ten_dang_nhap = $_POST["ten_dang_nhap"];
        $email = $_POST["email"];
        $so_dien_thoai = $_POST["so_dien_thoai"];
        $dia_chi = $_POST["dia_chi"];
        $loai_tai_khoan = $_POST["loai_tai_khoan"];

        // Nếu có mật khẩu mới thì cập nhật, nếu không thì giữ nguyên
        if (!empty($_POST["mat_khau"])) {
            $mat_khau = password_hash($_POST["mat_khau"], PASSWORD_DEFAULT);
            $query = "UPDATE taikhoan SET ten_dang_nhap='$ten_dang_nhap', mat_khau='$mat_khau', email='$email', so_dien_thoai='$so_dien_thoai', dia_chi='$dia_chi', loai_tai_khoan='$loai_tai_khoan' WHERE id='$id'";
        } else {
            $query = "UPDATE taikhoan SET ten_dang_nhap='$ten_dang_nhap', email='$email', so_dien_thoai='$so_dien_thoai', dia_chi='$dia_chi', loai_tai_khoan='$loai_tai_khoan' WHERE id='$id'";
        }
        $conn->query($query);
    } elseif (isset($_POST["delete"])) {
        // Xóa tài khoản
        $id = $_POST["id"];
        $query = "DELETE FROM taikhoan WHERE id='$id'";
        $conn->query($query);
    }
}

// Chuyển hướng về trang quản lý tài khoản
header("Location: quanly_taikhoan.php");
exit();
?>
