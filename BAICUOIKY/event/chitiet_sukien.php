<?php 
include '../connect.php'; 
include '../header.php'; 

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 1. Lấy thông tin sự kiện và giới hạn chỗ (cột soluong)
$sql_event = "SELECT s.*, c.ten_clb FROM sukien s 
              LEFT JOIN clb c ON s.id_clb = c.id 
              WHERE s.id = $id";
$result_event = $conn->query($sql_event);

if ($result_event && $result_event->num_rows > 0) {
    $row = $result_event->fetch_assoc();
} else {
    echo "<div class='container py-5 text-center'><h3>Sự kiện không tồn tại!</h3></div>";
    include '../footer.php';
    exit;
}

// 2. Đếm số lượng người đã đăng ký thực tế từ bảng 'dangky'
// Giả sử bảng đăng ký của bạn tên là 'dangky' và có cột 'id_sukien'
$sql_count = "SELECT COUNT(*) as total_reg FROM dangky WHERE id_sukien = $id";
$res_count = $conn->query($sql_count);
$data_count = $res_count->fetch_assoc();

$registered = $data_count['total_reg']; // Số người đã đăng ký thực tế
$limit = $row['soluong'] > 0 ? $row['soluong'] : 100; // Lấy cột soluong từ bảng sukien

// 3. Tính toán phần trăm
$percent = ($registered / $limit) * 100;
$percent = $percent > 100 ? 100 : $percent; // Đảm bảo không vượt quá 100%
?>

<style>
    .event-header-section {
        background: linear-gradient(135deg, #1a2a6c 0%, #b21f1f 50%, #fdbb2d 100%);
        padding: 80px 0 160px 0;
        color: white;
        text-align: center;
    }
    .event-container { margin-top: -100px; position: relative; z-index: 2; }
    .glass-card {
        background: white;
        border-radius: 30px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
        padding: 40px;
    }
    .info-item-box {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 15px 25px;
        display: flex;
        align-items: center;
        gap: 15px;
        height: 100%;
    }
    .btn-register-now {
        background: #00cec9;
        color: white;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 700;
        border: none;
    }
    .progress { height: 8px; border-radius: 10px; background-color: #eee; }
</style>

<div class="event-header-section">
    <div class="container">
        <h1 class="display-4 fw-bold"><?= htmlspecialchars($row['ten']) ?></h1>
    </div>
</div>

<div class="container event-container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="glass-card">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="info-item-box">
                            <i class="bi bi-calendar3 fs-3 text-info"></i>
                            <div>
                                <small class="text-muted d-block">Ngày tổ chức</small>
                                <strong><?= date("d/m/Y", strtotime($row['ngay'])) ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item-box">
                            <i class="bi bi-geo-alt fs-3 text-danger"></i>
                            <div>
                                <small class="text-muted d-block">Địa điểm</small>
                                <strong><?= htmlspecialchars($row['diadiem']) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <h5 class="fw-bold mb-3">Nội dung chi tiết</h5>
                    <div class="text-secondary" style="line-height: 1.8;">
                        <?= nl2br(htmlspecialchars($row['mota'])) ?>
                    </div>
                </div>

                <div class="border-top pt-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-bold">Tiến độ đăng ký</span>
                        <span class="small text-muted"><?= $registered ?> / <?= $limit ?> thành viên</span>
                    </div>
                    <div class="progress mb-4">
                        <div class="progress-bar" role="progressbar" 
                             style="width: <?= $percent ?>%; background-color: #00cec9;" 
                             aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="formdangki.php?id_sukien=<?= $id ?>" class="btn btn-register-now shadow-sm text-decoration-none d-inline-flex align-items-center justify-content-center">Đăng ký ngay</a>
                        <div class="capacity-info small">
                            <i class="bi bi-circle-fill me-2" style="color: <?= $percent >= 90 ? '#ff7675' : '#00cec9' ?>;"></i>
                            Đã đăng ký: <?= round($percent) ?>%
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="../index.php" class="btn btn-link text-decoration-none text-muted">Quay lại</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>