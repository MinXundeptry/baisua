<?php
// Vì file này nằm trong thư mục 'login', nên phải lùi ra ngoài 1 cấp (../) để tìm connect.php
include '../connect.php'; 
include '../header.php'; 

// Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: ../dangnhap.php"); // Sửa đường dẫn về trang đăng nhập
    exit();
}

$username = $_SESSION['username'];
$message = ""; 

// --- XỬ LÝ 1: CẬP NHẬT THÔNG TIN & AVATAR ---
if (isset($_POST['btnUpdateInfo'])) {
    $hoten = mysqli_real_escape_string($conn, $_POST['hoten']);
    $email = mysqli_real_escape_string($conn, $_POST['email']); 
    $sdt   = mysqli_real_escape_string($conn, $_POST['sdt']);
    
    // --- KHẮC PHỤC LỖI FETCH_ASSOC Ở ĐÂY ---
    // 1. Lấy thông tin avatar cũ
    $sql_old = "SELECT avatar FROM taikhoan WHERE username='$username'";
    $res_old = $conn->query($sql_old);

    // Kiểm tra nếu SQL bị lỗi
    if (!$res_old) {
        die("Lỗi SQL: " . $conn->error . ". <br>Gợi ý: Có thể bảng 'taikhoan' chưa có cột 'avatar'. Hãy vào phpMyAdmin chạy lệnh: ALTER TABLE taikhoan ADD COLUMN avatar VARCHAR(255) NULL;");
    }

    $row_old = $res_old->fetch_assoc();
    $avatar_name = $row_old['avatar'];

    // 2. Xử lý file upload
    if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] == 0) {
        // SỬA ĐƯỜNG DẪN UPLOAD: Thêm ../ để lùi ra thư mục gốc chứa folder uploads
        $target_dir = "../uploads/"; 
        
        // Kiểm tra xem thư mục uploads có tồn tại không
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true); // Tạo thư mục nếu chưa có
        }

        $file_extension = pathinfo($_FILES["avatar_file"]["name"], PATHINFO_EXTENSION);
        $new_file_name = time() . "_" . $username . "." . $file_extension;
        $target_file = $target_dir . $new_file_name;
        
        $allow_types = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array(strtolower($file_extension), $allow_types)) {
            if (move_uploaded_file($_FILES["avatar_file"]["tmp_name"], $target_file)) {
                $avatar_name = $new_file_name;
            } else {
                $message = "<div class='alert alert-danger'>Lỗi: Không thể lưu file vào thư mục uploads! Hãy kiểm tra quyền ghi file.</div>";
            }
        } else {
            $message = "<div class='alert alert-warning'>Chỉ chấp nhận file ảnh (JPG, PNG, GIF)!</div>";
        }
    }

    $sql_update = "UPDATE taikhoan SET hoten='$hoten', email='$email', sdt='$sdt', avatar='$avatar_name' WHERE username='$username'";
    
    if ($conn->query($sql_update)) {
        echo "<script>alert('Cập nhật hồ sơ thành công!'); window.location.href='ho_so.php';</script>";
    } else {
        $message = "<div class='alert alert-danger'>Lỗi SQL: " . $conn->error . "</div>";
    }
}

// --- XỬ LÝ 2: ĐỔI MẬT KHẨU ---
if (isset($_POST['btnChangePass'])) {
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $re_pass  = $_POST['re_pass'];

    $check = $conn->query("SELECT password FROM taikhoan WHERE username='$username'");
    $row_check = $check->fetch_assoc();
    $current_pass_db = $row_check['password'];

    // So sánh trực tiếp (nếu bạn không dùng mã hóa)
    if ($old_pass != $current_pass_db) {
        $message = "<div class='alert alert-danger'>Mật khẩu cũ không chính xác!</div>";
    } elseif ($new_pass != $re_pass) {
        $message = "<div class='alert alert-danger'>Mật khẩu nhập lại không khớp!</div>";
    } else {
        $conn->query("UPDATE taikhoan SET password='$new_pass' WHERE username='$username'");
        echo "<script>alert('Đổi mật khẩu thành công! Vui lòng đăng nhập lại.'); window.location.href='dangxuat.php';</script>";
    }
}

