&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

// 获取反馈列表
$feedbacks = $db-&gt;fetchAll("SELECT f.*, u.username, u.avatar, u.is_admin as user_is_admin FROM feedback f JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC");

// 获取每个反馈的回复
foreach ($feedbacks as &amp;$f) {
    // 获取点赞状态
    $f['liked'] = false;
    if (isLoggedIn()) {
        $like = $db-&gt;fetch("SELECT id FROM feedback_likes WHERE feedback_id = ? AND user_id = ?", [$f['id'], $_SESSION['user_id']]);
        $f['liked'] = $like ? true : false;
    }
    
    // 获取回复，管理员回复在前
    $replies = $db-&gt;fetchAll("SELECT r.*, u.username, u.avatar, u.is_admin FROM feedback_replies r JOIN users u ON r.user_id = u.id WHERE r.feedback_id = ? ORDER BY r.is_admin DESC, r.created_at ASC", [$f['id']]);
    $f['replies'] = $replies;
    $f['reply_count'] = count($replies);
}

$page_title = '用户反馈';
include __DIR__ . '/includes/header.php';
?&gt;

&lt;div class="main-content"&gt;
    &lt;div class="feedback-page"&gt;
        &lt;?php if (isLoggedIn()): ?&gt;
        &lt;div class="feedback-form-card"&gt;
            &lt;h3&gt;💬 提交反馈&lt;/h3&gt;
            &lt;form id="feedbackForm" onsubmit="submitFeedback(event)"&gt;
                &lt;input type="hidden" name="csrf_token" value="&lt;?php echo generateCSRFToken(); ?&gt;"&gt;
                &lt;div class="form-group"&gt;
                    &lt;label class="form-label"&gt;标题&lt;/label&gt;
                    &lt;input type="text" name="title" class="form-input" placeholder="简短描述您的问题或建议" required&gt;
                &lt;/div&gt;
                &lt;div class="form-group"&gt;
                    &lt;label class="form-label"&gt;详细内容&lt;/label&gt;
                    &lt;textarea name="content" class="textarea" placeholder="请详细描述您遇到的问题或建议..." required&gt;&lt;/textarea&gt;
                &lt;/div&gt;
                &lt;button type="submit" class="btn btn-primary"&gt;提交反馈&lt;/button&gt;
            &lt;/form&gt;
        &lt;/div&gt;
        &lt;?php else: ?&gt;
        &lt;div class="feedback-form-card" style="text-align: center; padding: 40px;"&gt;
            &lt;h3&gt;💬 用户反馈&lt;/h3&gt;
            &lt;p style="color: var(--text-secondary); margin: 20px 0;"&gt;请先登录后再提交反馈&lt;/p&gt;
            &lt;a href="/login.php" class="btn btn-primary"&gt;去登录&lt;/a&gt;
        &lt;/div&gt;
        &lt;?php endif; ?&gt;
        
        &lt;h3 style="font-size: 22px; margin-bottom: 20px;"&gt;📋 反馈列表&lt;/h3&gt;
        
        &lt;?php if (!empty($feedbacks)): ?&gt;
        &lt;div class="feedback-list"&gt;
            &lt;?php foreach ($feedbacks as $f): ?&gt;
            &lt;div class="feedback-item"&gt;
                &lt;div class="feedback-header"&gt;
                    &lt;img class="feedback-avatar" src="&lt;?php echo getAvatar(['avatar' =&gt; $f['avatar'], 'username' =&gt; $f['username']]); ?&gt;" alt="&lt;?php echo htmlspecialchars($f['username']); ?&gt;"&gt;
                    &lt;div class="feedback-user"&gt;
                        &lt;div class="feedback-username"&gt;
                            &lt;?php echo htmlspecialchars($f['username']); ?&gt;
                            &lt;?php if ($f['user_is_admin']): ?&gt;
                            &lt;span class="reply-badge"&gt;开发者&lt;/span&gt;
                            &lt;?php endif; ?&gt;
                        &lt;/div&gt;
                        &lt;div class="feedback-time"&gt;&lt;?php echo formatTime($f['created_at']); ?&gt;&lt;/div&gt;
                    &lt;/div&gt;
                &lt;/div&gt;
                
                &lt;h4 class="feedback-title"&gt;&lt;?php echo htmlspecialchars($f['title']); ?&gt;&lt;/h4&gt;
                &lt;p class="feedback-content"&gt;&lt;?php echo nl2br(htmlspecialchars($f['content'])); ?&gt;&lt;/p&gt;
                
                &lt;div class="feedback-actions"&gt;
                    &lt;?php if (isLoggedIn()): ?&gt;
                    &lt;span class="feedback-action &lt;?php echo $f['liked'] ? 'liked' : ''; ?&gt;" onclick="toggleLike(&lt;?php echo $f['id']; ?&gt;, this)"&gt;
                        &lt;svg viewBox="0 0 24 24"&gt;
                            &lt;?php if ($f['liked']): ?&gt;
                            &lt;path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/&gt;
                            &lt;?php else: ?&gt;
                            &lt;path d="M9 21h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2zM9 9l4.34-4.34L12 10h9v2l-3 7H9V9zM1 9h4v12H1z"/&gt;
                            &lt;?php endif; ?&gt;
                        &lt;/svg&gt;
                        &lt;span class="like-count"&gt;&lt;?php echo $f['likes']; ?&gt;&lt;/span&gt;
                    &lt;/span&gt;
                    &lt;?php else: ?&gt;
                    &lt;span class="feedback-action" style="cursor: default;"&gt;
                        &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M9 21h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2zM9 9l4.34-4.34L12 10h9v2l-3 7H9V9zM1 9h4v12H1z"/&gt;&lt;/svg&gt;
                        &lt;?php echo $f['likes']; ?&gt;
                    &lt;/span&gt;
                    &lt;?php endif; ?&gt;
                    &lt;span class="feedback-action" onclick="toggleReplies(this)"&gt;
                        &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18zM18 14H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/&gt;&lt;/svg&gt;
                        &lt;?php echo $f['reply_count']; ?&gt; 回复
                    &lt;/span&gt;
                &lt;/div&gt;
                
                &lt;div class="replies-section" style="display: none;"&gt;
                    &lt;div class="replies-container" data-feedback-id="&lt;?php echo $f['id']; ?&gt;" data-loaded="false" data-show-all="false"&gt;
                        &lt;?php 
                        $replies = $f['replies'];
                        $showReplies = array_slice($replies, 0, 3);
                        foreach ($showReplies as $r): 
                        ?&gt;
                        &lt;div class="reply-item &lt;?php echo $r['is_admin'] ? 'admin-reply' : ''; ?&gt;"&gt;
                            &lt;img class="reply-avatar" src="&lt;?php echo getAvatar(['avatar' =&gt; $r['avatar'], 'username' =&gt; $r['username']]); ?&gt;" alt="&lt;?php echo htmlspecialchars($r['username']); ?&gt;"&gt;
                            &lt;div class="reply-content"&gt;
                                &lt;div class="reply-header"&gt;
                                    &lt;span class="reply-username"&gt;&lt;?php echo htmlspecialchars($r['username']); ?&gt;&lt;/span&gt;
                                    &lt;?php if ($r['is_admin']): ?&gt;
                                    &lt;span class="reply-badge"&gt;开发者&lt;/span&gt;
                                    &lt;?php endif; ?&gt;
                                    &lt;span class="reply-time"&gt;&lt;?php echo formatTime($r['created_at']); ?&gt;&lt;/span&gt;
                                &lt;/div&gt;
                                &lt;p class="reply-text"&gt;&lt;?php echo nl2br(htmlspecialchars($r['content'])); ?&gt;&lt;/p&gt;
                            &lt;/div&gt;
                        &lt;/div&gt;
                        &lt;?php endforeach; ?&gt;
                        
                        &lt;?php if (count($replies) &gt; 3): ?&gt;
                        &lt;div class="expand-replies" onclick="expandReplies(this, &lt;?php echo htmlspecialchars(json_encode($replies)); ?&gt;)"&gt;
                            展开全部 &lt;?php echo count($replies); ?&gt; 条回复
                        &lt;/div&gt;
                        &lt;?php endif; ?&gt;
                    &lt;/div&gt;
                    
                    &lt;?php if (isLoggedIn()): ?&gt;
                    &lt;form class="reply-form" onsubmit="submitReply(event, &lt;?php echo $f['id']; ?&gt;, this)"&gt;
                        &lt;input type="hidden" name="csrf_token" value="&lt;?php echo generateCSRFToken(); ?&gt;"&gt;
                        &lt;input type="text" name="content" class="reply-input" placeholder="写下你的回复..." required&gt;
                        &lt;button type="submit" class="reply-submit"&gt;回复&lt;/button&gt;
                    &lt;/form&gt;
                    &lt;?php endif; ?&gt;
                &lt;/div&gt;
            &lt;/div&gt;
            &lt;?php endforeach; ?&gt;
        &lt;/div&gt;
        &lt;?php else: ?&gt;
        &lt;div class="empty-state"&gt;
            &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/&gt;&lt;/svg&gt;
            &lt;h3&gt;暂无反馈&lt;/h3&gt;
            &lt;p&gt;成为第一个提交反馈的人吧&lt;/p&gt;
        &lt;/div&gt;
        &lt;?php endif; ?&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
