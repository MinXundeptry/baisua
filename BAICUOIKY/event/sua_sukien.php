<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra nếu chưa đăng nhập HOẶC không phải admin thì đuổi ra trang chủ
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>
            alert('Bạn không có quyền truy cập trang quản trị!');
            window.location.href='index.php';
          </script>";
    exit();
}
?>
<?php 
include '../connect.php'; 
include '../header.php'; 

// 1. Lấy thông tin cũ của sự kiện dựa trên ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql_old = "SELECT * FROM sukien WHERE id = $id";
    $res = $conn->query($sql_old);
    $data = $res->fetch_assoc();
}

// 2. Xử lý khi nhấn nút Cập nhật
if (isset($_POST['btnCapNhat'])) {
    $ten = $_POST['ten'];
    $ngay = $_POST['ngay'];
    $diadiem = $_POST['diadiem'];
    $mota = $_POST['mota'];
    $soluong = $_POST['soluong'];

    $sql_update = "UPDATE sukien SET ten='$ten', ngay='$ngay', diadiem='$diadiem', mota='$mota', soluong='$soluong' WHERE id=$id";
    
    if ($conn->query($sql_update) === TRUE) {
        echo "<script>alert('Cập nhật thành công!'); window.location.href='danhsach_sukien.php';</script>";
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<div class="container mt-5">
    <div class="card shadow p-4 mx-auto" style="max-width: 600px;">
        <h3 class="text-center mb-4 text-warning">Chỉnh Sửa Sự Kiện</h3>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Tên sự kiện</label>
                <input type="text" name="ten" class="form-control" value="<?php echo $data['ten']; ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ngày tổ chức</label>
                    <input type="date" name="ngay" class="form-control" value="<?php echo $data['ngay']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Số lượng tham gia</label>
                    <input type="number" name="soluong" class="form-control" value="<?php echo $data['soluong']; ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Địa điểm</label>
                <input type="text" name="diadiem" class="form-control" value="<?php echo $data['diadiem']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mô tả nội dung</label>
                <textarea name="mota" class="form-control" rows="4"><?php echo $data['mota']; ?></textarea>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" name="btnCapNhat" class="btn btn-warning fw-bold">Cập Nhật</button>
                <a href="danhsach_sukien.php" class="btn btn-light">Hủy</a>
            </div>
        </form>
    </div>
</div>

<?php include '../footer.php'; ?>