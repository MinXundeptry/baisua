<?php
include '../connect.php';
include '../header.php';

if (isset($_POST['btnDangKy'])) {
    // Lấy dữ liệu và bảo mật đầu vào
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // Lấy mật khẩu thô (Không mã hóa)
    $hoten    = mysqli_real_escape_string($conn, $_POST['hoten']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $sdt      = mysqli_real_escape_string($conn, $_POST['sdt']);
   
    // 1. Kiểm tra xem tên đăng nhập đã tồn tại chưa
    $check_sql = "SELECT id FROM taikhoan WHERE username = '$username'";
    $check_res = $conn->query($check_sql);

    if ($check_res->num_rows > 0) {
        echo "<script>alert('Tên đăng nhập (Mã SV) này đã tồn tại!');</script>";
    } else {
        // 2. Thêm vào bảng taikhoan (Lưu trực tiếp $password)
        $sql = "INSERT INTO taikhoan (username, password, hoten, email, sdt, vaitro)
                VALUES ('$username', '$password', '$hoten', '$email', '$sdt', 'thanhvien')";
       
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Đăng ký thành công! Hãy đăng nhập.'); window.location.href='dangnhap.php';</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="card shadow mx-auto border-0" style="max-width: 500px; border-radius: 15px;">
        <div class="card-body p-5">
            <h3 class="text-center mb-4 fw-bold text-success">ĐĂNG KÝ THÀNH VIÊN</h3>
            <form method="POST">
                
                <div class="mb-3">
                    <label class="form-label fw-bold small">Họ và Tên</label>
                    <input type="text" name="hoten" class="form-control" placeholder="Nhập tên của bạn" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Số điện thoại</label>
                    <input type="text" name="sdt" class="form-control" placeholder="Nhập số điện thoại" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Tên đăng nhập (Mã SV)</label>
                    <input type="text" name="username" class="form-control" placeholder="Nhập mã sinh viên" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" name="btnDangKy" class="btn btn-success btn-lg rounded-pill shadow-sm fw-bold">ĐĂNG KÝ NGAY</button>
                </div>

                <div class="text-center mt-3 small">
                    Đã có tài khoản? <a href="dangnhap.php" class="text-decoration-none">Đăng nhập</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>