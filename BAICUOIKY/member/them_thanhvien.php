<?php 
include '../connect.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// --- LOGIC XÁC ĐỊNH QUYỀN & ID CLB ---
$user_role = isset($_SESSION['role']) ? mb_strtolower($_SESSION['role'], 'UTF-8') : '';
$is_management = in_array($user_role, ['admin', 'chunhiem', 'chủ nhiệm']);
    if (!$is_management) {
    header("Location: danhsach_thanhvien.php");
    exit();
}

$id_clb_user = (int)($_SESSION['id_clb'] ?? 0);
$id_clb_target = ($user_role === 'admin') ? (isset($_GET['id_clb']) ? intval($_GET['id_clb']) : 0) : $id_clb_user;

if ($id_clb_target <= 0) {
    header("Location: danhsach_thanhvien.php");
    exit();
}

// Lấy tên CLB để hiển thị tiêu đề
$clb_query = $conn->query("SELECT ten_clb FROM clb WHERE id = $id_clb_target");
$clb_data = $clb_query->fetch_assoc();
$ten_clb_hien_tai = $clb_data['ten_clb'] ?? "Câu lạc bộ";

// XỬ LÝ KHI SUBMIT FORM
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $masv = trim($_POST['masv']);
    $hoten = trim($_POST['hoten']);
    $ban = $_POST['ban'];
    $chucvu = $_POST['chucvu'];
    $ngaythamgia = $_POST['ngaythamgia'];

    // Kiểm tra trùng mã SV âm thầm (nếu trùng thì không insert, chỉ quay về)
    $stmt_check = $conn->prepare("SELECT id FROM thanhvien WHERE masv = ? AND id_clb = ?");
    $stmt_check->bind_param("si", $masv, $id_clb_target);
    $stmt_check->execute();
    
    if ($stmt_check->get_result()->num_rows == 0) {
        $stmt_ins = $conn->prepare("INSERT INTO thanhvien (masv, hoten, ban, chucvu, ngaythamgia, id_clb) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_ins->bind_param("sssssi", $masv, $hoten, $ban, $chucvu, $ngaythamgia, $id_clb_target);
        $stmt_ins->execute();
    }
    
    header("Location: danhsach_thanhvien.php?id_clb=$id_clb_target");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Thành Viên - <?= $ten_clb_hien_tai ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style_member.css">
</head>
<body class="bg-light">

<?php include '../header.php'; ?>

<div class="container mt-5 mb-5">
    <div class="card shadow-sm border-0 col-md-8 mx-auto member-card">
        <div class="card-header text-white py-3 member-card-header text-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-person-plus-fill me-2"></i> THÊM THÀNH VIÊN</h5>
            <small class="opacity-75"><?= $ten_clb_hien_tai ?></small>
        </div>
        
        <div class="card-body p-4">
            <form method="POST">
                <input type="hidden" name="id_clb" value="<?= $id_clb_target ?>">

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary text-uppercase">Họ và tên</label>
                    <input type="text" name="hoten" class="form-control shadow-sm" placeholder="Nhập tên đầy đủ" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary text-uppercase">Mã sinh viên</label>
                        <input type="text" name="masv" class="form-control shadow-sm" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary text-uppercase">Ngày tham gia</label>
                        <input type="date" name="ngaythamgia" class="form-control shadow-sm" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary text-uppercase">Ban chuyên môn</label>
                        <select name="ban" class="form-select shadow-sm" required>
                            <?php 
                            $ds_ban = ["Truyền thông", "Kỹ thuật", "Hậu cần", "Sự kiện", "Đối ngoại", "Văn nghệ", "Chưa xếp ban"];
                            foreach($ds_ban as $b) echo "<option value='$b'>$b</option>";
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary text-uppercase">Chức vụ</label>
                        <select name="chucvu" class="form-select shadow-sm" required>
                            <?php 
                            $ds_cv = ["Thành viên", "Trưởng ban", "Phó ban", "Phó chủ nhiệm", "Chủ nhiệm"];
                            foreach($ds_cv as $cv) echo "<option value='$cv'>$cv</option>";
                            ?>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-between gap-2 mt-4 pt-3 border-top">
                    <a href="danhsach_thanhvien.php?id_clb=<?= $id_clb_target ?>" class="btn btn-light border px-4">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary btn-confirm fw-bold shadow-sm">Xác nhận</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/member.js"></script>
</body>
</html>