<?php
session_start();
include('../config/connectDB.php');

// Check if user is logged in
if (!isset($_SESSION['ID_ND'])) {
    header("Location: login.php");
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


// Fetch cart items from database
$sql = "SELECT g.MaSP, g.MaThuCung, g.SoLuong, 
               s.TenSP, s.HinhAnh as HinhAnhSP, s.GiaBan as GiaBanSP, s.LoaiSP, 
               t.TenThuCung, t.Loai, t.GiaBan as GiaBanTC, t.HinhAnh as HinhAnhTC
        FROM giohang g
        LEFT JOIN sanpham s ON g.MaSP = s.MaSP
        LEFT JOIN thucung t ON g.MaThuCung = t.MaThuCung
        WHERE g.MaKH = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $MaKH);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total
$totalAmount = 0;
$cartItems = [];

while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
    
    // Add price to total - either product price or pet price
    if ($row['MaSP']) {
        $totalAmount += $row['GiaBanSP'] * $row['SoLuong'];
    } elseif ($row['MaThuCung']) {
        $totalAmount += $row['GiaBanTC'] * $row['SoLuong'];
    }
}


//Cập nhật số lượng 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_quantity'])) {
    $itemId = $_POST['item_id'];
    $itemType = $_POST['item_type'];
    $newQuantity = (int) $_POST['quantity'];

    if ($newQuantity < 1) {
        echo json_encode(["status" => "error", "message" => "Số lượng không hợp lệ"]);
        exit();
    }

    if ($itemType == 'product') {
        $updateSql = "UPDATE giohang SET SoLuong = ? WHERE MaKH = ? AND MaSP = ?";
    } else {
        $updateSql = "UPDATE giohang SET SoLuong = ? WHERE MaKH = ? AND MaThuCung = ?";
    }

    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("iss", $newQuantity, $MaKH, $itemId);
    
    if ($updateStmt->execute()) {
        echo json_encode(["status" => "success", "new_quantity" => $newQuantity]);
    } else {
        echo json_encode(["status" => "error", "message" => "Cập nhật thất bại"]);
    }
    exit();
}

