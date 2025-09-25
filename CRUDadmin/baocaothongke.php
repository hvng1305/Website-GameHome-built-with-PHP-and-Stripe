<?php
include("../db_connect.php");
include("admin.php");

// Tổng doanh thu
$result = $conn->query("SELECT SUM(sanpham.gia) AS doanh_thu FROM lichsu_mua JOIN sanpham ON lichsu_mua.id_sanpham = sanpham.id");
$doanh_thu = $result->fetch_assoc()['doanh_thu'] ?? 0;

// Số đơn hàng đã thanh toán / chưa thanh toán
$result = $conn->query("SELECT trang_thai, COUNT(*) as so_don FROM thanhtoan GROUP BY trang_thai");
$don_hang = [];
while ($row = $result->fetch_assoc()) {
    $don_hang[$row['trang_thai']] = $row['so_don'];
}

// Sản phẩm bán chạy nhất
$result = $conn->query("SELECT sanpham.ten_game, COUNT(*) as so_luot_mua FROM lichsu_mua 
                        JOIN sanpham ON lichsu_mua.id_sanpham = sanpham.id 
                        GROUP BY sanpham.id ORDER BY so_luot_mua DESC LIMIT 5");
$sanpham_banchay = $result->fetch_all(MYSQLI_ASSOC);

// Khách hàng có nhiều lượt mua nhất
$result = $conn->query("SELECT taikhoan.ten_dang_nhap, COUNT(*) as so_luot_mua FROM lichsu_mua 
                        JOIN taikhoan ON lichsu_mua.id_khachhang = taikhoan.id 
                        GROUP BY taikhoan.id ORDER BY so_luot_mua DESC LIMIT 5");
$khach_hang = $result->fetch_all(MYSQLI_ASSOC);

// Phương thức thanh toán phổ biến
$result = $conn->query("SELECT phuong_thuc, COUNT(*) as so_luot FROM thanhtoan GROUP BY phuong_thuc ORDER BY so_luot DESC");
$thanh_toan = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo Cáo Thống Kê</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: #f4f4f4; 
            text-align: center; 
            padding: 20px; 
        }
        h1 { 
            color: #333; 
        }
        h2 { 
            color: #007BFF; 
            margin-top: 20px; 
            font-size: 20px; /* Giảm kích thước tiêu đề */
        }
        .chart-container { 
            width: 60%; /* Giảm từ 80% xuống 60% */
            margin: 15px auto; /* Giảm margin */
            background: white; 
            padding: 15px; /* Giảm padding */
            border-radius: 8px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
            max-height: 350px; /* Giới hạn chiều cao */
        }
        .doanh-thu { 
            font-size: 20px; /* Giảm từ 24px xuống 20px */
            color: #28a745; 
            margin: 15px 0; /* Giảm margin */
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Báo Cáo Thống Kê</h1>
    <div class="doanh-thu">Doanh thu: <?= number_format($doanh_thu, 0, ',', '.') ?> VND</div>
    
    <!-- Biểu đồ trạng thái đơn hàng -->
    <h2>Đơn hàng</h2>
    <div class="chart-container">
        <canvas id="donHangChart"></canvas>
    </div>

    <!-- Biểu đồ sản phẩm bán chạy -->
    <h2>Sản phẩm bán chạy nhất</h2>
    <div class="chart-container">
        <canvas id="sanPhamChart"></canvas>
    </div>

    <!-- Biểu đồ khách hàng mua nhiều -->
    <h2>Khách hàng mua nhiều nhất</h2>
    <div class="chart-container">
        <canvas id="khachHangChart"></canvas>
    </div>

    <!-- Biểu đồ phương thức thanh toán -->
    <h2>Phương thức thanh toán phổ biến</h2>
    <div class="chart-container">
        <canvas id="thanhToanChart"></canvas>
    </div>

    <script>
        // Dữ liệu cho biểu đồ đơn hàng (Pie Chart)
        const donHangData = {
            labels: <?= json_encode(array_keys($don_hang)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($don_hang)) ?>,
                backgroundColor: ['#007BFF', '#28a745', '#dc3545', '#ffc107'],
            }]
        };
        new Chart(document.getElementById('donHangChart'), {
            type: 'pie',
            data: donHangData,
            options: { 
                responsive: true, 
                maintainAspectRatio: false, /* Cho phép điều chỉnh tỷ lệ */
                plugins: { legend: { position: 'top', labels: { font: { size: 12 } } } } /* Giảm kích thước chữ chú thích */
            }
        });

        // Dữ liệu cho biểu đồ sản phẩm bán chạy (Bar Chart)
        const sanPhamData = {
            labels: <?= json_encode(array_column($sanpham_banchay, 'ten_game')) ?>,
            datasets: [{
                label: 'Số lượt mua',
                data: <?= json_encode(array_column($sanpham_banchay, 'so_luot_mua')) ?>,
                backgroundColor: '#007BFF',
            }]
        };
        new Chart(document.getElementById('sanPhamChart'), {
            type: 'bar',
            data: sanPhamData,
            options: { 
                responsive: true, 
                maintainAspectRatio: false, /* Cho phép điều chỉnh tỷ lệ */
                scales: { 
                    y: { beginAtZero: true, ticks: { font: { size: 12 } } } /* Giảm kích thước chữ trục y */
                },
                plugins: { legend: { display: false } }
            }
        });

        // Dữ liệu cho biểu đồ khách hàng mua nhiều (Bar Chart)
        const khachHangData = {
            labels: <?= json_encode(array_column($khach_hang, 'ten_dang_nhap')) ?>,
            datasets: [{
                label: 'Số lượt mua',
                data: <?= json_encode(array_column($khach_hang, 'so_luot_mua')) ?>,
                backgroundColor: '#28a745',
            }]
        };
        new Chart(document.getElementById('khachHangChart'), {
            type: 'bar',
            data: khachHangData,
            options: { 
                responsive: true, 
                maintainAspectRatio: false, /* Cho phép điều chỉnh tỷ lệ */
                scales: { 
                    y: { beginAtZero: true, ticks: { font: { size: 12 } } } /* Giảm kích thước chữ trục y */
                },
                plugins: { legend: { display: false } }
            }
        });

        // Dữ liệu cho biểu đồ phương thức thanh toán (Pie Chart)
        const thanhToanData = {
            labels: <?= json_encode(array_column($thanh_toan, 'phuong_thuc')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($thanh_toan, 'so_luot')) ?>,
                backgroundColor: ['#007BFF', '#28a745', '#dc3545', '#ffc107'],
            }]
        };
        new Chart(document.getElementById('thanhToanChart'), {
            type: 'pie',
            data: thanhToanData,
            options: { 
                responsive: true, 
                maintainAspectRatio: false, /* Cho phép điều chỉnh tỷ lệ */
                plugins: { legend: { position: 'top', labels: { font: { size: 12 } } } } /* Giảm kích thước chữ chú thích */
            }
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>