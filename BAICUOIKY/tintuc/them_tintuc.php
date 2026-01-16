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

if (isset($_POST['submit'])) {
    $tieu_de = $_POST['tieu_de'];
    $noi_dung = $_POST['noi_dung'];
    $dia_diem = $_POST['dia_diem'];
    $ngay_dang = date('Y-m-d H:i:s');
    
    // Thay đổi: Nhận link URL thay vì file upload
    $hinh_anh = $_POST['hinh_anh'];

    $sql = "INSERT INTO tintuc (tieu_de, hinh_anh, noi_dung, ngay_dang, dia_diem)
            VALUES ('$tieu_de', '$hinh_anh', '$noi_dung', '$ngay_dang', '$dia_diem')";
    $conn->query($sql);

    header("Location: danhsach_tintuc.php");
}
?>

<div class="container py-5">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Thêm tin tức mới</h4>
            </div>

            <div class="card-body p-4">
                <form method="post"> <div class="mb-3">
                        <label class="form-label fw-semibold">Tiêu đề tin</label>
                        <input type="text" name="tieu_de" class="form-control" placeholder="Nhập tiêu đề..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Địa điểm</label>
                        <input type="text" name="dia_diem" class="form-control" placeholder="Ví dụ: Hội trường A" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Link ảnh đại diện (URL)</label>
                        <input type="url" name="hinh_anh" class="form-control" placeholder="Dán link ảnh từ internet vào đây..." required>
                        <small class="text-muted">Ví dụ: https://example.com/anh.jpg</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Nội dung</label>
                        <textarea name="noi_dung" rows="6" class="form-control" placeholder="Nội dung bài viết..." required></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="danhsach_tintuc.php" class="btn btn-secondary">← Quay lại</a>
                        <button name="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle"></i> Đăng tin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>