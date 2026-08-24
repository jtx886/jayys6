&lt;?php if (!defined('IN_SITE')) die('Access denied'); ?&gt;
    &lt;/main&gt;
&lt;/div&gt;

&lt;script&gt;
function showAdminToast(msg, type = 'success') {
    const toast = document.getElementById('adminToast');
    toast.textContent = msg;
    toast.className = 'toast ' + type + ' show';
    setTimeout(() =&gt; toast.classList.remove('show'), 3000);
}

function confirmAction(msg) {
    return confirm(msg);
}
&lt;/script&gt;
&lt;/body&gt;
&lt;/html&gt;
