&lt;?php
header('Content-Type: text/css');
require_once __DIR__ . '/../../config.php';
?&gt;
/* Jay影视 - 全局样式 */
:root {
    --theme-color: &lt;?php echo THEME_COLOR; ?&gt;;
    --theme-color-dark: &lt;?php echo THEME_COLOR; ?&gt;;
    --bg-primary: #0f0f0f;
    --bg-secondary: #1a1a1a;
    --bg-tertiary: #252525;
    --bg-card: #1e1e1e;
    --text-primary: #ffffff;
    --text-secondary: #b3b3b3;
    --text-muted: #808080;
    --border-color: #2a2a2a;
    --success: #46d369;
    --warning: #e87c03;
    --danger: #e50914;
    --info: #0077b5;
    --shadow-sm: 0 2px 8px rgba(0,0,0,0.3);
    --shadow-md: 0 4px 16px rgba(0,0,0,0.4);
    --shadow-lg: 0 8px 32px rgba(0,0,0,0.5);
    --shadow-xl: 0 12px 48px rgba(0,0,0,0.6);
    --radius-sm: 6px;
    --radius-md: 10px;
    --radius-lg: 16px;
    --radius-xl: 24px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Hiragino Sans GB', 'Microsoft YaHei', sans-serif;
    background: var(--bg-primary);
    color: var(--text-primary);
    line-height: 1.6;
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
}

a {
    color: inherit;
    text-decoration: none;
    transition: var(--transition);
}

button {
    font-family: inherit;
    cursor: pointer;
    border: none;
    outline: none;
    background: none;
}

input, textarea, select {
    font-family: inherit;
    outline: none;
}

img {
    max-width: 100%;
    display: block;
}

/* 滚动条样式 */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: var(--bg-primary);
}

::-webkit-scrollbar-thumb {
    background: var(--bg-tertiary);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--theme-color);
}

/* 导航栏 */
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    background: linear-gradient(to bottom, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.7) 50%, transparent 100%);
    padding: 16px 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: var(--transition);
}

.navbar.scrolled {
    background: var(--bg-primary);
    box-shadow: var(--shadow-md);
    padding: 12px 40px;
}

.nav-brand {
    display: flex;
    align-items: center;
    gap: 12px;
}

.nav-logo {
    font-size: 28px;
    font-weight: 900;
    letter-spacing: 1px;
    background: linear-gradient(135deg, var(--theme-color) 0%, #ff6b6b 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    position: relative;
}

.nav-logo::after {
    content: '影视';
    font-size: 16px;
    font-weight: 600;
    background: linear-gradient(135deg, #ffd700 0%, #ff8c00 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-left: 2px;
}

.nav-menu {
    display: flex;
    align-items: center;
    gap: 4px;
    list-style: none;
}

.nav-menu a {
    padding: 10px 18px;
    border-radius: var(--radius-sm);
    font-size: 15px;
    font-weight: 500;
    color: var(--text-secondary);
    position: relative;
}

.nav-menu a:hover, .nav-menu a.active {
    color: var(--text-primary);
}

.nav-menu a.active::after {
    content: '';
    position: absolute;
    bottom: 2px;
    left: 50%;
    transform: translateX(-50%);
    width: 24px;
    height: 3px;
    background: var(--theme-color);
    border-radius: 2px;
}

.nav-actions {
    display: flex;
    align-items: center;
    gap: 16px;
}

.search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.search-icon {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    cursor: pointer;
    transition: var(--transition);
}

.search-icon:hover {
    background: rgba(255,255,255,0.2);
}

.search-icon svg {
    width: 20px;
    height: 20px;
    fill: var(--text-primary);
}

.search-input {
    position: absolute;
    right: 50px;
    width: 0;
    opacity: 0;
    height: 42px;
    background: var(--bg-tertiary);
    border: 2px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 0 16px;
    color: var(--text-primary);
    font-size: 15px;
    transition: var(--transition);
}

.search-box.active .search-input {
    width: 280px;
    opacity: 1;
    padding: 0 16px 0 44px;
}

.nav-user {
    position: relative;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.nav-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid transparent;
    transition: var(--transition);
}

.nav-user:hover .nav-avatar {
    border-color: var(--theme-color);
}

.nav-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 12px;
    min-width: 200px;
    background: var(--bg-secondary);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-lg);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: var(--transition);
    overflow: hidden;
}

