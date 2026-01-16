<?php
session_start();
include '../connect.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Vui lòng đăng nhập!'); window.location.href='../dangnhap.php';</script>";
    exit();
}

// 2. Lấy ID CLB từ URL
$id_clb = isset($_GET['id_clb']) ? intval($_GET['id_clb']) : 0;
if ($id_clb == 0) {
    echo "<script>alert('Không xác định được CLB!'); window.location.href='../index.php';</script>";
    exit();
}

// Lấy tên CLB để hiển thị lên tiêu đề form
$clb_res = $conn->query("SELECT ten_clb FROM clb WHERE id = $id_clb");
$clb_name = $clb_res->fetch_assoc()['ten_clb'] ?? "Câu lạc bộ";

$username = $_SESSION['username'];
// Lấy thông tin tài khoản sẵn có
$user_query = $conn->query("SELECT id, hoten FROM taikhoan WHERE username = '$username'");
$user_data = $user_query->fetch_assoc();
$id_nguoidung = $user_data['id'];

// 3. Xử lý khi người dùng nhấn nút "Gửi đơn đăng ký" trong Form
if (isset($_POST['btnGoiDon'])) {
    // Kiểm tra xem đã gửi yêu cầu chưa
    $check = $conn->query("SELECT * FROM dangkyclb WHERE id_taikhoan = '$id_nguoidung' AND id_clb = '$id_clb'");

    if ($check && $check->num_rows > 0) {
        echo "<script>alert('Bạn đã gửi yêu cầu cho CLB này rồi!'); window.location.href='../dashboard.php?id_clb=$id_clb';</script>";
        exit();
    } else {
        $sql = "INSERT INTO dangkyclb (id_taikhoan, id_clb, ngay_dang_ky, trang_thai) 
                VALUES ('$id_nguoidung', '$id_clb', NOW(), 'Chờ duyệt')";
        
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Gửi đơn đăng ký thành công! Vui lòng chờ duyệt.'); window.location.href='../dashboard.php?id_clb=$id_clb';</script>";
            exit();
        } else {
            $error = "Lỗi: " . $conn->error;
        }
    }
}

include '../header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white text-center py-3 rounded-top-4">
                    <h4 class="mb-0">Đơn Đăng Ký Tham Gia</h4>
                </div>
                <div class="card-body p-4">
                    <p class="text-center text-muted">Bạn đang đăng ký gia nhập: <br> 
                       <strong class="text-dark fs-5"><?= htmlspecialchars($clb_name) ?></strong>
                    </p>
                    <hr>

                    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Họ và Tên</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user_data['hoten']) ?>" readonly>
                            <small class="text-muted italic">* Thông tin lấy từ tài khoản của bạn</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên đăng nhập / Mã SV</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($username) ?>" readonly>
                        </div>

                        <div class="mb-4 text-center">
                            <p class="small text-secondary">Bằng cách nhấn nút dưới đây, thông tin của bạn sẽ được gửi tới Ban quản lý CLB để xét duyệt.</p>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="btnGoiDon" class="btn btn-primary btn-lg rounded-pill shadow">
                                <i class="bi bi-send-fill"></i> Xác Nhận Gửi Đơn
                            </button>
                            <a href="../dashboard.php?id_clb=<?= $id_clb ?>" class="btn btn-light border rounded-pill">Hủy bỏ</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>