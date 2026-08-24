&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        session_destroy();
    }
}
redirect('/');
?&gt;
