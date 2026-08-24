&lt;?php
if (!defined('IN_SITE')) die('Access denied');
requireAdmin();
?&gt;
&lt;!DOCTYPE html&gt;
&lt;html lang="zh-CN"&gt;
&lt;head&gt;
    &lt;meta charset="UTF-8"&gt;
    &lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;
    &lt;title&gt;&lt;?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME . '管理后台' : SITE_NAME . '管理后台'; ?&gt;&lt;/title&gt;
    &lt;link rel="stylesheet" href="/assets/css/style.php"&gt;
    &lt;style&gt;
        body { background: var(--bg-primary); }
        .admin-layout { display: flex; min-height: 100vh; }
    &lt;/style&gt;
&lt;/head&gt;
&lt;body&gt;
&lt;div class="admin-layout"&gt;
    &lt;aside class="admin-sidebar" id="adminSidebar"&gt;
        &lt;div class="admin-logo"&gt;
            &lt;h2&gt;JAY&lt;span&gt;影视&lt;/span&gt;&lt;/h2&gt;
            &lt;p style="color: var(--text-muted); font-size: 12px; margin-top: 4px;"&gt;管理后台&lt;/p&gt;
        &lt;/div&gt;
        &lt;ul class="admin-nav"&gt;
            &lt;li&gt;
                &lt;a href="/admin/" class="&lt;?php echo $adminPage == 'dashboard' ? 'active' : ''; ?&gt;"&gt;
                    &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/&gt;&lt;/svg&gt;
                    仪表盘
                &lt;/a&gt;
            &lt;/li&gt;
            &lt;li&gt;
                &lt;a href="/admin/users.php" class="&lt;?php echo $adminPage == 'users' ? 'active' : ''; ?&gt;"&gt;
                    &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/&gt;&lt;/svg&gt;
                    用户管理
                &lt;/a&gt;
            &lt;/li&gt;
            &lt;li&gt;
                &lt;a href="/admin/sources.php" class="&lt;?php echo $adminPage == 'sources' ? 'active' : ''; ?&gt;"&gt;
                    &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"/&gt;&lt;/svg&gt;
                    播放源管理
                &lt;/a&gt;
            &lt;/li&gt;
            &lt;li&gt;
                &lt;a href="/admin/announcements.php" class="&lt;?php echo $adminPage == 'announcements' ? 'active' : ''; ?&gt;"&gt;
                    &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/&gt;&lt;/svg&gt;
                    公告管理
                &lt;/a&gt;
            &lt;/li&gt;
            &lt;li&gt;
                &lt;a href="/admin/feedbacks.php" class="&lt;?php echo $adminPage == 'feedbacks' ? 'active' : ''; ?&gt;"&gt;
                    &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/&gt;&lt;/svg&gt;
                    反馈管理
                &lt;/a&gt;
            &lt;/li&gt;
            &lt;li&gt;
                &lt;a href="/admin/history.php" class="&lt;?php echo $adminPage == 'history' ? 'active' : ''; ?&gt;"&gt;
                    &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.2 3.2.8-1.3-4.5-2.7V7z"/&gt;&lt;/svg&gt;
                    观看历史
                &lt;/a&gt;
            &lt;/li&gt;
            &lt;li&gt;
                &lt;a href="/admin/favorites.php" class="&lt;?php echo $adminPage == 'favorites' ? 'active' : ''; ?&gt;"&gt;
                    &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/&gt;&lt;/svg&gt;
                    用户收藏
                &lt;/a&gt;
            &lt;/li&gt;
            &lt;li&gt;
                &lt;a href="/admin/notifications.php" class="&lt;?php echo $adminPage == 'notifications' ? 'active' : ''; ?&gt;"&gt;
                    &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/&gt;&lt;/svg&gt;
                    邮件通知
                &lt;/a&gt;
            &lt;/li&gt;
            &lt;li&gt;
                &lt;a href="/admin/settings.php" class="&lt;?php echo $adminPage == 'settings' ? 'active' : ''; ?&gt;"&gt;
                    &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94 0 .31.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/&gt;&lt;/svg&gt;
                    网站设置
                &lt;/a&gt;
            &lt;/li&gt;
            &lt;li&gt;
                &lt;a href="/"&gt;
                    &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/&gt;&lt;/svg&gt;
                    返回网站
                &lt;/a&gt;
            &lt;/li&gt;
        &lt;/ul&gt;
    &lt;/aside&gt;
    
    &lt;main class="admin-main"&gt;
        &lt;button class="mobile-menu-btn" onclick="document.getElementById('adminSidebar').classList.toggle('active')" style="display: none; position: fixed; top: 16px; left: 16px; z-index: 100; background: var(--theme-color); border-radius: 8px; padding: 10px;"&gt;
            &lt;span style="background: white;"&gt;&lt;/span&gt;
            &lt;span style="background: white;"&gt;&lt;/span&gt;
            &lt;span style="background: white;"&gt;&lt;/span&gt;
        &lt;/button&gt;
        
        &lt;div class="toast" id="adminToast"&gt;&lt;/div&gt;
