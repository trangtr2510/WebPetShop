<?php
// Database connection
require_once '../config/connectDB.php';

// Function to sanitize input data
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

// =============== Sản phẩm ===============

// lấy danh scash sản phẩm
function getAllProducts() {
    global $conn;
    $query = "SELECT * FROM sanpham ORDER BY NgayThem DESC";
    $result = mysqli_query($conn, $query);
    
    $products = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
    
    return $products;
}

// Lấy sản phẩm theo masp
function getProductById($id) {
    global $conn;
    $id = sanitize($id);
    
    $query = "SELECT * FROM sanpham WHERE MaSP = '$id'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Add new product
function addProduct($name, $type, $price, $discount, $description, $image) {
    global $conn;
    
    $name = sanitize($name);
    $type = sanitize($type);
    $price = sanitize($price);
    $discount = sanitize($discount);
    $description = mysqli_real_escape_string($conn, $description);
    $image = sanitize($image);
    
    $date = date('Y-m-d H:i:s');
    
    $query = "INSERT INTO sanpham (TenSP, LoaiSP, MoTa, HinhAnh, GiamGia, GiaGoc, NgayThem, GiaBan) 
          VALUES ('$name', '$type', '$description', '$image', '$discount', '$price', '$date', '$price')";

    if (mysqli_query($conn, $query)) {
        return mysqli_insert_id($conn);
    } else {
        // Log the error for debugging
        error_log("MySQL Error: " . mysqli_error($conn));
        return false;
    }
}

// Update product
function updateProduct($id, $name, $type, $price, $discount, $description, $image) {
    global $conn;
    
    $id = sanitize($id);
    $name = sanitize($name);
    $type = sanitize($type);
    $price = sanitize($price);
    $discount = sanitize($discount);
    $description = sanitize($description);
    
    // Calculate final price after discount
    $final_price = $price - ($price * $discount / 100);
    
    // Check if image is provided, if not, keep the existing image
    $image_query = "";
    if (!empty($image)) {
        $image = sanitize($image);
        $image_query = ", HinhAnh = '$image'";
    }
    
    $query = "UPDATE sanpham 
              SET TenSP = '$name', LoaiSP = '$type', GiaBan = '$final_price', 
                  MoTa = '$description'$image_query, GiamGia = '$discount', GiaGoc = '$price' 
              WHERE MaSP = '$id'";
    
    return mysqli_query($conn, $query);
}

// Delete product
function deleteProduct($id) {
    global $conn;
    $id = sanitize($id);
    
    $query = "DELETE FROM sanpham WHERE MaSP = '$id'";
    return mysqli_query($conn, $query);
}

// ==================== Thú cưng ====================

// Fetch all pets
function getAllPets() {
    global $conn;
    $query = "SELECT * FROM thucung";
    $result = mysqli_query($conn, $query);
    
    $pets = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $pets[] = $row;
        }
    }
    
    return $pets;
}

// Get pet by ID
function getPetById($id) {
    global $conn;
    $id = sanitize($id);
    
    $query = "SELECT * FROM thucung WHERE MaThuCung = '$id'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Add new pet
function addPet($name, $type, $breed, $age, $gender, $price, $description, $image) {
    global $conn;
    
    // Sanitize và chuyển đổi kiểu dữ liệu phù hợp
    $name = sanitize($name);
    $type = sanitize($type);
    $breed = sanitize($breed);
    // Chuyển đổi tuổi thành số nguyên (int)
    $age = $age !== '' ? intval(sanitize($age)) : 'NULL';
    $gender = $gender !== '' ? "'" . sanitize($gender) . "'" : 'NULL';
    // Đảm bảo giá là số thập phân hợp lệ
    $price = $price !== '' ? floatval(sanitize($price)) : 0;
    $description = $description !== '' ? "'" . sanitize($description) . "'" : 'NULL';
    $image = $image !== '' ? "'" . sanitize($image) . "'" : 'NULL';
    
    // Xây dựng câu truy vấn với xử lý NULL cho các trường cho phép
    $query = "INSERT INTO thucung (TenThuCung, Loai, Giong, Tuoi, GioiTinh, GiaBan, MoTa, HinhAnh) 
              VALUES ('$name', '$type', '$breed', $age, $gender, $price, $description, $image)";
    
    // Log câu truy vấn để debug
    error_log("SQL Query: " . $query);
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        return mysqli_insert_id($conn);
    } else {
        // Log lỗi SQL cụ thể
        error_log("MySQL Error in addPet: " . mysqli_error($conn));
        return false;
    }
}

