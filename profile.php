&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$tmdb = new TMDB();
$tab = $_GET['tab'] ?? 'favorites';

// 获取收藏
$favorites = $db-&gt;fetchAll("SELECT * FROM favorites WHERE user_id = ? ORDER BY created_at DESC", [$_SESSION['user_id']]);

// 获取观看历史
$history = $db-&gt;fetchAll("SELECT * FROM watch_history WHERE user_id = ? ORDER BY watched_at DESC", [$_SESSION['user_id']]);

// 统计
$favCount = count($favorites);
$historyCount = count($history);

$page_title = '个人中心';
include __DIR__ . '/includes/header.php';
?&gt;

&lt;div class="main-content"&gt;
    &lt;div class="profile-page"&gt;
        &lt;div class="profile-header"&gt;
            &lt;div class="profile-avatar"&gt;
                &lt;img src="&lt;?php echo getAvatar($current_user); ?&gt;" alt="头像" id="avatarImg"&gt;
                &lt;label class="avatar-upload"&gt;
                    &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/&gt;&lt;/svg&gt;
                    &lt;input type="file" accept="image/*" style="display:none;" onchange="uploadAvatar(this)"&gt;
                &lt;/label&gt;
            &lt;/div&gt;
            &lt;div class="profile-info"&gt;
                &lt;h2&gt;
                    &lt;?php echo htmlspecialchars($current_user['username']); ?&gt;
                    &lt;?php if (isAdmin()): ?&gt;
                    &lt;span class="admin-badge" style="position: static; margin-left: 8px;"&gt;开发者&lt;/span&gt;
                    &lt;?php endif; ?&gt;
                &lt;/h2&gt;
                &lt;p&gt;&lt;?php echo htmlspecialchars($current_user['email']); ?&gt;&lt;/p&gt;
                &lt;p style="margin-top: 4px; font-size: 13px;"&gt;注册时间：&lt;?php echo date('Y-m-d', strtotime($current_user['created_at'])); ?&gt;&lt;/p&gt;
                
                &lt;div class="profile-stats"&gt;
                    &lt;div class="stat-card"&gt;
                        &lt;div class="stat-number"&gt;&lt;?php echo $favCount; ?&gt;&lt;/div&gt;
                        &lt;div class="stat-label"&gt;收藏&lt;/div&gt;
                    &lt;/div&gt;
                    &lt;div class="stat-card"&gt;
                        &lt;div class="stat-number"&gt;&lt;?php echo $historyCount; ?&gt;&lt;/div&gt;
                        &lt;div class="stat-label"&gt;观看历史&lt;/div&gt;
                    &lt;/div&gt;
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/div&gt;
        
        &lt;div class="profile-tabs"&gt;
            &lt;div class="profile-tab &lt;?php echo $tab == 'favorites' ? 'active' : ''; ?&gt;" onclick="switchTab('favorites')"&gt;我的收藏&lt;/div&gt;
            &lt;div class="profile-tab &lt;?php echo $tab == 'history' ? 'active' : ''; ?&gt;" onclick="switchTab('history')"&gt;观看历史&lt;/div&gt;
        &lt;/div&gt;
        
        &lt;!-- 收藏列表 --&gt;
        &lt;div id="favoritesTab"&gt;
            &lt;?php if (!empty($favorites)): ?&gt;
            &lt;div class="media-row"&gt;
                &lt;?php foreach ($favorites as $fav): ?&gt;
                &lt;div class="media-card" id="fav-&lt;?php echo $fav['id']; ?&gt;"&gt;
                    &lt;a href="/detail.php?type=&lt;?php echo $fav['media_type']; ?&gt;&amp;id=&lt;?php echo $fav['media_id']; ?&gt;"&gt;
                        &lt;div class="media-poster"&gt;
                            &lt;?php 
                            $posterUrl = $fav['poster'] ? $tmdb-&gt;getImageUrl($fav['poster'], 'w342') : '';
                            if ($posterUrl): ?&gt;
                            &lt;img src="&lt;?php echo $posterUrl; ?&gt;" alt="&lt;?php echo htmlspecialchars($fav['title']); ?&gt;" loading="lazy"&gt;
                            &lt;?php else: ?&gt;
                            &lt;div style="width:100%;height:100%;background:var(--bg-tertiary);display:flex;align-items:center;justify-content:center;color:var(--text-muted);"&gt;无海报&lt;/div&gt;
                            &lt;?php endif; ?&gt;
                            &lt;div class="media-type-badge"&gt;&lt;?php echo $fav['media_type'] == 'tv' ? '剧集' : '电影'; ?&gt;&lt;/div&gt;
                            &lt;?php if ($fav['vote_average'] &gt; 0): ?&gt;
                            &lt;div class="media-badge"&gt;
                                &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/&gt;&lt;/svg&gt;
                                &lt;?php echo number_format($fav['vote_average'], 1); ?&gt;
                            &lt;/div&gt;
                            &lt;?php endif; ?&gt;
                        &lt;/div&gt;
                        &lt;div class="media-info"&gt;
                            &lt;div class="media-title"&gt;&lt;?php echo htmlspecialchars($fav['title']); ?&gt;&lt;/div&gt;
                            &lt;div class="media-meta"&gt;
                                &lt;span class="media-year"&gt;&lt;?php echo substr($fav['release_date'] ?? '', 0, 4); ?&gt;&lt;/span&gt;
                                &lt;button onclick="event.preventDefault(); deleteFavorite(&lt;?php echo $fav['media_id']; ?&gt;, '&lt;?php echo $fav['media_type']; ?&gt;', &lt;?php echo $fav['id']; ?&gt;);" style="color: var(--danger); background: none; border: none; cursor: pointer; font-size: 12px;"&gt;删除&lt;/button&gt;
                            &lt;/div&gt;
                        &lt;/div&gt;
                    &lt;/a&gt;
                &lt;/div&gt;
                &lt;?php endforeach; ?&gt;
            &lt;/div&gt;
            &lt;?php else: ?&gt;
            &lt;div class="empty-state"&gt;
                &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3zm-4.4 15.55l-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z"/&gt;&lt;/svg&gt;
                &lt;h3&gt;还没有收藏&lt;/h3&gt;
                &lt;p&gt;去发现喜欢的影视并收藏吧&lt;/p&gt;
                &lt;a href="/" class="btn btn-primary" style="margin-top: 20px;"&gt;去逛逛&lt;/a&gt;
            &lt;/div&gt;
            &lt;?php endif; ?&gt;
        &lt;/div&gt;
        
        &lt;!-- 观看历史 --&gt;
        &lt;div id="historyTab" style="display: none;"&gt;
            &lt;div style="margin-bottom: 20px;"&gt;
                &lt;?php if (!empty($history)): ?&gt;
                &lt;button class="btn btn-secondary" style="font-size: 14px; padding: 8px 16px;" onclick="clearHistory()"&gt;清空历史&lt;/button&gt;
                &lt;?php endif; ?&gt;
            &lt;/div&gt;
            &lt;?php if (!empty($history)): ?&gt;
            &lt;div class="media-row"&gt;
                &lt;?php foreach ($history as $h): ?&gt;
                &lt;div class="media-card" id="history-&lt;?php echo $h['id']; ?&gt;"&gt;
                    &lt;a href="/play.php?type=&lt;?php echo $h['media_type']; ?&gt;&amp;id=&lt;?php echo $h['media_id']; ?&gt;&lt;?php echo $h['season'] ? '&amp;season=' . $h['season'] : ''; ?&gt;&lt;?php echo $h['episode'] ? '&amp;episode=' . $h['episode'] : ''; ?&gt;"&gt;
                        &lt;div class="media-poster"&gt;
                            &lt;?php 
                            $posterUrl = $h['poster'] ? $tmdb-&gt;getImageUrl($h['poster'], 'w342') : '';
                            if ($posterUrl): ?&gt;
                            &lt;img src="&lt;?php echo $posterUrl; ?&gt;" alt="&lt;?php echo htmlspecialchars($h['title']); ?&gt;" loading="lazy"&gt;
                            &lt;?php else: ?&gt;
                            &lt;div style="width:100%;height:100%;background:var(--bg-tertiary);display:flex;align-items:center;justify-content:center;color:var(--text-muted);"&gt;无海报&lt;/div&gt;
                            &lt;?php endif; ?&gt;
                            &lt;div class="media-type-badge"&gt;&lt;?php echo $h['media_type'] == 'tv' ? '剧集' : '电影'; ?&gt;&lt;/div&gt;
                            &lt;?php if ($h['season'] &amp;&amp; $h['episode']): ?&gt;
                            &lt;div class="media-badge" style="background: rgba(0,119,181,0.8); color: white; right: auto; left: 12px;"&gt;
                                S&lt;?php echo $h['season']; ?&gt;E&lt;?php echo $h['episode']; ?&gt;
                            &lt;/div&gt;
                            &lt;?php endif; ?&gt;
                        &lt;/div&gt;
                        &lt;div class="media-info"&gt;
                            &lt;div class="media-title"&gt;&lt;?php echo htmlspecialchars($h['title']); ?&gt;&lt;/div&gt;
                            &lt;div class="media-meta"&gt;
                                &lt;span class="media-year" style="font-size: 12px;"&gt;&lt;?php echo formatTime($h['watched_at']); ?&gt;&lt;/span&gt;
                                &lt;button onclick="event.preventDefault(); deleteHistory(&lt;?php echo $h['id']; ?&gt;);" style="color: var(--danger); background: none; border: none; cursor: pointer; font-size: 12px;"&gt;删除&lt;/button&gt;
                            &lt;/div&gt;
                        &lt;/div&gt;
                    &lt;/a&gt;
                &lt;/div&gt;
                &lt;?php endforeach; ?&gt;
            &lt;/div&gt;
            &lt;?php else: ?&gt;
            &lt;div class="empty-state"&gt;
                &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.2 3.2.8-1.3-4.5-2.7V7z"/&gt;&lt;/svg&gt;
                &lt;h3&gt;暂无观看记录&lt;/h3&gt;
                &lt;p&gt;开始观看精彩内容吧&lt;/p&gt;
                &lt;a href="/" class="btn btn-primary" style="margin-top: 20px;"&gt;去发现&lt;/a&gt;
            &lt;/div&gt;
            &lt;?php endif; ?&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
