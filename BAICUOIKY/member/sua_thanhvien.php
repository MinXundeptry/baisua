<?php include '../header.php'; ?>
<?php include '../connect.php'; ?>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra nếu chưa đăng nhập HOẶC không phải admin thì đuổi ra trang chủ
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin' &&$_SESSION['role'] !== 'chunhiem') {
    echo "<script>
            alert('Bạn không có quyền truy cập trang quản trị!');
            window.location.href='index.php';
          </script>";
    exit();
}
?>
<?php
// Lấy ID từ URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM thanhvien WHERE id = $id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">Cập nhật thông tin thành viên</h4>
                </div>
                <div class="card-body">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $masv = $_POST['masv'];
                        $hoten = $_POST['hoten'];
                        $ban = $_POST['ban'];
                        $chucvu = $_POST['chucvu'];
                        $ngaythamgia = $_POST['ngaythamgia'];

                        $sql_update = "UPDATE thanhvien SET 
                                       masv='$masv', hoten='$hoten', ban='$ban', chucvu='$chucvu', ngaythamgia='$ngaythamgia' 
                                       WHERE id=$id";

                        if ($conn->query($sql_update) === TRUE) {
                            echo "<div class='alert alert-success'>Cập nhật thành công! <a href='danhsach_thanhvien.php'>Về danh sách</a></div>";
                            // Cập nhật lại dữ liệu hiển thị
                            $row['masv'] = $masv;
                            $row['hoten'] = $hoten;
                            $row['ban'] = $ban;
                            $row['chucvu'] = $chucvu;
                            $row['ngaythamgia'] = $ngaythamgia;
                        } else {
                            echo "<div class='alert alert-danger'>Lỗi: " . $conn->error . "</div>";
                        }
                    }
                    ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Mã Sinh Viên</label>
                            <input type="text" name="masv" class="form-control" value="<?php echo $row['masv']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Họ và Tên</label>
                            <input type="text" name="hoten" class="form-control" value="<?php echo $row['hoten']; ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ban</label>
                                <select name="ban" class="form-select">
                                    <option <?php if($row['ban'] == 'Truyền thông') echo 'selected'; ?>>Truyền thông</option>
                                    <option <?php if($row['ban'] == 'Kỹ thuật') echo 'selected'; ?>>Kỹ thuật</option>
                                    <option <?php if($row['ban'] == 'Hậu cần') echo 'selected'; ?>>Hậu cần</option>
                                    <option <?php if($row['ban'] == 'Đối ngoại') echo 'selected'; ?>>Đối ngoại</option>
                                    <option <?php if($row['ban'] == 'Văn nghệ') echo 'selected'; ?>>Văn nghệ</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Chức vụ</label>
                                <select name="chucvu" class="form-select">
                                    <option <?php if($row['chucvu'] == 'Thành viên') echo 'selected'; ?>>Thành viên</option>
                                    <option <?php if($row['chucvu'] == 'Trưởng ban') echo 'selected'; ?>>Trưởng ban</option>
                                    <option <?php if($row['chucvu'] == 'Phó chủ nhiệm') echo 'selected'; ?>>Phó chủ nhiệm</option>
                                    <option <?php if($row['chucvu'] == 'Chủ nhiệm') echo 'selected'; ?>>Chủ nhiệm</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ngày tham gia</label>
                            <input type="date" name="ngaythamgia" class="form-control" value="<?php echo $row['ngaythamgia']; ?>">
                        </div>
                        <button type="submit" class="btn btn-warning">Cập nhật</button>
                        <a href="danhsach_thanhvien.php" class="btn btn-secondary">Quay lại</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>