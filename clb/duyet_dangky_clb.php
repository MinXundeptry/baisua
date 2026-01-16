<?php
include '../connect.php'; 
session_start();

// 1. Lấy ID câu lạc bộ từ URL trước để kiểm tra quyền
$id_clb = isset($_GET['id_clb']) ? intval($_GET['id_clb']) : 0;
$is_admin = (isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == '0'));
$is_chunhiem_this_clb = (isset($_SESSION['role']) && ($_SESSION['role'] == '1' || $_SESSION['role'] == 'chunhiem') && isset($_SESSION['id_clb']) && $_SESSION['id_clb'] == $id_clb);

if (!$is_admin && !$is_chunhiem_this_clb) {
    include '../header.php';
    echo "<div class='container mt-5 alert alert-danger shadow-sm'>
            <i class='bi bi-exclamation-triangle-fill me-2'></i>
            Bạn không có quyền quản trị tại câu lạc bộ này!
          </div>";
    echo "<div class='text-center'><a href='../index.php' class='btn btn-primary'>Quay lại trang chủ</a></div>";
    include '../footer.php';
    exit();
}

include '../header.php'; 

// 2. Xử lý khi nhấn nút Duyệt hoặc Từ chối
if (isset($_GET['action']) && isset($_GET['id_dk'])) {
    $id_dk = intval($_GET['id_dk']);
    $action = $_GET['action'];

    if ($action == 'approve') {
        // Lấy thông tin từ bảng dangkyclb
        $info_sql = "SELECT d.*, t.hoten, t.username FROM dangkyclb d 
                    JOIN taikhoan t ON d.id_taikhoan = t.id 
                    WHERE d.id = $id_dk AND d.id_clb = $id_clb"; // Thêm id_clb để bảo mật chéo
        $info_res = $conn->query($info_sql);
        
        if ($info_res && $info_res->num_rows > 0) {
            $info = $info_res->fetch_assoc();
            $masv = $info['username']; 
            $hoten = $info['hoten'];
            $clb_id = $info['id_clb'];
            $id_tk = $info['id_taikhoan'];

            // Bắt đầu Transaction để đảm bảo dữ liệu nhất quán
            $conn->begin_transaction();

            try {
                // Cập nhật trạng thái đơn
                $conn->query("UPDATE dangkyclb SET trang_thai = 'Đã duyệt' WHERE id = $id_dk");

                // Thêm vào bảng thành viên
                $sql_insert = "INSERT INTO thanhvien (id_taikhoan, masv, hoten, id_clb, ngaythamgia, chucvu, ban) 
                               VALUES ($id_tk, '$masv', '$hoten', $clb_id, NOW(), 'Thành viên', 'Chưa xếp ban')";
                $conn->query($sql_insert);

                $conn->commit();
                echo "<script>alert('Duyệt thành công!'); window.location.href='duyet_dangky_clb.php?id_clb=$id_clb';</script>";
            } catch (Exception $e) {
                $conn->rollback();
                echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại!');</script>";
            }
        }
    } 
    elseif ($action == 'reject') {
        $conn->query("UPDATE dangkyclb SET trang_thai = 'Từ chối' WHERE id = $id_dk AND id_clb = $id_clb");
        echo "<script>alert('Đã từ chối yêu cầu.'); window.location.href='duyet_dangky_clb.php?id_clb=$id_clb';</script>";
    }
}

// 3. Lấy danh sách chờ duyệt
$sql = "SELECT d.*, t.hoten, t.username FROM dangkyclb d 
        JOIN taikhoan t ON d.id_taikhoan = t.id 
        WHERE d.id_clb = $id_clb AND d.trang_thai = 'Chờ duyệt'
        ORDER BY d.ngay_dang_ky DESC";
$list_dk = $conn->query($sql);
?>

<div class="container mt-5">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-person-check-fill me-2"></i> Duyệt Đơn Gia Nhập (<?= $is_admin ? 'Admin' : 'Chủ nhiệm' ?>)</h5>
            <a href="../dashboard.php?id_clb=<?= $id_clb ?>" class="btn btn-light btn-sm rounded-pill px-3">Về Trang Chính</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Mã Sinh Viên</th>
                            <th>Họ và Tên</th>
                            <th>Ngày gửi</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($list_dk && $list_dk->num_rows > 0): ?>
                            <?php while($row = $list_dk->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-primary"><?= $row['username'] ?></td>
                                    <td><?= htmlspecialchars($row['hoten']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['ngay_dang_ky'])) ?></td>
                                    <td class="text-center">
                                        <a href="?id_clb=<?= $id_clb ?>&id_dk=<?= $row['id'] ?>&action=approve" 
                                           onclick="return confirm('Xác nhận duyệt thành viên này?')" 
                                           class="btn btn-success btn-sm px-3 rounded-pill">Duyệt</a>
                                        <a href="?id_clb=<?= $id_clb ?>&id_dk=<?= $row['id'] ?>&action=reject" 
                                           onclick="return confirm('Bạn có chắc chắn muốn từ chối?')" 
                                           class="btn btn-outline-danger btn-sm px-3 rounded-pill">Từ chối</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-5 text-muted">Hiện tại không có đơn đăng ký nào cần duyệt.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>