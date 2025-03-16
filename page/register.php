<?php
session_start();
include('../config/connectDB.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Mặc định vai trò là "Customer"
    $role = "Customer";

    // Kiểm tra xem email đã tồn tại chưa
    $checkEmail = $conn->prepare("SELECT * FROM NguoiDung WHERE Email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if ($result->num_rows > 0) {
        header("Location: login_register.php?message=Email đã tồn tại!");
        exit();
    } else {
        // Chèn người dùng mới vào database với vai trò "Customer"
        $stmt = $conn->prepare("INSERT INTO NguoiDung (HoTen, Email, MatKhau, VaiTro) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $role);

        if ($stmt->execute()) {
            header("Location: login_register.php?message=Đăng ký thành công! Hãy đăng nhập.");
            exit();
        } else {
            header("Location: login_register.php?message=Lỗi khi đăng ký!");
            exit();
        }
    }
}
?>