// Update pet
function updatePet($id, $name, $type, $breed, $age, $gender, $price, $description, $image) {
    global $conn;
    
    $id = sanitize($id);
    $name = sanitize($name);
    $type = sanitize($type);
    $breed = sanitize($breed);
    $age = sanitize($age);
    $gender = sanitize($gender);
    $price = sanitize($price);
    $description = sanitize($description);
    
    // Check if image is provided, if not, keep the existing image
    $image_query = "";
    if (!empty($image)) {
        $image = sanitize($image);
        $image_query = ", HinhAnh = '$image'";
    }
    
    $query = "UPDATE thucung 
              SET TenThuCung = '$name', Loai = '$type', Giong = '$breed', 
                  Tuoi = '$age', GioiTinh = '$gender', GiaBan = '$price', 
                  MoTa = '$description'$image_query 
              WHERE MaThuCung = '$id'";
    
    return mysqli_query($conn, $query);
}

// Delete pet
function deletePet($id) {
    global $conn;
    $id = sanitize($id);
    
    $query = "DELETE FROM thucung WHERE MaThuCung = '$id'";
    return mysqli_query($conn, $query);
}  

// ======== Tìm kiếm =======

function searchProducts($searchTerm) {
    global $conn;
    
    $searchTerm = sanitize_input($searchTerm);
    
    $query = "SELECT * FROM sanpham WHERE 
              TenSP LIKE '%$searchTerm%' OR 
              LoaiSP LIKE '%$searchTerm%' OR 
              MoTa LIKE '%$searchTerm%' 
              ORDER BY MaSP DESC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        error_log("MySQL Error in searchProducts: " . mysqli_error($conn));
        return array();
    }
    
    $products = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    
    return $products;
}

function searchPets($searchTerm) {
    global $conn;
    
    $searchTerm = sanitize_input($searchTerm);
    
    $query = "SELECT * FROM thucung WHERE 
              TenThuCung LIKE '%$searchTerm%' OR 
              Loai LIKE '%$searchTerm%' OR 
              Giong LIKE '%$searchTerm%' OR 
              GioiTinh LIKE '%$searchTerm%' 
              ORDER BY MaThuCung DESC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        error_log("MySQL Error in searchPets: " . mysqli_error($conn));
        return array();
    }
    
    $pets = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $pets[] = $row;
    }
    
    return $pets;
}

// Helper function to sanitize inputs
function sanitize_input($input) {
    global $conn;
    $input = trim($input);
    $input = mysqli_real_escape_string($conn, $input);
    return $input;
}

// =============== Người dùng ===============

// Lấy danh sách người dùng
function getAllUsers() {
    global $conn;
    $query = "SELECT * FROM nguoidung ORDER BY NgayThamGia DESC";
    $result = mysqli_query($conn, $query);
    
    $users = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }
    
    return $users;
}

