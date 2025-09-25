<?php
session_start();
include("../db_connect.php");

header('Content-Type: application/json'); // Đặt header để trả về JSON

if (!isset($_SESSION["id_khachhang"])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để thêm vào giỏ hàng!']);
    exit();
}

if (!isset($_POST["id_sanpham"])) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm!']);
    exit();
}

$id_khachhang = $_SESSION["id_khachhang"];
$id_sanpham = $_POST["id_sanpham"];

// Kiểm tra xem sản phẩm đã tồn tại trong giỏ hàng chưa
$sql_check = "SELECT id FROM giohang WHERE id_khachhang = ? AND id_sanpham = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("ii", $id_khachhang, $id_sanpham);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Nếu sản phẩm chưa có, thêm mới vào giỏ hàng
    $sql_insert = "INSERT INTO giohang (id_khachhang, id_sanpham) VALUES (?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("ii", $id_khachhang, $id_sanpham);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Thêm vào giỏ hàng thành công!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi thêm vào giỏ hàng!']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm đã có trong giỏ hàng!']);
}

$stmt->close();
$conn->close();
exit();
?>