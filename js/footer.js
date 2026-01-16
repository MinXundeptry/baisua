document.addEventListener('DOMContentLoaded', function() {
// Tự động nhận diện trang hiện tại để active menu
    const currentLocation = location.href;
    const menuItem = document.querySelectorAll('.nav-link');
    menuItem.forEach(item => {
       if(item.href === currentLocation) {
            item.classList.add('active');
        }
    });
});