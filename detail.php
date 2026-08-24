&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

$tmdb = new TMDB();
$type = $_GET['type'] ?? 'movie';
$id = intval($_GET['id'] ?? 0);

if (!in_array($type, ['movie', 'tv']) || $id &lt;= 0) {
    redirect('/');
}

$details = $tmdb-&gt;getDetails($type, $id);
if (!$details || isset($details['success']) &amp;&amp; !$details['success']) {
    redirect('/');
}

$title = $details['title'] ?? $details['name'];
$page_title = $title;

$isFavorited = false;
if (isLoggedIn()) {
    $fav = $db-&gt;fetch("SELECT id FROM favorites WHERE user_id = ? AND media_id = ? AND media_type = ?", [$_SESSION['user_id'], $id, $type]);
    $isFavorited = $fav ? true : false;
}

// 检查是否有普通话配音
$hasMandarin = $tmdb-&gt;hasMandarin($details);
$isChineseContent = false;
if (isset($details['original_language']) &amp;&amp; $details['original_language'] == 'zh') {
    $isChineseContent = true;
}

// 获取季信息（用于电视剧）
$seasons = [];
$currentSeason = 1;
$episodes = [];
if ($type == 'tv' &amp;&amp; isset($details['seasons'])) {
    foreach ($details['seasons'] as $s) {
        if ($s['season_number'] &gt; 0) {
            $seasons[] = $s;
        }
    }
    if (!empty($seasons)) {
        $currentSeason = isset($_GET['season']) ? intval($_GET['season']) : $details['seasons'][count($details['seasons']) - 1]['season_number'];
        $seasonData = $tmdb-&gt;getSeasonDetails($id, $currentSeason);
        if ($seasonData &amp;&amp; isset($seasonData['episodes'])) {
            $episodes = $seasonData['episodes'];
        }
    }
}

// 获取演员表
$cast = array_slice($details['credits']['cast'] ?? [], 0, 10);

// 年份
$year = $tmdb-&gt;getYear($details);

include __DIR__ . '/includes/header.php';
?&gt;

