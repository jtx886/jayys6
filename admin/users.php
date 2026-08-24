&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$adminPage = 'users';

// 处理封禁操作
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = '无效的请求';
    } else {
        $action = $_POST['action'] ?? '';
        $userId = intval($_POST['user_id'] ?? 0);
        
        if ($action == 'ban' &amp;&amp; $userId &gt; 0) {
            $banDays = intval($_POST['ban_days'] ?? 0);
            $banReason = trim($_POST['ban_reason'] ?? '违反社区规定');
            
            if ($banDays &lt;= 0) {
                $error = '请输入有效的封禁天数';
            } else {
                $banUntil = date('Y-m-d H:i:s', time() + $banDays * 86400);
                $db-&gt;query("UPDATE users SET is_banned = 1, ban_until = ?, ban_reason = ? WHERE id = ? AND is_admin = 0", [$banUntil, $banReason, $userId]);
                
                // 发送邮件通知
                $user = $db-&gt;fetch("SELECT * FROM users WHERE id = ?", [$userId]);
                if ($user) {
                    $mailer = new Mailer();
                    $mailer-&gt;sendBanNotice($user['email'], $user['username'], $banReason, $banUntil);
                }
                $success = '封禁成功，已通知用户';
            }
        } elseif ($action == 'unban' &amp;&amp; $userId &gt; 0) {
            $db-&gt;query("UPDATE users SET is_banned = 0, ban_until = NULL, ban_reason = '' WHERE id = ?", [$userId]);
            $success = '已解封';
        } elseif ($action == 'delete' &amp;&amp; $userId &gt; 0) {
            $db-&gt;query("DELETE FROM users WHERE id = ? AND is_admin = 0", [$userId]);
            $success = '已删除用户';
        }
    }
}

$users = $db-&gt;fetchAll("SELECT * FROM users ORDER BY created_at DESC");

$page_title = '用户管理';
include __DIR__ . '/header.php';
?&gt;

&lt;div class="admin-header"&gt;
    &lt;h1&gt;用户管理&lt;/h1&gt;
&lt;/div&gt;

&lt;?php if (isset($error)): ?&gt;
&lt;div class="alert alert-error"&gt;&lt;?php echo htmlspecialchars($error); ?&gt;&lt;/div&gt;
&lt;?php endif; ?&gt;
&lt;?php if (isset($success)): ?&gt;
&lt;div class="alert alert-success"&gt;&lt;?php echo htmlspecialchars($success); ?&gt;&lt;/div&gt;
&lt;?php endif; ?&gt;

