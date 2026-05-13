CREATE DATABASE IF NOT EXISTS `edusearch`;
USE `edusearch`;
SET FOREIGN_KEY_CHECKS=0;


-- Section 1 — Users & Authentication
CREATE TABLE `users` (
  `id`             VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `name`           VARCHAR(255) NOT NULL,
  `email`          VARCHAR(255) UNIQUE NOT NULL,
  `phone`          VARCHAR(20) UNIQUE,
  `password_hash`  VARCHAR(255),
  `role`           ENUM('STUDENT','COLLEGE_ADMIN','SUPER_ADMIN','MODERATOR','DATA_ENTRY')
                   NOT NULL DEFAULT 'STUDENT',
  `email_verified` BOOLEAN NOT NULL DEFAULT FALSE,
  `image_url`      TEXT,
  `created_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id`           VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `user_id`      VARCHAR(36) NOT NULL,
  `token`        VARCHAR(255) UNIQUE NOT NULL,
  `expires_at`   TIMESTAMP NOT NULL,
  `ip_address`   VARCHAR(45),
  `user_agent`   TEXT,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `student_profiles` (
  `user_id`           VARCHAR(36) PRIMARY KEY,
  `stream`            VARCHAR(50),
  `class_10_marks`    DECIMAL(5,2),
  `class_10_board`    VARCHAR(100),
  `class_10_year`     SMALLINT,
  `class_12_marks`    DECIMAL(5,2),
  `class_12_board`    VARCHAR(100),
  `class_12_year`     SMALLINT,
  `class_12_stream`   VARCHAR(50),
  `preferred_cities`  JSON,
  `preferred_states`  JSON,
  `budget_min`        INT,
  `budget_max`        INT,
  `career_goals`      TEXT,
  `counseling_points` INT NOT NULL DEFAULT 0,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `student_exam_scores` (
  `id`          VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `user_id`     VARCHAR(36) NOT NULL,
  `exam_id`     VARCHAR(36) NOT NULL,
  `score`       DECIMAL(10,2),
  `percentile`  DECIMAL(5,2),
  `rank`        INT,
  `year`        SMALLINT NOT NULL,
  `roll_number` VARCHAR(50),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`exam_id`) REFERENCES `exams`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Section 2 — Colleges & Courses
CREATE TABLE `colleges` (
  `id`                  VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `name`                VARCHAR(255) NOT NULL,
  `slug`                VARCHAR(255) UNIQUE NOT NULL,
  `city`                VARCHAR(100) NOT NULL,
  `state`               VARCHAR(100) NOT NULL,
  `latitude`            DECIMAL(10,7),
  `longitude`           DECIMAL(10,7),
  `established_year`    SMALLINT,
  `type`                ENUM('PRIVATE','GOVERNMENT','DEEMED','CENTRAL','AUTONOMOUS') NOT NULL,
  `affiliated_to`       VARCHAR(255),
  `campus_area_acres`   DECIMAL(8,2),
  `naac_grade`          VARCHAR(10),
  `naac_year`           SMALLINT,
  `nirf_rank`           INT,
  `nirf_year`           SMALLINT,
  `total_students`      INT,
  `total_faculty`       INT,
  `gender_type`         VARCHAR(20),
  `residential_type`    VARCHAR(30),
  `about_description`   TEXT,
  `admission_process`   TEXT,
  `logo_url`            TEXT,
  `banner_url`          TEXT,
  `brochure_pdf_url`    TEXT,
  `video_tour_url`      TEXT,
  `is_verified`         BOOLEAN NOT NULL DEFAULT FALSE,
  `is_sponsored`        BOOLEAN NOT NULL DEFAULT FALSE,
  `is_featured`         BOOLEAN NOT NULL DEFAULT FALSE,
  `data_quality_score`  TINYINT DEFAULT 0,
  `claimed_by_user_id`  VARCHAR(36),
  `created_at`          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`claimed_by_user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_approvals` (
  `college_id`    VARCHAR(36) NOT NULL,
  `body_name`     VARCHAR(50) NOT NULL,
  `cert_url`      TEXT,
  `approved_year` SMALLINT,
  PRIMARY KEY (`college_id`, `body_name`),
  FOREIGN KEY (`college_id`) REFERENCES `colleges`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_rankings` (
  `id`             VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `college_id`     VARCHAR(36) NOT NULL,
  `ranking_agency` ENUM('NIRF','THE_WEEK','OUTLOOK','INDIA_TODAY','QS','EDUSEARCH'),
  `category`       VARCHAR(100),
  `year`           SMALLINT NOT NULL,
  `rank`           INT NOT NULL,
  FOREIGN KEY (`college_id`) REFERENCES `colleges`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_facilities` (
  `id`            VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `college_id`    VARCHAR(36) NOT NULL,
  `facility_name` VARCHAR(100) NOT NULL,
  `description`   TEXT,
  `icon_name`     VARCHAR(50),
  FOREIGN KEY (`college_id`) REFERENCES `colleges`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_gallery` (
  `id`          VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `college_id`  VARCHAR(36) NOT NULL,
  `image_url`   TEXT NOT NULL,
  `category`    ENUM('CAMPUS','HOSTEL','LABS','EVENTS','CLASSROOMS','SPORTS','CAFETERIA','OTHER')
                NOT NULL DEFAULT 'OTHER',
  `caption`     VARCHAR(255),
  `sort_order`  INT NOT NULL DEFAULT 0,
  `uploaded_by` VARCHAR(36),
  `is_approved` BOOLEAN NOT NULL DEFAULT TRUE,
  FOREIGN KEY (`college_id`) REFERENCES `colleges`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `courses` (
  `id`                  VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `college_id`          VARCHAR(36) NOT NULL,
  `name`                VARCHAR(255) NOT NULL,
  `stream`              VARCHAR(100) NOT NULL,
  `specialization`      VARCHAR(150),
  `degree_level`        ENUM('UG','PG','DIPLOMA','PhD','CERTIFICATE','INTEGRATED') NOT NULL,
  `study_mode`          ENUM('FULL_TIME','PART_TIME','DISTANCE','ONLINE') NOT NULL DEFAULT 'FULL_TIME',
  `duration_years`      DECIMAL(3,1) NOT NULL,
  `total_fees`          INT NOT NULL,
  `first_year_fees`     INT,
  `eligibility_criteria` TEXT,
  `total_seats`         INT,
  `syllabus_pdf_url`    TEXT,
  `course_description`  TEXT,
  `status`              ENUM('ACTIVE','PAUSED','DISCONTINUED') NOT NULL DEFAULT 'ACTIVE',
  `created_at`          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`college_id`) REFERENCES `colleges`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `course_seats` (
  `course_id`   VARCHAR(36) PRIMARY KEY,
  `general`     INT,
  `obc_ncl`     INT,
  `sc`          INT,
  `st`          INT,
  `ews`         INT,
  `pwd`         INT,
  `nri`         INT,
  `management`  INT,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Section 3 — Exams & Cutoffs
CREATE TABLE `exams` (
  `id`               VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `name`             VARCHAR(100) UNIQUE NOT NULL,
  `slug`             VARCHAR(150) UNIQUE NOT NULL,
  `full_name`        VARCHAR(255),
  `conducting_body`  VARCHAR(255),
  `level`            ENUM('NATIONAL','STATE','UNIVERSITY','COLLEGE_LEVEL') NOT NULL,
  `stream`           VARCHAR(100),
  `mode`             VARCHAR(50),
  `official_url`     TEXT,
  `syllabus_pdf_url` TEXT,
  `negative_marking` BOOLEAN NOT NULL DEFAULT FALSE,
  `total_marks`      INT,
  `duration_minutes` INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `exam_sessions` (
  `id`                VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `exam_id`           VARCHAR(36) NOT NULL,
  `session_name`      VARCHAR(100),
  `year`              SMALLINT NOT NULL,
  `application_open`  DATE,
  `application_close` DATE,
  `exam_date`         DATE,
  `admit_card_date`   DATE,
  `result_date`       DATE,
  `counselling_date`  DATE,
  FOREIGN KEY (`exam_id`) REFERENCES `exams`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `course_exams` (
  `course_id`  VARCHAR(36) NOT NULL,
  `exam_id`    VARCHAR(36) NOT NULL,
  PRIMARY KEY (`course_id`, `exam_id`),
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`exam_id`) REFERENCES `exams`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cutoffs` (
  `id`               VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `course_id`        VARCHAR(36) NOT NULL,
  `exam_id`          VARCHAR(36) NOT NULL,
  `year`             SMALLINT NOT NULL,
  `category`         ENUM('GENERAL','OBC-NCL','SC','ST','EWS','PWD','NRI') NOT NULL,
  `quota`            VARCHAR(50),
  `counseling_round` VARCHAR(50),
  `cutoff_type`      ENUM('RANK','SCORE','PERCENTILE','MARKS') NOT NULL,
  `opening_value`    DECIMAL(10,2),
  `closing_value`    DECIMAL(10,2),
  `is_expected`      BOOLEAN NOT NULL DEFAULT FALSE,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`exam_id`) REFERENCES `exams`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `exam_alerts` (
  `id`         VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `user_id`    VARCHAR(36) NOT NULL,
  `exam_id`    VARCHAR(36) NOT NULL,
  `alert_type` ENUM('RESULT','APPLICATION_OPEN','ADMIT_CARD','COUNSELLING') NOT NULL DEFAULT 'RESULT',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_user_exam_alert` (`user_id`, `exam_id`, `alert_type`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`exam_id`) REFERENCES `exams`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Section 4 — Leads & Applications
CREATE TABLE `leads` (
  `id`               VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `student_id`       VARCHAR(36),
  `college_id`       VARCHAR(36) NOT NULL,
  `course_id`        VARCHAR(36),
  `student_name`     VARCHAR(255) NOT NULL,
  `student_phone`    VARCHAR(20) NOT NULL,
  `student_email`    VARCHAR(255) NOT NULL,
  `city`             VARCHAR(100),
  `quality_score`    ENUM('HIGH','MEDIUM','LOW') NOT NULL DEFAULT 'MEDIUM',
  `status`           ENUM('NEW','CONTACTED','CONVERTED','JUNK','INVALID') NOT NULL DEFAULT 'NEW',
  `source_page`      VARCHAR(500),
  `utm_source`       VARCHAR(100),
  `utm_medium`       VARCHAR(100),
  `utm_campaign`     VARCHAR(100),
  `ip_hash`          VARCHAR(64),
  `is_blacklisted`   BOOLEAN NOT NULL DEFAULT FALSE,
  `brevo_sms_sent`   BOOLEAN NOT NULL DEFAULT FALSE,
  `brevo_email_sent` BOOLEAN NOT NULL DEFAULT FALSE,
  `created_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`college_id`) REFERENCES `colleges`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `applications` (
  `id`               VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `student_id`       VARCHAR(36) NOT NULL,
  `college_id`       VARCHAR(36) NOT NULL,
  `course_id`        VARCHAR(36) NOT NULL,
  `status`           ENUM('SUBMITTED','UNDER_REVIEW','SHORTLISTED',
                     'INTERVIEW_SCHEDULED','OFFER_ISSUED','ADMITTED',
                     'WAITLISTED','REJECTED','WITHDRAWN') NOT NULL DEFAULT 'SUBMITTED',
  `payment_status`   ENUM('PENDING','PAID','FAILED','REFUNDED') NOT NULL DEFAULT 'PENDING',
  `payment_id`       VARCHAR(255),
  `application_fee`  INT,
  `documents_json`   JSON,
  `interview_at`     DATETIME,
  `offer_letter_url` TEXT,
  `notes`            TEXT,
  `applied_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`college_id`) REFERENCES `colleges`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Section 5 — Placements & Reviews
CREATE TABLE `placement_stats` (
  `id`                   VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `college_id`           VARCHAR(36) NOT NULL,
  `year`                 SMALLINT NOT NULL,
  `average_package`      DECIMAL(8,2),
  `highest_package`      DECIMAL(8,2),
  `median_package`       DECIMAL(8,2),
  `total_recruiters`     INT,
  `placement_percentage` DECIMAL(5,2),
  `students_placed`      INT,
  `total_eligible`       INT,
  `source_pdf_url`       TEXT,
  `is_self_reported`     BOOLEAN NOT NULL DEFAULT TRUE,
  FOREIGN KEY (`college_id`) REFERENCES `colleges`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `placement_companies` (
  `id`                VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `placement_stat_id` VARCHAR(36) NOT NULL,
  `company_name`      VARCHAR(150) NOT NULL,
  `logo_url`          TEXT,
  `offers_made`       INT,
  `highest_ctc`       DECIMAL(8,2),
  `sector`            VARCHAR(100),
  FOREIGN KEY (`placement_stat_id`) REFERENCES `placement_stats`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `reviews` (
  `id`                            VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `college_id`                    VARCHAR(36) NOT NULL,
  `student_id`                    VARCHAR(36) NOT NULL,
  `course_id`                     VARCHAR(36),
  `academic_rating`               DECIMAL(3,1) CHECK (`academic_rating` BETWEEN 1 AND 10),
  `faculty_rating`                DECIMAL(3,1) CHECK (`faculty_rating` BETWEEN 1 AND 10),
  `infrastructure_rating`         DECIMAL(3,1) CHECK (`infrastructure_rating` BETWEEN 1 AND 10),
  `accommodation_rating`          DECIMAL(3,1) CHECK (`accommodation_rating` BETWEEN 1 AND 10),
  `placement_rating`              DECIMAL(3,1) CHECK (`placement_rating` BETWEEN 1 AND 10),
  `social_life_rating`            DECIMAL(3,1) CHECK (`social_life_rating` BETWEEN 1 AND 10),
  `overall_rating`                DECIMAL(3,2),
  `batch_year`                    SMALLINT NOT NULL,
  `admission_year`                SMALLINT,
  `title`                         VARCHAR(255) NOT NULL,
  `course_curriculum_review`      TEXT,
  `faculty_review`                TEXT,
  `campus_life_review`            TEXT,
  `placement_review`              TEXT,
  `admission_process_review`      TEXT,
  `fees_and_financial_aid_review` TEXT,
  `pros`                          TEXT,
  `cons`                          TEXT,
  `verification_method`  ENUM('COLLEGE_EMAIL_OTP','STUDENT_ID_UPLOAD','ALUMNI_CERT'),
  `verified_at`          TIMESTAMP NULL,
  `verified_evidence_url` TEXT,
  `sentiment_label`      ENUM('POSITIVE','NEUTRAL','NEGATIVE','MIXED'),
  `quality_score`        TINYINT UNSIGNED DEFAULT 0,
  `status`               ENUM('PENDING','APPROVED','REJECTED','ESCALATED') NOT NULL DEFAULT 'PENDING',
  `rejection_reason`     VARCHAR(100),
  `helpful_votes`        INT NOT NULL DEFAULT 0,
  `created_at`           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`college_id`) REFERENCES `colleges`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `review_responses` (
  `id`          VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `review_id`   VARCHAR(36) UNIQUE NOT NULL,
  `college_id`  VARCHAR(36) NOT NULL,
  `response`    TEXT NOT NULL,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`review_id`) REFERENCES `reviews`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`college_id`) REFERENCES `colleges`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Section 6 — AI Counselor, College Q&A, Scholarships
CREATE TABLE `ai_counselor_sessions` (
  `id`         VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `user_id`    VARCHAR(36),
  `channel`    ENUM('WEB','WHATSAPP') NOT NULL DEFAULT 'WEB',
  `started_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ended_at`   TIMESTAMP NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ai_counselor_logs` (
  `id`                      VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `session_id`              VARCHAR(36) NOT NULL,
  `student_id`              VARCHAR(36),
  `turn_number`             TINYINT UNSIGNED NOT NULL,
  `prompt_payload`          JSON,
  `response_text`           TEXT,
  `recommended_college_ids` JSON,
  `feedback_score`          TINYINT,
  `cache_hit`               BOOLEAN NOT NULL DEFAULT FALSE,
  `response_time_ms`        INT,
  `created_at`              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`session_id`) REFERENCES `ai_counselor_sessions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_qa` (
  `id`           VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `college_id`   VARCHAR(36) NOT NULL,
  `asked_by`     VARCHAR(36),
  `question`     TEXT NOT NULL,
  `is_anonymous` BOOLEAN NOT NULL DEFAULT FALSE,
  `status`       ENUM('PENDING','APPROVED','REJECTED') NOT NULL DEFAULT 'PENDING',
  `view_count`   INT NOT NULL DEFAULT 0,
  `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`college_id`) REFERENCES `colleges`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`asked_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_qa_answers` (
  `id`          VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `question_id` VARCHAR(36) NOT NULL,
  `answered_by` VARCHAR(36) NOT NULL,
  `answer`      TEXT NOT NULL,
  `role_badge`  VARCHAR(50),
  `is_official` BOOLEAN NOT NULL DEFAULT FALSE,
  `is_pinned`   BOOLEAN NOT NULL DEFAULT FALSE,
  `upvotes`     INT NOT NULL DEFAULT 0,
  `status`      ENUM('APPROVED','REJECTED') NOT NULL DEFAULT 'APPROVED',
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`question_id`) REFERENCES `college_qa`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`answered_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `scholarships` (
  `id`                   VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `name`                 VARCHAR(255) NOT NULL,
  `provider_name`        VARCHAR(255) NOT NULL,
  `category`             ENUM('GOVERNMENT','PRIVATE','INSTITUTIONAL','INTERNATIONAL') NOT NULL,
  `target_caste_category` VARCHAR(100),
  `state_scope`          VARCHAR(100),
  `income_limit`         INT,
  `merit_percentage_min` DECIMAL(5,2),
  `amount_description`   VARCHAR(255),
  `amount_inr`           INT,
  `about_scholarship`    TEXT,
  `required_documents`   JSON,
  `application_link`     TEXT,
  `deadline`             DATE,
  `status`               ENUM('ACTIVE','EXPIRED','COMING_SOON') NOT NULL DEFAULT 'ACTIVE',
  `created_at`           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Section 7 — Platform Feature Tables
CREATE TABLE `shortlists` (
  `id`          VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `user_id`     VARCHAR(36) NOT NULL,
  `college_id`  VARCHAR(36) NOT NULL,
  `stage`       ENUM('TO_RESEARCH','INTERESTED','APPLIED','DECISION_MADE') NOT NULL DEFAULT 'TO_RESEARCH',
  `notes`       TEXT,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_user_college` (`user_id`, `college_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`college_id`) REFERENCES `colleges`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notifications` (
  `id`         VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `user_id`    VARCHAR(36) NOT NULL,
  `type`       VARCHAR(100) NOT NULL,
  `title`      VARCHAR(255) NOT NULL,
  `body`       TEXT,
  `action_url` VARCHAR(500),
  `channel`    ENUM('IN_APP','EMAIL','SMS','PUSH','WHATSAPP') NOT NULL DEFAULT 'IN_APP',
  `is_read`    BOOLEAN NOT NULL DEFAULT FALSE,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_b2b_accounts` (
  `id`                  VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `college_id`          VARCHAR(36) UNIQUE NOT NULL,
  `contact_email`       VARCHAR(255) NOT NULL,
  `contact_name`        VARCHAR(255),
  `contact_phone`       VARCHAR(20),
  `plan`                ENUM('FREE','GROWTH','PREMIUM','ENTERPRISE') NOT NULL DEFAULT 'FREE',
  `plan_started_at`     TIMESTAMP NULL,
  `plan_expires_at`     TIMESTAMP NULL,
  `cpl_rate`            INT NOT NULL DEFAULT 500,
  `lead_credit_balance` INT NOT NULL DEFAULT 0,
  `monthly_lead_cap`    INT,
  `is_trial`            BOOLEAN NOT NULL DEFAULT FALSE,
  `trial_expires_at`    TIMESTAMP NULL,
  `created_at`          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`college_id`) REFERENCES `colleges`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `b2b_invoices` (
  `id`                  VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `b2b_account_id`      VARCHAR(36) NOT NULL,
  `period_month`        TINYINT NOT NULL,
  `period_year`         SMALLINT NOT NULL,
  `leads_delivered`     INT NOT NULL DEFAULT 0,
  `cpl_rate`            INT NOT NULL,
  `gross_amount`        INT NOT NULL,
  `discount_amount`     INT NOT NULL DEFAULT 0,
  `net_amount`          INT NOT NULL,
  `status`              ENUM('DRAFT','SENT','PAID','OVERDUE','DISPUTED') NOT NULL DEFAULT 'DRAFT',
  `razorpay_payment_id` VARCHAR(255),
  `pdf_url`             TEXT,
  `sent_at`             TIMESTAMP NULL,
  `paid_at`             TIMESTAMP NULL,
  `created_at`          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`b2b_account_id`) REFERENCES `college_b2b_accounts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `admin_audit_log` (
  `id`            VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `admin_user_id` VARCHAR(36) NOT NULL,
  `action`        ENUM('CREATE','UPDATE','DELETE','APPROVE','REJECT',
                  'VERIFY','SUSPEND','RESTORE','LOGIN','PERMISSION_CHANGE') NOT NULL,
  `entity_type`   VARCHAR(100) NOT NULL,
  `entity_id`     VARCHAR(36),
  `old_value`     JSON,
  `new_value`     JSON,
  `ip_address`    VARCHAR(45),
  `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `data_deletion_requests` (
  `id`                  VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `user_id`             VARCHAR(36) NOT NULL,
  `requested_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statutory_deadline`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status`              ENUM('PENDING','IN_PROGRESS','COMPLETED') NOT NULL DEFAULT 'PENDING',
  `processed_by`        VARCHAR(36),
  `processed_at`        TIMESTAMP NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Section 8 — Performance Indexes
CREATE INDEX idx_users_role         ON users(role);
CREATE INDEX idx_sessions_user_id   ON sessions(user_id);
CREATE INDEX idx_sessions_expires   ON sessions(expires_at);
CREATE INDEX idx_exam_scores_user   ON student_exam_scores(user_id);
CREATE INDEX idx_colleges_city_state    ON colleges(city, state);
CREATE INDEX idx_colleges_state         ON colleges(state);
CREATE INDEX idx_colleges_type          ON colleges(type);
CREATE INDEX idx_colleges_naac          ON colleges(naac_grade);
CREATE INDEX idx_colleges_nirf          ON colleges(nirf_rank);
CREATE INDEX idx_colleges_verified      ON colleges(is_verified);
CREATE INDEX idx_colleges_featured      ON colleges(is_featured);
CREATE INDEX idx_colleges_quality       ON colleges(data_quality_score);
CREATE INDEX idx_approvals_college      ON college_approvals(college_id);
CREATE INDEX idx_rankings_college_year  ON college_rankings(college_id, year);
CREATE INDEX idx_gallery_college        ON college_gallery(college_id);
CREATE INDEX idx_courses_college        ON courses(college_id);
CREATE INDEX idx_courses_stream         ON courses(stream);
CREATE INDEX idx_courses_degree         ON courses(degree_level);
CREATE INDEX idx_courses_fees           ON courses(total_fees);
CREATE INDEX idx_courses_status         ON courses(status);
CREATE INDEX idx_exams_level            ON exams(level);
CREATE INDEX idx_exam_sessions_exam_yr  ON exam_sessions(exam_id, year);
CREATE INDEX idx_exam_alerts_user       ON exam_alerts(user_id);
CREATE INDEX idx_exam_alerts_exam       ON exam_alerts(exam_id);
CREATE INDEX idx_cutoffs_course_exam_yr ON cutoffs(course_id, exam_id, year);
CREATE INDEX idx_cutoffs_closing        ON cutoffs(closing_value);
CREATE INDEX idx_leads_college_created  ON leads(college_id, created_at DESC);
CREATE INDEX idx_leads_student          ON leads(student_id);
CREATE INDEX idx_leads_status           ON leads(status);
CREATE INDEX idx_leads_quality          ON leads(quality_score);
CREATE INDEX idx_leads_blacklisted      ON leads(is_blacklisted);
CREATE INDEX idx_apps_student           ON applications(student_id);
CREATE INDEX idx_apps_college           ON applications(college_id);
CREATE INDEX idx_apps_status            ON applications(status);
CREATE INDEX idx_apps_applied_at        ON applications(applied_at DESC);
CREATE INDEX idx_reviews_college_status ON reviews(college_id, status);
CREATE INDEX idx_reviews_student        ON reviews(student_id);
CREATE INDEX idx_reviews_created        ON reviews(created_at DESC);
CREATE INDEX idx_ai_logs_session        ON ai_counselor_logs(session_id);
CREATE INDEX idx_ai_logs_student        ON ai_counselor_logs(student_id);
CREATE INDEX idx_qa_college_status      ON college_qa(college_id, status);
CREATE INDEX idx_qa_answers_question    ON college_qa_answers(question_id);
CREATE INDEX idx_shortlists_user        ON shortlists(user_id);
CREATE INDEX idx_notifs_user_unread     ON notifications(user_id, is_read);
CREATE INDEX idx_b2b_college            ON college_b2b_accounts(college_id);
CREATE INDEX idx_invoices_b2b_status    ON b2b_invoices(b2b_account_id, status);
CREATE INDEX idx_audit_admin_user       ON admin_audit_log(admin_user_id);
CREATE INDEX idx_audit_entity           ON admin_audit_log(entity_type, entity_id);
CREATE INDEX idx_audit_created          ON admin_audit_log(created_at DESC);
CREATE INDEX idx_scholarships_state     ON scholarships(state_scope);
CREATE INDEX idx_scholarships_deadline  ON scholarships(deadline);
CREATE INDEX idx_scholarships_status    ON scholarships(status);

SET FOREIGN_KEY_CHECKS=1;

