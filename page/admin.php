<?php
session_start();
require_once '../config/connectDB.php';

// Check if user is logged in
if (!isset($_SESSION['ID_ND'])) {
    header("Location: login_register.php");
    exit();
}

include 'admin_functions.php';

// Load data
$products = getAllProducts();
$pets = getAllPets();

// Khởi tạo biến
$searchProductTerm = '';
$searchPetTerm = '';

// Xử lý yêu cầu tìm kiếm
if (isset($_GET['product_search'])) {
    $searchProductTerm = trim($_GET['product_search']);
    $products = !empty($searchProductTerm) ? searchProducts($searchProductTerm) : getAllProducts();
} else {
    $products = getAllProducts();
}

if (isset($_GET['pet_search'])) {
    $searchPetTerm = trim($_GET['pet_search']);
    $pets = !empty($searchPetTerm) ? searchPets($searchPetTerm) : getAllPets();
} else {
    $pets = getAllPets();
}

// Đảm bảo rằng $products và $pets là các mảng ngay cả khi tìm kiếm không trả về kết quả nào.
if (!is_array($products)) $products = array();
if (!is_array($pets)) $pets = array();

// Load data
$users = getAllUsers();

// Khởi tạo biến
$searchUserTerm = '';

// Xử lý yêu cầu tìm kiếm
if (isset($_GET['user_search'])) {
    $searchUserTerm = trim($_GET['user_search']);
    $users = !empty($searchUserTerm) ? searchUsers($searchUserTerm) : getAllUsers();
} else {
    $users = getAllUsers();
}

