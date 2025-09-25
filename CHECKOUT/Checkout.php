<?php
session_start();
include("../db_connect.php");

if (!isset($_SESSION['id_khachhang'])) {
    echo "<p>Vui lòng <a href='login.php'>đăng nhập</a> để xem đơn hàng.</p>";
    exit;
}

$id_khachhang = $_SESSION['id_khachhang'];

// Sử dụng prepared statement để tránh SQL Injection
$query_donhang = "SELECT * FROM donhang WHERE id_khachhang = ? ORDER BY ngay_dat DESC LIMIT 1";
$stmt_donhang = mysqli_prepare($conn, $query_donhang);
mysqli_stmt_bind_param($stmt_donhang, "i", $id_khachhang);
mysqli_stmt_execute($stmt_donhang);
$result_donhang = mysqli_stmt_get_result($stmt_donhang);
$donhang = mysqli_fetch_assoc($result_donhang);

if ($donhang) {
    $id_donhang = $donhang['id'];
    $tong_tien = $donhang['tong_tien'];
    $trang_thai = $donhang['trang_thai'];

    // Lấy sản phẩm từ giỏ hàng
    $query_giohang = "SELECT sanpham.ten_game, sanpham.gia, sanpham.hinh_anh 
                      FROM giohang 
                      JOIN sanpham ON giohang.id_sanpham = sanpham.id 
                      WHERE giohang.id_khachhang = ?";
    $stmt_giohang = mysqli_prepare($conn, $query_giohang);
    mysqli_stmt_bind_param($stmt_giohang, "i", $id_khachhang);
    mysqli_stmt_execute($stmt_giohang);
    $result_giohang = mysqli_stmt_get_result($stmt_giohang);
} else {
    $id_donhang = null;
    $tong_tien = 0;
    $trang_thai = "Không có đơn hàng";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán</title>
    <link rel="stylesheet" href="Checkoutcss.css">
    <style>
        .payment-option.active { border: 2px solid #28a745; }
        .payment-info { padding: 15px; border: 1px solid #ddd; margin-top: 10px; }
        .loading { display: none; text-align: center; padding: 20px; }
        .loading img { width: 50px; }
        .error { color: red; font-size: 0.9em; }
    </style>
</head>
<body>
    <?php include("../header.php"); ?>

    <section class="checkout-header">
        <div class="breadcrumb">TRANG CHỦ / <span> THANH TOÁN </span></div>
        <h1>THANH TOÁN</h1>
    </section>

    <section class="checkout-container">
        <div class="cart-left">
            <h2>Chi tiết đơn hàng</h2>
            <table>
                <thead>
                    <tr>
                        <th class="left-align">Sản phẩm</th>
                        <th class="right-align">Giá</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($id_donhang): ?>
                        <?php while ($row = mysqli_fetch_assoc($result_giohang)): ?>
                            <tr>
                                <td class="product-info">
                                    <img src="<?= htmlspecialchars($row['hinh_anh']) ?>" alt="<?= htmlspecialchars($row['ten_game']) ?>">
                                    <span><?= htmlspecialchars($row['ten_game']) ?></span>
                                </td>
                                <td class="product-price"><?= number_format($row['gia'], 0, ',', '.') ?> VNĐ</td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="2">Chưa có đơn hàng nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="cart-right">
            <h2>Thông tin thanh toán</h2>
            <p><strong>Trạng thái đơn hàng:</strong> <?= htmlspecialchars($trang_thai); ?></p>
            <p><strong>Tổng tiền:</strong> <?= number_format($tong_tien, 0, ',', '.'); ?> VNĐ</p>

            <?php if ($id_donhang && $trang_thai !== 'Đã thanh toán'): ?>
                <form id="paymentForm" action="process_payment.php" method="POST" onsubmit="return validatePayment()">
                    <input type="hidden" name="id_donhang" value="<?= $id_donhang; ?>">
                    <label>Chọn phương thức thanh toán:</label>
                    <div class="payment-methods">
                        <div class="payment-option" data-method="Chuyển khoản">
                            <img src="/BTLPHP/CHECKOUT/imgcheckout/chuyenkhoan.png" alt="Chuyển khoản">
                            <span>Chuyển khoản</span>
                        </div>
                        <div class="payment-option" data-method="Thẻ tín dụng">
                            <img src="/BTLPHP/CHECKOUT/imgcheckout/thecredit.png" alt="Thẻ tín dụng">
                            <span>Thẻ tín dụng</span>
                        </div>
                        <div class="payment-option" data-method="Ví điện tử">
                            <img src="/BTLPHP/CHECKOUT/imgcheckout/vidientu.png" alt="Ví điện tử">
                            <span>Ví điện tử</span>
                        </div>
                        <div class="payment-option" data-method="Stripe">
                            <img src="/BTLPHP/CHECKOUT/imgcheckout/stripe.png" alt="Stripe">
                            <span>Stripe</span>
                        </div>
                    </div>
                    <input type="hidden" name="payment_method" id="selected_payment" value="Chuyển khoản">

                    <!-- Nội dung hiển thị theo phương thức thanh toán -->
                    <div class="payment-info" id="bank-transfer" style="display: block;">
                        <h3>Thông tin chuyển khoản</h3>
                        <p>Ngân hàng: Vietcombank</p>
                        <p>Số tài khoản: 123456789</p>
                        <p>Chủ tài khoản: Nguyễn Văn Hạnh</p>
                        <p>Nội dung: Thanh toán đơn hàng #<?= $id_donhang; ?></p>
                    </div>

                    <div class="payment-info" id="credit-card" style="display: none;">
                        <h3>Nhập thông tin thẻ tín dụng</h3>
                        <label for="card-number">Số thẻ:</label>
                        <input type="text" id="card-number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                        <span class="error" id="card-number-error"></span>

                        <label for="expiry-date">Ngày hết hạn:</label>
                        <input type="text" id="expiry-date" name="expiry_date" placeholder="MM/YY" maxlength="5">
                        <span class="error" id="expiry-date-error"></span>

                        <label for="cvv">CVV:</label>
                        <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3">
                        <span class="error" id="cvv-error"></span>
                    </div>

                    <div class="payment-info" id="e-wallet" style="display: none;">
                        <h3>Quét mã QR để thanh toán</h3>
                        <img src="/BTLPHP/CHECKOUT/imgcheckout/qrvidientu.jpg" alt="QR Code">
                        <p>Hoặc chuyển khoản qua Ví Momo/ZaloPay theo số điện thoại: 0987654321</p>
                    </div>

                    <div class="payment-info" id="stripe" style="display: none;">
                        <h3>Thanh toán qua Stripe</h3>
                        <img src="/BTLPHP/CHECKOUT/imgcheckout/stripe.png" alt="Stripe">
                        <p>Nhấn "Xác nhận thanh toán" để chuyển hướng đến Stripe.</p>
                    </div>

                    <div class="loading" id="loading">
                        <img src="/BTLPHP/CHECKOUT/imgcheckout/loadinggif.gif" alt="Đang xử lý">
                        <p>Đang xử lý thanh toán...</p>
                    </div>

                    <button type="submit">Xác nhận thanh toán</button>
                    <button type="button" onclick="cancelPayment()">Hủy thanh toán</button>
                </form>
            <?php else: ?>
                <p>Đơn hàng đã được thanh toán hoặc không tồn tại.</p>
            <?php endif; ?>
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let paymentOptions = document.querySelectorAll(".payment-option");
            let selectedPayment = document.getElementById("selected_payment");
            let bankTransfer = document.getElementById("bank-transfer");
            let creditCard = document.getElementById("credit-card");
            let eWallet = document.getElementById("e-wallet");
            let stripe = document.getElementById("stripe");

            function hideAll() {
                bankTransfer.style.display = "none";
                creditCard.style.display = "none";
                eWallet.style.display = "none";
                stripe.style.display = "none";
            }

            paymentOptions.forEach(option => {
                option.addEventListener("click", function () {
                    paymentOptions.forEach(item => item.classList.remove("active"));
                    this.classList.add("active");
                    selectedPayment.value = this.getAttribute("data-method");

                    hideAll();
                    if (this.getAttribute("data-method") === "Chuyển khoản") {
                        bankTransfer.style.display = "block";
                    } else if (this.getAttribute("data-method") === "Thẻ tín dụng") {
                        creditCard.style.display = "block";
                    } else if (this.getAttribute("data-method") === "Ví điện tử") {
                        eWallet.style.display = "block";
                    } else if (this.getAttribute("data-method") === "Stripe") {
                        stripe.style.display = "block";
                    }
                });
            });
        });

        function validatePayment() {
            let method = document.getElementById("selected_payment").value;
            let isValid = true;

            if (method === "Thẻ tín dụng") {
                let cardNumber = document.getElementById("card-number").value.replace(/\s/g, '');
                let expiryDate = document.getElementById("expiry-date").value;
                let cvv = document.getElementById("cvv").value;

                // Reset error messages
                document.getElementById("card-number-error").textContent = "";
                document.getElementById("expiry-date-error").textContent = "";
                document.getElementById("cvv-error").textContent = "";

                // Validate card number (16 digits)
                if (!/^\d{16}$/.test(cardNumber)) {
                    document.getElementById("card-number-error").textContent = "Số thẻ phải có 16 chữ số!";
                    isValid = false;
                }

                // Validate expiry date (MM/YY)
                if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiryDate)) {
                    document.getElementById("expiry-date-error").textContent = "Ngày hết hạn không hợp lệ (MM/YY)!";
                    isValid = false;
                } else {
                    let [month, year] = expiryDate.split('/');
                    let currentYear = new Date().getFullYear() % 100;
                    let currentMonth = new Date().getMonth() + 1;
                    if (parseInt(year) < currentYear || (parseInt(year) === currentYear && parseInt(month) < currentMonth)) {
                        document.getElementById("expiry-date-error").textContent = "Thẻ đã hết hạn!";
                        isValid = false;
                    }
                }

                // Validate CVV (3 digits)
                if (!/^\d{3}$/.test(cvv)) {
                    document.getElementById("cvv-error").textContent = "CVV phải có 3 chữ số!";
                    isValid = false;
                }
            }

            if (isValid) {
                document.getElementById("loading").style.display = "block";
                setTimeout(() => {
                    document.getElementById("paymentForm").submit();
                }, 1000);
            }

            return false;
        }

        function cancelPayment() {
            if (confirm("Bạn có chắc chắn muốn hủy thanh toán?")) {
                window.location.href = "/BTLPHP/CART/Cart.php";
            }
        }

        document.getElementById("card-number")?.addEventListener("input", function (e) {
            let value = e.target.value.replace(/\D/g, "").substring(0, 16);
            e.target.value = value.replace(/(\d{4})/g, "$1 ").trim();
        });
    </script>
</body>
</html>

<?php
mysqli_stmt_close($stmt_donhang);
mysqli_stmt_close($stmt_giohang);
mysqli_close($conn);
?>