&lt;?php
define('IN_SITE', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

$error = '';
$success = '';

if (isLoggedIn()) {
    redirect('/');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = '无效的请求';
    } else {
        $action = $_POST['action'] ?? 'login';
        
        if ($action == 'login') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $error = '请填写邮箱和密码';
            } else {
                $user = $db-&gt;fetch("SELECT * FROM users WHERE email = ?", [$email]);
                if ($user &amp;&amp; password_verify($password, $user['password'])) {
                    if ($user['is_banned']) {
                        if ($user['ban_until'] &amp;&amp; strtotime($user['ban_until']) &gt; time()) {
                            $error = '账号已被封禁，解封时间：' . $user['ban_until'] . '，原因：' . $user['ban_reason'];
                        } else {
                            // 自动解封
                            $db-&gt;query("UPDATE users SET is_banned = 0, ban_until = NULL, ban_reason = '' WHERE id = ?", [$user['id']]);
                            $_SESSION['user_id'] = $user['id'];
                            redirect($_SESSION['redirect_after_login'] ?? '/');
                            unset($_SESSION['redirect_after_login']);
                        }
                    } else {
                        $_SESSION['user_id'] = $user['id'];
                        $redirect = $_SESSION['redirect_after_login'] ?? '/';
                        unset($_SESSION['redirect_after_login']);
                        redirect($redirect);
                    }
                } else {
                    $error = '邮箱或密码错误';
                }
            }
        } elseif ($action == 'register') {
            $email = trim($_POST['email'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $code = trim($_POST['code'] ?? '');
            
            if (empty($email) || empty($username) || empty($password) || empty($code)) {
                $error = '请填写所有必填项';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = '请输入有效的邮箱地址';
            } elseif (strlen($username) &lt; 2 || strlen($username) &gt; 20) {
                $error = '用户名长度应在2-20个字符之间';
            } elseif (strlen($password) &lt; 6) {
                $error = '密码长度至少6位';
            } elseif ($password !== $confirm_password) {
                $error = '两次密码输入不一致';
            } else {
                // 检查邮箱是否已注册
                $exists = $db-&gt;fetch("SELECT id FROM users WHERE email = ?", [$email]);
                if ($exists) {
                    $error = '该邮箱已被注册';
                } else {
                    // 检查验证码
                    $verify = $db-&gt;fetch("SELECT * FROM email_verifications WHERE email = ? AND code = ? AND type = 'register' AND expires_at &gt; NOW() ORDER BY created_at DESC LIMIT 1", [$email, $code]);
                    if (!$verify) {
                        $error = '验证码错误或已过期';
                    } else {
                        // 创建用户
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $db-&gt;query("INSERT INTO users (email, username, password) VALUES (?, ?, ?)", [$email, $username, $hashed_password]);
                        // 删除已使用的验证码
                        $db-&gt;query("DELETE FROM email_verifications WHERE email = ? AND type = 'register'", [$email]);
                        $success = '注册成功！请登录';
                    }
                }
            }
        }
    }
}

$msg = $_GET['msg'] ?? '';
if ($msg == 'login_required') {
    $error = '需要登录才可以观看哦，如没有账号请注册！';
}
if ($msg == 'banned') {
    $error = '您的账号已被封禁';
}

$page_title = '登录/注册';
?&gt;
&lt;!DOCTYPE html&gt;
&lt;html lang="zh-CN"&gt;
&lt;head&gt;
    &lt;meta charset="UTF-8"&gt;
    &lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;
    &lt;title&gt;&lt;?php echo $page_title; ?&gt; - &lt;?php echo SITE_NAME; ?&gt;&lt;/title&gt;
    &lt;link rel="stylesheet" href="/assets/css/style.php"&gt;
