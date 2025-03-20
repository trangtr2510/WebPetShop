<?php
session_start();
include('../config/connectDB.php');
if (!isset($_SESSION['ID_ND'])) {
    header("Location: login_register.php");
    exit();
}

// Hàm lấy sản phẩm theo danh sách MaSP
function getProducts($conn, $productIds) {
    $placeholders = implode(',', array_fill(0, count($productIds), '?')); // Tạo danh sách dấu ?
    $sql = "SELECT * FROM sanpham WHERE MaSP IN ($placeholders)";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param(str_repeat('s', count($productIds)), ...$productIds);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $products;
    } else {
        die("Lỗi truy vấn: " . $conn->error);
    }
}

// Hàm lấy danh sách đánh giá theo danh sách sản phẩm
function getReviews($conn, $productIds) {
    if (empty($productIds)) return []; // Tránh lỗi truy vấn nếu danh sách rỗng

    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $sql = "SELECT binhluan.*, khachhang.TenKH
            FROM binhluan
            JOIN khachhang ON binhluan.MaKH = khachhang.MaKH
            WHERE binhluan.MaSP IN ($placeholders)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Lỗi truy vấn đánh giá: " . $conn->error);
    }

    $stmt->bind_param(str_repeat('i', count($productIds)), ...$productIds);
    $stmt->execute();
    $reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $reviews;
}

// Lấy dữ liệu sản phẩm theo nhóm
$products = getProducts($conn, ['SP001', 'SP002']);
$feature_products = getProducts($conn, ['SP003', 'SP004', 'SP005', 'SP006']);
$latest_products = getProducts($conn, ['SP007', 'SP008', 'SP009', 'SP010', 'SP011', 'SP012']);

$reviews = getReviews($conn, $products);
$reviews_feature = getReviews($conn, $feature_products);
$reviews_latest = getReviews($conn, $latest_products );

$reviewCount = count($reviews);
$averageRating = 0;

if ($reviewCount > 0) {
    $totalRating = array_sum(array_column($reviews, 'DiemDanhGia'));
    $averageRating = round($totalRating / $reviewCount, 1);
}

$reviewCount_feature = count($reviews_feature);
$averageRating_feature = 0;

if ($reviewCount_feature > 0) {
    $totalRating = array_sum(array_column($reviews_feature, 'DiemDanhGia')); // Fixed
    $averageRating_feature = round($totalRating / $reviewCount_feature, 1);
}

$reviewCount_latest = count($reviews_latest);
$averageRating_latest = 0;

if ($reviewCount_latest > 0) {
    $totalRating = array_sum(array_column($reviews_latest, 'DiemDanhGia')); // Fixed
    $averageRating_latest = round($totalRating / $reviewCount_latest, 1);
}

