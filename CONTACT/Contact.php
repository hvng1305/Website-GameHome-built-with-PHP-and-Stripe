<?php
session_start();
include("../db_connect.php");

// Truy vấn lấy danh sách sản phẩm
$sql = "SELECT * FROM sanpham ORDER BY gia DESC LIMIT 6";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="Contactcss.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<?php include("../header.php"); ?>

<section class="contact-header">
    <div class="breadcrumb">
        TRANG CHỦ / <span> LIÊN HỆ </span>
    </div>
    <h1>LIÊN HỆ </h1>
</section>

<section class="contact-info">
    <div class="contact-box">
        <div class="icon">
            <i class="fas fa-map-marker-alt"></i>
        </div>
        <h3>Địa chỉ </h3>
        <p>Thành Phố Hà Nội <br> Đường Võ Chí Công </p>
    </div>
    <div class="contact-box">
        <div class="icon">
            <i class="fas fa-phone-alt"></i>
        </div>
        <h3>Số điện thoại </h3>
        <p>1-555-123-4567<br>1-800-123-4567</p>
    </div>
    <div class="contact-box">
        <div class="icon">
            <i class="fas fa-envelope"></i>
        </div>
        <h3>Email </h3>
        <p>info@gamehome.com<br>contact@gamehome.com</p>
    </div>
</section>
<section class="contact-form-section">
    <!-- Bên trái: Google Maps -->
    <div class="contact-map">
    <iframe
    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.856093708067!2d105.78293331538237!3d21.02851198599838!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135abc9a52d1e23%3A0x5d1b6e6e75a5c5c7!2sHanoi%2C%20Vietnam!5e0!3m2!1sen!2s!4v1631561234567!5m2!1sen!2s"
    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy">
</iframe>

    </div>

    <!-- Bên phải: Form gửi tin nhắn -->
    <div class="contact-form">
        <h2>Gửi tin nhắn cho chúng tôi </h2>
        <form action="lienhe.php" method="POST">
            <div class="form-group">
                <input type="text" name="first_name" placeholder="Họ" required>
                <input type="text" name="last_name" placeholder="Tên" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email của bạn" required>
                <input type="tel" name="phone" placeholder="Số điện thoại">
            </div>
            <input type="text" name="subject" placeholder="Chủ đề">
            <textarea name="message" rows="5" placeholder="Tin nhắn của bạn" required></textarea>
            <button type="submit">Gửi tin nhắn </button>
        </form>
    </div>
</section>

<section class="container">
    <div class="title-container">
        <div>
            <h2 class="title">Featured Products</h2>
            <p class="description">Feugiat pretium nibh ipsum consequat commodo.</p>
        </div>
        <a href="all_products.php" class="view-all">View All</a>
    </div>
    <div class="product-list">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="product-card">
            <a href="/BTLPHP/PRODUCT/product_detail.php?id=<?php echo $row['id']; ?>">
                    <img src="<?php echo $row['hinh_anh']; ?>" alt="<?php echo $row['ten_game']; ?>">
                    <h2><?php echo $row['ten_game']; ?></h2>
                </a>
                <p class="price"><?php echo number_format($row['gia'], 0, ',', '.'); ?> đ</p>
                <p class="category"><?php echo $row['the_loai']; ?></p>
                <form action="/BTLPHP/PRODUCT/add_to_cart.php" method="POST">
                    <input type="hidden" name="id_sanpham" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="add-to-cart">Add to Cart</button>
                </form>
            </div>
        <?php } ?>
    </div>
    </section>

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