// Đảm bảo rằng $users là mảng ngay cả khi tìm kiếm không trả về kết quả nào.
if (!is_array($users)) $users = array();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="../fontawesome-free-6.4.2-web/css/all.min.css">
    <link rel="stylesheet" href="../style/admin_style.css">
    <style>
        .user-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .user-table th, .user-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .user-table th {
            background-color: #f9f9f9;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="top">
            <div class="logo">
                <i class="fa-solid fa-paw"></i>
                <span> Website thú cưng</span>
            </div>
            <i class="fa-solid fa-bars" id="btn"></i>
        </div>
        <div class="user">
           <img src="../petShopImages/Img/logo.webp" alt="" class="user-img">
           <div>
            <p class="bold">Group 8</p>
            <p>Admin</p>
           </div>
        </div>
        <ul>
            <li>
                <a href="#" id="product-management-link">
                    <i class="fa-solid fa-church"></i>
                    <span class="nav-item">Quản lý sản phẩm</span>
                </a>
                <span class="tooltip">Quản lý sản phẩm</span>
            </li>
            <li>
                <a href="#" id="pet-management-link">
                    <i class="fa-solid fa-cat"></i>
                    <span class="nav-item">Quản lý thú cưng</span>
                </a>
                <span class="tooltip">Quản lý thú cưng</span>
            </li>
            <li>
                <a href="#" id="user-management-link">
                    <i class="fa-solid fa-users"></i>
                    <span class="nav-item">Quản lý người dùng</span>
                </a>
                <span class="tooltip">Quản lý người dùng</span>
            </li>
            <li>
                <a href="#" onclick="window.location.href='index.php'">
                    <i class="fa-solid fa-hand-point-left"></i>
                    <span class="nav-item">Quay lại</span>
                </a>
                <span class="tooltip">Quay lại</span>
            </li>
        </ul>
    </div>
    
    <div class="main-content">
        <!-- Product Management Section -->
        <div class="container" id="product-management">
            <div class="product-form">
                <h1>Quản Lý Sản Phẩm</h1>
                <form id="product-form" enctype="multipart/form-data" method="POST">
                    <input type="hidden" name="product_action" value="add">
                    <input type="hidden" name="product_id" id="product-id" value="">
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="product-code">Mã sản phẩm:</label>
                                <input type="text" id="product-code" class="form-control" value="Tự động tạo" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label for="product-name">Tên sản phẩm:</label>
                                <input type="text" name="product_name" id="product-name" class="form-control" placeholder="Nhập tên sản phẩm" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="product-type">Loại:</label>
                                <select name="product_type" id="product-type" class="form-control" required>
                                    <option value="Thức ăn thú cưng">Thức ăn thú cưng</option>
                                    <option value="Phụ kiện">Phụ kiện</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="product-price">Giá gốc:</label>
                                <input type="number" name="product_price" id="product-price" class="form-control" placeholder="Nhập giá sản phẩm" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="product-discount">Giảm giá (%):</label>
                                <input type="number" name="product_discount" id="product-discount" class="form-control" placeholder="Nhập % giảm giá" value="0">
                            </div>
                            
                            <div class="form-group">
                                <label for="product-description">Mô tả:</label>
                                <textarea name="product_description" id="product-description" class="form-control" placeholder="Nhập mô tả chi tiết" required></textarea>
                            </div>
                        </div>
                        
                        <div class="image-preview">
                            <div>
                                <p>Hình ảnh</p>
                                <input type="file" name="product_image" id="product-image" class="image-button" required>
                                <div id="product-image-preview"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" id="product-add-btn" class="btn btn-add">Thêm</button>
                        <button type="button" id="product-edit-btn" class="btn btn-edit">Sửa</button>
                        <button type="button" id="product-delete-btn" class="btn btn-delete">Xóa</button>
                        <button type="button" id="product-reset-btn" class="btn">Làm mới</button>
                    </div>
                </form>
            </div>

            <div class="search-container">
                <form action="" method="GET">
                    <input type="text" name="product_search" placeholder="Tìm kiếm sản phẩm..." value="<?php echo htmlspecialchars($searchProductTerm); ?>">
                    <button type="submit"><i class="fa fa-search"></i> Tìm kiếm</button>
                </form>
            </div>
            
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Mã SP</th>
                        <th>Tên sản phẩm</th>
                        <th>Loại</th>
                        <th>Giá bán</th>
                        <th>Mô tả</th>
                        <th>Ảnh</th>
                        <th>Giảm giá</th>
                        <th>Giá gốc</th>
                        <th>Ngày thêm</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr data-id="<?php echo $product['MaSP']; ?>">
                        <td><?php echo $product['MaSP']; ?></td>
                        <td><?php echo $product['TenSP']; ?></td>
                        <td><?php echo $product['LoaiSP']; ?></td>
                        <td><?php echo $product['GiaBan']; ?></td>
                        <td><?php echo $product['MoTa']; ?></td>
                        <td><img src="../petShopImages/Img/<?php echo $product['HinhAnh']; ?>" alt="Product Image" width="50"></td>
                        <td><?php echo $product['GiamGia']; ?>%</td>
                        <td><?php echo $product['GiaGoc']; ?></td>
                        <td><?php echo $product['NgayThem']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pet Management Section -->
        <div class="container" id="pet-management">
            <div class="product-form">
                <h1>Quản Lý Thú Cưng</h1>
                <form id="pet-form" enctype="multipart/form-data" method="POST">
                    <input type="hidden" name="pet_action" value="add">
                    <input type="hidden" name="pet_id" id="pet-id" value="">
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="pet-code">Mã thú cưng:</label>
                                <input type="text" id="pet-code" class="form-control" value="Tự động tạo" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label for="pet-name">Tên thú cưng:</label>
                                <input type="text" name="pet_name" id="pet-name" class="form-control" placeholder="Nhập tên thú cưng" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="pet-type">Loại:</label>
                                <select name="pet_type" id="pet-type" class="form-control" required>
                                    <option value="Chó">Chó</option>
                                    <option value="Mèo">Mèo</option>
                                    <option value="Thỏ">Thỏ</option>
                                    <option value="Chim">Chim</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="pet-breed">Giống:</label>
                                <input type="text" name="pet_breed" id="pet-breed" class="form-control" placeholder="Nhập giống thú cưng" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="pet-age">Tuổi:</label>
                                <input type="number" name="pet_age" id="pet-age" class="form-control" placeholder="Nhập tuổi thú cưng" required>
                            </div>
        
                            <div class="form-group">
                                <label for="pet-gender">Giới tính:</label>
                                <input type="text" name="pet_gender" id="pet-gender" class="form-control" placeholder="Nhập giới tính thú cưng" required>
                            </div>
        
                            <div class="form-group">
                                <label for="pet-price">Giá bán:</label>
                                <input type="number" name="pet_price" id="pet-price" class="form-control" placeholder="Nhập giá thú cưng" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="pet-description">Mô tả:</label>
                                <textarea name="pet_description" id="pet-description" class="form-control" placeholder="Nhập mô tả chi tiết" required></textarea>
                            </div>
                        </div>
                        
                        <div class="image-preview">
                            <div>
                                <p>Hình ảnh</p>
                                <input type="file" name="pet_image" id="pet-image" class="image-button" required>
                                <div id="pet-image-preview"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" id="pet-add-btn" class="btn btn-add">Thêm</button>
                        <button type="button" id="pet-edit-btn" class="btn btn-edit">Sửa</button>
                        <button type="button" id="pet-delete-btn" class="btn btn-delete">Xóa</button>
                        <button type="button" id="pet-reset-btn" class="btn">Làm mới</button>
                    </div>
                </form>
            </div>

            <div class="search-container">
                <form action="" method="GET">
                    <input type="text" name="pet_search" placeholder="Tìm kiếm thú cưng..." value="<?php echo htmlspecialchars($searchPetTerm); ?>">
                    <button type="submit"><i class="fa fa-search"></i> Tìm kiếm</button>
                </form>
            </div>
            
            <table class="pet-table">
                <thead>
                    <tr>
                        <th>Mã thú cưng</th>
                        <th>Tên thú cưng</th>
                        <th>Loại</th>
                        <th>Giống</th>
                        <th>Tuổi</th>
                        <th>Giới tính</th>
                        <th>Giá bán</th>
                        <th>Mô tả</th>
                        <th>Ảnh</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pets as $pet): ?>
                    <tr data-id="<?php echo $pet['MaThuCung']; ?>">
                        <td><?php echo $pet['MaThuCung']; ?></td>
                        <td><?php echo $pet['TenThuCung']; ?></td>
                        <td><?php echo $pet['Loai']; ?></td>
                        <td><?php echo $pet['Giong']; ?></td>
                        <td><?php echo $pet['Tuoi']; ?></td>
                        <td><?php echo $pet['GioiTinh']; ?></td>
                        <td><?php echo $pet['GiaBan']; ?></td>
                        <td><?php echo $pet['MoTa']; ?></td>
                        <td><img src="../petShopImages/Img/<?php echo $pet['HinhAnh']; ?>" alt="Pet Image" width="50"></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- User Management Section -->
        <div class="container" id="user-management">
            <div class="product-form">
                <h1>Quản Lý Người Dùng</h1>
                <form id="user-form" method="POST">
                    <input type="hidden" name="user_action" value="add">
                    <input type="hidden" name="user_id" id="user-id" value="">
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="user-code">Mã người dùng:</label>
                                <input type="text" id="user-code" class="form-control" value="Tự động tạo" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label for="user-name">Họ tên:</label>
                                <input type="text" name="user_name" id="user-name" class="form-control" placeholder="Nhập họ tên" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="user-email">Email:</label>
                                <input type="email" name="user_email" id="user-email" class="form-control" placeholder="Nhập email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="user-password">Mật khẩu:</label>
                                <input type="password" name="user_password" id="user-password" class="form-control" placeholder="Nhập mật khẩu">
                                <small>* Để trống nếu không thay đổi mật khẩu</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="user-role">Vai trò:</label>
                                <select name="user_role" id="user-role" class="form-control" required>
                                    <option value="Admin">Admin</option>
                                    <option value="Customer">Customer</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="user-join-date">Ngày tham gia:</label>
                                <input type="text" id="user-join-date" class="form-control" value="Tự động tạo" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" id="user-add-btn" class="btn btn-add">Thêm</button>
                        <button type="button" id="user-edit-btn" class="btn btn-edit">Sửa</button>
                        <button type="button" id="user-delete-btn" class="btn btn-delete">Xóa</button>
                        <button type="button" id="user-reset-btn" class="btn">Làm mới</button>
                    </div>
                </form>
            </div>

            <div class="search-container">
                <form action="" method="GET">
                    <input type="text" name="user_search" placeholder="Tìm kiếm người dùng..." value="<?php echo htmlspecialchars($searchUserTerm); ?>">
                    <button type="submit"><i class="fa fa-search"></i> Tìm kiếm</button>
                </form>
            </div>
            
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Mã ND</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Ngày tham gia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr data-id="<?php echo $user['ID_ND']; ?>">
                        <td><?php echo $user['ID_ND']; ?></td>
                        <td><?php echo $user['HoTen']; ?></td>
                        <td><?php echo $user['Email']; ?></td>
                        <td><?php echo $user['VaiTro']; ?></td>
                        <td><?php echo $user['NgayThamGia']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

    <script>
        let btn = document.querySelector("#btn");
        let sidebar = document.querySelector(".sidebar");
        
        btn.onclick = function() {
            sidebar.classList.toggle("active");
        };
        
        // Chuyển tab
        document.getElementById('product-management-link').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('product-management').style.display = 'block';
            document.getElementById('pet-management').style.display = 'none';
            document.getElementById('user-management').style.display = 'none';
        });
        
        document.getElementById('pet-management-link').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('product-management').style.display = 'none';
            document.getElementById('pet-management').style.display = 'block';
            document.getElementById('user-management').style.display = 'none';
        });

        document.getElementById('user-management-link').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('user-management').style.display = 'block';
            document.getElementById('product-management').style.display = 'none';
        document.getElementById('pet-management').style.display = 'none';
        });
        
        // Mặc định hiển thị quản lý sản phẩm
        document.getElementById('product-management').style.display = 'block';
        document.getElementById('pet-management').style.display = 'none';
        document.getElementById('user-management').style.display = 'none';
        
        // Chức năng xem trước hình ảnh
        document.getElementById('product-image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('product-image-preview');
                    preview.innerHTML = `<img src="${e.target.result}" alt="Product Preview" style="max-width: 100%; max-height: 200px;">`;
                };
                reader.readAsDataURL(file);
            }
        });
        
        document.getElementById('pet-image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('pet-image-preview');
                    preview.innerHTML = `<img src="${e.target.result}" alt="Pet Preview" style="max-width: 100%; max-height: 200px;">`;
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Chức năng của hình thức sản phẩm
        const productForm = document.getElementById('product-form');
        const productResetBtn = document.getElementById('product-reset-btn');
        const productEditBtn = document.getElementById('product-edit-btn');
        const productDeleteBtn = document.getElementById('product-delete-btn');
        
        // Đặt lại mẫu sản phẩm
        function resetProductForm() {
            productForm.reset();
            document.getElementById('product-id').value = '';
            document.getElementById('product-code').value = 'Tự động tạo';
            document.getElementById('product-image-preview').innerHTML = '';
            productForm.querySelector('input[name="product_action"]').value = 'add';
            document.getElementById('product-image').required = true;
            document.getElementById('product-add-btn').style.display = 'inline-block';
            productEditBtn.style.display = 'none';
            productDeleteBtn.style.display = 'none';
        }
        
        productResetBtn.addEventListener('click', resetProductForm);
        
        // Chọn hàng sản phẩm
        document.querySelector('.product-table tbody').addEventListener('click', function(e) {
            const row = e.target.closest('tr');
            if (row) {
                const id = row.getAttribute('data-id');
                const cells = row.querySelectorAll('td');
                
                document.getElementById('product-id').value = id;
                document.getElementById('product-code').value = id;
                document.getElementById('product-name').value = cells[1].textContent;
                document.getElementById('product-type').value = cells[2].textContent;
                document.getElementById('product-price').value = cells[7].textContent; // Giá gốc
                document.getElementById('product-discount').value = cells[6].textContent.replace('%', ''); // Giảm giá
                document.getElementById('product-description').value = cells[4].textContent;
                
                const imgSrc = cells[5].querySelector('img').getAttribute('src');
                document.getElementById('product-image-preview').innerHTML = `<img src="${imgSrc}" alt="Product Preview" style="max-width: 100%; max-height: 200px;">`;
                
                productForm.querySelector('input[name="product_action"]').value = 'edit';
                document.getElementById('product-image').required = false;
                document.getElementById('product-add-btn').style.display = 'none';
                productEditBtn.style.display = 'inline-block';
                productDeleteBtn.style.display = 'inline-block';
            }
        });
        
        // Handle product edit
        productEditBtn.addEventListener('click', function() {
            productForm.querySelector('input[name="product_action"]').value = 'edit';
            submitProductForm();
        });
        
        // Handle product delete
        productDeleteBtn.addEventListener('click', function() {
            if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
                productForm.querySelector('input[name="product_action"]').value = 'delete';
                submitProductForm();
            }
        });
        
        // Submit product form with AJAX
        function submitProductForm() {
            const formData = new FormData(productForm);
            
            fetch('admin_functions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    location.reload(); // Reload page to see changes
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã xảy ra lỗi khi xử lý yêu cầu.');
            });
        }
        
        productForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitProductForm();
        });
        
        // Pet form functionality
        const petForm = document.getElementById('pet-form');
        const petResetBtn = document.getElementById('pet-reset-btn');
        const petEditBtn = document.getElementById('pet-edit-btn');
        const petDeleteBtn = document.getElementById('pet-delete-btn');
        
        // Reset pet form
        function resetPetForm() {
            petForm.reset();
            document.getElementById('pet-id').value = '';
            document.getElementById('pet-code').value = 'Tự động tạo';
            document.getElementById('pet-image-preview').innerHTML = '';
            petForm.querySelector('input[name="pet_action"]').value = 'add';
            document.getElementById('pet-image').required = true;
            document.getElementById('pet-add-btn').style.display = 'inline-block';
            petEditBtn.style.display = 'none';
            petDeleteBtn.style.display = 'none';
        }
        
        petResetBtn.addEventListener('click', resetPetForm);
        
        // Select pet row
        document.querySelector('.pet-table tbody').addEventListener('click', function(e) {
            const row = e.target.closest('tr');
            if (row) {
                const id = row.getAttribute('data-id');
                const cells = row.querySelectorAll('td');
                
                document.getElementById('pet-id').value = id;
                document.getElementById('pet-code').value = id;
                document.getElementById('pet-name').value = cells[1].textContent;
                document.getElementById('pet-type').value = cells[2].textContent;
                document.getElementById('pet-breed').value = cells[3].textContent;
                document.getElementById('pet-age').value = cells[4].textContent;
                document.getElementById('pet-gender').value = cells[5].textContent;
                document.getElementById('pet-price').value = cells[6].textContent;
                document.getElementById('pet-description').value = cells[7].textContent;
                
                const imgSrc = cells[8].querySelector('img').getAttribute('src');
                document.getElementById('pet-image-preview').innerHTML = `<img src="${imgSrc}" alt="Pet Preview" style="max-width: 100%; max-height: 200px;">`;
                
                petForm.querySelector('input[name="pet_action"]').value = 'edit';
                document.getElementById('pet-image').required = false;
                document.getElementById('pet-add-btn').style.display = 'none';
                petEditBtn.style.display = 'inline-block';
                petDeleteBtn.style.display = 'inline-block';
            }
        });
        
        // Handle pet edit
        petEditBtn.addEventListener('click', function() {
            petForm.querySelector('input[name="pet_action"]').value = 'edit';
            submitPetForm();
        });
        
        // Handle pet delete
        petDeleteBtn.addEventListener('click', function() {
            if (confirm('Bạn có chắc chắn muốn xóa thú cưng này?')) {
                petForm.querySelector('input[name="pet_action"]').value = 'delete';
                submitPetForm();
            }
        });
        
        // Submit pet form with AJAX
        function submitPetForm() {
            const formData = new FormData(petForm);
            
            fetch('admin_functions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    location.reload(); // Reload page to see changes
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã xảy ra lỗi khi xử lý yêu cầu.');
            });
        }
        
        petForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitPetForm();
        });
        
        // Initialize: Hide edit and delete buttons on load
        document.getElementById('product-edit-btn').style.display = 'none';
        document.getElementById('product-delete-btn').style.display = 'none';
        document.getElementById('pet-edit-btn').style.display = 'none';
        document.getElementById('pet-delete-btn').style.display = 'none';

        // Chức năng của hình thức người dùng
        const userForm = document.getElementById('user-form');
        const userResetBtn = document.getElementById('user-reset-btn');
        const userEditBtn = document.getElementById('user-edit-btn');
        const userDeleteBtn = document.getElementById('user-delete-btn');
        
        // Đặt lại mẫu người dùng
        function resetUserForm() {
            userForm.reset();
            document.getElementById('user-id').value = '';
            document.getElementById('user-code').value = 'Tự động tạo';
            document.getElementById('user-join-date').value = 'Tự động tạo';
            userForm.querySelector('input[name="user_action"]').value = 'add';
            document.getElementById('user-password').required = true;
            document.getElementById('user-add-btn').style.display = 'inline-block';
            userEditBtn.style.display = 'none';
            userDeleteBtn.style.display = 'none';
        }
        
        userResetBtn.addEventListener('click', resetUserForm);
        
        // Chọn hàng người dùng
        document.querySelector('.user-table tbody').addEventListener('click', function(e) {
            const row = e.target.closest('tr');
            if (row) {
                const id = row.getAttribute('data-id');
                const cells = row.querySelectorAll('td');
                
                document.getElementById('user-id').value = id;
                document.getElementById('user-code').value = id;
                document.getElementById('user-name').value = cells[1].textContent;
                document.getElementById('user-email').value = cells[2].textContent;
                document.getElementById('user-role').value = cells[3].textContent;
                document.getElementById('user-join-date').value = cells[4].textContent;
                
                userForm.querySelector('input[name="user_action"]').value = 'edit';
                document.getElementById('user-password').required = false;
                document.getElementById('user-add-btn').style.display = 'none';
                userEditBtn.style.display = 'inline-block';
                userDeleteBtn.style.display = 'inline-block';
            }
        });
        
        // Handle user edit
        userEditBtn.addEventListener('click', function() {
            userForm.querySelector('input[name="user_action"]').value = 'edit';
            submitUserForm();
        });
        
        // Handle user delete
        userDeleteBtn.addEventListener('click', function() {
            if (confirm('Bạn có chắc chắn muốn xóa người dùng này?')) {
                userForm.querySelector('input[name="user_action"]').value = 'delete';
                submitUserForm();
            }
        });
        
        // Submit user form with AJAX
        function submitUserForm() {
            const formData = new FormData(userForm);
            
            fetch('admin_user_functions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    location.reload(); // Reload page to see changes
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã xảy ra lỗi khi xử lý yêu cầu.');
            });
        }
        
        userForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitUserForm();
        });
        
        // Initialize: Hide edit and delete buttons on load
        document.getElementById('user-edit-btn').style.display = 'none';
        document.getElementById('user-delete-btn').style.display = 'none';
    </script>
</body>
</html>