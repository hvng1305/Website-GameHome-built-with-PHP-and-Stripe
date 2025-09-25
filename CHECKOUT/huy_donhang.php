<?php
session_start();
include("../db_connect.php");

if (!isset($_SESSION["id_khachhang"])) {
    header("Location: /BTLPHP/USER/User.html");
    exit();
}

$id_khachhang = $_SESSION["id_khachhang"];

// Kiểm tra xem có đơn hàng "Chờ thanh toán" không
$sql_check_order = "SELECT id FROM donhang WHERE id_khachhang = ? AND trang_thai = 'Chờ thanh toán'";
$stmt_check = $conn->prepare($sql_check_order);
$stmt_check->bind_param("i", $id_khachhang);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$donhang = $result_check->fetch_assoc();
$stmt_check->close();

if (!$donhang) {
    die("Không tìm thấy đơn hàng cần hủy!");
}

$id_donhang = $donhang["id"];

// Cập nhật trạng thái đơn hàng thành "Đã hủy"
$sql_cancel_order = "UPDATE donhang SET trang_thai = 'Đã hủy' WHERE id = ?";
$stmt_cancel = $conn->prepare($sql_cancel_order);
$stmt_cancel->bind_param("i", $id_donhang);
if ($stmt_cancel->execute()) {
    echo "Đơn hàng đã được hủy thành công!";
} else {
    echo "Lỗi cập nhật: " . $stmt_cancel->error;
}
$stmt_cancel->close();

// Chuyển hướng về giỏ hàng
header("Location: /BTLPHP/CART/Cart.php?message=order_cancelled");
exit();

?>