// Handle item removal
if (isset($_GET['remove'])) {
    $itemId = isset($_GET['id']) ? $_GET['id'] : '';
    $itemType = isset($_GET['type']) ? $_GET['type'] : '';
    
    // Validate inputs
    if (empty($itemId)) {
        echo "<script>alert('ID sản phẩm không hợp lệ!');</script>";
        echo "<script>window.location.href = 'cart.php';</script>";
        exit();
    }
    
    if ($itemType == 'product') {
        $deleteSql = "DELETE FROM giohang WHERE MaKH = ? AND MaSP = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("ss", $MaKH, $itemId);
    } else if ($itemType == 'pet') {
        $deleteSql = "DELETE FROM giohang WHERE MaKH = ? AND MaThuCung = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("ss", $MaKH, $itemId);
    } else {
        echo "<script>alert('Loại sản phẩm không hợp lệ!');</script>";
        echo "<script>window.location.href = 'cart.php';</script>";
        exit();
    }
    
    if (!$deleteStmt->execute()) {
        echo "<script>alert('Lỗi khi xóa sản phẩm: " . $conn->error . "');</script>";
    }
    
    echo "<script>window.location.href = 'cart.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="../fontawesome-free-6.4.2-web/css/all.min.css">
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/cart_style.css">
    <link rel="stylesheet" href="../style/product_style.css">
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
            <!-- <div class="rel">
                <i class="fa-solid fa-cart-plus"></i>
                <span class="num">3</span>
                <span class="total">50K</span>
            </div> -->
            <!-- <div class="container_search">
                <div class="icon">
                    <i class="search fa-solid fa-magnifying-glass" id='icon_search'></i>
                </div>
                <div class="input">
                    <input type="text" placeholder="Tìm kiếm" id="search" value="<?php echo htmlspecialchars($search); ?>">
                    <i class="clear fa-solid fa-xmark" id="clear"></i>
                </div>
            </div> -->
        </div>
    </header>

    <div class="container">
        <div class="cart-container">
            <h2>Giỏ hàng của bạn</h2>
            
            <div class="cart-items">
                <?php if (count($cartItems) > 0): ?>
                    <p>Bạn đang có <?php echo count($cartItems); ?> sản phẩm trong giỏ hàng</p>
                    
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item">
                            <div class="item-image">
                                <?php if ($item['MaSP']): ?>
                                    <img src="../petShopImages/Img/<?php echo $item['HinhAnhSP']; ?>" alt="<?php echo $item['TenSP']; ?>">
                                <?php else: ?>
                                    <img src="../petShopImages/Img/<?php echo $item['HinhAnhTC']; ?>" alt="<?php echo $item['TenThuCung']; ?>">
                                <?php endif; ?>
                            </div>
                            
                            <div class="item-details">
                                <div class="item-name">
                                    <?php if ($item['MaSP']): ?>
                                        <?php echo $item['TenSP']; ?>
                                    <?php else: ?>
                                        <?php echo $item['TenThuCung']; ?>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="item-meta">
                                    <?php if ($item['MaSP']): ?>
                                        Bao <?php echo $item['LoaiSP']; ?>
                                    <?php else: ?>
                                        Loại: <?php echo $item['Loai']; ?>
                                    <?php endif; ?>
                                </div>
                                
                                <form method="post" action="">
                                    <div class="quantity-control">
                                        <button type="button" class="quantity-btn" onclick="updateQuantity(this, -1)" 
                                            data-id="<?php echo isset($item['MaSP']) && $item['MaSP'] ? $item['MaSP'] : (isset($item['MaThuCung']) && $item['MaThuCung'] ? $item['MaThuCung'] : ''); ?>" 
                                            data-type="<?php echo isset($item['MaSP']) && $item['MaSP'] ? 'product' : 'pet'; ?>">-</button>

                                        <input type="number" class="quantity-input" value="<?php echo $item['SoLuong']; ?>" min="1"
                                            data-id="<?php echo isset($item['MaSP']) && $item['MaSP'] ? $item['MaSP'] : (isset($item['MaThuCung']) && $item['MaThuCung'] ? $item['MaThuCung'] : ''); ?>"
                                            data-type="<?php echo isset($item['MaSP']) && $item['MaSP'] ? 'product' : 'pet'; ?>"
                                            onchange="updateQuantityInput(this)">

                                        <button type="button" class="quantity-btn" onclick="updateQuantity(this, 1)" 
                                            data-id="<?php echo isset($item['MaSP']) && $item['MaSP'] ? $item['MaSP'] : (isset($item['MaThuCung']) && $item['MaThuCung'] ? $item['MaThuCung'] : ''); ?>" 
                                            data-type="<?php echo isset($item['MaSP']) && $item['MaSP'] ? 'product' : 'pet'; ?>">+</button>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="item-price">
                                <?php if ($item['MaSP']): ?>
                                    <?php echo number_format($item['GiaBanSP'], 0, ',', '.'); ?>VND
                                <?php else: ?>
                                    <?php echo number_format($item['GiaBanTC'], 0, ',', '.'); ?>VND
                                <?php endif; ?>
                            </div>

                            <!-- Add Delete Icon -->
                            <div class="item-remove">
                                <i class="clear fa-solid fa-xmark" id="clear" 
                                data-id="<?php echo $item['MaSP'] ? $item['MaSP'] : $item['MaThuCung']; ?>" 
                                data-type="<?php echo $item['MaSP'] ? 'product' : 'pet'; ?>"
                                onclick="removeCartItem(this)"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Giỏ hàng của bạn đang trống.</p>
                <?php endif; ?>
            </div>
            
            <div class="order-notes">
                <h2>Ghi chú đơn hàng</h2>
                <textarea class="notes-textarea" placeholder="Nhập ghi chú về đơn hàng của bạn, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn."></textarea>
                
                <div class="invoice-checkbox">
                    <input type="checkbox" id="invoice-checkbox">
                    <label for="invoice-checkbox">Xuất hoá đơn cho đơn hàng</label>
                </div>
            </div>
        </div>
        
        <div class="order-summary">
            <h2>Thông tin đơn hàng</h2>
            
            <div class="cart-total">
                <h3>Tổng tiền: <span id="totalAmount"><?php echo number_format($totalAmount, 0, ',', '.'); ?> VND</span></h3>
            </div>
            
            <div class="shipping-info">
                <ul>
                    <li>Phí vận chuyển sẽ được tính ở trang thanh toán.</li>
                    <li>Bạn cũng có thể nhập mã giảm giá ở trang thanh toán.</li>
                </ul>
            </div>
            
            <button class="checkout-btn" onclick="window.location.href='checkout.php'">Thanh toán</button>
            
            <div class="policy-info">
                <h3>Chính sách mua hàng:</h3>
                <p>Hiện chúng tôi chỉ áp dụng thanh toán với đơn hàng có giá trị tối thiểu 100.000đ trở lên.</p>
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
        function updateQuantity(button, change) {
            const container = button.closest('.quantity-control');
            const input = container.querySelector('.quantity-input');
            const itemId = button.getAttribute('data-id');
            const itemType = button.getAttribute('data-type');
            
            console.log("Update triggered:", {itemId, itemType, change});
            
            // Lấy giá trị hiện tại và tính toán giá trị mới
            let currentValue = parseInt(input.value);
            let newValue = currentValue + change;
            
            // Đảm bảo giá trị không nhỏ hơn 1
            if (newValue < 1) newValue = 1;
            
            // Cập nhật giá trị hiển thị
            input.value = newValue;
            
            // Gửi request AJAX để cập nhật database
            updateCartInDatabase(itemId, itemType, newValue);
        }

        function updateQuantityInput(input) {
            const itemId = input.getAttribute('data-id');
            const itemType = input.getAttribute('data-type');
            let value = parseInt(input.value);
            
            console.log("Input update triggered:", {itemId, itemType, value});
            
            // Đảm bảo giá trị không nhỏ hơn 1
            if (value < 1) {
                value = 1;
                input.value = 1;
            }
            
            // Gửi request AJAX để cập nhật database
            updateCartInDatabase(itemId, itemType, value);
        }

        function updateCartInDatabase(itemId, itemType, quantity) {
            console.log("Sending to server:", {itemId, itemType, quantity});
            
            // Kiểm tra dữ liệu
            if (!itemId || itemId === 'undefined' || itemId === 'null') {
                console.error("Invalid item ID:", itemId);
                alert("ID sản phẩm không hợp lệ. Vui lòng tải lại trang.");
                return;
            }
            
            // Tạo đối tượng FormData để gửi dữ liệu
            const formData = new FormData();
            formData.append('item_id', itemId);
            formData.append('item_type', itemType);
            formData.append('quantity', quantity);
            
            // Debug what's being sent
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            // Sử dụng Fetch API để gửi request
            fetch('update_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log("Server response:", data);
                
                if (data.success) {
                    // Cập nhật tổng tiền trên giao diện
                    document.getElementById('totalAmount').textContent = data.totalAmount + ' VND';
                } else {
                    console.error('Lỗi khi cập nhật giỏ hàng:', data.message);
                    alert('Có lỗi xảy ra khi cập nhật giỏ hàng: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Lỗi kết nối:', error);
                alert('Có lỗi kết nối khi cập nhật giỏ hàng. Vui lòng thử lại sau.');
            });
        }

        // Add this to the existing script section
        function removeCartItem(icon) {
            const itemId = icon.getAttribute('data-id');
            const itemType = icon.getAttribute('data-type');
            
            console.log("Removing item:", {itemId, itemType}); // Add this for debugging
            
            if (!itemId || itemId === 'undefined' || itemId === 'null' || itemId === '') {
                alert("ID sản phẩm không hợp lệ. Vui lòng tải lại trang.");
                return;
            }
            
            if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                // Redirect to the same page with remove parameters
                window.location.href = 'cart.php?remove=1&id=' + itemId + '&type=' + itemType;
            }
            error_log("Item ID received: " . print_r($itemId, true));
            error_log("Item Type received: " . print_r($itemType, true));
        }
    </script>
</body>
</html>