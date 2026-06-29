# TravelMate

## Overview

TravelMate is a collaborative travel planning platform that enables travelers to create trips, find travel companions, assign responsibilities, share resources, track expenses, and preserve memories through shared photo and video albums.

Unlike traditional travel agencies or booking platforms, TravelMate focuses on community-driven trip coordination where participants collaborate to reduce costs and improve travel experiences.

---

## Core Features

### Trip Management
- Create and manage trips
- Browse public trips
- Request to join trips
- Approve or reject participants
- Manage trip details

### Collaboration
- Assign responsibilities to participants
- Shared equipment checklist
- Task tracking
- Group chat

### Cost Sharing
- Track shared expenses
- Calculate individual contributions
- Expense transparency

### Community
- User profiles
- Ratings and reviews
- Reliability score
- Trip history

### Memories
- Shared photo albums
- Shared video albums
- Trip gallery
- Downloadable trip memories

---

## Technology Stack

### Frontend
- HTML5
- CSS3
- JavaScript
- Bootstrap 5

### Backend
- PHP 8+

### Database
- MySQL

### Development Environment
- XAMPP

### Version Control
- Git
- GitHub

---

## Project Structure

```text
travelmate/
│
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│
├── uploads/
│   ├── profiles/
│   ├── trips/
│   └── albums/
│
├── config/
│   └── database.php
│
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── auth.php
│
├── api/
│
├── login.php
├── register.php
├── dashboard.php
├── create-trip.php
├── trip-details.php
├── profile.php
│
└── README.md
```

---

## Installation

### Prerequisites

- XAMPP
- PHP 8+
- MySQL 8+
- Git
- Modern Web Browser

### Clone Repository

```bash
git clone https://github.com/gogoialakesh/TravelMate
```

### Move Project

Place the project inside:

```text
xampp/htdocs/
```

Example:

```text
xampp/htdocs/travelmate
```

### Start Services

Open XAMPP Control Panel and start:

- Apache
- MySQL

### Create Database

Open phpMyAdmin and create:

```sql
travelmate_db
```

### Import Database

Import the SQL schema located in:

```text
database/travelmate.sql
```

### Configure Database

Edit:

```php
config/database.php
```

Example:

```php
$host = "localhost";
$user = "root";
$password = "";
$database = "travelmate_db";
```

### Run Application

Open:

```text
http://localhost/travelmate
```

---

## Development Goals

### Phase 1
- User Authentication
- User Profiles
- Trip Creation
- Trip Joining

### Phase 2
- Responsibilities Board
- Equipment Checklist
- Group Chat

### Phase 3
- Expense Tracking
- Reviews
- Reliability Scores

### Phase 4
- Shared Albums
- Media Management
- Trip Memories

---

## Security

- Password Hashing
- Session Authentication
- CSRF Protection
- Input Validation
- SQL Injection Prevention using Prepared Statements
- File Upload Validation

---

## License

This project is developed for educational and commercial purposes.

All rights reserved.

---

## Vision

TravelMate aims to become the leading platform for collaborative travel planning by helping travelers:

- Travel together
- Share responsibilities
- Share resources
- Reduce costs
- Create lasting memories
