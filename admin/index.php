&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$adminPage = 'dashboard';

// 统计数据
$totalUsers = $db-&gt;fetch("SELECT COUNT(*) as cnt FROM users WHERE is_admin = 0")['cnt'];
$totalFeedbacks = $db-&gt;fetch("SELECT COUNT(*) as cnt FROM feedback")['cnt'];
$totalFavorites = $db-&gt;fetch("SELECT COUNT(*) as cnt FROM favorites")['cnt'];
$totalHistory = $db-&gt;fetch("SELECT COUNT(*) as cnt FROM watch_history")['cnt'];

// 最新注册用户
$newUsers = $db-&gt;fetchAll("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");

// 最新反馈
$newFeedbacks = $db-&gt;fetchAll("SELECT f.*, u.username FROM feedback f JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC LIMIT 5");

// 最新观看历史（指定用户可查看）
$selectedUser = isset($_GET['history_user']) ? intval($_GET['history_user']) : 0;
$historyQuery = "SELECT h.*, u.username FROM watch_history h JOIN users u ON h.user_id = u.id";
$historyParams = [];
if ($selectedUser &gt; 0) {
    $historyQuery .= " WHERE h.user_id = ?";
    $historyParams[] = $selectedUser;
}
$historyQuery .= " ORDER BY h.watched_at DESC LIMIT 10";
$watchHistory = $db-&gt;fetchAll($historyQuery, $historyParams);

// 最新收藏
$favSelectedUser = isset($_GET['fav_user']) ? intval($_GET['fav_user']) : 0;
$favQuery = "SELECT f.*, u.username FROM favorites f JOIN users u ON f.user_id = u.id";
$favParams = [];
if ($favSelectedUser &gt; 0) {
    $favQuery .= " WHERE f.user_id = ?";
    $favParams[] = $favSelectedUser;
}
$favQuery .= " ORDER BY f.created_at DESC LIMIT 10";
$favoritesList = $db-&gt;fetchAll($favQuery, $favParams);

// 所有用户（用于选择查看）
$allUsers = $db-&gt;fetchAll("SELECT id, username FROM users ORDER BY username");

$page_title = '管理后台 - 仪表盘';
include __DIR__ . '/header.php';
?&gt;

&lt;div class="admin-header"&gt;
    &lt;h1&gt;仪表盘&lt;/h1&gt;
    &lt;a href="/" class="btn btn-secondary" style="padding: 10px 20px; font-size: 14px;"&gt;返回网站&lt;/a&gt;
&lt;/div&gt;

