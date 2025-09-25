<?php
include("../db_connect.php"); // Kết nối database
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm Kiếm</title>
    <link rel="stylesheet" href="Searchcss.css">
</head>
<body>
<?php include("../header.php"); ?>

<!-- Section Tìm kiếm -->
<section class="search-header">
    <div class="breadcrumb">
        TRANG CHỦ / <span> TÌM KIẾM </span>
    </div>
    <h1>TÌM KIẾM</h1>
</section>

<section class="search-info">
    <form method="GET" action="search.php">
        <input type="text" name="keyword" class="search-box" placeholder="Nhập tên game, thể loại hoặc nhà phát hành..." value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>" required>
        <button type="submit" class="search-button">Tìm kiếm</button>
    </form>
</section>

<section class="search-results">
    <?php
    if (isset($_GET['keyword'])) {
        $keyword = htmlspecialchars($_GET['keyword']);
        $sql = "SELECT * FROM sanpham WHERE ten_game LIKE ? OR the_loai LIKE ? OR nha_phat_hanh LIKE ?";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%$keyword%";
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<div class="search-grid">';
            while ($row = $result->fetch_assoc()) {
                echo '<div class="search-card">
                        <img src="' . $row['hinh_anh'] . '" alt="' . htmlspecialchars($row['ten_game']) . '">
                        <div class="search-card-content">
                            <h3>' . htmlspecialchars($row['ten_game']) . '</h3>
                            <p class="publisher">Nhà phát hành: ' . htmlspecialchars($row['nha_phat_hanh']) . '</p>
                            <p class="description">' . substr(htmlspecialchars($row['mo_ta']), 0, 100) . '...</p>
                            <a href="../PRODUCT/product_detail.php?id=' . $row['id'] . '" class="view-more">Xem thêm</a>
                        </div>
                    </div>';
            }
            echo '</div>';
        } else {
            echo '<p class="no-results">Không tìm thấy kết quả nào.</p>';
        }
        $stmt->close();
    }
    ?>
</section>


</body>
</html>
