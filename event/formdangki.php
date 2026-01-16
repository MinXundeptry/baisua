<?php 
include '../connect.php'; 
include '../header.php'; 

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['username'])) {
    echo "<script>
            alert('Bạn cần đăng nhập tài khoản để đăng ký tham gia!');
            window.location.href='../login/dangnhap.php';
          </script>";
    exit(); // Dừng thực thi các mã bên dưới
}

// 2. XỬ LÝ KHI NHẤN NÚT GỬI FORM
if (isset($_POST['btnDangKy'])) {
    $id_sukien = mysqli_real_escape_string($conn, $_POST['id_sukien']);
    $masv = mysqli_real_escape_string($conn, $_POST['masv']);
    $hoten = mysqli_real_escape_string($conn, $_POST['hoten']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Kiểm tra trùng đăng ký
    $check = $conn->query("SELECT id FROM dangky WHERE id_sukien = '$id_sukien' AND masv = '$masv'");
    
    if ($check->num_rows > 0) {
        echo "<script>alert('Bạn đã đăng ký sự kiện này rồi!');</script>";
    } else {
        $sql = "INSERT INTO dangky (id_sukien, masv, hoten, email, trangthai) 
                VALUES ('$id_sukien', '$masv', '$hoten', '$email', 'Cho duyet')";
        if ($conn->query($sql)) {
            echo "<script>alert('Gửi đơn đăng ký thành công!'); window.location.href='danhsachdki.php';</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="card shadow mx-auto" style="max-width: 500px; border-radius: 15px;">
        <div class="card-body p-4">
            <h3 class="text-center mb-4 fw-bold text-primary">ĐƠN ĐĂNG KÝ</h3>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Chọn Sự Kiện</label>
                    <select name="id_sukien" class="form-select" required>
                        <option value="">-- Chọn sự kiện muốn tham gia --</option>
                        <?php 
                        // Lấy danh sách các sự kiện đang mở
                        $list_sk = $conn->query("SELECT id, ten FROM sukien ORDER BY ngay DESC");
                        while($sk = $list_sk->fetch_assoc()) {
                            // Tự động chọn sự kiện nếu có ID truyền từ trang danh sách
                            $selected = (isset($_GET['id_sukien']) && $_GET['id_sukien'] == $sk['id']) ? 'selected' : '';
                            echo "<option value='".$sk['id']."' $selected>".$sk['ten']."</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Mã Sinh Viên</label>
                    <input type="text" name="masv" class="form-control bg-light" 
                           value="<?= htmlspecialchars($_SESSION['username']) ?>" readonly required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Họ và Tên</label>
                    <input type="text" name="hoten" class="form-control" 
                           value="<?= htmlspecialchars($_SESSION['hoten'] ?? '') ?>" 
                           placeholder="Nhập họ tên" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Nhập email liên hệ" required>
                </div>
                
                <div class="d-grid mt-4">
                    <button type="submit" name="btnDangKy" class="btn btn-primary btn-lg rounded-pill shadow">GỬI ĐĂNG KÝ</button>
                    <a href="danhsachdki.php" class="btn btn-link text-muted mt-2">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>