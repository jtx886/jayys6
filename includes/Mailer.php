&lt;?php
class Mailer {
    private $host;
    private $port;
    private $user;
    private $pass;
    private $from;
    private $fromName;
    
    public function __construct() {
        $this-&gt;host = SMTP_HOST;
        $this-&gt;port = SMTP_PORT;
        $this-&gt;user = SMTP_USER;
        $this-&gt;pass = SMTP_PASS;
        $this-&gt;from = SMTP_FROM;
        $this-&gt;fromName = SMTP_FROM_NAME;
    }
    
    public function send($to, $subject, $htmlBody, $altBody = '') {
        // 使用socket方式发送SMTP邮件，兼容所有PHP版本
        $socket = @fsockopen('ssl://' . $this-&gt;host, $this-&gt;port, $errno, $errstr, 30);
        if (!$socket) {
            return $this-&gt;sendMailFallback($to, $subject, $htmlBody);
        }
        
        $this-&gt;server = $socket;
        $this-&gt;getResponse();
        
        // EHLO
        $this-&gt;command("EHLO " . $_SERVER['HTTP_HOST']);
        $this-&gt;command("AUTH LOGIN");
        $this-&gt;command(base64_encode($this-&gt;user));
        $this-&gt;command(base64_encode($this-&gt;pass));
        
        // 发件人
        $this-&gt;command("MAIL FROM:&lt;" . $this-&gt;from . "&gt;");
        $this-&gt;command("RCPT TO:&lt;" . $to . "&gt;");
        $this-&gt;command("DATA");
        
        // 邮件头
        $headers = "From: " . $this-&gt;encodeHeader($this-&gt;fromName) . " &lt;" . $this-&gt;from . "&gt;\r\n";
        $headers .= "To: " . $to . "\r\n";
        $headers .= "Subject: " . $this-&gt;encodeHeader($subject) . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        
        fputs($this-&gt;server, $headers . $htmlBody . "\r\n.\r\n");
        $this-&gt;getResponse();
        $this-&gt;command("QUIT");
        fclose($this-&gt;server);
        
        return true;
    }
    
    private function command($cmd) {
        fputs($this-&gt;server, $cmd . "\r\n");
        return $this-&gt;getResponse();
    }
    
