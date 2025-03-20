<?php
session_start();
include('../config/connectDB.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['ID_ND'])) {
    header("Location: login_register.php");
    exit();
}

// Lấy ID thú cưng từ URL
if (isset($_GET['id'])) {
    $petId = $_GET['id'];
    
    // Lấy thông tin thú cưng
    $sql = "SELECT * FROM thucung WHERE MaThuCung = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $petId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $pet = $result->fetch_assoc();
    } else {
        // Thú cưng không tồn tại
        header("Location: pets.php");
        exit();
    }
    $stmt->close();
    
    // Lấy thú cưng liên quan (cùng loại)
    $sql = "SELECT * FROM thucung WHERE Loai = ? AND MaThuCung != ? LIMIT 4";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $pet['Loai'], $petId);
    $stmt->execute();
    $relatedPets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    // Lấy danh sách đánh giá - Thay MaSP bằng MaThuCung
    $sql = "SELECT binhluan.*, khachhang.TenKH
            FROM binhluan
            JOIN khachhang ON binhluan.MaKH = khachhang.MaKH
            WHERE binhluan.MaThuCung = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $petId);
    $stmt->execute();
    $reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // Không có ID thú cưng, chuyển hướng về trang thú cưng
    header("Location: pets.php");
    exit();
}

// Xử lý thêm đánh giá
if (isset($_POST['submit_review'])) {
    // Kiểm tra session ID_ND (ID người dùng đăng nhập)
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
        $maKH = $row['MaKH']; // Lấy MaKH từ kết quả truy vấn
    } else {
        echo "<script>alert('Không tìm thấy thông tin khách hàng!');</script>";
        exit();
    }
    $stmt_getMaKH->close();

    // Lấy nội dung đánh giá từ form
    $noiDung = $_POST['review_content']; // Nội dung đánh giá
    $diemDanhGia = $_POST['rating']; // Số sao đánh giá
    $ngayBL = date("Y-m-d H:i:s"); // Ngày bình luận

    // Thêm vào bảng binhluan - Thay MaSP bằng MaThuCung
    $sql = "INSERT INTO binhluan (MaKH, MaThuCung, NoiDung, NgayBL, DiemDanhGia) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $maKH, $petId, $noiDung, $ngayBL, $diemDanhGia);

    if ($stmt->execute()) {
        echo "<script>alert('Đánh giá của bạn đã được gửi!');</script>";
    } else {
        echo "<script>alert('Lỗi khi gửi đánh giá!');</script>";
    }

    $stmt->close();
}

// Tính số lượng đánh giá
$reviewCount = count($reviews);

// Tính điểm đánh giá trung bình
$averageRating = 0;
if ($reviewCount > 0) {
    $totalRating = 0;
    foreach ($reviews as $review) {
        $totalRating += $review['DiemDanhGia'];
    }
    $averageRating = round($totalRating / $reviewCount, 1);
}

