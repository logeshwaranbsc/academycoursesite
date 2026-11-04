-- =============================================================================
-- Digital Teenz Academy — Complete PostgreSQL Schema
-- Database: Supabase PostgreSQL
-- =============================================================================
-- HOW TO USE:
--   Option A — Supabase Dashboard:
--     SQL Editor → New Query → Paste entire file → Run
--
--   Option B — psql CLI:
--     psql "postgresql://postgres:[PASSWORD]@[HOST]:5432/postgres" -f schema.sql
--
--   Option C — Laravel migrations (already set up in /backend):
--     php artisan migrate
--
-- This file is the single source of truth for the database structure.
-- Keep it updated whenever you change the schema.
-- =============================================================================

-- -----------------------------------------------------------------------------
-- EXTENSIONS
-- -----------------------------------------------------------------------------

CREATE EXTENSION IF NOT EXISTS "pgcrypto";  -- gen_random_uuid(), crypt()


-- =============================================================================
-- DROP TABLES (safe cleanup — uncomment ONLY to fully reset the schema)
-- =============================================================================
-- Order is critical: dependents first, then parents.

/*
DROP TABLE IF EXISTS enquiries              CASCADE;
DROP TABLE IF EXISTS batches                CASCADE;
DROP TABLE IF EXISTS gallery                CASCADE;
DROP TABLE IF EXISTS culture_videos         CASCADE;
DROP TABLE IF EXISTS testimonials           CASCADE;
DROP TABLE IF EXISTS success_stories        CASCADE;
DROP TABLE IF EXISTS placed_students        CASCADE;
DROP TABLE IF EXISTS courses                CASCADE;
DROP TABLE IF EXISTS site_settings          CASCADE;
DROP TABLE IF EXISTS personal_access_tokens CASCADE;
DROP TABLE IF EXISTS admins                 CASCADE;
DROP FUNCTION IF EXISTS set_updated_at();
*/


-- =============================================================================
-- TABLE: admins
-- Stores admin panel users. Never exposed publicly.
-- =============================================================================

