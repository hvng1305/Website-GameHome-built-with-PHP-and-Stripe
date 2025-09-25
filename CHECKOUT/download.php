<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin'])) {
    header("Location: Adminlogin.php");
    exit();
}

include("../db_connect.php");

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $stmt = $conn->prepare("SELECT ten_game, file_game FROM sanpham WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $file_data = $row['file_game'];
        $file_name = $row['ten_game'] . ".zip"; // Đặt tên file dựa trên tên game, có thể tùy chỉnh

        if (!empty($file_data)) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
            header('Content-Length: ' . strlen($file_data));
            echo $file_data;
            exit;
        } else {
            die("Không có dữ liệu file game.");
        }
    }
    $stmt->close();
}
die("Không tìm thấy sản phẩm.");
?>