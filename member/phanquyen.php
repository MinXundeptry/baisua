<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!defined('BASE_URL')) { define('BASE_URL', '/BAICUOIKY/'); }

include '../connect.php'; 

if (!isset($conn)) { die("Lỗi kết nối database."); }

// 1. Lấy danh sách CLB
$list_clb = [];
$clb_query = $conn->query("SELECT id, ten_clb FROM clb ORDER BY ten_clb ASC");
if($clb_query) {
    while ($row = $clb_query->fetch_assoc()) { $list_clb[] = $row; }
}

// 2. Xử lý cập nhật quyền
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_update_role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = $_POST['role_select'];
    $id_clb_update = ($new_role === 'chunhiem') ? intval($_POST['id_clb_select']) : null;
    $my_id = $_SESSION['id_user'] ?? 0;

  
        $stmt = $conn->prepare("UPDATE taikhoan SET vaitro = ?, id_clb = ? WHERE id = ?");
        $stmt->bind_param("sii", $new_role, $id_clb_update, $user_id);
        if ($stmt->execute()) {
            $msg = "<div class='alert alert-success'>Đã cập nhật thành công!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Lỗi: " . $conn->error . "</div>";
        }
        $stmt->close();
    }   

// 3. Lấy danh sách tài khoản
$sql_users = "SELECT t.*, c.ten_clb FROM taikhoan t LEFT JOIN clb c ON t.id_clb = c.id ORDER BY t.id DESC";
$result_users = $conn->query($sql_users);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Phân Quyền</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style_phanquyen.css">
</head>
<body class="bg-light">

    <?php include_once __DIR__ . '/../header.php'; ?>

    <div class="container py-5">
        <div class="card shadow-sm border-0 table-vaitro">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-shield-lock-fill me-2"></i>Quản Lý Vai Trò</h5>
            </div>
            <div class="card-body">
                <?= $msg ?>
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tài khoản</th>
                            <th>Vai trò</th>
                            <th>CLB Quản lý</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result_users): ?>
                        <?php while ($u = $result_users->fetch_assoc()): ?>
                            <?php $vaitro_hientai = strtolower($u['vaitro'] ?? 'user'); ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
                                <td>
                                    <span class="badge rounded-pill role-badge <?= $vaitro_hientai == 'admin' ? 'bg-danger' : ($vaitro_hientai == 'chunhiem' ? 'bg-primary' : 'bg-secondary') ?>">
                                        <?= $vaitro_hientai == 'admin' ? 'Quản trị' : ($vaitro_hientai == 'chunhiem' ? 'Chủ nhiệm' : 'Thành viên') ?>
                                    </span>
                                </td>
                                <td><?= !empty($u['ten_clb']) ? $u['ten_clb'] : '---' ?></td>
                                <td class="text-end">
                                    <?php if ($u['username'] !== ($_SESSION['username'] ?? '')): ?>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $u['id'] ?>">Sửa</button>
                                        
                                        <div class="modal fade text-start" id="modalEdit<?= $u['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <form method="POST" class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Cấp quyền: <?= $u['username'] ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Chọn vai trò:</label>
                                                            <select name="role_select" class="form-select" onchange="toggleClbSelect(this, <?= $u['id'] ?>)">
                                                                <option value="user" <?= $vaitro_hientai == 'user' ? 'selected' : '' ?>>Thành viên</option>
                                                                <option value="chunhiem" <?= $vaitro_hientai == 'chunhiem' ? 'selected' : '' ?>>Chủ nhiệm</option>
                                                                <option value="admin" <?= $vaitro_hientai == 'admin' ? 'selected' : '' ?>>Quản trị viên</option>
                                                            </select>
                                                        </div>
                                                        <div id="div_clb_<?= $u['id'] ?>" class="clb-select-box" style="<?= $vaitro_hientai == 'chunhiem' ? '' : 'display:none;' ?>">
                                                            <label class="form-label fw-bold text-primary">Chọn CLB quản lý:</label>
                                                            <select name="id_clb_select" class="form-select">
                                                                <?php foreach($list_clb as $clb): ?>
                                                                    <option value="<?= $clb['id'] ?>" <?= ($u['id_clb'] == $clb['id']) ? 'selected' : '' ?>>
                                                                        <?= htmlspecialchars($clb['ten_clb']) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" name="btn_update_role" class="btn btn-primary">Lưu thay đổi</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/script_phanquyen.js"></script>
</body>
</html>