document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const eventName = this.getAttribute('data-name');
            if (!confirm(`Bạn có chắc chắn muốn xóa sự kiện "${eventName}" không?`)) {
                e.preventDefault(); // Dừng việc chuyển hướng xóa
            }
        });
    });
});
 AOS.init({
    duration: 800,
    once: true
});