<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include '../connect.php';
include '../header.php';

// Nếu đã đăng nhập thì chuyển hướng về trang chủ
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_POST['btnDangNhap'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Tìm user trong database
    $stmt = $conn->prepare("SELECT * FROM taikhoan WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // --- SO SÁNH TRỰC TIẾP (KHÔNG MÃ HÓA) ---
        if ($password == $user['password']) {
            
            // Lưu Session
            $_SESSION['user_id']  = $user['id']; 
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['vaitro']; 
            $_SESSION['hoten']    = $user['hoten'];
            $_SESSION['id_clb']   = $user['id_clb']; 

            echo "<script>alert('Đăng nhập thành công! Chào mừng " . $user['hoten'] . "'); window.location.href='../index.php';</script>";
        } else {
            echo "<script>alert('Mật khẩu không chính xác!');</script>";
        }
    } else {
        echo "<script>alert('Tên đăng nhập không tồn tại!');</script>";
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="card shadow-lg mx-auto border-0" style="max-width: 450px; border-radius: 20px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <div class="display-4 text-primary"><i class="bi bi-person-circle"></i></div>
                <h3 class="fw-bold text-uppercase mt-2">Đăng Nhập</h3>
                <p class="text-muted small">Hệ thống quản lý CLB Sinh viên</p>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên đăng nhập</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" class="form-control bg-light border-start-0" placeholder="Mã sinh viên" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control bg-light border-start-0" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="text-end mb-3">
                    <a href="quen_mat_khau.php" class="small text-decoration-none text-muted hover-link">
                        <i class="bi bi-question-circle me-1"></i>Quên mật khẩu?
                    </a>
                </div>

                <div class="d-grid">
                    <button type="submit" name="btnDangNhap" class="btn btn-primary btn-lg rounded-pill shadow-sm fw-bold">
                        VÀO HỆ THỐNG
                    </button>
                </div>

                <div class="text-center mt-4">
                    <span class="text-muted">Chưa có tài khoản?</span> 
                    <a href="dangky.php" class="text-decoration-none fw-bold text-primary">Đăng ký ngay</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Hiệu ứng nhỏ khi di chuột vào link Quên mật khẩu */
    .hover-link:hover {
        color: #0d6efd !important; /* Màu xanh bootstrap */
        text-decoration: underline !important;
    }
</style>

<?php include '../footer.php'; ?>