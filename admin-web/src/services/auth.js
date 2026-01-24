const AUTH_API_URL = '/VuonQueRestaurantOrder/backend/src/public/index.php/api/staff';

const authApi = {
    login: async (username, password) => {
        try {
            const response = await fetch(`${AUTH_API_URL}/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, password })
            });
            const result = await response.json();
            return result;
        } catch (error) {
            return { success: false, message: 'Lỗi kết nối server' };
        }
    },

    saveUser: (user) => {
        localStorage.setItem('admin_user', JSON.stringify(user));
    },

    getUser: () => {
        const user = localStorage.getItem('admin_user');
        return user ? JSON.parse(user) : null;
    },

    logout: () => {
        localStorage.removeItem('admin_user');
        window.location.href = '/VuonQueRestaurantOrder/admin-web/src/pages/login.html';
    },

    checkAccess: () => {
        const user = authApi.getUser();
        if (!user) return false;
        // Only ADMIN and MANAGE are allowed
        return ['ADMIN', 'MANAGE'].includes(user.position);
    }
};
