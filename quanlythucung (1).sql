-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2025 at 11:44 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quanlythucung`
--

-- --------------------------------------------------------

--
-- Table structure for table `binhluan`
--

CREATE TABLE `binhluan` (
  `MaBL` varchar(10) NOT NULL,
  `MaKH` varchar(10) NOT NULL,
  `MaSP` varchar(10) DEFAULT NULL,
  `MaThuCung` varchar(10) DEFAULT NULL,
  `NoiDung` text NOT NULL,
  `NgayBL` datetime DEFAULT current_timestamp(),
  `DiemDanhGia` int(11) DEFAULT NULL CHECK (`DiemDanhGia` between 1 and 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `binhluan`
--

INSERT INTO `binhluan` (`MaBL`, `MaKH`, `MaSP`, `MaThuCung`, `NoiDung`, `NgayBL`, `DiemDanhGia`) VALUES
('1', 'KH000', 'SP003', NULL, 'Chó nhà mình rất thích sản phẩm này, ăn rất khỏe. Lông mượt hơn trước rất nhiều!', '2025-03-08 17:33:19', 5),
('2', 'KH001', 'SP004', NULL, 'Đẹp ', '2025-03-09 10:07:27', 4),
('3', 'KH001', 'SP004', NULL, 'Đẹp ', '2025-03-09 10:07:34', 4);

--
-- Triggers `binhluan`
--
DELIMITER $$
CREATE TRIGGER `tr_auto_increment_mabl` BEFORE INSERT ON `binhluan` FOR EACH ROW BEGIN
    DECLARE max_id INT;

    -- Lấy giá trị MaBL lớn nhất trong bảng
    SELECT COALESCE(MAX(MaBL), 0) + 1 INTO max_id FROM binhluan;

    -- Gán giá trị MaBL mới cho bản ghi đang chèn
    SET NEW.MaBL = max_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `author` varchar(100) DEFAULT 'Nutrience Việt Nam',
  `date` date NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog`
--

