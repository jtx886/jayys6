&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$adminPage = 'feedbacks';

// 处理回复
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = '无效的请求';
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action == 'reply') {
            $feedbackId = intval($_POST['feedback_id'] ?? 0);
            $content = trim($_POST['content'] ?? '');
            
            if ($feedbackId &gt; 0 &amp;&amp; !empty($content)) {
                $db-&gt;query("INSERT INTO feedback_replies (feedback_id, user_id, content, is_admin) VALUES (?, ?, ?, 1)", 
                    [$feedbackId, $_SESSION['user_id'], $content]);
                $success = '回复成功';
            }
        } elseif ($action == 'delete') {
            $id = intval($_POST['id'] ?? 0);
            if ($id &gt; 0) {
                $db-&gt;query("DELETE FROM feedback WHERE id = ?", [$id]);
                $success = '反馈已删除';
            }
        }
    }
}

$feedbacks = $db-&gt;fetchAll("SELECT f.*, u.username, u.avatar FROM feedback f JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC");

foreach ($feedbacks as &amp;$f) {
    $replies = $db-&gt;fetchAll("SELECT r.*, u.username, u.avatar, u.is_admin FROM feedback_replies r JOIN users u ON r.user_id = u.id WHERE r.feedback_id = ? ORDER BY r.created_at ASC", [$f['id']]);
    $f['replies'] = $replies;
}

$page_title = '反馈管理';
include __DIR__ . '/header.php';
?&gt;

&lt;div class="admin-header"&gt;
    &lt;h1&gt;反馈管理&lt;/h1&gt;
&lt;/div&gt;

&lt;?php if (isset($error)): ?&gt;
&lt;div class="alert alert-error"&gt;&lt;?php echo htmlspecialchars($error); ?&gt;&lt;/div&gt;
&lt;?php endif; ?&gt;
&lt;?php if (isset($success)): ?&gt;
&lt;div class="alert alert-success"&gt;&lt;?php echo htmlspecialchars($success); ?&gt;&lt;/div&gt;
&lt;?php endif; ?&gt;

&lt;?php if (!empty($feedbacks)): ?&gt;
&lt;div class="feedback-list"&gt;
    &lt;?php foreach ($feedbacks as $f): ?&gt;
    &lt;div class="feedback-item" style="background: var(--bg-secondary); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 20px;"&gt;
        &lt;div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;"&gt;
            &lt;div style="display: flex; align-items: center; gap: 12px;"&gt;
                &lt;img class="feedback-avatar" src="&lt;?php echo getAvatar(['avatar' =&gt; $f['avatar'], 'username' =&gt; $f['username']]); ?&gt;" alt=""&gt;
                &lt;div&gt;
                    &lt;div class="feedback-username"&gt;&lt;?php echo htmlspecialchars($f['username']); ?&gt;&lt;/div&gt;
                    &lt;div class="feedback-time"&gt;&lt;?php echo $f['created_at']; ?&gt;&lt;/div&gt;
                &lt;/div&gt;
            &lt;/div&gt;
            &lt;form method="post"&gt;
                &lt;input type="hidden" name="csrf_token" value="&lt;?php echo generateCSRFToken(); ?&gt;"&gt;
                &lt;input type="hidden" name="action" value="delete"&gt;
                &lt;input type="hidden" name="id" value="&lt;?php echo $f['id']; ?&gt;"&gt;
                &lt;button type="submit" class="btn-sm btn-danger" onclick="return confirmAction('确定删除此反馈？')"&gt;删除&lt;/button&gt;
            &lt;/form&gt;
        &lt;/div&gt;
        
        &lt;h4 style="font-size: 18px; margin-bottom: 10px;"&gt;&lt;?php echo htmlspecialchars($f['title']); ?&gt;&lt;/h4&gt;
        &lt;p style="color: var(--text-secondary); line-height: 1.7; margin-bottom: 16px;"&gt;&lt;?php echo nl2br(htmlspecialchars($f['content'])); ?&gt;&lt;/p&gt;
        
        &lt;?php if (!empty($f['replies'])): ?&gt;
        &lt;div style="background: var(--bg-tertiary); border-radius: var(--radius-md); padding: 16px; margin-bottom: 16px;"&gt;
            &lt;h5 style="margin-bottom: 12px; font-size: 14px; color: var(--text-muted);"&gt;回复 ( &lt;?php echo count($f['replies']); ?&gt; )&lt;/h5&gt;
            &lt;?php foreach ($f['replies'] as $r): ?&gt;
            &lt;div style="display: flex; gap: 10px; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);"&gt;
                &lt;img class="reply-avatar" src="&lt;?php echo getAvatar(['avatar' =&gt; $r['avatar'], 'username' =&gt; $r['username']]); ?&gt;" alt=""&gt;
                &lt;div&gt;
                    &lt;div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;"&gt;
                        &lt;strong style="font-size: 14px;"&gt;&lt;?php echo htmlspecialchars($r['username']); ?&gt;&lt;/strong&gt;
                        &lt;?php if ($r['is_admin']): ?&gt;
                        &lt;span class="reply-badge"&gt;开发者&lt;/span&gt;
                        &lt;?php endif; ?&gt;
                        &lt;span style="font-size: 12px; color: var(--text-muted);"&gt;&lt;?php echo $r['created_at']; ?&gt;&lt;/span&gt;
                    &lt;/div&gt;
                    &lt;p style="font-size: 14px; color: var(--text-secondary);"&gt;&lt;?php echo nl2br(htmlspecialchars($r['content'])); ?&gt;&lt;/p&gt;
                &lt;/div&gt;
            &lt;/div&gt;
            &lt;?php endforeach; ?&gt;
        &lt;/div&gt;
        &lt;?php endif; ?&gt;
        
        &lt;form method="post"&gt;
            &lt;input type="hidden" name="csrf_token" value="&lt;?php echo generateCSRFToken(); ?&gt;"&gt;
            &lt;input type="hidden" name="action" value="reply"&gt;
            &lt;input type="hidden" name="feedback_id" value="&lt;?php echo $f['id']; ?&gt;"&gt;
            &lt;div style="display: flex; gap: 10px;"&gt;
                &lt;input type="text" name="content" class="form-input" placeholder="管理员回复..." required style="flex: 1;"&gt;
                &lt;button type="submit" class="btn btn-primary" style="padding: 0 24px;"&gt;回复&lt;/button&gt;
            &lt;/div&gt;
        &lt;/form&gt;
    &lt;/div&gt;
    &lt;?php endforeach; ?&gt;
&lt;/div&gt;
&lt;?php else: ?&gt;
&lt;div class="empty-state"&gt;
    &lt;h3&gt;暂无反馈&lt;/h3&gt;
&lt;/div&gt;
&lt;?php endif; ?&gt;

&lt;?php include __DIR__ . '/footer.php'; ?&gt;
