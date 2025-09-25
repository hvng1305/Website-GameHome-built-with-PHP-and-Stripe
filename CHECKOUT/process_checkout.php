<?php
session_start();
include("../db_connect.php");

if (!isset($_SESSION["id_khachhang"])) {
    header("Location: /BTLPHP/USER/User.html");
    exit();
}

$id_khachhang = $_SESSION["id_khachhang"];

// Kiểm tra xem khách hàng đã có đơn hàng "Chờ thanh toán" hay chưa
$sql_check_order = "SELECT id FROM donhang WHERE id_khachhang = ? AND trang_thai = 'Chờ thanh toán'";
$stmt_check = $conn->prepare($sql_check_order);
$stmt_check->bind_param("i", $id_khachhang);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$donhang = $result_check->fetch_assoc();
$stmt_check->close();

// Lấy danh sách sản phẩm hiện có trong giỏ hàng
$sql_cart = "SELECT g.id_sanpham, s.gia 
             FROM giohang g 
             JOIN sanpham s ON g.id_sanpham = s.id 
             WHERE g.id_khachhang = ?";
$stmt_cart = $conn->prepare($sql_cart);
$stmt_cart->bind_param("i", $id_khachhang);
$stmt_cart->execute();
$result_cart = $stmt_cart->get_result();

if ($result_cart->num_rows == 0) {
    header("Location: /BTLPHP/CART/Cart.php?error=empty_cart");
    exit();
}

// Nếu khách hàng đã có đơn hàng "Chờ thanh toán"
if ($donhang) {
    $id_donhang = $donhang["id"];

    // Kiểm tra sản phẩm nào chưa có trong đơn hàng
    while ($row = $result_cart->fetch_assoc()) {
        $id_sanpham = $row["id_sanpham"];
        $gia = $row["gia"];

        $sql_check_product = "SELECT id FROM chitietdonhang WHERE id_donhang = ? AND id_sanpham = ?";
        $stmt_check_product = $conn->prepare($sql_check_product);
        $stmt_check_product->bind_param("ii", $id_donhang, $id_sanpham);
        $stmt_check_product->execute();
        $result_check_product = $stmt_check_product->get_result();

        // Nếu sản phẩm chưa có trong đơn hàng thì thêm vào
        if ($result_check_product->num_rows == 0) {
            $sql_insert_product = "INSERT INTO chitietdonhang (id_donhang, id_sanpham, gia) VALUES (?, ?, ?)";
            $stmt_insert_product = $conn->prepare($sql_insert_product);
            $stmt_insert_product->bind_param("iid", $id_donhang, $id_sanpham, $gia);
            $stmt_insert_product->execute();
            $stmt_insert_product->close();
        }

        $stmt_check_product->close();
    }
} else {
    // Nếu chưa có đơn hàng "Chờ thanh toán", tạo đơn hàng mới
    $tong_tien = 0;
    $result_cart->data_seek(0); // Reset kết quả để lặp lại

    while ($row = $result_cart->fetch_assoc()) {
        $tong_tien += $row["gia"];
    }

    $sql_insert_order = "INSERT INTO donhang (id_khachhang, tong_tien, trang_thai) VALUES (?, ?, 'Chờ thanh toán')";
    $stmt_insert_order = $conn->prepare($sql_insert_order);
    $stmt_insert_order->bind_param("id", $id_khachhang, $tong_tien);
    $stmt_insert_order->execute();
    $id_donhang = $stmt_insert_order->insert_id; // Lấy ID của đơn hàng vừa tạo
    $stmt_insert_order->close();

    // Thêm tất cả sản phẩm trong giỏ hàng vào đơn hàng mới
    $result_cart->data_seek(0);
    while ($row = $result_cart->fetch_assoc()) {
        $id_sanpham = $row["id_sanpham"];
        $gia = $row["gia"];

        $sql_insert_product = "INSERT INTO chitietdonhang (id_donhang, id_sanpham, gia) VALUES (?, ?, ?)";
        $stmt_insert_product = $conn->prepare($sql_insert_product);
        $stmt_insert_product->bind_param("iid", $id_donhang, $id_sanpham, $gia);
        $stmt_insert_product->execute();
        $stmt_insert_product->close();
    }
}

// Chuyển hướng sang trang Checkout
header("Location: /BTLPHP/CHECKOUT/Checkout.php");
exit();
?>
