&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$tmdb = new TMDB();
$type = $_GET['type'] ?? 'movie';
$id = intval($_GET['id'] ?? 0);
$season = intval($_GET['season'] ?? 0);
$episode = intval($_GET['episode'] ?? 0);
$dub = $_GET['dub'] ?? 'original';

if (!in_array($type, ['movie', 'tv']) || $id &lt;= 0) {
    redirect('/');
}

$details = $tmdb-&gt;getDetails($type, $id);
if (!$details || isset($details['success']) &amp;&amp; !$details['success']) {
    redirect('/');
}

$title = $details['title'] ?? $details['name'];
$playTitle = $title;

// 构建搜索关键词
$searchKeyword = $title;
if ($type == 'tv' &amp;&amp; $season &gt; 0 &amp;&amp; $episode &gt; 0) {
    $playTitle .= ' 第' . $season . '季 第' . $episode . '集';
    $searchKeyword .= ' 第' . $season . '季';
}

// 普通话版本搜索词
if ($dub == 'mandarin') {
    $searchKeyword .= ' 普通话';
}

// 记录观看历史
$poster = $details['poster_path'] ?? '';
$vote = $details['vote_average'] ?? 0;
$releaseDate = $type == 'movie' ? ($details['release_date'] ?? '') : ($details['first_air_date'] ?? '');
$db-&gt;query("DELETE FROM watch_history WHERE user_id = ? AND media_id = ? AND media_type = ?", [$_SESSION['user_id'], $id, $type]);
$db-&gt;query("INSERT INTO watch_history (user_id, media_id, media_type, title, poster, vote_average, release_date, season, episode, watched_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())", 
    [$_SESSION['user_id'], $id, $type, $title, $poster, $vote, $releaseDate, $season ?: null, $episode ?: null]);

// 获取播放源
$sources = getActiveSources();
$playerUrl = PLAYER_URL;

$page_title = '正在播放: ' . $playTitle;
?&gt;
&lt;!DOCTYPE html&gt;
&lt;html lang="zh-CN"&gt;
&lt;head&gt;
    &lt;meta charset="UTF-8"&gt;
    &lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;
    &lt;title&gt;&lt;?php echo $page_title; ?&gt; - &lt;?php echo SITE_NAME; ?&gt;&lt;/title&gt;
    &lt;link rel="stylesheet" href="/assets/css/style.php"&gt;
    &lt;style&gt;
        body { background: #000; }
    &lt;/style&gt;
&lt;/head&gt;
&lt;body class="player-page"&gt;
    &lt;div class="player-container"&gt;
        &lt;a href="/detail.php?type=&lt;?php echo $type; ?&gt;&amp;id=&lt;?php echo $id; ?&gt;" class="player-back"&gt;
            &lt;svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"&gt;
                &lt;path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/&gt;
            &lt;/svg&gt;
            返回详情
        &lt;/a&gt;
        
        &lt;div id="playerWrapper" style="width: 100%; height: 100%;"&gt;
            &lt;div id="loadingTip" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: white;"&gt;
                &lt;div class="loading-spinner" style="border-top-color: var(--theme-color);"&gt;&lt;/div&gt;
                &lt;p style="margin-top: 20px; font-size: 18px;"&gt;正在加载播放源...&lt;/p&gt;
                &lt;p style="margin-top: 8px; color: var(--text-muted); font-size: 14px;"&gt;请稍候，如果长时间未加载请切换播放源&lt;/p&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/div&gt;
    
    &lt;div style="background: var(--bg-primary); padding: 30px 40px;"&gt;
        &lt;div style="max-width: 1200px; margin: 0 auto;"&gt;
            &lt;h2 style="font-size: 24px; margin-bottom: 8px;"&gt;&lt;?php echo htmlspecialchars($playTitle); ?&gt;&lt;/h2&gt;
            &lt;p style="color: var(--text-secondary); margin-bottom: 20px;"&gt;&lt;?php echo htmlspecialchars(mb_substr($details['overview'] ?? '', 0, 200)); ?&gt;&lt;/p&gt;
            
            &lt;?php if (count($sources) &gt; 1): ?&gt;
            &lt;div&gt;
                &lt;h4 style="margin-bottom: 12px; font-size: 16px;"&gt;切换播放源&lt;/h4&gt;
                &lt;div style="display: flex; gap: 10px; flex-wrap: wrap;"&gt;
                    &lt;?php foreach ($sources as $index =&gt; $source): ?&gt;
                    &lt;button class="btn btn-&lt;?php echo $index === 0 ? 'primary' : 'secondary'; ?&gt;" 
                            style="padding: 8px 20px; font-size: 14px;"
                            onclick="switchSource(&lt;?php echo $index; ?&gt;, '&lt;?php echo htmlspecialchars($source['url']); ?&gt;')"&gt;
                        &lt;?php echo htmlspecialchars($source['name']); ?&gt;
                    &lt;/button&gt;
                    &lt;?php endforeach; ?&gt;
                &lt;/div&gt;
            &lt;/div&gt;
            &lt;?php endif; ?&gt;
        &lt;/div&gt;
    &lt;/div&gt;

    &lt;script&gt;
    const sources = &lt;?php echo json_encode(array_map(function($s) { return ['url' =&gt; $s['url'], 'name' =&gt; $s['name']]; }, $sources)); ?&gt;;
    const playerBase = &lt;?php echo json_encode($playerUrl); ?&gt;;
    const keyword = &lt;?php echo json_encode($searchKeyword); ?&gt;;
    let currentSourceIndex = 0;
    
    function playSource(index) {
        const source = sources[index];
        const wrapper = document.getElementById('playerWrapper');
        const loading = document.getElementById('loadingTip');
        
        if (loading) loading.style.display = 'flex';
        
        // 尝试解析播放链接 - 直接用关键词构造解析链接
        // 对于YYZY源，可以直接构造播放URL
        let videoUrl = '';
        if (source.url.includes('yyzy-tv')) {
            // 这个源的格式，我们直接通过解析接口
            videoUrl = source.url + '?wd=' + encodeURIComponent(keyword);
        } else {
            videoUrl = source.url + (source.url.includes('?') ? '&amp;' : '?') + 'wd=' + encodeURIComponent(keyword);
        }
        
        const iframe = document.createElement('iframe');
        iframe.className = 'player-iframe';
        iframe.src = playerBase + encodeURIComponent(videoUrl);
        iframe.allowFullscreen = true;
        iframe.onload = function() {
            if (loading) loading.style.display = 'none';
        };
        
        wrapper.innerHTML = '';
        wrapper.appendChild(iframe);
        if (loading) {
            wrapper.appendChild(loading);
        }
    }
    
    function switchSource(index, url) {
        currentSourceIndex = index;
        document.querySelectorAll('.player-page button').forEach(b =&gt; {
            b.className = 'btn btn-secondary';
            b.style.padding = '8px 20px';
            b.style.fontSize = '14px';
        });
        event.target.className = 'btn btn-primary';
        event.target.style.padding = '8px 20px';
        event.target.style.fontSize = '14px';
        playSource(index);
    }
    
    // 初始播放第一个源
    playSource(0);
    &lt;/script&gt;
&lt;/body&gt;
&lt;/html&gt;
