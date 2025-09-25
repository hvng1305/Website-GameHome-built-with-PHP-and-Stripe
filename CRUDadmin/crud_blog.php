<?php
// Kết nối MySQL
include("../db_connect.php"); // File kết nối database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add"])) {
        // Lấy dữ liệu từ form và xử lý an toàn
        $tieu_de = mysqli_real_escape_string($conn, $_POST["tieu_de"]);
        $noi_dung = mysqli_real_escape_string($conn, $_POST["noi_dung"]);
        $id_tacgia = (int)$_POST["id_tacgia"]; // Chuyển thành số nguyên để tránh lỗi
        $hinh_anh = mysqli_real_escape_string($conn, $_POST["hinh_anh"]);

        // Thêm bài viết
        $query = "INSERT INTO baiviet (tieu_de, noi_dung, id_tacgia, hinh_anh) 
                  VALUES ('$tieu_de', '$noi_dung', '$id_tacgia', '$hinh_anh')";
        $conn->query($query);
    } elseif (isset($_POST["update"])) {
        // Sửa bài viết
        $id = (int)$_POST["id"];
        $tieu_de = mysqli_real_escape_string($conn, $_POST["tieu_de"]);
        $noi_dung = mysqli_real_escape_string($conn, $_POST["noi_dung"]);
        $id_tacgia = (int)$_POST["id_tacgia"];
        $hinh_anh = mysqli_real_escape_string($conn, $_POST["hinh_anh"]);

        $query = "UPDATE baiviet SET 
                  tieu_de='$tieu_de', noi_dung='$noi_dung', id_tacgia='$id_tacgia', hinh_anh='$hinh_anh' 
                  WHERE id='$id'";
        $conn->query($query);
    } elseif (isset($_POST["delete"])) {
        // Xóa bài viết
        $id = (int)$_POST["id"];
        $query = "DELETE FROM baiviet WHERE id='$id'";
        $conn->query($query);
    }
}

// Chuyển hướng về trang quản lý blog sau khi thực hiện thao tác
header("Location: quanly_blog.php");
exit();
?>
