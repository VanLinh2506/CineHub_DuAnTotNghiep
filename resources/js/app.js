import './bootstrap';

function showExpiredSessionNotice(message) {
    if (document.getElementById('session-expired-notice')) {
        return;
    }

    const overlay = document.createElement('div');
    overlay.id = 'session-expired-notice';
    overlay.style.cssText = 'position:fixed;inset:0;z-index:999999;display:flex;align-items:center;justify-content:center;padding:24px;background:rgba(5,5,12,.78);backdrop-filter:blur(10px)';
    overlay.innerHTML = `
        <div style="width:min(460px,100%);padding:32px;border:1px solid rgba(255,90,110,.45);border-radius:24px;background:linear-gradient(145deg,rgba(55,40,52,.98),rgba(25,17,27,.98));box-shadow:0 24px 80px rgba(0,0,0,.5);color:#fff;text-align:center;font-family:Arial,sans-serif">
            <div style="font-size:48px;margin-bottom:14px">⚠</div>
            <h2 style="margin:0 0 12px;font-size:26px">Phiên đăng nhập đã hết hạn</h2>
            <p style="margin:0 0 24px;color:#d8cfd5;line-height:1.6">${message}</p>
            <button id="session-expired-login" type="button" style="width:100%;padding:14px 20px;border:0;border-radius:14px;background:linear-gradient(90deg,#ff385c,#e50914);color:#fff;font-size:16px;font-weight:700;cursor:pointer">Đăng nhập lại</button>
        </div>`;
    document.body.appendChild(overlay);

    const goToLogin = () => {
        window.location.replace('/login');
    };

    document.getElementById('session-expired-login').addEventListener('click', goToLogin);
    window.setTimeout(goToLogin, 5000);
}

const currentUserId = document.querySelector('meta[name="auth-user-id"]')?.content;

if (currentUserId && window.Echo) {
    window.Echo.private(`user.session.${currentUserId}`)
        .listen('.session.replaced', (event) => {
            showExpiredSessionNotice(event.message || 'Tài khoản vừa đăng nhập trên thiết bị khác.');
        });
}
