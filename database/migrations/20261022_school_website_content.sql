CREATE TABLE IF NOT EXISTS school_website_posts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    kind VARCHAR(20) NOT NULL,
    title VARCHAR(160) NOT NULL,
    body TEXT NOT NULL,
    image_filename VARCHAR(100) NOT NULL DEFAULT '',
    event_date DATE NULL,
    venue VARCHAR(160) NOT NULL DEFAULT '',
    expires_on DATE NULL,
    published TINYINT(1) NOT NULL DEFAULT 0,
    created_by VARCHAR(64) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX website_public (kind, published, event_date, expires_on)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
