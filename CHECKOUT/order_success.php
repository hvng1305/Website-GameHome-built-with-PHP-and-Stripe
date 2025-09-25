<?php
session_start();
include("../db_connect.php");

// Kiểm tra đăng nhập
if (!isset($_SESSION["id_khachhang"])) {
    header("Location: /BTLPHP/USER/User.html");
    exit();
}

// Kiểm tra id đơn hàng
if (!isset($_GET["id_donhang"])) {
    echo "<script>alert('Không tìm thấy đơn hàng!'); window.location.href='checkout.php';</script>";
    exit();
}

$id_donhang = $_GET["id_donhang"];
$id_khachhang = $_SESSION["id_khachhang"];

// Truy vấn thông tin đơn hàng
$query_order = "SELECT donhang.id, donhang.trang_thai, donhang.tong_tien, thanhtoan.phuong_thuc 
                FROM donhang 
                LEFT JOIN thanhtoan ON donhang.id = thanhtoan.id_donhang
                WHERE donhang.id = ? AND donhang.id_khachhang = ?";
$stmt_order = mysqli_prepare($conn, $query_order);
mysqli_stmt_bind_param($stmt_order, "ii", $id_donhang, $id_khachhang);
mysqli_stmt_execute($stmt_order);
$result_order = mysqli_stmt_get_result($stmt_order);
$order = mysqli_fetch_assoc($result_order);

if (!$order) {
    echo "<script>alert('Đơn hàng không hợp lệ!'); window.location.href='checkout.php';</script>";
    exit();
}

// Truy vấn sản phẩm trong đơn hàng, bao gồm số lần tải
$query_products = "SELECT sanpham.id AS sanpham_id, sanpham.ten_game, sanpham.hinh_anh, sanpham.gia, sanpham.file_game, chitietdonhang.so_lan_tai
                   FROM chitietdonhang 
                   JOIN sanpham ON chitietdonhang.id_sanpham = sanpham.id
                   WHERE chitietdonhang.id_donhang = ?";
