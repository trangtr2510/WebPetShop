<?php
session_start();
include('../config/connectDB.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kiểm tra đăng nhập
if (!isset($_SESSION['ID_ND'])) {
    echo json_encode(["success" => false, "message" => "Bạn cần đăng nhập để thực hiện chức năng này"]);
    exit();
}

// Debug received data
$debug = [
    'post_data' => $_POST,
    'session' => $_SESSION['ID_ND']
];

// Lấy MaKH từ ID_ND
$idND = $_SESSION['ID_ND'];
$sql_getMaKH = "SELECT MaKH FROM khachhang WHERE ID_ND = ?";
$stmt_getMaKH = $conn->prepare($sql_getMaKH);
$stmt_getMaKH->bind_param("i", $idND); // ID_ND is still an integer
$stmt_getMaKH->execute();
$result = $stmt_getMaKH->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $MaKH = $row['MaKH'];
    $debug['MaKH'] = $MaKH;
} else {
    echo json_encode([
        "success" => false, 
        "message" => "Không tìm thấy thông tin khách hàng!",
        "debug" => $debug
    ]);
    exit();
}
$stmt_getMaKH->close();

// Kiểm tra và xử lý dữ liệu đầu vào
if (!isset($_POST['item_id']) || !isset($_POST['item_type']) || !isset($_POST['quantity'])) {
    echo json_encode([
        "success" => false, 
        "message" => "Thiếu thông tin cần thiết!",
        "debug" => $debug
    ]);
    exit();
}

// Lấy dữ liệu từ request
$itemId = $_POST['item_id'];
$itemType = $_POST['item_type'];
$newQuantity = max(1, intval($_POST['quantity']));

// More detailed validation
if (empty($itemId) || $itemId === 'null' || $itemId === 'undefined') {
    echo json_encode([
        "success" => false, 
        "message" => "ID sản phẩm trống hoặc không hợp lệ!",
        "debug" => $debug
    ]);
    exit();
}

// No longer trying to convert itemId to integer
$debug['item_id'] = $itemId;

if ($itemType != 'product' && $itemType != 'pet') {
    echo json_encode([
        "success" => false, 
        "message" => "Loại sản phẩm không hợp lệ!",
        "debug" => $debug
    ]);
    exit();
}

$conn->begin_transaction();

try {
    // Check if the item exists in the cart first
    if ($itemType == 'product') {
        $checkSql = "SELECT COUNT(*) as count FROM giohang WHERE MaKH = ? AND MaSP = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ss", $MaKH, $itemId); // Changed to string binding
    } else {
        $checkSql = "SELECT COUNT(*) as count FROM giohang WHERE MaKH = ? AND MaThuCung = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ss", $MaKH, $itemId); // Changed to string binding
    }
    
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $checkRow = $checkResult->fetch_assoc();
    
    if ($checkRow['count'] == 0) {
        throw new Exception("Sản phẩm không tồn tại trong giỏ hàng!");
    }
    $checkStmt->close();
    
    // Cập nhật số lượng trong giỏ hàng
    if ($itemType == 'product') {
        $updateSql = "UPDATE giohang SET SoLuong = ? WHERE MaKH = ? AND MaSP = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("iss", $newQuantity, $MaKH, $itemId); // Changed binding types
    } else {
        $updateSql = "UPDATE giohang SET SoLuong = ? WHERE MaKH = ? AND MaThuCung = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("iss", $newQuantity, $MaKH, $itemId); // Changed binding types
    }
    
    $stmt->execute();
    
    if ($stmt->affected_rows == 0) {
        throw new Exception("Không thể cập nhật số lượng, vui lòng thử lại.");
    }
    $stmt->close();
    
    // Tính tổng giá trị giỏ hàng
    $cartSql = "SELECT 
                    SUM(CASE 
                        WHEN g.MaSP IS NOT NULL THEN s.GiaBan * g.SoLuong
                        WHEN g.MaThuCung IS NOT NULL THEN t.GiaBan * g.SoLuong
                        ELSE 0
                    END) as TotalAmount,
                    COUNT(*) as CartItemCount
                FROM giohang g
                LEFT JOIN sanpham s ON g.MaSP = s.MaSP
                LEFT JOIN thucung t ON g.MaThuCung = t.MaThuCung
                WHERE g.MaKH = ?";
    
    $cartStmt = $conn->prepare($cartSql);
    $cartStmt->bind_param("s", $MaKH); // Changed to string binding
    $cartStmt->execute();
    $cartResult = $cartStmt->get_result();
    $cartRow = $cartResult->fetch_assoc();
    
    $totalAmount = $cartRow['TotalAmount'] ?: 0;
    $cartItemCount = $cartRow['CartItemCount'] ?: 0;
    
    $cartStmt->close();
    $conn->commit();

    echo json_encode([
        "success" => true,
        "totalAmount" => number_format($totalAmount, 0, ',', '.'),
        "rawTotal" => $totalAmount,
        "cartItemCount" => $cartItemCount,
        "debug" => $debug
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage(),
        "debug" => $debug
    ]);
}
?>