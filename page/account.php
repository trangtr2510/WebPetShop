<?php
session_start();
include('../config/connectDB.php');

// Check if user is logged in
if (!isset($_SESSION['ID_ND'])) {
    header("Location: login_register.php");
    exit();
}

$idND = $_SESSION['ID_ND']; // Lấy ID_ND từ session

// Truy vấn lấy MaKH từ ID_ND
$sql_getMaKH = "SELECT MaKH FROM khachhang WHERE ID_ND = ?";
$stmt_getMaKH = $conn->prepare($sql_getMaKH);
$stmt_getMaKH->bind_param("i", $idND);
$stmt_getMaKH->execute();
$result = $stmt_getMaKH->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $MaKH = $row['MaKH']; // Lấy MaKH từ kết quả truy vấn
} else {
    echo "<script>alert('Không tìm thấy thông tin khách hàng!');</script>";
    exit();
}
$stmt_getMaKH->close();

// Lấy thông tin khách hàng từ MaKH
$sql_getInfo = "SELECT * FROM khachhang WHERE MaKH = ?";
$stmt_getInfo = $conn->prepare($sql_getInfo);
$stmt_getInfo->bind_param("s", $MaKH);
$stmt_getInfo->execute();
$result = $stmt_getInfo->get_result();
$customer = $result->fetch_assoc();
$stmt_getInfo->close();

