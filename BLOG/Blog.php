<?php
session_start();
include("../db_connect.php"); // Kết nối CSDL
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <link rel="stylesheet" href="Blogcss.css">
</head>
<body>

<?php include("../header.php"); ?>

<!-- Blog Section -->
<section class="blog-header">
    <div class="breadcrumb">
        TRANG CHỦ / <span>BLOG</span>
    </div>
    <h1>Blog</h1>
</section>

<!-- Hiển thị danh sách bài viết -->
<section class="blog-info">
<section class="blog-container">
    <div class="blog-grid">
        <?php
        $sql = "SELECT * FROM baiviet ORDER BY ngay_dang DESC";
        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="blog-card">';
            echo '  <a href="blog_chitiet.php?id='.$row['id'].'">';
            if (!empty($row['hinh_anh']) && filter_var($row['hinh_anh'], FILTER_VALIDATE_URL)) {
                echo '    <img src="'.$row['hinh_anh'].'" alt="'.$row['tieu_de'].'">';
            } else {
                echo '    <img src="default.jpg" alt="Không có ảnh">';
            }
            echo '    <div class="blog-content">';
            echo '      <h3>'.$row['tieu_de'].'</h3>';
            echo '      <p>'.substr($row['noi_dung'], 0, 100).'...</p>';
            echo '      <span class="read-more">Xem thêm</span>';
            echo '    </div>';
            echo '  </a>';
            echo '</div>';
        }
        ?>
    </div>
</section>
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