&lt;div class="dashboard-grid"&gt;
    &lt;div class="dash-card"&gt;
        &lt;div class="dash-icon"&gt;
            &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/&gt;&lt;/svg&gt;
        &lt;/div&gt;
        &lt;div class="dash-number"&gt;&lt;?php echo $totalUsers; ?&gt;&lt;/div&gt;
        &lt;div class="dash-label"&gt;注册用户&lt;/div&gt;
    &lt;/div&gt;
    
    &lt;div class="dash-card"&gt;
        &lt;div class="dash-icon"&gt;
            &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/&gt;&lt;/svg&gt;
        &lt;/div&gt;
        &lt;div class="dash-number"&gt;&lt;?php echo $totalFeedbacks; ?&gt;&lt;/div&gt;
        &lt;div class="dash-label"&gt;用户反馈&lt;/div&gt;
    &lt;/div&gt;
    
    &lt;div class="dash-card"&gt;
        &lt;div class="dash-icon"&gt;
            &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/&gt;&lt;/svg&gt;
        &lt;/div&gt;
        &lt;div class="dash-number"&gt;&lt;?php echo $totalFavorites; ?&gt;&lt;/div&gt;
        &lt;div class="dash-label"&gt;总收藏数&lt;/div&gt;
    &lt;/div&gt;
    
    &lt;div class="dash-card"&gt;
        &lt;div class="dash-icon"&gt;
            &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.2 3.2.8-1.3-4.5-2.7V7z"/&gt;&lt;/svg&gt;
        &lt;/div&gt;
        &lt;div class="dash-number"&gt;&lt;?php echo $totalHistory; ?&gt;&lt;/div&gt;
        &lt;div class="dash-label"&gt;观看记录&lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;div class="dashboard-panels"&gt;
    &lt;div class="dash-panel"&gt;
        &lt;h3&gt;
            新注册用户
            &lt;a href="/admin/users.php"&gt;查看全部&lt;/a&gt;
        &lt;/h3&gt;
        &lt;div class="mini-list"&gt;
            &lt;?php foreach ($newUsers as $u): ?&gt;
            &lt;div class="mini-item"&gt;
                &lt;img class="mini-avatar" src="&lt;?php echo getAvatar($u); ?&gt;" alt="&lt;?php echo htmlspecialchars($u['username']); ?&gt;"&gt;
                &lt;div class="mini-info"&gt;
                    &lt;div class="mini-name"&gt;&lt;?php echo htmlspecialchars($u['username']); ?&gt;&lt;?php if ($u['is_admin']): ?&gt;&lt;span class="reply-badge" style="margin-left: 6px;"&gt;开发者&lt;/span&gt;&lt;?php endif; ?&gt;&lt;/div&gt;
                    &lt;div class="mini-meta"&gt;&lt;?php echo $u['email']; ?&gt;&lt;/div&gt;
                &lt;/div&gt;
                &lt;div class="mini-meta"&gt;&lt;?php echo formatTime($u['created_at']); ?&gt;&lt;/div&gt;
            &lt;/div&gt;
            &lt;?php endforeach; ?&gt;
        &lt;/div&gt;
    &lt;/div&gt;
    
    &lt;div class="dash-panel"&gt;
        &lt;h3&gt;
            最新反馈
            &lt;a href="/admin/feedbacks.php"&gt;查看全部&lt;/a&gt;
        &lt;/h3&gt;
        &lt;div class="mini-list"&gt;
            &lt;?php foreach ($newFeedbacks as $f): ?&gt;
            &lt;div class="mini-item"&gt;
                &lt;div class="mini-info"&gt;
                    &lt;div class="mini-name"&gt;&lt;?php echo htmlspecialchars($f['title']); ?&gt;&lt;/div&gt;
                    &lt;div class="mini-meta"&gt;来自 &lt;?php echo htmlspecialchars($f['username']); ?&gt;&lt;/div&gt;
                &lt;/div&gt;
                &lt;div class="mini-meta"&gt;&lt;?php echo formatTime($f['created_at']); ?&gt;&lt;/div&gt;
            &lt;/div&gt;
            &lt;?php endforeach; ?&gt;
        &lt;/div&gt;
    &lt;/div&gt;
    
    &lt;div class="dash-panel"&gt;
        &lt;h3&gt;
            观看历史
            &lt;div style="display: flex; gap: 8px; align-items: center;"&gt;
                &lt;select onchange="filterHistory(this.value)" style="background: var(--bg-tertiary); color: var(--text-primary); border: none; padding: 4px 8px; border-radius: 4px; font-size: 12px;"&gt;
                    &lt;option value="0"&gt;全部用户&lt;/option&gt;
                    &lt;?php foreach ($allUsers as $u): ?&gt;
                    &lt;option value="&lt;?php echo $u['id']; ?&gt;" &lt;?php echo $selectedUser == $u['id'] ? 'selected' : ''; ?&gt;&gt;&lt;?php echo htmlspecialchars($u['username']); ?&gt;&lt;/option&gt;
                    &lt;?php endforeach; ?&gt;
                &lt;/select&gt;
                &lt;a href="/admin/history.php"&gt;查看全部&lt;/a&gt;
            &lt;/div&gt;
        &lt;/h3&gt;
        &lt;div class="mini-list"&gt;
            &lt;?php foreach ($watchHistory as $h): ?&gt;
            &lt;div class="mini-item"&gt;
                &lt;div class="mini-info"&gt;
                    &lt;div class="mini-name"&gt;&lt;?php echo htmlspecialchars($h['title']); ?&gt;&lt;/div&gt;
                    &lt;div class="mini-meta"&gt;&lt;?php echo htmlspecialchars($h['username']); ?&gt; 观看&lt;/div&gt;
                &lt;/div&gt;
                &lt;div class="mini-meta"&gt;&lt;?php echo formatTime($h['watched_at']); ?&gt;&lt;/div&gt;
            &lt;/div&gt;
            &lt;?php endforeach; ?&gt;
        &lt;/div&gt;
    &lt;/div&gt;
    
    &lt;div class="dash-panel"&gt;
        &lt;h3&gt;
            用户收藏
            &lt;div style="display: flex; gap: 8px; align-items: center;"&gt;
                &lt;select onchange="filterFav(this.value)" style="background: var(--bg-tertiary); color: var(--text-primary); border: none; padding: 4px 8px; border-radius: 4px; font-size: 12px;"&gt;
                    &lt;option value="0"&gt;全部用户&lt;/option&gt;
                    &lt;?php foreach ($allUsers as $u): ?&gt;
                    &lt;option value="&lt;?php echo $u['id']; ?&gt;" &lt;?php echo $favSelectedUser == $u['id'] ? 'selected' : ''; ?&gt;&gt;&lt;?php echo htmlspecialchars($u['username']); ?&gt;&lt;/option&gt;
                    &lt;?php endforeach; ?&gt;
                &lt;/select&gt;
                &lt;a href="/admin/favorites.php"&gt;查看全部&lt;/a&gt;
            &lt;/div&gt;
        &lt;/h3&gt;
        &lt;div class="mini-list"&gt;
            &lt;?php foreach ($favoritesList as $f): ?&gt;
            &lt;div class="mini-item"&gt;
                &lt;div class="mini-info"&gt;
                    &lt;div class="mini-name"&gt;&lt;?php echo htmlspecialchars($f['title']); ?&gt;&lt;/div&gt;
                    &lt;div class="mini-meta"&gt;&lt;?php echo htmlspecialchars($f['username']); ?&gt; 收藏&lt;/div&gt;
                &lt;/div&gt;
                &lt;div class="mini-meta"&gt;&lt;?php echo formatTime($f['created_at']); ?&gt;&lt;/div&gt;
            &lt;/div&gt;
            &lt;?php endforeach; ?&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
function filterHistory(userId) {
    const url = new URL(window.location);
    if (userId &gt; 0) {
        url.searchParams.set('history_user', userId);
    } else {
        url.searchParams.delete('history_user');
    }
    window.location = url;
}

function filterFav(userId) {
    const url = new URL(window.location);
    if (userId &gt; 0) {
        url.searchParams.set('fav_user', userId);
    } else {
        url.searchParams.delete('fav_user');
    }
    window.location = url;
}
&lt;/script&gt;

&lt;?php include __DIR__ . '/footer.php'; ?&gt;