INSERT INTO `blog` (`id`, `title`, `excerpt`, `content`, `image`, `author`, `date`, `url`, `created_at`, `updated_at`) VALUES
(1, 'Tắm mèo đúng cách', 'Mèo và việc tắm rửa với nước trong tự nhiên gần như là không có nên cũng hiếm các bé mèo thích tắm. Và việc cố gắng dìm chúng vào bồn mà không tìm hiểu...', '<div class=\"list-article-content blog-posts row\">\n							\n							<!-- Begin: Nội dung blog -->      \n							\n							\n\n\n							<article class=\"article-loop col-md-6 col-6\">\n								<div class=\"article-inner\">				\n									<div class=\"article-image\">\n										<a href=\"/blogs/news/tam-meo-dung-cach\" class=\"blog-post-thumbnail\" title=\"Tắm mèo đúng cách\" rel=\"nofollow\">\n											<img class=\"lazyload\" data-src=\"//file.hstatic.net/1000290597/article/ovrs-cat-bath-shutterstock_640561972_5e5b443432024f94b74bef3180b535cd_1024x1024.jpg\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=\" alt=\"Tắm mèo đúng cách\">\n										</a>\n									</div>\n									<div class=\"article-detail\">	\n										<div class=\"article-title\">\n											<h3 class=\"post-title\">\n												<a href=\"/blogs/news/tam-meo-dung-cach\" title=\"Tắm mèo đúng cách\">Tắm mèo đúng cách</a>\n											</h3>\n										</div>		\n										\n										<p class=\"entry-content\">Mèo và việc tắm rửa với nước trong tự nhiên gần như là không có nên cũng hiếm các bé mèo thích tắm. Và việc cố gắng dìm chúng vào bồn mà không tìm hiểu...</p>\n										\n										<div class=\"article-post-meta\">   \n											<span class=\"author\">bởi: Nutrience Việt Nam</span>\n											<span class=\"date\">                \n												<time pubdate datetime=\"03 Tháng 06, 2021\">03 Tháng 06, 2021</time>\n											</span>\n												\n										</div>										\n									</div>\n								</div>						\n							</article>\n							\n							\n\n\n							<article class=\"article-loop col-md-6 col-6\">\n								<div class=\"article-inner\">				\n									<div class=\"article-image\">\n										<a href=\"/blogs/news/cho-bo-an-nguyen-nhan-va-cach-khac-phuc\" class=\"blog-post-thumbnail\" title=\"Chó bỏ ăn: Nguyên nhân và cách khắc phục\" rel=\"nofollow\">\n											<img class=\"lazyload\" data-src=\"//file.hstatic.net/1000290597/article/dog-wont-eat-750x500_d8fed2d3ec394a6eb410ab05a19088a2_grande.jpg\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=\" alt=\"Chó bỏ ăn: Nguyên nhân và cách khắc phục\">\n										</a>\n									</div>\n									<div class=\"article-detail\">	\n										<div class=\"article-title\">\n											<h3 class=\"post-title\">\n												<a href=\"/blogs/news/cho-bo-an-nguyen-nhan-va-cach-khac-phuc\" title=\"Chó bỏ ăn: Nguyên nhân và cách khắc phục\">Chó bỏ ăn: Nguyên nhân và cách khắc phục</a>\n											</h3>\n										</div>		\n										\n										<p class=\"entry-content\">Với những người nuôi chó, khi thấy chú chó của mình đang vui vẻ khỏe mạnh bỗng 1 ngày bỏ ăn, không ăn uống gì nhiều là 1 điều hết sức lo lắng. Việc chó bỏ ăn là...</p>\n										\n										<div class=\"article-post-meta\">   \n											<span class=\"author\">bởi: Nutrience Việt Nam</span>\n											<span class=\"date\">                \n												<time pubdate datetime=\"03 Tháng 06, 2021\">03 Tháng 06, 2021</time>\n											</span>\n												\n										</div>										\n									</div>\n								</div>						\n							</article>\n							\n							\n\n\n							<article class=\"article-loop col-md-6 col-6\">\n								<div class=\"article-inner\">				\n									<div class=\"article-image\">\n										<a href=\"/blogs/news/cac-thuc-pham-khong-nen-cho-cun-an\" class=\"blog-post-thumbnail\" title=\"Các thực phẩm không nên cho cún ăn\" rel=\"nofollow\">\n											<img class=\"lazyload\" data-src=\"//file.hstatic.net/1000290597/article/depositphotos_170095222_m-2015-1_e148625133cb4eb29c003a690fb1f9dd_grande.jpg\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=\" alt=\"Các thực phẩm không nên cho cún ăn\">\n										</a>\n									</div>\n									<div class=\"article-detail\">	\n										<div class=\"article-title\">\n											<h3 class=\"post-title\">\n												<a href=\"/blogs/news/cac-thuc-pham-khong-nen-cho-cun-an\" title=\"Các thực phẩm không nên cho cún ăn\">Các thực phẩm không nên cho cún ăn</a>\n											</h3>\n										</div>		\n										\n										<p class=\"entry-content\">Chó là loại động vật thông minh, thân thiện và gắn bó với con người nhất. Có rất nhiều giống chó trên thế giới được mọi người mua nuôi và chăm sóc. Tuy nhiên không...</p>\n										\n										<div class=\"article-post-meta\">   \n											<span class=\"author\">bởi: Nutrience Việt Nam</span>\n											<span class=\"date\">                \n												<time pubdate datetime=\"25 Tháng 05, 2021\">25 Tháng 05, 2021</time>\n											</span>\n												\n										</div>										\n									</div>\n								</div>						\n							</article>\n							\n							\n\n\n							<article class=\"article-loop col-md-6 col-6\">\n								<div class=\"article-inner\">				\n									<div class=\"article-image\">\n										<a href=\"/blogs/news/mot-so-loi-ich-thu-vi-den-tu-viec-nuoi-meo\" class=\"blog-post-thumbnail\" title=\"Một số lợi ích thú vị đến từ việc nuôi mèo\" rel=\"nofollow\">\n											<img class=\"lazyload\" data-src=\"//file.hstatic.net/1000290597/article/shutterstock-1030791046-148586_271a4b5fce8a44659c67465f8ea4f652_grande.jpg\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=\" alt=\"Một số lợi ích thú vị đến từ việc nuôi mèo\">\n										</a>\n									</div>\n									<div class=\"article-detail\">	\n										<div class=\"article-title\">\n											<h3 class=\"post-title\">\n												<a href=\"/blogs/news/mot-so-loi-ich-thu-vi-den-tu-viec-nuoi-meo\" title=\"Một số lợi ích thú vị đến từ việc nuôi mèo\">Một số lợi ích thú vị đến từ việc nuôi mèo</a>\n											</h3>\n										</div>		\n										\n										<p class=\"entry-content\">Mèo là loài động vật quen thuộc đối với nhiều gia đình. Thông thường, nhiều người nuôi mèo đơn giản vì sự đáng yêu của loài vật này. Tuy nhiên, bên cạnh sự đáng yêu đó,...</p>\n										\n										<div class=\"article-post-meta\">   \n											<span class=\"author\">bởi: Nutrience Việt Nam</span>\n											<span class=\"date\">                \n												<time pubdate datetime=\"21 Tháng 05, 2021\">21 Tháng 05, 2021</time>\n											</span>\n												\n										</div>										\n									</div>\n								</div>						\n							</article>\n							\n							\n\n\n							<article class=\"article-loop col-md-6 col-6\">\n								<div class=\"article-inner\">				\n									<div class=\"article-image\">\n										<a href=\"/blogs/news/cho-va-meo-co-the-song-chung\" class=\"blog-post-thumbnail\" title=\"Chó và Mèo có thể sống chung?\" rel=\"nofollow\">\n											<img class=\"lazyload\" data-src=\"//file.hstatic.net/1000290597/article/1_ac31b0173ee44d859ad9e935c32f8235_grande.png\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=\" alt=\"Chó và Mèo có thể sống chung?\">\n										</a>\n									</div>\n									<div class=\"article-detail\">	\n										<div class=\"article-title\">\n											<h3 class=\"post-title\">\n												<a href=\"/blogs/news/cho-va-meo-co-the-song-chung\" title=\"Chó và Mèo có thể sống chung?\">Chó và Mèo có thể sống chung?</a>\n											</h3>\n										</div>		\n										\n										<p class=\"entry-content\">Bạn đắn đo về việc nuôi thêm một chú chó nhưng lại sợ chú mèo ở nhà sẽ khó chịu? Hay chó mèo nhà bạn vẫn không ngừng đấu đá lẫn nhau? Quan niệm rằng...</p>\n										\n										<div class=\"article-post-meta\">   \n											<span class=\"author\">bởi: Nutrience Việt Nam</span>\n											<span class=\"date\">                \n												<time pubdate datetime=\"21 Tháng 05, 2021\">21 Tháng 05, 2021</time>\n											</span>\n												\n										</div>										\n									</div>\n								</div>						\n							</article>\n							\n							\n\n\n							<article class=\"article-loop col-md-6 col-6\">\n								<div class=\"article-inner\">				\n									<div class=\"article-image\">\n										<a href=\"/blogs/news/nuoi-cho-lam-thu-cung-va-nhung-loi-ich-duoc-khoa-hoc-chung-minh\" class=\"blog-post-thumbnail\" title=\"Nuôi chó làm thú cưng và những lợi ích được khoa học chứng minh\" rel=\"nofollow\">\n											<img class=\"lazyload\" data-src=\"//file.hstatic.net/1000290597/article/167417641_10158628038959504_5639219921170577573_n_c14cde858cc34d50acff72edb4f12b86_grande.jpg\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=\" alt=\"Nuôi chó làm thú cưng và những lợi ích được khoa học chứng minh\">\n										</a>\n									</div>\n									<div class=\"article-detail\">	\n										<div class=\"article-title\">\n											<h3 class=\"post-title\">\n												<a href=\"/blogs/news/nuoi-cho-lam-thu-cung-va-nhung-loi-ich-duoc-khoa-hoc-chung-minh\" title=\"Nuôi chó làm thú cưng và những lợi ích được khoa học chứng minh\">Nuôi chó làm thú cưng và những lợi ích được khoa học chứng minh</a>\n											</h3>\n										</div>		\n										\n										<p class=\"entry-content\">Nuôi chó làm thú cưng là điều tốt cho sức khỏe và hạnh phúc của bạn. Những người nuôi chó đều biết và cảm nhận được điều này mỗi ngày. Sự thoải mái và tình yêu thương...</p>\n										\n										<div class=\"article-post-meta\">   \n											<span class=\"author\">bởi: Nutrience Việt Nam</span>\n											<span class=\"date\">                \n												<time pubdate datetime=\"21 Tháng 05, 2021\">21 Tháng 05, 2021</time>\n											</span>\n												\n										</div>										\n									</div>\n								</div>						\n							</article>\n							\n							\n\n\n							<article class=\"article-loop col-md-6 col-6\">\n								<div class=\"article-inner\">				\n									<div class=\"article-image\">\n										<a href=\"/blogs/news/huong-dan-cach-cham-soc-cho-con-bi-mat-me\" class=\"blog-post-thumbnail\" title=\"Hướng dẫn cách chăm sóc chó con bị mất mẹ\" rel=\"nofollow\">\n											<img class=\"lazyload\" data-src=\"//file.hstatic.net/1000290597/article/cs_cun_con_mat_me_2fe2e5c4128949acb96e803f97abbab2_6b47099215f444e1aefbf7875aa43eed_grande.jpg\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=\" alt=\"Hướng dẫn cách chăm sóc chó con bị mất mẹ\">\n										</a>\n									</div>\n									<div class=\"article-detail\">	\n										<div class=\"article-title\">\n											<h3 class=\"post-title\">\n												<a href=\"/blogs/news/huong-dan-cach-cham-soc-cho-con-bi-mat-me\" title=\"Hướng dẫn cách chăm sóc chó con bị mất mẹ\">Hướng dẫn cách chăm sóc chó con bị mất mẹ</a>\n											</h3>\n										</div>		\n										\n										<p class=\"entry-content\">Bạn đang nuôi những chú chó sơ sinh bị mất mẹ? Bạn không biết chăm sóc chúng như thế nào là tốt nhất? Cách chăm sóc chó con bị mất mẹ như thế nào là...</p>\n										\n										<div class=\"article-post-meta\">   \n											<span class=\"author\">bởi: Nutrience Việt Nam</span>\n											<span class=\"date\">                \n												<time pubdate datetime=\"24 Tháng 11, 2020\">24 Tháng 11, 2020</time>\n											</span>\n												\n										</div>										\n									</div>\n								</div>						\n							</article>\n							\n							\n\n\n							<article class=\"article-loop col-md-6 col-6\">\n								<div class=\"article-inner\">				\n									<div class=\"article-image\">\n										<a href=\"/blogs/news/cach-keu-goi-quang-thuong\" class=\"blog-post-thumbnail\" title=\"Cách kêu gọi \'quàng thượng\'!\" rel=\"nofollow\">\n											<img class=\"lazyload\" data-src=\"//file.hstatic.net/1000290597/article/1_90682af635f042a1830da53dba80b22f_grande.png\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=\" alt=\"Cách kêu gọi \'quàng thượng\'!\">\n										</a>\n									</div>\n									<div class=\"article-detail\">	\n										<div class=\"article-title\">\n											<h3 class=\"post-title\">\n												<a href=\"/blogs/news/cach-keu-goi-quang-thuong\" title=\"Cách kêu gọi \'quàng thượng\'!\">Cách kêu gọi \'quàng thượng\'!</a>\n											</h3>\n										</div>		\n										\n										<p class=\"entry-content\">Trái với quan niệm thông thường, việc huấn luyện mèo không phải là điều bất khả thi! Mèo có thể thành thạo những kỹ năng nếu được huấn luyện đúng cách như chó, chúng có...</p>\n										\n										<div class=\"article-post-meta\">   \n											<span class=\"author\">bởi: Nutrience Việt Nam</span>\n											<span class=\"date\">                \n												<time pubdate datetime=\"13 Tháng 11, 2020\">13 Tháng 11, 2020</time>\n											</span>\n												\n										</div>										\n									</div>\n								</div>						\n							</article>\n							\n							\n						</div>', 'https://file.hstatic.net/1000290597/article/ovrs-cat-bath-shutterstock_640561972_5e5b443432024f94b74bef3180b535cd.jpg', 'Nutrience Việt Nam', '2025-03-10', 'https://nutrience.vn/blogs/news/tam-meo-dung-cach', '2025-03-10 04:04:06', '2025-03-10 04:06:41'),
(2, 'Chó bỏ ăn: Nguyên nhân và cách khắc phục', 'Với những người nuôi chó, khi thấy chú chó của mình đang vui vẻ khỏe mạnh bỗng 1 ngày bỏ ăn, không ăn uống gì nhiều là 1 điều hết sức lo lắng. Việc chó bỏ ăn là...', '<div class=\"article-inner\">				\n									<div class=\"article-image\">\n										<a href=\"/blogs/news/cho-bo-an-nguyen-nhan-va-cach-khac-phuc\" class=\"blog-post-thumbnail\" title=\"Chó bỏ ăn: Nguyên nhân và cách khắc phục\" rel=\"nofollow\">\n											<img class=\"lazyload\" data-src=\"//file.hstatic.net/1000290597/article/dog-wont-eat-750x500_d8fed2d3ec394a6eb410ab05a19088a2_grande.jpg\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=\" alt=\"Chó bỏ ăn: Nguyên nhân và cách khắc phục\">\n										</a>\n									</div>\n									<div class=\"article-detail\">	\n										<div class=\"article-title\">\n											<h3 class=\"post-title\">\n												<a href=\"/blogs/news/cho-bo-an-nguyen-nhan-va-cach-khac-phuc\" title=\"Chó bỏ ăn: Nguyên nhân và cách khắc phục\">Chó bỏ ăn: Nguyên nhân và cách khắc phục</a>\n											</h3>\n										</div>		\n										\n										<p class=\"entry-content\">Với những người nuôi chó, khi thấy chú chó của mình đang vui vẻ khỏe mạnh bỗng 1 ngày bỏ ăn, không ăn uống gì nhiều là 1 điều hết sức lo lắng. Việc chó bỏ ăn là...</p>\n										\n										<div class=\"article-post-meta\">   \n											<span class=\"author\">bởi: Nutrience Việt Nam</span>\n											<span class=\"date\">                \n												<time pubdate datetime=\"03 Tháng 06, 2021\">03 Tháng 06, 2021</time>\n											</span>\n												\n										</div>										\n									</div>\n								</div>', 'https://file.hstatic.net/1000290597/article/dog-wont-eat-750x500_d8fed2d3ec394a6eb410ab05a19088a2.jpg', 'Nutrience Việt Nam', '2025-03-10', 'https://nutrience.vn/blogs/news/cho-bo-an-nguyen-nhan-va-cach-khac-phuc', '2025-03-10 04:04:06', '2025-03-10 04:07:00'),
(3, 'Các thực phẩm không nên cho cún ăn', 'Chó là loại động vật thông minh, thân thiện và gắn bó với con người nhất. Có rất nhiều giống chó trên thế giới được mọi người mua nuôi và chăm sóc. Tuy nhiên không...', '<div class=\"article-inner\">				\n									<div class=\"article-image\">\n										<a href=\"/blogs/news/cac-thuc-pham-khong-nen-cho-cun-an\" class=\"blog-post-thumbnail\" title=\"Các thực phẩm không nên cho cún ăn\" rel=\"nofollow\">\n											<img class=\"lazyload\" data-src=\"//file.hstatic.net/1000290597/article/depositphotos_170095222_m-2015-1_e148625133cb4eb29c003a690fb1f9dd_grande.jpg\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=\" alt=\"Các thực phẩm không nên cho cún ăn\">\n										</a>\n									</div>\n									<div class=\"article-detail\">	\n										<div class=\"article-title\">\n											<h3 class=\"post-title\">\n												<a href=\"/blogs/news/cac-thuc-pham-khong-nen-cho-cun-an\" title=\"Các thực phẩm không nên cho cún ăn\">Các thực phẩm không nên cho cún ăn</a>\n											</h3>\n										</div>		\n										\n										<p class=\"entry-content\">Chó là loại động vật thông minh, thân thiện và gắn bó với con người nhất. Có rất nhiều giống chó trên thế giới được mọi người mua nuôi và chăm sóc. Tuy nhiên không...</p>\n										\n										<div class=\"article-post-meta\">   \n											<span class=\"author\">bởi: Nutrience Việt Nam</span>\n											<span class=\"date\">                \n												<time pubdate datetime=\"25 Tháng 05, 2021\">25 Tháng 05, 2021</time>\n											</span>\n												\n										</div>										\n									</div>\n								</div>', 'https://file.hstatic.net/1000290597/article/depositphotos_170095222_m-2015-1_e148625133cb4eb29c003a690fb1f9dd.jpg', 'Nutrience Việt Nam', '2025-03-10', 'https://nutrience.vn/blogs/news/cac-thuc-pham-khong-nen-cho-cun-an', '2025-03-10 04:04:06', '2025-03-10 04:07:15'),
(4, 'Một số lợi ích thú vị đến từ việc nuôi mèo', 'Mèo là loài động vật quen thuộc đối với nhiều gia đình. Thông thường, nhiều người nuôi mèo đơn giản vì sự đáng yêu của loài vật này. Tuy nhiên, bên cạnh sự đáng yêu đó,...', '<div class=\"article-inner\">				\n									<div class=\"article-image\">\n										<a href=\"/blogs/news/mot-so-loi-ich-thu-vi-den-tu-viec-nuoi-meo\" class=\"blog-post-thumbnail\" title=\"Một số lợi ích thú vị đến từ việc nuôi mèo\" rel=\"nofollow\">\n											<img class=\"lazyload\" data-src=\"//file.hstatic.net/1000290597/article/shutterstock-1030791046-148586_271a4b5fce8a44659c67465f8ea4f652_grande.jpg\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=\" alt=\"Một số lợi ích thú vị đến từ việc nuôi mèo\">\n										</a>\n									</div>\n									<div class=\"article-detail\">	\n										<div class=\"article-title\">\n											<h3 class=\"post-title\">\n												<a href=\"/blogs/news/mot-so-loi-ich-thu-vi-den-tu-viec-nuoi-meo\" title=\"Một số lợi ích thú vị đến từ việc nuôi mèo\">Một số lợi ích thú vị đến từ việc nuôi mèo</a>\n											</h3>\n										</div>		\n										\n										<p class=\"entry-content\">Mèo là loài động vật quen thuộc đối với nhiều gia đình. Thông thường, nhiều người nuôi mèo đơn giản vì sự đáng yêu của loài vật này. Tuy nhiên, bên cạnh sự đáng yêu đó,...</p>\n										\n										<div class=\"article-post-meta\">   \n											<span class=\"author\">bởi: Nutrience Việt Nam</span>\n											<span class=\"date\">                \n												<time pubdate datetime=\"21 Tháng 05, 2021\">21 Tháng 05, 2021</time>\n											</span>\n												\n										</div>										\n									</div>\n								</div>', 'https://file.hstatic.net/1000290597/article/shutterstock-1030791046-148586_271a4b5fce8a44659c67465f8ea4f652.jpg', 'Nutrience Việt Nam', '2025-03-10', 'https://nutrience.vn/blogs/news/mot-so-loi-ich-thu-vi-den-tu-viec-nuoi-meo', '2025-03-10 04:04:06', '2025-03-10 04:07:30'),
(5, 'Chó và Mèo có thể sống chung?', 'Bạn đắn đo về việc nuôi thêm một chú chó nhưng lại sợ chú mèo ở nhà sẽ khó chịu? Hay chó mèo nhà bạn vẫn không ngừng đấu đá lẫn nhau? Quan niệm rằng...', '<div class=\"article-inner\">				\n									<div class=\"article-image\">\n										<a href=\"/blogs/news/cho-va-meo-co-the-song-chung\" class=\"blog-post-thumbnail\" title=\"Chó và Mèo có thể sống chung?\" rel=\"nofollow\">\n											<img class=\"lazyload\" data-src=\"//file.hstatic.net/1000290597/article/1_ac31b0173ee44d859ad9e935c32f8235_grande.png\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=\" alt=\"Chó và Mèo có thể sống chung?\">\n										</a>\n									</div>\n									<div class=\"article-detail\">	\n										<div class=\"article-title\">\n											<h3 class=\"post-title\">\n												<a href=\"/blogs/news/cho-va-meo-co-the-song-chung\" title=\"Chó và Mèo có thể sống chung?\">Chó và Mèo có thể sống chung?</a>\n											</h3>\n										</div>		\n										\n										<p class=\"entry-content\">Bạn đắn đo về việc nuôi thêm một chú chó nhưng lại sợ chú mèo ở nhà sẽ khó chịu? Hay chó mèo nhà bạn vẫn không ngừng đấu đá lẫn nhau? Quan niệm rằng...</p>\n										\n										<div class=\"article-post-meta\">   \n											<span class=\"author\">bởi: Nutrience Việt Nam</span>\n											<span class=\"date\">                \n												<time pubdate datetime=\"21 Tháng 05, 2021\">21 Tháng 05, 2021</time>\n											</span>\n												\n										</div>										\n									</div>\n								</div>', 'https://file.hstatic.net/1000290597/article/1_ac31b0173ee44d859ad9e935c32f8235_grande.png', 'Nutrience Việt Nam', '2025-03-10', 'https://nutrience.vn/blogs/news/cho-va-meo-co-the-song-chung', '2025-03-10 04:04:06', '2025-03-10 04:07:46'),
(6, 'Nuôi chó làm thú cưng và những lợi ích được khoa học chứng minh', 'Nuôi chó làm thú cưng là điều tốt cho sức khỏe và hạnh phúc của bạn. Những người nuôi chó đều biết và cảm nhận được điều này mỗi ngày. Sự thoải mái và tình yêu thương...', '<div class=\"article-inner\">				\n									<div class=\"article-image\">\n										<a href=\"/blogs/news/nuoi-cho-lam-thu-cung-va-nhung-loi-ich-duoc-khoa-hoc-chung-minh\" class=\"blog-post-thumbnail\" title=\"Nuôi chó làm thú cưng và những lợi ích được khoa học chứng minh\" rel=\"nofollow\">\n											<img class=\"lazyload\" data-src=\"//file.hstatic.net/1000290597/article/167417641_10158628038959504_5639219921170577573_n_c14cde858cc34d50acff72edb4f12b86_grande.jpg\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=\" alt=\"Nuôi chó làm thú cưng và những lợi ích được khoa học chứng minh\">\n										</a>\n									</div>\n									<div class=\"article-detail\">	\n										<div class=\"article-title\">\n											<h3 class=\"post-title\">\n												<a href=\"/blogs/news/nuoi-cho-lam-thu-cung-va-nhung-loi-ich-duoc-khoa-hoc-chung-minh\" title=\"Nuôi chó làm thú cưng và những lợi ích được khoa học chứng minh\">Nuôi chó làm thú cưng và những lợi ích được khoa học chứng minh</a>\n											</h3>\n										</div>		\n										\n										<p class=\"entry-content\">Nuôi chó làm thú cưng là điều tốt cho sức khỏe và hạnh phúc của bạn. Những người nuôi chó đều biết và cảm nhận được điều này mỗi ngày. Sự thoải mái và tình yêu thương...</p>\n										\n										<div class=\"article-post-meta\">   \n											<span class=\"author\">bởi: Nutrience Việt Nam</span>\n											<span class=\"date\">                \n												<time pubdate datetime=\"21 Tháng 05, 2021\">21 Tháng 05, 2021</time>\n											</span>\n												\n										</div>										\n									</div>\n								</div>', 'https://file.hstatic.net/1000290597/article/167417641_10158628038959504_5639219921170577573_n_c14cde858cc34d50acff72edb4f12b86_grande.jpg', 'Nutrience Việt Nam', '2025-03-10', 'https://nutrience.vn/blogs/news/nuoi-cho-lam-thu-cung-va-nhung-loi-ich-duoc-khoa-hoc-chung-minh', '2025-03-10 04:04:06', '2025-03-10 04:07:55'),
(7, 'Hướng dẫn cách chăm sóc chó con bị mất mẹ', 'Bạn đang nuôi những chú chó sơ sinh bị mất mẹ? Bạn không biết chăm sóc chúng như thế nào là tốt nhất? Cách chăm sóc chó con bị mất mẹ như thế nào là...', '<div class=\"article-inner\">				\n									<div class=\"article-image\">\n										<a href=\"/blogs/news/huong-dan-cach-cham-soc-cho-con-bi-mat-me\" class=\"blog-post-thumbnail\" title=\"Hướng dẫn cách chăm sóc chó con bị mất mẹ\" rel=\"nofollow\">\n											<img class=\"lazyload\" data-src=\"//file.hstatic.net/1000290597/article/cs_cun_con_mat_me_2fe2e5c4128949acb96e803f97abbab2_6b47099215f444e1aefbf7875aa43eed_grande.jpg\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=\" alt=\"Hướng dẫn cách chăm sóc chó con bị mất mẹ\">\n										</a>\n									</div>\n									<div class=\"article-detail\">	\n										<div class=\"article-title\">\n											<h3 class=\"post-title\">\n												<a href=\"/blogs/news/huong-dan-cach-cham-soc-cho-con-bi-mat-me\" title=\"Hướng dẫn cách chăm sóc chó con bị mất mẹ\">Hướng dẫn cách chăm sóc chó con bị mất mẹ</a>\n											</h3>\n										</div>		\n										\n										<p class=\"entry-content\">Bạn đang nuôi những chú chó sơ sinh bị mất mẹ? Bạn không biết chăm sóc chúng như thế nào là tốt nhất? Cách chăm sóc chó con bị mất mẹ như thế nào là...</p>\n										\n										<div class=\"article-post-meta\">   \n											<span class=\"author\">bởi: Nutrience Việt Nam</span>\n											<span class=\"date\">                \n												<time pubdate datetime=\"24 Tháng 11, 2020\">24 Tháng 11, 2020</time>\n											</span>\n												\n										</div>										\n									</div>\n								</div>', 'https://file.hstatic.net/1000290597/article/cs_cun_con_mat_me_2fe2e5c4128949acb96e803f97abbab2_6b47099215f444e1aefbf7875aa43eed_grande.jpg', 'Nutrience Việt Nam', '2025-03-10', 'https://nutrience.vn/blogs/news/huong-dan-cach-cham-soc-cho-con-bi-mat-me', '2025-03-10 04:04:06', '2025-03-10 04:08:04'),
(8, 'Cách kêu gọi \'quàng thượng\'!', 'Trái với quan niệm thông thường, việc huấn luyện mèo không phải là điều bất khả thi! Mèo có thể thành thạo những kỹ năng nếu được huấn luyện đúng cách như chó, chúng có...', '<div class=\"article-inner\">				\n									<div class=\"article-image\">\n										<a href=\"/blogs/news/cach-keu-goi-quang-thuong\" class=\"blog-post-thumbnail\" title=\"Cách kêu gọi \'quàng thượng\'!\" rel=\"nofollow\">\n											<img class=\"lazyload\" data-src=\"//file.hstatic.net/1000290597/article/1_90682af635f042a1830da53dba80b22f_grande.png\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=\" alt=\"Cách kêu gọi \'quàng thượng\'!\">\n										</a>\n									</div>\n									<div class=\"article-detail\">	\n										<div class=\"article-title\">\n											<h3 class=\"post-title\">\n												<a href=\"/blogs/news/cach-keu-goi-quang-thuong\" title=\"Cách kêu gọi \'quàng thượng\'!\">Cách kêu gọi \'quàng thượng\'!</a>\n											</h3>\n										</div>		\n										\n										<p class=\"entry-content\">Trái với quan niệm thông thường, việc huấn luyện mèo không phải là điều bất khả thi! Mèo có thể thành thạo những kỹ năng nếu được huấn luyện đúng cách như chó, chúng có...</p>\n										\n										<div class=\"article-post-meta\">   \n											<span class=\"author\">bởi: Nutrience Việt Nam</span>\n											<span class=\"date\">                \n												<time pubdate datetime=\"13 Tháng 11, 2020\">13 Tháng 11, 2020</time>\n											</span>\n												\n										</div>										\n									</div>\n								</div>', 'https://file.hstatic.net/1000290597/article/1_90682af635f042a1830da53dba80b22f.png', 'Nutrience Việt Nam', '2025-03-10', 'https://nutrience.vn/blogs/news/cach-keu-goi-quang-thuong', '2025-03-10 04:04:06', '2025-03-10 04:08:12');

