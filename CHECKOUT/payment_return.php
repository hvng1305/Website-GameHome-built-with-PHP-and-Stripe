<?php
session_start();
include("../db_connect.php");

if (!isset($_SESSION["id_khachhang"])) {
    header("Location: /BTLPHP/USER/User.html");
    exit();
}

// Kiểm tra tham số từ Stripe
if (!isset($_GET['session_id']) || !isset($_GET['id_donhang'])) {
    echo "<h2>Lỗi: Không tìm thấy thông tin thanh toán!</h2>";
    echo "<a href='/BTLPHP/CHECKOUT/checkout.php'>Quay lại</a>";
    exit();
}

$id_donhang = $_GET['id_donhang'];
$session_id = $_GET['session_id'];
$id_khachhang = $_SESSION['id_khachhang'];

// Tải thư viện Stripe
require_once '../vendor/autoload.php';

$stripe = new \Stripe\StripeClient(getenv("STRIPE_SECRET_KEY"));

// Kiểm tra trạng thái thanh toán từ Stripe
try {
    $session = \Stripe\Checkout\Session::retrieve($session_id);
    if ($session->payment_status === 'paid') {
        // Thanh toán thành công, cập nhật trạng thái đơn hàng
        $query_insert = "INSERT INTO thanhtoan (id_donhang, phuong_thuc, trang_thai, ngay_thanh_toan)
                         VALUES (?, 'Stripe', 'Đã thanh toán', NOW())
                         ON DUPLICATE KEY UPDATE phuong_thuc = 'Stripe', trang_thai = 'Đã thanh toán', ngay_thanh_toan = NOW()";
        $stmt_insert = mysqli_prepare($conn, $query_insert);
        mysqli_stmt_bind_param($stmt_insert, "i", $id_donhang);
        mysqli_stmt_execute($stmt_insert);

        $query_update = "UPDATE donhang SET trang_thai = 'Đã thanh toán' WHERE id = ? AND id_khachhang = ?";
        $stmt_update = mysqli_prepare($conn, $query_update);
        mysqli_stmt_bind_param($stmt_update, "ii", $id_donhang, $id_khachhang);
        mysqli_stmt_execute($stmt_update);

        // Lưu lịch sử mua hàng
        $query_get_products = "SELECT id_sanpham FROM chitietdonhang WHERE id_donhang = ?";
        $stmt_get_products = mysqli_prepare($conn, $query_get_products);
        mysqli_stmt_bind_param($stmt_get_products, "i", $id_donhang);
        mysqli_stmt_execute($stmt_get_products);
        $result_products = mysqli_stmt_get_result($stmt_get_products);

        while ($row = mysqli_fetch_assoc($result_products)) {
            $id_sanpham = $row["id_sanpham"];
            $query_insert_history = "INSERT INTO lichsu_mua (id_khachhang, id_sanpham) VALUES (?, ?)";
            $stmt_insert_history = mysqli_prepare($conn, $query_insert_history);
            mysqli_stmt_bind_param($stmt_insert_history, "ii", $id_khachhang, $id_sanpham);
            mysqli_stmt_execute($stmt_insert_history);
        }

        // Xóa giỏ hàng
        $query_delete_cart = "DELETE FROM giohang WHERE id_khachhang = ?";
        $stmt_delete_cart = mysqli_prepare($conn, $query_delete_cart);
        mysqli_stmt_bind_param($stmt_delete_cart, "i", $id_khachhang);
        mysqli_stmt_execute($stmt_delete_cart);

        // Chuyển hướng đến trang thành công
        header("Location: order_success.php?id_donhang=" . $id_donhang);
        exit();
    } else {
        echo "<h2>Thanh toán thất bại!</h2>";
        echo "<p>Trạng thái: " . $session->payment_status . "</p>";
        echo "<a href='/BTLPHP/CHECKOUT/checkout.php'>Quay lại</a>";
        exit();
    }
} catch (\Stripe\Exception\ApiErrorException $e) {
    echo "<h2>Lỗi khi kiểm tra thanh toán!</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<a href='/BTLPHP/CHECKOUT/checkout.php'>Quay lại</a>";
    exit();
}

mysqli_close($conn);
?>