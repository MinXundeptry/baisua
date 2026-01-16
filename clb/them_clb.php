<?php 
include '../header.php'; 
include '../connect.php'; 

if (isset($_POST['btnThemCLB'])) {  
    $ten_clb = mysqli_real_escape_string($conn, $_POST['ten_clb']);
    $mota = mysqli_real_escape_string($conn, $_POST['mota']);
    // Lấy link ảnh từ mạng
    $link_anh_nen = mysqli_real_escape_string($conn, $_POST['link_anh_nen']);

    // Câu lệnh thêm vào bảng clb bao gồm cột link_anh_nen
    $sql = "INSERT INTO clb (ten_clb, mota, link_anh_nen) VALUES ('$ten_clb', '$mota', '$link_anh_nen')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Thêm Câu lạc bộ mới thành công!'); window.location.href='index.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Lỗi: " . $conn->error . "</div>";
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <h3 class="fw-bold text-center text-primary mb-4">THÊM CLB MỚI</h3>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên Câu Lạc Bộ</label>
                            <input type="text" name="ten_clb" class="form-control form-control-lg bg-light" placeholder="Ví dụ: CLB Tin Học" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Link Ảnh Nền (URL)</label>
                            <input type="text" name="link_anh_nen" class="form-control bg-light" placeholder="Dán link ảnh từ mạng vào đây...">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Mô tả ngắn gọn</label>
                            <textarea name="mota" class="form-control bg-light" rows="4" placeholder="Nhập mục tiêu, hoạt động của CLB..." required></textarea>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="btnThemCLB" class="btn btn-primary btn-lg rounded-pill fw-bold">XÁC NHẬN THÊM</button>
                            <a href="index.php" class="btn btn-outline-secondary rounded-pill">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>