// Lấy người dùng theo ID
function getUserById($id) {
    global $conn;
    $id = sanitize($id); // Assuming sanitize function is available from admin_functions.php
    
    $query = "SELECT * FROM nguoidung WHERE ID_ND = '$id'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Thêm người dùng mới
function addUser($name, $email, $password, $role) {
    global $conn;
    
    $name = sanitize($name);
    $email = sanitize($email);
    $role = sanitize($role);
    
    // Check if email already exists
    $checkQuery = "SELECT * FROM nguoidung WHERE Email = '$email'";
    $checkResult = mysqli_query($conn, $checkQuery);
    
    if (mysqli_num_rows($checkResult) > 0) {
        return ['status' => 'error', 'message' => 'Email đã tồn tại trong hệ thống!'];
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Current timestamp
    $date = date('Y-m-d H:i:s');
    
    $query = "INSERT INTO nguoidung (HoTen, Email, MatKhau, VaiTro, NgayThamGia) 
              VALUES ('$name', '$email', '$hashedPassword', '$role', '$date')";
    
    if (mysqli_query($conn, $query)) {
        return ['status' => 'success', 'message' => 'Thêm người dùng thành công!', 'id' => mysqli_insert_id($conn)];
    } else {
        error_log("MySQL Error: " . mysqli_error($conn));
        return ['status' => 'error', 'message' => 'Không thể thêm người dùng: ' . mysqli_error($conn)];
    }
}

// Cập nhật người dùng
function updateUser($id, $name, $email, $password, $role) {
    global $conn;
    
    $id = sanitize($id);
    $name = sanitize($name);
    $email = sanitize($email);
    $role = sanitize($role);
    
    // Check if email exists for other users
    $checkQuery = "SELECT * FROM nguoidung WHERE Email = '$email' AND ID_ND != '$id'";
    $checkResult = mysqli_query($conn, $checkQuery);
    
    if (mysqli_num_rows($checkResult) > 0) {
        return ['status' => 'error', 'message' => 'Email đã tồn tại trong hệ thống!'];
    }
    
    // Prepare query base
    $query = "UPDATE nguoidung SET HoTen = '$name', Email = '$email', VaiTro = '$role'";
    
    // Add password to query if provided
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query .= ", MatKhau = '$hashedPassword'";
    }
    
    $query .= " WHERE ID_ND = '$id'";
    
    if (mysqli_query($conn, $query)) {
        return ['status' => 'success', 'message' => 'Cập nhật người dùng thành công!'];
    } else {
        error_log("MySQL Error: " . mysqli_error($conn));
        return ['status' => 'error', 'message' => 'Không thể cập nhật người dùng: ' . mysqli_error($conn)];
    }
}

// Xóa người dùng
function deleteUser($id) {
    global $conn;
    $id = sanitize($id);
    
    // Check if this is the last admin
    $checkQuery = "SELECT COUNT(*) as admin_count FROM nguoidung WHERE VaiTro = 'Admin'";
    $checkResult = mysqli_query($conn, $checkQuery);
    $adminCount = mysqli_fetch_assoc($checkResult)['admin_count'];
    
    $userQuery = "SELECT VaiTro FROM nguoidung WHERE ID_ND = '$id'";
    $userResult = mysqli_query($conn, $userQuery);
    $userRole = mysqli_fetch_assoc($userResult)['VaiTro'];
    
    if ($adminCount <= 1 && $userRole == 'Admin') {
        return ['status' => 'error', 'message' => 'Không thể xóa người dùng admin cuối cùng!'];
    }
    
    // Kiểm tra xem người dùng có trong bảng khachhang không và xóa nếu có
    $checkCustomerQuery = "SELECT COUNT(*) as customer_count FROM khachhang WHERE ID_ND = '$id'";
    $checkCustomerResult = mysqli_query($conn, $checkCustomerQuery);
    $customerCount = mysqli_fetch_assoc($checkCustomerResult)['customer_count'];
    
    // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
    mysqli_begin_transaction($conn);
    
    try {
        // Nếu người dùng là khách hàng, xóa khách hàng trước
        if ($customerCount > 0) {
            $deleteCustomerQuery = "DELETE FROM khachhang WHERE ID_ND = '$id'";
            if (!mysqli_query($conn, $deleteCustomerQuery)) {
                throw new Exception(mysqli_error($conn));
            }
        }
        
        // Sau đó xóa người dùng
        $deleteUserQuery = "DELETE FROM nguoidung WHERE ID_ND = '$id'";
        if (!mysqli_query($conn, $deleteUserQuery)) {
            throw new Exception(mysqli_error($conn));
        }
        
        // Nếu mọi thứ thành công, commit transaction
        mysqli_commit($conn);
        return ['status' => 'success', 'message' => 'Xóa người dùng thành công!'];
    } catch (Exception $e) {
        // Nếu có lỗi, rollback transaction
        mysqli_rollback($conn);
        error_log("MySQL Error: " . $e->getMessage());
        return ['status' => 'error', 'message' => 'Không thể xóa người dùng: ' . $e->getMessage()];
    }
}

// Tìm kiếm người dùng
function searchUsers($searchTerm) {
    global $conn;
    
    $searchTerm = sanitize($searchTerm);
    
    $query = "SELECT * FROM nguoidung WHERE 
              HoTen LIKE '%$searchTerm%' OR 
              Email LIKE '%$searchTerm%' OR 
              VaiTro LIKE '%$searchTerm%' 
              ORDER BY NgayThamGia DESC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        error_log("MySQL Error in searchUsers: " . mysqli_error($conn));
        return array();
    }
    
    $users = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    
    return $users;
}



