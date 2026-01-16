<?php
include '../connect.php';
include '../header.php';

$message = "";

// Khi người dùng bấm nút "Đặt lại mật khẩu"
if (isset($_POST['btnLayLaiMK'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $sdt      = mysqli_real_escape_string($conn, $_POST['sdt']); 
    $new_pass = $_POST['new_pass']; 
    $re_pass  = $_POST['re_pass'];  

    // 1. Kiểm tra xem Tên đăng nhập và SĐT có khớp trong Database không
    $check = $conn->query("SELECT id FROM taikhoan WHERE username='$username' AND sdt='$sdt'");

    if ($check->num_rows > 0) {
        // Tìm thấy tài khoản -> Kiểm tra mật khẩu mới và nhập lại có giống nhau không
        if ($new_pass !== $re_pass) {
            $message = "<div class='alert alert-danger'>Mật khẩu nhập lại không khớp!</div>";
        } else {
            // 2. Cập nhật mật khẩu mới (Lưu trực tiếp, không mã hóa)
            $conn->query("UPDATE taikhoan SET password='$new_pass' WHERE username='$username'");
            
            echo "<script>
                    alert('Thành công! Mật khẩu đã được đổi. Hãy đăng nhập lại.');
                    window.location.href='dangnhap.php';
                  </script>";
        }
    } else {
        // Không tìm thấy tài khoản nào khớp thông tin
        $message = "<div class='alert alert-danger'>Tên đăng nhập hoặc Số điện thoại không đúng!</div>";
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="card shadow mx-auto border-0" style="max-width: 500px; border-radius: 15px;">
        <div class="card-body p-5">
            <h3 class="text-center mb-4 fw-bold text-warning">QUÊN MẬT KHẨU?</h3>
            <p class="text-center text-muted small mb-4">Nhập thông tin để lấy lại mật khẩu.</p>
            
            <?= $message ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Tên đăng nhập (Mã SV)</label>
                    <input type="text" name="username" class="form-control" required placeholder="Nhập mã sinh viên">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Số điện thoại đã đăng ký</label>
                    <input type="text" name="sdt" class="form-control" required placeholder="Nhập SĐT của bạn">
                </div>

                <hr class="my-4">

                <div class="mb-3">
                    <label class="form-label fw-bold small">Mật khẩu mới</label>
                    <input type="password" name="new_pass" class="form-control" required placeholder="Nhập mật khẩu mới">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Nhập lại mật khẩu mới</label>
                    <input type="password" name="re_pass" class="form-control" required placeholder="Xác nhận mật khẩu">
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" name="btnLayLaiMK" class="btn btn-warning btn-lg rounded-pill shadow-sm fw-bold text-white">
                        ĐẶT LẠI MẬT KHẨU
                    </button>
                </div>
                
                <div class="text-center mt-3">
                    <a href="dangnhap.php" class="text-decoration-none small text-muted">Quay lại Đăng nhập</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>