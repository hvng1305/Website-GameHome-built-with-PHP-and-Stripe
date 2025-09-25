<?php
session_start();
include("../db_connect.php");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="Cartcss.css">
</head>
<body>

<?php include("../header.php"); ?>

<!-- Cart Section -->
<section class="cart-header">
    <div class="breadcrumb">
        TRANG CHỦ / <span>GIỎ HÀNG</span>
    </div>
    <h1>GIỎ HÀNG</h1>
</section>

<div class="cart-container">
    <?php if (!isset($_SESSION["id_khachhang"])): ?>
        <p class="login-required">
            Bạn cần <a href="/BTLPHP/USER/User.html">đăng nhập</a> để xem giỏ hàng.
        </p>
    <?php else: 
        $id_khachhang = $_SESSION["id_khachhang"];

        // Lấy danh sách sản phẩm trong giỏ hàng
        $sql = "SELECT g.id_sanpham, s.ten_game, s.gia, s.hinh_anh
                FROM giohang g
                JOIN sanpham s ON g.id_sanpham = s.id
                WHERE g.id_khachhang = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_khachhang);
        $stmt->execute();
        $result = $stmt->get_result();

        $total = 0;
    ?>

    <div class="cart-content">
        <!-- Bên trái: Danh sách sản phẩm -->
        <div class="cart-left">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th class="left-align">Sản phẩm</th>
                        <th>Tạm tính</th>
                        <th></th> <!-- Bỏ chữ "Xóa" -->
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()):
                        $total += $row["gia"];
                    ?>
                    <tr>
                        <td class="product-info">
                            <img src="<?php echo htmlspecialchars($row['hinh_anh']); ?>" alt="<?php echo htmlspecialchars($row['ten_game']); ?>">
                            <div>
                                <span class="game-name"><?php echo htmlspecialchars($row['ten_game']); ?></span>
                                <span class="game-price"><?php echo number_format($row["gia"], 0, ',', '.'); ?> đ</span>
                            </div>
                        </td>
                        <td><?php echo number_format($row["gia"], 0, ',', '.'); ?> đ</td>
                        <td>
                            <form action="remove_from_cart.php" method="POST">
                                <input type="hidden" name="id_sanpham" value="<?php echo $row['id_sanpham']; ?>">
                                <button type="submit" class="remove">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Bên phải: Hiển thị từng giá sản phẩm và tổng tiền -->
<div class="cart-right">
    <h2>Chi tiết giá</h2>
    <ul class="price-list">
        <?php
        $stmt->execute(); // Chạy lại truy vấn để lấy dữ liệu
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()):
        ?>
            <li>
                <span><?php echo htmlspecialchars($row['ten_game']); ?></span>
                <span class="price"><?php echo number_format($row["gia"], 0, ',', '.'); ?> đ</span>
            </li>
        <?php endwhile; ?>
    </ul>
    <hr>
    <h2>Tổng tiền</h2>
    <p><strong><?php echo number_format($total, 0, ',', '.'); ?> đ</strong></p>
    <button class="checkout-btn" onclick="window.location.href='/BTLPHP/CHECKOUT/process_checkout.php'">Tiến hành thanh toán</button>

</div>

    </div>
    <?php endif; ?>
</div>


</body>
<footer class="footer">
    <div class="footer-container">
        <div class="footer-column">
            <img src="/BTLPHP/imgnavi/logoWEB.png" alt="Logo" class="footer-logo">
            <p>Trang web chuyên cung cấp các tựa game hot nhất với giá cả hợp lý.</p>
        </div>
        <div class="footer-column">
            <h3>Use Links</h3>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">Product</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h3>My Account</h3>
            <ul>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="cart.php">My Cart</a></li>
                <li><a href="orders.php">Order History</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h3>More Information</h3>
            <ul>
                <li><a href="policy.php">Privacy Policy</a></li>
                <li><a href="terms.php">Terms & Conditions</a></li>
                <li><a href="support.php">Customer Support</a></li>
                <li><a href="faq.php">FAQs</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2025 Game Home. All Rights Reserved.</p>
    </div>
</footer>
</html>