// ==================== XỬ LÝ GỬI FORM ====================

// Xử lý việc gửi mẫu sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_action'])) {
    $action = $_POST['product_action'];
    
    // Add product
    if ($action === 'add') {
        $name = $_POST['product_name'] ?? '';
        $type = $_POST['product_type'] ?? '';
        $price = floatval($_POST['product_price'] ?? 0);
        $discount = intval($_POST['product_discount'] ?? 0);
        $description = $_POST['product_description'] ?? '';
        
        $image = '';
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $image = basename($_FILES['product_image']['name']);
        }
        
        $result = addProduct($name, $type, $price, $discount, $description, $image);
        if ($result !== false) {  // Thay vì if ($result)
            $response = ['status' => 'success', 'message' => 'Sản phẩm đã được thêm thành công!'];
        } else {
            $response = ['status' => 'error', 'message' => 'Không thể thêm sản phẩm: ' . mysqli_error($conn)];
        }
        
        echo json_encode($response);
        exit;
    }
    
    // Edit product
    else if ($action === 'edit') {
        $id = $_POST['product_id'] ?? '';
        $name = $_POST['product_name'] ?? '';
        $type = $_POST['product_type'] ?? '';
        $price = floatval($_POST['product_price'] ?? 0);
        $discount = intval($_POST['product_discount'] ?? 0);
        $description = $_POST['product_description'] ?? '';
        
        $image = '';
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $image = basename($_FILES['product_image']['name']);
        }
        
        $result = updateProduct($id, $name, $type, $price, $discount, $description, $image);
        if ($result) {
            $response = ['status' => 'success', 'message' => 'Sản phẩm đã được cập nhật thành công!'];
        } else {
            $response = ['status' => 'error', 'message' => 'Không thể cập nhật sản phẩm: ' . mysqli_error($conn)];
        }
        
        echo json_encode($response);
        exit;
    }
    
    // Delete product
    else if ($action === 'delete') {
        $id = $_POST['product_id'] ?? '';
        
        $result = deleteProduct($id);
        if ($result) {
            $response = ['status' => 'success', 'message' => 'Sản phẩm đã được xóa thành công!'];
        } else {
            $response = ['status' => 'error', 'message' => 'Không thể xóa sản phẩm: ' . mysqli_error($conn)];
        }
        
        echo json_encode($response);
        exit;
    }
}

