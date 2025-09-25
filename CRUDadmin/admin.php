<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            padding: 15px 0;
            text-align: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
        }
        .nav {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .nav li {
            margin: 0 15px;
        }
        .nav a {
            text-decoration: none;
            color: white;
            font-size: 18px;
            font-weight: bold;
            padding: 12px 18px;
            border-radius: 8px;
            transition: 0.3s ease-in-out;
            display: inline-block;
        }
        .nav a:hover, .nav a.active {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <header class="header">
        <ul class="nav">
            <li><a href="quanly_sanpham.php">Quản Lý Sản Phẩm</a></li>
            <li><a href="quanly_taikhoan.php">Quản Lý Tài Khoản</a></li>
            <li><a href="quanly_danhgia.php">Quản Lý Đánh Giá</a></li>
            <li><a href="quanly_blog.php">Quản Lý Blog</a></li>
            <li><a href="quanly_lienhe.php">Quản Lý Liên Hệ</a></li>
            <li><a href="baocaothongke.php">Báo cáo thống kê </a></li>
        </ul>
    </header>
</body>
</html>
