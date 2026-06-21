-- ============================================================
-- TravelMate Database Schema
-- Version: 1.0
-- Engine: MySQL 8+
-- Character Set: utf8mb4
-- Collation: utf8mb4_unicode_ci
-- ============================================================

CREATE DATABASE IF NOT EXISTS `travelmate_db`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `travelmate_db`;

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- Table: users
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id`                BIGINT UNSIGNED     AUTO_INCREMENT  PRIMARY KEY,
    `full_name`         VARCHAR(100)        NOT NULL,
    `username`          VARCHAR(50)         NOT NULL UNIQUE,
    `email`             VARCHAR(150)        NOT NULL UNIQUE,
    `password`          VARCHAR(255)        NOT NULL,
    `profile_photo`     VARCHAR(255)        NULL,
    `bio`               TEXT                NULL,
    `location`          VARCHAR(100)        NULL,
    `reliability_score` DECIMAL(5,2)        NOT NULL DEFAULT 100.00,
    `email_verified`    TINYINT(1)          NOT NULL DEFAULT 0,
    `status`            ENUM('active','suspended') NOT NULL DEFAULT 'active',
    `created_at`        TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: trips
-- ============================================================
CREATE TABLE IF NOT EXISTS `trips` (
    `id`                BIGINT UNSIGNED     AUTO_INCREMENT  PRIMARY KEY,
    `creator_id`        BIGINT UNSIGNED     NOT NULL,
    `title`             VARCHAR(255)        NOT NULL,
    `destination`       VARCHAR(255)        NOT NULL,
    `description`       TEXT                NULL,
    `trip_type`         VARCHAR(50)         NULL,
    `cover_image`       VARCHAR(255)        NULL,
    `visibility`        ENUM('public','private') NOT NULL DEFAULT 'public',
    `start_date`        DATE                NOT NULL,
    `end_date`          DATE                NOT NULL,
    `max_participants`  INT                 NOT NULL DEFAULT 2,
    `status`            ENUM('upcoming','ongoing','completed','cancelled') NOT NULL DEFAULT 'upcoming',
    `created_at`        TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT `fk_trips_creator`
        FOREIGN KEY (`creator_id`) REFERENCES `users`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: trip_members
-- ============================================================
CREATE TABLE IF NOT EXISTS `trip_members` (
    `id`            BIGINT UNSIGNED     AUTO_INCREMENT  PRIMARY KEY,
    `trip_id`       BIGINT UNSIGNED     NOT NULL,
    `user_id`       BIGINT UNSIGNED     NOT NULL,
    `role`          ENUM('organizer','co_organizer','participant') NOT NULL DEFAULT 'participant',
    `join_status`   ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `joined_at`     TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY `uq_trip_member` (`trip_id`, `user_id`),

    CONSTRAINT `fk_trip_members_trip`
        FOREIGN KEY (`trip_id`) REFERENCES `trips`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT `fk_trip_members_user`
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: responsibilities
-- ============================================================
CREATE TABLE IF NOT EXISTS `responsibilities` (
    `id`            BIGINT UNSIGNED     AUTO_INCREMENT  PRIMARY KEY,
    `trip_id`       BIGINT UNSIGNED     NOT NULL,
    `assigned_to`   BIGINT UNSIGNED     NULL,
    `created_by`    BIGINT UNSIGNED     NULL,
    `title`         VARCHAR(255)        NOT NULL,
    `description`   TEXT                NULL,
    `status`        ENUM('pending','in_progress','completed') NOT NULL DEFAULT 'pending',
    `due_date`      DATE                NULL,
    `created_at`    TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT `fk_responsibilities_trip`
        FOREIGN KEY (`trip_id`) REFERENCES `trips`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT `fk_responsibilities_user`
        FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`)
        ON DELETE SET NULL ON UPDATE CASCADE,

    CONSTRAINT `fk_responsibilities_creator`
        FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: resources
