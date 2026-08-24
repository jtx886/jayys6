&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

$tmdb = new TMDB();
$q = trim($_GET['q'] ?? '');
$page = intval($_GET['page'] ?? 1);
$results = [];

if ($q) {
    $results = $tmdb-&gt;search($q, 'multi', $page);
}

$page_title = $q ? '搜索: ' . $q : '搜索';
include __DIR__ . '/includes/header.php';
?&gt;

&lt;div class="main-content"&gt;
    &lt;section class="content-section"&gt;
        &lt;div class="section-header"&gt;
            &lt;h2 class="section-title"&gt;🔍 搜索结果&lt;/h2&gt;
        &lt;/div&gt;
        
        &lt;div style="margin-bottom: 24px;"&gt;
            &lt;form action="/search.php" method="get" style="display: flex; gap: 12px; max-width: 600px;"&gt;
                &lt;input type="text" name="q" value="&lt;?php echo htmlspecialchars($q); ?&gt;" class="form-input" placeholder="搜索电影、电视剧..." required style="flex:1;"&gt;
                &lt;button type="submit" class="btn btn-primary"&gt;搜索&lt;/button&gt;
            &lt;/form&gt;
        &lt;/div&gt;
        
        &lt;?php if ($q): ?&gt;
            &lt;p style="color: var(--text-secondary); margin-bottom: 20px;"&gt;找到 &lt;?php echo $results['total_results'] ?? 0; ?&gt; 个与 "&lt;strong style="color: var(--theme-color);"&gt;&lt;?php echo htmlspecialchars($q); ?&gt;&lt;/strong&gt;" 相关的结果&lt;/p&gt;
            
            &lt;?php if (!empty($results['results'])): ?&gt;
            &lt;div class="media-row"&gt;
                &lt;?php 
                $mediaResults = array_filter($results['results'], function($item) {
                    return in_array($item['media_type'], ['movie', 'tv']);
                });
                foreach ($mediaResults as $index =&gt; $item): 
                    $type = $item['media_type'];
                    $title = $item['title'] ?? $item['name'];
                ?&gt;
                &lt;div class="media-card" style="--index: &lt;?php echo $index % 12; ?&gt;"&gt;
                    &lt;a href="/detail.php?type=&lt;?php echo $type; ?&gt;&amp;id=&lt;?php echo $item['id']; ?&gt;"&gt;
                        &lt;div class="media-poster"&gt;
                            &lt;?php if (!empty($item['poster_path'])): ?&gt;
                            &lt;img src="&lt;?php echo $tmdb-&gt;getImageUrl($item['poster_path'], 'w342'); ?&gt;" alt="&lt;?php echo htmlspecialchars($title); ?&gt;" loading="lazy"&gt;
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
                            &lt;div class="media-title"&gt;&lt;?php echo htmlspecialchars($title); ?&gt;&lt;/div&gt;
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
                &lt;a href="?q=&lt;?php echo urlencode($q); ?&gt;&amp;page=&lt;?php echo $page - 1; ?&gt;" class="btn btn-secondary"&gt;← 上一页&lt;/a&gt;
                &lt;?php endif; ?&gt;
                &lt;?php if ($page &lt; ($results['total_pages'] ?? 1)): ?&gt;
                &lt;a href="?q=&lt;?php echo urlencode($q); ?&gt;&amp;page=&lt;?php echo $page + 1; ?&gt;" class="btn btn-primary"&gt;下一页 →&lt;/a&gt;
                &lt;?php endif; ?&gt;
            &lt;/div&gt;
            &lt;?php else: ?&gt;
            &lt;div class="empty-state"&gt;
                &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/&gt;&lt;/svg&gt;
                &lt;h3&gt;没有找到相关结果&lt;/h3&gt;
                &lt;p&gt;试试其他关键词吧&lt;/p&gt;
            &lt;/div&gt;
            &lt;?php endif; ?&gt;
        &lt;?php else: ?&gt;
        &lt;div class="empty-state"&gt;
            &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/&gt;&lt;/svg&gt;
            &lt;h3&gt;搜索影视&lt;/h3&gt;
            &lt;p&gt;输入电影或电视剧名称开始搜索&lt;/p&gt;
        &lt;/div&gt;
        &lt;?php endif; ?&gt;
    &lt;/section&gt;
&lt;/div&gt;

&lt;?php include __DIR__ . '/includes/footer.php'; ?&gt;
