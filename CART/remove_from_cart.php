<?php
session_start();
include("../db_connect.php");

if (!isset($_SESSION["id_khachhang"])) {
    header("Location: /BTLPHP/USER/User.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id_sanpham"])) {
    $id_khachhang = $_SESSION["id_khachhang"];
    $id_sanpham = intval($_POST["id_sanpham"]);

    $sql = "DELETE FROM giohang WHERE id_khachhang = ? AND id_sanpham = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_khachhang, $id_sanpham);
    $stmt->execute();
}

header("Location: Cart.php");
exit();
?>
