&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$adminPage = 'history';

$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where = '';
$params = [];
if ($userId &gt; 0) {
    $where = "WHERE h.user_id = ?";
    $params[] = $userId;
}

if ($search) {
    $where = $where ? "$where AND (u.username LIKE ? OR u.email LIKE ?)" : "WHERE (u.username LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$history = $db-&gt;fetchAll("SELECT h.*, u.username, u.email FROM watch_history h JOIN users u ON h.user_id = u.id $where ORDER BY h.updated_at DESC LIMIT 200", $params);
$users = $db-&gt;fetchAll("SELECT id, username FROM users ORDER BY username");

$page_title = '观看历史';
include __DIR__ . '/header.php';
?&gt;

&lt;div class="admin-header"&gt;
    &lt;h1&gt;观看历史&lt;/h1&gt;
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

&lt;?php if (!empty($history)): ?&gt;
&lt;div class="admin-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));"&gt;
    &lt;?php foreach ($history as $h): ?&gt;
    &lt;div class="admin-card" style="margin: 0; display: flex; gap: 12px; align-items: center;"&gt;
        &lt;img src="&lt;?php echo TMDB_IMAGE_BASE . 'w300' . $h['poster_path']; ?&gt;" style="width: 60px; height: 90px; object-fit: cover; border-radius: var(--radius-sm);" alt=""&gt;
        &lt;div style="flex: 1; min-width: 0;"&gt;
            &lt;div style="font-weight: 600; font-size: 14px; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"&gt;
                &lt;?php echo htmlspecialchars($h['title']); ?&gt;
            &lt;/div&gt;
            &lt;div style="font-size: 12px; color: var(--theme-color); margin-bottom: 4px;"&gt;
                &lt;?php echo htmlspecialchars($h['username']); ?&gt;
                &lt;?php if ($h['season_number'] &amp;&amp; $h['episode_number']): ?&gt;
                    S&lt;?php echo $h['season_number']; ?&gt;E&lt;?php echo $h['episode_number']; ?&gt;
                &lt;?php endif; ?&gt;
            &lt;/div&gt;
            &lt;div style="font-size: 11px; color: var(--text-muted);"&gt;
                观看至 &lt;?php echo floor($h['progress_seconds']/60); ?&gt;分钟
            &lt;/div&gt;
            &lt;div style="font-size: 11px; color: var(--text-muted);"&gt;
                &lt;?php echo $h['updated_at']; ?&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/div&gt;
    &lt;?php endforeach; ?&gt;
&lt;/div&gt;
&lt;?php else: ?&gt;
&lt;div class="empty-state"&gt;
    &lt;h3&gt;暂无观看历史&lt;/h3&gt;
&lt;/div&gt;
&lt;?php endif; ?&gt;

&lt;?php include __DIR__ . '/footer.php'; ?&gt;
