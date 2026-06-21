# ARCHITECTURE.md

# TravelMate System Architecture

Version: 1.0

---

# 1. Architecture Overview

TravelMate follows a modular MVC-inspired architecture using:

- PHP 8+
- MySQL
- HTML5
- CSS3
- JavaScript
- Bootstrap 5

The application is designed to be simple enough for XAMPP development while remaining scalable for future migration to Laravel or other frameworks.

---

# 2. High-Level Architecture

```text
Browser
   │
   ▼
Frontend (HTML/CSS/JS)
   │
   ▼
Controllers
   │
   ▼
Services
   │
   ▼
Models
   │
   ▼
MySQL Database
```

---

# 3. Application Layers

## Presentation Layer

Responsible for:

- UI Rendering
- Forms
- User Interaction
- Validation Feedback

Technologies:

- HTML5
- CSS3
- Bootstrap 5
- JavaScript

---

## Controller Layer

Responsible for:

- Handling Requests
- Validating Input
- Calling Services
- Returning Responses

Examples:

- AuthController
- TripController
- ChatController
- ExpenseController

---

## Service Layer

Responsible for:

- Business Logic
- Data Processing
- Authorization Rules

Examples:

- AuthService
- TripService
- ExpenseService

---

## Data Layer

Responsible for:

- Database Queries
- CRUD Operations

Examples:

- UserModel
- TripModel
- ExpenseModel

---

# 4. Project Structure

```text
travelmate/
│
├── app/
│   │
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── TripController.php
│   │   ├── ChatController.php
│   │   ├── ExpenseController.php
│   │   ├── ResourceController.php
│   │   └── AlbumController.php
│   │
│   ├── models/
│   │   ├── User.php
│   │   ├── Trip.php
│   │   ├── Expense.php
│   │   ├── Resource.php
│   │   ├── Message.php
│   │   └── Album.php
│   │
│   ├── services/
│   │   ├── AuthService.php
│   │   ├── TripService.php
│   │   ├── ExpenseService.php
│   │   ├── ResourceService.php
│   │   └── AlbumService.php
│
├── config/
│   ├── database.php
│   └── app.php
│
├── public/
│   ├── index.php
│   ├── login.php
│   ├── register.php
│   ├── dashboard.php
│   └── assets/
│
├── uploads/
│   ├── profiles/
│   ├── albums/
│   ├── trips/
│   └── attachments/
│
├── routes/
│   └── web.php
│
├── storage/
│   ├── logs/
│   └── backups/
│
├── docs/
│
└── database/
```

---

# 5. Module Architecture

## Authentication Module

Responsibilities:

- Registration
- Login
- Logout
- Password Management

Components:

- AuthController
- AuthService
- User Model

---

## User Module

Responsibilities:

- Profile Management
- Reliability Score
- User Statistics

Components:

- UserController
- UserService
- User Model

---

## Trip Module

Responsibilities:

- Trip Creation
- Trip Updates
- Join Requests
- Member Management

Components:

- TripController
- TripService
- Trip Model

---

## Responsibility Module

Responsibilities:

- Task Creation
- Assignment
- Completion Tracking

Components:

- ResponsibilityController
- ResponsibilityService

---

## Resource Module

Responsibilities:

- Equipment Tracking
- Resource Assignment
- Resource Fulfillment

Components:

- ResourceController
- ResourceService

---

## Expense Module

Responsibilities:

- Expense Recording
- Cost Calculation
- Expense Summary

Components:

- ExpenseController
- ExpenseService

---

## Chat Module

Responsibilities:

- Group Messaging
- Media Sharing

Components:

- ChatController
- Message Model

---

## Album Module

Responsibilities:

- Photo Uploads
- Video Uploads
- Media Display

Components:

- AlbumController
- AlbumService

---

# 6. Request Lifecycle

Example:

User Creates Trip

```text
User
 ↓
Trip Form
 ↓
TripController
 ↓
TripService
 ↓
TripModel
 ↓
Database
 ↓
Success Response
 ↓
Trip Dashboard
```

---

# 7. Authentication Flow

```text
Login Form
 ↓
Validate Input
 ↓
Verify User
 ↓
Verify Password
 ↓
Create Session
 ↓
Redirect Dashboard
```

---

# 8. Authorization Rules

## Guest

Allowed:

- Home Page
- Registration
- Login
- Public Trips

Restricted:

- Dashboard
- Chat
- Create Trip

---

## User

Allowed:

- Join Trips
- Create Trips
- Upload Media

---

## Organizer

Additional Permissions:

- Approve Members
- Edit Trips
- Assign Tasks
- Complete Trips

---

# 9. Session Management

Authentication uses PHP Sessions.

Session Variables:

```php
$_SESSION['user_id']
$_SESSION['user_name']
$_SESSION['role']
```

Session Security:

- Regenerate Session IDs
- Auto Logout on Expiration
- Secure Session Cookies

---

# 10. File Upload Architecture

## Profile Images

Location:

```text
uploads/profiles/
```

---

## Trip Images

Location:

```text
uploads/trips/
```

---

## Album Media

Location:

```text
uploads/albums/
```

---

## Validation

Allowed:

Images:
- jpg
- jpeg
- png
- webp

Videos:
- mp4
- mov
- avi

Maximum Upload Size:

Images:
10 MB

Videos:
100 MB

---

# 11. Database Access Pattern

Use:

```php
PDO
```

Never use:

```php
mysqli_query()
```

Benefits:

- Prepared Statements
- Better Security
- Easier Maintenance

---

# 12. Security Architecture

## Password Security

Use:

```php
password_hash()
password_verify()
```

---

## SQL Injection Prevention

Use:

```php
Prepared Statements
```

Only.

---

## CSRF Protection

All forms must contain:

```php
csrf_token
```

---

## XSS Prevention

All output must use:

```php
htmlspecialchars()
```

---

## File Upload Security

- Validate MIME Type
- Validate Extension
- Rename Files
- Restrict Execution

---

# 13. Logging Architecture

Store logs in:

```text
storage/logs/
```

Types:

- Error Logs
- Authentication Logs
- System Logs

---

# 14. Backup Strategy

Daily Database Backup

Location:

```text
storage/backups/
```

Retention:

- 7 Daily Backups
- 4 Weekly Backups

---

# 15. Scalability Plan

Version 1

- XAMPP
- Monolithic PHP Application

---

Version 2

- VPS Deployment
- Nginx
- Separate Database Server

---

Version 3

- Laravel Migration
- REST API
- Mobile App Support

---

Version 4

- Cloud Storage
- CDN
- Microservices

---

# 16. Deployment Architecture

Development

```text
Local Machine
 ↓
XAMPP
 ↓
MySQL
```

Production

```text
Users
 ↓
Web Server
 ↓
PHP Application
 ↓
MySQL Server
 ↓
File Storage
```

---

# Architecture Principles

1. Keep modules independent.
2. Separate business logic from views.
3. Use PDO for all database access.
4. Validate all user input.
5. Prioritize security before features.
6. Design for future Laravel migration.
7. Maintain clean and reusable code.