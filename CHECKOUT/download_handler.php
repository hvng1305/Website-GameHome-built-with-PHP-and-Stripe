<?php
session_start();
include("../db_connect.php");

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'new_count' => 0];

if (!isset($_SESSION["id_khachhang"])) {
    $response['message'] = 'Vui lòng đăng nhập để tải file!';
    echo json_encode($response);
    exit();
}

if (!isset($_POST['download_id']) || !isset($_POST['id_donhang'])) {
    $response['message'] = 'Thiếu thông tin đơn hàng hoặc sản phẩm!';
    echo json_encode($response);
    exit();
}

$download_id = $conn->real_escape_string($_POST['download_id']);
$id_donhang = $conn->real_escape_string($_POST['id_donhang']);
$id_khachhang = $_SESSION["id_khachhang"];

// Kiểm tra số lần tải hiện tại
$query_check = "SELECT so_lan_tai FROM chitietdonhang WHERE id_donhang = ? AND id_sanpham = ?";
$stmt_check = $conn->prepare($query_check);
$stmt_check->bind_param("ii", $id_donhang, $download_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$row_check = $result_check->fetch_assoc();

if ($row_check && $row_check['so_lan_tai'] >= 5) {
    $response['message'] = 'Bạn đã vượt quá giới hạn 5 lần tải cho sản phẩm này!';
    $response['new_count'] = $row_check['so_lan_tai'];
} else {
    // Cập nhật số lần tải
    $update_query = "UPDATE chitietdonhang SET so_lan_tai = so_lan_tai + 1 WHERE id_donhang = ? AND id_sanpham = ?";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->bind_param("ii", $id_donhang, $download_id);
    $stmt_update->execute();
    $stmt_update->close();

    // Lấy số lần tải mới
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row_check = $result_check->fetch_assoc();
    $response['success'] = true;
    $response['new_count'] = $row_check['so_lan_tai'];
}

$stmt_check->close();
$conn->close();
echo json_encode($response);
?>