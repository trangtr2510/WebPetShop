<?php
session_start();
include('../config/connectDB.php');

// Lấy loại thú cưng từ URL nếu có
$loai_thucung = isset($_GET['Loai']) ? mysqli_real_escape_string($conn, $_GET['Loai']) : "";

// Thực hiện truy vấn để lấy tổng số thú cưng
$count_query = "SELECT COUNT(*) as total FROM thucung";
$count_result = mysqli_query($conn, $count_query);
$total_pets = mysqli_fetch_assoc($count_result)['total'];

// Xử lý sắp xếp
$order_by = "MaThuCung ASC"; // Mặc định sắp xếp theo mã thú cưng tăng dần

if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price-asc':
            $order_by = "GiaBan ASC";
            break;
        case 'price-desc':
            $order_by = "GiaBan DESC";
            break;
        case 'name-asc':
            $order_by = "TenThuCung ASC";
            break;
        case 'name-desc':
            $order_by = "TenThuCung DESC";
            break;
        case 'age-asc':
            $order_by = "Tuoi ASC";
            break;
        case 'age-desc':
            $order_by = "Tuoi DESC";
            break;
    }
}

// Truy vấn để lấy tất cả thú cưng với thứ tự sắp xếp
$sql = "SELECT * FROM thucung ORDER BY $order_by";
$result = mysqli_query($conn, $sql);

// Kiểm tra lỗi truy vấn
if (!$result) {
    die("Lỗi truy vấn: " . mysqli_error($conn));
}

// Lấy tất cả thú cưng
$pets = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pets[] = $row;
}

// Xử lý tìm kiếm thú cưng
$search = "";
$where_clause = "WHERE 1"; // Luôn đúng để dễ dàng thêm điều kiện

if (!empty($loai_thucung)) {
    $where_clause .= " AND Loai = '$loai_thucung'";
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_clause .= " AND (TenThuCung LIKE '%$search%' OR Giong LIKE '%$search%')";
}

// Truy vấn lấy thú cưng có điều kiện lọc
$sql = "SELECT * FROM thucung $where_clause ORDER BY $order_by";
$result = mysqli_query($conn, $sql);

$pets = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pets[] = $row;
}

