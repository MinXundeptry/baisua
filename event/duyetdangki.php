<?php 
include '../connect.php'; 
include '../header.php'; 

// Xử lý cập nhật trạng thái khi nhận được yêu cầu từ URL
if (isset($_GET['action']) && isset($_GET['id_dk'])) {
    $id_dk = $_GET['id_dk'];
    $status = $_GET['action']; // 'Da duyet' hoặc 'Da huy'

    // Cập nhật trạng thái vào cơ sở dữ liệu
    $sql_update = "UPDATE dangky SET trangthai = '$status' WHERE id = $id_dk";
    
    if ($conn->query($sql_update) === TRUE) {
        $msg = ($status == 'Da duyet') ? 'Đã duyệt đơn đăng ký thành công!' : 'Đã hủy đơn đăng ký!';
        // Hiển thị thông báo JS và quay về trang duyệt để danh sách tự cập nhật
        echo "<script>
                alert('$msg');
                window.location.href='duyetdangki.php';
              </script>";
    } else {
        echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
    }
}
?>

<div class="container mt-4 pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-shield-check me-2 text-primary"></i>Duyệt Đăng Ký</h2>
        <span class="badge bg-warning text-dark border p-2 fs-6">
            Cần xử lý: <?php echo $conn->query("SELECT id FROM dangky WHERE trangthai = 'Cho duyet'")->num_rows; ?> đơn
        </span>
    </div>

    <div class="table-responsive bg-white p-3 shadow-sm rounded">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>STT</th>
                    <th>Sinh Viên</th>
                    <th>Sự Kiện</th>
                    <th>Ngày Đăng Ký</th>
                    <th>Trạng Thái</th>
                    <th class="text-center">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // SỬA 2: Thêm điều kiện WHERE d.trangthai = 'Cho duyet' vào câu truy vấn
                $sql = "SELECT d.*, s.ten as ten_sk 
                        FROM dangky d 
                        JOIN sukien s ON d.id_sukien = s.id 
                        WHERE d.trangthai = 'Cho duyet' 
                        ORDER BY d.id DESC";
                $res = $conn->query($sql);
                
                if ($res && $res->num_rows > 0):
                    $stt = 1;
                    while($row = $res->fetch_assoc()):
                        // Vì chỉ hiện 'Cho duyet' nên badge mặc định là màu vàng/xám
                        $badgeClass = 'bg-secondary'; 
                ?>
                <tr>
                    <td><?= $stt++ ?></td>
                    <td>
                        <strong class="text-dark"><?= htmlspecialchars($row['hoten']) ?></strong><br>
                        <small class="text-muted"><i class="bi bi-card-text me-1"></i><?= htmlspecialchars($row['masv']) ?></small>
                    </td>
                    <td>
                        <span class="text-truncate d-inline-block" style="max-width: 200px; font-weight: 500; color: #0d6efd;">
                            <?= htmlspecialchars($row['ten_sk']) ?>
                        </span>
                    </td>
                    <td><small><?= date("H:i d/m/Y", strtotime($row['ngay_dk'] ?? 'now')) ?></small></td>
                    <td>
                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group shadow-sm">
                            <a href="?action=Da duyet&id_dk=<?= $row['id'] ?>" 
                               class="btn btn-sm btn-success px-3" 
                               onclick="return confirm('Xác nhận DUYỆT đơn này?')">
                               <i class="bi bi-check-lg"></i> Duyệt
                            </a>
                            <a href="?action=Da huy&id_dk=<?= $row['id'] ?>" 
                               class="btn btn-sm btn-outline-danger px-3" 
                               onclick="return confirm('Xác nhận TỪ CHỐI đơn này?')">
                               <i class="bi bi-x-lg"></i> Hủy
                            </a>
                        </div>
                    </td>
                </tr>
                <?php 
                    endwhile; 
                else: 
                ?>
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="bi bi-check2-circle text-success display-3"></i>
                        <p class="text-muted mt-3">Tuyệt vời! Không còn đơn nào cần xử lý.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../footer.php'; ?>