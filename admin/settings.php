&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$adminPage = 'settings';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = '无效的请求';
    } else {
        $settings_to_update = [
            'theme_color' =&gt; $_POST['theme_color'] ?? '#e50914',
            'tmdb_api_key' =&gt; trim($_POST['tmdb_api_key'] ?? ''),
            'player_url' =&gt; trim($_POST['player_url'] ?? 'https://svip.ffzyplay.com/?url='),
            'site_name' =&gt; trim($_POST['site_name'] ?? 'Jay影视'),
        ];
        
        foreach ($settings_to_update as $key =&gt; $value) {
            $exists = $db-&gt;fetch("SELECT id FROM settings WHERE setting_key = ?", [$key]);
            if ($exists) {
                $db-&gt;query("UPDATE settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
            } else {
                $db-&gt;query("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)", [$key, $value]);
            }
        }
        
        $success = '设置保存成功，刷新页面生效';
    }
}

// 重新加载设置
$settings = [];
$settingsResult = $db-&gt;query("SELECT setting_key, setting_value FROM settings");
while ($row = $settingsResult-&gt;fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

$page_title = '网站设置';
include __DIR__ . '/header.php';
?&gt;

&lt;div class="admin-header"&gt;
    &lt;h1&gt;网站设置&lt;/h1&gt;
&lt;/div&gt;

&lt;?php if (isset($error)): ?&gt;
&lt;div class="alert alert-error"&gt;&lt;?php echo htmlspecialchars($error); ?&gt;&lt;/div&gt;
&lt;?php endif; ?&gt;
&lt;?php if (isset($success)): ?&gt;
&lt;div class="alert alert-success"&gt;&lt;?php echo htmlspecialchars($success); ?&gt;&lt;/div&gt;
&lt;?php endif; ?&gt;

&lt;form method="post"&gt;
    &lt;input type="hidden" name="csrf_token" value="&lt;?php echo generateCSRFToken(); ?&gt;"&gt;
    
    &lt;div class="admin-card"&gt;
        &lt;h3&gt;基本设置&lt;/h3&gt;
        &lt;div class="form-row-2"&gt;
            &lt;div class="form-group"&gt;
                &lt;label class="form-label"&gt;网站名称&lt;/label&gt;
                &lt;input type="text" name="site_name" class="form-input" value="&lt;?php echo htmlspecialchars($settings['site_name'] ?? 'Jay影视'); ?&gt;" required&gt;
            &lt;/div&gt;
            &lt;div class="form-group"&gt;
                &lt;label class="form-label"&gt;主题颜色&lt;/label&gt;
                &lt;div class="color-picker-wrap"&gt;
                    &lt;input type="color" name="theme_color" class="color-picker" value="&lt;?php echo htmlspecialchars($settings['theme_color'] ?? '#e50914'); ?&gt;" id="themeColor"&gt;
                    &lt;input type="text" class="form-input" value="&lt;?php echo htmlspecialchars($settings['theme_color'] ?? '#e50914'); ?&gt;" id="themeColorText" onchange="document.getElementById('themeColor').value = this.value" style="width: 150px;"&gt;
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/div&gt;
    
    &lt;div class="admin-card"&gt;
        &lt;h3&gt;API 设置&lt;/h3&gt;
        &lt;div class="form-group"&gt;
            &lt;label class="form-label"&gt;TMDB API Key&lt;/label&gt;
            &lt;input type="text" name="tmdb_api_key" class="form-input" value="&lt;?php echo htmlspecialchars($settings['tmdb_api_key'] ?? ''); ?&gt;" placeholder="输入您的TMDB API Key"&gt;
            &lt;p style="color: var(--text-muted); font-size: 13px; margin-top: 8px;"&gt;
                获取地址：&lt;a href="https://www.themoviedb.org/settings/api" target="_blank" style="color: var(--theme-color);"&gt;https://www.themoviedb.org/settings/api&lt;/a&gt;
            &lt;/p&gt;
        &lt;/div&gt;
        &lt;div class="form-group"&gt;
            &lt;label class="form-label"&gt;播放器解析地址&lt;/label&gt;
            &lt;input type="url" name="player_url" class="form-input" value="&lt;?php echo htmlspecialchars($settings['player_url'] ?? 'https://svip.ffzyplay.com/?url='); ?&gt;" placeholder="https://svip.ffzyplay.com/?url="&gt;
        &lt;/div&gt;
    &lt;/div&gt;
    
    &lt;button type="submit" class="btn btn-primary" style="padding: 14px 40px; font-size: 16px;"&gt;保存设置&lt;/button&gt;
&lt;/form&gt;

&lt;div class="admin-card" style="margin-top: 24px;"&gt;
    &lt;h3&gt;SMTP 邮件配置&lt;/h3&gt;
    &lt;p style="color: var(--text-secondary); line-height: 1.8;"&gt;
        SMTP 配置已在 config.php 文件中设置，如需修改请编辑该文件。&lt;br&gt;
        当前配置：&lt;br&gt;
        &lt;strong&gt;SMTP主机：&lt;/strong&gt; &lt;?php echo SMTP_HOST; ?&gt;:&lt;?php echo SMTP_PORT; ?&gt;&lt;br&gt;
        &lt;strong&gt;发件邮箱：&lt;/strong&gt; &lt;?php echo SMTP_USER; ?&gt;&lt;br&gt;
        &lt;strong&gt;发件人名称：&lt;/strong&gt; &lt;?php echo SMTP_FROM_NAME; ?&gt;
    &lt;/p&gt;
&lt;/div&gt;

&lt;script&gt;
document.getElementById('themeColor').addEventListener('input', function() {
    document.getElementById('themeColorText').value = this.value;
});
&lt;/script&gt;

&lt;?php include __DIR__ . '/footer.php'; ?&gt;
