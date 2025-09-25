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

// Biến lưu thông tin bài viết cần chỉnh sửa
$edit_id = $edit_tieu_de = $edit_noi_dung = $edit_id_tacgia = $edit_hinh_anh = "";

// Kiểm tra nếu có yêu cầu sửa
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $result = $conn->query("SELECT * FROM baiviet WHERE id = $id");
    if ($row = $result->fetch_assoc()) {
        $edit_id = $row['id'];
        $edit_tieu_de = $row['tieu_de'];
        $edit_noi_dung = $row['noi_dung'];
        $edit_id_tacgia = $row['id_tacgia'];
        $edit_hinh_anh = $row['hinh_anh'];
    }
}

// Lấy danh sách bài viết
$result = $conn->query("SELECT baiviet.*, taikhoan.ten_dang_nhap FROM baiviet JOIN taikhoan ON baiviet.id_tacgia = taikhoan.id ORDER BY ngay_dang DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Blog</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f4; padding: 20px; }
        h1 { color: #333; }
        table { width: 90%; margin: auto; border-collapse: collapse; background: white; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #007BFF; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        tr:hover { background-color: #ddd; }
        input, button, select, textarea { padding: 10px; margin: 5px; border: 1px solid #ccc; border-radius: 5px; width: 90%; }
        button { cursor: pointer; }
        .btn-add { background-color: #28a745; color: white; }
        .btn-update { background-color: #ffc107; color: black; }
        .btn-delete { background-color: #dc3545; color: white; }
        form { background: white; padding: 15px; width: 80%; border-radius: 8px; margin: auto; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .btn-cancel {
        display: inline-block;
        background-color: #6c757d;
        color: white;
        padding: 10px;
        text-decoration: none;
        border-radius: 5px;
        margin-top: 5px;
        text-align: center;
    }
    .btn-cancel:hover {
        background-color: #5a6268;
    }
    </style>
</head>
<body>

<h1>Quản Lý Blog</h1>

<p><a href="logout.php">Đăng xuất</a></p>

<!-- Form thêm/sửa bài viết -->
<!-- Form thêm/sửa bài viết -->
<form action="crud_blog.php" method="post">
    <h3><?= $edit_id ? 'Cập nhật bài viết' : 'Thêm bài viết' ?></h3>
    <input type="hidden" name="id" value="<?= $edit_id ?>">
    <input type="text" name="tieu_de" placeholder="Tiêu đề" value="<?= $edit_tieu_de ?>" required>
    <textarea name="noi_dung" placeholder="Nội dung" rows="4" required><?= $edit_noi_dung ?></textarea>
    <select name="id_tacgia" required>
        <option value="">Chọn tác giả</option>
        <?php
        $users = $conn->query("SELECT id, ten_dang_nhap FROM taikhoan WHERE loai_tai_khoan='admin'");
        while ($user = $users->fetch_assoc()) {
            $selected = ($user['id'] == $edit_id_tacgia) ? "selected" : "";
            echo "<option value='{$user['id']}' $selected>{$user['ten_dang_nhap']}</option>";
        }
        ?>
    </select>
    <input type="text" name="hinh_anh" placeholder="URL hình ảnh" value="<?= $edit_hinh_anh ?>">
    
    <button type="submit" name="<?= $edit_id ? 'update' : 'add' ?>" class="<?= $edit_id ? 'btn-update' : 'btn-add' ?>">
        <?= $edit_id ? 'Cập nhật' : 'Thêm' ?>
    </button>
    
    <?php if ($edit_id): ?>
        <button type="submit" name="delete" class="btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa?');">Xóa</button>
        <a href="quanly_blog.php" class="btn-cancel">Hủy</a>
    <?php endif; ?>
</form>

<!-- Danh sách bài viết -->
<table>
    <tr>
        <th>ID</th>
        <th>Tiêu đề</th>
        <th>Nội dung</th>
        <th>Tác giả</th>
        <th>Ngày đăng</th>
        <th>Hình ảnh</th>
        <th>Hành động</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row["id"] ?></td>
        <td><?= $row["tieu_de"] ?></td>
        <td><?= substr($row["noi_dung"], 0, 50) ?>...</td>
        <td><?= $row["ten_dang_nhap"] ?></td>
        <td><?= $row["ngay_dang"] ?></td>
        <td><img src="<?= $row["hinh_anh"] ?>" alt="Hình ảnh" width="50"></td>
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