&lt;div class="detail-page"&gt;
    &lt;div class="detail-backdrop" style="background-image: url('&lt;?php echo $tmdb-&gt;getBackdropUrl($details['backdrop_path'] ?? '', 'original'); ?&gt;');"&gt;&lt;/div&gt;
    
    &lt;div class="detail-content"&gt;
        &lt;div class="detail-poster"&gt;
            &lt;img src="&lt;?php echo $tmdb-&gt;getImageUrl($details['poster_path'] ?? '', 'w500'); ?&gt;" alt="&lt;?php echo htmlspecialchars($title); ?&gt;"&gt;
        &lt;/div&gt;
        
        &lt;div class="detail-info"&gt;
            &lt;h1 class="detail-title"&gt;&lt;?php echo htmlspecialchars($title); ?&gt;&lt;/h1&gt;
            
            &lt;div class="detail-meta"&gt;
                &lt;?php if (!empty($details['vote_average'])): ?&gt;
                &lt;span class="detail-rating"&gt;
                    &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/&gt;&lt;/svg&gt;
                    &lt;?php echo number_format($details['vote_average'], 1); ?&gt;
                &lt;/span&gt;
                &lt;?php endif; ?&gt;
                &lt;?php if ($year): ?&gt;
                &lt;span class="detail-tag"&gt;&lt;?php echo $year; ?&gt;&lt;/span&gt;
                &lt;?php endif; ?&gt;
                &lt;?php 
                $runtime = '';
                if ($type == 'movie' &amp;&amp; !empty($details['runtime'])) {
                    $runtime = floor($details['runtime']/60) . '小时' . ($details['runtime']%60) . '分';
                } elseif ($type == 'tv' &amp;&amp; !empty($details['number_of_seasons'])) {
                    $runtime = $details['number_of_seasons'] . '季';
                }
                if ($runtime): ?&gt;
                &lt;span class="detail-tag"&gt;&lt;?php echo $runtime; ?&gt;&lt;/span&gt;
                &lt;?php endif; ?&gt;
                &lt;span class="detail-tag"&gt;&lt;?php echo $type == 'tv' ? '电视剧' : '电影'; ?&gt;&lt;/span&gt;
            &lt;/div&gt;
            
            &lt;?php if (!empty($details['genres'])): ?&gt;
            &lt;div class="detail-genres"&gt;
                &lt;?php foreach ($details['genres'] as $g): ?&gt;
                &lt;span class="genre-tag"&gt;&lt;?php echo htmlspecialchars($g['name']); ?&gt;&lt;/span&gt;
                &lt;?php endforeach; ?&gt;
            &lt;/div&gt;
            &lt;?php endif; ?&gt;
            
            &lt;p class="detail-overview"&gt;&lt;?php echo htmlspecialchars($details['overview'] ?? '暂无简介'); ?&gt;&lt;/p&gt;
            
            &lt;div class="detail-actions"&gt;
                &lt;?php if ($type == 'movie'): ?&gt;
                &lt;?php if (isLoggedIn()): ?&gt;
                &lt;a href="/play.php?type=&lt;?php echo $type; ?&gt;&amp;id=&lt;?php echo $id; ?&gt;" class="action-btn primary"&gt;
                    &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M8 5v14l11-7z"/&gt;&lt;/svg&gt;
                    立即播放
                &lt;/a&gt;
                &lt;?php else: ?&gt;
                &lt;button class="action-btn primary" onclick="needLogin()"&gt;
                    &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M8 5v14l11-7z"/&gt;&lt;/svg&gt;
                    立即播放
                &lt;/button&gt;
                &lt;?php endif; ?&gt;
                &lt;?php endif; ?&gt;
                
                &lt;button class="action-btn secondary &lt;?php echo $isFavorited ? 'active' : ''; ?&gt;" id="favBtn" onclick="toggleFavorite()"&gt;
                    &lt;svg viewBox="0 0 24 24"&gt;
                        &lt;?php if ($isFavorited): ?&gt;
                        &lt;path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/&gt;
                        &lt;?php else: ?&gt;
                        &lt;path d="M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3zm-4.4 15.55l-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z"/&gt;
                        &lt;?php endif; ?&gt;
                    &lt;/svg&gt;
                    &lt;span id="favText"&gt;&lt;?php echo $isFavorited ? '已收藏' : '收藏'; ?&gt;&lt;/span&gt;
                &lt;/button&gt;
            &lt;/div&gt;
            
            &lt;?php if ($type == 'tv' &amp;&amp; $hasMandarin &amp;&amp; !$isChineseContent): ?&gt;
            &lt;div style="margin-bottom: 24px;"&gt;
                &lt;h3 class="section-subtitle"&gt;配音选择&lt;/h3&gt;
                &lt;div class="dub-selector"&gt;
                    &lt;button class="dub-btn active" onclick="setDub(this, 'original')"&gt;原声&lt;/button&gt;
                    &lt;button class="dub-btn" onclick="setDub(this, 'mandarin')"&gt;普通话&lt;/button&gt;
                &lt;/div&gt;
            &lt;/div&gt;
            &lt;?php elseif ($type == 'movie' &amp;&amp; $hasMandarin &amp;&amp; !$isChineseContent): ?&gt;
            &lt;div style="margin-bottom: 24px;"&gt;
                &lt;h3 class="section-subtitle"&gt;配音选择&lt;/h3&gt;
                &lt;div class="dub-selector"&gt;
                    &lt;button class="dub-btn active" onclick="setDub(this, 'original')"&gt;原声&lt;/button&gt;
                    &lt;button class="dub-btn" onclick="setDub(this, 'mandarin')"&gt;普通话&lt;/button&gt;
                &lt;/div&gt;
            &lt;/div&gt;
            &lt;?php endif; ?&gt;
        &lt;/div&gt;
    &lt;/div&gt;
    
    &lt;?php if ($type == 'tv' &amp;&amp; !empty($seasons)): ?&gt;
    &lt;div class="episodes-section"&gt;
        &lt;h3 class="section-subtitle"&gt;选择季&lt;/h3&gt;
        &lt;div class="season-selector"&gt;
            &lt;?php foreach ($seasons as $s): ?&gt;
            &lt;?php 
            $seasonName = '第' . $s['season_number'] . '季';
            if ($s['season_number'] == 1) $seasonName = '第一季';
            ?&gt;
            &lt;a href="?type=&lt;?php echo $type; ?&gt;&amp;id=&lt;?php echo $id; ?&gt;&amp;season=&lt;?php echo $s['season_number']; ?&gt;" class="season-btn &lt;?php echo $s['season_number'] == $currentSeason ? 'active' : ''; ?&gt;"&gt;
                &lt;?php echo htmlspecialchars($seasonName); ?&gt;
            &lt;/a&gt;
            &lt;?php endforeach; ?&gt;
        &lt;/div&gt;
        
        &lt;h3 class="section-subtitle" style="margin-top: 24px;"&gt;剧集列表&lt;/h3&gt;
        &lt;?php if (!empty($episodes)): ?&gt;
        &lt;div class="episodes-grid"&gt;
            &lt;?php foreach ($episodes as $ep): ?&gt;
            &lt;div class="episode-card" onclick="playEpisode(&lt;?php echo $ep['episode_number']; ?&gt;)"&gt;
                &lt;div class="episode-thumb"&gt;
                    &lt;?php if (!empty($ep['still_path'])): ?&gt;
                    &lt;img src="&lt;?php echo $tmdb-&gt;getImageUrl($ep['still_path'], 'w300'); ?&gt;" alt="第&lt;?php echo $ep['episode_number']; ?&gt;集"&gt;
                    &lt;?php else: ?&gt;
                    &lt;img src="&lt;?php echo $tmdb-&gt;getImageUrl($details['poster_path'] ?? '', 'w300'); ?&gt;" alt="第&lt;?php echo $ep['episode_number']; ?&gt;集"&gt;
                    &lt;?php endif; ?&gt;
                    &lt;div class="episode-number"&gt;EP&lt;?php echo $ep['episode_number']; ?&gt;&lt;/div&gt;
                &lt;/div&gt;
                &lt;div class="episode-info"&gt;
                    &lt;div class="episode-title"&gt;&lt;?php echo htmlspecialchars($ep['name'] ?? '第' . $ep['episode_number'] . '集'); ?&gt;&lt;/div&gt;
                    &lt;div class="episode-overview"&gt;&lt;?php echo htmlspecialchars(mb_substr($ep['overview'] ?? '暂无简介', 0, 60) . (mb_strlen($ep['overview'] ?? '') &gt; 60 ? '...' : '')); ?&gt;&lt;/div&gt;
                &lt;/div&gt;
            &lt;/div&gt;
            &lt;?php endforeach; ?&gt;
        &lt;/div&gt;
        &lt;?php else: ?&gt;
        &lt;p style="color: var(--text-muted);"&gt;暂无剧集信息&lt;/p&gt;
        &lt;?php endif; ?&gt;
    &lt;/div&gt;
    &lt;?php endif; ?&gt;
    
    &lt;?php if (!empty($cast)): ?&gt;
    &lt;div class="episodes-section"&gt;
        &lt;h3 class="section-subtitle"&gt;主要演员&lt;/h3&gt;
        &lt;div style="display: flex; gap: 16px; overflow-x: auto; padding-bottom: 10px;"&gt;
            &lt;?php foreach ($cast as $actor): ?&gt;
            &lt;div style="flex: 0 0 120px; text-align: center;"&gt;
                &lt;div style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; margin: 0 auto 10px; background: var(--bg-tertiary);"&gt;
                    &lt;?php if (!empty($actor['profile_path'])): ?&gt;
                    &lt;img src="&lt;?php echo $tmdb-&gt;getImageUrl($actor['profile_path'], 'w185'); ?&gt;" style="width:100%;height:100%;object-fit:cover;" alt="&lt;?php echo htmlspecialchars($actor['name']); ?&gt;"&gt;
                    &lt;?php else: ?&gt;
                    &lt;div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--text-muted);font-size:24px;"&gt;
                        &lt;?php echo mb_substr($actor['name'], 0, 1); ?&gt;
                    &lt;/div&gt;
                    &lt;?php endif; ?&gt;
                &lt;/div&gt;
                &lt;div style="font-size: 14px; font-weight: 600; margin-bottom: 4px;"&gt;&lt;?php echo htmlspecialchars($actor['name']); ?&gt;&lt;/div&gt;
                &lt;div style="font-size: 12px; color: var(--text-muted);"&gt;&lt;?php echo htmlspecialchars($actor['character']); ?&gt;&lt;/div&gt;
            &lt;/div&gt;
            &lt;?php endforeach; ?&gt;
        &lt;/div&gt;
    &lt;/div&gt;
    &lt;?php endif; ?&gt;
