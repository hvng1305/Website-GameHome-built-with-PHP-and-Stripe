<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../header.php");
include("../db_connect.php");

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    echo "Không tìm thấy sản phẩm!";
    exit();
}

$id = intval($_GET["id"]);

$sql = "SELECT * FROM sanpham WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Sản phẩm không tồn tại!";
    exit();
}

$sql_featured = "SELECT * FROM sanpham WHERE id != ? ORDER BY RAND() LIMIT 6";
$stmt_featured = $conn->prepare($sql_featured);
$stmt_featured->bind_param("i", $id);
$stmt_featured->execute();
$result_featured = $stmt_featured->get_result();

$sql_reviews = "SELECT r.so_sao, r.binh_luan, r.ngay_danh_gia, t.ten_dang_nhap 
                FROM danhgia r 
                JOIN taikhoan t ON r.id_khachhang = t.id 
                WHERE r.id_sanpham = ? 
                ORDER BY r.ngay_danh_gia DESC";
$stmt_reviews = $conn->prepare($sql_reviews);
$stmt_reviews->bind_param("i", $id);
$stmt_reviews->execute();
$result_reviews = $stmt_reviews->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product["ten_game"]); ?></title>
    <link rel="stylesheet" href="Productcss.css">
</head>
<body>

<div class="product-detail">
    <div class="product-image">
        <img src="<?php echo htmlspecialchars($product['hinh_anh']); ?>" alt="<?php echo htmlspecialchars($product['ten_game']); ?>">
    </div>
    
    <div class="product-info">
        <h1><?php echo htmlspecialchars($product["ten_game"]); ?></h1>
        <p class="price"><?php echo number_format($product["gia"], 0, ',', '.'); ?> đ</p>
        <p class="description"><?php echo nl2br(htmlspecialchars($product["mo_ta"])); ?></p>

        <div class="small-info">
            <p><b>Nhà phát hành:</b> <?php echo htmlspecialchars($product["nha_phat_hanh"]); ?></p>
            <p><b>Ngày phát hành:</b> <?php echo htmlspecialchars($product["ngay_phat_hanh"]); ?></p>
        </div>

        <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
            <input type="hidden" name="id_sanpham" value="<?php echo $product['id']; ?>">
            <button type="submit" class="add-to-cart">Thêm vào giỏ hàng</button>
        </form>
    </div>
</div>

<div class="reviews-section">
    <h2>Đánh giá sản phẩm</h2>
    <div class="reviews-container">
        <div class="reviews-list">
            <?php
            if ($result_reviews->num_rows > 0) {
                while ($review = $result_reviews->fetch_assoc()) {
                    echo '<div class="review">';
                    echo '<p><strong>' . htmlspecialchars($review["ten_dang_nhap"]) . '</strong> - ⭐ ' . $review["so_sao"] . ' sao</p>';
                    echo '<p>' . nl2br(htmlspecialchars($review["binh_luan"])) . '</p>';
                    echo '<p class="review-date">Ngày đánh giá: ' . htmlspecialchars($review["ngay_danh_gia"]) . '</p>';
                    echo '</div>';
                }
            } else {
                echo "<p>Chưa có đánh giá nào.</p>";
            }
            ?>
        </div>

        <div class="review-form-container">
            <?php if (isset($_SESSION["id_khachhang"])) : ?>
                <form action="submit_review.php" method="POST" class="review-form">
                    <input type="hidden" name="id_sanpham" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="id_khachhang" value="<?php echo $_SESSION["id_khachhang"]; ?>">
                    
                    <label for="rating">Chọn số sao:</label>
                    <select name="so_sao" id="rating" required>
                        <option value="5">5 Sao</option>
                        <option value="4">4 Sao</option>
                        <option value="3">3 Sao</option>
                        <option value="2">2 Sao</option>
                        <option value="1">1 Sao</option>
                    </select>
                    
                    <label for="review">Đánh giá:</label>
                    <textarea name="binh_luan" id="review" required></textarea>
                    
                    <button type="submit" class="submit-review">Gửi đánh giá</button>
                </form>
            <?php else : ?>
                <p><a href="../login.php">Đăng nhập</a> để gửi đánh giá.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<section class="container">
    <div class="title-container">
        <div>
            <h2 class="title">Sản phẩm nổi bật</h2>
            <p class="description">Những tựa game cùng thể loại được yêu thích nhất </p>
        </div>
        <a href="all_products.php" class="view-all">Xem tất cả</a>
    </div>
    <div class="product-list">
        <?php while ($row = $result_featured->fetch_assoc()) { ?>
            <div class="product-card">
                <a href="/BTLPHP/PRODUCT/product_detail.php?id=<?php echo $row['id']; ?>">
                    <img src="<?php echo htmlspecialchars($row['hinh_anh']); ?>" alt="<?php echo htmlspecialchars($row['ten_game']); ?>">
                    <h2><?php echo htmlspecialchars($row['ten_game']); ?></h2>
                </a>
                <p class="price"><?php echo number_format($row['gia'], 0, ',', '.'); ?> đ</p>
                <p class="category"><?php echo htmlspecialchars($row['the_loai']); ?></p>
                <form action="/BTLPHP/PRODUCT/add_to_cart.php" method="POST" class="add-to-cart-form">
                    <input type="hidden" name="id_sanpham" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="add-to-cart">Thêm vào giỏ hàng</button>
                </form>
            </div>
        <?php } ?>
    </div>
</section>

<!-- Toast Notification -->
<div id="toast" class="toast"></div>

<footer class="footer">
    <!-- Giữ nguyên footer như cũ -->
</footer>

<script>
    // Xử lý add to cart bằng AJAX
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
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

    // Xử lý submit review bằng AJAX
    document.querySelector('.review-form')?.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);

        fetch('submit_review.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            showToast(data.message, data.success);
            if (data.success) {
                setTimeout(() => {
                    location.reload(); // Tải lại trang để hiển thị đánh giá mới
                }, 2000);
            }
        })
        .catch(error => {
            showToast('Có lỗi xảy ra!', false);
            console.error('Error:', error);
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