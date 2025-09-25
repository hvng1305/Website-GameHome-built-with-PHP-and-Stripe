<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Kết nối database
include("../db_connect.php");

// Hàm đếm số lượng sản phẩm trong giỏ hàng
function getCartItemCount($conn, $id_khachhang) {
    $sql = "SELECT COUNT(*) as total FROM giohang WHERE id_khachhang = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_khachhang);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Lấy số lượng sản phẩm trong giỏ hàng nếu người dùng đã đăng nhập
$cart_count = 0;
if (isset($_SESSION["id_khachhang"])) {
    $cart_count = getCartItemCount($conn, $_SESSION["id_khachhang"]);
}
?>

<header class="header">
    <div class="logo">
        <img src="http://localhost/BTLPHP/imgnavi/logoWEB.png" alt="Game Home Logo" />
    </div>

    <nav class="navigation">
        <ul>
            <li><a href="/BTLPHP/HOME/Home.php">TRANG CHỦ </a></li>
            <li><a href="/BTLPHP/PRODUCT/Product.php">SẢN PHẨM </a></li>
            <li><a href="/BTLPHP/ABOUT/About.php">GIỚI THIỆU </a></li>
            <li><a href="/BTLPHP/BLOG/Blog.php">BÀI VIẾT </a></li>
            <li><a href="/BTLPHP/CONTACT/Contact.php">LIÊN HỆ </a></li>
        </ul>
    </nav>

    <div class="icons">
        <a href="/BTLPHP/SEARCH/Search.php"><img src="/BTLPHP/imgnavi/iconSearch.png" alt="Search"></a>
        
        <div class="user-dropdown">
            <a href="#"><img src="/BTLPHP/imgnavi/iconUser.png" alt="User"></a>
            <div class="dropdown-menu">
                <?php if (isset($_SESSION["ten_dang_nhap"])): ?>
                    <a href="/BTLPHP/USER/Profile.php">Hồ sơ cá nhân</a>
                    <a href="/BTLPHP/USER/logout.php">Đăng xuất</a>
                <?php else: ?>
                    <a href="/BTLPHP/USER/User.php">Đăng nhập</a>
                    <a href="/BTLPHP/USER/Register.html">Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Icon giỏ hàng với số lượng -->
        <a href="/BTLPHP/CART/Cart.php" class="cart-icon-wrapper">
            <img src="/BTLPHP/imgnavi/iconCart.png" alt="Cart">
            <?php if ($cart_count > 0): ?>
                <span class="cart-count"><?php echo $cart_count; ?></span>
            <?php endif; ?>
        </a>
    </div>
</header>

<!-- Liên kết CSS -->
<link rel="stylesheet" href="/BTLPHP/css/header.css">