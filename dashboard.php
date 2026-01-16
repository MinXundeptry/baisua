<?php 
// 1. Khởi động hệ thống
include 'connect.php'; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Kiểm tra ID câu lạc bộ
if (!isset($_GET['id_clb'])) {
    header("Location: index.php");
    exit();
}

$id_clb = intval($_GET['id_clb']);
$_SESSION['current_clb_id'] = $id_clb; 

// 3. TRUY VẤN DỮ LIỆU CƠ BẢN
$sql_clb = "SELECT ten_clb FROM clb WHERE id = $id_clb";
$res_clb = $conn->query($sql_clb);
$clb_info = $res_clb->fetch_assoc();

if (!$clb_info) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0; 
$is_member = false;
$is_pending = false;

if ($user_id > 0) {
    $check_tv = $conn->query("SELECT id FROM thanhvien WHERE id_taikhoan = $user_id AND id_clb = $id_clb");
    if ($check_tv && $check_tv->num_rows > 0) $is_member = true;

    $check_dk = $conn->query("SELECT id FROM dangkyclb WHERE id_taikhoan = $user_id AND id_clb = $id_clb AND trang_thai = 'Chờ duyệt'");
    if ($check_dk && $check_dk->num_rows > 0) $is_pending = true;
}

/** * 4. LOGIC PHÂN QUYỀN (Thay đổi trọng tâm ở đây)
 */
$is_manager_power = false;    // Quyền Admin/Chủ nhiệm tại CLB hiện tại
$is_other_club_leader = false; // Là Chủ nhiệm nhưng đang ở CLB khác

if (isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
    
    // Admin (role 0) có quyền quản trị ở mọi CLB
    if ($role == '0' || $role == 'admin') {
        $is_manager_power = true;
    } 
    // Chủ nhiệm (role 1)
    elseif ($role == '1' || $role == 'chunhiem') {
        if (isset($_SESSION['id_clb']) && $_SESSION['id_clb'] == $id_clb) {
            $is_manager_power = true; // Là chủ nhiệm của CLB này -> Hiện Menu Quản trị
        } else {
            $is_other_club_leader = true; // Là chủ nhiệm CLB khác -> Sẽ dùng để ẩn mục Đăng ký
        }
    }
}

include 'header.php'; 
?>

<link href="css/index.css" rel="stylesheet">
<style>
    .card-custom { transition: all 0.3s cubic-bezier(.25,.8,.25,1); border: none; border-radius: 15px; overflow: hidden; }
    .card-custom:hover { transform: translateY(-7px); box-shadow: 0 14px 28px rgba(0,0,0,0.1), 0 10px 10px rgba(0,0,0,0.08) !important; }
    .icon-circle { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; }
</style>

