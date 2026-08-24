&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    jsonResponse(false, [], '请先登录');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, [], '请求方法错误');
}

$action = $_POST['do'] ?? '';

if ($action == 'update_progress') {
    // 更新观看进度（秒数）
    $media_id = intval($_POST['media_id'] ?? 0);
    $media_type = $_POST['media_type'] ?? '';
    $progress = intval($_POST['progress'] ?? 0);
    $season = intval($_POST['season'] ?? 0) ?: null;
    $episode = intval($_POST['episode'] ?? 0) ?: null;
    
    if ($media_id &lt;= 0 || !in_array($media_type, ['movie', 'tv'])) {
        jsonResponse(false, [], '参数错误');
    }
    
    // 更新或插入进度
    if ($season &amp;&amp; $episode) {
        $db-&gt;query("INSERT INTO watch_history (user_id, media_id, media_type, season_number, episode_number, progress_seconds, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                    ON DUPLICATE KEY UPDATE progress_seconds = ?, updated_at = NOW()",
            [$_SESSION['user_id'], $media_id, $media_type, $season, $episode, $progress, $progress]);
    } else {
        $db-&gt;query("INSERT INTO watch_history (user_id, media_id, media_type, progress_seconds, updated_at) 
                    VALUES (?, ?, ?, ?, NOW())
                    ON DUPLICATE KEY UPDATE progress_seconds = ?, updated_at = NOW()",
            [$_SESSION['user_id'], $media_id, $media_type, $progress, $progress]);
    }
    jsonResponse(true, [], '进度已更新');
} elseif ($action == 'add') {
    $media_id = intval($_POST['media_id'] ?? 0);
    $media_type = $_POST['media_type'] ?? '';
    $title = $_POST['title'] ?? '';
    $poster_path = $_POST['poster_path'] ?? '';
    $vote = floatval($_POST['vote'] ?? 0);
    $release_date = $_POST['release_date'] ?? '';
    $season = intval($_POST['season'] ?? 0) ?: null;
    $episode = intval($_POST['episode'] ?? 0) ?: null;
    
    if ($media_id &lt;= 0 || !in_array($media_type, ['movie', 'tv'])) {
        jsonResponse(false, [], '参数错误');
    }
    
    if ($season &amp;&amp; $episode) {
        $db-&gt;query("INSERT INTO watch_history (user_id, media_id, media_type, title, poster_path, vote_average, release_date, season_number, episode_number, season, episode, watched_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE updated_at = NOW()",
            [$_SESSION['user_id'], $media_id, $media_type, $title, $poster_path, $vote, $release_date, $season, $episode, $season, $episode]);
    } else {
        $db-&gt;query("INSERT INTO watch_history (user_id, media_id, media_type, title, poster_path, vote_average, release_date, watched_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE updated_at = NOW()",
            [$_SESSION['user_id'], $media_id, $media_type, $title, $poster_path, $vote, $release_date]);
    }
    jsonResponse(true, [], '记录成功');
} elseif ($action == 'delete') {
    $id = intval($_POST['id'] ?? 0);
    if ($id &gt; 0) {
        $db-&gt;query("DELETE FROM watch_history WHERE id = ? AND user_id = ?", [$id, $_SESSION['user_id']]);
    }
    jsonResponse(true, [], '删除成功');
} elseif ($action == 'clear') {
    $db-&gt;query("DELETE FROM watch_history WHERE user_id = ?", [$_SESSION['user_id']]);
    jsonResponse(true, [], '已清空历史');
}

jsonResponse(false, [], '未知操作');
?&gt;
