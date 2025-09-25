<?php
session_start();
include("../header.php");
include("../db_connect.php");

if (!isset($_SESSION['id_khachhang'])) {
    header("Location: User.php");
    exit();
}

$id_khachhang = $_SESSION['id_khachhang'];
$message = "";

// Truy vấn thông tin người dùng
$query = "SELECT ten_dang_nhap, email, so_dien_thoai, dia_chi FROM taikhoan WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_khachhang);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Xử lý cập nhật thông tin
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten_dang_nhap = $_POST['ten_dang_nhap'];
    $email = $_POST['email'];
    $so_dien_thoai = $_POST['so_dien_thoai'];
    $dia_chi = $_POST['dia_chi'];

    $update_query = "UPDATE taikhoan SET ten_dang_nhap = ?, email = ?, so_dien_thoai = ?, dia_chi = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssi", $ten_dang_nhap, $email, $so_dien_thoai, $dia_chi, $id_khachhang);
    
    if ($stmt->execute()) {
        $message = "Cập nhật thành công!";
        $user['ten_dang_nhap'] = $ten_dang_nhap;
        $user['email'] = $email;
        $user['so_dien_thoai'] = $so_dien_thoai;
        $user['dia_chi'] = $dia_chi;
    } else {
        $message = "Lỗi khi cập nhật!";
    }
}

// Truy vấn lịch sử mua hàng với số lần tải từ chitietdonhang
$query = "SELECT dh.id, sp.ten_game, sp.gia, dh.ngay_dat, sp.id AS sanpham_id, 
          COALESCE(ctdh.so_lan_tai, 0) AS so_lan_tai
          FROM donhang dh
          JOIN chitietdonhang ctdh ON dh.id = ctdh.id_donhang
          JOIN sanpham sp ON ctdh.id_sanpham = sp.id
          WHERE dh.id_khachhang = ?
          ORDER BY dh.ngay_dat DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_khachhang);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ cá nhân</title>
    <link rel="stylesheet" href="Usercss.css">
    <style>
        /* CSS giữ nguyên như cũ */
        .btn-download {
            display: inline-block;
            padding: 8px 16px;
            background: linear-gradient(90deg, #007bff, #00c4ff);
            color: white;
            font-weight: bold;
            border-radius: 5px;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 123, 255, 0.3);
        }

        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.5);
        }

        .btn-download:active {
            transform: translateY(0);
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
            animation: spin 1.5s linear infinite;
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
    <div class="profile-wrapper">
        <section class="profile-container">
            <h1>Hồ sơ cá nhân</h1>
            <?php if (!empty($message)): ?>
                <div class="message <?php echo $message === "Cập nhật thành công!" ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <label>Tên đăng nhập</label>
                <input type="text" name="ten_dang_nhap" value="<?php echo htmlspecialchars($user['ten_dang_nhap']); ?>" required>

                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                <label>Số điện thoại</label>
                <input type="text" name="so_dien_thoai" value="<?php echo htmlspecialchars($user['so_dien_thoai']); ?>" required>

                <label>Địa chỉ</label>
                <input type="text" name="dia_chi" value="<?php echo htmlspecialchars($user['dia_chi']); ?>" required>

                <button type="submit">Lưu thay đổi</button>
            </form>
        </section>

        <section class="order-history">
            <h2>Lịch sử mua hàng</h2>
            <table>
                <tr>
                    <th>Mã đơn</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Ngày đặt</th>
                    <th>Tải xuống</th>
                </tr>
                <?php while ($order = $orders->fetch_assoc()) { ?>
                <tr data-product-id="<?php echo $order['sanpham_id']; ?>">
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo htmlspecialchars($order['ten_game']); ?></td>
                    <td><?php echo number_format($order['gia'], 0, ',', '.') . " VNĐ"; ?></td>
                    <td><?php echo $order['ngay_dat']; ?></td>
                    <td class="product-download">
                        <?php if ($order['so_lan_tai'] < 5) { ?>
                            <a href="#" class="btn-download" 
                               data-download-id="<?php echo $order['sanpham_id']; ?>" 
                               data-donhang-id="<?php echo $order['id']; ?>">
                               Tải xuống (<?php echo $order['so_lan_tai']; ?>/5)
                            </a>
                        <?php } else { ?>
                            <span class="btn-disabled">Đã đạt giới hạn tải (5/5)</span>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </table>
            <div class="download-message" id="download-message"></div>
        </section>
    </div>

    <script>
    const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms));

    document.querySelectorAll('.btn-download').forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            const downloadId = this.getAttribute('data-download-id');
            const donhangId = this.getAttribute('data-donhang-id');
            const messageDiv = document.getElementById('download-message');

            this.classList.add('loading');
            this.style.pointerEvents = 'none';

            try {
                await delay(2000);

                // Sửa đường dẫn để trỏ đến CHECKOUT
                const response = await fetch('../CHECKOUT/download_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `download_id=${downloadId}&id_donhang=${donhangId}`
                });
                const data = await response.json();

                this.classList.remove('loading');
                this.style.pointerEvents = 'auto';

                if (data.success) {
                    const link = document.createElement('a');
                    link.href = `../CHECKOUT/download_file.php?download_id=${downloadId}&id_donhang=${donhangId}`;
                    link.download = '';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    const row = document.querySelector(`tr[data-product-id="${downloadId}"] .product-download`);
                    const newCount = data.new_count;
                    if (newCount < 5) {
                        row.innerHTML = `<a href="#" class="btn-download" data-download-id="${downloadId}" data-donhang-id="${donhangId}">Tải xuống (${newCount}/5)</a>`;
                    } else {
                        row.innerHTML = `<span class="btn-disabled">Đã đạt giới hạn tải (5/5)</span>`;
                    }

                    row.querySelector('.btn-download')?.addEventListener('click', arguments.callee);
                } else {
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
$stmt->close();
$conn->close();
?>