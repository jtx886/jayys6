&lt;?php
/**
 * Jay影视安装脚本
 * 访问此文件自动初始化数据库和创建管理员账号
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "&lt;!DOCTYPE html&gt;&lt;html&gt;&lt;head&gt;&lt;meta charset='utf-8'&gt;&lt;title&gt;Jay影视 安装&lt;/title&gt;";
echo "&lt;style&gt;body{font-family:Arial,sans-serif;max-width:700px;margin:50px auto;padding:20px;background:#141414;color:#fff;}h1{color:#e50914;}.success{color:#46d369;}.error{color:#e87c03;}a{color:#e50914;text-decoration:none;}hr{border:0;border-top:1px solid #333;margin:20px 0;}&lt;/style&gt;";
echo "&lt;/head&gt;&lt;body&gt;";
echo "&lt;h1&gt;🎬 Jay影视 安装程序&lt;/h1&gt;";

// 数据库配置 - 用户需要根据自己的主机修改这里
$db_host = 'localhost';
$db_name = 'jay_movie';
$db_user = 'root';
$db_pass = '';

// 创建uploads目录
$uploadDir = __DIR__ . '/uploads/avatars';
if (!file_exists($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
    echo "&lt;p class='success'&gt;✓ 上传目录创建成功&lt;/p&gt;";
} else {
    echo "&lt;p&gt;✓ 上传目录已存在&lt;/p&gt;";
}

// 创建.htaccess保护uploads目录
$htaccess = __DIR__ . '/uploads/.htaccess';
if (!file_exists($htaccess)) {
    @file_put_contents($htaccess, "Options -Indexes\n");
}

try {
    // 连接数据库（不指定数据库名）
    $pdo = new PDO("mysql:host=$db_host;charset=utf8mb4", $db_user, $db_pass);
    $pdo-&gt;setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "&lt;p class='success'&gt;✓ 数据库连接成功&lt;/p&gt;";
    
    // 创建数据库
    $pdo-&gt;exec("CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "&lt;p class='success'&gt;✓ 数据库创建成功&lt;/p&gt;";
    
    // 选择数据库
    $pdo-&gt;exec("USE `$db_name`");
    
    // 读取SQL文件
    $sql = file_get_contents(__DIR__ . '/database.sql');
    
    // 移除CREATE DATABASE和USE语句（因为已经执行了）
    $sql = preg_replace('/CREATE DATABASE.*?;/i', '', $sql);
    $sql = preg_replace('/USE.*?;/i', '', $sql);
    
    // 分割SQL语句执行
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            try {
                $pdo-&gt;exec($stmt);
            } catch (Exception $e) {
                // 忽略已存在的表错误
                if (strpos($e-&gt;getMessage(), 'already exists') === false) {
                    echo "&lt;p class='error'&gt;⚠ SQL警告: " . $e-&gt;getMessage() . "&lt;/p&gt;";
                }
            }
        }
    }
    echo "&lt;p class='success'&gt;✓ 数据表创建成功&lt;/p&gt;";
    
    // 创建管理员账号（杰同学 / 101113）
    $admin_username = '杰同学';
    $admin_password = password_hash('101113', PASSWORD_DEFAULT);
    $admin_email = 'jtxnb886@163.com';
    
    // 先检查是否已存在管理员
    $stmt = $pdo-&gt;prepare("SELECT id FROM users WHERE is_admin = 1 LIMIT 1");
    $stmt-&gt;execute();
    if (!$stmt-&gt;fetch()) {
        $stmt = $pdo-&gt;prepare("INSERT INTO users (username, email, password, is_admin, email_verified) VALUES (?, ?, ?, 1, 1)");
        $stmt-&gt;execute([$admin_username, $admin_email, $admin_password]);
        echo "&lt;p class='success'&gt;✓ 管理员账号创建成功（用户名：杰同学，密码：101113）&lt;/p&gt;";
    } else {
        echo "&lt;p&gt;✓ 管理员账号已存在&lt;/p&gt;";
    }
    
    // 添加默认播放源
    $stmt = $pdo-&gt;prepare("SELECT id FROM sources WHERE url = ? LIMIT 1");
    $stmt-&gt;execute(['https://api.yyzy-tv.vip/inc/apijson.php']);
    if (!$stmt-&gt;fetch()) {
        $stmt = $pdo-&gt;prepare("INSERT INTO sources (name, url, priority, is_active) VALUES (?, ?, 10, 1)");
        $stmt-&gt;execute(['优亿资源', 'https://api.yyzy-tv.vip/inc/apijson.php']);
        echo "&lt;p class='success'&gt;✓ 默认播放源添加成功&lt;/p&gt;";
    }
    
    // 添加默认设置
    $default_settings = [
        ['theme_color', '#e50914'],
        ['site_name', 'Jay影视'],
        ['player_url', 'https://svip.ffzyplay.com/?url='],
    ];
    
    foreach ($default_settings as $s) {
        $stmt = $pdo-&gt;prepare("SELECT id FROM settings WHERE setting_key = ? LIMIT 1");
        $stmt-&gt;execute([$s[0]]);
        if (!$stmt-&gt;fetch()) {
            $stmt = $pdo-&gt;prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
            $stmt-&gt;execute($s);
        }
    }
    echo "&lt;p class='success'&gt;✓ 默认设置完成&lt;/p&gt;";
    
    echo "&lt;hr&gt;";
    echo "&lt;h2 style='color: #46d369;'&gt;🎉 安装完成！&lt;/h2&gt;";
    echo "&lt;p&gt;&lt;a href='/' style='font-size: 18px;'&gt;→ 点击进入网站首页&lt;/a&gt;&lt;/p&gt;";
    echo "&lt;p&gt;&lt;a href='/admin/' style='font-size: 18px;'&gt;→ 点击进入管理后台&lt;/a&gt;&lt;/p&gt;";
    echo "&lt;p style='color: #888; margin-top: 20px;'&gt;⚠️ 提示：安装完成后请删除 install.php 文件以保证安全&lt;/p&gt;";
    echo "&lt;p style='color: #888;'&gt;⚠️ 请编辑 config.php 文件配置您的数据库连接信息（如果不是默认配置）&lt;/p&gt;";
    echo "&lt;p style='color: #888;'&gt;⚠️ 首次使用请先登录管理后台设置 TMDB API Key&lt;/p&gt;";
    
} catch (PDOException $e) {
    echo "&lt;p class='error'&gt;✗ 数据库错误: " . $e-&gt;getMessage() . "&lt;/p&gt;";
    echo "&lt;p style='color:#888;'&gt;请编辑 install.php 文件中的数据库配置信息后重试&lt;/p&gt;";
}

echo "&lt;/body&gt;&lt;/html&gt;";
?&gt;