// Đóng kết nối
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tất cả thú cưng - Pet Shop</title>
    <link rel="stylesheet" href="../fontawesome-free-6.4.2-web/css/all.min.css">
    <link rel="stylesheet" href="../style/product_style.css">
    <link rel="stylesheet" href="../style/style.css">
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
            <div class="container_search">
                <div class="icon">
                    <i class="search fa-solid fa-magnifying-glass" id='icon_search'></i>
                </div>
                <div class="input">
                    <input type="text" placeholder="Tìm kiếm" id="search" value="<?php echo htmlspecialchars($search); ?>">
                    <i class="clear fa-solid fa-xmark" id="clear"></i>
                </div>
            </div>
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
        <h1 class="page-title">
            <?php 
            if (isset($_GET['Loai']) && !empty($_GET['Loai'])) {
                echo htmlspecialchars($_GET['Loai']);
            } else {
                echo "Tất cả thú cưng";
            }
            ?>
        </h1>
        
        <div class="product-filter">
            <div class="product-count">
                TỔNG THÚ CƯNG: <?php echo $total_pets; ?>
            </div>
            
            <div class="product-sort">
                <select id="sort-select" onchange="sortProducts(this.value)">
                    <option value="name-asc" <?php if($order_by == "TenThuCung ASC") echo "selected"; ?>>Tên: A-Z</option>
                    <option value="name-desc" <?php if($order_by == "TenThuCung DESC") echo "selected"; ?>>Tên: Z-A</option>
                    <option value="price-asc" <?php if($order_by == "GiaBan ASC") echo "selected"; ?>>Giá: Tăng dần</option>
                    <option value="price-desc" <?php if($order_by == "GiaBan DESC") echo "selected"; ?>>Giá: Giảm dần</option>
                    <option value="age-asc" <?php if($order_by == "Tuoi ASC") echo "selected"; ?>>Tuổi: Tăng dần</option>
                    <option value="age-desc" <?php if($order_by == "Tuoi DESC") echo "selected"; ?>>Tuổi: Giảm dần</option>
                </select>
            </div>
        </div>
        
        <div class="product-grid">
            <?php foreach ($pets as $pet): ?>
            <div class="product-item">
                <!-- Hình ảnh thú cưng -->
                <div class="product-image">
                    <img src="../petShopImages/Img/<?php echo $pet['HinhAnh']; ?>" alt="<?php echo $pet['TenThuCung']; ?>">
                    <div class="hidden_icons">
                        <a href="pet-detail.php?id=<?php echo $pet['MaThuCung']; ?>" style = "color: #272727">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Loại thú cưng -->
                <div class="product-category">
                    <?php echo $pet['Loai']; ?> - <?php echo $pet['Giong']; ?>
                </div>
                
                <!-- Tên thú cưng -->
                <h3 class="product-title">
                    <a href="pet-detail.php?id=<?php echo $pet['MaThuCung']; ?>">
                        <?php echo $pet['TenThuCung']; ?>
                    </a>
                </h3>
                
                <!-- Thông tin thêm -->
                <div class="pet-info">
                    <span>Tuổi: <?php echo $pet['Tuoi']; ?></span> | 
                    <span>Giới tính: <?php echo $pet['GioiTinh']; ?></span>
                </div>
                
                <!-- Giá thú cưng -->
                <div class="product-price">
                    <span class="sale-price"><?php echo number_format($pet['GiaBan'], 0, ',', '.'); ?> VND</span>
                </div>

                <form method="POST" action="" name="add_to_cart">
                    <input type="hidden" name="MaThuCung" value="<?php echo $pet['MaThuCung']; ?>">
                    <button type="submit" name="add_to_cart" class="add-to-cart" data-pet-id="<?php echo $pet['MaThuCung']; ?>" >
                        Thêm vào giỏ hàng
                    </button>
                </form>
                
            </div>
            <?php endforeach; ?>
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
        function sortProducts(sortValue) {
            window.location.href = 'pet.php?sort=' + sortValue;
        }

        // headerheader
        window.onscroll = function() {myFunction()};
        var header = document.getElementById('myHeader');
        var sticky = header.offsetTop;

        function myFunction(){
            if(window.pageYOffset > sticky){
                header.classList.add('sticky');
            }
            else{
                header.classList.remove('sticky');
            }
        }

        // Search
        let search = document.getElementById('icon_search');
        let clear = document.getElementById('clear');

        search.onclick = function() {
            document.querySelector('.container_search').classList.toggle('active');
        }

        clear.onclick = function() {
            document.getElementById('search').value = "";
        }
        
        document.getElementById('search').addEventListener('keypress', function (event) {
            if (event.key === 'Enter') {
                let searchValue = this.value.trim();

                if (searchValue === '') {
                    // Nếu input rỗng, tải lại toàn bộ trang
                    window.location.href = 'pet.php';
                } else {
                    // Chuyển hướng với tham số tìm kiếm
                    window.location.href = `pet.php?search=${encodeURIComponent(searchValue)}`;
                }
            }
        });

        // Thêm code xử lý form submit qua AJAX
        document.addEventListener('DOMContentLoaded', function() {
            const addToCartForms = document.querySelectorAll('form[name="add_to_cart"]');
            
            addToCartForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // Ngăn form submit bình thường
                    
                    const formData = new FormData(this);
                    formData.append('add_to_cart', '1');
                    
                    fetch('add_to_cart.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Thêm vào giỏ hàng thành công!');
                        } else {
                            alert(data.message || 'Có lỗi xảy ra!');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi thêm vào giỏ hàng!');
                    });
                });
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