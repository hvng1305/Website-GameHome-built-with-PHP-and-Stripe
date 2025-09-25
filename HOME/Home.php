<?php
include("../db_connect.php");

// Truy vấn lấy danh sách sản phẩm
$sql = "SELECT * FROM sanpham ORDER BY gia DESC LIMIT 9";
$result = mysqli_query($conn, $sql);

// Truy vấn lấy danh sách bài viết mới nhất
$sql_blog = "SELECT * FROM baiviet ORDER BY ngay_dang DESC LIMIT 9";
$result_blog = mysqli_query($conn, $sql_blog);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Home</title>
    <link rel="stylesheet" href="Homecss.css">
</head>
<body>
<?php include("../header.php"); ?>

    <section class="hero">
        <div class="hero-text">
            <p>Chỉ từ 129$</p>
            <h2>Overwatch II</h2>
            <div class="hero-buttons">
                <button class="buy-now">Mua Ngay</button>
                <button class="watch-video">Xem Video</button>
            </div>
        </div>
        <video autoplay loop muted class="hero-video">
            <source src="imgWeb/Overwatch Animated Short - Dragons.mp4" type="video/mp4">
            Trình duyệt của bạn không hỗ trợ video.
        </video>
    </section>

    <section class="features">
        <div class="feature-left">
            <div class="feature">
                <img src="image/iconGame.png" alt="Quality Games">
                <h3>Trò chơi chất lượng cao</h3>
                <p>Luôn luôn mang đến các trò chơi chất lượng cao</p>
            </div>
            <div class="feature">
                <img src="image/iconMoney.png" alt="100% Money Back">
                <h3>100% hoàn tiền</h3>
                <p>Bạn có 7 ngày để hoàn lại</p>
            </div>
        </div>
        <div class="feature-right">
            <div class="feature">
                <img src="image/iconSP.png" alt="Support 24/7">
                <h3>Hỗ trợ 24/7</h3>
                <p>Chúng tôi luôn hỗ trợ trong ngày</p>
            </div>
            <div class="feature">
                <img src="image/iconSecure.png" alt="100% Secure">
                <h3>100% Bảo mật</h3>
                <p>Quá trình thanh toán của bạn luôn được bảo đảm an toàn</p>
            </div>
        </div>
    </section>

    <section class="new-section">
        <div class="container">
            <div class="main-item">
                <img src="image/Cyberpunk2077.png" alt="Main Game">
                <h2>Games</h2>
            </div>
            <div class="sub-items">
                <div class="sub-item">
                    <img src="image/PCgaming.png" alt="PC Gaming">
                    <h3>PC Gaming</h3>
                    <button>Mua Ngay</button>
                </div>
                <div class="sub-item">
                    <img src="image/Latopgaming.png" alt="Laptops">
                    <h3>Laptops</h3>
                    <button>Mua Ngay</button>
                </div>
                <div class="sub-item">
                    <img src="image/GamingPS5.png" alt="Gaming">
                    <h3>Gaming</h3>
                    <button>Mua Ngay</button>
                </div>
            </div>
        </div>
        <div class="feature-list">
            <div class="feature">
                <h3>#1 Game Phổ Biến</h3>
                <p>Khám phá bộ sưu tập các game đang được yêu thích nhất hiện nay.</p>
            </div>
            <div class="feature">
                <h3>#2 Game Mới Phát Hành</h3>
                <p>Luôn cập nhật với những tựa game mới nhất.</p>
            </div>
            <div class="feature">
                <h3>#3 Ưu Đãi Tốt Nhất</h3>
                <p>Đừng bỏ lỡ những ưu đãi tuyệt vời nhất.</p>
            </div>
            <div class="feature">
                <h3>#4 Gói Game Độc Quyền</h3>
                <p>Tận hưởng giá trị nhiều hơn với các gói game độc quyền.</p>
            </div>
        </div>
    </section>

    <section class="oculus-section">
        <div class="oculus-container">
            <div class="oculus-content">
                <h1>Oculus VR</h1>
                <p>Trải nghiệm thực tế ảo đỉnh cao với công nghệ tiên tiến! Khám phá thế giới ảo sống động, chân thực với Oculus VR – nơi giải trí và tương tác không giới hạn.</p>
                <div class="oculus-buttons">
                    <button class="offer-btn">Xem thêm</button>
                    <button class="video-btn">Mua ngay</button>
                </div>
            </div>
            <div class="oculus-image">
                <img src="imgWeb/oculuspng.png" alt="Oculus VR">
            </div>
        </div>
    </section>

    <section class="container">
        <div class="title-container">
            <div>
                <h2 class="title">Sản phẩm nổi bật</h2>
                <p class="description">Những trò chơi nổi bật với đồ họa đẹp mắt.</p>
            </div>
            <a href="all_products.php" class="view-all">Xem tất cả</a>
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
                    <form action="/BTLPHP/PRODUCT/add_to_cart.php" method="POST" class="add-to-cart-form">
                        <input type="hidden" name="id_sanpham" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="add-to-cart">Thêm vào giỏ hàng</button>
                    </form>
                </div>
            <?php } ?>
        </div>
    </section>

    <section class="video-section">
        <div class="video-overlay">
            <h2>Trải Nghiệm Game Đỉnh Cao</h2>
            <p>Khám phá thế giới game với đồ họa tuyệt đẹp và lối chơi hấp dẫn.</p>
            <button class="watch-more" onclick="window.location.href='/BTLPHP/PRODUCT/Product.php'">Xem Thêm</button>
        </div>
        <video autoplay loop muted>
            <source src="imgWeb/videoWKtrailer.mp4" type="video/mp4">
            Trình duyệt của bạn không hỗ trợ video.
        </video>
    </section>

    <section class="container">
        <div class="title-container">
            <div>
                <h2 class="title">Những bài viết mới nhất</h2>
                <p class="description">Cập nhật những tin tức và bài viết mới nhất về thế giới game.</p>
            </div>
            <a href="../BLOG/Blog.php" class="view-all">Xem tất cả</a>
        </div>
        <div class="product-list">
            <?php while ($row = mysqli_fetch_assoc($result_blog)) { ?>
                <div class="product-card">
                    <a href="../BLOG/blog_chitiet.php?id=<?php echo $row['id']; ?>">
                        <img src="<?php echo $row['hinh_anh']; ?>" alt="<?php echo $row['tieu_de']; ?>">
                        <h2><?php echo $row['tieu_de']; ?></h2>
                    </a>
                    <p class="excerpt"><?php echo substr($row['noi_dung'], 0, 100) . '...'; ?></p>
                    <a href="../BLOG/blog_chitiet.php?id=<?php echo $row['id']; ?>" class="read-more">Đọc thêm</a>
                </div>
            <?php } ?>
        </div>
    </section>

    <!-- Thêm toast notification -->
    <div id="toast" class="toast"></div>

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

    <script>
        // Xử lý sự kiện submit form bằng AJAX
        document.querySelectorAll('.add-to-cart-form').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(this);

                fetch('/BTLPHP/PRODUCT/add_to_cart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    showToast(data.message, data.success);
                })
                .catch(error => {
                    showToast('Có lỗi xảy ra!', false);
                    console.error('Error:', error);
                });
            });
        });

        // Hàm hiển thị toast
        function showToast(message, success) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.style.background = success ? '#00cc00' : '#ff3333';
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
    </script>
</body>
</html>