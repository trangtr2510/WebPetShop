<?php
session_start();
include('../config/connectDB.php');



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idND = $_SESSION['ID_ND'] ?? null;
    
    if (!$idND) {
        echo "<div class='alert alert-danger text-center'>Vui lòng đăng nhập để thanh toán</div>";
        echo "<script>setTimeout(function(){ window.location.href='login.php'; }, 2000);</script>";
        exit();
    }

    // Lấy dữ liệu từ form
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $payment_method = $_POST['payment_method'] ?? '';

    // Kiểm tra dữ liệu đầu vào
    if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($payment_method)) {
        $errorMsg = "<div class='alert alert-danger text-center'>Vui lòng điền đầy đủ thông tin</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "<div class='alert alert-danger text-center'>Email không hợp lệ</div>";
    } elseif (!preg_match("/^[0-9]{10,11}$/", $phone)) {
        $errorMsg = "<div class='alert alert-danger text-center'>Số điện thoại phải có 10-11 chữ số</div>";
    } else {
        try {
            $conn->begin_transaction();

            // Lấy MaKH từ ID_ND
            $stmt = $conn->prepare("SELECT MaKH FROM khachhang WHERE ID_ND = ?");
            $stmt->bind_param("i", $idND);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $MaKH = $row['MaKH'] ?? null;
            $stmt->close();

            if (!$MaKH) {
                throw new Exception("Không tìm thấy mã khách hàng");
            }

            // Tính tổng tiền giỏ hàng
            $TongTien = 0;
            if (isset($_SESSION['giohang']) && is_array($_SESSION['giohang'])) {
                foreach ($_SESSION['giohang'] as $item) {
                    if (isset($item['SoLuong'], $item['GiaBan'])) {
                        $TongTien += $item['SoLuong'] * $item['GiaBan'];
                    }
                }
            } else {
                $_SESSION['giohang'] = []; // Khởi tạo giỏ hàng nếu chưa có
            }
            

            // Tạo mã đơn hàng (10 ký tự)
            $MaDH = 'DH' . substr(uniqid(), -8);

            // Tạo đơn hàng mới
            $stmt = $conn->prepare("INSERT INTO donhang (MaDH, MaKH, TongTien, TrangThai) VALUES (?, ?, ?, 'Chờ xử lý')");
            $stmt->bind_param("ssd", $MaDH, $MaKH, $TongTien);
            $stmt->execute();
            $stmt->close();

            // Thêm chi tiết đơn hàng
            $stmt = $conn->prepare("INSERT INTO chitietdonhang (MaCTDH, MaDH, MaSP, MaThuCung, SoLuong, ThanhTien) VALUES (?, ?, ?, ?, ?, ?)");
            if (isset($_SESSION['giohang']) && is_array($_SESSION['giohang'])) {
                foreach ($_SESSION['giohang'] as $item) {
                    if (isset($item['MaSP'], $item['SoLuong'], $item['GiaBan'], $MaThuCung)) {
                        $MaCTDH = 'CT' . substr(uniqid(), -8);
                        $ThanhTien = $item['SoLuong'] * $item['GiaBan'];
                        $stmt->bind_param("ssssid", $MaCTDH, $MaDH, $item['MaSP'], $MaThuCung, $item['SoLuong'], $ThanhTien);
                        $stmt->execute();
                    }
                }
            }
            
            $stmt->close();

            // Thêm thông tin thanh toán
            $stmt = $conn->prepare("INSERT INTO thanhtoan (MaTT, MaDH, HinhThuc, TrangThai) VALUES (?, ?, ?, ?)");
            $MaTT = 'TT' . substr(uniqid(), -8);
            $TrangThai = 'Chưa thanh toán';
            $stmt->bind_param("ssss", $MaTT, $MaDH, $HinhThuc, $TrangThai);
            $stmt->execute();
            $stmt->close();

            // XÓA GIỎ HÀNG
            // 1. Xóa giỏ hàng trong database
            $stmt = $conn->prepare("DELETE FROM giohang WHERE MaKH = ?");
            $stmt->bind_param("s", $MaKH);
            $stmt->execute();
            $stmt->close();

            // 2. Xóa giỏ hàng trong session
            unset($_SESSION['giohang']);
            
            $conn->commit();

            echo "<script>
                alert('Thanh toán thành công! Bạn sẽ được chuyển về trang chủ.');
                window.location.href = 'index.php';
            </script>";
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $errorMsg = "<div class='alert alert-danger text-center'>Lỗi đặt hàng: " . $e->getMessage() . "</div>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f3f3f3, #e0e0e0);
            font-family: 'Arial', sans-serif;
        }
        .checkout-container {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease-in-out;
        }
        .checkout-container:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }
        .form-label {
            font-weight: bold;
            color: #333;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ccc;
            padding: 10px;
        }
        .btn-primary {
            background: #28a745;
            border: none;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            transition: 0.3s;
        }
        .btn-primary:hover {
            background: #218838;
            transform: scale(1.05);
        }
        h2 {
            color: #222;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .error-message {
            color: red;
            font-size: 13px;
            display: none;
            margin-top: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="checkout-container">
        <h2>🛒 Thanh toán</h2>
        
        <?php if (isset($errorMsg)) echo $errorMsg; ?>

        <form id="checkout-form" method="post">
            <div class="mb-3">
                <label class="form-label">Họ và tên:</label>
                <input type="text" name="name" id="name" class="form-control" required>
                <div id="name-error" class="error-message">Tên không hợp lệ</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" id="email" class="form-control" required>
                <div id="email-error" class="error-message">Email không hợp lệ</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Số điện thoại:</label>
                <input type="text" name="phone" id="phone" class="form-control" required>
                <div id="phone-error" class="error-message">Số điện thoại phải có 10-11 chữ số</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Địa chỉ:</label>
                <textarea name="address" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Phương thức thanh toán:</label>
                <select name="payment_method" class="form-select" id="payment_method" required>
                    <option value="Tiền mặt">Tiền mặt</option>
                    <option value="Chuyển khoản">Chuyển khoản</option>
                </select>
            </div>

            <div id="qr_payment" style="display: none; text-align: center;">
                <p>Vui lòng quét mã QR để thanh toán:</p>
                <img src="../petShopImages/Img/qr_code.png" alt="QR Code" style="width: 150px; height: 150px;">
                <p><strong>STK:</strong> 000123456789 - Ngân hàng TP Bank</p>
                <p><strong>Tên chủ tài khoản:</strong> Cửa Hàng Thú Cưng</p>
            </div>

            <script>
                document.getElementById("payment_method").addEventListener("change", function() {
                    var qrDiv = document.getElementById("qr_payment");
                    if (this.value === "Chuyển khoản") {
                        qrDiv.style.display = "block";
                    } else {
                        qrDiv.style.display = "none";
                    }
                });
            </script>


            <button type="submit" class="btn btn-primary w-100">✅ Xác nhận thanh toán</button>
        </form>
    </div>
</div>

<script>
    document.getElementById("checkout-form").addEventListener("input", function(event) {
        let inputField = event.target;
        let errorField = document.getElementById(inputField.id + "-error");

        if (!errorField || inputField.value.trim() === "") {
            if (errorField) errorField.style.display = "none";
            return;
        }

        switch (inputField.id) {
            case "name":
                errorField.style.display = /^[\p{L} ]+$/u.test(inputField.value) ? "none" : "block";
                break;
            case "email":
                errorField.style.display = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(inputField.value) ? "none" : "block";
                break;
            case "phone":
                errorField.style.display = /^[0-9]{10,11}$/.test(inputField.value) ? "none" : "block";
                break;
        }
    });
</script>

</body>
</html>