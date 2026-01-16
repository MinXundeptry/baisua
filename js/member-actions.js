/**
 * File xử lý các tương tác người dùng tại trang danh sách thành viên
 */
document.addEventListener('DOMContentLoaded', function() {
    
    // Xử lý xác nhận khi bấm nút Xóa
    const deleteButtons = document.querySelectorAll('.btn-confirm-delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const memberName = this.getAttribute('data-name');
            const confirmMessage = `Bạn có chắc chắn muốn xóa thành viên "${memberName}" không?`;
            
            if (!confirm(confirmMessage)) {
                e.preventDefault(); // Ngừng chuyển hướng nếu người dùng cancel
            }
        });
    });

    // Tự động focus vào ô tìm kiếm khi trang load
    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput) {
        // Đưa con trỏ xuống cuối chữ nếu đã có nội dung tìm kiếm
        const val = searchInput.value;
        searchInput.value = '';
        searchInput.value = val;
        searchInput.focus();
    }
});