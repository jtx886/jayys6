&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, [], '请求方法错误');
}

$announcement_id = intval($_POST['announcement_id'] ?? 0);
if ($announcement_id &lt;= 0) {
    jsonResponse(false, [], '参数错误');
}

if (isLoggedIn()) {
    // 登录用户记录到数据库
    $exists = $db-&gt;fetch("SELECT id FROM announcement_reads WHERE user_id = ? AND announcement_id = ?", [$_SESSION['user_id'], $announcement_id]);
    if (!$exists) {
        $db-&gt;query("INSERT INTO announcement_reads (user_id, announcement_id) VALUES (?, ?)", [$_SESSION['user_id'], $announcement_id]);
    }
} else {
    // 未登录用户设置cookie（30天）
    setcookie('ann_read_' . $announcement_id, '1', time() + 86400 * 30, '/');
}

jsonResponse(true, [], '标记成功');
?&gt;