&lt;div class="admin-table"&gt;
    &lt;table&gt;
        &lt;thead&gt;
            &lt;tr&gt;
                &lt;th&gt;用户&lt;/th&gt;
                &lt;th&gt;邮箱&lt;/th&gt;
                &lt;th&gt;注册时间&lt;/th&gt;
                &lt;th&gt;状态&lt;/th&gt;
                &lt;th&gt;封禁到期&lt;/th&gt;
                &lt;th&gt;操作&lt;/th&gt;
            &lt;/tr&gt;
        &lt;/thead&gt;
        &lt;tbody&gt;
            &lt;?php foreach ($users as $u): ?&gt;
            &lt;tr&gt;
                &lt;td&gt;
                    &lt;div style="display: flex; align-items: center; gap: 12px;"&gt;
                        &lt;img class="user-avatar" src="&lt;?php echo getAvatar($u); ?&gt;" alt=""&gt;
                        &lt;div&gt;
                            &lt;strong&gt;&lt;?php echo htmlspecialchars($u['username']); ?&gt;&lt;/strong&gt;
                            &lt;?php if ($u['is_admin']): ?&gt;
                            &lt;span class="reply-badge" style="margin-left: 8px;"&gt;开发者&lt;/span&gt;
                            &lt;?php endif; ?&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;
                &lt;/td&gt;
                &lt;td&gt;&lt;?php echo htmlspecialchars($u['email']); ?&gt;&lt;/td&gt;
                &lt;td&gt;&lt;?php echo $u['created_at']; ?&gt;&lt;/td&gt;
                &lt;td&gt;
                    &lt;?php if ($u['is_admin']): ?&gt;
                    &lt;span class="badge badge-info"&gt;管理员&lt;/span&gt;
                    &lt;?php elseif ($u['is_banned']): ?&gt;
                    &lt;span class="badge badge-danger"&gt;已封禁&lt;/span&gt;
                    &lt;?php else: ?&gt;
                    &lt;span class="badge badge-success"&gt;正常&lt;/span&gt;
                    &lt;?php endif; ?&gt;
                &lt;/td&gt;
                &lt;td&gt;&lt;?php echo $u['ban_until'] ?: '-'; ?&gt;&lt;/td&gt;
                &lt;td&gt;
                    &lt;?php if (!$u['is_admin']): ?&gt;
                        &lt;?php if ($u['is_banned']): ?&gt;
                        &lt;form method="post" style="display: inline;"&gt;
                            &lt;input type="hidden" name="csrf_token" value="&lt;?php echo generateCSRFToken(); ?&gt;"&gt;
                            &lt;input type="hidden" name="action" value="unban"&gt;
                            &lt;input type="hidden" name="user_id" value="&lt;?php echo $u['id']; ?&gt;"&gt;
                            &lt;button type="submit" class="btn-sm btn-primary" onclick="return confirmAction('确定要解封此用户吗？')"&gt;解封&lt;/button&gt;
                        &lt;/form&gt;
                        &lt;?php else: ?&gt;
                        &lt;button class="btn-sm btn-danger" onclick="showBanModal(&lt;?php echo $u['id']; ?&gt;, '&lt;?php echo htmlspecialchars(addslashes($u['username'])); ?&gt;')"&gt;封禁&lt;/button&gt;
                        &lt;?php endif; ?&gt;
                        &lt;form method="post" style="display: inline; margin-left: 6px;"&gt;
                            &lt;input type="hidden" name="csrf_token" value="&lt;?php echo generateCSRFToken(); ?&gt;"&gt;
                            &lt;input type="hidden" name="action" value="delete"&gt;
                            &lt;input type="hidden" name="user_id" value="&lt;?php echo $u['id']; ?&gt;"&gt;
                            &lt;button type="submit" class="btn-sm btn-secondary" onclick="return confirmAction('确定要删除此用户吗？此操作不可恢复！')"&gt;删除&lt;/button&gt;
                        &lt;/form&gt;
                    &lt;?php else: ?&gt;
                    -
                    &lt;?php endif; ?&gt;
                &lt;/td&gt;
            &lt;/tr&gt;
            &lt;?php endforeach; ?&gt;
        &lt;/tbody&gt;
    &lt;/table&gt;
&lt;/div&gt;

&lt;!-- 封禁弹窗 --&gt;
&lt;div class="modal" id="banModal"&gt;
    &lt;div class="modal-content"&gt;
        &lt;div class="modal-header"&gt;
            &lt;h3&gt;封禁用户&lt;/h3&gt;
            &lt;div class="modal-close" onclick="closeBanModal()"&gt;
                &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/&gt;&lt;/svg&gt;
            &lt;/div&gt;
        &lt;/div&gt;
        &lt;form method="post"&gt;
            &lt;div class="modal-body"&gt;
                &lt;input type="hidden" name="csrf_token" value="&lt;?php echo generateCSRFToken(); ?&gt;"&gt;
                &lt;input type="hidden" name="action" value="ban"&gt;
                &lt;input type="hidden" name="user_id" id="banUserId"&gt;
                &lt;p style="margin-bottom: 20px; color: var(--text-secondary);"&gt;即将封禁用户：&lt;strong id="banUserName" style="color: var(--danger);"&gt;&lt;/strong&gt;&lt;/p&gt;
                &lt;div class="form-group"&gt;
                    &lt;label class="form-label"&gt;封禁天数&lt;/label&gt;
                    &lt;input type="number" name="ban_days" class="form-input" min="1" value="7" required placeholder="输入封禁天数"&gt;
                &lt;/div&gt;
                &lt;div class="form-group"&gt;
                    &lt;label class="form-label"&gt;封禁原因&lt;/label&gt;
                    &lt;textarea name="ban_reason" class="textarea" placeholder="请输入封禁原因"&gt;违反社区规定&lt;/textarea&gt;
                &lt;/div&gt;
            &lt;/div&gt;
            &lt;div class="modal-footer"&gt;
                &lt;button type="button" class="btn btn-secondary" style="padding: 10px 24px; font-size: 14px;" onclick="closeBanModal()"&gt;取消&lt;/button&gt;
                &lt;button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 14px; background: var(--danger);"&gt;确认封禁&lt;/button&gt;
            &lt;/div&gt;
        &lt;/form&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
function showBanModal(userId, username) {
    document.getElementById('banUserId').value = userId;
    document.getElementById('banUserName').textContent = username;
    document.getElementById('banModal').classList.add('active');
}

function closeBanModal() {
    document.getElementById('banModal').classList.remove('active');
}
&lt;/script&gt;

&lt;?php include __DIR__ . '/footer.php'; ?&gt;
