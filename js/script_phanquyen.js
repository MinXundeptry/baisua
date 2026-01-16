function toggleClbSelect(selectObj, userId) {
    const divClb = document.getElementById('div_clb_' + userId);
    if (divClb) {
        // Hiện nếu chọn 'chunhiem', ẩn nếu chọn vai trò khác
        divClb.style.display = (selectObj.value === 'chunhiem') ? 'block' : 'none';
    }
}