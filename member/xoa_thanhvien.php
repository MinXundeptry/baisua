<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include '../connect.php';

// 1. KIỂM TRA QUYỀN TRUY CẬP CƠ BẢN
$user_role = isset($_SESSION['role']) ? mb_strtolower($_SESSION['role'], 'UTF-8') : '';
$is_management = ($user_role === 'admin' || $user_role === 'chủ nhiệm' || $user_role === 'chunhiem');

if (!$is_management || !isset($_GET['id'])) {
    header("Location: danhsach_thanhvien.php");
    exit();
}

$id = intval($_GET['id']);
$my_id_clb = intval($_SESSION['id_clb'] ?? 0);

/**
 * 2. KIỂM TRA BẢO MẬT & XỬ LÝ XÓA
 * Admin: Xóa bất kỳ ai.
 * Chủ nhiệm: Chỉ xóa được nếu id_clb của thành viên đó khớp với id_clb của chủ nhiệm trong Session.
 */
if ($user_role === 'admin') {
    // Admin xóa thẳng, lấy id_clb để quay về đúng trang
    $res = $conn->query("SELECT id_clb FROM thanhvien WHERE id = $id");
    $row = $res->fetch_assoc();
    $id_clb_member = $row['id_clb'] ?? 0;

    $conn->query("DELETE FROM thanhvien WHERE id = $id");
    header("Location: danhsach_thanhvien.php?id_clb=$id_clb_member");
} 
else {
    // Chủ nhiệm: Chỉ xóa nếu thuộc CLB mình quản lý
    $stmt = $conn->prepare("DELETE FROM thanhvien WHERE id = ? AND id_clb = ?");
    $stmt->bind_param("ii", $id, $my_id_clb);
    $stmt->execute();
    
    header("Location: danhsach_thanhvien.php?id_clb=$my_id_clb");
}
exit();
?>