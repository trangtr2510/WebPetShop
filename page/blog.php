<?php
// Database connection
session_start();
require_once '../config/connectDB.php';
// Check if user is logged in
if (!isset($_SESSION['ID_ND'])) {
    header("Location: login.php");
    exit();
}

// Function to fetch blog posts from database
function getBlogPosts($conn) {
    $sql = "SELECT * FROM blog ORDER BY date DESC";
    $result = $conn->query($sql);
    
    $posts = array();
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
    }
    return $posts;
}

// Function to get latest posts for sidebar
function getLatestPosts($conn, $limit = 4) {
    $sql = "SELECT id, title, image, date FROM blog ORDER BY date DESC LIMIT " . $limit;
    $result = $conn->query($sql);
    
    $posts = array();
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
    }
    return $posts;
}

// Function to update blog database from Nutrience website
function updateBlogFromNutrience($conn) {
    $nutrienceUrl = "https://nutrience.vn/blogs/news";
    
    // Initialize cURL with proper settings
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $nutrienceUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For HTTPS
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'); // Mimic a browser
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Set a reasonable timeout
    
    $html = curl_exec($ch);
    
    if(curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return false;
    }
    
    // Check if we got a valid response
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if($httpCode != 200) {
        error_log('HTTP Error: ' . $httpCode);
        curl_close($ch);
        return false;
    }
    
    curl_close($ch);
    
    // Check if we actually got content
    if(empty($html)) {
        error_log('Empty response from Nutrience website');
        return false;
    }
    
    // Use DOMDocument for more reliable HTML parsing
    $blogPosts = extractBlogPostsUsingDOM($html);
    
    if(empty($blogPosts)) {
        error_log('No blog posts extracted from Nutrience website');
        return false;
    }
    
    // Update database with extracted blog posts
    $insertCount = 0;
    foreach($blogPosts as $post) {
        // Check if post already exists
        $sql = "SELECT id FROM blog WHERE url = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $post['url']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows == 0) {
            // Insert new post
            $sql = "INSERT INTO blog (title, excerpt, content, image, author, date, url) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", 
                $post['title'], 
                $post['excerpt'], 
                $post['content'], 
                $post['image'], 
                $post['author'], 
                $post['date'], 
                $post['url']
            );
            if ($stmt->execute()) {
                $insertCount++;
            } else {
                error_log('Error inserting blog post: ' . $stmt->error);
            }
        }
    }
    
    error_log('Added ' . $insertCount . ' new blog posts from Nutrience website');
    return true;
}

// Use DOMDocument for more reliable HTML parsing
function extractBlogPostsUsingDOM($html) {
    $posts = array();
    
    // Create a new DOM Document
    $dom = new DOMDocument();
    
    // Suppress warnings for malformed HTML
    libxml_use_internal_errors(true);
    
    // Load the HTML
    $dom->loadHTML($html);
    
    // Clear errors
    libxml_clear_errors();
    
    // Create a new DOMXPath object
    $xpath = new DOMXPath($dom);
    
    // Find all article elements (adjust selectors based on Nutrience's actual HTML structure)
    // These XPath queries are examples and need to be adjusted based on the actual structure
    
    // Get all articles/blog posts
    $articleNodes = $xpath->query('//article[contains(@class, "blog-post")] | //div[contains(@class, "article")] | //div[contains(@class, "post")]');
    
    // If we don't find articles with the above selectors, try some alternatives
    if ($articleNodes->length == 0) {
        $articleNodes = $xpath->query('//div[contains(@class, "blog-card")] | //div[contains(@class, "post-item")] | //div[contains(@class, "blog-item")]');
    }
    
    // If we still don't find any articles, look for standard HTML patterns
    if ($articleNodes->length == 0) {
        $articleNodes = $xpath->query('//div[.//h2 or .//h3][.//img][.//p]');
    }
    
    // Process each article
    foreach ($articleNodes as $article) {
        // Extract title (try different selectors)
        $title = '';
        $titleNode = $xpath->query('.//h2 | .//h3 | .//div[contains(@class, "title")]', $article)->item(0);
        if ($titleNode) {
            $title = trim($titleNode->textContent);
        }
        
        // Skip if no title found
        if (empty($title)) continue;
        
        // Extract image URL
        $image = '';
        $imgNode = $xpath->query('.//img', $article)->item(0);
        if ($imgNode && $imgNode->hasAttribute('src')) {
            $tempImage = $imgNode->getAttribute('src');
        
            // Kiểm tra nếu URL bắt đầu bằng "https://file.hstatic.net/"
            if (strpos($tempImage, 'https://file.hstatic.net/') === 0) {
                $image = $tempImage;
            }
        }
        
        // Extract URL
        $url = '';
        $linkNode = $xpath->query('.//a[@href]', $article)->item(0);
        if ($linkNode) {
            $url = $linkNode->getAttribute('href');
            
            // Handle relative URLs
            if (strpos($url, 'http') !== 0) {
                $url = 'https://nutrience.vn' . (strpos($url, '/') === 0 ? '' : '/') . $url;
            }
        }
        
        // Extract excerpt/content
        $excerpt = '';
        $contentNode = $xpath->query('.//p', $article)->item(0);
        if ($contentNode) {
            $excerpt = trim($contentNode->textContent);
        }
        
        // Extract date (this will vary based on Nutrience's format)
        $date = date('Y-m-d'); // Default to today
        $dateNode = $xpath->query('.//*[contains(@class, "date")] | .//*[contains(@class, "time")]', $article)->item(0);
        if ($dateNode) {
            $dateText = trim($dateNode->textContent);
            
            // Try to parse the date (adjust based on Nutrience's format)
            // The format might be something like "03 Tháng 06, 2021"
            if (preg_match('/(\d{2})[\s\.\/]Th[áa]ng[\s\.\/](\d{2})[\s\,\.\/](\d{4})/', $dateText, $matches)) {
                $day = $matches[1];
                $month = $matches[2];
                $year = $matches[3];
                $date = "$year-$month-$day";
            }
        }
        
        // Extract author
        $author = 'Nutrience Việt Nam'; // Default
        $authorNode = $xpath->query('.//*[contains(@class, "author")]', $article)->item(0);
        if ($authorNode) {
            $authorText = trim($authorNode->textContent);
            if (preg_match('/bởi\s*:\s*(.*?)$/i', $authorText, $matches)) {
                $author = trim($matches[1]);
            } else {
                $author = $authorText;
            }
        }
        
        // Only add posts with at least a title and URL
        if (!empty($title) && !empty($url)) {
            $posts[] = array(
                'title' => $title,
                'excerpt' => $excerpt,
                'content' => $dom->saveHTML($article), // Save the full HTML for the article
                'image' => $image,
                'author' => $author,
                'date' => $date,
                'url' => $url,
            );
        }
    }
    
    return $posts;
}

