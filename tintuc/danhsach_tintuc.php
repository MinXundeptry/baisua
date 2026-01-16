<?php
include '../header.php';
include '../connect.php';

/* ========= LỌC CLB ========= */
$id_clb_filter = isset($_GET['id_clb']) ? intval($_GET['id_clb']) : 0;

/* ========= PHÂN TRANG ========= */
$limit = 6; // 6 tin / trang
$page = isset($_GET['p_news']) ? intval($_GET['p_news']) : 1;
if ($page < 1) $page = 1;
$start = ($page - 1) * $limit;

$where_clause = ($id_clb_filter > 0)
    ? "WHERE t.id_clb = $id_clb_filter"
    : "";

/* ========= ĐẾM TỔNG TIN ========= */
$count_sql = "SELECT COUNT(*) AS total FROM tintuc t $where_clause";
$count_res = $conn->query($count_sql);
$total_news = $count_res->fetch_assoc()['total'];
$total_pages = ceil($total_news / $limit);

/* ========= LẤY DANH SÁCH TIN ========= */
$sql = "
SELECT t.*, c.ten_clb
FROM tintuc t
LEFT JOIN clb c ON t.id_clb = c.id
$where_clause
ORDER BY t.ngay_dang DESC
LIMIT $start, $limit
";
$result = $conn->query($sql);
?>

<div class="container py-5">

    <!-- TIÊU ĐỀ + LỌC -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">📰 Tin tức CLB</h2>

        <div class="d-flex gap-2 align-items-center">

            <!-- LỌC CLB -->
            <form method="get">
                <select name="id_clb"
                        class="form-select rounded-pill shadow-sm"
                        onchange="this.form.submit()">
                    <option value="0">Tất cả CLB</option>
                    <?php
                    $clb = $conn->query("SELECT id, ten_clb FROM clb");
                    while ($c = $clb->fetch_assoc()):
                    ?>
                        <option value="<?= $c['id'] ?>"
                            <?= ($id_clb_filter == $c['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['ten_clb']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>

            <!-- NÚT THÊM (ADMIN) -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <a href="them_tintuc.php" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> Thêm tin
                </a>
            <?php endif; ?>

        </div>
    </div>

    <!-- DANH SÁCH TIN -->
    <div class="row g-4">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">

                        <a href="chitiet_tintuc.php?id=<?= $row['id'] ?>">
                            <img src="<?= htmlspecialchars($row['hinh_anh']) ?>"
                                 alt="<?= htmlspecialchars($row['tieu_de']) ?>"
                                 class="news-img"
                                 style="height:220px; object-fit:cover; width:100%; border-radius:6px 6px 0 0;">
                        </a>

                        <div class="card-body d-flex flex-column">

                            <!-- CLB -->
                            <span class="badge bg-info mb-2">
                                <?= htmlspecialchars($row['ten_clb'] ?? 'Tin chung') ?>
                            </span>

                            <h5 class="card-title mb-2">
                                <a href="chitiet_tintuc.php?id=<?= $row['id'] ?>"
                                   class="text-dark text-decoration-none fw-bold">
                                    <?= htmlspecialchars($row['tieu_de']) ?>
                                </a>
                            </h5>

                            <p class="text-muted small mb-1">
                                📍 <?= htmlspecialchars($row['dia_diem']) ?>
                            </p>

                            <p class="text-muted small mb-2">
                                🗓 <?= date('d/m/Y', strtotime($row['ngay_dang'])) ?>
                            </p>

                            <p class="card-text text-secondary"
                               style="overflow:hidden; display:-webkit-box;
                               -webkit-line-clamp:3; -webkit-box-orient:vertical;">
                                <?= htmlspecialchars($row['noi_dung']) ?>
                            </p>

                            <div class="mt-auto d-flex justify-content-between align-items-center">

                                <a href="chitiet_tintuc.php?id=<?= $row['id'] ?>"
                                   class="btn btn-outline-primary btn-sm rounded-pill">
                                    Xem chi tiết
                                </a>

                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                    <div class="d-flex gap-2">
                                        <a href="sua_tintuc.php?id=<?= $row['id'] ?>"
                                           class="btn btn-warning btn-sm rounded-pill">
                                            Sửa
                                        </a>
                                        <a href="xoa_tintuc.php?id=<?= $row['id'] ?>"
                                           onclick="return confirm('Xóa tin này?')"
                                           class="btn btn-danger btn-sm rounded-pill">
                                            Xóa
                                        </a>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center text-muted py-5">
                Chưa có tin tức nào.
            </div>
        <?php endif; ?>
    </div>

    <!-- PHÂN TRANG -->
    <?php if ($total_pages > 1): ?>
    <nav class="mt-5">
        <ul class="pagination justify-content-center">

            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link border-0 shadow-sm mx-1 rounded-circle"
                   href="?id_clb=<?= $id_clb_filter ?>&p_news=<?= $page - 1 ?>">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link border-0 shadow-sm mx-1 rounded-circle"
                       href="?id_clb=<?= $id_clb_filter ?>&p_news=<?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link border-0 shadow-sm mx-1 rounded-circle"
                   href="?id_clb=<?= $id_clb_filter ?>&p_news=<?= $page + 1 ?>">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>

        </ul>
    </nav>
    <?php endif; ?>

</div>

<?php include '../footer.php'; ?>
