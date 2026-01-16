<?php
include '../header.php';
include '../connect.php';

$id = $_GET['id'];
$r = $conn->query("SELECT * FROM quy_clb WHERE id = $id")->fetch_assoc();
?>

<h4>Sửa khoản <?= $r['loai'] ?></h4>

<form method="post">
    <input type="number" name="so_tien" class="form-control mb-2" value="<?= $r['so_tien'] ?>" required>
    <input type="text" name="noi_dung" class="form-control mb-2" value="<?= $r['noi_dung'] ?>" required>
    <input type="date" name="ngay" class="form-control mb-2" value="<?= $r['ngay'] ?>" required>
    <textarea name="ghi_chu" class="form-control mb-2"><?= $r['ghi_chu'] ?></textarea>
    <button class="btn btn-success">Cập nhật</button>
</form>

<?php
if ($_POST) {
    $conn->query("
        UPDATE quy_clb SET
            so_tien = {$_POST['so_tien']},
            noi_dung = '{$_POST['noi_dung']}',
            ngay = '{$_POST['ngay']}',
            ghi_chu = '{$_POST['ghi_chu']}'
        WHERE id = $id
    ");
    echo "<script>location.href='quy_clb.php'</script>";
}
?>
