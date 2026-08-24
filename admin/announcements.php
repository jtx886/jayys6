&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$adminPage = 'announcements';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = '无效的请求';
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action == 'add') {
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            
            if (empty($title) || empty($content)) {
                $error = '请填写标题和内容';
            } else {
                // 先将其他公告设为不活跃
                $db-&gt;query("UPDATE announcements SET is_active = 0");
                $db-&gt;query("INSERT INTO announcements (title, content, is_active) VALUES (?, ?, 1)", [$title, $content]);
                $success = '公告发布成功';
            }
        } elseif ($action == 'toggle') {
            $id = intval($_POST['id'] ?? 0);
            if ($id &gt; 0) {
                $ann = $db-&gt;fetch("SELECT is_active FROM announcements WHERE id = ?", [$id]);
                if ($ann) {
                    if ($ann['is_active']) {
                        $db-&gt;query("UPDATE announcements SET is_active = 0 WHERE id = ?", [$id]);
                    } else {
                        $db-&gt;query("UPDATE announcements SET is_active = 0");
                        $db-&gt;query("UPDATE announcements SET is_active = 1 WHERE id = ?", [$id]);
                    }
                    $success = '状态已更新';
                }
            }
        } elseif ($action == 'delete') {
            $id = intval($_POST['id'] ?? 0);
            if ($id &gt; 0) {
                $db-&gt;query("DELETE FROM announcements WHERE id = ?", [$id]);
                $success = '公告已删除';
            }
        }
    }
}

$announcements = $db-&gt;fetchAll("SELECT * FROM announcements ORDER BY created_at DESC");

$page_title = '公告管理';
include __DIR__ . '/header.php';
?&gt;

&lt;div class="admin-header"&gt;
    &lt;h1&gt;公告管理&lt;/h1&gt;
    &lt;button class="btn btn-primary" style="padding: 10px 20px; font-size: 14px;" onclick="document.getElementById('addModal').classList.add('active')"&gt;+ 发布公告&lt;/button&gt;
&lt;/div&gt;

&lt;?php if (isset($error)): ?&gt;
&lt;div class="alert alert-error"&gt;&lt;?php echo htmlspecialchars($error); ?&gt;&lt;/div&gt;
&lt;?php endif; ?&gt;
&lt;?php if (isset($success)): ?&gt;
&lt;div class="alert alert-success"&gt;&lt;?php echo htmlspecialchars($success); ?&gt;&lt;/div&gt;
&lt;?php endif; ?&gt;