function toggleReplies(btn) {
    const section = btn.closest('.feedback-item').querySelector('.replies-section');
    section.style.display = section.style.display === 'none' ? 'block' : 'none';
}

function expandReplies(btn, allReplies) {
    const container = btn.closest('.replies-container');
    container.innerHTML = '';
    allReplies.forEach(r =&gt; {
        const avatarBg = r.avatar || '';
        const item = document.createElement('div');
        item.className = 'reply-item' + (r.is_admin ? ' admin-reply' : '');
        item.innerHTML = `
            &lt;img class="reply-avatar" src="${avatarBg || getDefaultAvatar(r.username)}" alt="${r.username}"&gt;
            &lt;div class="reply-content"&gt;
                &lt;div class="reply-header"&gt;
                    &lt;span class="reply-username"&gt;${r.username}&lt;/span&gt;
                    ${r.is_admin ? '&lt;span class="reply-badge"&gt;开发者&lt;/span&gt;' : ''}
                    &lt;span class="reply-time"&gt;${formatTime(r.created_at)}&lt;/span&gt;
                &lt;/div&gt;
                &lt;p class="reply-text"&gt;${r.content.replace(/\n/g, '&lt;br&gt;')}&lt;/p&gt;
            &lt;/div&gt;
        `;
        container.appendChild(item);
    });
    btn.remove();
}