// Đóng kết nối database
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Shop</title>
    <link rel="stylesheet" href="../fontawesome-free-6.4.2-web/css/all.min.css">
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
    
    <!-- Banner -->
    <div class="banner">
       <div class="banner_txt">
           <p>Bio-Organic 30% OFF</p>
           <h1>We Care About Your Pet!</h1>
       </div>
    </div>

    <!-- Facilities -->
    <div class="facility w-70">
        <div class="f_item">
            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 288 288"><path 
                style="fill:#57aa5e"><animate repeatCount="indefinite" attributeName="d" dur="5s" values="M37.5,
                186c-12.1-10.5-11.8-32.3-7.2-46.7c4.8-15,13.1-17.8,30.1-36.7C91,68.8,83.5,56.7,103.4,45 c22.2-13.1,51.1-9.5,
                69.6-1.6c18.1,7.8,15.7,15.3,43.3,33.2c28.8,18.8,37.2,14.3,46.7,27.9c15.6,22.3,6.4,53.3,4.4,60.2 c-3.3,11.2-7.1,
                23.9-18.5,32c-16.3,11.5-29.5,0.7-48.6,11c-16.2,8.7-12.6,19.7-28.2,33.2c-22.7,19.7-63.8,25.7-79.9,9.7 c-15.2-15.1,
                0.3-41.7-16.6-54.9C63,186,49.7,196.7,37.5,186z;M51,171.3c-6.1-17.7-15.3-17.2-20.7-32c-8-21.9,0.7-54.6,
                20.7-67.1c19.5-12.3,32.8,5.5,67.7-3.4C145.2,62,145,49.9,173,43.4 c12-2.8,41.4-9.6,60.2,6.6c19,16.4,16.7,
                47.5,16,57.7c-1.7,22.8-10.3,25.5-9.4,46.4c1,22.5,11.2,25.8,9.1,42.6 c-2.2,17.6-16.3,37.5-33.5,40.8c-22,
                4.1-29.4-22.4-54.9-22.6c-31-0.2-40.8,39-68.3,35.7c-17.3-2-32.2-19.8-37.3-34.8 C48.9,198.6,57.8,191,51,171.3z;
                M37.5,186c-12.1-10.5-11.8-32.3-7.2-46.7c4.8-15,13.1-17.8,30.1-36.7C91,68.8,83.5,56.7,103.4,45 c22.2-13.1,
                51.1-9.5,69.6-1.6c18.1,7.8,15.7,15.3,43.3,33.2c28.8,18.8,37.2,14.3,46.7,27.9c15.6,22.3,6.4,53.3,4.4,60.2 
                c-3.3,11.2-7.1,23.9-18.5,32c-16.3,11.5-29.5,0.7-48.6,11c-16.2,8.7-12.6,19.7-28.2,33.2c-22.7,19.7-63.8,25.7-79.9,
                9.7 c-15.2-15.1,0.3-41.7-16.6-54.9C63,186,49.7,196.7,37.5,186z"></animate></path></svg>
            <i class="fa-solid fa-bag-shopping"></i>
            <h3>Order Today <br> save 10%</h3>
        </div>

        <div class="f_item">
            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 288 288"><path 
                style="fill:#ffd70a"><animate repeatCount="indefinite" attributeName="d" dur="5s" values="M37.5,
                186c-12.1-10.5-11.8-32.3-7.2-46.7c4.8-15,13.1-17.8,30.1-36.7C91,68.8,83.5,56.7,103.4,45 c22.2-13.1,51.1-9.5,
                69.6-1.6c18.1,7.8,15.7,15.3,43.3,33.2c28.8,18.8,37.2,14.3,46.7,27.9c15.6,22.3,6.4,53.3,4.4,60.2 c-3.3,11.2-7.1,
                23.9-18.5,32c-16.3,11.5-29.5,0.7-48.6,11c-16.2,8.7-12.6,19.7-28.2,33.2c-22.7,19.7-63.8,25.7-79.9,9.7 c-15.2-15.1,
                0.3-41.7-16.6-54.9C63,186,49.7,196.7,37.5,186z;M51,171.3c-6.1-17.7-15.3-17.2-20.7-32c-8-21.9,0.7-54.6,
                20.7-67.1c19.5-12.3,32.8,5.5,67.7-3.4C145.2,62,145,49.9,173,43.4 c12-2.8,41.4-9.6,60.2,6.6c19,16.4,16.7,
                47.5,16,57.7c-1.7,22.8-10.3,25.5-9.4,46.4c1,22.5,11.2,25.8,9.1,42.6 c-2.2,17.6-16.3,37.5-33.5,40.8c-22,
                4.1-29.4-22.4-54.9-22.6c-31-0.2-40.8,39-68.3,35.7c-17.3-2-32.2-19.8-37.3-34.8 C48.9,198.6,57.8,191,51,171.3z;
                M37.5,186c-12.1-10.5-11.8-32.3-7.2-46.7c4.8-15,13.1-17.8,30.1-36.7C91,68.8,83.5,56.7,103.4,45 c22.2-13.1,
                51.1-9.5,69.6-1.6c18.1,7.8,15.7,15.3,43.3,33.2c28.8,18.8,37.2,14.3,46.7,27.9c15.6,22.3,6.4,53.3,4.4,60.2 
                c-3.3,11.2-7.1,23.9-18.5,32c-16.3,11.5-29.5,0.7-48.6,11c-16.2,8.7-12.6,19.7-28.2,33.2c-22.7,19.7-63.8,25.7-79.9,
                9.7 c-15.2-15.1,0.3-41.7-16.6-54.9C63,186,49.7,196.7,37.5,186z"></animate></path></svg>
            <i class="fa-solid fa-circle-arrow-left"></i>
            <h3>Moneyback<br> gurantee</h3>
        </div>

        <div class="f_item">
            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 288 288"><path 
                style="fill:#c2835d"><animate repeatCount="indefinite" attributeName="d" dur="5s" values="M37.5,
                186c-12.1-10.5-11.8-32.3-7.2-46.7c4.8-15,13.1-17.8,30.1-36.7C91,68.8,83.5,56.7,103.4,45 c22.2-13.1,51.1-9.5,
                69.6-1.6c18.1,7.8,15.7,15.3,43.3,33.2c28.8,18.8,37.2,14.3,46.7,27.9c15.6,22.3,6.4,53.3,4.4,60.2 c-3.3,11.2-7.1,
                23.9-18.5,32c-16.3,11.5-29.5,0.7-48.6,11c-16.2,8.7-12.6,19.7-28.2,33.2c-22.7,19.7-63.8,25.7-79.9,9.7 c-15.2-15.1,
                0.3-41.7-16.6-54.9C63,186,49.7,196.7,37.5,186z;M51,171.3c-6.1-17.7-15.3-17.2-20.7-32c-8-21.9,0.7-54.6,
                20.7-67.1c19.5-12.3,32.8,5.5,67.7-3.4C145.2,62,145,49.9,173,43.4 c12-2.8,41.4-9.6,60.2,6.6c19,16.4,16.7,
                47.5,16,57.7c-1.7,22.8-10.3,25.5-9.4,46.4c1,22.5,11.2,25.8,9.1,42.6 c-2.2,17.6-16.3,37.5-33.5,40.8c-22,
                4.1-29.4-22.4-54.9-22.6c-31-0.2-40.8,39-68.3,35.7c-17.3-2-32.2-19.8-37.3-34.8 C48.9,198.6,57.8,191,51,171.3z;
                M37.5,186c-12.1-10.5-11.8-32.3-7.2-46.7c4.8-15,13.1-17.8,30.1-36.7C91,68.8,83.5,56.7,103.4,45 c22.2-13.1,
                51.1-9.5,69.6-1.6c18.1,7.8,15.7,15.3,43.3,33.2c28.8,18.8,37.2,14.3,46.7,27.9c15.6,22.3,6.4,53.3,4.4,60.2 
                c-3.3,11.2-7.1,23.9-18.5,32c-16.3,11.5-29.5,0.7-48.6,11c-16.2,8.7-12.6,19.7-28.2,33.2c-22.7,19.7-63.8,25.7-79.9,
                9.7 c-15.2-15.1,0.3-41.7-16.6-54.9C63,186,49.7,196.7,37.5,186z"></animate></path></svg>
            <i class="fa-solid fa-headset"></i>
            <h3>24/7 customer<br> support</h3>
        </div>
    </div>

    <!-- Category -->
    <section id="shop_category" class="w-70">
        <div class="shop_category">
            <div class="heading">
                <h3>Shop By Category</h3>
                <div class="paw">
                    <i class="fa-solid fa-paw"></i>
                </div>
            </div>
            <div class="food">
                <?php foreach ($products as $product): ?>
                <div class="food_items">
                    <div class="img">
                        <img src="../petShopImages/Img/<?php echo $product['HinhAnh']; ?>" alt="<?php echo $product['TenSP']; ?>">
                    </div>
                    <div class="food_details">
                        <div class="review">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fa-<?php echo ($i <= $averageRating) ? 'solid' : 'regular'; ?> fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <h4 style = "cursor: pointer;" onclick="window.location.href='product_detail.php?id=<?php echo $product['MaSP']; ?>'"><?php echo $product['TenSP']; ?></h4>
                        <h3><?php echo number_format($product['GiaBan'], 2); ?> VND</h3>
                        <p><?php echo $product['MoTa']; ?></p>
                        <form method="POST" action="" name="add_to_cart">
                            <input type="hidden" name="MaSP" value="<?php echo $product['MaSP']; ?>">
                            <button type="submit" name="add_to_cart" class="add-to-cart-btn" data-product-id="<?php echo $product['MaSP']; ?>" >
                                Thêm vào giỏ hàng
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


    <!-- Category (2) -->
    <section id="shop_category" class="w-70">
        <div class="shop_category">
            <div class="heading">
                <h3>Shop By Category</h3>
                <div class="paw">
                    <i class="fa-solid fa-paw"></i>
                </div>
            </div>
            <div class="pet_list">
                <div class="single_pet">
                    <div class="img">
                        <img src="../petShopImages/Img/dog.webp" alt="">
                        <!-- svg -->
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 288 288"><path 
                            style="fill:#f6f6f6"><animate repeatCount="indefinite" attributeName="d" dur="5s" 
                            values="M37.5,186c-12.1-10.5-11.8-32.3-7.2-46.7c4.8-15,13.1-17.8,30.1-36.7C91,68.8,
                            83.5,56.7,103.4,45 c22.2-13.1,51.1-9.5,69.6-1.6c18.1,7.8,15.7,15.3,43.3,33.2c28.8,
                            18.8,37.2,14.3,46.7,27.9c15.6,22.3,6.4,53.3,4.4,60.2 c-3.3,11.2-7.1,23.9-18.5,
                            32c-16.3,11.5-29.5,0.7-48.6,11c-16.2,8.7-12.6,19.7-28.2,33.2c-22.7,19.7-63.8,
                            25.7-79.9,9.7 c-15.2-15.1,0.3-41.7-16.6-54.9C63,186,49.7,196.7,37.5,186z; M51,
                            171.3c-6.1-17.7-15.3-17.2-20.7-32c-8-21.9,0.7-54.6,20.7-67.1c19.5-12.3,32.8,5.5,
                            67.7-3.4C145.2,62,145,49.9,173,43.4 c12-2.8,41.4-9.6,60.2,6.6c19,16.4,16.7,47.5,
                            16,57.7c-1.7,22.8-10.3,25.5-9.4,46.4c1,22.5,11.2,25.8,9.1,42.6 c-2.2,17.6-16.3,
                            37.5-33.5,40.8c-22,4.1-29.4-22.4-54.9-22.6c-31-0.2-40.8,39-68.3,
                            35.7c-17.3-2-32.2-19.8-37.3-34.8 C48.9,198.6,57.8,191,51,171.3z; M37.5,
                            186c-12.1-10.5-11.8-32.3-7.2-46.7c4.8-15,13.1-17.8,30.1-36.7C91,68.8,83.5,56.7,
                            103.4,45 c22.2-13.1,51.1-9.5,69.6-1.6c18.1,7.8,15.7,15.3,43.3,33.2c28.8,18.8,
                            37.2,14.3,46.7,27.9c15.6,22.3,6.4,53.3,4.4,60.2 c-3.3,11.2-7.1,23.9-18.5,32c-16.3,
                            11.5-29.5,0.7-48.6,11c-16.2,8.7-12.6,19.7-28.2,33.2c-22.7,19.7-63.8,25.7-79.9,
                            9.7 c-15.2-15.1,0.3-41.7-16.6-54.9C63,186,49.7,196.7,37.5,186z"></animate></path></svg>
                    </div>
                    <h4><a href="pet.php?Loai=Chó">all for dogs</a></h4>
                </div>

                <div class="single_pet">
                    <div class="img">
                        <img src="../petShopImages/Img/cat.webp" alt="">
                        <!-- svg -->
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 288 288"><path 
                            style="fill:#f6f6f6"><animate repeatCount="indefinite" attributeName="d" dur="5s" 
                            values="M37.5,186c-12.1-10.5-11.8-32.3-7.2-46.7c4.8-15,13.1-17.8,30.1-36.7C91,68.8,
                            83.5,56.7,103.4,45 c22.2-13.1,51.1-9.5,69.6-1.6c18.1,7.8,15.7,15.3,43.3,33.2c28.8,
                            18.8,37.2,14.3,46.7,27.9c15.6,22.3,6.4,53.3,4.4,60.2 c-3.3,11.2-7.1,23.9-18.5,
                            32c-16.3,11.5-29.5,0.7-48.6,11c-16.2,8.7-12.6,19.7-28.2,33.2c-22.7,19.7-63.8,
                            25.7-79.9,9.7 c-15.2-15.1,0.3-41.7-16.6-54.9C63,186,49.7,196.7,37.5,186z; M51,
                            171.3c-6.1-17.7-15.3-17.2-20.7-32c-8-21.9,0.7-54.6,20.7-67.1c19.5-12.3,32.8,5.5,
                            67.7-3.4C145.2,62,145,49.9,173,43.4 c12-2.8,41.4-9.6,60.2,6.6c19,16.4,16.7,47.5,
                            16,57.7c-1.7,22.8-10.3,25.5-9.4,46.4c1,22.5,11.2,25.8,9.1,42.6 c-2.2,17.6-16.3,
                            37.5-33.5,40.8c-22,4.1-29.4-22.4-54.9-22.6c-31-0.2-40.8,39-68.3,
                            35.7c-17.3-2-32.2-19.8-37.3-34.8 C48.9,198.6,57.8,191,51,171.3z; M37.5,
                            186c-12.1-10.5-11.8-32.3-7.2-46.7c4.8-15,13.1-17.8,30.1-36.7C91,68.8,83.5,56.7,
                            103.4,45 c22.2-13.1,51.1-9.5,69.6-1.6c18.1,7.8,15.7,15.3,43.3,33.2c28.8,18.8,
                            37.2,14.3,46.7,27.9c15.6,22.3,6.4,53.3,4.4,60.2 c-3.3,11.2-7.1,23.9-18.5,32c-16.3,
                            11.5-29.5,0.7-48.6,11c-16.2,8.7-12.6,19.7-28.2,33.2c-22.7,19.7-63.8,25.7-79.9,
                            9.7 c-15.2-15.1,0.3-41.7-16.6-54.9C63,186,49.7,196.7,37.5,186z"></animate></path></svg>
                    </div>
                    <h4><a href="pet.php?Loai=Mèo">all for cats</a></h4>
                </div>

                <div class="single_pet">
                    <div class="img">
                        <img src="../petShopImages/Img/bird.webp" alt="">
                        <!-- svg -->
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 288 288"><path 
                            style="fill:#f6f6f6"><animate repeatCount="indefinite" attributeName="d" dur="5s" 
                            values="M37.5,186c-12.1-10.5-11.8-32.3-7.2-46.7c4.8-15,13.1-17.8,30.1-36.7C91,68.8,
                            83.5,56.7,103.4,45 c22.2-13.1,51.1-9.5,69.6-1.6c18.1,7.8,15.7,15.3,43.3,33.2c28.8,
                            18.8,37.2,14.3,46.7,27.9c15.6,22.3,6.4,53.3,4.4,60.2 c-3.3,11.2-7.1,23.9-18.5,
                            32c-16.3,11.5-29.5,0.7-48.6,11c-16.2,8.7-12.6,19.7-28.2,33.2c-22.7,19.7-63.8,
                            25.7-79.9,9.7 c-15.2-15.1,0.3-41.7-16.6-54.9C63,186,49.7,196.7,37.5,186z; M51,
                            171.3c-6.1-17.7-15.3-17.2-20.7-32c-8-21.9,0.7-54.6,20.7-67.1c19.5-12.3,32.8,5.5,
                            67.7-3.4C145.2,62,145,49.9,173,43.4 c12-2.8,41.4-9.6,60.2,6.6c19,16.4,16.7,47.5,
                            16,57.7c-1.7,22.8-10.3,25.5-9.4,46.4c1,22.5,11.2,25.8,9.1,42.6 c-2.2,17.6-16.3,
                            37.5-33.5,40.8c-22,4.1-29.4-22.4-54.9-22.6c-31-0.2-40.8,39-68.3,
                            35.7c-17.3-2-32.2-19.8-37.3-34.8 C48.9,198.6,57.8,191,51,171.3z; M37.5,
                            186c-12.1-10.5-11.8-32.3-7.2-46.7c4.8-15,13.1-17.8,30.1-36.7C91,68.8,83.5,56.7,
                            103.4,45 c22.2-13.1,51.1-9.5,69.6-1.6c18.1,7.8,15.7,15.3,43.3,33.2c28.8,18.8,
                            37.2,14.3,46.7,27.9c15.6,22.3,6.4,53.3,4.4,60.2 c-3.3,11.2-7.1,23.9-18.5,32c-16.3,
                            11.5-29.5,0.7-48.6,11c-16.2,8.7-12.6,19.7-28.2,33.2c-22.7,19.7-63.8,25.7-79.9,
                            9.7 c-15.2-15.1,0.3-41.7-16.6-54.9C63,186,49.7,196.7,37.5,186z"></animate></path></svg>
                    </div>
                    <h4><a href="pet.php?Loai=Chim">all for birds</a></h4>
                </div>

                <div class="single_pet">
                    <div class="img">
                        <img src="../petShopImages/Img/rabit.webp" alt="">
                        <!-- svg -->
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 288 288"><path 
                            style="fill:#f6f6f6"><animate repeatCount="indefinite" attributeName="d" dur="5s" 
                            values="M37.5,186c-12.1-10.5-11.8-32.3-7.2-46.7c4.8-15,13.1-17.8,30.1-36.7C91,68.8,
                            83.5,56.7,103.4,45 c22.2-13.1,51.1-9.5,69.6-1.6c18.1,7.8,15.7,15.3,43.3,33.2c28.8,
                            18.8,37.2,14.3,46.7,27.9c15.6,22.3,6.4,53.3,4.4,60.2 c-3.3,11.2-7.1,23.9-18.5,
                            32c-16.3,11.5-29.5,0.7-48.6,11c-16.2,8.7-12.6,19.7-28.2,33.2c-22.7,19.7-63.8,
                            25.7-79.9,9.7 c-15.2-15.1,0.3-41.7-16.6-54.9C63,186,49.7,196.7,37.5,186z; M51,
                            171.3c-6.1-17.7-15.3-17.2-20.7-32c-8-21.9,0.7-54.6,20.7-67.1c19.5-12.3,32.8,5.5,
                            67.7-3.4C145.2,62,145,49.9,173,43.4 c12-2.8,41.4-9.6,60.2,6.6c19,16.4,16.7,47.5,
                            16,57.7c-1.7,22.8-10.3,25.5-9.4,46.4c1,22.5,11.2,25.8,9.1,42.6 c-2.2,17.6-16.3,
                            37.5-33.5,40.8c-22,4.1-29.4-22.4-54.9-22.6c-31-0.2-40.8,39-68.3,
                            35.7c-17.3-2-32.2-19.8-37.3-34.8 C48.9,198.6,57.8,191,51,171.3z; M37.5,
                            186c-12.1-10.5-11.8-32.3-7.2-46.7c4.8-15,13.1-17.8,30.1-36.7C91,68.8,83.5,56.7,
                            103.4,45 c22.2-13.1,51.1-9.5,69.6-1.6c18.1,7.8,15.7,15.3,43.3,33.2c28.8,18.8,
                            37.2,14.3,46.7,27.9c15.6,22.3,6.4,53.3,4.4,60.2 c-3.3,11.2-7.1,23.9-18.5,32c-16.3,
                            11.5-29.5,0.7-48.6,11c-16.2,8.7-12.6,19.7-28.2,33.2c-22.7,19.7-63.8,25.7-79.9,
                            9.7 c-15.2-15.1,0.3-41.7-16.6-54.9C63,186,49.7,196.7,37.5,186z"></animate></path></svg>
                    </div>
                    <h4><a href="pet.php?Loai=Thỏ">all for rabbits</a></h4>
                </div>
            </div>
        </div>
    </section>

    <!-- Second banner -->
    <div class="second_banner">
        <div class="txt">
            <h5>Bio-Organic 30% OFF</h5>
            <h2>Testi & Healthy <br> 
            food</h2>
            <button>Shop Now</button>
        </div>
    </div>

    <!-- featured products -->
    <section id="feature_products" class="w-70">
        <div class="feature_pro">
            <div class="heading">
                <h3>Shop By Category</h3>
                <div class="paw">
                    <i class="fa-solid fa-paw"></i>
                </div>
            </div>
            <div class="feature_product">
                <?php foreach ($feature_products as $product): ?>
                    <div class="fp_items">
                        <div class="img">
                            <img src="../petShopImages/Img/<?php echo $product['HinhAnh']; ?>" alt="<?php echo $product['TenSP']; ?>">
                            <?php if (isset($product['GiamGia']) && $product['GiamGia'] > 0): ?>
                                <div class="tag">-<?php echo $product['GiamGia']; ?>% Sale</div>
                            <?php endif; ?>
                            <div class="hidden_icons">
                                <!-- <i class="fa-regular fa-heart"></i> -->
                                <i class="fa-solid fa-eye"></i>
                            </div>
                        </div>
                        <div class="fp_details">
                            <div class="reviews">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fa-<?php echo ($i <= $averageRating_feature) ? 'solid' : 'regular'; ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="sub_category"><?php echo $product['LoaiSP']; ?></span>
                            <h6><?php echo $product['TenSP']; ?></h6>
                            <div class="price">
                                <?php if (!empty($product['GiamGia']) && $product['GiamGia'] > 0 && isset($product['GiaGoc']) && $product['GiaGoc'] > 0): ?>
                                    <del><?php echo number_format($product['GiaGoc'], 0); ?> VND</del>
                                    <span><?php echo number_format($product['GiaBan'], 0); ?> VND</span>
                                <?php else: ?>
                                    <span><?php echo number_format($product['GiaBan'], 0); ?> VND</span>
                                <?php endif; ?>
                            </div>
                            <form method="POST" action="" name="add_to_cart">
                                <input type="hidden" name="MaSP" value="<?php echo $product['MaSP']; ?>">
                                <button type="submit" name="add_to_cart" class="add-to-cart-btn" data-product-id="<?php echo $product['MaSP']; ?>" >
                                    Thêm vào giỏ hàng
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Banner 3 -->
    <section class="w-70">
        <div class="two_banner">
            <div class="img">
                <img src="../petShopImages/Img/left_banner.webp" alt="">
                <div class="txt">
                    <h5>Today Only</h5>
                    <h4>Big Sale <br> UP TO 50 %</h4>
                </div>
            </div>
            <div class="img">
                <img src="../petShopImages/Img/right_banner.webp" alt="">
                <div class="txt">
                    <h5>Special Offer</h5>
                    <h4>Get An Extra <br> 10% OFF</h4>
                </div>
            </div>
        </div>
    </section>

    <!-- Lastest -->
    <section class="lastest_products" class="w-70">
        <div class="lastest_pro">
            <div class="heading">
                <h3>Shop By Category</h3>
                <div class="paw">
                    <i class="fa-solid fa-paw"></i>
                </div>
            </div>
            <div class="lastest_products">
                <?php foreach ($latest_products as $product): ?>
                    <div class="lp_items">
                        <div class="img">
                            <img src="../petShopImages/Img/<?php echo $product['HinhAnh']; ?>" alt="<?php echo $product['TenSP']; ?>">
                            <?php if (isset($product['GiamGia']) && $product['GiamGia'] > 0): ?>
                                <div class="tag">-<?php echo $product['GiamGia']; ?>%</div>
                            <?php endif; ?>
                            <div class="hidden_icons">
                                <!-- <i class="fa-regular fa-heart"></i> -->
                                <i class="fa-solid fa-eye"></i>
                            </div>
                        </div>
                        <div class="lp_details">
                            <div class="reviews">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fa-<?php echo ($i <= $averageRating_latest) ? 'solid' : 'regular'; ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <h4><?php echo $product['TenSP']; ?></h4>
                            <div class="price">
                                <?php if (!empty($product['GiamGia']) && $product['GiamGia'] > 0 && isset($product['GiaGoc']) && $product['GiaGoc'] > 0): ?>
                                    <del><?php echo number_format($product['GiaGoc'], 0); ?> VND</del>
                                    <span><?php echo number_format($product['GiaBan'], 0); ?> VND</span>
                                <?php else: ?>
                                    <span><?php echo number_format($product['GiaBan'], 0); ?> VND</span>
                                <?php endif; ?>
                            </div>
                            <form method="POST" action="" name="add_to_cart">
                                <input type="hidden" name="MaSP" value="<?php echo $product['MaSP']; ?>">
                                <button type="submit" name="add_to_cart" class="add-to-cart-btn" data-product-id="<?php echo $product['MaSP']; ?>" >
                                    Thêm vào giỏ hàng
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div> 
        </div>
    </section>

    <!-- Big food banner -->
    <div class="bf_banner">
        <div class="bf_food">
            <img src="../petShopImages/Img/big_banner_food.webp" alt="">
        </div>
        <div class="left_txt">
            <div>
                <h4>Real Food</h4>
                <p>Human-grade meat and veggies in simple recipes, made for dogs</p>
            </div>
            <div>
                <h4>USDA</h4>
                <p>Safety and quality never before avaiable to pets</p>
            </div>
        </div>

        <div class="right_txt">
            <div>
                <h4>Made Fresh</h4>
                <p>Maintain whole food and nutritional</p>
            </div>
            <div>
                <h4>Vet Approved</h4>
                <p>Nutrition that exceeds industry standarts for pets</p>
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

    <!-- JS -->
    <script>

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

        // Xử lý sự kiện click vào icon "eye" (biểu tượng con mắt)
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.hidden_icons .fa-eye').forEach(icon => {
                icon.addEventListener('click', function() {
                    const productItem = this.closest('.fp_items, .lp_items'); // Tìm phần tử cha phù hợp
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