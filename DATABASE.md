# DATABASE.md

# TravelMate Database Design

Version: 1.0

Database Engine: MySQL 8+

Character Set: utf8mb4

Collation: utf8mb4_unicode_ci

---

# 1. Database Overview

Database Name:

```sql
travelmate_db
```

Purpose:

Store all application data including:

- Users
- Trips
- Participants
- Responsibilities
- Resources
- Expenses
- Chat Messages
- Albums
- Reviews
- Notifications

---

# 2. Entity Relationship Overview

```text
Users
 │
 ├── Trips
 │
 ├── Trip Members
 │
 ├── Responsibilities
 │
 ├── Expenses
 │
 ├── Messages
 │
 ├── Albums
 │
 └── Reviews

Trips
 │
 ├── Members
 ├── Responsibilities
 ├── Resources
 ├── Expenses
 ├── Messages
 └── Albums
```

---

# 3. Users Table

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_photo VARCHAR(255) NULL,
    bio TEXT NULL,
    location VARCHAR(100) NULL,
    reliability_score DECIMAL(5,2) DEFAULT 100.00,
    email_verified TINYINT(1) DEFAULT 0,
    status ENUM('active','suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

# 4. Trips Table

```sql
CREATE TABLE trips (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    creator_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    destination VARCHAR(255) NOT NULL,
    description TEXT,
    trip_type VARCHAR(50),
    visibility ENUM('public','private') DEFAULT 'public',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    max_participants INT NOT NULL,
    status ENUM('upcoming','ongoing','completed','cancelled')
    DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (creator_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);
```

---

# 5. Trip Members Table

Purpose:

Tracks users participating in trips.

```sql
CREATE TABLE trip_members (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trip_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,

    role ENUM(
        'organizer',
        'co_organizer',
        'participant'
    ) DEFAULT 'participant',

    join_status ENUM(
        'pending',
        'approved',
        'rejected'
    ) DEFAULT 'pending',

    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_trip_member (trip_id,user_id),

    FOREIGN KEY (trip_id)
    REFERENCES trips(id)
    ON DELETE CASCADE,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);
```

---

# 6. Responsibilities Table

```sql
CREATE TABLE responsibilities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trip_id BIGINT UNSIGNED NOT NULL,
    assigned_to BIGINT UNSIGNED NULL,

    title VARCHAR(255) NOT NULL,
    description TEXT,

    status ENUM(
        'pending',
        'in_progress',
        'completed'
    ) DEFAULT 'pending',

    due_date DATE NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (trip_id)
    REFERENCES trips(id)
    ON DELETE CASCADE,

    FOREIGN KEY (assigned_to)
    REFERENCES users(id)
    ON DELETE SET NULL
);
```

---

# 7. Resources Table

Purpose:

Tracks equipment required for trips.

```sql
CREATE TABLE resources (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trip_id BIGINT UNSIGNED NOT NULL,

    resource_name VARCHAR(255) NOT NULL,

    quantity_required INT DEFAULT 1,
    quantity_assigned INT DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (trip_id)
    REFERENCES trips(id)
    ON DELETE CASCADE
);
```

---

# 8. Resource Assignments Table

```sql
CREATE TABLE resource_assignments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    resource_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,

    quantity INT DEFAULT 1,

    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (resource_id)
    REFERENCES resources(id)
    ON DELETE CASCADE,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);
```

---

# 9. Expenses Table

```sql
CREATE TABLE expenses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    trip_id BIGINT UNSIGNED NOT NULL,
    added_by BIGINT UNSIGNED NOT NULL,

    title VARCHAR(255) NOT NULL,
    description TEXT,

    amount DECIMAL(10,2) NOT NULL,

    expense_date DATE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (trip_id)
    REFERENCES trips(id)
    ON DELETE CASCADE,

    FOREIGN KEY (added_by)
    REFERENCES users(id)
    ON DELETE CASCADE
);
```

---

# 10. Messages Table

```sql
CREATE TABLE messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    trip_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,

    message TEXT NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (trip_id)
    REFERENCES trips(id)
    ON DELETE CASCADE,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);
```

---

# 11. Albums Table

```sql
CREATE TABLE albums (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    trip_id BIGINT UNSIGNED NOT NULL,

    title VARCHAR(255) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (trip_id)
    REFERENCES trips(id)
    ON DELETE CASCADE
);
```

---

# 12. Media Table

```sql
CREATE TABLE media (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    album_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,

    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,

    file_type ENUM(
        'image',
        'video'
    ) NOT NULL,

    caption TEXT NULL,

    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (album_id)
    REFERENCES albums(id)
    ON DELETE CASCADE,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);
```

---

# 13. Reviews Table

```sql
CREATE TABLE reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    trip_id BIGINT UNSIGNED NOT NULL,

    reviewer_id BIGINT UNSIGNED NOT NULL,
    reviewed_user_id BIGINT UNSIGNED NOT NULL,

    rating TINYINT NOT NULL,
    review TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (trip_id)
    REFERENCES trips(id)
    ON DELETE CASCADE,

    FOREIGN KEY (reviewer_id)
    REFERENCES users(id)
    ON DELETE CASCADE,

    FOREIGN KEY (reviewed_user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);
```

---

# 14. Notifications Table

```sql
CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id BIGINT UNSIGNED NOT NULL,

    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,

    is_read TINYINT(1) DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);
```

---

# 15. Recommended Indexes

```sql
CREATE INDEX idx_user_email
ON users(email);

CREATE INDEX idx_trip_destination
ON trips(destination);

CREATE INDEX idx_trip_dates
ON trips(start_date,end_date);

CREATE INDEX idx_message_trip
ON messages(trip_id);

CREATE INDEX idx_expense_trip
ON expenses(trip_id);

CREATE INDEX idx_media_album
ON media(album_id);

CREATE INDEX idx_review_user
ON reviews(reviewed_user_id);
```

---

# 16. Reliability Score Logic

Calculated from:

```text
Completed Trips        40%
Task Completion        30%
Average Rating         20%
Report History         10%
```

Formula:

```text
Reliability Score =
(Trip Score × 0.4)
+
(Task Score × 0.3)
+
(Rating Score × 0.2)
+
(Report Score × 0.1)
```

Range:

```text
0 - 100
```

---

# 17. Storage Structure

```text
uploads/
│
├── profiles/
│
├── trips/
│
├── albums/
│   ├── images/
│   └── videos/
│
└── attachments/
```

---

# 18. Future Tables

Version 2:

- friend_requests
- bookmarks
- trip_templates

Version 3:

- ai_itineraries
- recommendations

Version 4:

- wallets
- transactions
- escrow_payments

---

# Database Design Principles

1. Use BIGINT for all primary keys.
2. Enforce foreign key constraints.
3. Use soft-delete in future versions if required.
4. Maintain audit timestamps.
5. Normalize data to reduce redundancy.
6. Optimize with indexes on frequently queried columns.
7. Keep schema compatible with future Laravel migration.