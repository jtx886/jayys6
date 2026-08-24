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
$media_id = intval($_POST['media_id'] ?? 0);
$media_type = $_POST['media_type'] ?? '';
$title = $_POST['title'] ?? '';
$poster_path = $_POST['poster_path'] ?? '';
$vote = floatval($_POST['vote'] ?? 0);
$release_date = $_POST['release_date'] ?? '';

if ($media_id &lt;= 0 || !in_array($media_type, ['movie', 'tv'])) {
    jsonResponse(false, [], '参数错误');
}

if ($action == 'add') {
    $exists = $db-&gt;fetch("SELECT id FROM favorites WHERE user_id = ? AND media_id = ? AND media_type = ?", [$_SESSION['user_id'], $media_id, $media_type]);
    if ($exists) {
        jsonResponse(false, [], '已收藏');
    }
    $db-&gt;query("INSERT INTO favorites (user_id, media_id, media_type, title, poster_path, poster, vote_average, release_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", 
        [$_SESSION['user_id'], $media_id, $media_type, $title, $poster_path, $poster_path, $vote, $release_date]);
    jsonResponse(true, ['favorited' =&gt; true], '收藏成功');
} elseif ($action == 'remove') {
    $db-&gt;query("DELETE FROM favorites WHERE user_id = ? AND media_id = ? AND media_type = ?", [$_SESSION['user_id'], $media_id, $media_type]);
    jsonResponse(true, ['favorited' =&gt; false], '已取消收藏');
} elseif ($action == 'check') {
    $exists = $db-&gt;fetch("SELECT id FROM favorites WHERE user_id = ? AND media_id = ? AND media_type = ?", [$_SESSION['user_id'], $media_id, $media_type]);
    jsonResponse(true, ['favorited' =&gt; $exists ? true : false]);
} elseif ($action == 'delete') {
    $id = intval($_POST['id'] ?? 0);
    if ($id &gt; 0) {
        $db-&gt;query("DELETE FROM favorites WHERE id = ? AND user_id = ?", [$id, $_SESSION['user_id']]);
    }
    jsonResponse(true, [], '删除成功');
}

jsonResponse(false, [], '未知操作');
?&gt;
