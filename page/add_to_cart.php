<?php
session_start();
include('../config/connectDB.php');
header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['ID_ND'])) {
    $response['message'] = 'Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng!';
    echo json_encode($response);
    exit();
}

if (isset($_POST['add_to_cart'])) {
    $conn->begin_transaction();
   
    try {
        $idND = $_SESSION['ID_ND'];
       
        // Lấy MaKH từ ID_ND
        $sql_getMaKH = "SELECT MaKH FROM khachhang WHERE ID_ND = ?";
        $stmt_getMaKH = $conn->prepare($sql_getMaKH);
        $stmt_getMaKH->bind_param("i", $idND);
        $stmt_getMaKH->execute();
        $result = $stmt_getMaKH->get_result();
       
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $maKH = $row['MaKH'];
        } else {
            throw new Exception('Không tìm thấy thông tin khách hàng!');
        }
        $stmt_getMaKH->close();

        // Xác định xem chúng ta đang thêm sản phẩm hay thú cưng
        $isProduct = isset($_POST['MaSP']) && !empty($_POST['MaSP']);
        $isPet = isset($_POST['MaThuCung']) && !empty($_POST['MaThuCung']);
        
        if (!$isProduct && !$isPet) {
            throw new Exception("Thiếu thông tin sản phẩm hoặc thú cưng!");
        }
        
        $soLuong = isset($_POST['SoLuong']) ? max(1, (int)$_POST['SoLuong']) : 1;
        
        if ($isProduct) {
            // Xử lý thêm sản phẩm
            $maSP = $_POST['MaSP'];
            
            // Kiểm tra sản phẩm đã tồn tại trong giỏ hàng chưa
            $sql_check = "SELECT SoLuong FROM giohang WHERE MaKH = ? AND MaSP = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("ss", $maKH, $maSP);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows == 0) {
                // Thêm mới sản phẩm vào giỏ hàng
                $sql_insert = "INSERT INTO giohang (MaKH, MaSP, SoLuong) VALUES (?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("ssi", $maKH, $maSP, $soLuong);
                $stmt_insert->execute();
                $stmt_insert->close();
            } else {
                // Cập nhật số lượng sản phẩm
                $sql_update = "UPDATE giohang SET SoLuong = SoLuong + ? WHERE MaKH = ? AND MaSP = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("iss", $soLuong, $maKH, $maSP);
                $stmt_update->execute();
                $stmt_update->close();
            }
            
            $stmt_check->close();
            $response['message'] = 'Thêm sản phẩm vào giỏ hàng thành công!';
        } else {
            // Xử lý thêm thú cưng
            $maThuCung = $_POST['MaThuCung'];
            
            // Kiểm tra thú cưng đã tồn tại trong giỏ hàng chưa
            $sql_check = "SELECT SoLuong FROM giohang WHERE MaKH = ? AND MaThuCung = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("ss", $maKH, $maThuCung);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows == 0) {
                // Thêm mới thú cưng vào giỏ hàng
                $sql_insert = "INSERT INTO giohang (MaKH, MaThuCung, SoLuong) VALUES (?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("ssi", $maKH, $maThuCung, $soLuong);
                $stmt_insert->execute();
                $stmt_insert->close();
            } else {
                // Cập nhật số lượng thú cưng
                $sql_update = "UPDATE giohang SET SoLuong = SoLuong + ? WHERE MaKH = ? AND MaThuCung = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("iss", $soLuong, $maKH, $maThuCung);
                $stmt_update->execute();
                $stmt_update->close();
            }
            
            $stmt_check->close();
            $response['message'] = 'Thêm thú cưng vào giỏ hàng thành công!';
        }
       
        $conn->commit();
        $response['success'] = true;
        
        // Lấy số lượng item trong giỏ hàng để trả về cho client
        $sql_count = "SELECT COUNT(*) as count FROM giohang WHERE MaKH = ?";
        $stmt_count = $conn->prepare($sql_count);
        $stmt_count->bind_param("s", $maKH);
        $stmt_count->execute();
        $result_count = $stmt_count->get_result();
        $row_count = $result_count->fetch_assoc();
        $response['cart_count'] = $row_count['count'];
        $stmt_count->close();
        
    } catch (Exception $e) {
        $conn->rollback();
        $response['message'] = 'Lỗi: ' . $e->getMessage();
    }
   
    mysqli_close($conn);
}

echo json_encode($response);
?>