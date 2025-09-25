<?php
session_start();
include("../db_connect.php");

if (!isset($_SESSION["id_khachhang"])) {
    header("Location: /BTLPHP/USER/User.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id_sanpham"], $_POST["action"])) {
    $id_khachhang = $_SESSION["id_khachhang"];
    $id_sanpham = intval($_POST["id_sanpham"]);
    $action = $_POST["action"];

    if ($action === "increase") {
        $sql = "UPDATE giohang SET so_luong = so_luong + 1 WHERE id_khachhang = ? AND id_sanpham = ?";
    } elseif ($action === "decrease") {
        $sql = "UPDATE giohang SET so_luong = GREATEST(so_luong - 1, 1) WHERE id_khachhang = ? AND id_sanpham = ?";
    }

    if (isset($sql)) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_khachhang, $id_sanpham);
        $stmt->execute();
    }
}

header("Location: Cart.php");
exit();
?>