.nav-user:hover .nav-dropdown {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.nav-dropdown a, .nav-dropdown button {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    color: var(--text-secondary);
    font-size: 14px;
    width: 100%;
    text-align: left;
    transition: var(--transition);
}

.nav-dropdown a:hover, .nav-dropdown button:hover {
    background: var(--bg-tertiary);
    color: var(--text-primary);
}

.nav-dropdown svg {
    width: 18px;
    height: 18px;
    fill: currentColor;
}

.admin-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
    border-radius: 4px;
    font-size: 10px;
    font-weight: 900;
    color: white;
    margin-left: 4px;
    box-shadow: 0 2px 8px rgba(255,0,0,0.4);
}

.mobile-menu-btn {
    display: none;
    width: 36px;
    height: 36px;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 5px;
    cursor: pointer;
}

.mobile-menu-btn span {
    width: 24px;
    height: 2px;
    background: var(--text-primary);
    border-radius: 2px;
    transition: var(--transition);
}

/* 主内容区 */
.main-content {
    padding-top: 80px;
    min-height: 100vh;
}

/* Hero区域 */
.hero-section {
    position: relative;
    height: 85vh;
    min-height: 600px;
    display: flex;
    align-items: flex-end;
    padding: 0 40px 80px;
    overflow: hidden;
}

.hero-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-size: cover;
    background-position: center top;
    z-index: -2;
}

.hero-backdrop::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to top, var(--bg-primary) 0%, rgba(15,15,15,0.4) 50%, rgba(15,15,15,0.2) 100%);
}

.hero-backdrop::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to right, rgba(15,15,15,0.9) 0%, rgba(15,15,15,0.3) 60%, transparent 100%);
}

.hero-content {
    max-width: 700px;
    z-index: 1;
    animation: fadeInUp 0.8s ease-out;
}

.hero-title {
    font-size: 56px;
    font-weight: 900;
    margin-bottom: 16px;
    line-height: 1.1;
    text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
}

.hero-meta {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 16px;
    font-size: 16px;
    color: var(--text-secondary);
}

.hero-rating {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--success);
    font-weight: 700;
}

.hero-rating svg {
    width: 20px;
    height: 20px;
    fill: var(--success);
}

.hero-overview {
    font-size: 18px;
    color: var(--text-secondary);
    margin-bottom: 28px;
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.hero-buttons {
    display: flex;
    gap: 16px;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 32px;
    border-radius: var(--radius-md);
    font-size: 16px;
    font-weight: 600;
    transition: var(--transition);
    cursor: pointer;
}

.btn-primary {
    background: var(--theme-color);
    color: white;
}

.btn-primary:hover {
    background: #ff0a16;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(229,9,20,0.4);
}

.btn-secondary {
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    color: white;
}

.btn-secondary:hover {
    background: rgba(255,255,255,0.3);
}

.btn svg {
    width: 22px;
    height: 22px;
    fill: currentColor;
}

/* 影视列表区域 */
.content-section {
    padding: 30px 40px;
}

.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.section-title {
    font-size: 26px;
    font-weight: 700;
    position: relative;
    padding-left: 16px;
}

.section-title::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 28px;
    background: var(--theme-color);
    border-radius: 2px;
}

.media-row {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
}

.media-card {
    position: relative;
    border-radius: var(--radius-md);
    overflow: hidden;
    cursor: pointer;
    transition: var(--transition);
    background: var(--bg-card);
    animation: fadeIn 0.5s ease-out backwards;
}

.media-card:nth-child(n) {
    animation-delay: calc(var(--index, 0) * 0.05s);
}

.media-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: var(--shadow-xl);
    z-index: 10;
}

.media-poster {
    position: relative;
    aspect-ratio: 2/3;
    overflow: hidden;
}

.media-poster img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
}

.media-card:hover .media-poster img {
    transform: scale(1.1);
}

.media-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.2) 50%, transparent 100%);
    opacity: 0;
    transition: var(--transition);
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 16px;
}

.media-card:hover .media-overlay {
    opacity: 1;
}

.media-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    display: flex;
    align-items: center;
    gap: 4px;
    background: rgba(0,0,0,0.7);
    backdrop-filter: blur(10px);
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    color: var(--success);
}

