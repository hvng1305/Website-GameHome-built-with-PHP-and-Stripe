<?php
session_start();
include("../db_connect.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM baiviet WHERE id = $id";
    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $row['tieu_de']; ?></title>
    <link rel="stylesheet" href="Blogcss.css">
</head>
<body>

<?php include("../header.php"); ?>

<!-- Chi tiết bài viết -->
<section class="blog-detail">
    <h1><?php echo $row['tieu_de']; ?></h1>
    <p class="date">Ngày đăng: <?php echo date("d/m/Y", strtotime($row['ngay_dang'])); ?></p>
    
    <?php if (!empty($row['hinh_anh']) && filter_var($row['hinh_anh'], FILTER_VALIDATE_URL)) { ?>
        <img src="<?php echo $row['hinh_anh']; ?>" alt="<?php echo $row['tieu_de']; ?>" class="blog-image">
    <?php } else { ?>
        <img src="default.jpg" alt="Không có ảnh">
    <?php } ?>
    
    <p><?php echo nl2br($row['noi_dung']); ?></p>
</section>

</body>
</html>
<?php
    } else {
        echo "Bài viết không tồn tại!";
    }
} else {
    echo "Lỗi: Không có ID bài viết!";
}
?>