function switchTab(tab) {
    document.querySelectorAll('.profile-tab').forEach(t =&gt; t.classList.remove('active'));
    event.target.classList.add('active');
    document.getElementById('favoritesTab').style.display = tab === 'favorites' ? 'block' : 'none';
    document.getElementById('historyTab').style.display = tab === 'history' ? 'block' : 'none';
    history.replaceState(null, null, '?tab=' + tab);
}

function uploadAvatar(input) {
    if (!input.files || !input.files[0]) return;
    
    const formData = new FormData();
    formData.append('avatar', input.files[0]);
    formData.append('csrf_token', '&lt;?php echo generateCSRFToken(); ?&gt;');
    
    fetch('/api/avatar.php', { method: 'POST', body: formData })
    .then(r =&gt; r.json())
    .then(res =&gt; {
        if (res.success) {
            document.getElementById('avatarImg').src = res.data.avatar;
            showToast('头像更新成功', 'success');
        } else {
            showToast(res.msg || '上传失败', 'error');
        }
    });
}

function deleteFavorite(mediaId, mediaType, favId) {
    if (!confirm('确定要取消收藏吗？')) return;
    fetch('/api/favorite.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'do=remove&amp;media_id=' + mediaId + '&amp;media_type=' + mediaType + '&amp;csrf_token=&lt;?php echo generateCSRFToken(); ?&gt;'
    })
    .then(r =&gt; r.json())
    .then(res =&gt; {
        if (res.success) {
            document.getElementById('fav-' + favId).remove();
            showToast('已取消收藏', 'success');
        }
    });
}

function deleteHistory(id) {
    fetch('/api/history.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'do=delete&amp;id=' + id + '&amp;csrf_token=&lt;?php echo generateCSRFToken(); ?&gt;'
    })
    .then(r =&gt; r.json())
    .then(res =&gt; {
        if (res.success) {
            document.getElementById('history-' + id).remove();
            showToast('已删除', 'success');
        }
    });
}

function clearHistory() {
    if (!confirm('确定要清空所有观看历史吗？')) return;
    fetch('/api/history.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'do=clear&amp;csrf_token=&lt;?php echo generateCSRFToken(); ?&gt;'
    })
    .then(r =&gt; r.json())
    .then(res =&gt; {
        if (res.success) {
            location.reload();
        }
    });
}
&lt;/script&gt;

&lt;?php include __DIR__ . '/includes/footer.php'; ?&gt;