.media-badge svg {
    width: 14px;
    height: 14px;
    fill: currentColor;
}

.media-type-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: var(--theme-color);
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}

.play-btn-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0);
    width: 60px;
    height: 60px;
    background: var(--theme-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
    box-shadow: 0 4px 20px rgba(229,9,20,0.5);
}

.media-card:hover .play-btn-overlay {
    transform: translate(-50%, -50%) scale(1);
}

.play-btn-overlay svg {
    width: 28px;
    height: 28px;
    fill: white;
    margin-left: 4px;
}

.media-info {
    padding: 14px;
}

.media-title {
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.media-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 13px;
    color: var(--text-muted);
}

.media-year {
    color: var(--text-secondary);
}

/* 横向滚动列表 */
.scroll-row {
    position: relative;
}

.scroll-container {
    display: flex;
    gap: 16px;
    overflow-x: auto;
    padding: 10px 0 20px;
    scroll-behavior: smooth;
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scroll-container::-webkit-scrollbar {
    display: none;
}

.scroll-card {
    flex: 0 0 220px;
}

.scroll-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 48px;
    height: 48px;
    background: rgba(0,0,0,0.8);
    backdrop-filter: blur(10px);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    transition: var(--transition);
    opacity: 0;
}

.scroll-row:hover .scroll-arrow {
    opacity: 1;
}

.scroll-arrow:hover {
    background: var(--theme-color);
}

.scroll-arrow.left { left: -20px; }
.scroll-arrow.right { right: -20px; }

.scroll-arrow svg {
    width: 24px;
    height: 24px;
    fill: white;
}

/* 认证页面 */
.auth-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #0f0f0f 0%, #1a1a2e 50%, #16213e 100%);
    padding: 20px;
    position: relative;
    overflow: hidden;
}

.auth-page::before {
    content: '';
    position: absolute;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(229,9,20,0.15) 0%, transparent 70%);
    top: -200px;
    right: -200px;
    animation: float 8s ease-in-out infinite;
}

.auth-page::after {
    content: '';
    position: absolute;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(229,9,20,0.1) 0%, transparent 70%);
    bottom: -150px;
    left: -150px;
    animation: float 10s ease-in-out infinite reverse;
}

.auth-container {
    width: 100%;
    max-width: 450px;
    background: rgba(26,26,26,0.9);
    backdrop-filter: blur(20px);
    border-radius: var(--radius-xl);
    padding: 48px 40px;
    box-shadow: var(--shadow-xl);
    border: 1px solid rgba(255,255,255,0.1);
    position: relative;
    z-index: 1;
    animation: fadeInUp 0.6s ease-out;
}

.auth-logo {
    text-align: center;
    margin-bottom: 36px;
}

.auth-logo h1 {
    font-size: 42px;
    font-weight: 900;
    background: linear-gradient(135deg, var(--theme-color) 0%, #ff6b6b 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.auth-logo span {
    background: linear-gradient(135deg, #ffd700 0%, #ff8c00 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 24px;
}

.auth-tabs {
    display: flex;
    margin-bottom: 32px;
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
    padding: 4px;
}

.auth-tab {
    flex: 1;
    padding: 12px;
    text-align: center;
    border-radius: var(--radius-sm);
    font-weight: 600;
    color: var(--text-muted);
    transition: var(--transition);
    cursor: pointer;
}

.auth-tab.active {
    background: var(--theme-color);
    color: white;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-secondary);
    margin-bottom: 8px;
}

.form-input {
    width: 100%;
    height: 50px;
    background: var(--bg-tertiary);
    border: 2px solid transparent;
    border-radius: var(--radius-md);
    padding: 0 16px;
    color: var(--text-primary);
    font-size: 15px;
    transition: var(--transition);
}

.form-input:focus {
    border-color: var(--theme-color);
    background: var(--bg-secondary);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 12px;
}

.code-btn {
    height: 50px;
    padding: 0 20px;
    background: var(--bg-tertiary);
    border: 2px solid var(--theme-color);
    border-radius: var(--radius-md);
    color: var(--theme-color);
    font-weight: 600;
    white-space: nowrap;
    transition: var(--transition);
}

.code-btn:hover:not(:disabled) {
    background: var(--theme-color);
    color: white;
}

.code-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.form-submit {
    width: 100%;
    height: 52px;
    background: linear-gradient(135deg, var(--theme-color) 0%, #ff0a16 100%);
    border-radius: var(--radius-md);
    color: white;
    font-size: 16px;
    font-weight: 700;
    margin-top: 12px;
    transition: var(--transition);
    box-shadow: 0 4px 20px rgba(229,9,20,0.3);
}

.form-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(229,9,20,0.5);
}

.alert {
    padding: 14px 18px;
    border-radius: var(--radius-md);
    margin-bottom: 20px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-error {
    background: rgba(229,9,20,0.1);
    border: 1px solid rgba(229,9,20,0.3);
    color: #ff6b6b;
}

.alert-success {
    background: rgba(70,211,105,0.1);
    border: 1px solid rgba(70,211,105,0.3);
    color: var(--success);
}

/* 详情页 */
.detail-page {
    padding-top: 0;
}

.detail-backdrop {
    position: relative;
    height: 70vh;
    min-height: 500px;
    background-size: cover;
    background-position: center top;
}

.detail-backdrop::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 300px;
    background: linear-gradient(to top, var(--bg-primary) 0%, transparent 100%);
}

.detail-backdrop::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
}