// LẤY DỮ LIỆU HIỂN THỊ
$user_info = $conn->query("SELECT * FROM taikhoan WHERE username = '$username'")->fetch_assoc();
$res_reg   = $conn->query("SELECT d.*, s.ten as ten_sk, s.ngay as ngay_sk 
                           FROM dangky d JOIN sukien s ON d.id_sukien = s.id 
                           WHERE d.masv = '$username' ORDER BY d.id DESC");

$is_admin = ($user_info['vaitro'] == 'admin');
?>

<div class="container py-5">
    <?= $message ?>
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0 text-center p-4">
                <div class="mx-auto mb-3" style="width: 120px; height: 120px; overflow: hidden; border-radius: 50%; border: 4px solid #f8f9fa; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                    <?php if (!empty($user_info['avatar']) && file_exists("../uploads/" . $user_info['avatar'])): ?>
                        <img src="../uploads/<?= $user_info['avatar'] ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-primary text-white d-flex align-items-center justify-content-center h-100" style="font-size: 40px;">
                            <?= strtoupper(substr($user_info['username'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <h4 class="fw-bold"><?= htmlspecialchars($user_info['hoten']) ?></h4>
                <p class="text-muted small mb-3">@<?= htmlspecialchars($user_info['username']) ?></p>
                <span class="badge <?= $is_admin ? 'bg-danger' : 'bg-info' ?> rounded-pill px-3 py-2 mb-4">
                    <?= $is_admin ? 'Quản trị viên' : 'Thành viên CLB' ?>
                </span>
                <hr>
                <div class="text-start small">
                    <p class="mb-2"><i class="bi bi-envelope me-2"></i> <?= !empty($user_info['email']) ? $user_info['email'] : 'Chưa cập nhật' ?></p>
                    <p class="mb-2"><i class="bi bi-telephone me-2"></i> <?= !empty($user_info['sdt']) ? $user_info['sdt'] : 'Chưa cập nhật' ?></p>
                    <p><strong><i class="bi bi-calendar3 me-2"></i>Ngày tham gia:</strong> <br><?= date('d/m/Y', strtotime($user_info['ngaytao'])) ?></p>
                </div>
                <a href="dangxuat.php" class="btn btn-outline-danger btn-sm mt-3 w-100">Đăng xuất</a>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow border-0 p-4">
                <ul class="nav nav-tabs mb-4" id="profileTab" role="tablist">
                    <?php if (!$is_admin): ?>
                    <li class="nav-item">
                        <button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#history">Lịch sử đăng ký</button>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <button class="nav-link fw-bold <?= $is_admin ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#profile">Cập nhật hồ sơ</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#password">Đổi mật khẩu</button>
                    </li>
                </ul>

                <div class="tab-content" id="profileTabContent">
                    <?php if (!$is_admin): ?>
                    <div class="tab-pane fade show active" id="history">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light"><tr><th>Tên Sự Kiện</th><th>Ngày</th><th>Trạng Thái</th></tr></thead>
                                <tbody>
                                    <?php if ($res_reg->num_rows > 0): ?>
                                        <?php while($reg = $res_reg->fetch_assoc()): ?>
                                            <tr>
                                                <td><span class="fw-bold text-dark"><?= htmlspecialchars($reg['ten_sk']) ?></span></td>
                                                <td><small><?= date('d/m/Y', strtotime($reg['ngay_sk'])) ?></small></td>
                                                <td><span class="badge bg-secondary"><?= $reg['trangthai'] ?></span></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="3" class="text-center py-4 text-muted small">Chưa đăng ký.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3"><a href="../event/danhsach_sukien.php" class="btn btn-primary btn-sm rounded-pill px-3">Sự kiện mới</a></div>
                    </div>
                    <?php endif; ?>

                    <div class="tab-pane fade <?= $is_admin ? 'show active' : '' ?>" id="profile">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">Ảnh đại diện</label>
                                    <input type="file" name="avatar_file" class="form-control">
                                    <div class="form-text small">Chấp nhận file ảnh: jpg, jpeg, png, gif</div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Họ và Tên</label>
                                    <input type="text" name="hoten" class="form-control" value="<?= htmlspecialchars($user_info['hoten']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user_info['email'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="text" name="sdt" class="form-control" value="<?= htmlspecialchars($user_info['sdt'] ?? '') ?>">
                                </div>
                            </div>
                            <button type="submit" name="btnUpdateInfo" class="btn btn-info text-white rounded-pill px-4">Lưu thay đổi</button>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="password">
                        <form method="POST">
                            <div class="mb-3"><label class="form-label">Mật khẩu cũ</label><input type="password" name="old_pass" class="form-control" required></div>
                            <div class="mb-3"><label class="form-label">Mật khẩu mới</label><input type="password" name="new_pass" class="form-control" required></div>
                            <div class="mb-3"><label class="form-label">Nhập lại mật khẩu mới</label><input type="password" name="re_pass" class="form-control" required></div>
                            <button type="submit" name="btnChangePass" class="btn btn-danger rounded-pill px-4">Đổi mật khẩu</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>