&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

$tmdb = new TMDB();
$apiKey = trim($settings['tmdb_api_key'] ?? '');

// 如果没有设置API key，显示提示页面
$hasApiKey = !empty($apiKey);

$trending = [];
$popularMovies = [];
$popularTv = [];
$topRated = [];
$heroMedia = null;

if ($hasApiKey) {
    $trending = $tmdb-&gt;getTrending('all', 'week');
    $popularMovies = $tmdb-&gt;getPopular('movie', 1);
    $popularTv = $tmdb-&gt;getPopular('tv', 1);
    $topRated = $tmdb-&gt;getTopRated('movie', 1);
    
    // 获取Hero展示内容
    if (!empty($trending['results'])) {
        $heroMedia = $trending['results'][array_rand(array_slice($trending['results'], 0, 8))];
    }
}

// 获取活跃公告
$announcement = null;
$showAnnouncement = false;

if (!$hasApiKey) {
    // 没有API key时不显示公告
} else {
    $announcement = $db-&gt;fetch("SELECT * FROM announcements WHERE is_active = 1 ORDER BY created_at DESC LIMIT 1");
    if ($announcement) {
        // 检查用户是否已读此公告
        $annRead = false;
        if (isset($_SESSION['user_id'])) {
            $read = $db-&gt;fetch("SELECT id FROM announcement_reads WHERE user_id = ? AND announcement_id = ?", 
                [$_SESSION['user_id'], $announcement['id']]);
            $annRead = $read ? true : false;
        } else {
            // 未登录用户用cookie记录
            $annRead = isset($_COOKIE['ann_read_' . $announcement['id']]);
        }
        $showAnnouncement = !$annRead;
    }
}

$page_title = '首页';
include __DIR__ . '/includes/header.php';
?&gt;

&lt;?php if (!$hasApiKey): ?&gt;
&lt;div class="main-content" style="display: flex; align-items: center; justify-content: center; min-height: calc(100vh - 200px);"&gt;
    &lt;div style="text-align: center; max-width: 500px; padding: 40px;"&gt;
        &lt;svg width="80" height="80" viewBox="0 0 24 24" fill="var(--theme-color)" style="margin-bottom: 24px;"&gt;
            &lt;path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4zm-4 7l2.14 2.14-6 6L7 18l-.14-3 6.14-4z"/&gt;
        &lt;/svg&gt;
        &lt;h2 style="font-size: 28px; margin-bottom: 16px;"&gt;欢迎使用 Jay 影视&lt;/h2&gt;
        &lt;p style="color: var(--text-secondary); margin-bottom: 24px; line-height: 1.8;"&gt;请先在管理后台设置 TMDB API Key 以获取影视数据。&lt;br&gt;管理员账号：&lt;strong&gt;杰同学&lt;/strong&gt;，默认密码：&lt;strong&gt;101113&lt;/strong&gt;&lt;/p&gt;
        &lt;a href="/login.php" class="btn btn-primary"&gt;前往登录&lt;/a&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;?php else: ?&gt;

