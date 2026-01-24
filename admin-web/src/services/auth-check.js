// Check authentication on page load
(function () {
    const userJson = localStorage.getItem('admin_user');

    // Helper to get correct login path relative to current location
    // Assumes we are in /admin-web/src/ or /admin-web/src/pages/
    const getLoginPath = () => {
        const path = window.location.pathname;
        if (path.includes('/pages/')) {
            return 'login.html';
        } else {
            return 'pages/login.html';
        }
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