<section class="hero text-center text-lg-start bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge bg-light text-primary mb-3 px-3 py-2 rounded-pill shadow-sm fw-bold">
                    <?= $is_manager_power ? 'CHẾ ĐỘ QUẢN TRỊ VIÊN' : ($is_other_club_leader ? 'CHẾ ĐỘ KHÁCH' : 'KHÔNG GIAN SINH VIÊN') ?>
                </span>
                <h1 class="mb-4 fw-bold display-4">
                    <?= htmlspecialchars($clb_info['ten_clb']) ?>
                </h1>
                <p class="mb-5 opacity-75 fs-5">Hệ thống quản lý thông tin và hoạt động thành viên dành riêng cho câu lạc bộ của bạn.</p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                    <a href="#modules" class="btn btn-warning text-dark fw-bold px-4 py-3 rounded-pill shadow">Truy cập chức năng</a>
                    <a href="index.php" class="btn btn-outline-light px-4 py-3 rounded-pill">Trang chủ</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="modules" class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">BẢNG ĐIỀU KHIỂN</h2>
            <div style="width: 50px; height: 3px; background: #ffc107; margin: 10px auto;"></div>
        </div>

        <div class="row g-4 justify-content-center">
            
            <?php if ($is_manager_power): ?>
                <div class="col-lg-4 col-md-6">
                    <a href="clb/duyet_dangky_clb.php?id_clb=<?= $id_clb ?>" class="text-decoration-none text-dark">
                        <div class="card card-custom shadow-sm p-4 text-center">
                            <div class="icon-circle bg-warning bg-opacity-10 text-warning fs-2"><i class="bi bi-person-check-fill"></i></div>
                            <h4 class="fw-bold">Duyệt Thành Viên</h4>
                            <p class="text-muted small">Kiểm duyệt các đơn xin gia nhập mới nhất của sinh viên.</p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-4 col-md-6">
                    <a href="member/danhsach_thanhvien.php?id_clb=<?= $id_clb ?>" class="text-decoration-none text-dark">
                        <div class="card card-custom shadow-sm p-4 text-center">
                            <div class="icon-circle bg-primary bg-opacity-10 text-primary fs-2"><i class="bi bi-people-fill"></i></div>
                            <h4 class="fw-bold">Quản Lý Thành Viên</h4>
                            <p class="text-muted small">Xem danh sách, phân quyền và quản lý thông tin hội viên.</p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-4 col-md-6">
                    <a href="event/danhsach_sukien.php?id_clb=<?= $id_clb ?>" class="text-decoration-none text-dark">
                        <div class="card card-custom shadow-sm p-4 text-center">
                            <div class="icon-circle bg-success bg-opacity-10 text-success fs-2"><i class="bi bi-calendar-plus-fill"></i></div>
                            <h4 class="fw-bold">Quản Lý Sự Kiện</h4>
                            <p class="text-muted small">Tạo sự kiện mới, điểm danh và quản lý các hoạt động.</p>
                        </div>
                    </a>
                </div>

            <?php else: ?>
                <div class="col-lg-4 col-md-6">
                    <a href="member/danhsach_thanhvien.php?id_clb=<?= $id_clb ?>" class="text-decoration-none text-dark">
                        <div class="card card-custom shadow-sm p-4 text-center">
                            <div class="icon-circle bg-primary bg-opacity-10 text-primary fs-2"><i class="bi bi-people-fill"></i></div>
                            <h4 class="fw-bold">Thành Viên CLB</h4>
                            <p class="text-muted small">Giao lưu và xem danh sách các thành viên cùng tham gia.</p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-4 col-md-6">
                    <a href="event/danhsach_sukien.php?id_clb=<?= $id_clb ?>" class="text-decoration-none text-dark">
                        <div class="card card-custom shadow-sm p-4 text-center">
                            <div class="icon-circle bg-success bg-opacity-10 text-success fs-2"><i class="bi bi-calendar-event"></i></div>
                            <h4 class="fw-bold">Sự Kiện CLB</h4>
                            <p class="text-muted small">Tham gia các hoạt động sôi nổi do CLB tổ chức.</p>
                        </div>
                    </a>
                </div>

                <?php if (!$is_other_club_leader): ?>
                    <div class="col-lg-4 col-md-6">
                        <?php if ($is_member): ?>
                            <div class="card card-custom bg-white border-success border-top border-4 shadow-sm p-4 text-center">
                                <div class="icon-circle bg-success bg-opacity-10 text-success fs-2"><i class="bi bi-check-circle-fill"></i></div>
                                <h4 class="fw-bold text-success">Đã Gia Nhập</h4>
                                <p class="text-muted small">Bạn đang là thành viên chính thức của câu lạc bộ này.</p>
                            </div>
                        <?php elseif ($is_pending): ?>
                            <div class="card card-custom bg-white border-warning border-top border-4 shadow-sm p-4 text-center">
                                <div class="icon-circle bg-warning bg-opacity-10 text-warning fs-2"><i class="bi bi-clock-history"></i></div>
                                <h4 class="fw-bold text-warning">Đang Chờ Duyệt</h4>
                                <p class="text-muted small">Vui lòng chờ ban chủ nhiệm phê duyệt yêu cầu của bạn.</p>
                            </div>
                        <?php else: ?>
                            <a href="clb/xuly_thamgia_clb.php?id_clb=<?= $id_clb ?>" class="text-decoration-none text-dark">
                                <div class="card card-custom shadow-sm p-4 text-center">
                                    <div class="icon-circle bg-info bg-opacity-10 text-info fs-2"><i class="bi bi-pencil-square"></i></div>
                                    <h4 class="fw-bold">Đăng Ký Tham Gia</h4>
                                    <p class="text-muted small">Đăng ký ngay để trở thành một phần của cộng đồng này.</p>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
            <?php endif; ?>

        </div>
    </div>
</section>

<?php include 'footer.php'; ?>