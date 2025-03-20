<?php
session_start();
include('../config/connectDB.php');

if (!isset($_SESSION['ID_ND'])) {
    die("Lỗi: Session ID_ND không tồn tại. Vui lòng đăng nhập lại.");
}

// Thực hiện truy vấn để lấy tổng số sản phẩm
$count_query = "SELECT COUNT(*) as total FROM sanpham";
$count_result = mysqli_query($conn, $count_query);
$total_products = mysqli_fetch_assoc($count_result)['total'];

// Xử lý sắp xếp
$order_by = "MaSP ASC"; // Mặc định sắp xếp theo mã sản phẩm tăng dần

if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price-asc':
            $order_by = "GiaBan ASC";
            break;
        case 'price-desc':
            $order_by = "GiaBan DESC";
            break;
        case 'name-asc':
            $order_by = "TenSP ASC";
            break;
        case 'name-desc':
            $order_by = "TenSP DESC";
            break;
        case 'oldest':
            $order_by = "NgayThem ASC";
            break;
        case 'newest':
            $order_by = "NgayThem DESC";
            break;
    }
}

// Earlier in the file, modify the search and where_clause section:

// Xử lý tìm kiếm và lọc sản phẩm
$search = "";
$where_clause = "";
$loai_sp = "";

// Check for LoaiSP parameter
if (isset($_GET['LoaiSP']) && !empty($_GET['LoaiSP'])) {
    $loai_sp = mysqli_real_escape_string($conn, $_GET['LoaiSP']);
    $where_clause = "WHERE LoaiSP = '$loai_sp'";
}

// If there's also a search parameter, add it to the where clause
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    if (!empty($where_clause)) {
        // If we already have a where clause for LoaiSP, add the search with AND
        $where_clause .= " AND (TenSP LIKE '%$search%' OR LoaiSP LIKE '%$search%')";
    } else {
        // If no where clause yet, create it with just the search condition
        $where_clause = "WHERE TenSP LIKE '%$search%' OR LoaiSP LIKE '%$search%'";
    }
}

// Truy vấn để lấy sản phẩm (có thể bao gồm tìm kiếm và lọc loại sản phẩm)
$sql = "SELECT * FROM sanpham $where_clause ORDER BY $order_by";
$result_search = mysqli_query($conn, $sql);

// Kiểm tra lỗi truy vấn
if (!$result_search) {
    die("Lỗi truy vấn: " . mysqli_error($conn));
}

// Lấy tất cả sản phẩm
$products = [];
while ($row = mysqli_fetch_assoc($result_search)) {
    $products[] = $row;
}

// Đóng kết nối
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tất cả sản phẩm - Pet Shop</title>
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
            <!-- <div class="rel">
                <i class="fa-regular fa-heart"></i>
                <span class="num">3</span>
            </div> -->
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
            if (isset($_GET['LoaiSP']) && !empty($_GET['LoaiSP'])) {
                echo htmlspecialchars($_GET['LoaiSP']);
            } else {
                echo "Tất cả sản phẩm";
            }
            ?>
        </h1>
        
        <div class="product-filter">
            <div class="product-count">
                TỔNG SẢN PHẨM: <?php echo $total_products; ?>
            </div>
            
            <div class="product-sort">
                <select id="sort-select" onchange="sortProducts(this.value)">
                    <option value="newest" <?php if($order_by == "NgayThem DESC") echo "selected"; ?>>MỚI NHẤT</option>
                    <option value="price-asc" <?php if($order_by == "GiaBan ASC") echo "selected"; ?>>Giá: Tăng dần</option>
                    <option value="price-desc" <?php if($order_by == "GiaBan DESC") echo "selected"; ?>>Giá: Giảm dần</option>
                    <option value="name-asc" <?php if($order_by == "TenSP ASC") echo "selected"; ?>>Tên: A-Z</option>
                    <option value="name-desc" <?php if($order_by == "TenSP DESC") echo "selected"; ?>>Tên: Z-A</option>
                    <option value="oldest" <?php if($order_by == "NgayThem ASC") echo "selected"; ?>>Cũ nhất</option>
                    <!-- <option value="bestselling" >Bán chạy nhất</option> -->
                </select>
            </div>
        </div>
        
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
            <div class="product-item">
            
                <!-- Hình ảnh sản phẩm -->
                <div class="product-image">
                    <img src="../petShopImages/Img/<?php echo $product['HinhAnh']; ?>" alt="<?php echo $product['TenSP']; ?>">
                     <!-- Hiển thị giảm giá nếu có -->
                    <?php if (isset($product['GiamGia']) && $product['GiamGia'] > 0): ?>
                        <div class="tag">-<?php echo $product['GiamGia']; ?>%</div>
                    <?php endif; ?>
                    <div class="hidden_icons">
                        <!-- <i class="fa-regular fa-heart"></i> -->
                        <i class="fa-solid fa-eye"></i>
                    </div>
                </div>
                
                <!-- Loại sản phẩm -->
                <div class="product-category">
                    <?php echo $product['LoaiSP']; ?>
                </div>
                
                <!-- Tiêu đề sản phẩm -->
                <h3 class="product-title">
                    <a href="product_detail.php?id=<?php echo $product['MaSP']; ?>">
                        <?php echo $product['TenSP']; ?>
                    </a>
                </h3>
                
                <!-- Giá sản phẩm -->
                <div class="product-price">
                    <?php if(isset($product['GiaGoc']) && $product['GiaGoc'] > $product['GiaBan']): ?>
                        <span class="original-price"><?php echo number_format($product['GiaGoc'], 0, ',', '.'); ?> VND</span>
                    <?php endif; ?>
                    <span class="sale-price"><?php echo number_format($product['GiaBan'], 0, ',', '.'); ?> VND</span>
                </div>
                
                <!-- Nút thêm vào giỏ hàng -->
                <form method="POST" action="" name="add_to_cart">
                    <input type="hidden" name="MaSP" value="<?php echo $product['MaSP']; ?>">
                    <button type="submit" name="add_to_cart" class="add-to-cart-btn" data-product-id="<?php echo $product['MaSP']; ?>" >
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
            var currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('sort', sortValue);
            window.location.href = currentUrl.toString();
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
                    window.location.href = 'product.php';
                } else {
                    // Chuyển hướng với tham số tìm kiếm
                    window.location.href = `product.php?search=${encodeURIComponent(searchValue)}`;
                }
            }
        });

        // Xử lý sự kiện click vào icon "eye" (biểu tượng con mắt)
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.hidden_icons .fa-eye').forEach(icon => {
                icon.addEventListener('click', function() {
                    const productItem = this.closest('.product-item'); // Tìm phần tử cha phù hợp
                    const productId = productItem?.querySelector('button')?.getAttribute('data-product-id');
                    if (productId) window.location.href = `product_detail.php?id=${productId}`;
                });
            });
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