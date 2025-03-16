<?php
session_start();
include('../config/connectDB.php');

if (!isset($_SESSION['ID_ND'])) {
    echo json_encode(['count' => 0, 'total' => '0K']);
    exit();
}

// Function to get cart count and total amount
function getCartInfo($conn, $idND) {
    $sql_getMaKH = "SELECT MaKH FROM khachhang WHERE ID_ND = ?";
    $stmt_getMaKH = $conn->prepare($sql_getMaKH);
    $stmt_getMaKH->bind_param("i", $idND);
    $stmt_getMaKH->execute();
    $result = $stmt_getMaKH->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $maKH = $row['MaKH'];

        // Count products and calculate their total price
        $sql_products = "SELECT 
                            COUNT(DISTINCT g.MaSP) AS ProductCount, 
                            SUM(g.SoLuong * s.GiaBan) AS ProductTotal 
                         FROM giohang g 
                         JOIN sanpham s ON g.MaSP = s.MaSP 
                         WHERE g.MaKH = ? AND g.MaSP IS NOT NULL";
        $stmt_products = $conn->prepare($sql_products);
        $stmt_products->bind_param("s", $maKH);
        $stmt_products->execute();
        $result_products = $stmt_products->get_result();
        $data_products = $result_products->fetch_assoc();
        
        $productCount = $data_products['ProductCount'] ?? 0;
        $productTotal = $data_products['ProductTotal'] ?? 0;

        // Count pets and calculate their total price
        $sql_pets = "SELECT 
                        COUNT(DISTINCT g.MaThuCung) AS PetCount, 
                        SUM(g.SoLuong * t.GiaBan) AS PetTotal 
                     FROM giohang g 
                     JOIN thucung t ON g.MaThuCung = t.MaThuCung 
                     WHERE g.MaKH = ? AND g.MaThuCung IS NOT NULL";
        $stmt_pets = $conn->prepare($sql_pets);
        $stmt_pets->bind_param("s", $maKH);
        $stmt_pets->execute();
        $result_pets = $stmt_pets->get_result();
        $data_pets = $result_pets->fetch_assoc();
        
        $petCount = $data_pets['PetCount'] ?? 0;
        $petTotal = $data_pets['PetTotal'] ?? 0;

        // Calculate totals
        $totalCount = $productCount + $petCount;
        $totalPrice = $productTotal + $petTotal;

        // Format price (e.g., 50,000 -> 50K)
        if ($totalPrice >= 1000000) {
            $formattedPrice = number_format($totalPrice / 1000000, 1, '.', '') . 'M';
        } elseif ($totalPrice >= 1000) {
            $formattedPrice = number_format($totalPrice / 1000, 0, '.', '') . 'K';
        } else {
            $formattedPrice = number_format($totalPrice, 0, '.', '');
        }

        return ['count' => $totalCount, 'total' => $formattedPrice];
    }

    return ['count' => 0, 'total' => '0K'];
}

// Get cart information
$cartInfo = getCartInfo($conn, $_SESSION['ID_ND']);

// Return JSON for AJAX or direct inclusion
echo json_encode($cartInfo);
?>