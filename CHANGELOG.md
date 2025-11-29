# Changelog

All notable changes to StudyHub are documented in this file.

## [2.0.0] - 2024-11-29

### Added

#### Security Enhancements
- **CSRF Protection**: Added CSRF tokens to all forms (login, register, dashboard)
- **Rate Limiting**: Implemented login attempt throttling (5 attempts per 5 minutes)
- **Input Validation**: Added server-side validation for all user inputs
  - Email format validation
  - Password minimum length (8 characters)
  - Resource type enum validation
  - Status enum validation
- **Error Handling**: Improved error messages (generic to users, detailed in logs)
- **Session Security**: Enhanced session management with regeneration on login

#### Features
- **Edit Resources**: Users can now edit existing resources
- **Search Functionality**: Real-time search by title or subject
- **Filter System**: Filter resources by type (note/link/task) and status
- **Pagination**: Browse resources with pagination (10 per page)
- **Resource Counter**: Display total number of resources
- **Better Delete Handling**: Improved error handling and user feedback

#### UI/UX Improvements
- **Custom CSS**: Added comprehensive styling in `style.css`
  - Improved card hover effects
  - Better form styling
  - Responsive design enhancements
  - Pagination styling
  - Filter section styling
- **Footer Implementation**: Added Bootstrap JS bundle
- **Consistent Navbar**: Fixed dashboard to use shared navbar component
- **Mobile Responsiveness**: Enhanced mobile layout and button sizing

#### Documentation
- **README.md**: Comprehensive setup and usage guide
- **API.md**: Complete API endpoint documentation
- **SECURITY.md**: Detailed security implementation guide
- **CHANGELOG.md**: This file

#### Database
- **Migration Scripts**: 
  - `migrate.bat` for Windows
  - `migrate.sh` for Mac/Linux
- **Schema File**: Complete database schema with indexes
- **Foreign Keys**: Added CASCADE delete for data integrity

### Changed

#### Code Quality
- **Fixed Duplicate Code**: Removed duplicate `DOMContentLoaded` listener in `main.js`
- **Consistent Error Handling**: Standardized error handling across all pages
- **Better Validation**: Enhanced input validation with helper functions
- **Improved Security Functions**: Expanded `security.php` with new utilities

#### Security
- **Password Requirements**: Enforced 8-character minimum
- **Database Errors**: No longer exposed to users
- **Ownership Checks**: Enhanced resource access control
- **Token Validation**: Using timing-safe comparison for CSRF tokens

### Fixed

- **JavaScript Errors**: Fixed duplicate event listener registration
- **Dashboard Navbar**: Now uses shared navbar component instead of inline HTML
- **Empty Files**: Implemented `footer.php` and `style.css`
- **Delete Resource**: Added proper error handling and user feedback
- **Form Validation**: Added client-side HTML5 validation attributes
- **Resource Type**: Validated against allowed enum values
- **Status Toggle**: Improved error handling and user feedback

### Security Fixes

- **SQL Injection**: Already protected, maintained prepared statements
- **XSS**: Already protected, maintained output escaping
- **CSRF**: **NEW** - Added comprehensive CSRF protection
- **Brute Force**: **NEW** - Added rate limiting on login
- **Session Fixation**: **NEW** - Added session regeneration
- **Information Disclosure**: **NEW** - Generic error messages

---

## [1.0.0] - Initial Release

### Features
- User registration and authentication
- Add study resources (notes, links, tasks)
- Delete resources
- Toggle resource status (todo/in_progress/done)
- Random motivational study tips
- Bootstrap 5 UI
- Session-based authentication
- Password hashing (Argon2id/bcrypt)

### Security
- Prepared statements for SQL queries
- Password hashing
- Output escaping with `htmlspecialchars()`
- Session management

### Known Issues (Fixed in 2.0.0)
- No CSRF protection
- No rate limiting
- Duplicate JavaScript code
- Empty CSS file
- Empty footer file
- No edit functionality
- No search/filter
- No pagination
- Database errors exposed to users
- Inconsistent navbar usage
