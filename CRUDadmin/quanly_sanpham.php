<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin'])) {
    header("Location: Adminlogin.php");
    exit();
}

// Kết nối MySQL
include("../db_connect.php");
include("admin.php");

// Biến lưu thông tin sản phẩm cần chỉnh sửa
$edit_id = $edit_ten = $edit_mo_ta = $edit_gia = $edit_the_loai = $edit_ngay_phat_hanh = $edit_nha_phat_hanh = $edit_hinh_anh = $edit_file_game = "";

// Xử lý tìm kiếm
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $search_term = "%" . $conn->real_escape_string($search_query) . "%";
    $sql = "SELECT * FROM sanpham WHERE ten_game LIKE '$search_term' OR mo_ta LIKE '$search_term' OR the_loai LIKE '$search_term' OR nha_phat_hanh LIKE '$search_term'";
} else {
    $sql = "SELECT * FROM sanpham";
}

// Kiểm tra nếu có yêu cầu sửa
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $result = $conn->query("SELECT * FROM sanpham WHERE id = $id");
    if ($row = $result->fetch_assoc()) {
        $edit_id = $row['id'];
        $edit_ten = $row['ten_game'];
        $edit_mo_ta = $row['mo_ta'];
        $edit_gia = $row['gia'];
        $edit_the_loai = $row['the_loai'];
        $edit_ngay_phat_hanh = $row['ngay_phat_hanh'];
        $edit_nha_phat_hanh = $row['nha_phat_hanh'];
        $edit_hinh_anh = $row['hinh_anh'];
        $edit_file_game = $row['file_game'] ? "Đã có file" : "Chưa có file"; // Chỉ hiển thị trạng thái
    }
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sản Phẩm</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #333;
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
        th {
            background-color: #007BFF;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        input, button, select {
            padding: 10px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            cursor: pointer;
        }
        .btn-add {
            background-color: #28a745;
            color: white;
        }
        .btn-add:hover {
            background-color: #218838;
        }
        .btn-update {
            background-color: #ffc107;
            color: black;
        }
        .btn-update:hover {
            background-color: #e0a800;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
        form {
            margin: 20px auto;
            background: white;
            padding: 15px;
            width: 80%;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .btn-cancel {
            background-color: #6c757d;
            color: white;
        }
        .btn-cancel:hover {
            background-color: #5a6268;
        }
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

<h1>Quản Lý Sản Phẩm</h1>

<p><a href="logout.php">Đăng xuất</a></p>

<!-- Form tìm kiếm -->
<form action="" method="get" class="search-form">
    <input type="text" name="search" placeholder="Tìm kiếm sản phẩm" value="<?= htmlspecialchars($search_query) ?>">
    <button type="submit">Tìm kiếm</button>
</form>

<!-- Form chung cho thêm, sửa, xóa -->
<form action="crud_sanpham.php" method="post" enctype="multipart/form-data">
    <h3><?= $edit_id ? 'Cập nhật sản phẩm' : 'Thêm sản phẩm' ?></h3>
    <input type="hidden" name="id" value="<?= $edit_id ?>">
    <input type="text" name="ten_game" placeholder="Tên game" value="<?= $edit_ten ?>" required>
    <input type="text" name="mo_ta" placeholder="Mô tả" value="<?= $edit_mo_ta ?>">
    <input type="number" step="0.01" name="gia" placeholder="Giá" value="<?= $edit_gia ?>" required>
    <input type="text" name="the_loai" placeholder="Thể loại" value="<?= $edit_the_loai ?>">
    <input type="date" name="ngay_phat_hanh" value="<?= $edit_ngay_phat_hanh ?>">
    <input type="text" name="nha_phat_hanh" placeholder="Nhà phát hành" value="<?= $edit_nha_phat_hanh ?>">
    <input type="text" name="hinh_anh" placeholder="URL hình ảnh" value="<?= $edit_hinh_anh ?>">
    <input type="file" name="file_game" <?= !$edit_id ? 'required' : '' ?>>
    <?php if ($edit_file_game): ?>
        <p>File hiện tại: <?= $edit_file_game ?></p>
    <?php endif; ?>
    
    <button type="submit" name="<?= $edit_id ? 'update' : 'add' ?>" class="<?= $edit_id ? 'btn-update' : 'btn-add' ?>">
        <?= $edit_id ? 'Cập nhật' : 'Thêm' ?>
    </button>

    <?php if ($edit_id): ?>
        <button type="submit" name="delete" class="btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">Xóa</button>
        <button type="button" class="btn-cancel" onclick="window.location.href='quanly_sanpham.php'">Hủy</button>
    <?php endif; ?>
</form>

<!-- Danh sách sản phẩm -->
<table>
    <tr>
        <th>ID</th>
        <th>Tên game</th>
        <th>Mô tả</th>
        <th>Giá</th>
        <th>Thể loại</th>
        <th>Ngày phát hành</th>
        <th>Nhà phát hành</th>
        <th>Hình ảnh</th>
        <th>File game</th>
        <th>Hành động</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row["id"] ?></td>
        <td><?= $row["ten_game"] ?></td>
        <td><?= $row["mo_ta"] ?></td>
        <td><?= $row["gia"] ?></td>
        <td><?= $row["the_loai"] ?></td>
        <td><?= $row["ngay_phat_hanh"] ?></td>
        <td><?= $row["nha_phat_hanh"] ?></td>
        <td><img src="<?= $row["hinh_anh"] ?>" alt="Hình ảnh" width="50"></td>
        <td>
            <a href="download.php?id=<?= $row['id'] ?>" class="btn-download">Tải xuống</a>
        </td>
        <td>
            <form action="" method="post">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <button type="submit" name="edit" class="btn-update">Chỉnh sửa</button>
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