<?php
include("../db_connect.php");

if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM lienhe WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Xóa liên hệ thành công!'); window.location.href='quanly_lienhe.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi xóa liên hệ!'); window.location.href='quanly_lienhe.php';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
