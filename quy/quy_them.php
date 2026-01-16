<?php
include '../header.php';
include '../connect.php';

$loai   = $_GET['loai'] ?? 'thu';
$id_clb = $_SESSION['id_clb'];
?>

<h4>Thêm khoản <?= $loai == 'thu' ? 'thu' : 'chi' ?></h4>

<form method="post">
    <input type="number" name="so_tien" class="form-control mb-2" placeholder="Số tiền" required>
    <input type="text" name="noi_dung" class="form-control mb-2" placeholder="Nội dung" required>
    <input type="date" name="ngay" class="form-control mb-2" required>
    <textarea name="ghi_chu" class="form-control mb-2" placeholder="Ghi chú"></textarea>
    <button class="btn btn-primary">Lưu</button>
</form>

<?php
if ($_POST) {
    $conn->query("
        INSERT INTO quy_clb(id_clb, loai, so_tien, noi_dung, ngay, ghi_chu, nguoi_thuc_hien)
        VALUES (
            $id_clb,
            '$loai',
            {$_POST['so_tien']},
            '{$_POST['noi_dung']}',
            '{$_POST['ngay']}',
            '{$_POST['ghi_chu']}',
            '{$_SESSION['username']}'
        )
    ");
    echo "<script>location.href='quy_clb.php'</script>";
}
?>