// Check if we need to update blog content
$updateNeeded = false;

// Check last update time from database instead of file
$sql = "SELECT value FROM config WHERE `key` = 'last_blog_update'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $lastUpdate = (int)$row['value'];
    
    // Get update interval from config
    $updateInterval = 86400; // Default: 24 hours
    $intervalResult = $conn->query("SELECT value FROM config WHERE `key` = 'blog_update_interval'");
    if ($intervalResult && $intervalResult->num_rows > 0) {
        $intervalRow = $intervalResult->fetch_assoc();
        $updateInterval = (int)$intervalRow['value'];
    }
    
    // Update if interval has passed
    if (time() - $lastUpdate > $updateInterval) {
        $updateNeeded = true;
    }
} else {
    // No last update record found, do an update
    $updateNeeded = true;
}

// Force update if requested via URL parameter
if (isset($_GET['force_update']) && $_GET['force_update'] == 1) {
    $updateNeeded = true;
}

// Update from Nutrience website if needed
if ($updateNeeded) {
    $updateSuccess = updateBlogFromNutrience($conn);
    
    // Update last update time in database
    if ($updateSuccess) {
        $now = time();
        $stmt = $conn->prepare("INSERT INTO config (`key`, `value`) VALUES ('last_blog_update', ?) ON DUPLICATE KEY UPDATE `value` = ?");
        $stmt->bind_param("ss", $now, $now);
        $stmt->execute();
    }
}

// Get blog posts for main display
$blogPosts = getBlogPosts($conn);

// Get latest posts for sidebar
$latestPosts = getLatestPosts($conn);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tin tức</title>
    <link rel="stylesheet" href="../fontawesome-free-6.4.2-web/css/all.min.css">
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/blog_style.css">
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

    <div class="container">
        
        <main class="main-content">
            <div class="blog-container">
                <h1 class="page-title">Tin tức</h1>
                
                <div class="blog-grid">
                    <?php if(count($blogPosts) > 0): ?>
                        <div class="featured-posts">
                            <?php 
                            for($i = 0; $i < min(2, count($blogPosts)); $i++): 
                                $post = $blogPosts[$i];
                            ?>
                            <div class="blog-card featured">
                                <a href="<?php echo htmlspecialchars($post['url']); ?>">
                                    <div class="blog-image">
                                        <?php if(!empty($post['image'])): ?>
                                            <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                        <?php else: ?>
                                            <div class="placeholder-image">Nutrience</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="blog-content">
                                        <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                                        <?php if(!empty($post['excerpt'])): ?>
                                            <p><?php echo htmlspecialchars(substr($post['excerpt'], 0, 150) . '...'); ?></p>
                                        <?php endif; ?>
                                        <div class="blog-meta">
                                            <span>bởi: <?php echo htmlspecialchars($post['author']); ?></span>
                                            <span class="post-date">Tin tức - <?php 
                                                $date = strtotime($post['date']);
                                                echo $date ? date('d.m.Y', $date) : 'N/A'; 
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php endfor; ?>
                        </div>
                        
                        <?php if(count($blogPosts) > 2): ?>
                        <div class="regular-posts">
                            <?php 
                            for($i = 2; $i < count($blogPosts); $i++): 
                                $post = $blogPosts[$i];
                            ?>
                            <div class="blog-card">
                                <a href="<?php echo htmlspecialchars($post['url']); ?>">
                                    <div class="blog-image">
                                        <?php if(!empty($post['image'])): ?>
                                            <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                        <?php else: ?>
                                            <div class="placeholder-image">Nutrience</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="blog-content">
                                        <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                                        <?php if(!empty($post['excerpt'])): ?>
                                            <p><?php echo htmlspecialchars(substr($post['excerpt'], 0, 100) . '...'); ?></p>
                                        <?php endif; ?>
                                        <div class="blog-meta">
                                            <span>bởi: <?php echo htmlspecialchars($post['author']); ?></span>
                                            <span class="post-date">Tin tức - <?php 
                                                $date = strtotime($post['date']);
                                                echo $date ? date('d.m.Y', $date) : 'N/A'; 
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php endfor; ?>
                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="no-posts">
                            <p>Không có bài viết nào. Vui lòng thử lại sau.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
           
        </main>
        
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