function getDefaultAvatar(username) {
    const colors = ['#e50914', '#e87c03', '#1db954', '#0077b5', '#6b46c1', '#d63384'];
    const color = colors[username.charCodeAt(0) % colors.length];
    const letter = username.charAt(0).toUpperCase();
    return 'data:image/svg+xml,' + encodeURIComponent(`&lt;svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"&gt;&lt;rect fill="${color}" width="100" height="100"/&gt;&lt;text x="50" y="65" font-size="50" text-anchor="middle" fill="white" font-family="Arial" font-weight="bold"&gt;${letter}&lt;/text&gt;&lt;/svg&gt;`);
}

function formatTime(datetime) {
    const d = new Date(datetime);
    const now = new Date();
    const diff = Math.floor((now - d) / 1000);
    if (diff &lt; 60) return '刚刚';
    if (diff &lt; 3600) return Math.floor(diff/60) + '分钟前';
    if (diff &lt; 86400) return Math.floor(diff/3600) + '小时前';
    if (diff &lt; 2592000) return Math.floor(diff/86400) + '天前';
    return d.toLocaleDateString();
}

function toggleLike(id, btn) {
    fetch('/api/feedback.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'do=like&amp;feedback_id=' + id + '&amp;csrf_token=&lt;?php echo isLoggedIn() ? generateCSRFToken() : ''; ?&gt;'
    })
    .then(r =&gt; r.json())
    .then(res =&gt; {
        if (res.success) {
            const countSpan = btn.querySelector('.like-count');
            let count = parseInt(countSpan.textContent);
            if (res.data.liked) {
                btn.classList.add('liked');
                count++;
                btn.querySelector('svg').innerHTML = '&lt;path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/&gt;';
            } else {
                btn.classList.remove('liked');
                count--;
                btn.querySelector('svg').innerHTML = '&lt;path d="M9 21h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2zM9 9l4.34-4.34L12 10h9v2l-3 7H9V9zM1 9h4v12H1z"/&gt;';
            }
            countSpan.textContent = count;
        } else {
            showToast(res.msg || '操作失败', 'error');
        }
    });
}

function submitFeedback(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    formData.append('do', 'create');
    
    fetch('/api/feedback.php', { method: 'POST', body: formData })
    .then(r =&gt; r.json())
    .then(res =&gt; {
        if (res.success) {
            showToast('反馈提交成功', 'success');
            setTimeout(() =&gt; location.reload(), 1000);
        } else {
            showToast(res.msg || '提交失败', 'error');
        }
    });
}

function submitReply(e, feedbackId, form) {
    e.preventDefault();
    const input = form.querySelector('input[name="content"]');
    if (!input.value.trim()) return;
    
    const formData = new FormData();
    formData.append('do', 'reply');
    formData.append('feedback_id', feedbackId);
    formData.append('content', input.value.trim());
    formData.append('csrf_token', '&lt;?php echo isLoggedIn() ? generateCSRFToken() : ''; ?&gt;');
    
    fetch('/api/feedback.php', { method: 'POST', body: formData })
    .then(r =&gt; r.json())
    .then(res =&gt; {
        if (res.success) {
            const container = form.parentElement.querySelector('.replies-container');
            const reply = res.data.reply;
            const item = document.createElement('div');
            item.className = 'reply-item' + (reply.is_admin ? ' admin-reply' : '');
            item.innerHTML = `
                &lt;img class="reply-avatar" src="${reply.avatar || getDefaultAvatar(reply.username)}" alt="${reply.username}"&gt;
                &lt;div class="reply-content"&gt;
                    &lt;div class="reply-header"&gt;
                        &lt;span class="reply-username"&gt;${reply.username}&lt;/span&gt;
                        ${reply.is_admin ? '&lt;span class="reply-badge"&gt;开发者&lt;/span&gt;' : ''}
                        &lt;span class="reply-time"&gt;刚刚&lt;/span&gt;
                    &lt;/div&gt;
                    &lt;p class="reply-text"&gt;${reply.content.replace(/\n/g, '&lt;br&gt;')}&lt;/p&gt;
                &lt;/div&gt;
            `;
            // 如果有展开按钮，先删除
            const expandBtn = container.querySelector('.expand-replies');
            if (expandBtn) {
                container.insertBefore(item, expandBtn);
            } else {
                container.appendChild(item);
            }
            input.value = '';
            showToast('回复成功', 'success');
        } else {
            showToast(res.msg || '回复失败', 'error');
        }
    });
}
&lt;/script&gt;

&lt;?php include __DIR__ . '/includes/footer.php'; ?&gt;
