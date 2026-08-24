&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, [], '请求方法错误');
}

if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    jsonResponse(false, [], '无效的请求');
}

$email = trim($_POST['email'] ?? '');
$type = $_POST['type'] ?? 'register';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(false, [], '请输入有效的邮箱地址');
}

// 检查是否请求太频繁
$last = $db-&gt;fetch("SELECT created_at FROM email_verifications WHERE email = ? ORDER BY created_at DESC LIMIT 1", [$email]);
if ($last &amp;&amp; time() - strtotime($last['created_at']) &lt; 60) {
    jsonResponse(false, [], '请求太频繁，请稍后再试');
}

// 如果是注册，检查邮箱是否已存在
if ($type == 'register') {
    $exists = $db-&gt;fetch("SELECT id FROM users WHERE email = ?", [$email]);
    if ($exists) {
        jsonResponse(false, [], '该邮箱已被注册');
    }
}

// 生成验证码
$code = generateCode();
$expires = date('Y-m-d H:i:s', time() + 300); // 5分钟有效

// 删除之前的验证码
$db-&gt;query("DELETE FROM email_verifications WHERE email = ? AND type = ?", [$email, $type]);
$db-&gt;query("INSERT INTO email_verifications (email, code, type, expires_at) VALUES (?, ?, ?, ?)", [$email, $code, $type, $expires]);

// 发送邮件
$mailer = new Mailer();
if ($mailer-&gt;sendVerificationCode($email, $code)) {
    jsonResponse(true, [], '验证码已发送');
} else {
    jsonResponse(false, [], '邮件发送失败，请检查邮箱地址');
}
?&gt;
