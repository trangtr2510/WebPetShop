<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; 

session_start();
include('../config/connectDB.php');

if (!isset($_SESSION['ID_ND'])) {
    header("Location: login_register.php");
    exit();
}

// Xử lý khi người dùng gửi form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $message = $conn->real_escape_string($_POST['message']);

    // Lưu vào cơ sở dữ liệu
    $sql = "INSERT INTO customer_contacts (name, email, phone, message, created_at) 
            VALUES ('$name', '$email', '$phone', '$message', NOW())";

    if ($conn->query($sql) === TRUE) {
        // Gửi email bằng PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Cấu hình SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // SMTP Server của Gmail
            $mail->SMTPAuth   = true;
            $mail->Username   = 'changcherchou@gmail.com'; // Email 
            $mail->Password   = 'upknoiodhfilswjy'; //  mật khẩu ứng dụng
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Cấu hình email gửi
            $mail->setFrom($email, $name); // Email người gửi từ form
            $mail->addAddress('changcherchou@gmail.com', 'Admin PetPro'); // Email nhận
            $mail->addReplyTo($email, $name); // Trả lời đến email người gửi

            // Nội dung email
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->Subject = "Liên hệ mới từ khách hàng - $name";
            $mail->Body    = "
                <h3>Thông tin khách hàng:</h3>
                <p><strong>Họ và tên:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Số điện thoại:</strong> $phone</p>
                <p><strong>Nội dung:</strong> $message</p>
                <br>
                <p>Vui lòng kiểm tra và phản hồi sớm nhất có thể.</p>
            ";

            // Gửi email
            if ($mail->send()) {
                $success_message = "Cám ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm nhất có thể.";
            } else {
                $error_message = "Email không thể gửi. Vui lòng thử lại.";
            }
        } catch (Exception $e) {
            $error_message = "Gửi email thất bại: {$mail->ErrorInfo}";
        }
    } else {
        $error_message = "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ - PetPro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../fontawesome-free-6.4.2-web/css/all.min.css">
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/contact_style.css">
</head>
<body>
    <!-- Header -->
    <header id="myHeader">
        <div class="logo">
            <img src="../petShopImages/Img/logo.webp" alt="" onclick="window.location.href='index.php'"
            style = 'cursor: pointer;'>
        </div>
        <div class="menu">
            <ul>
                <li><a href="#" onclick="window.location.href='index.php'">Home</a></li>
                <li><a href="#" onclick="window.location.href='pet.php'">Pet Shop</a></li>
                <li><a href="#" onclick="window.location.href= 'product.php?LoaiSP=Phụ kiện'">Pet Accessories</a></li>
                <li><a href="#" onclick="window.location.href= 'product.php?LoaiSP=Thức ăn thú cưng'">Pet Food</a></li>
                <li><a href="#" onclick="window.location.href='contact.php'">Contact</a></li>
                <li><a href="#" onclick="window.location.href='blog.php'">Blog</a></li>
                <?php if (isset($_SESSION['VaiTro']) && $_SESSION['VaiTro'] == 'Admin'): ?>
                    <li><a href="#" onclick="window.location.href='admin.php'">Admin</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="header_right">
            <!-- <div class="rel">
                <i class="fa-regular fa-heart"></i>
                <span class="num">3</span>
            </div> -->
            <i class="fa-solid fa-circle-user" onclick="window.location.href='account.php'" title = "Account"></i>
            <i class="fa-solid fa-right-from-bracket" onclick="window.location.href='logout.php'" title = "Đăng xuất"></i>
            <?php if (isset($_SESSION['VaiTro']) && $_SESSION['VaiTro'] == 'Customer'): ?>
                <div class="rel">
                    <a href="#" onclick="window.location.href='cart.php'"><i class="fa-solid fa-cart-plus"></i></a>
                    <span class="num">11</span>
                    <span class="total">11</span>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <div class="container">
        <div class="contact-info">
            <h1>Thông tin liên hệ</h1>
            
            <div class="info-item">
                <div class="icon-container">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="info-content">
                    <h3>Địa chỉ</h3>
                    <p>283/50 Cách Mạng Tháng 8, P.12, Q.10, TP HCM</p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="icon-container">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <div class="info-content">
                    <h3>Điện thoại</h3>
                    <p>+84 901636896</p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="icon-container">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="info-content">
                    <h3>Thời gian làm việc</h3>
                    <p>Thứ 2 đến Thứ 7: từ 9h đến 18h</p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="icon-container">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="info-content">
                    <h3>Email</h3>
                    <p>info@petpro.vn</p>
                </div>
            </div>
        </div>
        
        <div class="contact-form">
            <h2>Gửi thắc mắc cho chúng tôi</h2>
            <p style="margin-bottom: 20px;">Nếu bạn có thắc mắc gì, có thể gửi yêu cầu cho chúng tôi, và chúng tôi sẽ liên lạc lại với bạn sớm nhất có thể.</p>
            
            <?php if(isset($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <form id="contactForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <input type="text" id="name" name="name" placeholder="Tên của bạn" required>
                </div>
                
                <div class="form-group">
                    <input type="email" id="email" name="email" placeholder="Email của bạn" required>
                </div>
                
                <div class="form-group">
                    <input type="tel" id="phone" name="phone" placeholder="Số điện thoại của bạn" required>
                </div>
                
                <div class="form-group">
                    <textarea id="message" name="message" placeholder="Nội dung" required></textarea>
                </div>
                
                <div class="privacy-notice">
                Trang web này được bảo vệ bởi reCAPTCHA và 
                    <a href="https://policies.google.com/privacy" target="_blank">Chính sách Bảo mật của Google</a> cùng với 
                    <a href="https://policies.google.com/terms" target="_blank">Điều khoản Dịch vụ</a> áp dụng.
                </div>
                
                <button type="submit" class="submit-btn">Gửi cho chúng tôi</button>
            </form>
        </div>
    </div>
    
    <div class="map-container">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4241674197876!2d106.66408937483384!3d10.77646358929867!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f1b7c3ed289%3A0xa06651894598e488!2zMjgzIEPDoWNoIE3huqFuZyBUaMOhbmcgVMOhbSwgUGjGsOG7nW5nIDEyLCBRdeG6rW4gMTAsIFRow6BuaCBwaOG7kSBI4buTIENow60gTWluaCwgVmlldG5hbQ!5e0!3m2!1sen!2s!4v1709967020370!5m2!1sen!2s"
            width="100%"
            height="100%"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>

     <!-- Footer -->
     <footer id="footer">
        <div class="footer_top w-70">
            <div class="img">
                <img src="../petShopImages/Img/logo.webp" alt="">
            </div>
            <div class="menu">
                <ul>
                    <li><a href="#">My Account</a></li>
                    <li><a href="#">View Cart</a></li>
                    <li><a href="#">My Wishlist</a></li>
                    <li><a href="#">Order Status</a></li>
                </ul>
            </div>
            <div class="icons">
                <i class="fa-brands fa-facebook-f"></i>
                <i class="fa-brands fa-twitter"></i>
                <i class="fa-brands fa-google-plus-g"></i>
                <i class="fa-brands fa-vimeo-v"></i>
                <i class="fa-brands fa-youtube"></i>
                <i class="fa-brands fa-pinterest-p"></i>
            </div>
        </div>

        <div class="footer_bottom">
            <div class="payment_link">
                <i class="fa-brands fa-amazon-pay"></i>
                <i class="fa-brands fa-cc-visa"></i>
                <i class="fa-brands fa-paypal"></i>
                <i class="fa-brands fa-cc-mastercard"></i>
                <i class="fa-brands fa-cc-paypal"></i>
                <i class="fa-brands fa-cc-apple-pay"></i>
            </div>
            <small>Disigned By Group 8</small>
        </div>
    </footer>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('contactForm');
            
            form.addEventListener('submit', function(event) {
                const name = document.getElementById('name').value.trim();
                const email = document.getElementById('email').value.trim();
                const phone = document.getElementById('phone').value.trim();
                const message = document.getElementById('message').value.trim();
                
                let isValid = true;
                
                // Simple validation
                if (name === '') {
                    isValid = false;
                    showError('name', 'Vui lòng nhập tên của bạn');
                } else {
                    clearError('name');
                }
                
                if (email === '') {
                    isValid = false;
                    showError('email', 'Vui lòng nhập email của bạn');
                } else if (!isValidEmail(email)) {
                    isValid = false;
                    showError('email', 'Vui lòng nhập email hợp lệ');
                } else {
                    clearError('email');
                }
                
                if (phone === '') {
                    isValid = false;
                    showError('phone', 'Vui lòng nhập số điện thoại của bạn');
                } else if (!isValidPhone(phone)) {
                    isValid = false;
                    showError('phone', 'Vui lòng nhập số điện thoại hợp lệ');
                } else {
                    clearError('phone');
                }
                
                if (message === '') {
                    isValid = false;
                    showError('message', 'Vui lòng nhập nội dung');
                } else {
                    clearError('message');
                }
                
                if (!isValid) {
                    event.preventDefault();
                }
            });
            
            function showError(fieldId, message) {
                const field = document.getElementById(fieldId);
                
                // Create error element if it doesn't exist
                let errorElement = field.nextElementSibling;
                if (!errorElement || !errorElement.classList.contains('error-message')) {
                    errorElement = document.createElement('div');
                    errorElement.className = 'error-message';
                    errorElement.style.color = 'red';
                    errorElement.style.fontSize = '0.85rem';
                    errorElement.style.marginTop = '5px';
                    field.parentNode.insertBefore(errorElement, field.nextSibling);
                }
                
                errorElement.textContent = message;
                field.style.borderColor = 'red';
            }
            
            function clearError(fieldId) {
                const field = document.getElementById(fieldId);
                const errorElement = field.nextElementSibling;
                
                if (errorElement && errorElement.classList.contains('error-message')) {
                    errorElement.remove();
                }
                
                field.style.borderColor = '';
            }
            
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }
            
            function isValidPhone(phone) {
                const phoneRegex = /^[0-9+\-\s]{7,15}$/;
                return phoneRegex.test(phone);
            }
        });

        function updateCartInfo() {
            fetch("total_cart.php")
                .then(response => response.json())
                .then(data => {
                    document.querySelector(".num").textContent = data.count;
                    document.querySelector(".total").textContent = data.total;
                })
                .catch(error => console.error("Lỗi khi lấy dữ liệu giỏ hàng:", error));
        }

        // Gọi hàm ngay khi trang tải và cập nhật sau mỗi 10 giây
        updateCartInfo();
    </script>
</body>
</html>