<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra nếu chưa đăng nhập HOẶC không phải admin thì đuổi ra trang chủ
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>
            alert('Bạn không có quyền truy cập trang quản trị!');
            window.location.href='index.php';
          </script>";
    exit();
}
?>
<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Bạn không có quyền truy cập trang này!'); window.location.href='index.php';</script>";
    exit();
}
?><?php
include '../connect.php';
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM sukien WHERE id = $id");
}
header("Location: danhsach_sukien.php");
?>