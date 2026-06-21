# API.md

# TravelMate REST API Specification

Version: 1.0

Base URL:

```text
http://localhost/travelmate/api
```

API Format:

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {}
}
```

---

# Authentication

Authentication Method:

```text
PHP Session Authentication
```

Future Version:

```text
JWT Authentication
```

---

# Standard Response Codes

| Code | Meaning |
|--------|----------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 500 | Server Error |

---

# AUTH MODULE

## Register User

### Endpoint

```http
POST /auth/register
```

### Request

```json
{
  "full_name": "John Doe",
  "username": "johndoe",
  "email": "john@example.com",
  "password": "password123",
  "confirm_password": "password123"
}
```

### Response

```json
{
  "success": true,
  "message": "Registration successful"
}
```

---

## Login

### Endpoint

```http
POST /auth/login
```

### Request

```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

### Response

```json
{
  "success": true,
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe"
  }
}
```

---

## Logout

### Endpoint

```http
POST /auth/logout
```

### Response

```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

# USER MODULE

## Get Profile

### Endpoint

```http
GET /users/{id}
```

### Response

```json
{
  "success": true,
  "data": {
    "id": 1,
    "full_name": "John Doe",
    "username": "johndoe",
    "bio": "Adventure traveler"
  }
}
```

---

## Update Profile

### Endpoint

```http
PUT /users/{id}
```

### Request

```json
{
  "full_name": "John Doe",
  "bio": "Backpacker and photographer"
}
```

---

# TRIP MODULE

## Create Trip

### Endpoint

```http
POST /trips
```

### Request

```json
{
  "title": "Dzukou Valley Trek",
  "destination": "Nagaland",
  "description": "Weekend trek",
  "trip_type": "trekking",
  "start_date": "2026-12-12",
  "end_date": "2026-12-15",
  "max_participants": 10
}
```

### Response

```json
{
  "success": true,
  "message": "Trip created successfully"
}
```

---

## Get All Trips

### Endpoint

```http
GET /trips
```

### Query Parameters

```http
?destination=nagaland
?trip_type=trekking
```

---

## Get Single Trip

### Endpoint

```http
GET /trips/{id}
```

---

## Update Trip

### Endpoint

```http
PUT /trips/{id}
```

---

## Delete Trip

### Endpoint

```http
DELETE /trips/{id}
```

Organizer only.

---

# TRIP MEMBERS

## Join Trip

### Endpoint

```http
POST /trips/{id}/join
```

### Response

```json
{
  "success": true,
  "message": "Join request submitted"
}
```

---

## Approve Member

### Endpoint

```http
POST /trips/{id}/approve-member
```

### Request

```json
{
  "user_id": 10
}
```

---

## Reject Member

### Endpoint

```http
POST /trips/{id}/reject-member
```

### Request

```json
{
  "user_id": 10
}
```

---

## Get Trip Members

### Endpoint

```http
GET /trips/{id}/members
```

---

# RESPONSIBILITIES MODULE

## Create Responsibility

### Endpoint

```http
POST /responsibilities
```

### Request

```json
{
  "trip_id": 1,
  "title": "Arrange Taxi",
  "description": "Book taxi from Dimapur"
}
```

---

## Assign Responsibility

### Endpoint

```http
POST /responsibilities/{id}/assign
```

### Request

```json
{
  "user_id": 5
}
```

---

## Complete Responsibility

### Endpoint

```http
POST /responsibilities/{id}/complete
```

---

## List Responsibilities

### Endpoint

```http
GET /trips/{id}/responsibilities
```

---

# RESOURCE MODULE

## Create Resource

### Endpoint

```http
POST /resources
```

### Request

```json
{
  "trip_id": 1,
  "resource_name": "Tent",
  "quantity_required": 3
}
```

---

## Claim Resource

### Endpoint

```http
POST /resources/{id}/claim
```

### Request

```json
{
  "quantity": 1
}
```

---

## Remove Claim

### Endpoint

```http
DELETE /resources/{id}/claim
```

---

## List Resources

### Endpoint

```http
GET /trips/{id}/resources
```

---

# CHAT MODULE

## Send Message

### Endpoint

```http
POST /messages
```

### Request

```json
{
  "trip_id": 1,
  "message": "I can bring two tents."
}
```

---

## Get Messages

### Endpoint

```http
GET /trips/{id}/messages
```

---

# EXPENSE MODULE

## Add Expense

### Endpoint

```http
POST /expenses
```

### Request

```json
{
  "trip_id": 1,
  "title": "Taxi Fare",
  "amount": 4000
}
```

---

## Get Expenses

### Endpoint

```http
GET /trips/{id}/expenses
```

---

## Expense Summary

### Endpoint

```http
GET /trips/{id}/expense-summary
```

### Response

```json
{
  "total_expense": 6000,
  "participants": 6,
  "individual_share": 1000
}
```

---

# ALBUM MODULE

## Create Album

### Endpoint

```http
POST /albums
```

### Request

```json
{
  "trip_id": 1,
  "title": "Dzukou Valley Memories"
}
```

---

## Upload Media

### Endpoint

```http
POST /albums/{id}/upload
```

### Form Data

```text
file
caption
```

---

## Get Album Media

### Endpoint

```http
GET /albums/{id}/media
```

---

## Delete Media

### Endpoint

```http
DELETE /media/{id}
```

Uploader or organizer only.

---

# REVIEWS MODULE

## Submit Review

### Endpoint

```http
POST /reviews
```

### Request

```json
{
  "trip_id": 1,
  "reviewed_user_id": 5,
  "rating": 5,
  "review": "Very responsible traveler."
}
```

---

## Get User Reviews

### Endpoint

```http
GET /users/{id}/reviews
```

---

# NOTIFICATION MODULE

## Get Notifications

### Endpoint

```http
GET /notifications
```

---

## Mark Notification Read

### Endpoint

```http
POST /notifications/{id}/read
```

---

# SEARCH MODULE

## Search Trips

### Endpoint

```http
GET /search/trips
```

### Query Parameters

```http
?destination=meghalaya
&type=camping
&start_date=2026-10-01
```

---

# VALIDATION RULES

## Registration

| Field | Rule |
|---------|---------|
| Full Name | Required |
| Username | Unique |
| Email | Unique |
| Password | Minimum 8 characters |

---

## Trip Creation

| Field | Rule |
|---------|---------|
| Title | Required |
| Destination | Required |
| Start Date | Required |
| End Date | Required |
| Max Participants | Greater than 1 |

---

## Expense

| Field | Rule |
|---------|---------|
| Amount | Must be greater than 0 |

---

## Resource

| Field | Rule |
|---------|---------|
| Quantity Required | Minimum 1 |

---

# Error Response Format

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": [
      "Email already exists"
    ]
  }
}
```

---

# API Versioning Strategy

Current:

```text
/api/
```

Future:

```text
/api/v1/
/api/v2/
```

---

# Security Requirements

- Session-based authentication
- Password hashing
- CSRF protection
- Input sanitization
- Prepared SQL statements
- File upload validation
- Rate limiting (future)

---

# Future APIs

Version 2

- Friend System
- Bookmarks
- Advanced Search

Version 3

- AI Recommendations
- AI Packing Suggestions
- Smart Itineraries

Version 4

- Wallet
- Payments
- Escrow Services