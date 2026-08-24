&lt;?php
// 通用函数

function redirect($url) {
    header("Location: $url");
    exit;
}

function isLoggedIn() {
    global $current_user;
    return $current_user !== null;
}

function isAdmin() {
    global $current_user;
    return $current_user &amp;&amp; $current_user['is_admin'];
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect('/login.php?msg=login_required');
    }
    global $current_user;
    if ($current_user['is_banned']) {
        session_destroy();
        redirect('/login.php?msg=banned');
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        redirect('/');
    }
}

function sanitize($str) {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

function formatTime($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff &lt; 60) return '刚刚';
    if ($diff &lt; 3600) return floor($diff / 60) . '分钟前';
    if ($diff &lt; 86400) return floor($diff / 3600) . '小时前';
    if ($diff &lt; 2592000) return floor($diff / 86400) . '天前';
    return date('Y-m-d', $time);
}

function generateCode($length = 6) {
    return str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

function getAvatar($user) {
    if ($user['avatar']) {
        return $user['avatar'];
    }
    $colors = ['#e50914', '#e87c03', '#1db954', '#0077b5', '#6b46c1', '#d63384'];
    $color = $colors[crc32($user['username']) % count($colors)];
    $letter = strtoupper(mb_substr($user['username'], 0, 1));
    return "data:image/svg+xml," . rawurlencode('&lt;svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"&gt;&lt;rect fill="' . $color . '" width="100" height="100"/&gt;&lt;text x="50" y="55" font-size="45" text-anchor="middle" fill="white" font-family="Arial,sans-serif" font-weight="bold"&gt;' . $letter . '&lt;/text&gt;&lt;/svg&gt;');
}

// JSON响应
function jsonResponse($success, $data = [], $msg = '') {
    header('Content-Type: application/json');
    echo json_encode(['success' =&gt; $success, 'data' =&gt; $data, 'msg' =&gt; $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

// CSRF Token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) &amp;&amp; $token === $_SESSION['csrf_token'];
}

// 获取播放源
function getActiveSources() {
    global $db;
    return $db-&gt;fetchAll("SELECT * FROM sources WHERE is_active = 1 ORDER BY priority DESC");
}

// 搜索影视播放链接
function searchPlayUrl($sourceUrl, $keyword, $type = 'movie') {
    $ch = curl_init();
    $url = $sourceUrl . '?wd=' . urlencode($keyword);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    return $data;
}
?&gt;
