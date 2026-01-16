<?php
include '../connect.php';
include '../header.php';

if (!isset($_GET['id'])) {
    echo "<div class='container py-5 text-center'>Không tìm thấy tin tức.</div>";
    include '../footer.php';
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM tintuc WHERE id = $id";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    echo "<div class='container py-5 text-center'>Tin tức không tồn tại.</div>";
    include '../footer.php';
    exit;
}

$row = $result->fetch_assoc();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="danhsach_tintuc.php">Tin tức</a></li>
                    <li class="breadcrumb-item active">Chi tiết</li>
                </ol>
            </nav>

            <h1 class="fw-bold mb-3"><?= htmlspecialchars($row['tieu_de']) ?></h1>

            <div class="text-muted mb-4">
                <span class="me-3">📍 <?= htmlspecialchars($row['dia_diem']) ?></span>
                <span>🗓 <?= date('d/m/Y', strtotime($row['ngay_dang'])) ?></span>
            </div>

            <img src="<?= htmlspecialchars($row['hinh_anh']) ?>" 
                 class="img-fluid rounded mb-4 shadow-sm" 
                 style="max-height:500px; width:100%; object-fit:cover;">

            <div class="content-text" style="font-size:1.1rem; line-height:1.8; text-align: justify;">
                <?= nl2br(htmlspecialchars($row['noi_dung'])) ?>
            </div>

            <div class="mt-5 pt-4 border-top">
                <a href="danhsach_tintuc.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>