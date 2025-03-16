<?php

include('../config/connectDB.php'); // Kết nối CSDL
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Truy vấn kiểm tra email
    $query = "SELECT * FROM NguoiDung WHERE Email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Lấy dữ liệu người dùng
        $user = $result->fetch_assoc();
        // Kiểm tra mật khẩu
        if ($password === $user['MatKhau']) { 
            $_SESSION['HoTen'] = $user['HoTen'];
            $_SESSION['Email'] = $user['Email'];
            $_SESSION['MatKhau'] = $user['MatKhau'];
            $_SESSION['ID_ND'] = $user['ID_ND'];
            $_SESSION['VaiTro'] = $user['VaiTro'];
            header("Location: index.php");
            // header("Location: login_register.php?message=Đăng nhập thành công!");
            exit();
        } else {
            header("Location: login_register.php?message=Sai mật khẩu!");
            exit();
        }
    } else {
        header("Location: login_register.php?message=Email không tồn tại!");
        exit();
    }
}
?>
