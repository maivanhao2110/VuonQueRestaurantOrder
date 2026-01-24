// Check authentication on page load
(function () {
    const userJson = localStorage.getItem('admin_user');

    // All admin pages are now in /pages/
    const getLoginPath = () => {
        return 'login.html';
    };

    if (!userJson) {
        window.location.href = getLoginPath();
        return;
    }

    try {
        const user = JSON.parse(userJson);
        const allowedRoles = ['ADMIN', 'MANAGE'];

        if (!allowedRoles.includes(user.position)) {
            alert('Bạn không có quyền truy cập trang này!');
            localStorage.removeItem('admin_user');
            window.location.href = getLoginPath();
        }
    } catch (e) {
        localStorage.removeItem('admin_user');
        window.location.href = getLoginPath();
    }
})();
