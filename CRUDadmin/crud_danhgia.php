<?php
// Kết nối MySQL
include("../db_connect.php"); // File kết nối database

// Xử lý xóa đánh giá
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
    $id = $_POST["id"];
    $query = "DELETE FROM danhgia WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Chuyển hướng về trang quản lý đánh giá
header("Location: quanly_danhgia.php");
exit();
?>