&lt;div class="admin-card"&gt;
    &lt;h3&gt;公告列表&lt;/h3&gt;
    &lt;?php if (!empty($announcements)): ?&gt;
    &lt;div class="admin-table"&gt;
        &lt;table&gt;
            &lt;thead&gt;
                &lt;tr&gt;
                    &lt;th&gt;ID&lt;/th&gt;
                    &lt;th&gt;标题&lt;/th&gt;
                    &lt;th&gt;内容&lt;/th&gt;
                    &lt;th&gt;发布时间&lt;/th&gt;
                    &lt;th&gt;状态&lt;/th&gt;
                    &lt;th&gt;操作&lt;/th&gt;
                &lt;/tr&gt;
            &lt;/thead&gt;
            &lt;tbody&gt;
                &lt;?php foreach ($announcements as $a): ?&gt;
                &lt;tr&gt;
                    &lt;td&gt;&lt;?php echo $a['id']; ?&gt;&lt;/td&gt;
                    &lt;td&gt;&lt;?php echo htmlspecialchars($a['title']); ?&gt;&lt;/td&gt;
                    &lt;td style="max-width: 300px;"&gt;&lt;?php echo htmlspecialchars(mb_substr($a['content'], 0, 50)) . (mb_strlen($a['content']) &gt; 50 ? '...' : ''); ?&gt;&lt;/td&gt;
                    &lt;td&gt;&lt;?php echo $a['created_at']; ?&gt;&lt;/td&gt;
                    &lt;td&gt;
                        &lt;?php if ($a['is_active']): ?&gt;
                        &lt;span class="badge badge-success"&gt;显示中&lt;/span&gt;
                        &lt;?php else: ?&gt;
                        &lt;span class="badge badge-secondary" style="background: var(--bg-tertiary); color: var(--text-muted);"&gt;已隐藏&lt;/span&gt;
                        &lt;?php endif; ?&gt;
                    &lt;/td&gt;
                    &lt;td&gt;
                        &lt;form method="post" style="display: inline;"&gt;
                            &lt;input type="hidden" name="csrf_token" value="&lt;?php echo generateCSRFToken(); ?&gt;"&gt;
                            &lt;input type="hidden" name="action" value="toggle"&gt;
                            &lt;input type="hidden" name="id" value="&lt;?php echo $a['id']; ?&gt;"&gt;
                            &lt;button type="submit" class="btn-sm btn-primary"&gt;&lt;?php echo $a['is_active'] ? '隐藏' : '显示'; ?&gt;&lt;/button&gt;
                        &lt;/form&gt;
                        &lt;form method="post" style="display: inline; margin-left: 6px;"&gt;
                            &lt;input type="hidden" name="csrf_token" value="&lt;?php echo generateCSRFToken(); ?&gt;"&gt;
                            &lt;input type="hidden" name="action" value="delete"&gt;
                            &lt;input type="hidden" name="id" value="&lt;?php echo $a['id']; ?&gt;"&gt;
                            &lt;button type="submit" class="btn-sm btn-danger" onclick="return confirmAction('确定删除？')"&gt;删除&lt;/button&gt;
                        &lt;/form&gt;
                    &lt;/td&gt;
                &lt;/tr&gt;
                &lt;?php endforeach; ?&gt;
            &lt;/tbody&gt;
        &lt;/table&gt;
    &lt;/div&gt;
    &lt;?php else: ?&gt;
    &lt;div class="empty-state" style="padding: 40px;"&gt;
        &lt;p&gt;暂无公告&lt;/p&gt;
    &lt;/div&gt;
    &lt;?php endif; ?&gt;
&lt;/div&gt;

&lt;!-- 添加公告弹窗 --&gt;
&lt;div class="modal" id="addModal"&gt;
    &lt;div class="modal-content"&gt;
        &lt;div class="modal-header"&gt;
            &lt;h3&gt;发布公告&lt;/h3&gt;
            &lt;div class="modal-close" onclick="document.getElementById('addModal').classList.remove('active')"&gt;
                &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/&gt;&lt;/svg&gt;
            &lt;/div&gt;
        &lt;/div&gt;
        &lt;form method="post"&gt;
            &lt;div class="modal-body"&gt;
                &lt;input type="hidden" name="csrf_token" value="&lt;?php echo generateCSRFToken(); ?&gt;"&gt;
                &lt;input type="hidden" name="action" value="add"&gt;
                &lt;p style="color: var(--warning); margin-bottom: 16px; font-size: 14px;"&gt;提示：发布新公告后将自动隐藏之前的公告，同一时间只显示一条公告&lt;/p&gt;
                &lt;div class="form-group"&gt;
                    &lt;label class="form-label"&gt;公告标题&lt;/label&gt;
                    &lt;input type="text" name="title" class="form-input" required placeholder="输入公告标题"&gt;
                &lt;/div&gt;
                &lt;div class="form-group"&gt;
                    &lt;label class="form-label"&gt;公告内容&lt;/label&gt;
                    &lt;textarea name="content" class="textarea" required placeholder="输入公告内容..." style="min-height: 150px;"&gt;&lt;/textarea&gt;
                &lt;/div&gt;
            &lt;/div&gt;
            &lt;div class="modal-footer"&gt;
                &lt;button type="button" class="btn btn-secondary" style="padding: 10px 24px; font-size: 14px;" onclick="document.getElementById('addModal').classList.remove('active')"&gt;取消&lt;/button&gt;
                &lt;button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 14px;"&gt;发布&lt;/button&gt;
            &lt;/div&gt;
        &lt;/form&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;?php include __DIR__ . '/footer.php'; ?&gt;
