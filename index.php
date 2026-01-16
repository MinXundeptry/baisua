<?php 
include 'header.php'; 
include 'connect.php'; 
?>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<style>
    /* Hiệu ứng chung cho Card */
    .card-hover {
        transition: all 0.3s ease-in-out;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .card-hover:hover {
        transform: translateY(-12px);
        box-shadow: 0 15px 30px rgba(13, 110, 253, 0.15) !important;
        border-color: #0dcaf0;
    }

    /* Hiệu ứng cho phần Sự Kiện */
    .date-badge {
        background: linear-gradient(45deg, #0dcaf0, #0d6efd);
        color: white;
        border-radius: 12px;
        padding: 8px;
        text-align: center;
        min-width: 65px;
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2);
    }
    .date-badge .day { font-size: 1.4rem; font-weight: 800; display: block; line-height: 1; }
    .date-badge .month { font-size: 0.7rem; text-transform: uppercase; font-weight: 600; }

    .club-tag {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 5px 12px;
        background: rgba(13, 202, 240, 0.1);
        color: #0aa2c0;
        border-radius: 50px;
    }

    .event-title {
        height: 3rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        line-height: 1.5;
    }
</style>
<link href="css/index.css" rel="stylesheet">

<section class="hero text-center text-lg-start">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7" data-aos="fade-right">
                <span class="badge bg-white text-primary mb-3 px-3 py-2 rounded-pill shadow-sm">Sáng Tạo - Kết Nối - Phát Triển</span>
                <h1 class="mb-4 fw-bold display-4">Hệ Thống Quản Lý <br><span class="text-warning">Câu Lạc Bộ Sinh Viên</span></h1>
                <p class="mb-5 opacity-75 fs-5">Nền tảng giúp tối ưu hóa việc quản lý thành viên và tổ chức sự kiện cho toàn bộ các CLB trong trường.</p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                    <a href="#clb-list" class="btn btn-light text-primary fw-bold shadow-lg px-4 py-3 rounded-pill">Xem Các Câu Lạc Bộ</a>
                    <a href="#events-preview" class="btn btn-outline-light px-4 py-3 rounded-pill">Sự Kiện Mới Nhất</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="clb-list" class="py-5">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <h2 class="fw-bold">Danh Sách Các Câu Lạc Bộ</h2>
            <div class="line mx-auto mb-3" style="width: 80px; height: 3px; background: #0dcaf0;"></div>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <div class="mb-4">
                    <a href="clb/them_clb.php" class="btn btn-success rounded-pill px-4 shadow-sm">
                        <i class="bi bi-plus-circle me-2"></i>Thêm CLB Mới
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="row g-4">
            <?php
            $limit = 6; 
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $start = ($page - 1) * $limit;

            $total_query = $conn->query("SELECT COUNT(*) as total FROM clb");
            $total_data = $total_query->fetch_assoc();
            $total_pages = ceil($total_data['total'] / $limit);

            $sql_clb = "SELECT * FROM clb LIMIT $start, $limit";
            $result_clb = $conn->query($sql_clb);
            
            if ($result_clb && $result_clb->num_rows > 0):
                while($row = $result_clb->fetch_assoc()):
            ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="card h-100 shadow-sm border-0 p-4 text-center rounded-4 card-hover">
                    <div class="module-icon mb-3" style="font-size: 2.5rem; color: #0dcaf0;">
                        <i class="bi bi-rocket-takeoff-fill"></i>
                    </div>
                    <h4 class="fw-bold"><?= htmlspecialchars($row['ten_clb']) ?></h4>
                    <p class="text-muted small"><?= htmlspecialchars($row['mota']) ?></p>
                    <a href="dashboard.php?id_clb=<?= $row['id'] ?>" class="btn btn-outline-info rounded-pill px-4 mt-auto">Xem Câu Lạc Bộ</a>
                </div>
            </div>
            <?php endwhile; else: ?>
                <div class="col-12 text-center"><p class="text-muted">Chưa có dữ liệu câu lạc bộ.</p></div>
            <?php endif; ?>
        </div>

        <?php if ($total_pages > 1): ?>
        <nav class="mt-5" data-aos="fade-up">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link border-0 shadow-sm mx-1 rounded-circle" href="?page=<?= $page - 1 ?>&page_ev=<?= isset($_GET['page_ev']) ? $_GET['page_ev'] : 1 ?>#clb-list">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                        <a class="page-link border-0 shadow-sm mx-1 rounded-circle <?= ($page == $i) ? 'bg-primary text-white' : '' ?>" 
                           href="?page=<?= $i ?>&page_ev=<?= isset($_GET['page_ev']) ? $_GET['page_ev'] : 1 ?>#clb-list"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link border-0 shadow-sm mx-1 rounded-circle" href="?page=<?= $page + 1 ?>&page_ev=<?= isset($_GET['page_ev']) ? $_GET['page_ev'] : 1 ?>#clb-list">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</section>

<section id="events-preview" class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5" data-aos="fade-up">
            <div>
                <h3 class="fw-bold m-0 text-dark">Sự Kiện Sắp Diễn Ra</h3>
                <p class="text-muted mb-0">Các hoạt động mới nhất từ các câu lạc bộ</p>
            </div>
            <a href="event/danhsach_sukien.php" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
                Xem tất cả <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php
            $today = date('Y-m-d');
            $limit_ev = 3; 
            $page_ev = isset($_GET['page_ev']) ? intval($_GET['page_ev']) : 1;
            $start_ev = ($page_ev - 1) * $limit_ev;

            $total_ev_query = $conn->query("SELECT COUNT(*) as total FROM sukien WHERE ngay >= '$today'");
            $total_ev_data = $total_ev_query->fetch_assoc();
            $total_ev_pages = ceil($total_ev_data['total'] / $limit_ev);

            $sql_ev = "SELECT s.*, c.ten_clb FROM sukien s 
                       LEFT JOIN clb c ON s.id_clb = c.id 
                       WHERE s.ngay >= '$today'
                       ORDER BY s.ngay ASC LIMIT $start_ev, $limit_ev";
            $result_ev = $conn->query($sql_ev);

            if ($result_ev && $result_ev->num_rows > 0):
                $delay = 100;
                while($ev = $result_ev->fetch_assoc()):
                    $d = date('d', strtotime($ev['ngay']));
                    $m = 'Tháng ' . date('m', strtotime($ev['ngay']));
            ?>
            <div class="col-md-4" data-aos="zoom-in-up" data-aos-delay="<?= $delay ?>">
                <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="date-badge">
                                <span class="day"><?= $d ?></span>
                                <span class="month"><?= $m ?></span>
                            </div>
                            <span class="club-tag">
                                <i class="bi bi-people-fill me-1"></i><?= htmlspecialchars($ev['ten_clb'] ?? 'Chung') ?>
                            </span>
                        </div>
                        <h5 class="fw-bold event-title"><?= htmlspecialchars($ev['ten']) ?></h5>
                        <div class="mb-4">
                            <div class="small text-muted mb-1">
                                <i class="bi bi-geo-alt-fill text-danger me-2"></i><?= htmlspecialchars($ev['diadiem']) ?>
                            </div>
                            <div class="small text-muted">
                                <i class="bi bi-clock-fill text-warning me-2"></i>Theo lịch thông báo
                            </div>
                        </div>
                        <div class="mt-auto">
                            <a href="event/chitiet_sukien.php?id=<?= $ev['id'] ?>" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">Chi tiết sự kiện</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                $delay += 100;
                endwhile; 
            else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Chưa có sự kiện nào sắp diễn ra.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($total_ev_pages > 1): ?>
        <nav class="mt-5" data-aos="fade-up">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page_ev <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link border-0 shadow-sm mx-1 rounded-circle" href="?page_ev=<?= $page_ev - 1 ?>&page=<?= isset($_GET['page']) ? $_GET['page'] : 1 ?>#events-preview">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php for($i = 1; $i <= $total_ev_pages; $i++): ?>
                    <li class="page-item <?= ($page_ev == $i) ? 'active' : '' ?>">
                        <a class="page-link border-0 shadow-sm mx-1 rounded-circle <?= ($page_ev == $i) ? 'bg-primary text-white' : '' ?>" 
                           href="?page_ev=<?= $i ?>&page=<?= isset($_GET['page']) ? $_GET['page'] : 1 ?>#events-preview"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page_ev >= $total_ev_pages) ? 'disabled' : '' ?>">
                    <a class="page-link border-0 shadow-sm mx-1 rounded-circle" href="?page_ev=<?= $page_ev + 1 ?>&page=<?= isset($_GET['page']) ? $_GET['page'] : 1 ?>#events-preview">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</section>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true
    });
</script>

<?php include 'footer.php'; ?>