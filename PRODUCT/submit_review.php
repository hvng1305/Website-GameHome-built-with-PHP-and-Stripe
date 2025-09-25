<?php
session_start();
include("../db_connect.php");

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["id_khachhang"])) {
    $id_khachhang = $_SESSION["id_khachhang"];
    $id_sanpham = $_POST["id_sanpham"];
    $so_sao = $_POST["so_sao"];
    $binh_luan = $_POST["binh_luan"];

    $sql = "INSERT INTO danhgia (id_khachhang, id_sanpham, so_sao, binh_luan)
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $id_khachhang, $id_sanpham, $so_sao, $binh_luan);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Đánh giá đã được gửi thành công!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi khi gửi đánh giá!'
        ]);
    }
    
    $stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Vui lòng đăng nhập để gửi đánh giá!'
    ]);
}
$conn->close();
?>