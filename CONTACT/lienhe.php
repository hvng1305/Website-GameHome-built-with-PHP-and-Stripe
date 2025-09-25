<?php
session_start();
include("../db_connect.php");

// Kiểm tra nếu khách hàng chưa đăng nhập
if (!isset($_SESSION["id_khachhang"])) {
    die("Lỗi: Không tìm thấy ID khách hàng. Vui lòng đăng nhập lại.");
}

// Kiểm tra dữ liệu gửi đi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['subject'], $_POST['message'])) {
        $_SESSION['msg_status'] = "Lỗi: Vui lòng điền đầy đủ thông tin!";
        header("Location: contact.php");
        exit();
    }

    // Lấy dữ liệu từ form
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // Kiểm tra dữ liệu đầu vào
    if (empty($first_name) || empty($last_name) || empty($email) || empty($subject) || empty($message)) {
        $_SESSION['msg_status'] = "Lỗi: Vui lòng nhập đầy đủ thông tin!";
        header("Location: contact.php");
        exit();
    }

    // Tránh lỗi SQL Injection
    $sql = "INSERT INTO lienhe (first_name, last_name, email, so_dien_thoai, subject, tin_nhan) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $_SESSION['msg_status'] = "Lỗi khi chuẩn bị truy vấn: " . $conn->error;
        header("Location: contact.php");
        exit();
    }

    $stmt->bind_param("ssssss", $first_name, $last_name, $email, $phone, $subject, $message);

    if ($stmt->execute()) {
        $_SESSION['msg_status'] = "Gửi tin nhắn thành công!";
    } else {
        $_SESSION['msg_status'] = "Lỗi khi gửi tin nhắn: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    // Quay lại trang Contact
    header("Location: contact.php");
    exit();
}
?>
