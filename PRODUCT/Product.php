<?php
include("../header.php");
include("../db_connect.php"); // File kết nối database

// Truy vấn lấy danh sách sản phẩm
$sql = "SELECT * FROM sanpham";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="Productcss.css">
</head>
<body>

<!-- Cart Section -->
<section class="product-header">
    <div class="breadcrumb">
        TRANG CHỦ / <span>SẢN PHẨM </span>
    </div>
    <h1>SẢN PHẨM </h1>
</section>

<div class="container">
    <div class="product-list">
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <div class="product-card">
            <a href="product_detail.php?id=<?php echo $row['id']; ?>">
                <img src="<?php echo $row['hinh_anh']; ?>" alt="<?php echo $row['ten_game']; ?>">
                <h2><?php echo $row['ten_game']; ?></h2>
            </a>
            <p class="price"><?php echo number_format($row['gia'], 0, ',', '.'); ?> đ</p>
            <p class="category"><?php echo $row['the_loai']; ?></p>

            <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                <input type="hidden" name="id_sanpham" value="<?php echo $row['id']; ?>">
                <button type="submit" class="add-to-cart">Thêm vào giỏ hàng</button>
            </form>
        </div>
    <?php } ?>
    </div>
</div>

<!-- Toast Notification -->
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
            event.preventDefault(); // Ngăn form submit theo cách thông thường

            const formData = new FormData(this);

            fetch('add_to_cart.php', {
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
        toast.style.background = success ? '#00cc00' : '#ff3333'; // Xanh nếu thành công, đỏ nếu lỗi
        toast.classList.add('show');

        // Ẩn toast sau 3 giây
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
</script>

</body>
</html>