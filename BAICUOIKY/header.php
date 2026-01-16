<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * BASE URL của dự án
 */
if (!defined('BASE_URL')) {
    define('BASE_URL', '/BAICUOIKY/');
}

/**
 * Kết nối Database
 */
include_once __DIR__ . '/connect.php';

/**
 * 1. Lấy danh sách CLB (Dùng cho dropdown menu)
 */
$list_clb = [];
if (isset($conn)) {
    $query_clb = $conn->query("SELECT id, ten_clb FROM clb");
    if ($query_clb && $query_clb->num_rows > 0) {
        while ($row = $query_clb->fetch_assoc()) {
            $list_clb[] = $row;
        }
    }
}

/**
 * 2. Kiểm tra vai trò và phân quyền hiển thị
 */
$is_management = false; // Chủ nhiệm hoặc Admin
$is_admin = false;      // Chỉ Admin
$role_display = 'Thành viên';

if (isset($_SESSION['role'])) {
    $role = trim(mb_strtolower($_SESSION['role'], 'UTF-8'));
    
    if ($role === 'admin') {
        $is_admin = true;
        $is_management = true;
        $role_display = 'Quản trị viên';
    } 
    elseif ($role === 'chunhiem') {
        $is_management = true;
        $role_display = 'Chủ nhiệm CLB';
    }
}

/**
 * Helper check active menu
 */
function isActive($file) {
    return basename($_SERVER['PHP_SELF']) === $file ? 'active text-primary fw-bold' : '';
}

/**
 * Lấy avatar người dùng
 */
$user_avatar = ''; 
if (isset($_SESSION['username']) && isset($conn)) {
    $u_check = $conn->real_escape_string($_SESSION['username']);
    $sql_avt = "SELECT avatar FROM taikhoan WHERE username = '$u_check'";
    $res_avt = $conn->query($sql_avt);
    if ($res_avt && $res_avt->num_rows > 0) {
        $row_avt = $res_avt->fetch_assoc();
        $user_avatar = $row_avt['avatar'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .dropdown-menu { z-index: 2000 !important; margin-top: 10px !important; }
        .user-nav-item { cursor: pointer; user-select: none; }
        .nav-link.active { border-bottom: 2px solid #0d6efd; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light sticky-top shadow-sm bg-white">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>index.php">
            <i class="bi bi-rocket-takeoff-fill fs-3 text-primary me-2"></i>
            <span class="fw-bold text-dark">SV-MANAGER</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link <?= isActive('index.php') ?>" href="<?= BASE_URL ?>index.php">Trang chủ</a>
                </li>

                <?php if ($is_management): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Thành viên</a>
                    <ul class="dropdown-menu shadow border-0">
                        <?php foreach ($list_clb as $clb): ?>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>member/danhsach_thanhvien.php?id_clb=<?= $clb['id'] ?>"><?= htmlspecialchars($clb['ten_clb']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Sự kiện</a>
                    <ul class="dropdown-menu shadow border-0">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>event/danhsach_sukien.php"><i class="bi bi-calendar-event me-2"></i>Tất cả</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php foreach ($list_clb as $clb): ?>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>event/danhsach_sukien.php?id_clb=<?= $clb['id'] ?>"><?= htmlspecialchars($clb['ten_clb']) ?></a></li>
                        <?php endforeach; ?>
                        
                        <?php if (isset($_SESSION['username'])): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item fw-semibold text-success" href="<?= BASE_URL ?>quy/quy_clb.php">
                                    <i class="bi bi-wallet2 me-2"></i>Quỹ CLB
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>

                <?php if (!$is_management): ?>
                <li class="nav-item">
                    <a class="nav-link <?= isActive('danhsachdki.php') ?>" href="<?= BASE_URL ?>event/danhsachdki.php">Đăng ký</a>
                </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link <?= isActive('danhsach_tintuc.php') ?>" href="<?= BASE_URL ?>tintuc/danhsach_tintuc.php">Tin tức</a>
                </li>

                <?php if ($is_admin): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-danger fw-semibold" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-gear-fill me-1"></i>Hệ thống
                    </a>
                    <ul class="dropdown-menu shadow border-0">
                        <li>
                            <a class="dropdown-item" href="<?= BASE_URL ?>member/phanquyen.php">
                                <i class="bi bi-shield-lock me-2"></i>Phân quyền tài khoản
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="dropdown">
                        <div class="user-nav-item d-flex align-items-center dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown">
                            <?php if (!empty($user_avatar)): ?>
                                <img src="<?= BASE_URL . 'uploads/' . $user_avatar ?>" alt="Avatar" class="rounded-circle me-2" style="width: 35px; height: 35px; object-fit: cover; border: 1px solid #ddd;">
                            <?php else: ?>
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:35px;height:35px; font-weight: bold;">
                                    <?= mb_strtoupper(mb_substr($_SESSION['username'], 0, 1, 'UTF-8'), 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                            <div class="lh-1">
                                <div class="fw-semibold text-dark"><?= htmlspecialchars($_SESSION['username']) ?></div>
                                <small class="text-muted" style="font-size: 11px;"><?= $role_display ?></small>
                            </div>
                        </div>

                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <?php if ($is_management): ?>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>event/duyetdangki.php"><i class="bi bi-clipboard-check me-2"></i>Duyệt đăng ký</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>event/them_sukien.php"><i class="bi bi-plus-circle me-2"></i>Thêm sự kiện</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>quy/quy_clb.php"><i class="bi bi-wallet2 me-2"></i>Quỹ CLB</a></li>
                            
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>login/ho_so.php"><i class="bi bi-person me-2"></i>Hồ sơ cá nhân</a></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>login/dangxuat.php"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>login/dangnhap.php" class="btn btn-outline-primary rounded-pill px-4">Đăng nhập</a>
                    <a href="<?= BASE_URL ?>login/dangky.php" class="btn btn-primary rounded-pill px-4">Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>