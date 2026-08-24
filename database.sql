-- Jay影视数据库
CREATE DATABASE IF NOT EXISTS jay_movie DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jay_movie;

-- 用户表
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(500) DEFAULT '',
    is_admin TINYINT(1) DEFAULT 0,
    email_verified TINYINT(1) DEFAULT 0,
    is_banned TINYINT(1) DEFAULT 0,
    ban_until DATETIME DEFAULT NULL,
    ban_reason VARCHAR(255) DEFAULT '',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 邮箱验证码表
CREATE TABLE IF NOT EXISTS email_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    code VARCHAR(10) NOT NULL,
    type VARCHAR(20) NOT NULL DEFAULT 'register',
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 播放源表
CREATE TABLE IF NOT EXISTS sources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    url VARCHAR(500) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    priority INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 收藏表
CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    media_id INT NOT NULL,
    media_type VARCHAR(20) NOT NULL,
    title VARCHAR(255) NOT NULL,
    poster_path VARCHAR(500),
    vote_average DECIMAL(3,1) DEFAULT 0,
    release_date VARCHAR(20),
    poster VARCHAR(500),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, media_id, media_type)
);

-- 观看历史表
CREATE TABLE IF NOT EXISTS watch_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    media_id INT NOT NULL,
    media_type VARCHAR(20) NOT NULL,
    title VARCHAR(255) NOT NULL,
    poster_path VARCHAR(500),
    vote_average DECIMAL(3,1) DEFAULT 0,
    release_date VARCHAR(20),
    poster VARCHAR(500),
    season_number INT DEFAULT NULL,
    episode_number INT DEFAULT NULL,
    season INT DEFAULT NULL,
    episode INT DEFAULT NULL,
    progress_seconds INT DEFAULT 0,
    watch_progress INT DEFAULT 0,
    watched_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_history (user_id, media_id, media_type, season_number, episode_number)
);

-- 公告表
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 公告已读表
CREATE TABLE IF NOT EXISTS announcement_reads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    announcement_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (announcement_id) REFERENCES announcements(id) ON DELETE CASCADE,
    UNIQUE KEY unique_read (user_id, announcement_id)
);

-- 反馈表
CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    likes INT DEFAULT 0,
    status VARCHAR(20) DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 反馈回复表
CREATE TABLE IF NOT EXISTS feedback_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    feedback_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (feedback_id) REFERENCES feedback(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 反馈点赞表
CREATE TABLE IF NOT EXISTS feedback_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    feedback_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (feedback_id) REFERENCES feedback(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_like (feedback_id, user_id)
);

-- 网站设置表
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 邮件通知表
CREATE TABLE IF NOT EXISTS email_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    subject VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    sent_count INT DEFAULT 1,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 头像上传目录
-- 请确保uploads目录存在且可写
