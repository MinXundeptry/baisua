<?php
include '../header.php';
include '../connect.php';

$id_clb = $_SESSION['id_clb'] ?? null;
$role   = $_SESSION['role'] ?? 'user';
?>

<div class="container py-4">
    <h3 class="mb-4">Quỹ CLB</h3>

<?php if (!$id_clb): ?>

    <div class="alert alert-danger text-center">
        Tài khoản chưa thuộc CLB nào nên không thể quản lý quỹ.
    </div>

<?php else: ?>

    <?php
    // TỔNG THU
    $thu = $conn->query("
        SELECT IFNULL(SUM(so_tien),0) AS tong
        FROM quy_clb
        WHERE id_clb = $id_clb AND loai = 'thu'
    ")->fetch_assoc()['tong'];

    // TỔNG CHI
    $chi = $conn->query("
        SELECT IFNULL(SUM(so_tien),0) AS tong
        FROM quy_clb
        WHERE id_clb = $id_clb AND loai = 'chi'
    ")->fetch_assoc()['tong'];

    $so_du = $thu - $chi;
    ?>

    <!-- THỐNG KÊ -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body text-success">
                    <h6>Tổng thu</h6>
                    <strong><?= number_format($thu) ?> đ</strong>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-danger">
                <div class="card-body text-danger">
                    <h6>Tổng chi</h6>
                    <strong><?= number_format($chi) ?> đ</strong>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body text-primary">
                    <h6>Số dư</h6>
                    <strong><?= number_format($so_du) ?> đ</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- NÚT THAO TÁC -->
    <?php if (in_array($role, ['admin','chunhiem'])): ?>
        <a href="quy_them.php?loai=thu" class="btn btn-success me-2">+ Thêm thu</a>
        <a href="quy_them.php?loai=chi" class="btn btn-danger">+ Thêm chi</a>
    <?php endif; ?>

    <hr>
    <h5 class="mt-4">Lịch sử thu – chi</h5>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Loại</th>
                <th>Nội dung</th>
                <th>Số tiền</th>
                <th>Ngày</th>
                <th>Ghi chú</th>
                <?php if (in_array($role,['admin','chunhiem'])): ?>
                    <th>Hành động</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>

        <?php
        $ds = $conn->query("
            SELECT * FROM quy_clb
            WHERE id_clb = $id_clb
            ORDER BY ngay DESC, id DESC
        ");

        while ($r = $ds->fetch_assoc()):
        ?>
            <tr>
                <td class="<?= $r['loai']=='thu'?'text-success':'text-danger' ?>">
                    <?= strtoupper($r['loai']) ?>
                </td>
                <td><?= $r['noi_dung'] ?></td>
                <td><?= number_format($r['so_tien']) ?> đ</td>
                <td><?= $r['ngay'] ?></td>
                <td><?= $r['ghi_chu'] ?></td>

                <?php if (in_array($role,['admin','chunhiem'])): ?>
                <td>
                    <a href="quy_sua.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                    <a href="quy_xoa.php?id=<?= $r['id'] ?>"
                       onclick="return confirm('Xoá khoản này?')"
                       class="btn btn-sm btn-danger">Xoá</a>
                </td>
                <?php endif; ?>
            </tr>
        <?php endwhile; ?>

        </tbody>
    </table>

<?php endif; ?>
</div>

<?php include '../footer.php'; ?>