// Xử lý cập nhật thông tin
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_info'])) {
        $tenKH = $_POST['TenKH'];
        $sdt = $_POST['SDT'] == '' ? NULL : $_POST['SDT'];
        $email = $_POST['Email'];
        $diaChi = $_POST['DiaChi'] == '' ? NULL : $_POST['DiaChi'];
        $matKhau = $_POST['MatKhau'];
        
        // Xử lý upload ảnh
        $hinhAnh = $customer['HinhAnh']; // Giữ nguyên ảnh cũ nếu không upload ảnh mới
        
        if (isset($_FILES['HinhAnh']) && $_FILES['HinhAnh']['size'] > 0) {
            $target_dir = "../uploads/avatars/";
            // Tạo thư mục nếu chưa tồn tại
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES["HinhAnh"]["name"], PATHINFO_EXTENSION);
            $new_filename = $MaKH . "_" . time() . "." . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            // Upload file
            if (move_uploaded_file($_FILES["HinhAnh"]["tmp_name"], $target_file)) {
                $hinhAnh = $new_filename;
            } else {
                echo "<script>alert('Lỗi khi tải ảnh lên!');</script>";
            }
        }
        
        // Cập nhật thông tin trong cơ sở dữ liệu
        $sql_update = "UPDATE khachhang SET 
                      TenKH = ?, 
                      SDT = ?, 
                      Email = ?, 
                      DiaChi = ?, 
                      MatKhau = ?, 
                      HinhAnh = ? 
                      WHERE MaKH = ?";
        
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssssss", $tenKH, $sdt, $email, $diaChi, $matKhau, $hinhAnh, $MaKH);
        
        if ($stmt_update->execute()) {
            echo "<script>alert('Cập nhật thông tin thành công!');</script>";
            // Làm mới thông tin sau khi cập nhật
            $stmt_getInfo = $conn->prepare($sql_getInfo);
            $stmt_getInfo->bind_param("s", $MaKH);
            $stmt_getInfo->execute();
            $result = $stmt_getInfo->get_result();
            $customer = $result->fetch_assoc();
            $stmt_getInfo->close();
        } else {
            echo "<script>alert('Lỗi khi cập nhật thông tin: " . $stmt_update->error . "');</script>";
        }
        
        $stmt_update->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin tài khoản</title>
    <link rel="stylesheet" href="../fontawesome-free-6.4.2-web/css/all.min.css">
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/account.css">

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

    <div class="body_container">
        <!-- Background circles -->
        <div class="bg-circle-1"></div>
        <div class="bg-circle-2"></div>
        
        <div class="container">
            <div class="profile-icons">
                <a href="#"><i class="fa-solid fa-arrow-left"></i></a>
                <a href="#"><i class="fas fa-user"></i></a>
            </div>
            
            <!-- Profile card view -->
            <div class="profile-card">
                <div class="avatar-container">
                    <?php if (!empty($customer['HinhAnh'])): ?>
                        <img src="../uploads/avatars/<?php echo $customer['HinhAnh']; ?>" alt="Avatar">
                    <?php else: ?>
                        <i class="fas fa-user"></i>
                    <?php endif; ?>
                </div>
                
                <h2><?php echo $customer['TenKH']; ?></h2>
                <div class="location"><?php echo $customer['DiaChi']; ?></div>
                
                <div class="occupation">Web Pet Shop</div>
                <div class="education"><?php echo $customer['Email']; ?></div>
                
                <button type="button" id="showMoreBtn">Show more</button>
            </div>
            
            <!-- Edit profile form -->
            <form method="POST" action="" enctype="multipart/form-data" class="edit-form">
                <h1>Thông tin tài khoản</h1>
                
                <div class="avatar-upload">
                    <label for="fileInput">
                        <?php if (!empty($customer['HinhAnh'])): ?>
                            <img src="../uploads/avatars/<?php echo $customer['HinhAnh']; ?>" alt="Avatar">
                        <?php else: ?>
                            <i class="fas fa-user" style="font-size: 40px; color: #ccc;"></i>
                        <?php endif; ?>
                        <div class="overlay-text">Change</div>
                    </label>
                    <input type="file" id="fileInput" name="HinhAnh" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label for="TenKH">Họ và tên:</label>
                    <input type="text" id="TenKH" name="TenKH" value="<?php echo $customer['TenKH']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="SDT">Số điện thoại:</label>
                    <input type="text" id="SDT" name="SDT" value="<?php echo $customer['SDT']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="Email">Email:</label>
                    <input type="email" id="Email" name="Email" value="<?php echo $customer['Email']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="DiaChi">Địa chỉ:</label>
                    <textarea id="DiaChi" name="DiaChi" rows="3"><?php echo $customer['DiaChi']; ?></textarea>
                </div>
                
                <div class="form-group password-field">
                    <label for="MatKhau">Mật khẩu:</label>
                    <div class="password-container">
                        <input type="password" id="MatKhau" name="MatKhau" value="<?php echo $customer['MatKhau']; ?>" required>
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                </div>
                
                <button type="submit" name="update_info">Cập nhật thông tin</button>
            </form>
        </div>
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
        // Show the form when the "Show more" button is clicked
        document.getElementById('showMoreBtn').addEventListener('click', function() {
            document.querySelector('.profile-card').style.display = 'none';
            document.querySelector('.edit-form').style.display = 'block';
        });
        
        // Trigger file input when clicking on avatar in edit mode
        document.querySelector('.avatar-upload').addEventListener('click', function() {
            document.getElementById('fileInput').click();
        });
        
        // Show image preview when user selects a file
        document.getElementById('fileInput').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    var avatarContainer = document.querySelector('.avatar-upload label');
                    
                    // Remove icon if exists
                    if (avatarContainer.querySelector('i')) {
                        avatarContainer.querySelector('i').remove();
                    }
                    
                    // Update existing image or create new one
                    if (avatarContainer.querySelector('img')) {
                        avatarContainer.querySelector('img').src = e.target.result;
                    } else {
                        var img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = 'Avatar Preview';
                        avatarContainer.appendChild(img);
                    }
                }
                
                reader.readAsDataURL(e.target.files[0]);
            }
        });
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('MatKhau');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle the eye icon (between eye and eye-slash)
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        document.addEventListener('DOMContentLoaded', function() {
            const profileCard = document.querySelector('.profile-card');
            const editForm = document.querySelector('.edit-form');
            const backIcon = document.querySelector('.fa-arrow-left').parentElement;
            const showMoreBtn = document.getElementById('showMoreBtn');
            
            // Khởi tạo: Ẩn icon back khi đang ở profile-card (mặc định)
            backIcon.style.display = 'none';
            
            // Khi nhấn nút "Show more" để chuyển sang edit form
            showMoreBtn.addEventListener('click', function() {
                profileCard.style.display = 'none';
                editForm.style.display = 'block';
                backIcon.style.display = 'inline-block'; // Hiện icon back
            });
            
            // Khi nhấn icon back để quay lại profile-card
            backIcon.addEventListener('click', function(e) {
                e.preventDefault(); // Ngăn chặn hành vi mặc định của thẻ a
                editForm.style.display = 'none';
                profileCard.style.display = 'block';
                backIcon.style.display = 'none'; // Ẩn icon back
            });
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