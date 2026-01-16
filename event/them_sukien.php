<?php 
// 1. Xử lý PHP trước để tránh lỗi Header
include '../connect.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_clb = $_POST['id_clb'];
    $ten = $_POST['ten'];
    $ngay = $_POST['ngay'];
    $diadiem = $_POST['diadiem'];
    $soluong = $_POST['soluong'];
    $mota = $_POST['mota']; // Lấy dữ liệu mô tả mới

    $sql = "INSERT INTO sukien (id_clb, ten, ngay, diadiem, soluong, mota) 
            VALUES ('$id_clb', '$ten', '$ngay', '$diadiem', '$soluong', '$mota')";

    if ($conn->query($sql) === TRUE) {
        header("Location: danhsach_sukien.php");
        exit();
    } else {
        $error = "Lỗi: " . $conn->error;
    }
}

include '../header.php'; // Gọi header sau khi đã xử lý logic PHP xong
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-4 p-4">
                <h2 class="text-center fw-bold text-primary mb-4">Thêm Sự Kiện Mới</h2>
                
                <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Câu lạc bộ tổ chức</label>
                        <select name="id_clb" class="form-select" required>
                            <option value="">-- Chọn câu lạc bộ --</option>
                            <?php
                            $clbs = $conn->query("SELECT id, ten_clb FROM clb");
                            while($row = $clbs->fetch_assoc()) {
                                echo "<option value='".$row['id']."'>".$row['ten_clb']."</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên sự kiện</label>
                        <input type="text" name="ten" class="form-control" placeholder="Nhập tên sự kiện..." required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ngày tổ chức</label>
                            <input type="date" name="ngay" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Số lượng tham gia</label>
                            <input type="number" name="soluong" class="form-control" value="50">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Địa điểm</label>
                        <input type="text" name="diadiem" class="form-control" placeholder="Ví dụ: Hội trường A" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Mô tả nội dung</label>
                        <textarea name="mota" class="form-control" rows="5" placeholder="Viết mô tả ngắn gọn về sự kiện..."></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold">Lưu Sự Kiện</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>