// Đóng kết nối database
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pet['TenThuCung']; ?> - Chi tiết thú cưng</title>
    <link rel="stylesheet" href="../fontawesome-free-6.4.2-web/css/all.min.css">
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/product_detail_style.css">
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

    <!-- Nội dung chính -->
    <div class="w-70">

        <!-- Chi tiết thú cưng -->
        <div class="product-detail">
            <div class="product-images">
                <img src="../petShopImages/Img/<?php echo $pet['HinhAnh']; ?>" alt="<?php echo $pet['TenThuCung']; ?>" class="main-image">
                <div class="thumbnail-images">
                    <img src="../petShopImages/Img/<?php echo $pet['HinhAnh']; ?>" alt="Thumbnail 1" class="thumbnail active">
                    <?php if (isset($pet['HinhAnhPhu1']) && !empty($pet['HinhAnhPhu1'])): ?>
                        <img src="../petShopImages/Img/<?php echo $pet['HinhAnhPhu1']; ?>" alt="Thumbnail 2" class="thumbnail">
                    <?php endif; ?>
                    <?php if (isset($pet['HinhAnhPhu2']) && !empty($pet['HinhAnhPhu2'])): ?>
                        <img src="../petShopImages/Img/<?php echo $pet['HinhAnhPhu2']; ?>" alt="Thumbnail 3" class="thumbnail">
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="product-info">
                <h1 class="product-title"><?php echo $pet['TenThuCung']; ?></h1>
                <div class="review">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fa-<?php echo ($i <= $averageRating) ? 'solid' : 'regular'; ?> fa-star"></i>
                    <?php endfor; ?>
                    <span>(<?php echo $reviewCount; ?> đánh giá)</span>
                </div>
                
                <div class="pet-details">
                    <p><strong>Loại:</strong> <?php echo $pet['Loai']; ?></p>
                    <p><strong>Giống:</strong> <?php echo $pet['Giong']; ?></p>
                    <p><strong>Tuổi:</strong> <?php echo $pet['Tuoi']; ?></p>
                    <p><strong>Giới tính:</strong> <?php echo $pet['GioiTinh']; ?></p>
                </div>
                
                <div class="product-price">
                    <?php echo number_format($pet['GiaBan'], 0); ?> VND
                </div>
                
                <div class="product-description">
                    <p><?php echo $pet['MoTa']; ?></p>
                </div>
                
                <form method="post" action="">
                    <div class="quantity-selector">
                        <label>Số lượng:</label>
                        <div class="quantity-btn decrease">-</div>
                        <input type="number" name="quantity" value="1" min="1" class="quantity-input">
                        <div class="quantity-btn increase">+</div>
                    </div>
                    
                    <button type="submit" name="add_to_cart" class="add-to-cart-btn">
                        <i class="fa-solid fa-cart-plus"></i> Thêm vào giỏ hàng
                    </button>
                </form>
                
                <div class="social-share">
                    <span>Chia sẻ:</span>
                    <div class="social-icons">
                        <div class="social-icon"><i class="fa-brands fa-facebook-f"></i></div>
                        <div class="social-icon"><i class="fa-brands fa-twitter"></i></div>
                        <div class="social-icon"><i class="fa-brands fa-pinterest-p"></i></div>
                        <div class="social-icon"><i class="fa-brands fa-instagram"></i></div>
                    </div>
                </div>
            </div>
        </div>

        
        <!-- Tabs thú cưng -->
        <div class="product-tabs">
            <div class="tab-buttons">
                <div class="tab-btn active" data-tab="description">Mô tả</div>
                <div class="tab-btn" data-tab="reviews">Đánh giá (<?php echo $reviewCount; ?>)</div>
            </div>
            
            <div class="tab-content">
                <div class="tab-pane active" id="description">
                    <p><?php echo $pet['MoTa']; ?></p>
                </div>
                
                <div class="tab-pane" id="reviews">
                    <div class="review-section">
                        <!-- Hiển thị đánh giá của người dùng -->
                        <div class="reviews">
                                <?php if (!empty($reviews)): ?>
                                    <?php foreach ($reviews as $review): ?>
                                        <div class="review-item">
                                            <strong><?php echo $review['TenKH']; ?></strong> - <?php echo date("d/m/Y H:i", strtotime($review['NgayBL'])); ?>
                                            <div class="rating">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fa<?php echo ($i <= $review['DiemDanhGia']) ? '-solid' : '-regular'; ?> fa-star"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <p><?php echo $review['NoiDung']; ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>Chưa có đánh giá nào.</p>
                                <?php endif; ?>
                        </div>

                        <!-- Form nhập đánh giá -->
                        <?php if (isset($_SESSION['ID_ND'])): ?>
                            <form method="post" action="" class="review-form">
                                <h3>Đánh giá thú cưng</h3>
                                <label>Chọn số sao:</label>
                                <select name="rating" required>
                                    <option value="5">★★★★★ - Tuyệt vời</option>
                                    <option value="4">★★★★☆ - Tốt</option>
                                    <option value="3">★★★☆☆ - Bình thường</option>
                                    <option value="2">★★☆☆☆ - Không tốt</option>
                                    <option value="1">★☆☆☆☆ - Rất tệ</option>
                                </select>
                                <label>Nhập đánh giá của bạn:</label>
                                <textarea name="review_content" required></textarea>
                                <button type="submit" name="submit_review">Gửi đánh giá</button>
                            </form>
                        <?php else: ?>
                            <p>Bạn cần <a href="login_register.php">đăng nhập</a> để đánh giá.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Thú cưng liên quan -->
        <div class="related-products">
            <h3>Thú cưng liên quan</h3>
            <div class="related-product-list">
                <?php foreach ($relatedPets as $related): ?>
                <div class="related-product-item">
                    <img src="../petShopImages/Img/<?php echo $related['HinhAnh']; ?>" alt="<?php echo $related['TenThuCung']; ?>" class="related-product-img">
                    <div class="related-product-info">
                        <h4 class="related-product-title"><?php echo $related['TenThuCung']; ?></h4>
                        <p class="related-pet-breed"><?php echo $related['Giong']; ?></p>
                        <div class="related-product-price"><?php echo number_format($related['GiaBan'], 0); ?>K VND</div>
                        <a href="pet-detail.php?id=<?php echo $related['MaThuCung']; ?>">
                            <button class="view-product-btn">Xem chi tiết</button>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
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
        // Script xử lý các tính năng của trang
        document.addEventListener('DOMContentLoaded', function() {
            // Xử lý chọn ảnh thumbnail
            const thumbnails = document.querySelectorAll('.thumbnail');
            const mainImage = document.querySelector('.main-image');
            
            thumbnails.forEach(thumbnail => {
                thumbnail.addEventListener('click', function() {
                    // Gỡ bỏ class active từ tất cả thumbnails
                    thumbnails.forEach(thumb => thumb.classList.remove('active'));
                    
                    // Thêm class active cho thumbnail được click
                    this.classList.add('active');
                    
                    // Cập nhật ảnh chính
                    mainImage.src = this.src;
                });
            });
            
            // Xử lý tăng giảm số lượng
            const decreaseBtn = document.querySelector('.decrease');
            const increaseBtn = document.querySelector('.increase');
            const quantityInput = document.querySelector('.quantity-input');
            
            decreaseBtn.addEventListener('click', function() {
                let value = parseInt(quantityInput.value);
                if (value > 1) {
                    quantityInput.value = value - 1;
                }
            });
            
            increaseBtn.addEventListener('click', function() {
                let value = parseInt(quantityInput.value);
                quantityInput.value = value + 1;
            });
            
            // Xử lý tabs
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Gỡ bỏ class active từ tất cả buttons và panes
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabPanes.forEach(pane => pane.classList.remove('active'));
                    
                    // Thêm class active cho button được click
                    this.classList.add('active');
                    
                    // Hiển thị tab-pane tương ứng
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });
            
            // Ẩn thông báo thành công sau 3 giây
            const successMessage = document.querySelector('.success-message');
            if (successMessage) {
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 3000);
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