$stmt_products = mysqli_prepare($conn, $query_products);
mysqli_stmt_bind_param($stmt_products, "i", $id_donhang);
mysqli_stmt_execute($stmt_products);
$result_products = mysqli_stmt_get_result($stmt_products);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán</title>
    <link rel="stylesheet" href="Checkoutcss.css">
    <style>
        /* Cải tiến nút Tải xuống */
        .btn-download {
            display: inline-block;
            padding: 8px 16px;
            background: linear-gradient(90deg, #007bff, #00c4ff); /* Gradient xanh dương */
            color: white;
            font-weight: bold;
            border-radius: 5px;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 123, 255, 0.3);
            position: relative;
        }

        .btn-download:hover {
            transform: translateY(-2px); /* Nâng nhẹ nút khi hover */
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.5);
        }

        .btn-download:active {
            transform: translateY(0); /* Trở lại vị trí ban đầu khi nhấn */
            box-shadow: 0 2px 5px rgba(0, 123, 255, 0.3);
        }

        .btn-download.loading::after {
            content: '';
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid white;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1.5s linear infinite; /* Làm chậm animation loading */
            margin-left: 5px;
            vertical-align: middle;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .btn-disabled {
            display: inline-block;
            padding: 8px 16px;
            background: #ccc;
            color: #666;
            font-weight: bold;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Thông báo dạng toast */
        .download-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #ff4444;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            display: none;
            z-index: 1000;
            animation: fadeInOut 3s ease forwards;
        }

        @keyframes fadeInOut {
            0% { opacity: 0; transform: translateY(-20px); }
            10% { opacity: 1; transform: translateY(0); }
            90% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <?php include("../header.php"); ?>

    <section class="checkout-container">
        <div class="cart-left">
            <table>
                <thead>
                    <tr>
                        <th class="left-align">Sản phẩm</th>
                        <th>Tải xuống</th>
                    </tr>
                </thead>
                <tbody id="product-list">
                    <?php while ($product = mysqli_fetch_assoc($result_products)) { ?>
                        <tr data-product-id="<?= $product['sanpham_id'] ?>">
                            <td class="product-info">
                                <img src="<?= htmlspecialchars($product['hinh_anh']) ?>" alt="<?= htmlspecialchars($product['ten_game']) ?>">
                                <span><?= htmlspecialchars($product['ten_game']) ?></span>
                            </td>
                            <td class="product-download">
                                <?php if ($product['so_lan_tai'] < 5) { ?>
                                    <a href="#" class="btn-download" data-download-id="<?= $product['sanpham_id'] ?>" data-donhang-id="<?= $id_donhang ?>">Tải xuống (<?= $product['so_lan_tai'] ?>/5)</a>
                                <?php } else { ?>
                                    <span class="btn-disabled">Đã đạt giới hạn tải (5/5)</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="download-message" id="download-message"></div>
        </div>

        <div class="cart-right">
            <h2>Thông tin thanh toán</h2>
            <p><strong>Mã đơn hàng:</strong> <?= $order["id"]; ?></p>
            <p><strong>Trạng thái:</strong> <?= $order["trang_thai"]; ?></p>
            <p><strong>Tổng tiền:</strong> <?= number_format($order["tong_tien"], 0, ',', '.'); ?> VNĐ</p>
            <p><strong>Phương thức thanh toán:</strong> <?= $order["phuong_thuc"]; ?></p>
            <a href="index.php" class="btn-back">Quay về trang chủ</a>
        </div>
    </section>

    <script>
        // Hàm tạo độ trễ giả lập
        const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms));

        document.querySelectorAll('.btn-download').forEach(button => {
            button.addEventListener('click', async function(e) {
                e.preventDefault();
                const downloadId = this.getAttribute('data-download-id');
                const donhangId = this.getAttribute('data-donhang-id');
                const messageDiv = document.getElementById('download-message');

                // Thêm hiệu ứng loading
                this.classList.add('loading');
                this.style.pointerEvents = 'none'; // Vô hiệu hóa nút trong khi tải

                try {
                    // Thêm độ trễ giả lập 2 giây
                    await delay(2000);

                    // Gửi yêu cầu AJAX để kiểm tra và cập nhật số lần tải
                    const response = await fetch('download_handler.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `download_id=${downloadId}&id_donhang=${donhangId}`
                    });
                    const data = await response.json();

                    // Xóa hiệu ứng loading
                    this.classList.remove('loading');
                    this.style.pointerEvents = 'auto';

                    if (data.success) {
                        // Tải file
                        const link = document.createElement('a');
                        link.href = `download_file.php?download_id=${downloadId}&id_donhang=${donhangId}`;
                        link.download = '';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        // Cập nhật số lần tải trên giao diện
                        const row = document.querySelector(`tr[data-product-id="${downloadId}"] .product-download`);
                        const newCount = data.new_count;
                        if (newCount < 5) {
                            row.innerHTML = `<a href="#" class="btn-download" data-download-id="${downloadId}" data-donhang-id="${donhangId}">Tải xuống (${newCount}/5)</a>`;
                        } else {
                            row.innerHTML = `<span class="btn-disabled">Đã đạt giới hạn tải (5/5)</span>`;
                        }

                        // Gắn lại sự kiện cho nút mới
                        row.querySelector('.btn-download')?.addEventListener('click', arguments.callee);
                    } else {
                        // Hiển thị thông báo lỗi dạng toast
                        messageDiv.textContent = data.message;
                        messageDiv.style.display = 'block';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.classList.remove('loading');
                    this.style.pointerEvents = 'auto';
                    messageDiv.textContent = 'Đã có lỗi xảy ra, vui lòng thử lại!';
                    messageDiv.style.display = 'block';
                }
            });
        });
    </script>
</body>
</html>

<?php
mysqli_stmt_close($stmt_order);
mysqli_stmt_close($stmt_products);
mysqli_close($conn);
?>