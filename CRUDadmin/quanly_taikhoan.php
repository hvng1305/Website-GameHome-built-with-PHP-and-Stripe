<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin'])) {
    header("Location: Adminlogin.php");
    exit();
}

// Kết nối MySQL
include("../db_connect.php"); // File kết nối database
include("admin.php"); // Giữ nguyên header điều hướng

// Xử lý tìm kiếm
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $search_term = "%" . $conn->real_escape_string($search_query) . "%";
    $sql = "SELECT * FROM taikhoan WHERE ten_dang_nhap LIKE '$search_term' OR email LIKE '$search_term' OR so_dien_thoai LIKE '$search_term' OR dia_chi LIKE '$search_term'";
} else {
    $sql = "SELECT * FROM taikhoan";
}

// Lấy danh sách tài khoản
$result = $conn->query($sql);

// Lấy dữ liệu tài khoản cần chỉnh sửa nếu có
$edit_id = "";
$edit_data = ["ten_dang_nhap" => "", "email" => "", "so_dien_thoai" => "", "dia_chi" => "", "loai_tai_khoan" => "khachhang"];

if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $query = $conn->query("SELECT * FROM taikhoan WHERE id='$edit_id'");
    if ($query->num_rows > 0) {
        $edit_data = $query->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Tài Khoản</title>
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
        input, button, select {
            padding: 10px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
        .btn-delete:hover { background-color: #c82333; }
        .btn-edit {
            background-color: #ffc107;
            color: black;
        }
        .btn-edit:hover { background-color: #e0a800; }
        form {
            margin: 20px auto;
            background: white;
            padding: 15px;
            width: 80%;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        /* CSS cho form tìm kiếm */
        .search-form {
            margin: 20px auto;
            width: 50%;
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .search-form input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #007BFF;
            transition: border-color 0.3s;
        }
        .search-form input[type="text"]:focus {
            border-color: #0056b3;
            outline: none;
        }
        .search-form button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            transition: background-color 0.3s;
        }
        .search-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<h1>Quản Lý Tài Khoản</h1>
<p><a href="logout.php">Đăng xuất</a></p>

<!-- Form tìm kiếm -->
<form action="" method="get" class="search-form">
    <input type="text" name="search" placeholder="Tìm kiếm tài khoản" value="<?= htmlspecialchars($search_query) ?>">
    <button type="submit">Tìm kiếm</button>
</form>

<form action="crud_taikhoan.php" method="post">
    <h3><?= $edit_id ? "Chỉnh sửa tài khoản" : "Thêm tài khoản" ?></h3>
    <input type="hidden" name="id" value="<?= $edit_id ?>">
    <input type="text" name="ten_dang_nhap" placeholder="Tên đăng nhập" value="<?= $edit_data['ten_dang_nhap'] ?>" required>
    <input type="password" name="mat_khau" placeholder="Mật khẩu (nếu sửa thì nhập mới)">
    <input type="email" name="email" placeholder="Email" value="<?= $edit_data['email'] ?>" required>
    <input type="text" name="so_dien_thoai" placeholder="Số điện thoại" value="<?= $edit_data['so_dien_thoai'] ?>">
    <input type="text" name="dia_chi" placeholder="Địa chỉ" value="<?= $edit_data['dia_chi'] ?>">
    <select name="loai_tai_khoan">
        <option value="admin" <?= $edit_data['loai_tai_khoan'] == 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="khachhang" <?= $edit_data['loai_tai_khoan'] == 'khachhang' ? 'selected' : '' ?>>Khách hàng</option>
    </select>
    <button type="submit" name="<?= $edit_id ? "update" : "add" ?>">
        <?= $edit_id ? "Cập nhật" : "Thêm" ?>
    </button>
    <?php if ($edit_id): ?>
        <a href="quanly_taikhoan.php">Hủy</a>
    <?php endif; ?>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>Tên đăng nhập</th>
        <th>Email</th>
        <th>Số điện thoại</th>
        <th>Địa chỉ</th>
        <th>Loại tài khoản</th>
        <th>Ngày tạo</th>
        <th>Hành động</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row["id"] ?></td>
        <td><?= $row["ten_dang_nhap"] ?></td>
        <td><?= $row["email"] ?></td>
        <td><?= $row["so_dien_thoai"] ?></td>
        <td><?= $row["dia_chi"] ?></td>
        <td><?= $row["loai_tai_khoan"] ?></td>
        <td><?= $row["ngay_tao"] ?></td>
        <td>
            <a href="?edit=<?= $row['id'] ?>" class="btn-edit">Sửa</a>
            <form action="crud_taikhoan.php" method="post" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?');">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <button type="submit" name="delete" class="btn-delete">Xóa</button>
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