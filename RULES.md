# RULES.md

# TravelMate Development Rules & Coding Standards

Version: 1.0

Status: Mandatory

Applies To:

- Developers
- Contributors
- AI Coding Agents (Codex, Claude, Cursor, Windsurf, ChatGPT)

---

# 1. Project Mission

TravelMate is a collaborative travel planning platform.

The platform focuses on:

- Trip creation
- Group coordination
- Responsibility management
- Resource sharing
- Expense tracking
- Shared memories

The platform is NOT:

- A travel agency
- A hotel booking platform
- A flight booking platform
- A payment gateway

---

# 2. Technology Stack

Mandatory Stack

Frontend

- HTML5
- CSS3
- JavaScript
- Bootstrap 5

Backend

- PHP 8+

Database

- MySQL 8+

Development Environment

- XAMPP

---

# 3. Architecture Rules

Use MVC-inspired architecture.

Required Structure:

```text
travelmate/
│
├── app/
│   ├── controllers/
│   ├── services/
│   ├── models/
│
├── config/
│
├── public/
│
├── uploads/
│
├── routes/
│
├── storage/
│
└── database/
```

---

# 4. Separation of Concerns

Controllers

Must:

- Handle requests
- Validate input
- Call services

Must Not:

- Contain SQL queries
- Contain HTML

---

Services

Must:

- Handle business logic

Must Not:

- Render HTML

---

Models

Must:

- Interact with database

Must Not:

- Contain presentation logic

---

Views

Must:

- Render user interface

Must Not:

- Execute database queries

---

# 5. Database Rules

Use:

```php
PDO
```

Never Use:

```php
mysqli_query()
```

All database interactions must use:

```php
Prepared Statements
```

---

# 6. Security Rules

Mandatory Security Measures

### Passwords

Always use:

```php
password_hash()
password_verify()
```

Never store plain text passwords.

---

### SQL Injection

Use PDO prepared statements only.

Example:

```php
$stmt = $pdo->prepare(
    "SELECT * FROM users WHERE email = ?"
);
$stmt->execute([$email]);
```

---

### XSS Protection

All output must use:

```php
htmlspecialchars()
```

Example:

```php
echo htmlspecialchars($user['name']);
```

---

### CSRF Protection

Every form must include:

```php
csrf_token
```

Validation required on submission.

---

### File Upload Security

Validate:

- File type
- MIME type
- File size

Rename uploaded files.

Never trust original filenames.

---

# 7. Coding Standards

Indentation

```text
4 spaces
```

Never use tabs.

---

Maximum Line Length

```text
120 characters
```

---

File Naming

Controllers

```text
TripController.php
UserController.php
```

Models

```text
Trip.php
User.php
```

Services

```text
TripService.php
```

---

Variable Naming

Use:

```php
$tripId
$userName
$expenseAmount
```

Do Not Use:

```php
$trip_id
$username
```

inside PHP variables.

---

Method Naming

Use:

```php
createTrip()
joinTrip()
calculateExpense()
```

Use camelCase.

---

# 8. HTML Rules

Use semantic HTML.

Preferred:

```html
<header>
<nav>
<main>
<section>
<footer>
```

Avoid excessive div nesting.

---

All forms must include:

```html
<label>
```

elements.

---

# 9. CSS Rules

Use:

```text
Bootstrap First
```

Custom CSS only when necessary.

---

Organize CSS:

```css
layout.css
dashboard.css
trip.css
profile.css
```

Avoid inline styles.

Example:

Bad:

```html
<div style="color:red">
```

Good:

```html
<div class="error-message">
```

---

# 10. JavaScript Rules

Use Vanilla JavaScript.

Avoid unnecessary libraries.

Preferred:

```javascript
fetch()
addEventListener()
```

---

Never use inline JavaScript.

Bad:

```html
<button onclick="save()">
```

Good:

```javascript
button.addEventListener('click', save);
```

---

# 11. API Rules

All API responses must follow:

```json
{
  "success": true,
  "message": "",
  "data": {}
}
```

Error format:

```json
{
  "success": false,
  "message": "",
  "errors": {}
}
```

---

# 12. Validation Rules

Validate on:

### Frontend

For better UX.

### Backend

For security.

Backend validation is mandatory.

---

# 13. Error Handling

Use:

```php
try {
}
catch(Exception $e) {
}
```

Log errors.

Do not expose internal errors to users.

Bad:

```php
echo $e->getMessage();
```

Good:

```php
error_log($e->getMessage());
```

---

# 14. Logging Rules

Store logs in:

```text
storage/logs/
```

Log:

- Login failures
- System errors
- Upload failures

---

# 15. Media Upload Rules

Images

Allowed:

- jpg
- jpeg
- png
- webp

Maximum:

```text
10 MB
```

---

Videos

Allowed:

- mp4
- mov
- avi

Maximum:

```text
100 MB
```

---

Storage Locations

```text
uploads/profiles/
uploads/trips/
uploads/albums/
```

---

# 16. Git Rules

Branch Naming

Feature:

```text
feature/create-trip
```

Bug Fix:

```text
fix/login-error
```

Hotfix:

```text
hotfix/upload-validation
```

---

Commit Format

```text
feat: add trip creation module

fix: resolve login validation issue

refactor: improve trip service structure
```

---

# 17. UI Rules

Theme Goals

- Modern
- Clean
- Travel-focused
- Mobile-friendly

---

Primary Pages

- Home
- Login
- Register
- Dashboard
- Trip Details
- Profile

All pages must be responsive.

---

# 18. Performance Rules

Avoid:

```php
SELECT *
```

Prefer:

```php
SELECT id,name,email
```

when possible.

---

Use pagination for:

- Trips
- Messages
- Albums

---

Optimize uploaded images.

---

# 19. Testing Rules

Every feature must be tested for:

- Success cases
- Validation failures
- Unauthorized access
- Edge cases

---

# 20. AI Agent Instructions

When generating code:

1. Follow MVC architecture.
2. Use PDO only.
3. Use prepared statements only.
4. Validate all inputs.
5. Implement CSRF protection.
6. Escape all outputs.
7. Use Bootstrap 5.
8. Write reusable code.
9. Do not duplicate logic.
10. Keep code production-ready.

---

# MVP Priorities

Priority Order

1. Authentication
2. Trip Management
3. Responsibilities
4. Resource Sharing
5. Chat
6. Expense Tracking
7. Shared Albums
8. Reviews

Do not build advanced features before MVP completion.

---

# Definition of Done

A task is complete only if:

- Code is functional.
- Validation exists.
- Security requirements are met.
- Mobile responsive.
- Database migration completed.
- No critical bugs remain.
- Documentation updated.

---

# Final Principle

Build simple.
Build secure.
Build maintainable.

Every feature should help travelers:

- Plan together
- Share responsibilities
- Reduce costs
- Create memories
- Build trust within the community