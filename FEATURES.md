# FEATURES.md

# TravelMate Feature Specifications

Version: 1.0

---

# 1. User Authentication

## Description

Allows users to create accounts and securely access the platform.

---

## Registration

### Fields

- Full Name
- Username
- Email Address
- Password
- Confirm Password

### Validation

- Name required
- Username unique
- Email unique
- Password minimum 8 characters
- Password confirmation must match

---

## Login

### Fields

- Email
- Password

### Validation

- Correct credentials required

---

## Logout

- Destroy active session
- Redirect to homepage

---

# 2. User Profile

## Description

Each user has a public profile visible to other travelers.

---

## Profile Information

### Basic Information

- Profile Picture
- Full Name
- Username
- Bio
- Location

### Travel Information

- Trips Created
- Trips Joined
- Trips Completed
- Reliability Score

---

## Actions

Users can:

- Edit Profile
- Upload Profile Picture
- Change Password

---

# 3. Trip Creation

## Description

Allows users to create travel groups.

---

## Create Trip Form

### Fields

- Trip Title
- Destination
- Description
- Start Date
- End Date
- Maximum Participants
- Visibility

### Visibility Options

- Public
- Private

---

## Validation

- Title required
- Destination required
- Start date required
- End date required
- Maximum participants greater than 1

---

## Automatic Actions

Upon creation:

- Organizer assigned automatically
- Trip dashboard generated
- Trip chat room generated

---

# 4. Trip Discovery

## Description

Allows travelers to browse and join trips.

---

## Search Filters

- Destination
- Date Range
- Available Seats
- Trip Type

---

## Trip Types

- Trekking
- Camping
- Backpacking
- Road Trip
- Photography
- Adventure

---

## Trip Card Information

Display:

- Trip Name
- Destination
- Start Date
- End Date
- Current Members
- Available Seats

---

# 5. Join Trip

## Description

Allows users to participate in trips.

---

## Join Request

User clicks:

Join Trip

Request sent to organizer.

---

## Organizer Actions

Can:

- Accept Request
- Reject Request

---

## Automatic Actions

Upon approval:

- User added to trip
- Chat access granted
- Resource board access granted

---

# 6. Responsibilities Board

## Description

Allows participants to assign and track responsibilities.

---

## Responsibility Fields

- Title
- Description
- Assigned User
- Due Date
- Status

---

## Status Values

- Pending
- In Progress
- Completed

---

## Examples

- Arrange Taxi
- Bring Tent
- Carry First Aid Kit
- Book Local Guide

---

## Actions

Users can:

- Create Responsibility
- Assign Responsibility
- Mark Complete

---

# 7. Equipment & Resource Board

## Description

Tracks equipment required for a trip.

---

## Resource Fields

- Resource Name
- Quantity Required
- Quantity Assigned

---

## Examples

- Tent
- Sleeping Bag
- Cooking Stove
- Power Bank
- Medical Kit

---

## Actions

Participants can:

- Claim Resource
- Remove Claim
- Update Quantity

---

## Status Logic

Green:
Requirement fulfilled

Yellow:
Partially fulfilled

Red:
Not fulfilled

---

# 8. Trip Chat

## Description

Provides communication between trip participants.

---

## Features

- Group Chat
- Text Messages
- Image Sharing
- File Sharing

---

## Permissions

Only approved trip members may access chat.

---

## Message Information

Display:

- Sender Name
- Profile Picture
- Message
- Timestamp

---

# 9. Expense Tracking

## Description

Tracks shared trip expenses.

---

## Expense Fields

- Title
- Amount
- Description
- Added By
- Date

---

## Examples

- Taxi Fare
- Guide Fee
- Equipment Rental

---

## Expense Summary

Display:

- Total Expenses
- Individual Share
- Expense Breakdown

---

## Calculation

Individual Share:

Total Expense ÷ Number of Participants

---

# 10. Shared Album

## Description

Stores trip memories.

---

## Supported Files

### Images

- JPG
- JPEG
- PNG
- WEBP

### Videos

- MP4
- MOV
- AVI

---

## Upload Rules

Maximum Image Size:
10 MB

Maximum Video Size:
100 MB

---

## Media Information

Display:

- Thumbnail
- Uploaded By
- Upload Date
- Caption

---

## Permissions

Only trip participants may:

- Upload
- View
- Download

---

# 11. Reviews & Ratings

## Description

Allows participants to evaluate each other.

---

## Rating Scale

1 Star
2 Stars
3 Stars
4 Stars
5 Stars

---

## Review Categories

### Organizer

- Communication
- Planning
- Reliability

### Participant

- Cooperation
- Responsibility
- Reliability

---

## Submission Rules

Only trip participants can submit reviews.

---

# 12. Notifications

## Description

Keeps users informed about important events.

---

## Notification Types

- Join Request Received
- Join Request Approved
- Responsibility Assigned
- Resource Claimed
- Expense Added
- Media Uploaded

---

# 13. Dashboard

## Description

Central hub for user activity.

---

## Dashboard Widgets

### My Trips

- Active Trips
- Upcoming Trips
- Completed Trips

---

### Responsibilities

- Pending Tasks
- Completed Tasks

---

### Resources

- Claimed Resources
- Missing Resources

---

### Notifications

Recent updates and alerts.

---

# 14. Trip Completion

## Description

Finalizes trip records.

---

## Completion Actions

Organizer marks trip completed.

System automatically:

- Locks trip editing
- Enables reviews
- Enables memory archive

---

# 15. Reliability Score

## Description

Measures trustworthiness.

---

## Formula

Based on:

- Completed Trips
- Completed Responsibilities
- Average Rating
- Report History

---

## Display

Example:

Reliability Score: 92%

---

# 16. Admin Panel (Future)

## Features

- User Management
- Trip Management
- Content Moderation
- Reports Handling
- Statistics Dashboard

---

# Mobile Requirements

All pages must:

- Be responsive
- Support mobile navigation
- Support touch interactions
- Maintain usability on screens 320px and above

---

# Accessibility Requirements

- Semantic HTML
- Keyboard Navigation
- Alt Text Support
- High Contrast Compatibility

---

# Feature Release Plan

## Phase 1

- Authentication
- Profiles
- Trip Creation
- Trip Joining

## Phase 2

- Responsibilities Board
- Resource Board
- Group Chat

## Phase 3

- Expense Tracking
- Reviews
- Reliability Score

## Phase 4

- Shared Albums
- Notifications
- Memory Archive