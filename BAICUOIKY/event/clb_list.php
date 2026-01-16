<?php 
session_start();
include '../connect.php'; 
include '../header.php'; 

// Lấy danh sách CLB
$sql = "SELECT * FROM clb";
$result = $conn->query($sql);
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">DANH SÁCH CÂU LẠC BỘ</h2>
    <div class="row">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-success fw-bold"><?php echo $row['ten_clb']; ?></h5>
                        <p class="card-text text-muted">Mô tả ngắn về CLB...</p>
                        
                        <form action="xuly_thamgia.php" method="POST">
                            <input type="hidden" name="id_clb" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="btnThamGia" class="btn btn-outline-success w-100 rounded-pill">
                                Tham gia ngay
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>