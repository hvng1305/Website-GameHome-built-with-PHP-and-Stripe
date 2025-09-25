<?php
session_start();
include("../db_connect.php");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
    <link rel="stylesheet" href="Aboutcss.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<?php include("../header.php"); ?>
<section class="about-header">
    <div class="breadcrumb">
        TRANG CHỦ / <span>GIỚI THIỆU </span>
    </div>
    <h1>GIỚI THIỆU </h1>
</section>

<section class="about-image">
    <img src="about.jpg" alt="About Us">
</section>

<section class="about-container">
    <div class="about-box">
        <h2>Founder: Nguyễn Văn Hạnh </h2>
        <p>Chúng tôi bắt đầu với đam mê dành cho trò chơi và mong muốn mang đến trải nghiệm mua sắm game tốt nhất cho cộng đồng.</p>
    </div>
    <div class="about-box">
        <h2>Directory: Trần Minh Phúc</h2>
        <p>Kho game đa dạng với hàng ngàn tựa game từ nhiều thể loại, giúp bạn dễ dàng tìm kiếm và sở hữu những trò chơi yêu thích.</p>
    </div>
</section>


<section class="stats-section">
    <h1>Hàng Ngàn Tựa Game Đang Chờ Bạn<br>Trải Nghiệm Ngay Hôm Nay!</h1>

    <div class="stats-container">
        <div class="stat-box">
            <h2 class="counter" data-target="5000">0</h2>
            <p>TRÒ CHƠI ĐÃ BÁN</p>
        </div>
        <div class="stat-box">
            <h2 class="counter" data-target="4000">0</h2>
            <p>KHÁCH HÀNG HÀI LÒNG</p>
        </div>
        <div class="stat-box">
            <h2 class="counter" data-target="99">0</h2>
            <p>ĐÁNH GIÁ TÍCH CỰC</p>
        </div>
    </div>

    <section class="info-container">
        <p>Chúng tôi cung cấp hàng ngàn tựa game chất lượng, từ game hành động, phiêu lưu đến chiến thuật và nhập vai.  
           Với kho game khổng lồ và dịch vụ khách hàng tận tâm, bạn sẽ có những trải nghiệm chơi game tuyệt vời nhất.</p>
    </section>

    <section class="button-container">
        <button class="contact-btn" onclick="window.location.href='/BTLPHP/PRODUCT/Product.php'">Mua Ngay</button>
    </section>
</section>

   


<script>
document.addEventListener("DOMContentLoaded", () => {
    const counters = document.querySelectorAll(".counter");
    const speed = 200; // Tốc độ đếm (càng nhỏ càng nhanh)

    counters.forEach(counter => {
        const updateCount = () => {
            const target = +counter.getAttribute("data-target"); // Lấy giá trị đích
            const count = +counter.innerText; // Lấy giá trị hiện tại
            const increment = target / speed; // Tăng dần theo tỉ lệ

            if (count < target) {
                counter.innerText = Math.ceil(count + increment); // Cập nhật giá trị
                setTimeout(updateCount, 10); // Gọi lại sau 10ms
            } else {
                counter.innerText = target + (target === 5000 || target === 4000 ? "+" : "%"); // Thêm ký hiệu + hoặc %
            }
        };

        // Kích hoạt khi phần tử xuất hiện trong tầm nhìn
        const observer = new IntersectionObserver(entries => {
            if (entries[0].isIntersecting) {
                updateCount();
                observer.disconnect(); // Ngừng theo dõi sau khi đếm xong
            }
        }, { threshold: 0.5 });

        observer.observe(counter);
    });
});
</script>
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