-- --------------------------------------------------------

--
-- Table structure for table `chitietdonhang`
--

CREATE TABLE `chitietdonhang` (
  `MaCTDH` varchar(10) NOT NULL,
  `MaDH` varchar(10) NOT NULL,
  `MaSP` varchar(10) NOT NULL,
  `MaThuCung` varchar(10) NOT NULL,
  `SoLuong` int(11) DEFAULT NULL CHECK (`SoLuong` > 0),
  `ThanhTien` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE `config` (
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`key`, `value`, `created_at`, `updated_at`) VALUES
('blog_update_interval', '86400', '2025-03-10 04:03:59', '2025-03-10 04:03:59'),
('last_blog_update', '1742053806', '2025-03-10 04:03:59', '2025-03-15 15:50:06');

-- --------------------------------------------------------

--
-- Table structure for table `customer_contacts`
--

CREATE TABLE `customer_contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_contacts`
--

INSERT INTO `customer_contacts` (`id`, `name`, `email`, `phone`, `message`, `created_at`, `is_read`, `read_at`, `notes`) VALUES
(1, 'Changg', 'changthui@gmail.com', '0987654321', 'Shop có cơ sở tại Hà Nội không?', '2025-03-09 13:33:04', 0, NULL, NULL),
(2, 'Changg', 'changthui@gmail.com', '0987654321', 'Shop có cơ sở tại Hà Nội không?', '2025-03-09 13:33:12', 0, NULL, NULL),
(3, 'changg', 'trnnguyenthien12345@gmail.com', '0397507701', 'Test', '2025-03-11 21:41:12', 0, NULL, NULL),
(4, 'changg', 'trnnguyenthien12345@gmail.com', '0397507701', 'Test', '2025-03-11 21:41:17', 0, NULL, NULL),
(5, 'changg', 'trnnguyenthien12345@gmail.com', '0397507701', 'Test', '2025-03-11 21:41:21', 0, NULL, NULL),
(6, 'changg', 'trnnguyenthien12345@gmail.com', '0397507701', 'Test', '2025-03-11 21:44:26', 0, NULL, NULL),
(7, 'changg', 'trnnguyenthien12345@gmail.com', '0397507701', 'Test', '2025-03-11 21:45:28', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `donhang`
--

CREATE TABLE `donhang` (
  `MaDH` varchar(10) NOT NULL,
  `MaKH` varchar(10) NOT NULL,
  `NgayDat` datetime DEFAULT current_timestamp(),
  `TongTien` decimal(18,2) NOT NULL,
  `TrangThai` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL CHECK (`TrangThai` in ('Chờ xử lý','Đang giao','Hoàn thành'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donhang`
--

INSERT INTO `donhang` (`MaDH`, `MaKH`, `NgayDat`, `TongTien`, `TrangThai`) VALUES
('DH92a3870b', 'KH001', '2025-03-15 22:13:46', 0.00, 'Chờ xử lý');

-- --------------------------------------------------------

--
-- Table structure for table `giohang`
--

CREATE TABLE `giohang` (
  `MaGH` int(11) NOT NULL,
  `MaKH` varchar(10) NOT NULL,
  `MaSP` varchar(10) DEFAULT NULL,
  `SoLuong` int(11) DEFAULT NULL CHECK (`SoLuong` > 0),
  `MaThuCung` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `giohang`
--

INSERT INTO `giohang` (`MaGH`, `MaKH`, `MaSP`, `SoLuong`, `MaThuCung`) VALUES
(24, 'KH002', 'SP001', 1, NULL),
(25, 'KH001', 'SP001', 1, NULL),
(26, 'KH001', 'SP026', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `khachhang`
--

CREATE TABLE `khachhang` (
  `MaKH` varchar(10) NOT NULL,
  `TenKH` varchar(100) NOT NULL,
  `SDT` varchar(20) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `MatKhau` varchar(255) NOT NULL,
  `HinhAnh` varchar(255) DEFAULT NULL,
  `ID_ND` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `khachhang`
--

INSERT INTO `khachhang` (`MaKH`, `TenKH`, `SDT`, `Email`, `DiaChi`, `MatKhau`, `HinhAnh`, `ID_ND`) VALUES
('KH000', 'Admin', NULL, 'changthui@gmail.com', NULL, '$2y$10$oJpuXEP5cpq/.TI1ge2tWeHGvAOLFbfHHN.PEjjM35zFYrDo1V/s.', NULL, 3),
('KH001', 'Changg', NULL, 'changthuii@gmail.com', NULL, '123', NULL, 4),
('KH002', 'changgg', NULL, 'trnnguyenthien12345@gmail.com', NULL, '123', NULL, 12);

-- --------------------------------------------------------

--
-- Table structure for table `nguoidung`
--

CREATE TABLE `nguoidung` (
  `ID_ND` int(10) UNSIGNED NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `MatKhau` varchar(255) NOT NULL,
  `VaiTro` enum('Admin','Customer') DEFAULT 'Customer',
  `NgayThamGia` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nguoidung`
--

INSERT INTO `nguoidung` (`ID_ND`, `HoTen`, `Email`, `MatKhau`, `VaiTro`, `NgayThamGia`) VALUES
(3, 'Changg', 'changthui@gmail.com', '123', 'Admin', '2025-03-08 09:37:40'),
(4, 'Changg', 'changthuii@gmail.com', '123', 'Customer', '2025-03-08 13:20:06'),
(12, 'changgg', 'trnnguyenthien12345@gmail.com', '123', 'Customer', '2025-03-12 08:39:56');

--
-- Triggers `nguoidung`
--
DELIMITER $$
CREATE TRIGGER `after_nguoidung_insert` AFTER INSERT ON `nguoidung` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    DECLARE next_makh VARCHAR(10);
    
    IF NEW.VaiTro = 'Customer' THEN
        -- Get the highest existing numeric part or use 0 if none exists
        SELECT IFNULL(MAX(CAST(SUBSTRING(MaKH, 3) AS UNSIGNED)), 0) INTO next_id 
        FROM KhachHang;
        
        -- Increment to get the next ID number
        SET next_id = next_id + 1;
        
        -- Format the new MaKH with leading zeros (KH001, KH002, etc.)
        SET next_makh = CONCAT('KH', LPAD(next_id, 3, '0'));
        
        -- Insert the new customer record WITH the ID_ND value
        INSERT INTO KhachHang (MaKH, TenKH, SDT, Email, DiaChi, MatKhau, HinhAnh, ID_ND)
        VALUES (next_makh, NEW.HoTen, NULL, NEW.Email, NULL, NEW.MatKhau, NULL, NEW.ID_ND);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sanpham`
--

CREATE TABLE `sanpham` (
  `MaSP` varchar(10) NOT NULL,
  `TenSP` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `LoaiSP` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `GiaBan` decimal(18,2) NOT NULL,
  `MoTa` text DEFAULT NULL,
  `HinhAnh` varchar(255) DEFAULT NULL,
  `GiamGia` int(11) NOT NULL DEFAULT 0,
  `GiaGoc` decimal(10,2) NOT NULL DEFAULT 0.00,
  `NgayThem` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sanpham`
--

INSERT INTO `sanpham` (`MaSP`, `TenSP`, `LoaiSP`, `GiaBan`, `MoTa`, `HinhAnh`, `GiamGia`, `GiaGoc`, `NgayThem`) VALUES
('SP001', 'Dogs Healthy Puppy Food', 'Thức ăn thú cưng', 150.00, 'Nutrience Infusion Healthy Puppy là thức ăn dành riêng cho chó con có thành phần gồm thịt gà tươi, \r\n  ngũ cốc nguyên hạt có hàm lượng đường huyết thấp, rau, trái cây và thực vật giàu chất dinh dưỡng.', 'dogs_food.webp', 0, 150.00, '2025-03-07 12:10:53'),
('SP002', 'Cats Healthy Puppy Food', 'Thức ăn thú cưng', 150.00, 'Nutrience Infusion Healthy Puppy là thức ăn dành riêng cho mèo con có thành phần gồm thịt gà tươi, \r\n  ngũ cốc nguyên hạt có hàm lượng đường huyết thấp, rau, trái cây và thực vật giàu chất dinh dưỡng.', 'cats_food.webp', 0, 150.00, '2025-03-05 12:10:53'),
('SP003', 'Small Dogs Food', 'Thức ăn thú cưng', 200.00, 'Thức ăn dành riêng cho chó nhỏ, giàu dinh dưỡng và hỗ trợ tiêu hóa.', 'feature_product-1.webp', 0, 200.00, '2025-03-06 12:10:53'),
('SP004', 'Bird Cage', 'Phụ kiện', 180.00, 'Lồng chim cao cấp, thiết kế thoải mái, dễ dàng vệ sinh.', 'feature_product-2.webp', 10, 200.00, '2025-03-07 12:10:53'),
('SP005', '12 Cans Pack Cats Food', 'Thức ăn thú cưng', 180.00, 'Bộ 12 lon thức ăn cho mèo, cung cấp đầy đủ vitamin và khoáng chất.', 'feature_product-3.webp', 0, 180.00, '2025-03-05 12:10:53'),
('SP006', 'Cat Activity Play Center', 'Phụ kiện', 149.00, 'Khu vui chơi dành cho mèo, giúp mèo thư giãn và rèn luyện thể chất.', 'feature_product-4.webp', 0, 149.00, '2025-03-08 12:10:53'),
('SP007', 'Lồng chim trắng', 'Phụ kiện', 100.00, 'Lồng chim trắng có giá đỡ, phù hợp cho chim cảnh', 'latest_product-1.webp', 50, 200.00, '2025-03-08 12:10:53'),
('SP008', 'Thức ăn cho chim', 'Thức ăn thú cưng', 180.00, 'Thức ăn dinh dưỡng cao cấp dành cho chim cảnh', 'latest_product-2.webp', 0, 180.00, '2025-03-08 12:10:53'),
('SP009', 'Thức ăn cho mèo', 'Thức ăn thú cưng', 100.00, 'Thức ăn bổ dưỡng cho mèo, giúp lông mượt và khỏe mạnh', 'latest_product-3.webp', 50, 200.00, '2025-03-08 12:10:53'),
('SP010', 'Thức ăn cho thỏ', 'Thức ăn thú cưng', 100.00, 'Thức ăn dành cho thỏ với thành phần tự nhiên', 'latest_product-4.webp', 50, 200.00, '2025-03-08 12:10:53'),
('SP011', 'Đồ chơi cho mèo', 'Phụ kiện ', 180.00, 'Đồ chơi dạng ống chui giúp mèo giải trí', 'latest_product-5.webp', 0, 180.00, '2025-03-08 12:10:53'),
('SP012', 'Thức ăn cho chó', 'Thức ăn thú cưng', 100.00, 'Bánh thưởng cho chó, hỗ trợ sức khỏe răng miệng', 'latest_product-6.webp', 50, 200.00, '2025-03-11 15:13:47'),
('SP013', 'NT Infusion', 'Thức ăn thú cưng', 160000.00, 'NT Infusion cho Chó kích thước vừa và lớn trưởng thành (Trên 10kg) - Thịt gà và rau củ quả tự nhiênn', 'catfood_product-1.webp', 20, 200000.00, '2025-03-11 09:27:58'),
('SP014', 'NT Subzero cho Mèo', 'Thức ăn thú cưng', 160000.00, 'Sản phẩm Nutrience Subzero Fraser Valley cho Mèo dùng được cho mọi giống mèo ở mọi độ tuổi (All life stages) và không chứa tinh bột ngũ cốc (Grain-free), sử dụng các nguồn nguyên liệu tự nhiên tươi sống của Canada như thịt gà tây, thịt gà cao cấp Canada, heo rừng, cá biển đại dương và hạt thịt gà tươi sấy lạnh để cho ra hương vị tuyệt hảo có thể khiến cho những chú mèo cưng kén ăn nhất cũng phải thèm ăn.', 'catfood_product-1.webp', 20, 200000.00, '2025-03-15 17:27:36'),
('SP015', 'NT Infusion cho Mèo con', 'Thức ăn thú cưng', 180000.00, 'Nutrience Infusion Healthy Kitten là thức ăn dành cho mèo con bao gồm thịt gà tươi thả vườn, không đông lạnh, ngũ cốc nguyên hạt có hàm lượng đường huyết thấp, rau củ quả và thực vật giàu chất dinh dưỡng, phát triển hệ miễn dịch, nguồn DHA tự nhiên hỗ trợ sự phát triển của não và mắt, hương vị thơm ngon, protein cao, bổ sung thêm prebiotics và probiotics tăng khả năng hấp thụ chất dinh dưỡng, bảo vệ và hỗ trợ hệ vi khuẩn đường ruột khỏe mạnh.', 'catfood_product-2.webp', 10, 200000.00, '2025-03-15 17:33:40'),
('SP016', 'NT Subzero cho Chó', 'Thức ăn thú cưng', 250000.00, 'Thức ăn cho chó Nutrience SubZero Fraser Valley được sản xuất tại Canada, chế biến từ gà và gà tây tươi không chứa hormone và kháng sinh từ Thung lũng Fraser của British Columbia và cá tự nhiên từ Bờ Tây Thái Bình Dương. Chỉ những loại protein chất lượng cao nhất từ các nguồn tin cậy mới được chọn cho công thức của chúng tôi.\r\n\r\nHạt thức ăn giàu protein của chúng tôi được kết hợp với Nutriboost - viên gà sống sấy thăng hoa, chế biến tối thiểu, được tẩm với vẹm xanh để hỗ trợ khớp tự nhiên, bí ngô tốt cho đường ruột, dầu gan cá tuyết giàu omega-3 và tảo Acadian hỗ trợ hệ miễn dịch.\r\n\r\nThức ăn này được bổ sung prebiotic và probiotic, tạo môi trường đường ruột khỏe mạnh cho chó của bạn. Hỗ trợ quan trọng này đảm bảo tiêu hóa và hấp thu dưỡng chất tối ưu. Sự có mặt của các axit béo omega-3 và omega-6 giúp duy trì làn da và bộ lông khỏe mạnh. Để tăng cường hệ miễn dịch cho chó, công thức này được bổ sung các vitamin và khoáng chất thiết yếu. Những dưỡng chất này hoạt động cùng nhau để hỗ trợ sức khỏe miễn dịch, cung cấp một lớp bảo vệ thêm chống lại các bệnh thông thường.\r\n\r\nHơn nữa, taurine, một thành phần quan trọng trong công thức của chúng tôi, đóng vai trò quan trọng trong việc hỗ trợ sức khỏe tim mạch và mắt. Công thức này không chứa lúa mì, đậu nành, ngô, sữa và trứng!', 'dogfood_product-2.webp', 0, 250000.00, '2025-03-15 17:40:36'),
('SP017', 'NT Infusion cho Mèo trưởng thành', 'Thức ăn thú cưng', 225000.00, 'Nutrience Infusion Adult Indoor là thức ăn cho mèo do Canada sản xuất riêng dành cho mèo trong nhà, sản phẩm là sự kết hợp từ thịt gà tươi, không bao giờ đông lạnh, ngũ cốc nguyên hạt có hàm lượng đường huyết thấp, rau, trái cây và các loại thực vật giàu chất dinh dưỡng. Sản phẩm mang đến sức khỏe toàn diện cho mèo.', 'catfood_product-3.webp', 10, 250000.00, '2025-03-16 06:23:44'),
('SP018', 'KHAY VỆ SINH TAI MÈO', 'Phụ kiện', 228000.00, 'Đuợc làm từ chất liệu tốt, bền, kích thước rộng thoải mái.\r\nKhay được thiết kế có vành xung quanh chậu ,để khi mèo bới cát không bị bắn ra ngoài ,nắp có thể tháo ra và vệ sinh hàng ngày\r\nKhay vệ sinh có chiều cao lý tưởng, cho phép con mèo của bạn nhảy vào, nhảy ra thoải mái mà không có phân dính quanh nhà.\r\nKhay chống lật khi mèo đứng lên đi vệ sinh', 'product_Accessories_1.png', 5, 240000.00, '2025-03-16 09:40:06'),
('SP019', 'LƯỢC CHẢI VE RẬN', 'Phụ kiện', 20000.00, 'Chấy rận thường là những côn trùng nhỏ bé sống trên da thú cưng của bạn. Những  ký sinh tự nhiên này ăn máu từ da .\r\n\r\nChải  lông thú cưng và kiểm tra gần da của chó mèo và dọc theo theo sống lưng xem có chấy rận bất kỳ nào không.\r\n\r\nCứ vừa chải vừa vạch lần lượt và kiểm tra tất cả các khu vực trên lưng thú cưng . Đặc biệt tập trung chú ý vào các khu vực như trong tai, phía sau tai, kẽ móng chân vì đó là địa điểm yêu thích chấy rận thường sinh sống vì khó bị phát hiện\r\n\r\nLược chải ve chấy rận như là cách để giảm bớt chấy rận cho thú cưng của bạn đem lại sự thoải mái cho thú cưng.', 'product_Accessories_2.png', 0, 20000.00, '2025-03-16 09:41:24'),
('SP020', 'KHAY VỆ SINH CON VỊT', 'Phụ kiện', 198000.00, 'TÊN SẢN PHẨM: Khay vệ sinh cho mèo hình vịt con đáng yêu cute phô mai que 36*45,5*21cm\r\nKÍCH THƯỚC: 36*45.5*21cm\r\n– Thiết kế theo hình chú vịt dễ thương, sang trọng\r\n– Khay được thiết kế có vành xung quanh chậu ,để khi mèo bới cát không bị bắn ra ngoài ,nắp có thể tháo ra và vệ sinh hàng ngày.\r\n– Tháo lắp dễ dàng, dễ vệ sinh\r\n– Khay vệ sinh có chiều cao lý tưởng, cho phép mèo của bạn nhảy vào, nhảy ra thoải mái.\r\n– Khay vệ sinh có tặng kèm xẻng.\r\n– Chất liệu : nhựa PP cao cấp, bền, đẹp', 'product_Accessories_3.png', 10, 220000.00, '2025-03-16 09:42:53'),
('SP021', 'Lồng tĩnh điện size M', 'Phụ kiện', 425000.00, 'Lồng tĩnh điện size M', 'product_Accessories_4.png', 15, 500000.00, '2025-03-16 09:44:53'),
('SP022', 'BÁT INOX IN HOA SIZE 22', 'Phụ kiện', 109250.00, 'BÁT INOX IN HOA SIZE 22', 'product_Accessories_5.png', 5, 115000.00, '2025-03-16 09:45:49'),
('SP023', 'Khăn tắm lớn có hộp', 'Phụ kiện', 60000.00, '[GIỚI THIỆU] Khăn siêu thấm hút cho chó mèo sau khi tắm Size nhỏ – kích thước: 22*32 cm [CÔNG DỤNG] dùng cho thú lơn hay nhỏ đều được, dùng được cho cả chó, mèo: – Khăn siêu thấm hút cho chó mèo sau khi tắm – Thấm cực nhiều nước, giúp nhanh chóng lau khô thú cưng – Đa năng, sử dụng nhiều mục đích, siêu bền, thời gian sử dụng lâu dài – Tiện dụng, dễ sử dụng Hướng Dẫn Sử Dụng : Khăn là chất liệu poly tổng hợp nên sẽ luôn ẩm nhẹ để mềm, trước khi sử dụng bạn xả 1 lần rồi vắt khô trước khi lau cho bé. Sau khi sử dụng thì vắt hết nước rồi bỏ vào túi nilon kín hoặc hộp kín ( không phơi trực tiếp với ánh nắng để độ bên khăn được đảm bảo)', 'product_Accessories_6.png', 0, 60000.00, '2025-03-16 09:47:16'),
('SP024', 'Cây lăn lông 15cm', 'Phụ kiện', 35000.00, 'THÔNG TIN SẢN PHẨM :\r\n\r\nLăn lông mini siêu nhỏ gọn tiện lợi, bạn có thể bỏ vào trong túi mang đi khắp nơi\r\n\r\nkích thước :15cm\r\n\r\nchiều dài cuộn giấy : 7cm\r\n\r\nNgoài tác dụng lấy hết lông chó mèo trên quần áo còn lấy được sạch bụi bẩn trên chăn , nệm , ghế salon, ghế ô tô,… dễ dàng loại bỏ bụi bẩn, thuận tiện cho những người đi làm, đi tiệc, họp hành chỉ cần lăn qua lăn lại con lăn trên quần áo sẽ sạch trong tích tắc', 'product_Accessories_7.png', 0, 35000.00, '2025-03-16 09:48:29'),
('SP025', 'Kìm Cắt Móng cho boss yêu', 'Phụ kiện', 55000.00, 'Kềm Bấm Cắt Móng An Toàn Cho Chó Mèo\r\n\r\nChúng ta hay lo nghĩ về móng của cún yêu sẽ cào vào bạn khi chơi đùa với chúng, hay những chú cún không tự biết mài móng, vì vậy bạn hãy yên tâm và sử dụng ngay kiềm cắt móng, để bạn có thể chơi đùa thoải mái mà không lo bị móng của cún cào vào người bạn. Khi cắt móng cho cún yêu, bạn phải chú ý cắt cẩn thận để không làm chúng chảy máu vì cắt quá sâu vào tủy. sau khi cắt xong, bạn dùng dũa để dũa cho móng êm và mịn đầu móng.\r\n\r\n– Thích hợp cho chó và mèo ở tất cả độ tuổi, kể cả chó mèo con.\r\n– Bảo vệ sức khỏe cho thú yêu và cho con người khỏi sợ móng thú cưng cào vào người.\r\n– Cắt được cho tất cả các loại móng của chó mèo lớn và nhỏ.\r\n– Lưỡi kìm bén giúp dễ dàng cắt móng cho cún cưng của bạn.\r\n– Chất liệu: Thép và nhựa cao cấp, không gỉ sét, không chứa độc hại, đảm bảo an toàn cho thú cưng của bạn.', 'product_Accessories_8.png', 0, 55000.00, '2025-03-16 09:50:03'),
('SP026', 'Dây vòng cổ 1.5', 'Phụ kiện', 60000.00, 'Công dụng và ưu điểm của sản phẩm:\r\nVòng cổ dây dắt với thiết kế dày dặn và chắc chắn, tạo cảm giác gọn gàng và thoải mái khi đeo lên cổ thú cưng.\r\nĐược làm bằng chất liệu vải dù cao cấp chịu được tác động vật lí\r\nKhông gây kích ứng cọ xát giúp giữ cưng luôn ở gần bạn, không lo lạc khi đi chơi.\r\nDễ dàng tháo mở và điều chỉnh kích thước dây nhanh chóng bởi móc khoá tiện lợi, chịu được va chạm.', 'product_Accessories_9.png', 0, 60000.00, '2025-03-16 09:51:28'),
('SP027', 'Thức ăn hamster Bucatstate H1', 'Thức ăn thú cưng', 114000.00, 'Thức ăn hamster Bucatstate H1', 'hamster_food_1.jpg', 5, 120000.00, '2025-03-16 09:54:52'),
('SP028', 'Thức ăn hỗn hợp dành cho Hamster', 'Thức ăn thú cưng', 25000.00, 'Thức ăn hỗn hợp dành cho Hamster', 'hamster_food_2.jpg', 0, 25000.00, '2025-03-16 09:56:02'),
('SP029', 'Hạt Zum - thức ăn cho Vẹt', 'Thức ăn thú cưng', 200000.00, 'Hạt zum nhập khẩu \r\nChiết xuất từ hoa quả sấy khô , đa vi lượng bao gồm các loại vitamin tổng hợp . Cung cấp cho vẹt đầy đủ dưỡng chất và ăn trong nhiều ngày\r\nĐặc biệt rất tốt cho vẹt non mới tập ăn và vẹt bố mẹ mớm thức ăn cho con\r\nthực phẩm dành cho tất cả các loại vẹt\r\nhàng nhập khẩu ', 'bird_food_1.webp', 0, 200000.00, '2025-03-16 09:59:09'),
('SP030', 'Xiên hoa quả, ngô, đồ ăn cho chim cảnh, vẹt lovebird, chào mào, chim khuyên', 'Phụ kiện', 2500.00, 'Xiên hoa quả cho chim - Tiện lợi - bền đẹp - Phù hợp các loại lồng.\r\n\r\n👉 Chất liệu: Nhựa \r\n\r\n👉 Xiên chuối dế là phụ kiện không thể thiếu trong 1 chiếc lồng. \r\n\r\n👉 Xiên chuối giúp cố định hoa quả giúp chim dễ dàng ăn cũng như có thể thay dễ dàng. Không làm rơi vãi xuống sàn giúp tránh vương hoa quả ôi. Làm lồng chim sạch sẽ\r\n\r\n👉 Xiên chuối cho chim được thiết kế rãnh gá sẵn nên dễ dàng lắp đặt lên lồng chim\r\n\r\n💥 Giá thành đảm bảo giá hợp lý nhất thị trường\r\n💥 Sản phẩm được kiểm tra kĩ càng.\r\n💥 Sản phẩm luôn có sẵn trong kho hàng. \r\n', 'bird_accessories_1.webp', 0, 2500.00, '2025-03-16 10:01:07');

--
-- Triggers `sanpham`
--
DELIMITER $$
CREATE TRIGGER `trg_auto_increment_masp` BEFORE INSERT ON `sanpham` FOR EACH ROW BEGIN
    DECLARE new_id VARCHAR(10);
    
    -- Lấy giá trị lớn nhất của MaSP và tăng lên 1
    SELECT CONCAT('SP', LPAD(COALESCE(MAX(SUBSTRING(MaSP, 3)), 0) + 1, 3, '0'))
    INTO new_id FROM sanpham;

    -- Gán giá trị mới cho MaSP
    SET NEW.MaSP = new_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_update_giaban` BEFORE INSERT ON `sanpham` FOR EACH ROW BEGIN
    -- Kiểm tra nếu GiamGia nằm trong khoảng 1-100
    IF NEW.GiamGia BETWEEN 1 AND 100 THEN
        -- Tính giá bán dựa vào GiaGoc và phần trăm giảm giá
        SET NEW.GiaBan = NEW.GiaGoc * (1 - NEW.GiamGia / 100);
    ELSE
        -- Nếu GiamGia không hợp lệ, giữ nguyên giá gốc
        SET NEW.GiaBan = NEW.GiaGoc;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `thanhtoan`
--

CREATE TABLE `thanhtoan` (
  `MaTT` varchar(10) NOT NULL,
  `MaDH` varchar(10) NOT NULL,
  `HinhThuc` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL CHECK (`HinhThuc` in ('Tiền mặt','Chuyển khoản','Thẻ')),
  `TrangThai` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL CHECK (`TrangThai` in ('Chưa thanh toán','Đã thanh toán'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thanhtoan`
--

INSERT INTO `thanhtoan` (`MaTT`, `MaDH`, `HinhThuc`, `TrangThai`) VALUES
('TT92a3923e', 'DH92a3870b', NULL, 'Chưa thanh toán');

-- --------------------------------------------------------

--
-- Table structure for table `thucung`
--

CREATE TABLE `thucung` (
  `MaThuCung` varchar(10) NOT NULL,
  `TenThuCung` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Loai` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Giong` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Tuoi` int(11) DEFAULT NULL CHECK (`Tuoi` >= 0),
  `GioiTinh` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL CHECK (`GioiTinh` in ('Đực','Cái')),
  `GiaBan` decimal(18,2) NOT NULL,
  `MoTa` text DEFAULT NULL,
  `HinhAnh` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thucung`
--

INSERT INTO `thucung` (`MaThuCung`, `TenThuCung`, `Loai`, `Giong`, `Tuoi`, `GioiTinh`, `GiaBan`, `MoTa`, `HinhAnh`) VALUES
('1', 'Milu', 'Chó', 'Poodle', 2, 'Đực', 5000000.00, 'Chó Poodle lông xoăn dễ thương', 'milu.jpg'),
('10', 'Husky', 'Chó', 'Husky', 1, 'Đực', 5000000.00, 'Thông minh', 'husky.jfif'),
('11', 'Bo Peep', 'Chó', 'Poodle', 1, 'Cái', 3500000.00, 'Poodle Bò Sữa Cái', 'BoPeep.jpg'),
('2', 'Mèo Mun', 'Mèo', 'Mun', 1, 'Cái', 3000000.00, 'Mèo mun lông đen bóng', 'meo_mun.jpg'),
('3', 'Bobby', 'Chó', 'Golden Retriever', 3, 'Đực', 7000000.00, 'Chó Golden thông minh và thân thiện', 'bobby.jpg'),
('4', 'Lulu', 'Chó', 'Corgi', 2, 'Cái', 8000000.00, 'Chó Corgi chân ngắn đáng yêu', 'lulu.jpg'),
('5', 'Tom', 'Mèo', 'Ba Tư', 2, 'Đực', 6000000.00, 'Mèo Ba Tư lông dài sang trọng', 'tom.jpg'),
('6', 'Vẹt Xanh', 'Chim', 'Vẹt', 2, 'Đực', 1500000.00, 'Vẹt xanh thông minh, có thể học nói.', 'vetxanh.webp'),
('7', 'Vẹt Cacatua', 'Chim', 'Cacatua', 3, 'Cái', 3500000.00, 'Vẹt Cacatua trắng đẹp, thân thiện.', 'vetcacatua.jpg'),
('8', 'Thỏ Đen', 'Thỏ', 'Thỏ Hà Lan', 1, 'Đực', 800000.00, 'Thỏ đen nhỏ dễ thương, thích hợp nuôi làm thú cưng.', 'thoden.jpg'),
('9', 'Thỏ Xám', 'Thỏ', 'Thỏ Chinchilla', 2, 'Cái', 1200000.00, 'Thỏ Chinchilla lông mềm, màu xám, hiền lành.', 'thoxam.jpg');

--
-- Triggers `thucung`
--
DELIMITER $$
CREATE TRIGGER `before_insert_thucung` BEFORE INSERT ON `thucung` FOR EACH ROW BEGIN
    DECLARE max_id INT;
    SELECT IFNULL(MAX(CAST(MaThuCung AS UNSIGNED)), 0) + 1 INTO max_id FROM thucung;
    SET NEW.MaThuCung = CAST(max_id AS CHAR);
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `binhluan`
--
ALTER TABLE `binhluan`
  ADD PRIMARY KEY (`MaBL`),
  ADD KEY `MaKH` (`MaKH`),
  ADD KEY `MaSP` (`MaSP`),
  ADD KEY `MaThuCung` (`MaThuCung`);

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `url_unique` (`url`);

--
-- Indexes for table `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD PRIMARY KEY (`MaCTDH`),
  ADD KEY `MaDH` (`MaDH`),
  ADD KEY `MaSP` (`MaSP`),
  ADD KEY `FK_Chitietdonhang_ThuCung` (`MaThuCung`);

--
-- Indexes for table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `customer_contacts`
--
ALTER TABLE `customer_contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donhang`
--
ALTER TABLE `donhang`
  ADD PRIMARY KEY (`MaDH`),
  ADD KEY `MaKH` (`MaKH`);

--
-- Indexes for table `giohang`
--
ALTER TABLE `giohang`
  ADD PRIMARY KEY (`MaGH`),
  ADD KEY `MaSP` (`MaSP`),
  ADD KEY `giohang_ibfk_1` (`MaKH`);

--
-- Indexes for table `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`MaKH`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `SDT` (`SDT`),
  ADD KEY `FK_Nd_Khachhang` (`ID_ND`);

--
-- Indexes for table `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD PRIMARY KEY (`ID_ND`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`MaSP`);

--
-- Indexes for table `thanhtoan`
--
ALTER TABLE `thanhtoan`
  ADD PRIMARY KEY (`MaTT`),
  ADD KEY `MaDH` (`MaDH`);

--
-- Indexes for table `thucung`
--
ALTER TABLE `thucung`
  ADD PRIMARY KEY (`MaThuCung`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `customer_contacts`
--
ALTER TABLE `customer_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `giohang`
--
ALTER TABLE `giohang`
  MODIFY `MaGH` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `binhluan`
--
ALTER TABLE `binhluan`
  ADD CONSTRAINT `binhluan_ibfk_1` FOREIGN KEY (`MaKH`) REFERENCES `khachhang` (`MaKH`) ON DELETE CASCADE,
  ADD CONSTRAINT `binhluan_ibfk_2` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`MaSP`) ON DELETE SET NULL,
  ADD CONSTRAINT `binhluan_ibfk_3` FOREIGN KEY (`MaThuCung`) REFERENCES `thucung` (`MaThuCung`) ON DELETE SET NULL;

--
-- Constraints for table `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD CONSTRAINT `FK_Chitietdonhang_ThuCung` FOREIGN KEY (`MaThuCung`) REFERENCES `thucung` (`MaThuCung`),
  ADD CONSTRAINT `chitietdonhang_ibfk_1` FOREIGN KEY (`MaDH`) REFERENCES `donhang` (`MaDH`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietdonhang_ibfk_2` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`MaSP`) ON DELETE CASCADE;

--
-- Constraints for table `donhang`
--
ALTER TABLE `donhang`
  ADD CONSTRAINT `donhang_ibfk_1` FOREIGN KEY (`MaKH`) REFERENCES `khachhang` (`MaKH`) ON DELETE CASCADE;

--
-- Constraints for table `giohang`
--
ALTER TABLE `giohang`
  ADD CONSTRAINT `giohang_ibfk_1` FOREIGN KEY (`MaKH`) REFERENCES `khachhang` (`MaKH`) ON DELETE CASCADE,
  ADD CONSTRAINT `giohang_ibfk_2` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`MaSP`) ON DELETE CASCADE;

--
-- Constraints for table `khachhang`
--
ALTER TABLE `khachhang`
  ADD CONSTRAINT `FK_Nd_Khachhang` FOREIGN KEY (`ID_ND`) REFERENCES `nguoidung` (`ID_ND`);

--
-- Constraints for table `thanhtoan`
--
ALTER TABLE `thanhtoan`
  ADD CONSTRAINT `thanhtoan_ibfk_1` FOREIGN KEY (`MaDH`) REFERENCES `donhang` (`MaDH`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
