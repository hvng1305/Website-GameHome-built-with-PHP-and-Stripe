<?php
session_start();
include("../db_connect.php");

if (!isset($_SESSION["id_khachhang"])) {
    die("Vui lòng đăng nhập để tải file!");
}

if (!isset($_GET['download_id']) || !isset($_GET['id_donhang'])) {
    die("Thiếu thông tin đơn hàng hoặc sản phẩm!");
}

$download_id = $conn->real_escape_string($_GET['download_id']);
$id_donhang = $conn->real_escape_string($_GET['id_donhang']);
$id_khachhang = $_SESSION["id_khachhang"];

// Lấy thông tin file game
$stmt = $conn->prepare("SELECT ten_game, file_game FROM sanpham WHERE id = ?");
$stmt->bind_param("i", $download_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $file_data = $row['file_game'];
    $file_name = $row['ten_game'] . ".zip";

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
$conn->close();
die("Không tìm thấy sản phẩm.");
?>