CREATE TABLE IF NOT EXISTS admins (
    id          BIGSERIAL       PRIMARY KEY,
    name        VARCHAR(255)    NOT NULL,
    email       VARCHAR(255)    NOT NULL UNIQUE,
    password    VARCHAR(255)    NOT NULL,          -- bcrypt hashed via Laravel Hash::make()
    created_at  TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

COMMENT ON TABLE  admins          IS 'Admin panel users — password is always bcrypt hashed';
COMMENT ON COLUMN admins.password IS 'Bcrypt hash (cost=12). Never stored or returned as plain text.';


-- =============================================================================
-- TABLE: personal_access_tokens
-- Created by Laravel Sanctum — stores bearer tokens for admin auth.
-- =============================================================================

CREATE TABLE IF NOT EXISTS personal_access_tokens (
    id              BIGSERIAL       PRIMARY KEY,
    tokenable_type  VARCHAR(255)    NOT NULL,           -- 'App\Models\Admin'
    tokenable_id    BIGINT          NOT NULL,
    name            VARCHAR(255)    NOT NULL,           -- 'admin-token'
    token           VARCHAR(64)     NOT NULL UNIQUE,    -- SHA-256 hash of the actual token
    abilities       TEXT            DEFAULT NULL,       -- JSON array of abilities
    last_used_at    TIMESTAMPTZ     DEFAULT NULL,
    expires_at      TIMESTAMPTZ     DEFAULT NULL,
    created_at      TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_pat_tokenable
    ON personal_access_tokens (tokenable_type, tokenable_id);

COMMENT ON TABLE personal_access_tokens IS 'Laravel Sanctum bearer tokens — managed automatically by the framework';


-- =============================================================================
-- TABLE: site_settings
-- Single-row table. Academy-wide configuration.
-- =============================================================================

CREATE TABLE IF NOT EXISTS site_settings (
    id              BIGSERIAL       PRIMARY KEY,
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
    created_at      TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

COMMENT ON TABLE site_settings IS 'Single-row config — edited via admin settings page. Always query id = 1.';


-- =============================================================================
-- TABLE: courses
-- Academy course catalogue. One-to-many with batches.
-- =============================================================================

CREATE TABLE IF NOT EXISTS courses (
    id          BIGSERIAL       PRIMARY KEY,
    title       VARCHAR(255)    NOT NULL,
    description TEXT            NOT NULL,
    image_url   TEXT            DEFAULT NULL,           -- Supabase Storage URL
    duration    VARCHAR(100)    NOT NULL,               -- e.g. "3 Months", "6 Weeks"
    mode        VARCHAR(100)    NOT NULL DEFAULT 'Classroom',
                                                        -- Classroom | Online | Hybrid
    price       VARCHAR(50)     DEFAULT NULL,           -- e.g. "15000" or "10000-15000"
    status      VARCHAR(20)     NOT NULL DEFAULT 'active'
                    CONSTRAINT chk_courses_status CHECK (status IN ('active', 'inactive')),
    created_at  TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_courses_status     ON courses (status);
CREATE INDEX IF NOT EXISTS idx_courses_created_at ON courses (created_at DESC);

COMMENT ON TABLE  courses       IS 'Academy course catalogue';
COMMENT ON COLUMN courses.price IS 'String to support ranges and NULL (enquire-only pricing)';
COMMENT ON COLUMN courses.mode  IS 'Classroom | Online | Hybrid';


-- =============================================================================
-- TABLE: placed_students
-- Students successfully placed through the academy.
-- =============================================================================

CREATE TABLE IF NOT EXISTS placed_students (
    id              BIGSERIAL       PRIMARY KEY,
    student_name    VARCHAR(255)    NOT NULL,
    course          VARCHAR(255)    NOT NULL,
    company         VARCHAR(255)    NOT NULL,
    designation     VARCHAR(255)    NOT NULL,
    image_url       TEXT            DEFAULT NULL,
    description     TEXT            DEFAULT NULL,
    status          VARCHAR(20)     NOT NULL DEFAULT 'active'
                        CONSTRAINT chk_placed_status CHECK (status IN ('active', 'inactive')),
    display_order   INTEGER         NOT NULL DEFAULT 0,
    created_at      TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_placed_status  ON placed_students (status);
CREATE INDEX IF NOT EXISTS idx_placed_order   ON placed_students (display_order ASC);
CREATE INDEX IF NOT EXISTS idx_placed_company ON placed_students (company);

COMMENT ON TABLE placed_students IS 'Placement records — shown in placements section of the website';


-- =============================================================================
-- TABLE: success_stories
-- Longer student transformation narratives.
-- =============================================================================

CREATE TABLE IF NOT EXISTS success_stories (
    id              BIGSERIAL       PRIMARY KEY,
    title           VARCHAR(255)    NOT NULL,
    student_name    VARCHAR(255)    NOT NULL,
    description     TEXT            NOT NULL,
    image_url       TEXT            DEFAULT NULL,
    video_url       TEXT            DEFAULT NULL,       -- YouTube / Vimeo URL
    status          VARCHAR(20)     NOT NULL DEFAULT 'active'
                        CONSTRAINT chk_stories_status CHECK (status IN ('active', 'inactive')),
    display_order   INTEGER         NOT NULL DEFAULT 0,
    created_at      TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_stories_status ON success_stories (status);
CREATE INDEX IF NOT EXISTS idx_stories_order  ON success_stories (display_order ASC);

COMMENT ON TABLE success_stories IS 'Detailed student success narratives with optional video';


-- =============================================================================
-- TABLE: testimonials
-- Short reviews with star ratings.
-- =============================================================================

CREATE TABLE IF NOT EXISTS testimonials (
    id              BIGSERIAL       PRIMARY KEY,
    student_name    VARCHAR(255)    NOT NULL,
    student_image   TEXT            DEFAULT NULL,
    course          VARCHAR(255)    NOT NULL,
    testimonial     TEXT            NOT NULL,
    rating          SMALLINT        NOT NULL DEFAULT 5
                        CONSTRAINT chk_rating CHECK (rating BETWEEN 1 AND 5),
    video_url       TEXT            DEFAULT NULL,
    status          VARCHAR(20)     NOT NULL DEFAULT 'active'
                        CONSTRAINT chk_testimonials_status CHECK (status IN ('active', 'inactive')),
    display_order   INTEGER         NOT NULL DEFAULT 0,
    created_at      TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_testimonials_status ON testimonials (status);
CREATE INDEX IF NOT EXISTS idx_testimonials_order  ON testimonials (display_order ASC);
CREATE INDEX IF NOT EXISTS idx_testimonials_rating ON testimonials (rating DESC);

COMMENT ON TABLE  testimonials        IS 'Student reviews shown in testimonials section';
COMMENT ON COLUMN testimonials.rating IS '1–5 star rating';


-- =============================================================================
-- TABLE: culture_videos
-- Academy life videos (YouTube / Vimeo).
-- =============================================================================

CREATE TABLE IF NOT EXISTS culture_videos (
    id              BIGSERIAL       PRIMARY KEY,
    title           VARCHAR(255)    NOT NULL,
    description     TEXT            DEFAULT NULL,
    video_url       TEXT            NOT NULL,           -- Full YouTube/Vimeo URL
    thumbnail_url   TEXT            DEFAULT NULL,       -- Optional custom thumbnail
    status          VARCHAR(20)     NOT NULL DEFAULT 'active'
                        CONSTRAINT chk_culture_status CHECK (status IN ('active', 'inactive')),
    display_order   INTEGER         NOT NULL DEFAULT 0,
    created_at      TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_culture_status ON culture_videos (status);
CREATE INDEX IF NOT EXISTS idx_culture_order  ON culture_videos (display_order ASC);

COMMENT ON TABLE culture_videos IS 'Academy life videos — workshops, events, student activities';


-- =============================================================================
-- TABLE: gallery
-- Photo gallery with optional category filtering.
-- =============================================================================

CREATE TABLE IF NOT EXISTS gallery (
    id              BIGSERIAL       PRIMARY KEY,
    title           VARCHAR(255)    NOT NULL,
    image_url       TEXT            NOT NULL,           -- Supabase Storage URL
    category        VARCHAR(100)    DEFAULT NULL,       -- Events | Workshops | Graduation | etc.
    status          VARCHAR(20)     NOT NULL DEFAULT 'active'
                        CONSTRAINT chk_gallery_status CHECK (status IN ('active', 'inactive')),
    display_order   INTEGER         NOT NULL DEFAULT 0,
    created_at      TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_gallery_status   ON gallery (status);
CREATE INDEX IF NOT EXISTS idx_gallery_category ON gallery (category);
CREATE INDEX IF NOT EXISTS idx_gallery_order    ON gallery (display_order ASC);

COMMENT ON TABLE  gallery          IS 'Academy photo gallery — filterable by category on public site';
COMMENT ON COLUMN gallery.category IS 'Free text — common values: Events, Workshops, Graduation, Celebrations';


-- =============================================================================
-- TABLE: hero_banners
-- Dynamic multi-image hero banners for homepage slider.
-- =============================================================================

CREATE TABLE IF NOT EXISTS hero_banners (
    id              BIGSERIAL       PRIMARY KEY,
    title           VARCHAR(255)    DEFAULT NULL,
    subtitle        TEXT            DEFAULT NULL,
    image_url       TEXT            NOT NULL,
    button_text     VARCHAR(255)    DEFAULT NULL,
    button_link     VARCHAR(255)    DEFAULT NULL,
    status          VARCHAR(20)     NOT NULL DEFAULT 'active'
                        CONSTRAINT chk_hero_status CHECK (status IN ('active', 'inactive')),
    display_order   INTEGER         NOT NULL DEFAULT 0,
    created_at      TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_hero_status ON hero_banners (status);
CREATE INDEX IF NOT EXISTS idx_hero_order  ON hero_banners (display_order ASC);

COMMENT ON TABLE hero_banners IS 'Homepage hero slider banners — auto rotates every 5s';


-- =============================================================================
-- TABLE: batches
-- Upcoming course batches. FK to courses.
-- =============================================================================

CREATE TABLE IF NOT EXISTS batches (
    id                  BIGSERIAL       PRIMARY KEY,
    course_id           BIGINT          NOT NULL
                            REFERENCES courses (id) ON DELETE CASCADE,
    batch_name          VARCHAR(255)    NOT NULL,       -- e.g. "Batch 14 — Jan 2025"
    start_date          DATE            NOT NULL,
    start_time          TIME            NOT NULL,
    duration            VARCHAR(100)    NOT NULL,       -- e.g. "3 Months"
    location            VARCHAR(255)    NOT NULL,       -- Centre name or "Online"
    available_seats     INTEGER         NOT NULL DEFAULT 30
                            CONSTRAINT chk_seats CHECK (available_seats >= 0),
    status              VARCHAR(20)     NOT NULL DEFAULT 'active'
                            CONSTRAINT chk_batches_status CHECK (status IN ('active', 'inactive')),
    created_at          TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    updated_at          TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_batches_course_id  ON batches (course_id);
CREATE INDEX IF NOT EXISTS idx_batches_start_date ON batches (start_date ASC);
CREATE INDEX IF NOT EXISTS idx_batches_status     ON batches (status);

COMMENT ON TABLE  batches                 IS 'Course batches — public site shows active future batches only';
COMMENT ON COLUMN batches.available_seats IS 'Decremented when student enrolls. 0 = full.';


-- =============================================================================
-- TABLE: enquiries
-- Public form submissions. Status tracks follow-up progress.
-- =============================================================================

CREATE TABLE IF NOT EXISTS enquiries (
    id          BIGSERIAL       PRIMARY KEY,
    name        VARCHAR(255)    NOT NULL,
    email       VARCHAR(255)    DEFAULT NULL,
    phone       VARCHAR(20)     NOT NULL,
    course      VARCHAR(255)    DEFAULT NULL,
    message     TEXT            DEFAULT NULL,
    status      VARCHAR(20)     NOT NULL DEFAULT 'new'
                    CONSTRAINT chk_enquiries_status CHECK (status IN ('new', 'contacted', 'closed')),
    created_at  TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_enquiries_status     ON enquiries (status);
CREATE INDEX IF NOT EXISTS idx_enquiries_created_at ON enquiries (created_at DESC);
CREATE INDEX IF NOT EXISTS idx_enquiries_phone      ON enquiries (phone);

COMMENT ON TABLE  enquiries        IS 'Public enquiry form submissions';
COMMENT ON COLUMN enquiries.status IS 'new → contacted → closed';


-- =============================================================================
-- TRIGGER FUNCTION — auto-set updated_at on every UPDATE
-- =============================================================================

CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER LANGUAGE plpgsql AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$;

COMMENT ON FUNCTION set_updated_at() IS 'Automatically sets updated_at = NOW() on any row update';

-- Apply trigger to all tables in a loop
DO $$
DECLARE
    tbl TEXT;
BEGIN
    FOREACH tbl IN ARRAY ARRAY[
        'admins',
        'personal_access_tokens',
        'site_settings',
        'courses',
        'placed_students',
        'success_stories',
        'testimonials',
        'culture_videos',
        'gallery',
        'hero_banners',
        'batches',
        'enquiries'
    ]
    LOOP
        EXECUTE format(
            'DROP TRIGGER IF EXISTS trg_%1$I_updated_at ON %1$I;
             CREATE TRIGGER trg_%1$I_updated_at
             BEFORE UPDATE ON %1$I
             FOR EACH ROW EXECUTE FUNCTION set_updated_at();',
            tbl
        );
        RAISE NOTICE 'Trigger created for table: %', tbl;
    END LOOP;
END;
$$;


-- =============================================================================
-- SEED DATA
-- =============================================================================

-- Default admin account
-- ⚠ CHANGE PASSWORD IMMEDIATELY after first login via the admin panel.
-- The hash below is bcrypt(cost=12) of "Admin@1234" — generated by Laravel.
-- To regenerate: php artisan tinker → echo Hash::make('YourNewPassword');
-- Or via PostgreSQL pgcrypto: crypt('YourPassword', gen_salt('bf', 12))

INSERT INTO admins (name, email, password)
VALUES (
    'Administrator',
    'admin@gmail.com',
    crypt('Admin@1234', gen_salt('bf', 12))
)
ON CONFLICT (email) DO UPDATE SET password = EXCLUDED.password;

-- Default site settings (id = 1 is always the active row)
INSERT INTO site_settings (
    id,
    academy_name,
    email,
    phone,
    whatsapp,
    address
) VALUES (
    1,
    'Digital Teenz Academy',
    'info@Digital Teenz Academyacademy.com',
    '+91 98765 43210',
    '+91 98765 43210',
    '123, Education Hub, Koramangala, Bangalore – 560 095'
)
ON CONFLICT (id) DO NOTHING;


-- =============================================================================
-- REFERENCE QUERIES
-- Uncomment and run these in Supabase SQL Editor as needed.
-- =============================================================================


-- ── 1. Schema overview — all tables and their row counts ────────────────────
/*
SELECT
    tablename,
    n_live_tup AS row_count
FROM pg_stat_user_tables
WHERE schemaname = 'public'
ORDER BY tablename;
*/


-- ── 2. Dashboard stats — one query for the admin dashboard ──────────────────
/*
SELECT
    (SELECT COUNT(*) FROM courses           WHERE status = 'active')              AS active_courses,
    (SELECT COUNT(*) FROM placed_students   WHERE status = 'active')              AS active_placements,
    (SELECT COUNT(*) FROM testimonials      WHERE status = 'active')              AS active_testimonials,
    (SELECT COUNT(*) FROM culture_videos    WHERE status = 'active')              AS active_videos,
    (SELECT COUNT(*) FROM gallery           WHERE status = 'active')              AS active_gallery,
    (SELECT COUNT(*) FROM enquiries)                                               AS total_enquiries,
    (SELECT COUNT(*) FROM enquiries         WHERE status = 'new')                 AS new_enquiries,
    (SELECT COUNT(*) FROM batches           WHERE status = 'active'
                                              AND start_date >= CURRENT_DATE)     AS upcoming_batches;
*/


-- ── 3. All active courses ────────────────────────────────────────────────────
/*
SELECT id, title, duration, mode, price, status, created_at
FROM   courses
WHERE  status = 'active'
ORDER  BY created_at DESC;
*/


-- ── 4. Upcoming batches with course name ────────────────────────────────────
/*
SELECT
    b.id,
    c.title             AS course,
    b.batch_name,
    b.start_date,
    b.start_time,
    b.duration,
    b.location,
    b.available_seats,
    b.status
FROM   batches b
JOIN   courses c ON c.id = b.course_id
WHERE  b.status = 'active'
  AND  b.start_date >= CURRENT_DATE
ORDER  BY b.start_date ASC;
*/


-- ── 5. Enquiries — new only, most recent first ───────────────────────────────
/*
SELECT id, name, phone, email, course, message, created_at
FROM   enquiries
WHERE  status = 'new'
ORDER  BY created_at DESC;
*/


-- ── 6. Enquiry status breakdown ──────────────────────────────────────────────
/*
SELECT
    status,
    COUNT(*) AS total
FROM   enquiries
GROUP  BY status
ORDER  BY total DESC;
*/


-- ── 7. Placed students — search by company ───────────────────────────────────
/*
SELECT student_name, course, designation, company, status
FROM   placed_students
WHERE  LOWER(company) LIKE LOWER('%infosys%')
ORDER  BY display_order ASC;
*/


-- ── 8. Reorder gallery items (update display_order values) ──────────────────
/*
UPDATE gallery SET display_order = 1 WHERE id = 10;
UPDATE gallery SET display_order = 2 WHERE id =  7;
UPDATE gallery SET display_order = 3 WHERE id = 15;
-- (repeat for all items in the desired order)
*/


-- ── 9. Toggle a course status ────────────────────────────────────────────────
/*
UPDATE courses
SET    status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END
WHERE  id = 1
RETURNING id, title, status;
*/


-- ── 10. Mark enquiry as contacted ───────────────────────────────────────────
/*
UPDATE enquiries
SET    status = 'contacted'
WHERE  id = 42
RETURNING id, name, status, updated_at;
*/


-- ── 11. Reset admin password ─────────────────────────────────────────────────
-- STEP 1: Generate bcrypt hash in Laravel:
--   php artisan tinker
--   echo Hash::make('YourNewSecurePassword123!');
-- STEP 2: Paste the output hash below and run:
/*
UPDATE admins
SET    password = '$2y$12$PASTE_YOUR_NEW_BCRYPT_HASH_HERE'
WHERE  email    = 'admin@Digital Teenz Academyacademy.com'
RETURNING id, name, email, updated_at;
*/


-- ── 12. Delete closed enquiries older than 6 months ─────────────────────────
/*
DELETE FROM enquiries
WHERE  status     = 'closed'
  AND  created_at < NOW() - INTERVAL '6 months';
*/


-- ── 13. Full-text course search ───────────────────────────────────────────────
/*
SELECT id, title, duration, mode
FROM   courses
WHERE  to_tsvector('english', title || ' ' || description)
       @@ plainto_tsquery('english', 'digital marketing');
*/


-- ── 14. Gallery images grouped by category ───────────────────────────────────
/*
SELECT
    COALESCE(category, 'Uncategorised') AS category,
    COUNT(*)                            AS image_count
FROM   gallery
WHERE  status = 'active'
GROUP  BY category
ORDER  BY image_count DESC;
*/


-- ── 15. Decrease available seats when a student enrolls ──────────────────────
/*
UPDATE batches
SET    available_seats = GREATEST(available_seats - 1, 0)
WHERE  id = 5
RETURNING id, batch_name, available_seats;
*/


-- =============================================================================
-- END OF SCHEMA
-- =============================================================================
