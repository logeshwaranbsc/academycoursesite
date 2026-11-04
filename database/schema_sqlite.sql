-- =============================================================================
-- Digital Teenz Academy — SQLite Local File Storage Schema
-- =============================================================================

CREATE TABLE IF NOT EXISTS admins (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    name        VARCHAR(255)    NOT NULL,
    email       VARCHAR(255)    NOT NULL UNIQUE,
    password    VARCHAR(255)    NOT NULL,
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS personal_access_tokens (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    tokenable_type  VARCHAR(255)    NOT NULL,
    tokenable_id    INTEGER         NOT NULL,
    name            VARCHAR(255)    NOT NULL,
    token           VARCHAR(64)     NOT NULL UNIQUE,
    abilities       TEXT            DEFAULT NULL,
    last_used_at    DATETIME        DEFAULT NULL,
    expires_at      DATETIME        DEFAULT NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS site_settings (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    academy_name    VARCHAR(255)    NOT NULL DEFAULT 'Digital Teenz Academy',
    logo_url        TEXT            DEFAULT NULL,
    email           VARCHAR(255)    DEFAULT NULL,
    phone           VARCHAR(30)     DEFAULT NULL,
    whatsapp        VARCHAR(30)     DEFAULT NULL,
    address         TEXT            DEFAULT NULL,
    facebook_url    TEXT            DEFAULT NULL,
    instagram_url   TEXT            DEFAULT NULL,
    linkedin_url    TEXT            DEFAULT NULL,
    youtube_url     TEXT            DEFAULT NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS courses (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    title       VARCHAR(255)    NOT NULL,
    description TEXT            NOT NULL,
    image_url   TEXT            DEFAULT NULL,
    duration    VARCHAR(100)    NOT NULL,
    mode        VARCHAR(100)    NOT NULL DEFAULT 'Classroom',
    price       VARCHAR(50)     DEFAULT NULL,
    status      VARCHAR(20)     NOT NULL DEFAULT 'active',
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS placed_students (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    student_name    VARCHAR(255)    NOT NULL,
    course          VARCHAR(255)    NOT NULL,
    company         VARCHAR(255)    NOT NULL,
    designation     VARCHAR(255)    NOT NULL,
    image_url       TEXT            DEFAULT NULL,
    description     TEXT            DEFAULT NULL,
    status          VARCHAR(20)     NOT NULL DEFAULT 'active',
    display_order   INTEGER         NOT NULL DEFAULT 0,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS success_stories (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    title           VARCHAR(255)    NOT NULL,
    student_name    VARCHAR(255)    NOT NULL,
    description     TEXT            NOT NULL,
    image_url       TEXT            DEFAULT NULL,
    video_url       TEXT            DEFAULT NULL,
    status          VARCHAR(20)     NOT NULL DEFAULT 'active',
    display_order   INTEGER         NOT NULL DEFAULT 0,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS testimonials (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    student_name    VARCHAR(255)    NOT NULL,
    student_image   TEXT            DEFAULT NULL,
    course          VARCHAR(255)    NOT NULL,
    testimonial     TEXT            NOT NULL,
    rating          SMALLINT        NOT NULL DEFAULT 5,
    video_url       TEXT            DEFAULT NULL,
    status          VARCHAR(20)     NOT NULL DEFAULT 'active',
    display_order   INTEGER         NOT NULL DEFAULT 0,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS culture_videos (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    title           VARCHAR(255)    NOT NULL,
    description     TEXT            DEFAULT NULL,
    video_url       TEXT            NOT NULL,
    thumbnail_url   TEXT            DEFAULT NULL,
    status          VARCHAR(20)     NOT NULL DEFAULT 'active',
    display_order   INTEGER         NOT NULL DEFAULT 0,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS gallery (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    title           VARCHAR(255)    NOT NULL,
    image_url       TEXT            NOT NULL,
    category        VARCHAR(100)    DEFAULT NULL,
    status          VARCHAR(20)     NOT NULL DEFAULT 'active',
    display_order   INTEGER         NOT NULL DEFAULT 0,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS hero_banners (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    title           VARCHAR(255)    DEFAULT NULL,
    subtitle        TEXT            DEFAULT NULL,
    image_url       TEXT            NOT NULL,
    button_text     VARCHAR(255)    DEFAULT NULL,
    button_link     VARCHAR(255)    DEFAULT NULL,
    status          VARCHAR(20)     NOT NULL DEFAULT 'active',
    display_order   INTEGER         NOT NULL DEFAULT 0,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS batches (
    id                  INTEGER PRIMARY KEY AUTOINCREMENT,
    course_id           INTEGER         NOT NULL REFERENCES courses (id) ON DELETE CASCADE,
    batch_name          VARCHAR(255)    NOT NULL,
    start_date          DATE            NOT NULL,
    start_time          TIME            NOT NULL,
    duration            VARCHAR(100)    NOT NULL,
    location            VARCHAR(255)    NOT NULL,
    available_seats     INTEGER         NOT NULL DEFAULT 30,
    status              VARCHAR(20)     NOT NULL DEFAULT 'active',
    created_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS enquiries (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    name        VARCHAR(255)    NOT NULL,
    email       VARCHAR(255)    DEFAULT NULL,
    phone       VARCHAR(20)     NOT NULL,
    course      VARCHAR(255)    DEFAULT NULL,
    message     TEXT            DEFAULT NULL,
    status      VARCHAR(20)     NOT NULL DEFAULT 'new',
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- =============================================================================
-- SEED DATA
-- =============================================================================

INSERT OR IGNORE INTO admins (name, email, password)
VALUES ('Administrator', 'admin@Digital Teenz Academyacademy.com', '$2y$10$iJ4T6oVZx3aa3o.Huk9OauoepRwPvRyLK/yw4W1RiF5iZqrDfgIAK');

INSERT OR IGNORE INTO admins (name, email, password)
VALUES ('Administrator', 'admin@gmail.com', '$2y$10$iJ4T6oVZx3aa3o.Huk9OauoepRwPvRyLK/yw4W1RiF5iZqrDfgIAK');

INSERT OR IGNORE INTO site_settings (id, academy_name, email, phone, whatsapp, address)
VALUES (1, 'Digital Teenz Academy', 'info@Digital Teenz Academyacademy.com', '+91 98765 43210', '+91 98765 43210', '123, Education Hub, Koramangala, Bangalore – 560 095');