    private function getResponse() {
        $response = '';
        while ($line = fgets($this-&gt;server, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) == ' ') break;
        }
        return $response;
    }
    
    private function encodeHeader($str) {
        return '=?UTF-8?B?' . base64_encode($str) . '?=';
    }
    
    private function sendMailFallback($to, $subject, $body) {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . $this-&gt;fromName . " &lt;" . $this-&gt;from . "&gt;\r\n";
        return @mail($to, $subject, $body, $headers);
    }
    
    // 发送验证码邮件
    public function sendVerificationCode($to, $code) {
        $subject = 'Jay影视 - 邮箱验证码';
        $html = '
        &lt;!DOCTYPE html&gt;
        &lt;html&gt;
        &lt;head&gt;
            &lt;meta charset="UTF-8"&gt;
            &lt;title&gt;邮箱验证码&lt;/title&gt;
            &lt;style&gt;
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 0; padding: 0; background: #141414; }
                .container { max-width: 500px; margin: 40px auto; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(229, 9, 20, 0.3); }
                .header { background: linear-gradient(135deg, #e50914 0%, #b20710 100%); padding: 40px 30px; text-align: center; }
                .logo { font-size: 36px; font-weight: 800; color: white; letter-spacing: 2px; }
                .logo span { color: #ffd700; }
                .content { padding: 40px 30px; text-align: center; }
                .greeting { color: #fff; font-size: 24px; margin-bottom: 20px; }
                .desc { color: #aaa; font-size: 16px; line-height: 1.6; margin-bottom: 30px; }
                .code-box { background: linear-gradient(135deg, rgba(229,9,20,0.1) 0%, rgba(178,7,16,0.1) 100%); border: 2px dashed #e50914; border-radius: 16px; padding: 30px; margin: 30px 0; }
                .code { font-size: 48px; font-weight: 800; color: #e50914; letter-spacing: 12px; text-shadow: 0 0 20px rgba(229,9,20,0.5); }
                .note { color: #666; font-size: 14px; margin-top: 20px; }
                .footer { padding: 20px 30px; text-align: center; border-top: 1px solid rgba(255,255,255,0.1); color: #666; font-size: 12px; }
            &lt;/style&gt;
        &lt;/head&gt;
        &lt;body&gt;
            &lt;div class="container"&gt;
                &lt;div class="header"&gt;
                    &lt;div class="logo"&gt;JAY&lt;span&gt;影视&lt;/span&gt;&lt;/div&gt;
                &lt;/div&gt;
                &lt;div class="content"&gt;
                    &lt;div class="greeting"&gt;欢迎来到 Jay 影视！🎬&lt;/div&gt;
                    &lt;div class="desc"&gt;您的验证码已生成，请在注册页面输入以下验证码完成注册。验证码5分钟内有效。&lt;/div&gt;
                    &lt;div class="code-box"&gt;
                        &lt;div class="code"&gt;' . $code . '&lt;/div&gt;
                    &lt;/div&gt;
                    &lt;div class="note"&gt;如果这不是您的操作，请忽略此邮件。&lt;/div&gt;
                &lt;/div&gt;
                &lt;div class="footer"&gt;
                    © 2024 Jay影视. 尽享观影时光
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/body&gt;
        &lt;/html&gt;';
        return $this-&gt;send($to, $subject, $html);
    }
    
    // 发送封禁通知
    public function sendBanNotice($to, $username, $reason, $banUntil) {
        $subject = 'Jay影视 - 账号封禁通知';
        $html = '
        &lt;!DOCTYPE html&gt;
        &lt;html&gt;
        &lt;head&gt;
            &lt;meta charset="UTF-8"&gt;
            &lt;title&gt;账号封禁通知&lt;/title&gt;
            &lt;style&gt;
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 0; padding: 0; background: #141414; }
                .container { max-width: 500px; margin: 40px auto; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(255, 68, 68, 0.3); }
                .header { background: linear-gradient(135deg, #ff4444 0%, #cc0000 100%); padding: 40px 30px; text-align: center; }
                .icon { font-size: 48px; margin-bottom: 10px; }
                .title { font-size: 28px; font-weight: 700; color: white; }
                .content { padding: 40px 30px; }
                .user-info { background: rgba(255,255,255,0.05); border-radius: 12px; padding: 20px; margin-bottom: 20px; }
                .label { color: #888; font-size: 14px; margin-bottom: 5px; }
                .value { color: #fff; font-size: 16px; font-weight: 600; }
                .reason-box { background: rgba(255,68,68,0.1); border-left: 4px solid #ff4444; border-radius: 0 12px 12px 0; padding: 20px; margin: 20px 0; }
                .reason-title { color: #ff4444; font-weight: 700; margin-bottom: 10px; }
                .reason-text { color: #ddd; line-height: 1.6; }
                .footer { padding: 20px 30px; text-align: center; border-top: 1px solid rgba(255,255,255,0.1); color: #666; font-size: 12px; }
            &lt;/style&gt;
        &lt;/head&gt;
        &lt;body&gt;
            &lt;div class="container"&gt;
                &lt;div class="header"&gt;
                    &lt;div class="icon"&gt;⚠️&lt;/div&gt;
                    &lt;div class="title"&gt;账号封禁通知&lt;/div&gt;
                &lt;/div&gt;
                &lt;div class="content"&gt;
                    &lt;p style="color: #ddd; font-size: 16px; line-height: 1.6; margin-bottom: 20px;"&gt;尊敬的 &lt;strong style="color: #fff;"&gt;' . htmlspecialchars($username) . '&lt;/strong&gt;，您的账号已被封禁。&lt;/p&gt;
                    &lt;div class="user-info"&gt;
                        &lt;div class="label"&gt;封禁原因&lt;/div&gt;
                        &lt;div class="value" style="color: #ff6b6b;"&gt;' . htmlspecialchars($reason) . '&lt;/div&gt;
                    &lt;/div&gt;
                    &lt;div class="user-info"&gt;
                        &lt;div class="label"&gt;解除时间&lt;/div&gt;
                        &lt;div class="value" style="color: #ffd700;"&gt;' . $banUntil . '&lt;/div&gt;
                    &lt;/div&gt;
                    &lt;div class="reason-box"&gt;
                        &lt;div class="reason-title"&gt;温馨提示&lt;/div&gt;
                        &lt;div class="reason-text"&gt;如果您对此封禁有异议，请通过反馈功能联系管理员。封禁期间您将无法登录和使用任何功能。&lt;/div&gt;
                    &lt;/div&gt;
                &lt;/div&gt;
                &lt;div class="footer"&gt;
                    © 2024 Jay影视
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/body&gt;
        &lt;/html&gt;';
        return $this-&gt;send($to, $subject, $html);
    }
    
    // 发送自定义通知
    public function sendNotification($to, $subject, $content) {
        $html = '
        &lt;!DOCTYPE html&gt;
        &lt;html&gt;
        &lt;head&gt;
            &lt;meta charset="UTF-8"&gt;
            &lt;title&gt;' . htmlspecialchars($subject) . '&lt;/title&gt;
            &lt;style&gt;
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 0; padding: 0; background: #141414; }
                .container { max-width: 500px; margin: 40px auto; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(229, 9, 20, 0.2); }
                .header { background: linear-gradient(135deg, #e50914 0%, #b20710 100%); padding: 40px 30px; text-align: center; }
                .logo { font-size: 36px; font-weight: 800; color: white; letter-spacing: 2px; }
                .logo span { color: #ffd700; }
                .content { padding: 40px 30px; color: #ddd; line-height: 1.8; }
                .content h2 { color: #fff; margin-top: 0; }
                .footer { padding: 20px 30px; text-align: center; border-top: 1px solid rgba(255,255,255,0.1); color: #666; font-size: 12px; }
            &lt;/style&gt;
        &lt;/head&gt;
        &lt;body&gt;
            &lt;div class="container"&gt;
                &lt;div class="header"&gt;
                    &lt;div class="logo"&gt;JAY&lt;span&gt;影视&lt;/span&gt;&lt;/div&gt;
                &lt;/div&gt;
                &lt;div class="content"&gt;
                    &lt;h2&gt;' . htmlspecialchars($subject) . '&lt;/h2&gt;
                    ' . nl2br(htmlspecialchars($content)) . '
                &lt;/div&gt;
                &lt;div class="footer"&gt;
                    © 2024 Jay影视. 尽享观影时光
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/body&gt;
        &lt;/html&gt;';
        return $this-&gt;send($to, $subject, $html);
    }
}
?&gt;
