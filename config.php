&lt;?php
// Jay影视配置文件

// 数据库配置 - InfinityFree免费主机通常使用localhost
define('DB_HOST', 'localhost');
define('DB_NAME', 'jay_movie');
define('DB_USER', 'root');
define('DB_PASS', '');

// SMTP邮件配置
define('SMTP_HOST', 'smtp.163.com');
define('SMTP_PORT', 465);
define('SMTP_USER', 'jtxnb886@163.com');
define('SMTP_PASS', 'FLLRDtadYAfGXp9Y');
define('SMTP_FROM', 'jtxnb886@163.com');
define('SMTP_FROM_NAME', 'Jay影视');

// TMDB API配置
define('TMDB_API_KEY', '');
define('TMDB_API_BASE', 'https://api.themoviedb.org/3');
define('TMDB_IMAGE_BASE', 'https://image.tmdb.org/t/p');

// 站点URL
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST']);

// 会话启动
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 自动加载类
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/includes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// 初始化数据库连接
try {
    $db = new Database();
} catch (Exception $e) {
    // 如果数据库不存在，尝试创建
    die('数据库连接失败，请先导入database.sql文件并配置数据库信息。错误: ' . $e-&gt;getMessage());
}

// 获取网站设置
$settings = [];
$settingsResult = $db-&gt;query("SELECT setting_key, setting_value FROM settings");
while ($row = $settingsResult-&gt;fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// 定义主题颜色
define('THEME_COLOR', isset($settings['theme_color']) ? $settings['theme_color'] : '#e50914');
define('SITE_NAME', isset($settings['site_name']) ? $settings['site_name'] : 'Jay影视');
define('PLAYER_URL', isset($settings['player_url']) ? $settings['player_url'] : 'https://svip.ffzyplay.com/?url=');

// 获取当前登录用户
$current_user = null;
if (isset($_SESSION['user_id'])) {
    $current_user = $db-&gt;fetch("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
    // 检查是否被封禁
    if ($current_user &amp;&amp; $current_user['is_banned']) {
        if ($current_user['ban_until'] &amp;&amp; strtotime($current_user['ban_until']) &gt; time()) {
            // 仍在封禁期
        } elseif ($current_user['ban_until']) {
            // 封禁到期，自动解封
            $db-&gt;query("UPDATE users SET is_banned = 0, ban_until = NULL, ban_reason = '' WHERE id = ?", [$current_user['id']]);
            $current_user['is_banned'] = 0;
        }
    }
}
?&gt;
