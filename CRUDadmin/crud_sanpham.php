<?php
// Kết nối MySQL
include("../db_connect.php"); // File kết nối database

// Xử lý thêm, sửa, xóa sản phẩm
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add"])) {
        // Thêm sản phẩm
        $ten_game = $conn->real_escape_string($_POST["ten_game"]);
        $mo_ta = $conn->real_escape_string($_POST["mo_ta"]);
        $gia = $conn->real_escape_string($_POST["gia"]);
        $the_loai = $conn->real_escape_string($_POST["the_loai"]);
        $ngay_phat_hanh = $conn->real_escape_string($_POST["ngay_phat_hanh"]);
        $nha_phat_hanh = $conn->real_escape_string($_POST["nha_phat_hanh"]);
        $hinh_anh = $conn->real_escape_string($_POST["hinh_anh"]);

        // Xử lý upload file game vào cơ sở dữ liệu
        $file_game = null;
        if (!isset($_FILES["file_game"])) {
            die("Lỗi: Không nhận được dữ liệu file game từ form.");
        } elseif ($_FILES["file_game"]["error"] != 0) {
            switch ($_FILES["file_game"]["error"]) {
                case UPLOAD_ERR_NO_FILE:
                    die("Vui lòng chọn file game để upload.");
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    die("File game vượt quá kích thước cho phép.");
                default:
                    die("Lỗi upload file: " . $_FILES["file_game"]["error"]);
            }
        } else {
            $file_game = file_get_contents($_FILES["file_game"]["tmp_name"]);
            if ($file_game === false) {
                die("Không thể đọc nội dung file game.");
            }
        }

        // Sử dụng prepared statement để lưu dữ liệu nhị phân
        $stmt = $conn->prepare("INSERT INTO sanpham (ten_game, mo_ta, gia, the_loai, ngay_phat_hanh, nha_phat_hanh, hinh_anh, file_game) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsssss", $ten_game, $mo_ta, $gia, $the_loai, $ngay_phat_hanh, $nha_phat_hanh, $hinh_anh, $file_game);
        if ($stmt->execute()) {
            $stmt->close();
        } else {
            die("Lỗi khi lưu vào cơ sở dữ liệu: " . $stmt->error);
        }
    } elseif (isset($_POST["update"])) {
        // Sửa sản phẩm
        $id = $conn->real_escape_string($_POST["id"]);
        $ten_game = $conn->real_escape_string($_POST["ten_game"]);
        $mo_ta = $conn->real_escape_string($_POST["mo_ta"]);
        $gia = $conn->real_escape_string($_POST["gia"]);
        $the_loai = $conn->real_escape_string($_POST["the_loai"]);
        $ngay_phat_hanh = $conn->real_escape_string($_POST["ngay_phat_hanh"]);
        $nha_phat_hanh = $conn->real_escape_string($_POST["nha_phat_hanh"]);
        $hinh_anh = $conn->real_escape_string($_POST["hinh_anh"]);

        // Xử lý upload file game mới (nếu có)
        if (isset($_FILES["file_game"]) && $_FILES["file_game"]["error"] == 0) {
            $file_game = file_get_contents($_FILES["file_game"]["tmp_name"]);
            if ($file_game === false) {
                die("Không thể đọc nội dung file game.");
            }
            $stmt = $conn->prepare("UPDATE sanpham SET ten_game=?, mo_ta=?, gia=?, the_loai=?, ngay_phat_hanh=?, nha_phat_hanh=?, hinh_anh=?, file_game=? WHERE id=?");
            $stmt->bind_param("ssdsssssi", $ten_game, $mo_ta, $gia, $the_loai, $ngay_phat_hanh, $nha_phat_hanh, $hinh_anh, $file_game, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conn->prepare("UPDATE sanpham SET ten_game=?, mo_ta=?, gia=?, the_loai=?, ngay_phat_hanh=?, nha_phat_hanh=?, hinh_anh=? WHERE id=?");
            $stmt->bind_param("ssdssssi", $ten_game, $mo_ta, $gia, $the_loai, $ngay_phat_hanh, $nha_phat_hanh, $hinh_anh, $id);
            $stmt->execute();
            $stmt->close();
        }
    } elseif (isset($_POST["delete"])) {
        // Xóa sản phẩm
        $id = $conn->real_escape_string($_POST["id"]);
        $stmt = $conn->prepare("DELETE FROM sanpham WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Chuyển hướng về trang quản lý sản phẩm
header("Location: quanly_sanpham.php");
exit();
?>