&lt;/head&gt;
&lt;body class="auth-page"&gt;
    &lt;div class="auth-container"&gt;
        &lt;div class="auth-logo"&gt;
            &lt;h1&gt;JAY&lt;span&gt;影视&lt;/span&gt;&lt;/h1&gt;
        &lt;/div&gt;
        
        &lt;div class="auth-tabs"&gt;
            &lt;div class="auth-tab active" onclick="switchTab('login')"&gt;登录&lt;/div&gt;
            &lt;div class="auth-tab" onclick="switchTab('register')"&gt;注册&lt;/div&gt;
        &lt;/div&gt;
        
        &lt;?php if ($error): ?&gt;
        &lt;div class="alert alert-error"&gt;
            &lt;svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"&gt;&lt;path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/&gt;&lt;/svg&gt;
            &lt;?php echo htmlspecialchars($error); ?&gt;
        &lt;/div&gt;
        &lt;?php endif; ?&gt;
        
        &lt;?php if ($success): ?&gt;
        &lt;div class="alert alert-success"&gt;
            &lt;svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"&gt;&lt;path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/&gt;&lt;/svg&gt;
            &lt;?php echo htmlspecialchars($success); ?&gt;
        &lt;/div&gt;
        &lt;?php endif; ?&gt;
        
        &lt;!-- 登录表单 --&gt;
        &lt;form id="loginForm" method="post"&gt;
            &lt;input type="hidden" name="csrf_token" value="&lt;?php echo generateCSRFToken(); ?&gt;"&gt;
            &lt;input type="hidden" name="action" value="login"&gt;
            
            &lt;div class="form-group"&gt;
                &lt;label class="form-label"&gt;邮箱&lt;/label&gt;
                &lt;input type="email" name="email" class="form-input" placeholder="请输入邮箱" required value="&lt;?php echo htmlspecialchars($_POST['email'] ?? ''); ?&gt;"&gt;
            &lt;/div&gt;
            
            &lt;div class="form-group"&gt;
                &lt;label class="form-label"&gt;密码&lt;/label&gt;
                &lt;input type="password" name="password" class="form-input" placeholder="请输入密码" required&gt;
            &lt;/div&gt;
            
            &lt;button type="submit" class="form-submit"&gt;登录&lt;/button&gt;
        &lt;/form&gt;
        
        &lt;!-- 注册表单 --&gt;
        &lt;form id="registerForm" method="post" style="display:none;"&gt;
            &lt;input type="hidden" name="csrf_token" value="&lt;?php echo generateCSRFToken(); ?&gt;"&gt;
            &lt;input type="hidden" name="action" value="register"&gt;
            
            &lt;div class="form-group"&gt;
                &lt;label class="form-label"&gt;邮箱&lt;/label&gt;
                &lt;input type="email" name="email" id="regEmail" class="form-input" placeholder="请输入邮箱" required&gt;
            &lt;/div&gt;
            
            &lt;div class="form-group"&gt;
                &lt;label class="form-label"&gt;用户名&lt;/label&gt;
                &lt;input type="text" name="username" class="form-input" placeholder="请输入用户名" required&gt;
            &lt;/div&gt;
            
            &lt;div class="form-group"&gt;
                &lt;label class="form-label"&gt;验证码&lt;/label&gt;
                &lt;div class="form-row"&gt;
                    &lt;input type="text" name="code" class="form-input" placeholder="请输入验证码" required&gt;
                    &lt;button type="button" class="code-btn" id="sendCodeBtn" onclick="sendCode()"&gt;获取验证码&lt;/button&gt;
                &lt;/div&gt;
            &lt;/div&gt;
            
            &lt;div class="form-group"&gt;
                &lt;label class="form-label"&gt;密码&lt;/label&gt;
                &lt;input type="password" name="password" class="form-input" placeholder="请输入密码（至少6位）" required&gt;
            &lt;/div&gt;
            
            &lt;div class="form-group"&gt;
                &lt;label class="form-label"&gt;确认密码&lt;/label&gt;
                &lt;input type="password" name="confirm_password" class="form-input" placeholder="请再次输入密码" required&gt;
            &lt;/div&gt;
            
            &lt;button type="submit" class="form-submit"&gt;注册&lt;/button&gt;
        &lt;/form&gt;
        
        &lt;div style="text-align: center; margin-top: 20px;"&gt;
            &lt;a href="/" style="color: var(--text-muted); font-size: 14px;"&gt;← 返回首页&lt;/a&gt;
        &lt;/div&gt;
    &lt;/div&gt;

    &lt;script&gt;
    function switchTab(tab) {
        document.querySelectorAll('.auth-tab').forEach(t =&gt; t.classList.remove('active'));
        event.target.classList.add('active');
        
        if (tab === 'login') {
            document.getElementById('loginForm').style.display = 'block';
            document.getElementById('registerForm').style.display = 'none';
        } else {
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('registerForm').style.display = 'block';
        }
    }
    
    let countdown = 0;
    function sendCode() {
        if (countdown &gt; 0) return;
        
        const email = document.getElementById('regEmail').value.trim();
        if (!email || !email.includes('@')) {
            showToast('请先输入正确的邮箱地址', 'error');
            return;
        }
        
        const btn = document.getElementById('sendCodeBtn');
        btn.disabled = true;
        
        fetch('/api/send_code.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'email=' + encodeURIComponent(email) + '&amp;type=register&amp;csrf_token=&lt;?php echo generateCSRFToken(); ?&gt;'
        })
        .then(r =&gt; r.json())
        .then(res =&gt; {
            if (res.success) {
                showToast('验证码已发送，请查收邮箱');
                countdown = 60;
                const timer = setInterval(() =&gt; {
                    countdown--;
                    if (countdown &gt; 0) {
                        btn.textContent = countdown + '秒后重试';
                    } else {
                        clearInterval(timer);
                        btn.textContent = '获取验证码';
                        btn.disabled = false;
                    }
                }, 1000);
            } else {
                showToast(res.msg || '发送失败', 'error');
                btn.disabled = false;
            }
        })
        .catch(() =&gt; {
            showToast('发送失败，请重试', 'error');
            btn.disabled = false;
        });
    }
    
    function showToast(msg, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast ' + type;
        toast.textContent = msg;
        toast.style.transform = 'translateX(0)';
        document.body.appendChild(toast);
        setTimeout(() =&gt; {
            toast.style.transform = 'translateX(120%)';
            setTimeout(() =&gt; toast.remove(), 300);
        }, 3000);
    }
    &lt;/script&gt;
&lt;/body&gt;
&lt;/html&gt;