.detail-content {
    position: relative;
    margin-top: -200px;
    padding: 0 40px 60px;
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 40px;
    z-index: 10;
}

.detail-poster {
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-xl);
    animation: fadeInLeft 0.6s ease-out;
}

.detail-poster img {
    width: 100%;
    aspect-ratio: 2/3;
    object-fit: cover;
}

.detail-info {
    padding-top: 40px;
    animation: fadeInRight 0.6s ease-out;
}

.detail-title {
    font-size: 42px;
    font-weight: 900;
    margin-bottom: 12px;
    line-height: 1.2;
}

.detail-meta {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.detail-rating {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 20px;
    font-weight: 700;
    color: var(--success);
}

.detail-rating svg {
    width: 24px;
    height: 24px;
    fill: var(--success);
}

.detail-tag {
    padding: 6px 14px;
    background: var(--bg-tertiary);
    border-radius: 20px;
    font-size: 14px;
    color: var(--text-secondary);
}

.detail-genres {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 20px;
}

.genre-tag {
    padding: 6px 16px;
    background: linear-gradient(135deg, rgba(229,9,20,0.2) 0%, rgba(229,9,20,0.1) 100%);
    border: 1px solid rgba(229,9,20,0.3);
    border-radius: 20px;
    font-size: 13px;
    color: var(--theme-color);
}

.detail-overview {
    font-size: 16px;
    color: var(--text-secondary);
    line-height: 1.8;
    margin-bottom: 28px;
}

.detail-actions {
    display: flex;
    gap: 16px;
    margin-bottom: 32px;
    flex-wrap: wrap;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: var(--radius-md);
    font-size: 15px;
    font-weight: 600;
    transition: var(--transition);
}

.action-btn svg {
    width: 20px;
    height: 20px;
    fill: currentColor;
}

.action-btn.primary {
    background: var(--theme-color);
    color: white;
}

.action-btn.primary:hover {
    background: #ff0a16;
    transform: translateY(-2px);
}

.action-btn.secondary {
    background: var(--bg-tertiary);
    color: var(--text-primary);
}

.action-btn.secondary:hover {
    background: var(--bg-secondary);
}

.action-btn.secondary.active {
    background: var(--theme-color);
    color: white;
}

/* 选集区域 */
.episodes-section {
    padding: 0 40px 60px;
}

.section-subtitle {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 20px;
}

.season-selector {
    display: flex;
    gap: 10px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}

.season-btn {
    padding: 10px 20px;
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    color: var(--text-secondary);
    transition: var(--transition);
}

.season-btn.active, .season-btn:hover {
    background: var(--theme-color);
    color: white;
}

.episodes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
}

.episode-card {
    display: flex;
    gap: 12px;
    padding: 12px;
    background: var(--bg-card);
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: var(--transition);
}

.episode-card:hover {
    background: var(--bg-tertiary);
    transform: translateX(4px);
}

.episode-thumb {
    width: 140px;
    height: 80px;
    border-radius: var(--radius-sm);
    overflow: hidden;
    flex-shrink: 0;
    position: relative;
}

