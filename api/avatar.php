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

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    jsonResponse(false, [], '请选择图片');
}

$file = $_FILES['avatar'];
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $allowedTypes)) {
    jsonResponse(false, [], '只支持JPG、PNG、GIF、WebP格式');
}

if ($file['size'] &gt; 2 * 1024 * 1024) {
    jsonResponse(false, [], '图片大小不能超过2MB');
}

// 读取图片并转为base64保存到数据库
$imageData = file_get_contents($file['tmp_name']);
$base64 = 'data:' . $file['type'] . ';base64,' . base64_encode($imageData);

$db-&gt;query("UPDATE users SET avatar = ? WHERE id = ?", [$base64, $_SESSION['user_id']]);
jsonResponse(true, ['avatar' =&gt; $base64], '头像更新成功');
?&gt;
