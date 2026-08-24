&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

$tmdb = new TMDB();
$page = intval($_GET['page'] ?? 1);
// TMDB动漫分类ID: 16动画
$anime = $tmdb-&gt;getByGenre('tv', 16, $page);

$page_title = '动漫';
include __DIR__ . '/includes/header.php';
?&gt;

&lt;div class="main-content"&gt;
    &lt;section class="content-section"&gt;
        &lt;div class="section-header"&gt;
            &lt;h2 class="section-title"&gt;🎌 动漫&lt;/h2&gt;
        &lt;/div&gt;
        
        &lt;?php if (!empty($anime['results'])): ?&gt;
        &lt;div class="media-row"&gt;
            &lt;?php foreach ($anime['results'] as $index =&gt; $item): ?&gt;
            &lt;div class="media-card" style="--index: &lt;?php echo $index % 12; ?&gt;"&gt;
                &lt;a href="/detail.php?type=tv&amp;id=&lt;?php echo $item['id']; ?&gt;"&gt;
                    &lt;div class="media-poster"&gt;
                        &lt;?php if (!empty($item['poster_path'])): ?&gt;
                        &lt;img src="&lt;?php echo $tmdb-&gt;getImageUrl($item['poster_path'], 'w342'); ?&gt;" alt="&lt;?php echo htmlspecialchars($item['name'] ?? $item['title']); ?&gt;" loading="lazy"&gt;
                        &lt;?php else: ?&gt;
                        &lt;div style="width:100%;height:100%;background:var(--bg-tertiary);display:flex;align-items:center;justify-content:center;color:var(--text-muted);"&gt;无海报&lt;/div&gt;
                        &lt;?php endif; ?&gt;
                        &lt;div class="media-type-badge"&gt;动漫&lt;/div&gt;
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
                        &lt;div class="media-title"&gt;&lt;?php echo htmlspecialchars($item['name'] ?? $item['title']); ?&gt;&lt;/div&gt;
                        &lt;div class="media-meta"&gt;
                            &lt;span class="media-year"&gt;&lt;?php echo $tmdb-&gt;getYear($item); ?&gt;&lt;/span&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;
                &lt;/a&gt;
            &lt;/div&gt;
            &lt;?php endforeach; ?&gt;
        &lt;/div&gt;
        
        &lt;div style="display: flex; justify-content: center; gap: 12px; margin-top: 40px;"&gt;
            &lt;?php if ($page &gt; 1): ?&gt;
            &lt;a href="?page=&lt;?php echo $page - 1; ?&gt;" class="btn btn-secondary"&gt;← 上一页&lt;/a&gt;
            &lt;?php endif; ?&gt;
            &lt;?php if ($page &lt; ($anime['total_pages'] ?? 1)): ?&gt;
            &lt;a href="?page=&lt;?php echo $page + 1; ?&gt;" class="btn btn-primary"&gt;下一页 →&lt;/a&gt;
            &lt;?php endif; ?&gt;
        &lt;/div&gt;
        &lt;?php else: ?&gt;
        &lt;div class="empty-state"&gt;
            &lt;h3&gt;暂无数据&lt;/h3&gt;
        &lt;/div&gt;
        &lt;?php endif; ?&gt;
    &lt;/section&gt;
&lt;/div&gt;

&lt;?php include __DIR__ . '/includes/footer.php'; ?&gt;