.episode-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.episode-number {
    position: absolute;
    top: 8px;
    left: 8px;
    background: rgba(0,0,0,0.8);
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 700;
}

.episode-info {
    flex: 1;
    min-width: 0;
}

.episode-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.episode-overview {
    font-size: 12px;
    color: var(--text-muted);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* 播放页 */
.player-page {
    min-height: 100vh;
    background: #000;
}

.player-container {
    position: relative;
    width: 100%;
    height: 80vh;
    min-height: 500px;
}

.player-iframe {
    width: 100%;
    height: 100%;
    border: none;
}

.player-back {
    position: absolute;
    top: 20px;
    left: 20px;
    z-index: 100;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: rgba(0,0,0,0.7);
    backdrop-filter: blur(10px);
    border-radius: var(--radius-md);
    color: white;
    font-weight: 600;
    transition: var(--transition);
}

.player-back:hover {
    background: var(--theme-color);
}

/* 配音选择 */
.dub-selector {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.dub-btn {
    padding: 10px 24px;
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    color: var(--text-secondary);
    transition: var(--transition);
}

.dub-btn.active, .dub-btn:hover {
    background: var(--theme-color);
    color: white;
}

/* 个人中心 */
.profile-page {
    padding: 40px;
}

.profile-header {
    background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
    border-radius: var(--radius-xl);
    padding: 40px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 30px;
    position: relative;
    overflow: hidden;
}

.profile-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(229,9,20,0.1) 0%, transparent 70%);
}

.profile-avatar {
    position: relative;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid var(--theme-color);
    box-shadow: var(--shadow-lg);
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-upload {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 40px;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    transition: var(--transition);
}

.profile-avatar:hover .avatar-upload {
    opacity: 1;
}

.avatar-upload svg {
    width: 20px;
    height: 20px;
    fill: white;
}

.profile-info h2 {
    font-size: 32px;
    font-weight: 800;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.profile-info p {
    color: var(--text-muted);
    font-size: 15px;
}

.profile-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.stat-card {
    background: var(--bg-card);
    padding: 20px;
    border-radius: var(--radius-md);
    text-align: center;
}

.stat-number {
    font-size: 32px;
    font-weight: 800;
    color: var(--theme-color);
    margin-bottom: 4px;
}

.stat-label {
    font-size: 13px;
    color: var(--text-muted);
}

.profile-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 24px;
    background: var(--bg-secondary);
    padding: 6px;
    border-radius: var(--radius-md);
    width: fit-content;
}

.profile-tab {
    padding: 12px 28px;
    border-radius: var(--radius-sm);
    font-weight: 600;
    color: var(--text-secondary);
    transition: var(--transition);
    cursor: pointer;
}

.profile-tab.active {
    background: var(--theme-color);
    color: white;
}

/* 反馈页面 */
.feedback-page {
    padding: 40px;
    max-width: 1000px;
    margin: 0 auto;
}

.feedback-form-card {
    background: var(--bg-secondary);
    border-radius: var(--radius-xl);
    padding: 30px;
    margin-bottom: 30px;
}

.feedback-form-card h3 {
    font-size: 22px;
    margin-bottom: 20px;
}

.feedback-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.feedback-item {
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    padding: 24px;
    transition: var(--transition);
}

.feedback-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.feedback-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 16px;
}

.feedback-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    object-fit: cover;
}

.feedback-user {
    flex: 1;
}

.feedback-username {
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
}

.feedback-time {
    font-size: 13px;
    color: var(--text-muted);
}

.feedback-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 10px;
}

.feedback-content {
    color: var(--text-secondary);
    line-height: 1.7;
    margin-bottom: 16px;
}

.feedback-actions {
    display: flex;
    align-items: center;
    gap: 20px;
}

.feedback-action {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--text-muted);
    font-size: 14px;
    cursor: pointer;
    transition: var(--transition);
}

.feedback-action:hover, .feedback-action.liked {
    color: var(--theme-color);
}

.feedback-action svg {
    width: 18px;
    height: 18px;
    fill: currentColor;
}

.replies-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--border-color);
}

.reply-item {
    display: flex;
    gap: 12px;
    padding: 16px;
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
    margin-bottom: 12px;
}

.reply-item.admin-reply {
    border-left: 4px solid var(--theme-color);
}

.reply-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}

.reply-content {
    flex: 1;
}