-- ============================================================
CREATE TABLE IF NOT EXISTS `resources` (
    `id`                BIGINT UNSIGNED     AUTO_INCREMENT  PRIMARY KEY,
    `trip_id`           BIGINT UNSIGNED     NOT NULL,
    `resource_name`     VARCHAR(255)        NOT NULL,
    `quantity_required` INT                 NOT NULL DEFAULT 1,
    `quantity_assigned` INT                 NOT NULL DEFAULT 0,
    `created_at`        TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT `fk_resources_trip`
        FOREIGN KEY (`trip_id`) REFERENCES `trips`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: resource_assignments
-- ============================================================
CREATE TABLE IF NOT EXISTS `resource_assignments` (
    `id`            BIGINT UNSIGNED     AUTO_INCREMENT  PRIMARY KEY,
    `resource_id`   BIGINT UNSIGNED     NOT NULL,
    `user_id`       BIGINT UNSIGNED     NOT NULL,
    `quantity`      INT                 NOT NULL DEFAULT 1,
    `assigned_at`   TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY `uq_resource_user` (`resource_id`, `user_id`),

    CONSTRAINT `fk_resource_assign_resource`
        FOREIGN KEY (`resource_id`) REFERENCES `resources`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT `fk_resource_assign_user`
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: expenses
-- ============================================================
CREATE TABLE IF NOT EXISTS `expenses` (
    `id`            BIGINT UNSIGNED     AUTO_INCREMENT  PRIMARY KEY,
    `trip_id`       BIGINT UNSIGNED     NOT NULL,
    `added_by`      BIGINT UNSIGNED     NOT NULL,
    `title`         VARCHAR(255)        NOT NULL,
    `description`   TEXT                NULL,
    `amount`        DECIMAL(10,2)       NOT NULL,
    `expense_date`  DATE                NULL,
    `created_at`    TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT `fk_expenses_trip`
        FOREIGN KEY (`trip_id`) REFERENCES `trips`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT `fk_expenses_user`
        FOREIGN KEY (`added_by`) REFERENCES `users`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: messages
-- ============================================================
CREATE TABLE IF NOT EXISTS `messages` (
    `id`            BIGINT UNSIGNED     AUTO_INCREMENT  PRIMARY KEY,
    `trip_id`       BIGINT UNSIGNED     NOT NULL,
    `user_id`       BIGINT UNSIGNED     NOT NULL,
    `message`       TEXT                NOT NULL,
    `attachment`    VARCHAR(255)        NULL,
    `created_at`    TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT `fk_messages_trip`
        FOREIGN KEY (`trip_id`) REFERENCES `trips`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT `fk_messages_user`
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: albums
-- ============================================================
CREATE TABLE IF NOT EXISTS `albums` (
    `id`            BIGINT UNSIGNED     AUTO_INCREMENT  PRIMARY KEY,
    `trip_id`       BIGINT UNSIGNED     NOT NULL,
    `title`         VARCHAR(255)        NOT NULL,
    `created_at`    TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT `fk_albums_trip`
        FOREIGN KEY (`trip_id`) REFERENCES `trips`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: media
-- ============================================================
CREATE TABLE IF NOT EXISTS `media` (
    `id`            BIGINT UNSIGNED     AUTO_INCREMENT  PRIMARY KEY,
    `album_id`      BIGINT UNSIGNED     NOT NULL,
    `user_id`       BIGINT UNSIGNED     NOT NULL,
    `file_name`     VARCHAR(255)        NOT NULL,
    `file_path`     VARCHAR(255)        NOT NULL,
    `file_type`     ENUM('image','video') NOT NULL,
    `caption`       TEXT                NULL,
    `uploaded_at`   TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT `fk_media_album`
        FOREIGN KEY (`album_id`) REFERENCES `albums`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT `fk_media_user`
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: reviews
-- ============================================================
CREATE TABLE IF NOT EXISTS `reviews` (
    `id`                BIGINT UNSIGNED     AUTO_INCREMENT  PRIMARY KEY,
    `trip_id`           BIGINT UNSIGNED     NOT NULL,
    `reviewer_id`       BIGINT UNSIGNED     NOT NULL,
    `reviewed_user_id`  BIGINT UNSIGNED     NOT NULL,
    `rating`            TINYINT UNSIGNED    NOT NULL,
    `review`            TEXT                NULL,
    `created_at`        TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY `uq_review` (`trip_id`, `reviewer_id`, `reviewed_user_id`),

    CONSTRAINT `chk_rating` CHECK (`rating` BETWEEN 1 AND 5),

    CONSTRAINT `fk_reviews_trip`
        FOREIGN KEY (`trip_id`) REFERENCES `trips`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT `fk_reviews_reviewer`
        FOREIGN KEY (`reviewer_id`) REFERENCES `users`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT `fk_reviews_reviewed`
        FOREIGN KEY (`reviewed_user_id`) REFERENCES `users`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: notifications
-- ============================================================
CREATE TABLE IF NOT EXISTS `notifications` (
    `id`            BIGINT UNSIGNED     AUTO_INCREMENT  PRIMARY KEY,
    `user_id`       BIGINT UNSIGNED     NOT NULL,
    `type`          VARCHAR(50)         NOT NULL DEFAULT 'general',
    `title`         VARCHAR(255)        NOT NULL,
    `message`       TEXT                NOT NULL,
    `link`          VARCHAR(255)        NULL,
    `is_read`       TINYINT(1)          NOT NULL DEFAULT 0,
    `created_at`    TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT `fk_notifications_user`
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Indexes (Performance Optimization)
-- ============================================================
CREATE INDEX `idx_user_email`          ON `users`(`email`);
CREATE INDEX `idx_user_username`       ON `users`(`username`);
CREATE INDEX `idx_trip_destination`    ON `trips`(`destination`);
CREATE INDEX `idx_trip_dates`          ON `trips`(`start_date`, `end_date`);
CREATE INDEX `idx_trip_status`         ON `trips`(`status`);
CREATE INDEX `idx_trip_visibility`     ON `trips`(`visibility`);
CREATE INDEX `idx_trip_creator`        ON `trips`(`creator_id`);
CREATE INDEX `idx_members_trip`        ON `trip_members`(`trip_id`);
CREATE INDEX `idx_members_user`        ON `trip_members`(`user_id`);
CREATE INDEX `idx_members_status`      ON `trip_members`(`join_status`);
CREATE INDEX `idx_resp_trip`           ON `responsibilities`(`trip_id`);
CREATE INDEX `idx_resp_assigned`       ON `responsibilities`(`assigned_to`);
CREATE INDEX `idx_resource_trip`       ON `resources`(`trip_id`);
CREATE INDEX `idx_message_trip`        ON `messages`(`trip_id`);
CREATE INDEX `idx_message_created`     ON `messages`(`created_at`);
CREATE INDEX `idx_expense_trip`        ON `expenses`(`trip_id`);
CREATE INDEX `idx_media_album`         ON `media`(`album_id`);
CREATE INDEX `idx_review_reviewed`     ON `reviews`(`reviewed_user_id`);
CREATE INDEX `idx_review_trip`         ON `reviews`(`trip_id`);
CREATE INDEX `idx_notif_user`          ON `notifications`(`user_id`);
CREATE INDEX `idx_notif_read`          ON `notifications`(`is_read`);

SET FOREIGN_KEY_CHECKS = 1;
