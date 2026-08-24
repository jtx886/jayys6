&lt;?php
if (!defined('IN_SITE')) die('Access denied');
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?&gt;
&lt;!DOCTYPE html&gt;
&lt;html lang="zh-CN"&gt;
&lt;head&gt;
    &lt;meta charset="UTF-8"&gt;
    &lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;
    &lt;title&gt;&lt;?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?&gt;&lt;/title&gt;
    &lt;link rel="stylesheet" href="/assets/css/style.php"&gt;
&lt;/head&gt;
&lt;body&gt;
&lt;nav class="navbar" id="navbar"&gt;
    &lt;div class="nav-brand"&gt;
        &lt;div class="mobile-menu-btn" onclick="toggleMobileMenu()"&gt;
            &lt;span&gt;&lt;/span&gt;
            &lt;span&gt;&lt;/span&gt;
            &lt;span&gt;&lt;/span&gt;
        &lt;/div&gt;
        &lt;a href="/" class="nav-logo"&gt;JAY&lt;/a&gt;
    &lt;/div&gt;
    &lt;ul class="nav-menu" id="navMenu"&gt;
        &lt;li&gt;&lt;a href="/" class="&lt;?php echo $current_page == 'index' ? 'active' : ''; ?&gt;"&gt;首页&lt;/a&gt;&lt;/li&gt;
        &lt;li&gt;&lt;a href="/movies.php" class="&lt;?php echo $current_page == 'movies' ? 'active' : ''; ?&gt;"&gt;电影&lt;/a&gt;&lt;/li&gt;
        &lt;li&gt;&lt;a href="/tv.php" class="&lt;?php echo $current_page == 'tv' ? 'active' : ''; ?&gt;"&gt;电视剧&lt;/a&gt;&lt;/li&gt;
        &lt;li&gt;&lt;a href="/anime.php" class="&lt;?php echo $current_page == 'anime' ? 'active' : ''; ?&gt;"&gt;动漫&lt;/a&gt;&lt;/li&gt;
        &lt;li&gt;&lt;a href="/variety.php" class="&lt;?php echo $current_page == 'variety' ? 'active' : ''; ?&gt;"&gt;综艺&lt;/a&gt;&lt;/li&gt;
        &lt;li&gt;&lt;a href="/feedback.php" class="&lt;?php echo $current_page == 'feedback' ? 'active' : ''; ?&gt;"&gt;反馈&lt;/a&gt;&lt;/li&gt;
    &lt;/ul&gt;
    &lt;div class="nav-actions"&gt;
        &lt;div class="search-box" id="searchBox"&gt;
            &lt;input type="text" class="search-input" id="searchInput" placeholder="搜索影视..." onkeypress="if(event.key==='Enter')doSearch()"&gt;
            &lt;div class="search-icon" onclick="toggleSearch()"&gt;
                &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/&gt;&lt;/svg&gt;
            &lt;/div&gt;
        &lt;/div&gt;
        &lt;?php if (isLoggedIn()): ?&gt;
        &lt;div class="nav-user"&gt;
            &lt;img class="nav-avatar" src="&lt;?php echo getAvatar($current_user); ?&gt;" alt="&lt;?php echo htmlspecialchars($current_user['username']); ?&gt;"&gt;
            &lt;div class="nav-dropdown"&gt;
                &lt;a href="/profile.php"&gt;
                    &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/&gt;&lt;/svg&gt;
                    个人中心
                &lt;/a&gt;
                &lt;?php if (isAdmin()): ?&gt;
                &lt;a href="/admin/"&gt;
                    &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M19.43 12.98c.04-.32.07-.64.07-.98s-.03-.66-.07-.98l2.11-1.65c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.3-.61-.22l-2.49 1c-.52-.4-1.08-.73-1.69-.98l-.38-2.65C14.46 2.18 14.25 2 14 2h-4c-.25 0-.46.18-.49.42l-.38 2.65c-.61.25-1.17.59-1.69.98l-2.49-1c-.23-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64l2.11 1.65c-.04.32-.07.65-.07.98s.03.66.07.98l-2.11 1.65c-.19.15-.24.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1c.52.4 1.08.73 1.69.98l.38 2.65c.03.24.24.42.49.42h4c.25 0 .46-.18.49-.42l.38-2.65c.61-.25 1.17-.59 1.69-.98l2.49 1c.23.09.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.65zM12 15.5c-1.93 0-3.5-1.57-3.5-3.5s1.57-3.5 3.5-3.5 3.5 1.57 3.5 3.5-1.57 3.5-3.5 3.5z"/&gt;&lt;/svg&gt;
                    管理后台
                &lt;/a&gt;
                &lt;?php endif; ?&gt;
                &lt;form action="/logout.php" method="post" style="margin:0;"&gt;
                    &lt;input type="hidden" name="csrf_token" value="&lt;?php echo generateCSRFToken(); ?&gt;"&gt;
                    &lt;button type="submit"&gt;
                        &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/&gt;&lt;/svg&gt;
                        退出登录
                    &lt;/button&gt;
                &lt;/form&gt;
            &lt;/div&gt;
        &lt;/div&gt;
        &lt;?php else: ?&gt;
        &lt;a href="/login.php" class="btn btn-primary" style="padding: 10px 24px; font-size: 14px;"&gt;登录&lt;/a&gt;
        &lt;?php endif; ?&gt;
    &lt;/div&gt;
&lt;/nav&gt;

&lt;div class="toast" id="toast"&gt;&lt;/div&gt;

&lt;script&gt;
// 导航栏滚动效果
window.addEventListener('scroll', function() {
    const navbar = document.getElementById('navbar');
    if (window.scrollY &gt; 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// 移动端菜单
function toggleMobileMenu() {
    document.getElementById('navMenu').classList.toggle('active');
}

// 搜索框
function toggleSearch() {
    document.getElementById('searchBox').classList.toggle('active');
    if (document.getElementById('searchBox').classList.contains('active')) {
        document.getElementById('searchInput').focus();
    }
}

function doSearch() {
    const q = document.getElementById('searchInput').value.trim();
    if (q) {
        window.location.href = '/search.php?q=' + encodeURIComponent(q);
    }
}

// Toast提示
function showToast(msg, type = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.className = 'toast ' + type + ' show';
    setTimeout(() =&gt; toast.classList.remove('show'), 3000);
}

// 点击关闭移动端菜单
document.addEventListener('click', function(e) {
    const menu = document.getElementById('navMenu');
    const btn = document.querySelector('.mobile-menu-btn');
    if (!menu.contains(e.target) &amp;&amp; !btn.contains(e.target)) {
        menu.classList.remove('active');
    }
});
&lt;/script&gt;