.reply-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
}

.reply-username {
    font-weight: 600;
    font-size: 14px;
}

.reply-badge {
    padding: 2px 8px;
    background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
    border-radius: 4px;
    font-size: 10px;
    font-weight: 900;
    color: white;
}

.reply-time {
    font-size: 12px;
    color: var(--text-muted);
}

.reply-text {
    font-size: 14px;
    color: var(--text-secondary);
    line-height: 1.6;
}

.reply-form {
    display: flex;
    gap: 10px;
    margin-top: 12px;
}

.reply-input {
    flex: 1;
    height: 42px;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 0 14px;
    color: var(--text-primary);
    font-size: 14px;
}

.reply-submit {
    padding: 0 20px;
    background: var(--theme-color);
    border-radius: var(--radius-md);
    color: white;
    font-weight: 600;
}

.expand-replies {
    text-align: center;
    padding: 12px;
    color: var(--theme-color);
    font-weight: 600;
    cursor: pointer;
    font-size: 14px;
}

/* 公告弹窗 */
.announcement-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.8);
    backdrop-filter: blur(8px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    animation: fadeIn 0.3s ease-out;
}

.announcement-content {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border-radius: var(--radius-xl);
    max-width: 500px;
    width: 100%;
    overflow: hidden;
    box-shadow: var(--shadow-xl);
    animation: scaleIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.announcement-header {
    background: linear-gradient(135deg, var(--theme-color) 0%, #b20710 100%);
    padding: 24px 30px;
    text-align: center;
    position: relative;
}

.announcement-icon {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}

.announcement-icon svg {
    width: 32px;
    height: 32px;
    fill: white;
}

.announcement-title {
    font-size: 24px;
    font-weight: 800;
    color: white;
}

.announcement-body {
    padding: 30px;
}

.announcement-text {
    color: var(--text-secondary);
    line-height: 1.8;
    font-size: 15px;
    margin-bottom: 24px;
}

.announcement-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    cursor: pointer;
    font-size: 14px;
    color: var(--text-muted);
}

.announcement-checkbox input {
    width: 18px;
    height: 18px;
    accent-color: var(--theme-color);
}

.announcement-close {
    width: 100%;
    height: 48px;
    background: var(--theme-color);
    border-radius: var(--radius-md);
    color: white;
    font-size: 16px;
    font-weight: 700;
    transition: var(--transition);
}

.announcement-close:hover {
    background: #ff0a16;
}

/* 管理后台 */
.admin-layout {
    display: flex;
    min-height: 100vh;
}

.admin-sidebar {
    width: 260px;
    background: var(--bg-secondary);
    padding: 24px 0;
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    overflow-y: auto;
    border-right: 1px solid var(--border-color);
    transition: var(--transition);
}

.admin-logo {
    padding: 0 24px 24px;
    border-bottom: 1px solid var(--border-color);
    margin-bottom: 16px;
}

.admin-logo h2 {
    font-size: 24px;
    font-weight: 900;
    background: linear-gradient(135deg, var(--theme-color) 0%, #ff6b6b 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.admin-logo span {
    background: linear-gradient(135deg, #ffd700 0%, #ff8c00 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 14px;
}

.admin-nav {
    list-style: none;
    padding: 0 12px;
}

.admin-nav li a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    border-radius: var(--radius-md);
    color: var(--text-secondary);
    font-size: 14px;
    font-weight: 500;
    transition: var(--transition);
    margin-bottom: 4px;
}

.admin-nav li a:hover, .admin-nav li a.active {
    background: var(--theme-color);
    color: white;
}

.admin-nav svg {
    width: 20px;
    height: 20px;
    fill: currentColor;
}

.admin-main {
    flex: 1;
    margin-left: 260px;
    padding: 30px;
}

.admin-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 30px;
}

.admin-header h1 {
    font-size: 28px;
    font-weight: 800;
}

/* 仪表盘 */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.dash-card {
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    padding: 24px;
    position: relative;
    overflow: hidden;
    transition: var(--transition);
}

.dash-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.dash-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    border-radius: 50%;
    opacity: 0.1;
}

.dash-card:nth-child(1)::before { background: var(--theme-color); }
.dash-card:nth-child(2)::before { background: var(--success); }
.dash-card:nth-child(3)::before { background: var(--info); }
.dash-card:nth-child(4)::before { background: var(--warning); }

