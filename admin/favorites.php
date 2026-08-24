&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$adminPage = 'favorites';

$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where = '';
$params = [];
if ($userId &gt; 0) {
    $where = "WHERE f.user_id = ?";
    $params[] = $userId;
}

if ($search) {
    $where = $where ? "$where AND (u.username LIKE ? OR u.email LIKE ?)" : "WHERE (u.username LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$favorites = $db-&gt;fetchAll("SELECT f.*, u.username, u.email FROM favorites f JOIN users u ON f.user_id = u.id $where ORDER BY f.created_at DESC LIMIT 200", $params);
$users = $db-&gt;fetchAll("SELECT id, username FROM users ORDER BY username");

$page_title = '用户收藏';
include __DIR__ . '/header.php';
?&gt;

&lt;div class="admin-header"&gt;
    &lt;h1&gt;用户收藏&lt;/h1&gt;
&lt;/div&gt;

&lt;div class="admin-card" style="margin-bottom: 24px;"&gt;
    &lt;form method="get" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: end;"&gt;
        &lt;div class="form-group" style="margin: 0; min-width: 200px;"&gt;
            &lt;label class="form-label"&gt;选择用户&lt;/label&gt;
            &lt;select name="user_id" class="form-select" onchange="this.form.submit()"&gt;
                &lt;option value="0"&gt;全部用户&lt;/option&gt;
                &lt;?php foreach ($users as $u): ?&gt;
                &lt;option value="&lt;?php echo $u['id']; ?&gt;" &lt;?php echo $userId == $u['id'] ? 'selected' : ''; ?&gt;&gt;&lt;?php echo htmlspecialchars($u['username']); ?&gt;&lt;/option&gt;
                &lt;?php endforeach; ?&gt;
            &lt;/select&gt;
        &lt;/div&gt;
        &lt;div class="form-group" style="margin: 0; flex: 1; min-width: 200px;"&gt;
            &lt;label class="form-label"&gt;搜索用户&lt;/label&gt;
            &lt;input type="text" name="search" class="form-input" value="&lt;?php echo htmlspecialchars($search); ?&gt;" placeholder="用户名或邮箱"&gt;
        &lt;/div&gt;
        &lt;button type="submit" class="btn btn-primary" style="padding: 12px 24px; height: 48px;"&gt;搜索&lt;/button&gt;
    &lt;/form&gt;
&lt;/div&gt;

&lt;?php if (!empty($favorites)): ?&gt;
&lt;div class="admin-grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));"&gt;
    &lt;?php foreach ($favorites as $fav): ?&gt;
    &lt;div class="movie-card"&gt;
        &lt;div class="movie-poster"&gt;
            &lt;img src="&lt;?php echo TMDB_IMAGE_BASE . 'w300' . $fav['poster_path']; ?&gt;" alt="&lt;?php echo htmlspecialchars($fav['title']); ?&gt;" loading="lazy"&gt;
            &lt;div style="position: absolute; top: 8px; right: 8px; background: var(--theme-color); color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;"&gt;
                &lt;?php echo htmlspecialchars($fav['username']); ?&gt;
            &lt;/div&gt;
        &lt;/div&gt;
        &lt;div class="movie-info"&gt;
            &lt;div class="movie-title" title="&lt;?php echo htmlspecialchars($fav['title']); ?&gt;"&gt;&lt;?php echo htmlspecialchars($fav['title']); ?&gt;&lt;/div&gt;
            &lt;div class="movie-meta"&gt;
                &lt;span class="movie-year"&gt;收藏于 &lt;?php echo substr($fav['created_at'], 0, 10); ?&gt;&lt;/span&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/div&gt;
    &lt;?php endforeach; ?&gt;
&lt;/div&gt;
&lt;?php else: ?&gt;
&lt;div class="empty-state"&gt;
    &lt;h3&gt;暂无收藏&lt;/h3&gt;
&lt;/div&gt;
&lt;?php endif; ?&gt;

&lt;?php include __DIR__ . '/footer.php'; ?&gt;
