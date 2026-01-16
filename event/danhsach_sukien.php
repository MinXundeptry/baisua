<?php 
include '../header.php'; 
include '../connect.php'; 

// 1. Lấy id_clb từ URL để lọc sự kiện theo CLB (nếu có)
$id_clb_filter = isset($_GET['id_clb']) ? intval($_GET['id_clb']) : 0;

// --- LOGIC PHÂN TRANG ---
$limit = 6; // Hiển thị 6 sự kiện trên một trang
$page = isset($_GET['p_ev']) ? intval($_GET['p_ev']) : 1;
if ($page < 1) $page = 1;
$start = ($page - 1) * $limit;

// Điều kiện lọc WHERE
$where_clause = ($id_clb_filter > 0) ? "WHERE s.id_clb = $id_clb_filter" : "";

// 2. Tính tổng số sự kiện để chia trang
$total_res = $conn->query("SELECT COUNT(*) as total FROM sukien s $where_clause");
$total_data = $total_res->fetch_assoc();
$total_ev = $total_data['total'];
$total_pages = ceil($total_ev / $limit);
?>

<style>
    .card-hover {
        transition: all 0.3s ease-in-out;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .card-hover:hover {
        transform: translateY(-12px);
        box-shadow: 0 15px 30px rgba(13, 110, 253, 0.15) !important;
        border-color: #0dcaf0;
    }
    .bg-soft-primary {
        background-color: #e7f1ff;
        color: #0d6efd;
    }
    .line-clamp {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;  
        overflow: hidden;
    }
</style>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5" data-aos="fade-right">
        <div>
            <h2 class="fw-bold mb-1">
                <?php 
                if ($id_clb_filter > 0) {
                    $clb_name_res = $conn->query("SELECT ten_clb FROM clb WHERE id = $id_clb_filter");
                    $clb_name = $clb_name_res->fetch_assoc();
                    echo "Sự Kiện: <span class='text-info'>" . htmlspecialchars($clb_name['ten_clb']) . "</span>";
                } else {
                    echo "Khám Phá <span class='text-info'>Sự Kiện Mới</span>";
                }
                ?>
            </h2>
            <p class="text-muted">Cập nhật những hoạt động mới nhất từ các câu lạc bộ</p>
        </div>
        
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <a href="them_sukien.php" class="btn btn-success rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-circle me-2"></i>Thêm Sự Kiện Mới
            </a>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <?php
        // 3. Truy vấn lấy dữ liệu sự kiện kèm tên CLB và có LIMIT phân trang
        $sql = "SELECT s.*, c.ten_clb 
                FROM sukien s 
                LEFT JOIN clb c ON s.id_clb = c.id 
                $where_clause 
                ORDER BY s.ngay DESC 
                LIMIT $start, $limit";
                
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>
        <div class="col-lg-4 col-md-6" data-aos="fade-up">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body p-4 d-flex flex-column">
                    <span class="badge bg-soft-primary mb-3 align-self-start px-3 py-2 rounded-pill">
                        <i class="bi bi-bookmark-star-fill me-1"></i>
                        <?= htmlspecialchars($row['ten_clb'] ?? 'Sự kiện chung') ?>
                    </span>
                    
                    <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($row['ten']) ?></h5>
                    
                    <p class="text-muted small line-clamp mb-4">
                        <?= htmlspecialchars($row['mota'] ?? 'Chưa có mô tả chi tiết cho sự kiện này.') ?>
                    </p>

                    <div class="mt-auto">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                            <span class="text-muted small"><?= htmlspecialchars($row['diadiem']) ?></span>
                        </div>
                        <div class="d-flex align-items-center mb-4">
                            <i class="bi bi-calendar-event-fill text-info me-2"></i>
                            <span class="text-muted small"><?= date("d/m/Y", strtotime($row['ngay'])) ?></span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                            <a href="chitiet_sukien.php?id=<?= $row['id'] ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">Xem chi tiết</a>
                            
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                <div class="btn-group">
                                    <a href="sua_sukien.php?id=<?= $row['id'] ?>" class="btn btn-outline-warning border-0" title="Sửa"><i class="bi bi-pencil-square"></i></a>
                                    <a href="xoa_sukien.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger border-0" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa sự kiện này?')"><i class="bi bi-trash"></i></a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php 
            endwhile; 
        else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-calendar-x" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">Hiện chưa có sự kiện nào được đăng tải.</p>
                <a href="danhsach_sukien.php" class="btn btn-outline-primary btn-sm rounded-pill">Xem tất cả sự kiện</a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <nav class="mt-5">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link border-0 shadow-sm mx-1 rounded-circle" href="?id_clb=<?= $id_clb_filter ?>&p_ev=<?= $page - 1 ?>">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>

            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link border-0 shadow-sm mx-1 rounded-circle <?= ($page == $i) ? 'bg-primary text-white' : '' ?>" 
                       href="?id_clb=<?= $id_clb_filter ?>&p_ev=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link border-0 shadow-sm mx-1 rounded-circle" href="?id_clb=<?= $id_clb_filter ?>&p_ev=<?= $page + 1 ?>">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<script>language="javascript" src="js/event.js"></script>

<?php include '../footer.php'; ?>