.dash-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
}

.dash-card:nth-child(1) .dash-icon { background: rgba(229,9,20,0.2); color: var(--theme-color); }
.dash-card:nth-child(2) .dash-icon { background: rgba(70,211,105,0.2); color: var(--success); }
.dash-card:nth-child(3) .dash-icon { background: rgba(0,119,181,0.2); color: var(--info); }
.dash-card:nth-child(4) .dash-icon { background: rgba(232,124,3,0.2); color: var(--warning); }

.dash-icon svg {
    width: 24px;
    height: 24px;
    fill: currentColor;
}

.dash-number {
    font-size: 36px;
    font-weight: 800;
    margin-bottom: 4px;
}

.dash-label {
    color: var(--text-muted);
    font-size: 14px;
}

.dashboard-panels {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 24px;
}

.dash-panel {
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    padding: 24px;
}

.dash-panel h3 {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.dash-panel h3 a {
    font-size: 13px;
    color: var(--theme-color);
    font-weight: 500;
}

.mini-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.mini-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px;
    border-radius: var(--radius-md);
    transition: var(--transition);
}

.mini-item:hover {
    background: var(--bg-tertiary);
}

.mini-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
}

.mini-info {
    flex: 1;
    min-width: 0;
}

.mini-name {
    font-weight: 600;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.mini-meta {
    font-size: 12px;
    color: var(--text-muted);
}

/* 表格 */
.admin-table {
    width: 100%;
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    overflow: hidden;
}

.admin-table table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th, .admin-table td {
    padding: 16px 20px;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}

.admin-table th {
    background: var(--bg-tertiary);
    font-weight: 600;
    font-size: 13px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.admin-table td {
    font-size: 14px;
}

.admin-table tr:hover td {
    background: rgba(255,255,255,0.02);
}

.admin-table tr:last-child td {
    border-bottom: none;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.badge-success { background: rgba(70,211,105,0.2); color: var(--success); }
.badge-danger { background: rgba(229,9,20,0.2); color: var(--danger); }
.badge-warning { background: rgba(232,124,3,0.2); color: var(--warning); }
.badge-info { background: rgba(0,119,181,0.2); color: var(--info); }

.btn-sm {
    padding: 6px 14px;
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 600;
    transition: var(--transition);
    display: inline-block;
}

.btn-sm.btn-danger {
    background: rgba(229,9,20,0.2);
    color: var(--danger);
}

.btn-sm.btn-danger:hover {
    background: var(--danger);
    color: white;
}

.btn-sm.btn-primary {
    background: var(--theme-color);
    color: white;
}

.btn-sm.btn-primary:hover {
    background: #ff0a16;
}

.btn-sm.btn-secondary {
    background: var(--bg-tertiary);
    color: var(--text-primary);
}

/* 表单 */
.admin-card {
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    padding: 30px;
    margin-bottom: 24px;
}

.admin-card h3 {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--border-color);
}

.form-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.color-picker-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
}

.color-picker {
    width: 60px;
    height: 50px;
    border: none;
    border-radius: var(--radius-md);
    cursor: pointer;
    background: none;
}

/* 模态框 */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.8);
    backdrop-filter: blur(8px);
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.modal.active {
    display: flex;
    animation: fadeIn 0.3s ease-out;
}

.modal-content {
    background: var(--bg-secondary);
    border-radius: var(--radius-xl);
    max-width: 500px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    animation: scaleIn 0.3s ease-out;
}

.modal-header {
    padding: 24px 30px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-header h3 {
    font-size: 20px;
    font-weight: 700;
}

.modal-close {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--bg-tertiary);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
}

.modal-close:hover {
    background: var(--danger);
}

.modal-close svg {
    width: 18px;
    height: 18px;
    fill: white;
}

.modal-body {
    padding: 30px;
}

.modal-footer {
    padding: 20px 30px;
    border-top: 1px solid var(--border-color);
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.textarea {
    width: 100%;
    min-height: 120px;
    background: var(--bg-tertiary);
    border: 2px solid transparent;
    border-radius: var(--radius-md);
    padding: 14px 16px;
    color: var(--text-primary);
    font-size: 15px;
    resize: vertical;
    transition: var(--transition);
}

.textarea:focus {
    border-color: var(--theme-color);
    background: var(--bg-primary);
}

select.form-input {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23808080' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    padding-right: 40px;
}

/* Toast提示 */
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 16px 24px;
    border-radius: var(--radius-md);
    font-weight: 600;
    z-index: 10000;
    transform: translateX(120%);
    transition: var(--transition);
    box-shadow: var(--shadow-lg);
}

