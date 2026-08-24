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

if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    jsonResponse(false, [], '无效的请求');
}

$action = $_POST['do'] ?? '';

if ($action == 'create') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    if (empty($title) || empty($content)) {
        jsonResponse(false, [], '标题和内容不能为空');
    }
    
    $db-&gt;query("INSERT INTO feedback (user_id, title, content) VALUES (?, ?, ?)", [$_SESSION['user_id'], $title, $content]);
    jsonResponse(true, [], '反馈提交成功');
} elseif ($action == 'reply') {
    $feedback_id = intval($_POST['feedback_id'] ?? 0);
    $content = trim($_POST['content'] ?? '');
    
    if ($feedback_id &lt;= 0 || empty($content)) {
        jsonResponse(false, [], '参数错误');
    }
    
    $feedback = $db-&gt;fetch("SELECT id FROM feedback WHERE id = ?", [$feedback_id]);
    if (!$feedback) {
        jsonResponse(false, [], '反馈不存在');
    }
    
    $is_admin = isAdmin() ? 1 : 0;
    $db-&gt;query("INSERT INTO feedback_replies (feedback_id, user_id, content, is_admin) VALUES (?, ?, ?, ?)", 
        [$feedback_id, $_SESSION['user_id'], $content, $is_admin]);
    
    $reply = $db-&gt;fetch("SELECT r.*, u.username, u.avatar, u.is_admin FROM feedback_replies r JOIN users u ON r.user_id = u.id WHERE r.id = ?", [$db-&gt;lastInsertId()]);
    
    jsonResponse(true, ['reply' =&gt; $reply], '回复成功');
} elseif ($action == 'like') {
    $feedback_id = intval($_POST['feedback_id'] ?? 0);
    
    if ($feedback_id &lt;= 0) {
        jsonResponse(false, [], '参数错误');
    }
    
    // 检查是否已点赞
    $existing = $db-&gt;fetch("SELECT id FROM feedback_likes WHERE feedback_id = ? AND user_id = ?", [$feedback_id, $_SESSION['user_id']]);
    if ($existing) {
        // 取消点赞
        $db-&gt;query("DELETE FROM feedback_likes WHERE feedback_id = ? AND user_id = ?", [$feedback_id, $_SESSION['user_id']]);
        $db-&gt;query("UPDATE feedback SET likes = likes - 1 WHERE id = ?", [$feedback_id]);
        jsonResponse(true, ['liked' =&gt; false], '已取消点赞');
    } else {
        $db-&gt;query("INSERT INTO feedback_likes (feedback_id, user_id) VALUES (?, ?)", [$feedback_id, $_SESSION['user_id']]);
        $db-&gt;query("UPDATE feedback SET likes = likes + 1 WHERE id = ?", [$feedback_id]);
        jsonResponse(true, ['liked' =&gt; true], '点赞成功');
    }
}

jsonResponse(false, [], '未知操作');
?&gt;