&lt;/div&gt;

&lt;script&gt;
let currentDub = 'original';

function needLogin() {
    window.location.href = '/login.php?msg=login_required';
}

function setDub(btn, dub) {
    document.querySelectorAll('.dub-btn').forEach(b =&gt; b.classList.remove('active'));
    btn.classList.add('active');
    currentDub = dub;
}

function toggleFavorite() {
    &lt;?php if (!isLoggedIn()): ?&gt;
    needLogin();
    return;
    &lt;?php endif; ?&gt;
    
    const btn = document.getElementById('favBtn');
    const text = document.getElementById('favText');
    const isFav = btn.classList.contains('active');
    
    fetch('/api/favorite.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            do: isFav ? 'remove' : 'add',
            media_id: &lt;?php echo $id; ?&gt;,
            media_type: '&lt;?php echo $type; ?&gt;',
            title: &lt;?php echo json_encode($title); ?&gt;,
            poster: '&lt;?php echo urlencode($details['poster_path'] ?? ''); ?&gt;',
            vote: '&lt;?php echo $details['vote_average'] ?? 0; ?&gt;',
            release_date: '&lt;?php echo ($type == 'movie' ? ($details['release_date'] ?? '') : ($details['first_air_date'] ?? '')); ?&gt;',
            csrf_token: '&lt;?php echo generateCSRFToken(); ?&gt;'
        })
    })
    .then(r =&gt; r.json())
    .then(res =&gt; {
        if (res.success) {
            if (res.data.favorited) {
                btn.classList.add('active');
                text.textContent = '已收藏';
                showToast('收藏成功', 'success');
            } else {
                btn.classList.remove('active');
                text.textContent = '收藏';
                showToast('已取消收藏', 'success');
            }
        } else {
            showToast(res.msg || '操作失败', 'error');
        }
    });
}

function playEpisode(ep) {
    &lt;?php if (!isLoggedIn()): ?&gt;
    needLogin();
    return;
    &lt;?php endif; ?&gt;
    
    let url = '/play.php?type=&lt;?php echo $type; ?&gt;&amp;id=&lt;?php echo $id; ?&gt;&amp;season=&lt;?php echo $currentSeason; ?&gt;&amp;episode=' + ep;
    if (currentDub === 'mandarin') {
        url += '&amp;dub=mandarin';
    }
    window.location.href = url;
}
&lt;/script&gt;

&lt;?php include __DIR__ . '/includes/footer.php'; ?&gt;
