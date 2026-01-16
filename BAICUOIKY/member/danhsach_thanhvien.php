<?php 
include '../header.php'; 
include_once __DIR__ . '/../connect.php'; 

// 1. Xử lý đầu vào
$id_clb_filter = isset($_GET['id_clb']) ? intval($_GET['id_clb']) : 0;
$keyword = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

// 2. Logic phân quyền CLB cho Chủ nhiệm
if ($id_clb_filter <= 0 && isset($_SESSION['role'])) {
    $role_check = trim(mb_strtolower($_SESSION['role'], 'UTF-8'));
    if ($role_check === 'chunhiem' && isset($_SESSION['id_clb'])) {
        $id_clb_filter = intval($_SESSION['id_clb']);
    }
}

// 3. Phân trang
$limit = 8; 
$page = isset($_GET['p_mem']) ? intval($_GET['p_mem']) : 1;
$start = ($page - 1) * $limit;

// 4. Xây dựng Where Clause
$conditions = [];
if ($id_clb_filter > 0) $conditions[] = "t.id_clb = $id_clb_filter";
if (!empty($keyword)) $conditions[] = "(t.hoten LIKE '%$keyword%' OR t.masv LIKE '%$keyword%' OR t.ban LIKE '%$keyword%')";
$where_clause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

$total_res = $conn->query("SELECT COUNT(*) as total FROM thanhvien t $where_clause");
$total_mem = $total_res->fetch_assoc()['total'];
$total_pages = ceil($total_mem / $limit);

$can_add_main = false;
if (isset($_SESSION['role'])) {
    $sess_role = trim(mb_strtolower($_SESSION['role'], 'UTF-8'));
    if ($sess_role === 'admin' || ($sess_role === 'chunhiem' && $id_clb_filter == ($_SESSION['id_clb'] ?? 0))) {
        $can_add_main = true;
    }
}
?>

<link rel="stylesheet" href="../css/danhsach_thanhvien.css?v=<?= time(); ?>">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <?php 
                if ($id_clb_filter > 0) {
                    $clb_q = $conn->query("SELECT ten_clb FROM clb WHERE id = $id_clb_filter");
                    $clb_name = ($clb_q->num_rows > 0) ? $clb_q->fetch_assoc()['ten_clb'] : 'Câu lạc bộ';
                    echo "Thành Viên: <span style='color: #d81b1b;'>" . htmlspecialchars($clb_name) . "</span>";
                } else {
                    echo "Danh Sách <span style='color: #d81b1b;'>Thành Viên</span>";
                }
                ?>
            </h2>
            <p class="text-muted small">Quản lý nhân sự và thành viên hệ thống</p>
        </div>
        
        <?php if ($can_add_main && $id_clb_filter > 0): ?>
            <a href="them_thanhvien.php?id_clb=<?= $id_clb_filter ?>" class="btn btn-danger rounded-pill px-4 shadow-sm fw-bold" style="background-color: #d81b1b;">
                <i class="bi bi-person-plus-fill me-2"></i>Thêm Thành Viên
            </a>
        <?php endif; ?>
    </div>

    <div class="card mb-5 border-0 shadow-sm rounded-4">
        <div class="card-body p-3">
            <form action="" method="GET" class="row g-2">
                <input type="hidden" name="id_clb" value="<?= $id_clb_filter ?>">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-0 bg-light px-3 py-2 rounded-end-pill" 
                               placeholder="Tìm tên, mã sinh viên, ban chuyên môn..." value="<?= htmlspecialchars($keyword) ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 fw-bold">Tìm kiếm</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <?php
        $sql = "SELECT t.*, c.ten_clb FROM thanhvien t LEFT JOIN clb c ON t.id_clb = c.id $where_clause ORDER BY t.id DESC LIMIT $start, $limit";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
                $can_edit_card = false;
                if (isset($_SESSION['role'])) {
                    $r = trim(mb_strtolower($_SESSION['role'], 'UTF-8'));
                    if ($r === 'admin' || ($r === 'chunhiem' && $row['id_clb'] == ($_SESSION['id_clb'] ?? 0))) $can_edit_card = true;
                }
        ?>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 card-hover text-center p-3">
                <div class="card-body d-flex flex-column">
                    <div class="mb-3">
                        <span class="clb-badge-custom shadow-sm">
                            <i class="bi bi-shield-check"></i>
                            <?= htmlspecialchars($row['ten_clb'] ?? 'Tự do') ?>
                        </span>
                    </div>
                    
                    <div class="avatar-circle">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    
                    <h5 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($row['hoten']) ?></h5>
                    <p class="text-muted small mb-3">MSV: <?= htmlspecialchars($row['masv']) ?></p>
                    
                    <div class="mb-3">
                        <div class="ban-highlight text-uppercase mb-1">
                            <?= htmlspecialchars($row['ban'] ?: 'CHƯA XẾP BAN') ?>
                        </div>
                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($row['chucvu']) ?></span>
                    </div>

                    <div class="mt-auto pt-3 border-top">
                        <div class="text-muted small mb-3">
                            <i class="bi bi-calendar3 me-1"></i> <?= date("d/m/Y", strtotime($row['ngaythamgia'])) ?>
                        </div>
                        
                        <?php if ($can_edit_card): ?>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="sua_thanhvien.php?id=<?= $row['id'] ?>" class="text-warning fs-5"><i class="bi bi-pencil-square"></i></a>
                                <a href="xoa_thanhvien.php?id=<?= $row['id'] ?>" class="text-danger fs-5" onclick="return confirm('Xác nhận xóa thành viên?')"><i class="bi bi-trash"></i></a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-person-x fs-1 text-muted"></i>
                <p class="text-muted mt-3">Không tìm thấy thành viên phù hợp.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <nav class="mt-5">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?id_clb=<?= $id_clb_filter ?>&q=<?= urlencode($keyword) ?>&p_mem=<?= $page - 1 ?>"><i class="bi bi-chevron-left"></i></a>
            </li>
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="?id_clb=<?= $id_clb_filter ?>&q=<?= urlencode($keyword) ?>&p_mem=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?id_clb=<?= $id_clb_filter ?>&q=<?= urlencode($keyword) ?>&p_mem=<?= $page + 1 ?>"><i class="bi bi-chevron-right"></i></a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<?php include '../footer.php'; ?>