&lt;?php if ($heroMedia): ?&gt;
&lt;section class="hero-section"&gt;
    &lt;div class="hero-backdrop" style="background-image: url('&lt;?php echo $tmdb-&gt;getBackdropUrl($heroMedia['backdrop_path'], 'original'); ?&gt;');"&gt;&lt;/div&gt;
    &lt;div class="hero-content"&gt;
        &lt;h1 class="hero-title"&gt;&lt;?php echo htmlspecialchars($heroMedia['title'] ?? $heroMedia['name']); ?&gt;&lt;/h1&gt;
        &lt;div class="hero-meta"&gt;
            &lt;?php if (!empty($heroMedia['vote_average'])): ?&gt;
            &lt;span class="hero-rating"&gt;
                &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/&gt;&lt;/svg&gt;
                &lt;?php echo number_format($heroMedia['vote_average'], 1); ?&gt;
            &lt;/span&gt;
            &lt;?php endif; ?&gt;
            &lt;span&gt;&lt;?php echo $tmdb-&gt;getYear($heroMedia); ?&gt;&lt;/span&gt;
            &lt;?php 
            $mediaType = $heroMedia['media_type'] == 'tv' ? '电视剧' : '电影';
            if (isset($heroMedia['first_air_date'])) $mediaType = '电视剧';
            if (isset($heroMedia['release_date'])) $mediaType = '电影';
            ?&gt;
            &lt;span&gt;&lt;?php echo $mediaType; ?&gt;&lt;/span&gt;
        &lt;/div&gt;
        &lt;p class="hero-overview"&gt;&lt;?php echo htmlspecialchars($heroMedia['overview'] ?? '暂无简介'); ?&gt;&lt;/p&gt;
        &lt;div class="hero-buttons"&gt;
            &lt;a href="/detail.php?type=&lt;?php echo $heroMedia['media_type'] ?? (isset($heroMedia['first_air_date']) ? 'tv' : 'movie'); ?&gt;&amp;id=&lt;?php echo $heroMedia['id']; ?&gt;" class="btn btn-primary"&gt;
                &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M8 5v14l11-7z"/&gt;&lt;/svg&gt;
                立即播放
            &lt;/a&gt;
            &lt;a href="/detail.php?type=&lt;?php echo $heroMedia['media_type'] ?? (isset($heroMedia['first_air_date']) ? 'tv' : 'movie'); ?&gt;&amp;id=&lt;?php echo $heroMedia['id']; ?&gt;" class="btn btn-secondary"&gt;
                &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/&gt;&lt;/svg&gt;
                详情信息
            &lt;/a&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/section&gt;
&lt;?php endif; ?&gt;

