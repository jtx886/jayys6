&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$adminPage = 'sources';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = '无效的请求';
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action == 'add') {
            $name = trim($_POST['name'] ?? '');
            $url = trim($_POST['url'] ?? '');
            $priority = intval($_POST['priority'] ?? 0);
            
            if (empty($name) || empty($url)) {
                $error = '请填写名称和URL';
            } else {
                $db-&gt;query("INSERT INTO sources (name, url, priority) VALUES (?, ?, ?)", [$name, $url, $priority]);
                $success = '播放源添加成功';
            }
        } elseif ($action == 'edit') {
            $id = intval($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $url = trim($_POST['url'] ?? '');
            $priority = intval($_POST['priority'] ?? 0);
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            
            if ($id &gt; 0) {
                $db-&gt;query("UPDATE sources SET name = ?, url = ?, priority = ?, is_active = ? WHERE id = ?", [$name, $url, $priority, $isActive, $id]);
                $success = '播放源更新成功';
            }
        } elseif ($action == 'delete') {
            $id = intval($_POST['id'] ?? 0);
            if ($id &gt; 0) {
                $db-&gt;query("DELETE FROM sources WHERE id = ?", [$id]);
                $success = '播放源已删除';
            }
        }
    }
}

$sources = $db-&gt;fetchAll("SELECT * FROM sources ORDER BY priority DESC, id ASC");

$page_title = '播放源管理';
include __DIR__ . '/header.php';
?&gt;

&lt;div class="admin-header"&gt;
    &lt;h1&gt;播放源管理&lt;/h1&gt;
    &lt;button class="btn btn-primary" style="padding: 10px 20px; font-size: 14px;" onclick="document.getElementById('addModal').classList.add('active')"&gt;+ 添加播放源&lt;/button&gt;
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
                &lt;th&gt;ID&lt;/th&gt;
                &lt;th&gt;名称&lt;/th&gt;
                &lt;th&gt;URL&lt;/th&gt;
                &lt;th&gt;优先级&lt;/th&gt;
                &lt;th&gt;状态&lt;/th&gt;
                &lt;th&gt;操作&lt;/th&gt;
            &lt;/tr&gt;
        &lt;/thead&gt;
        &lt;tbody&gt;
            &lt;?php foreach ($sources as $s): ?&gt;
            &lt;tr&gt;
                &lt;td&gt;&lt;?php echo $s['id']; ?&gt;&lt;/td&gt;
                &lt;td&gt;&lt;?php echo htmlspecialchars($s['name']); ?&gt;&lt;/td&gt;
                &lt;td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="&lt;?php echo htmlspecialchars($s['url']); ?&gt;"&gt;&lt;?php echo htmlspecialchars($s['url']); ?&gt;&lt;/td&gt;
                &lt;td&gt;&lt;?php echo $s['priority']; ?&gt;&lt;/td&gt;
                &lt;td&gt;
                    &lt;?php if ($s['is_active']): ?&gt;
                    &lt;span class="badge badge-success"&gt;启用&lt;/span&gt;
                    &lt;?php else: ?&gt;
                    &lt;span class="badge badge-danger"&gt;禁用&lt;/span&gt;
                    &lt;?php endif; ?&gt;
                &lt;/td&gt;
                &lt;td&gt;
                    &lt;button class="btn-sm btn-primary" onclick='editSource(&lt;?php echo json_encode($s); ?&gt;)'&gt;编辑&lt;/button&gt;
                    &lt;form method="post" style="display: inline; margin-left: 6px;"&gt;
                        &lt;input type="hidden" name="csrf_token" value="&lt;?php echo generateCSRFToken(); ?&gt;"&gt;
                        &lt;input type="hidden" name="action" value="delete"&gt;
                        &lt;input type="hidden" name="id" value="&lt;?php echo $s['id']; ?&gt;"&gt;
                        &lt;button type="submit" class="btn-sm btn-danger" onclick="return confirmAction('确定删除？')"&gt;删除&lt;/button&gt;
                    &lt;/form&gt;
                &lt;/td&gt;
            &lt;/tr&gt;
            &lt;?php endforeach; ?&gt;
        &lt;/tbody&gt;
    &lt;/table&gt;
&lt;/div&gt;

