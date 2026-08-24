&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$adminPage = 'email';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = '无效的请求';
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action == 'send') {
            $to = trim($_POST['to'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $content = trim($_POST['content'] ?? '');
            
            if (empty($to) || empty($subject) || empty($content)) {
                $error = '请填写完整信息';
            } else {
                $mailer = new Mailer();
                
                if ($to == 'all') {
                    // 发送给所有用户
                    $users = $db-&gt;fetchAll("SELECT email, username FROM users WHERE is_banned = 0");
                    $successCount = 0;
                    foreach ($users as $u) {
                        if ($mailer-&gt;send($u['email'], $subject, $content, $u['username'])) {
                            $successCount++;
                        }
                    }
                    $db-&gt;query("INSERT INTO email_notifications (subject, content, sent_count) VALUES (?, ?, ?)", [$subject, $content, $successCount]);
                    $success = "邮件发送成功，共发送给 {$successCount} 个用户";
                } else {
                    // 发送给指定用户
                    $user = $db-&gt;fetch("SELECT * FROM users WHERE email = ? OR username = ?", [$to, $to]);
                    if ($user) {
                        if ($mailer-&gt;send($user['email'], $subject, $content, $user['username'])) {
                            $db-&gt;query("INSERT INTO email_notifications (subject, content, sent_count) VALUES (?, ?, 1)", [$subject, $content]);
                            $success = "邮件已发送给 {$user['username']} ({$user['email']})";
                        } else {
                            $error = '邮件发送失败';
                        }
                    } else {
                        $error = '用户不存在';
                    }
                }
            }
        }
    }
}

$notifications = $db-&gt;fetchAll("SELECT * FROM email_notifications ORDER BY created_at DESC LIMIT 20");

$page_title = '邮件通知';
include __DIR__ . '/header.php';
?&gt;

&lt;div class="admin-header"&gt;
    &lt;h1&gt;邮件通知&lt;/h1&gt;
&lt;/div&gt;

&lt;?php if (isset($error)): ?&gt;
&lt;div class="alert alert-error"&gt;&lt;?php echo htmlspecialchars($error); ?&gt;&lt;/div&gt;
&lt;?php endif; ?&gt;
&lt;?php if (isset($success)): ?&gt;
&lt;div class="alert alert-success"&gt;&lt;?php echo htmlspecialchars($success); ?&gt;&lt;/div&gt;
&lt;?php endif; ?&gt;

&lt;div class="admin-grid-2"&gt;
    &lt;div class="admin-card"&gt;
        &lt;h3&gt;发送邮件&lt;/h3&gt;
        &lt;form method="post"&gt;
            &lt;input type="hidden" name="csrf_token" value="&lt;?php echo generateCSRFToken(); ?&gt;"&gt;
            &lt;input type="hidden" name="action" value="send"&gt;
            
            &lt;div class="form-group"&gt;
                &lt;label class="form-label"&gt;接收对象&lt;/label&gt;
                &lt;input type="text" name="to" class="form-input" required placeholder="输入用户邮箱或用户名，输入 all 发送给所有用户"&gt;
                &lt;p style="font-size: 12px; color: var(--text-muted); margin-top: 6px;"&gt;输入 all 可发送给所有未封禁用户&lt;/p&gt;
            &lt;/div&gt;
            
            &lt;div class="form-group"&gt;
                &lt;label class="form-label"&gt;邮件标题&lt;/label&gt;
                &lt;input type="text" name="subject" class="form-input" required placeholder="输入邮件标题"&gt;
            &lt;/div&gt;
            
            &lt;div class="form-group"&gt;
                &lt;label class="form-label"&gt;邮件内容&lt;/label&gt;
                &lt;textarea name="content" class="textarea" required placeholder="输入邮件内容..." style="min-height: 200px;"&gt;&lt;/textarea&gt;
            &lt;/div&gt;
            
            &lt;button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px;"&gt;发送邮件&lt;/button&gt;
        &lt;/form&gt;
    &lt;/div&gt;
    
    &lt;div class="admin-card"&gt;
        &lt;h3&gt;发送记录&lt;/h3&gt;
        &lt;?php if (!empty($notifications)): ?&gt;
        &lt;div class="admin-table"&gt;
            &lt;table&gt;
                &lt;thead&gt;
                    &lt;tr&gt;
                        &lt;th&gt;时间&lt;/th&gt;
                        &lt;th&gt;标题&lt;/th&gt;
                        &lt;th&gt;发送数&lt;/th&gt;
                    &lt;/tr&gt;
                &lt;/thead&gt;
                &lt;tbody&gt;
                    &lt;?php foreach ($notifications as $n): ?&gt;
                    &lt;tr&gt;
                        &lt;td style="white-space: nowrap;"&gt;&lt;?php echo substr($n['created_at'], 0, 16); ?&gt;&lt;/td&gt;
                        &lt;td&gt;&lt;?php echo htmlspecialchars($n['subject']); ?&gt;&lt;/td&gt;
                        &lt;td&gt;&lt;?php echo $n['sent_count']; ?&gt;&lt;/td&gt;
                    &lt;/tr&gt;
                    &lt;?php endforeach; ?&gt;
                &lt;/tbody&gt;
            &lt;/table&gt;
        &lt;/div&gt;
        &lt;?php else: ?&gt;
        &lt;div class="empty-state" style="padding: 30px;"&gt;
            &lt;p style="color: var(--text-muted);"&gt;暂无发送记录&lt;/p&gt;
        &lt;/div&gt;
        &lt;?php endif; ?&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;?php include __DIR__ . '/footer.php'; ?&gt;