&lt;div class="main-content" style="padding-top: 0;"&gt;
    &lt;?php if (!empty($trending['results'])): ?&gt;
    &lt;section class="content-section"&gt;
        &lt;div class="section-header"&gt;
            &lt;h2 class="section-title"&gt;🔥 本周热门&lt;/h2&gt;
        &lt;/div&gt;
        &lt;div class="scroll-row"&gt;
            &lt;div class="scroll-arrow left" onclick="scrollRow(this, -1)"&gt;
                &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/&gt;&lt;/svg&gt;
            &lt;/div&gt;
            &lt;div class="scroll-container"&gt;
                &lt;?php foreach (array_slice($trending['results'], 0, 12) as $index =&gt; $item): ?&gt;
                &lt;?php
                $type = $item['media_type'] ?? (isset($item['first_air_date']) ? 'tv' : 'movie');
                $poster = $tmdb-&gt;getImageUrl($item['poster_path'], 'w342');
                $title = $item['title'] ?? $item['name'];
                $year = $tmdb-&gt;getYear($item);
                ?&gt;
                &lt;div class="scroll-card media-card" style="--index: &lt;?php echo $index; ?&gt;"&gt;
                    &lt;a href="/detail.php?type=&lt;?php echo $type; ?&gt;&amp;id=&lt;?php echo $item['id']; ?&gt;"&gt;
                        &lt;div class="media-poster"&gt;
                            &lt;?php if ($poster): ?&gt;
                            &lt;img src="&lt;?php echo $poster; ?&gt;" alt="&lt;?php echo htmlspecialchars($title); ?&gt;" loading="lazy"&gt;
                            &lt;?php else: ?&gt;
                            &lt;div style="width:100%;height:100%;background:var(--bg-tertiary);display:flex;align-items:center;justify-content:center;color:var(--text-muted);"&gt;无海报&lt;/div&gt;
                            &lt;?php endif; ?&gt;
                            &lt;div class="media-type-badge"&gt;&lt;?php echo $type == 'tv' ? '剧集' : '电影'; ?&gt;&lt;/div&gt;
                            &lt;?php if (!empty($item['vote_average'])): ?&gt;
                            &lt;div class="media-badge"&gt;
                                &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/&gt;&lt;/svg&gt;
                                &lt;?php echo number_format($item['vote_average'], 1); ?&gt;
                            &lt;/div&gt;
                            &lt;?php endif; ?&gt;
                            &lt;div class="media-overlay"&gt;
                                &lt;div class="play-btn-overlay"&gt;
                                    &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M8 5v14l11-7z"/&gt;&lt;/svg&gt;
                                &lt;/div&gt;
                            &lt;/div&gt;
                        &lt;/div&gt;
                        &lt;div class="media-info"&gt;
                            &lt;div class="media-title" title="&lt;?php echo htmlspecialchars($title); ?&gt;"&gt;&lt;?php echo htmlspecialchars($title); ?&gt;&lt;/div&gt;
                            &lt;div class="media-meta"&gt;
                                &lt;span class="media-year"&gt;&lt;?php echo $year; ?&gt;&lt;/span&gt;
                            &lt;/div&gt;
                        &lt;/div&gt;
                    &lt;/a&gt;
                &lt;/div&gt;
                &lt;?php endforeach; ?&gt;
            &lt;/div&gt;
            &lt;div class="scroll-arrow right" onclick="scrollRow(this, 1)"&gt;
                &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/&gt;&lt;/svg&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/section&gt;
    &lt;?php endif; ?&gt;

    &lt;?php if (!empty($popularMovies['results'])): ?&gt;
    &lt;section class="content-section"&gt;
        &lt;div class="section-header"&gt;
            &lt;h2 class="section-title"&gt;🎬 热门电影&lt;/h2&gt;
            &lt;a href="/movies.php" style="color: var(--theme-color); font-size: 14px; font-weight: 600;"&gt;查看更多 →&lt;/a&gt;
        &lt;/div&gt;
        &lt;div class="media-row"&gt;
            &lt;?php foreach (array_slice($popularMovies['results'], 0, 12) as $index =&gt; $item): ?&gt;
            &lt;div class="media-card" style="--index: &lt;?php echo $index; ?&gt;"&gt;
                &lt;a href="/detail.php?type=movie&amp;id=&lt;?php echo $item['id']; ?&gt;"&gt;
                    &lt;div class="media-poster"&gt;
                        &lt;?php if (!empty($item['poster_path'])): ?&gt;
                        &lt;img src="&lt;?php echo $tmdb-&gt;getImageUrl($item['poster_path'], 'w342'); ?&gt;" alt="&lt;?php echo htmlspecialchars($item['title']); ?&gt;" loading="lazy"&gt;
                        &lt;?php else: ?&gt;
                        &lt;div style="width:100%;height:100%;background:var(--bg-tertiary);display:flex;align-items:center;justify-content:center;color:var(--text-muted);"&gt;无海报&lt;/div&gt;
                        &lt;?php endif; ?&gt;
                        &lt;div class="media-type-badge"&gt;电影&lt;/div&gt;
                        &lt;?php if (!empty($item['vote_average'])): ?&gt;
                        &lt;div class="media-badge"&gt;
                            &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/&gt;&lt;/svg&gt;
                            &lt;?php echo number_format($item['vote_average'], 1); ?&gt;
                        &lt;/div&gt;
                        &lt;?php endif; ?&gt;
                        &lt;div class="media-overlay"&gt;
                            &lt;div class="play-btn-overlay"&gt;
                                &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M8 5v14l11-7z"/&gt;&lt;/svg&gt;
                            &lt;/div&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;
                    &lt;div class="media-info"&gt;
                        &lt;div class="media-title"&gt;&lt;?php echo htmlspecialchars($item['title']); ?&gt;&lt;/div&gt;
                        &lt;div class="media-meta"&gt;
                            &lt;span class="media-year"&gt;&lt;?php echo $tmdb-&gt;getYear($item); ?&gt;&lt;/span&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;
                &lt;/a&gt;
            &lt;/div&gt;
            &lt;?php endforeach; ?&gt;
        &lt;/div&gt;
    &lt;/section&gt;
    &lt;?php endif; ?&gt;

    &lt;?php if (!empty($popularTv['results'])): ?&gt;
    &lt;section class="content-section"&gt;
        &lt;div class="section-header"&gt;
            &lt;h2 class="section-title"&gt;📺 热门剧集&lt;/h2&gt;
            &lt;a href="/tv.php" style="color: var(--theme-color); font-size: 14px; font-weight: 600;"&gt;查看更多 →&lt;/a&gt;
        &lt;/div&gt;
        &lt;div class="media-row"&gt;
            &lt;?php foreach (array_slice($popularTv['results'], 0, 12) as $index =&gt; $item): ?&gt;
            &lt;div class="media-card" style="--index: &lt;?php echo $index; ?&gt;"&gt;
                &lt;a href="/detail.php?type=tv&amp;id=&lt;?php echo $item['id']; ?&gt;"&gt;
                    &lt;div class="media-poster"&gt;
                        &lt;?php if (!empty($item['poster_path'])): ?&gt;
                        &lt;img src="&lt;?php echo $tmdb-&gt;getImageUrl($item['poster_path'], 'w342'); ?&gt;" alt="&lt;?php echo htmlspecialchars($item['name']); ?&gt;" loading="lazy"&gt;
                        &lt;?php else: ?&gt;
                        &lt;div style="width:100%;height:100%;background:var(--bg-tertiary);display:flex;align-items:center;justify-content:center;color:var(--text-muted);"&gt;无海报&lt;/div&gt;
                        &lt;?php endif; ?&gt;
                        &lt;div class="media-type-badge"&gt;剧集&lt;/div&gt;
                        &lt;?php if (!empty($item['vote_average'])): ?&gt;
                        &lt;div class="media-badge"&gt;
                            &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/&gt;&lt;/svg&gt;
                            &lt;?php echo number_format($item['vote_average'], 1); ?&gt;
                        &lt;/div&gt;
                        &lt;?php endif; ?&gt;
                        &lt;div class="media-overlay"&gt;
                            &lt;div class="play-btn-overlay"&gt;
                                &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M8 5v14l11-7z"/&gt;&lt;/svg&gt;
                            &lt;/div&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;
                    &lt;div class="media-info"&gt;
                        &lt;div class="media-title"&gt;&lt;?php echo htmlspecialchars($item['name']); ?&gt;&lt;/div&gt;
                        &lt;div class="media-meta"&gt;
                            &lt;span class="media-year"&gt;&lt;?php echo $tmdb-&gt;getYear($item); ?&gt;&lt;/span&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;
                &lt;/a&gt;
            &lt;/div&gt;
            &lt;?php endforeach; ?&gt;
        &lt;/div&gt;
    &lt;/section&gt;
    &lt;?php endif; ?&gt;

    &lt;?php if (!empty($topRated['results'])): ?&gt;
    &lt;section class="content-section"&gt;
        &lt;div class="section-header"&gt;
            &lt;h2 class="section-title"&gt;⭐ 高分推荐&lt;/h2&gt;
        &lt;/div&gt;
        &lt;div class="media-row"&gt;
            &lt;?php foreach (array_slice($topRated['results'], 0, 12) as $index =&gt; $item): ?&gt;
            &lt;div class="media-card" style="--index: &lt;?php echo $index; ?&gt;"&gt;
                &lt;a href="/detail.php?type=movie&amp;id=&lt;?php echo $item['id']; ?&gt;"&gt;
                    &lt;div class="media-poster"&gt;
                        &lt;?php if (!empty($item['poster_path'])): ?&gt;
                        &lt;img src="&lt;?php echo $tmdb-&gt;getImageUrl($item['poster_path'], 'w342'); ?&gt;" alt="&lt;?php echo htmlspecialchars($item['title']); ?&gt;" loading="lazy"&gt;
                        &lt;?php else: ?&gt;
                        &lt;div style="width:100%;height:100%;background:var(--bg-tertiary);display:flex;align-items:center;justify-content:center;color:var(--text-muted);"&gt;无海报&lt;/div&gt;
                        &lt;?php endif; ?&gt;
                        &lt;div class="media-type-badge"&gt;电影&lt;/div&gt;
                        &lt;?php if (!empty($item['vote_average'])): ?&gt;
                        &lt;div class="media-badge"&gt;
                            &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/&gt;&lt;/svg&gt;
                            &lt;?php echo number_format($item['vote_average'], 1); ?&gt;
                        &lt;/div&gt;
                        &lt;?php endif; ?&gt;
                        &lt;div class="media-overlay"&gt;
                            &lt;div class="play-btn-overlay"&gt;
                                &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M8 5v14l11-7z"/&gt;&lt;/svg&gt;
                            &lt;/div&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;
                    &lt;div class="media-info"&gt;
                        &lt;div class="media-title"&gt;&lt;?php echo htmlspecialchars($item['title']); ?&gt;&lt;/div&gt;
                        &lt;div class="media-meta"&gt;
                            &lt;span class="media-year"&gt;&lt;?php echo $tmdb-&gt;getYear($item); ?&gt;&lt;/span&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;
                &lt;/a&gt;
            &lt;/div&gt;
            &lt;?php endforeach; ?&gt;
        &lt;/div&gt;
    &lt;/section&gt;
    &lt;?php endif; ?&gt;
