<?php
$servername = "localhost";
$username = "root"; // Mặc định của XAMPP
$password = "";     // Mặc định của XAMPP thường để trống
$dbname = "webcuoiki";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Thiết lập font chữ tiếng Việt
mysqli_set_charset($conn, 'UTF8');
?>