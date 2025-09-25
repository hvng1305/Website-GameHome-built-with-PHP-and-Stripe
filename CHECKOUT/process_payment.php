<?php
session_start();
include("../db_connect.php");

// Đặt múi giờ
date_default_timezone_set('Asia/Ho_Chi_Minh');

if (!isset($_SESSION["id_khachhang"])) {
    header("Location: /BTLPHP/USER/User.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo "<script>alert('Yêu cầu không hợp lệ!'); window.history.back();</script>";
    exit();
}

$id_donhang = $_POST['id_donhang'];
$phuong_thuc = $_POST['payment_method'];
$id_khachhang = $_SESSION["id_khachhang"];

// Kiểm tra đơn hàng
$query_check = "SELECT * FROM donhang WHERE id = ? AND id_khachhang = ?";
$stmt_check = mysqli_prepare($conn, $query_check);
mysqli_stmt_bind_param($stmt_check, "ii", $id_donhang, $id_khachhang);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);

if (mysqli_num_rows($result_check) == 0) {
    echo "<script>alert('Đơn hàng không hợp lệ!'); window.history.back();</script>";
    exit();
}

$donhang = mysqli_fetch_assoc($result_check);
$tong_tien = $donhang['tong_tien'];

if ($phuong_thuc === "Stripe") {
    // Tải thư viện Stripe
    require_once '../vendor/autoload.php';

    // Cấu hình Stripe
    $stripe_secret_key = "";
    \Stripe\Stripe::setApiKey($stripe_secret_key);

    // Chuyển đổi $tong_tien thành số nguyên
    $tong_tien_int = intval($tong_tien); // Chuyển đổi thành số nguyên

    // Tạo Checkout Session
    try {
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'], // Hỗ trợ thanh toán bằng thẻ
            'line_items' => [[
                'price_data' => [
                    'currency' => 'vnd', // Stripe hỗ trợ VNĐ
                    'product_data' => [
                        'name' => "Thanh toán đơn hàng #$id_donhang",
                    ],
                    'unit_amount' => $tong_tien_int, // Sử dụng số nguyên
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => "http://localhost/BTLPHP/CHECKOUT/payment_return.php?session_id={CHECKOUT_SESSION_ID}&id_donhang=$id_donhang",
            'cancel_url' => "http://localhost/BTLPHP/CHECKOUT/checkout.php",
            'metadata' => [
                'id_donhang' => $id_donhang,
                'id_khachhang' => $id_khachhang,
            ],
        ]);

        // Chuyển hướng đến trang thanh toán của Stripe
        header("Location: " . $checkout_session->url);
        exit();
    } catch (\Stripe\Exception\ApiErrorException $e) {
        echo "<script>alert('Lỗi khi tạo phiên thanh toán Stripe: " . $e->getMessage() . "'); window.history.back();</script>";
        exit();
    }
} else {
    // Xử lý các phương thức thanh toán khác (Chuyển khoản, Ví điện tử, Thẻ tín dụng)
    $query_insert = "INSERT INTO thanhtoan (id_donhang, phuong_thuc, trang_thai, ngay_thanh_toan)
                     VALUES (?, ?, 'Đã thanh toán', NOW())
                     ON DUPLICATE KEY UPDATE phuong_thuc = VALUES(phuong_thuc), trang_thai = 'Đã thanh toán', ngay_thanh_toan = NOW()";
    $stmt_insert = mysqli_prepare($conn, $query_insert);
    mysqli_stmt_bind_param($stmt_insert, "is", $id_donhang, $phuong_thuc);

    if (mysqli_stmt_execute($stmt_insert)) {
        $query_update = "UPDATE donhang SET trang_thai = 'Đã thanh toán' WHERE id = ?";
        $stmt_update = mysqli_prepare($conn, $query_update);
        mysqli_stmt_bind_param($stmt_update, "i", $id_donhang);
        mysqli_stmt_execute($stmt_update);

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
            mysqli_stmt_close($stmt_insert_history);
        }

        $query_delete_cart = "DELETE FROM giohang WHERE id_khachhang = ?";
        $stmt_delete_cart = mysqli_prepare($conn, $query_delete_cart);
        mysqli_stmt_bind_param($stmt_delete_cart, "i", $id_khachhang);
        mysqli_stmt_execute($stmt_delete_cart);

        header("Location: order_success.php?id_donhang=" . $id_donhang);
        exit();
    } else {
        echo "<script>alert('Có lỗi xảy ra khi thanh toán!'); window.history.back();</script>";
    }
}
?>