&lt;/div&gt;

&lt;script&gt;
function scrollRow(btn, direction) {
    const container = btn.parentElement.querySelector('.scroll-container');
    const scrollAmount = container.offsetWidth * 0.8 * direction;
    container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
}
&lt;/script&gt;

&lt;?php if ($showAnnouncement &amp;&amp; $announcement): ?&gt;
&lt;!-- 公告弹窗 --&gt;
&lt;div class="modal active" id="announcementModal"&gt;
    &lt;div class="modal-content" style="max-width: 500px;"&gt;
        &lt;div class="modal-header"&gt;
            &lt;h3&gt;&lt;svg viewBox="0 0 24 24" style="width:24px;height:24px;vertical-align:middle;margin-right:8px;fill:var(--theme-color);"&gt;&lt;path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/&gt;&lt;/svg&gt;&lt;?php echo htmlspecialchars($announcement['title']); ?&gt;&lt;/h3&gt;
        &lt;/div&gt;
        &lt;div class="modal-body" style="line-height: 1.8; color: var(--text-secondary);"&gt;
            &lt;?php echo nl2br(htmlspecialchars($announcement['content'])); ?&gt;
        &lt;/div&gt;
        &lt;div class="modal-footer" style="flex-direction: column; gap: 12px; align-items: stretch;"&gt;
            &lt;label style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--text-muted); cursor: pointer; user-select: none;"&gt;
                &lt;input type="checkbox" id="dontShowAgain" style="width: 16px; height: 16px; accent-color: var(--theme-color);"&gt;
                不再提示此公告
            &lt;/label&gt;
            &lt;button onclick="closeAnnouncement()" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 16px;"&gt;我知道了&lt;/button&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
function closeAnnouncement() {
    const dontShow = document.getElementById('dontShowAgain').checked;
    const annId = &lt;?php echo $announcement['id']; ?&gt;;
    
    if (dontShow) {
        fetch('/api/announcement_read.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'announcement_id=' + annId
        });
    }
    
    document.getElementById('announcementModal').classList.remove('active');
}
&lt;/script&gt;
&lt;?php endif; ?&gt;

&lt;?php endif; ?&gt;

&lt;?php include __DIR__ . '/includes/footer.php'; ?&gt;
