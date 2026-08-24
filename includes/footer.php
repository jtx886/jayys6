&lt;?php if (!defined('IN_SITE')) die('Access denied'); ?&gt;
&lt;footer style="background: var(--bg-secondary); padding: 40px; margin-top: 60px; text-align: center; border-top: 1px solid var(--border-color);"&gt;
    &lt;div style="max-width: 1200px; margin: 0 auto;"&gt;
        &lt;div style="font-size: 28px; font-weight: 900; margin-bottom: 12px; background: linear-gradient(135deg, var(--theme-color) 0%, #ff6b6b 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"&gt;JAY&lt;span style="background: linear-gradient(135deg, #ffd700 0%, #ff8c00 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 18px;"&gt;影视&lt;/span&gt;&lt;/div&gt;
        &lt;p style="color: var(--text-muted); font-size: 14px; margin-bottom: 8px;"&gt;© &lt;?php echo date('Y'); ?&gt; &lt;?php echo SITE_NAME; ?&gt; - 尽享观影时光&lt;/p&gt;
        &lt;p style="color: var(--text-muted); font-size: 12px;"&gt;本站数据来源于TMDB，视频解析来源于网络，如有侵权请联系删除&lt;/p&gt;
    &lt;/div&gt;
&lt;/footer&gt;

&lt;?php if (isLoggedIn() &amp;&amp; basename($_SERVER['PHP_SELF']) == 'index.php'): ?&gt;
&lt;!-- 公告弹窗 --&gt;
&lt;?php
global $db;
$announcement = $db-&gt;fetch("SELECT * FROM announcements WHERE is_active = 1 ORDER BY created_at DESC LIMIT 1");
if ($announcement) {
    $read = $db-&gt;fetch("SELECT id FROM announcement_reads WHERE user_id = ? AND announcement_id = ?", [$_SESSION['user_id'], $announcement['id']]);
    if (!$read):
?&gt;
&lt;div class="announcement-modal" id="announcementModal"&gt;
    &lt;div class="announcement-content"&gt;
        &lt;div class="announcement-header"&gt;
            &lt;div class="announcement-icon"&gt;
                &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/&gt;&lt;/svg&gt;
            &lt;/div&gt;
            &lt;div class="announcement-title"&gt;&lt;?php echo htmlspecialchars($announcement['title']); ?&gt;&lt;/div&gt;
        &lt;/div&gt;
        &lt;div class="announcement-body"&gt;
            &lt;div class="announcement-text"&gt;&lt;?php echo nl2br(htmlspecialchars($announcement['content'])); ?&gt;&lt;/div&gt;
            &lt;label class="announcement-checkbox"&gt;
                &lt;input type="checkbox" id="dontShowAgain"&gt;
                不再提示此公告
            &lt;/label&gt;
            &lt;button class="announcement-close" onclick="closeAnnouncement()"&gt;我知道了&lt;/button&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;script&gt;
function closeAnnouncement() {
    const dontShow = document.getElementById('dontShowAgain').checked;
    if (dontShow) {
        fetch('/api/announcement_read.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'announcement_id=&lt;?php echo $announcement['id']; ?&gt;&amp;csrf_token=&lt;?php echo generateCSRFToken(); ?&gt;'
        });
    }
    document.getElementById('announcementModal').style.display = 'none';
}
&lt;/script&gt;
&lt;?php endif; } ?&gt;
&lt;?php elseif (!isLoggedIn() &amp;&amp; basename($_SERVER['PHP_SELF']) == 'index.php'): ?&gt;
&lt;?php
global $db;
$announcement = $db-&gt;fetch("SELECT * FROM announcements WHERE is_active = 1 ORDER BY created_at DESC LIMIT 1");
if ($announcement):
?&gt;
&lt;div class="announcement-modal" id="announcementModal"&gt;
    &lt;div class="announcement-content"&gt;
        &lt;div class="announcement-header"&gt;
            &lt;div class="announcement-icon"&gt;
                &lt;svg viewBox="0 0 24 24"&gt;&lt;path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/&gt;&lt;/svg&gt;
            &lt;/div&gt;
            &lt;div class="announcement-title"&gt;&lt;?php echo htmlspecialchars($announcement['title']); ?&gt;&lt;/div&gt;
        &lt;/div&gt;
        &lt;div class="announcement-body"&gt;
            &lt;div class="announcement-text"&gt;&lt;?php echo nl2br(htmlspecialchars($announcement['content'])); ?&gt;&lt;/div&gt;
            &lt;button class="announcement-close" onclick="document.getElementById('announcementModal').style.display='none'"&gt;我知道了&lt;/button&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;?php endif; ?&gt;
&lt;?php endif; ?&gt;

&lt;/body&gt;
&lt;/html&gt;