.toast.show {
    transform: translateX(0);
}

.toast.success { background: var(--success); color: white; }
.toast.error { background: var(--danger); color: white; }

/* 空状态 */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-muted);
}

.empty-state svg {
    width: 80px;
    height: 80px;
    fill: var(--bg-tertiary);
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 20px;
    color: var(--text-secondary);
    margin-bottom: 8px;
}

/* 动画 */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* 加载动画 */
.loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid var(--bg-tertiary);
    border-top-color: var(--theme-color);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin: 40px auto;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* 响应式设计 */
@media (max-width: 1200px) {
    .navbar { padding: 16px 24px; }
    .navbar.scrolled { padding: 12px 24px; }
    .content-section { padding: 30px 24px; }
    .hero-section { padding: 0 24px 80px; }
    .detail-content { padding: 0 24px 60px; gap: 30px; }
    .episodes-section { padding: 0 24px 60px; }
    .profile-page, .feedback-page { padding: 24px; }
    .admin-main { padding: 24px; }
}

@media (max-width: 1024px) {
    .detail-content {
        grid-template-columns: 220px 1fr;
        gap: 24px;
    }
    .detail-title { font-size: 32px; }
    .admin-sidebar { width: 220px; }
    .admin-main { margin-left: 220px; }
    .form-row-2 { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .nav-menu {
        position: fixed;
        top: 0;
        left: -100%;
        bottom: 0;
        width: 280px;
        background: var(--bg-secondary);
        flex-direction: column;
        align-items: stretch;
        padding: 80px 20px 20px;
        gap: 8px;
        transition: var(--transition);
        z-index: 999;
        box-shadow: var(--shadow-xl);
    }
    .nav-menu.active { left: 0; }
    .nav-menu a {
        padding: 14px 20px;
        font-size: 16px;
    }
    .mobile-menu-btn { display: flex; }
    .search-box.active .search-input { width: 200px; }
    .hero-title { font-size: 36px; }
    .hero-section { height: 70vh; min-height: 500px; padding-bottom: 60px; }
    .hero-buttons { gap: 12px; }
    .btn { padding: 12px 24px; font-size: 15px; }
    .media-row { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 12px; }
    .detail-content {
        grid-template-columns: 1fr;
        margin-top: -100px;
    }
    .detail-poster {
        width: 180px;
        margin: 0 auto;
    }
    .detail-info { padding-top: 0; text-align: center; }
    .detail-title { font-size: 28px; }
    .detail-meta, .detail-genres, .detail-actions { justify-content: center; }
    .episodes-grid { grid-template-columns: 1fr; }
    .profile-header {
        flex-direction: column;
        text-align: center;
        padding: 30px 20px;
    }
    .profile-tabs { width: 100%; overflow-x: auto; }
    .admin-sidebar {
        transform: translateX(-100%);
        z-index: 999;
        width: 260px;
    }
    .admin-sidebar.active { transform: translateX(0); }
    .admin-main { margin-left: 0; padding: 20px; }
    .dashboard-grid { grid-template-columns: repeat(2, 1fr); }
    .dashboard-panels { grid-template-columns: 1fr; }
    .admin-table { overflow-x: auto; }
    .section-title { font-size: 20px; }
}

@media (max-width: 480px) {
    .navbar { padding: 12px 16px; }
    .hero-section { padding: 0 16px 40px; }
    .hero-title { font-size: 28px; }
    .hero-overview { font-size: 15px; }
    .content-section { padding: 20px 16px; }
    .media-row { grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .detail-content { padding: 0 16px 40px; }
    .episodes-section { padding: 0 16px 40px; }
    .auth-container { padding: 32px 24px; }
    .auth-logo h1 { font-size: 32px; }
    .profile-page, .feedback-page { padding: 16px; }
    .episode-card { flex-direction: column; }
    .episode-thumb { width: 100%; height: 160px; }
    .dashboard-grid { grid-template-columns: 1fr; }
}
