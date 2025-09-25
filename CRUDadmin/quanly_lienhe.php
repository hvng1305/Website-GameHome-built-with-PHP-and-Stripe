<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin'])) {
    header("Location: Adminlogin.php");
    exit();
}

// Kết nối MySQL
include("../db_connect.php"); // File kết nối database
include("admin.php");

// Lấy danh sách liên hệ
$result = $conn->query("SELECT * FROM lienhe");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Liên Hệ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th { background-color: #007BFF; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        tr:hover { background-color: #ddd; }
        button {
            padding: 8px 12px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            background-color: #dc3545;
            color: white;
            cursor: pointer;
        }
        button:hover { background-color: #c82333; }
        form {
            display: inline;
        }
    </style>
</head>
<body>

<h1>Quản Lý Liên Hệ</h1>

<p><a href="logout.php">Đăng xuất</a></p>

<!-- Danh sách liên hệ -->
<table>
    <tr>
        <th>ID</th>
        <th>Họ và Tên</th>
        <th>Email</th>
        <th>Số Điện Thoại</th>
        <th>Chủ Đề</th>
        <th>Tin Nhắn</th>
        <th>Ngày Gửi</th>
        <th>Hành động</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row["id"]) ?></td>
        <td><?= htmlspecialchars($row["first_name"] . " " . $row["last_name"]) ?></td>
        <td><?= htmlspecialchars($row["email"]) ?></td>
        <td><?= htmlspecialchars($row["so_dien_thoai"]) ?></td>
        <td><?= htmlspecialchars($row["subject"]) ?></td>
        <td><?= nl2br(htmlspecialchars($row["tin_nhan"])) ?></td>
        <td><?= htmlspecialchars($row["ngay_gui"]) ?></td>
        <td>
            <form action="crud_lienhe.php" method="post" onsubmit="return confirm('Bạn có chắc chắn muốn xóa liên hệ này?');">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <button type="submit" name="delete">Xóa</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>

<?php
$conn->close();
?>