&lt;!-- 添加弹窗 --&gt;
&lt;div class="modal" id="addModal"&gt;
    &lt;div class="modal-content"&gt;
        &lt;div class="modal-header"&gt;
            &lt;h3&gt;添加播放源&lt;/h3&gt;
            &lt;div class="modal-close" onclick="document.getElementById('addModal').classList.remove('active')"&gt;
                &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/&gt;&lt;/svg&gt;
            &lt;/div&gt;
        &lt;/div&gt;
        &lt;form method="post"&gt;
            &lt;div class="modal-body"&gt;
                &lt;input type="hidden" name="csrf_token" value="&lt;?php echo generateCSRFToken(); ?&gt;"&gt;
                &lt;input type="hidden" name="action" value="add"&gt;
                &lt;div class="form-group"&gt;
                    &lt;label class="form-label"&gt;名称&lt;/label&gt;
                    &lt;input type="text" name="name" class="form-input" required placeholder="如：默认源"&gt;
                &lt;/div&gt;
                &lt;div class="form-group"&gt;
                    &lt;label class="form-label"&gt;API URL&lt;/label&gt;
                    &lt;input type="url" name="url" class="form-input" required placeholder="https://api.example.com/inc/apijson.php"&gt;
                &lt;/div&gt;
                &lt;div class="form-group"&gt;
                    &lt;label class="form-label"&gt;优先级（数字越大越优先）&lt;/label&gt;
                    &lt;input type="number" name="priority" class="form-input" value="0"&gt;
                &lt;/div&gt;
            &lt;/div&gt;
            &lt;div class="modal-footer"&gt;
                &lt;button type="button" class="btn btn-secondary" style="padding: 10px 24px; font-size: 14px;" onclick="document.getElementById('addModal').classList.remove('active')"&gt;取消&lt;/button&gt;
                &lt;button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 14px;"&gt;添加&lt;/button&gt;
            &lt;/div&gt;
        &lt;/form&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;!-- 编辑弹窗 --&gt;
&lt;div class="modal" id="editModal"&gt;
    &lt;div class="modal-content"&gt;
        &lt;div class="modal-header"&gt;
            &lt;h3&gt;编辑播放源&lt;/h3&gt;
            &lt;div class="modal-close" onclick="document.getElementById('editModal').classList.remove('active')"&gt;
                &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/&gt;&lt;/svg&gt;
            &lt;/div&gt;
        &lt;/div&gt;
        &lt;form method="post"&gt;
            &lt;div class="modal-body"&gt;
                &lt;input type="hidden" name="csrf_token" value="&lt;?php echo generateCSRFToken(); ?&gt;"&gt;
                &lt;input type="hidden" name="action" value="edit"&gt;
                &lt;input type="hidden" name="id" id="editId"&gt;
                &lt;div class="form-group"&gt;
                    &lt;label class="form-label"&gt;名称&lt;/label&gt;
                    &lt;input type="text" name="name" id="editName" class="form-input" required&gt;
                &lt;/div&gt;
                &lt;div class="form-group"&gt;
                    &lt;label class="form-label"&gt;API URL&lt;/label&gt;
                    &lt;input type="url" name="url" id="editUrl" class="form-input" required&gt;
                &lt;/div&gt;
                &lt;div class="form-group"&gt;
                    &lt;label class="form-label"&gt;优先级&lt;/label&gt;
                    &lt;input type="number" name="priority" id="editPriority" class="form-input"&gt;
                &lt;/div&gt;
                &lt;div class="form-group"&gt;
                    &lt;label style="display: flex; align-items: center; gap: 10px; cursor: pointer;"&gt;
                        &lt;input type="checkbox" name="is_active" id="editActive" style="width: 18px; height: 18px; accent-color: var(--theme-color);"&gt;
                        启用此播放源
                    &lt;/label&gt;
                &lt;/div&gt;
            &lt;/div&gt;
            &lt;div class="modal-footer"&gt;
                &lt;button type="button" class="btn btn-secondary" style="padding: 10px 24px; font-size: 14px;" onclick="document.getElementById('editModal').classList.remove('active')"&gt;取消&lt;/button&gt;
                &lt;button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 14px;"&gt;保存&lt;/button&gt;
            &lt;/div&gt;
        &lt;/form&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
function editSource(s) {
    document.getElementById('editId').value = s.id;
    document.getElementById('editName').value = s.name;
    document.getElementById('editUrl').value = s.url;
    document.getElementById('editPriority').value = s.priority;
    document.getElementById('editActive').checked = s.is_active == '1';
    document.getElementById('editModal').classList.add('active');
}
&lt;/script&gt;

&lt;?php include __DIR__ . '/footer.php'; ?&gt;