// Xử lý việc gửi thú cưng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pet_action'])) {
    $action = $_POST['pet_action'];
    
    // Add pet
    if ($action === 'add') {
        // Lấy dữ liệu từ form
        $name = $_POST['pet_name'] ?? '';
        $type = $_POST['pet_type'] ?? '';
        $breed = $_POST['pet_breed'] ?? '';
        $age = $_POST['pet_age'] ?? '';
        $gender = $_POST['pet_gender'] ?? '';
        $price = $_POST['pet_price'] ?? 0;
        $description = $_POST['pet_description'] ?? '';
        
        // Xử lý ảnh
        $image = '';
        if (isset($_FILES['pet_image']) && $_FILES['pet_image']['error'] === UPLOAD_ERR_OK) {
            $image = basename($_FILES['pet_image']['name']);
        }
        
        // Kiểm tra dữ liệu bắt buộc
        if (empty($name) || empty($type) || empty($breed) || empty($price)) {
            $response = ['status' => 'error', 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc'];
            echo json_encode($response);
            exit;
        }
        
        // Thêm thú cưng
        $result = addPet($name, $type, $breed, $age, $gender, $price, $description, $image);
        
        if ($result !== false) {
            $response = ['status' => 'success', 'message' => 'Thú cưng đã được thêm thành công!'];
        } else {
            $response = ['status' => 'error', 'message' => 'Không thể thêm thú cưng: ' . mysqli_error($conn)];
        }
        
        echo json_encode($response);
        exit;
    }
    
    // Edit pet
    else if ($action === 'edit') {
        $id = $_POST['pet_id'] ?? '';
        $name = $_POST['pet_name'] ?? '';
        $type = $_POST['pet_type'] ?? '';
        $breed = $_POST['pet_breed'] ?? '';
        $age = $_POST['pet_age'] ?? '';
        $gender = $_POST['pet_gender'] ?? '';
        $price = $_POST['pet_price'] ?? 0;
        $description = $_POST['pet_description'] ?? '';
        
        $image = '';
        if (isset($_FILES['pet_image']) && $_FILES['pet_image']['error'] === UPLOAD_ERR_OK) {
            $image = basename($_FILES['pet_image']['name']);
        }
        
        $result = updatePet($id, $name, $type, $breed, $age, $gender, $price, $description, $image);
        if ($result) {
            $response = ['status' => 'success', 'message' => 'Thú cưng đã được cập nhật thành công!'];
        } else {
            $response = ['status' => 'error', 'message' => 'Không thể cập nhật thú cưng: ' . mysqli_error($conn)];
        }
        
        echo json_encode($response);
        exit;
    }
    
    // Delete pet
    else if ($action === 'delete') {
        $id = $_POST['pet_id'] ?? '';
        
        $result = deletePet($id);
        if ($result) {
            $response = ['status' => 'success', 'message' => 'Thú cưng đã được xóa thành công!'];
        } else {
            $response = ['status' => 'error', 'message' => 'Không thể xóa thú cưng: ' . mysqli_error($conn)];
        }
        
        echo json_encode($response);
        exit;
    }
}

// Xử lý việc gửi mẫu người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_action'])) {
    $action = $_POST['user_action'];
    
    // Add user
    if ($action === 'add') {
        $name = $_POST['user_name'] ?? '';
        $email = $_POST['user_email'] ?? '';
        $password = $_POST['user_password'] ?? '';
        $role = $_POST['user_role'] ?? 'Customer';
        
        if (empty($name) || empty($email) || empty($password)) {
            $response = ['status' => 'error', 'message' => 'Vui lòng điền đầy đủ thông tin người dùng!'];
        } else {
            $response = addUser($name, $email, $password, $role);
        }
        
        echo json_encode($response);
        exit;
    }
    
    // Edit user
    else if ($action === 'edit') {
        $id = $_POST['user_id'] ?? '';
        $name = $_POST['user_name'] ?? '';
        $email = $_POST['user_email'] ?? '';
        $password = $_POST['user_password'] ?? '';
        $role = $_POST['user_role'] ?? 'Customer';
        
        if (empty($name) || empty($email) || empty($id)) {
            $response = ['status' => 'error', 'message' => 'Thông tin người dùng không đầy đủ!'];
        } else {
            $response = updateUser($id, $name, $email, $password, $role);
        }
        
        echo json_encode($response);
        exit;
    }
    
    // Delete user
    else if ($action === 'delete') {
        $id = $_POST['user_id'] ?? '';
        
        if (empty($id)) {
            $response = ['status' => 'error', 'message' => 'ID người dùng không hợp lệ!'];
        } else {
            $response = deleteUser($id);
        }
        
        echo json_encode($response);
        exit;
    }
}

// Tải dữ liệu để hiển thị trong bảng.
$products = getAllProducts();
$pets = getAllPets();
?>