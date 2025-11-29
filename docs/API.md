# StudyHub API Documentation

This document describes the API endpoints available in StudyHub.

## Base URL

All API endpoints are relative to the application root:
```
http://localhost/studyhub/api/
```

## Authentication

API endpoints that require authentication check for a valid session. Users must be logged in through the web interface.

---

## Endpoints

### 1. Get Motivation Tip

Returns a random study tip to motivate users.

**Endpoint:** `GET /api/motivation.php`

**Authentication:** Not required

**Response:**
```json
{
  "tip": "Study in focused 25-minute blocks and rest for 5 minutes."
}
```

**Status Codes:**
- `200 OK`: Success

**Example:**
```javascript
fetch('/api/motivation.php')
  .then(res => res.json())
  .then(data => console.log(data.tip));
```

---

### 2. Toggle Resource Status

Cycles a resource's status through: todo → in_progress → done → todo

**Endpoint:** `POST /api/toggle_status.php`

**Authentication:** Required

**Request Body:**
```json
{
  "id": 123
}
```

**Parameters:**
- `id` (integer, required): The resource ID to toggle

**Response (Success):**
```json
{
  "success": true,
  "new_status": "in_progress"
}
```

**Response (Error):**
```json
{
  "success": false,
  "error": "Resource not found"
}
```

**Status Codes:**
- `200 OK`: Success
- `400 Bad Request`: Invalid resource ID
- `401 Unauthorized`: Not authenticated
- `404 Not Found`: Resource not found or access denied
- `405 Method Not Allowed`: Wrong HTTP method
- `500 Internal Server Error`: Database error

**Example:**
```javascript
fetch('/api/toggle_status.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({ id: 123 })
})
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      console.log('New status:', data.new_status);
    }
  });
```

---

## Error Handling

All API endpoints return JSON responses. Error responses follow this format:

```json
{
  "success": false,
  "error": "Error message description"
}
```

Common error scenarios:
- **Authentication errors**: User not logged in
- **Validation errors**: Invalid or missing parameters
- **Authorization errors**: User doesn't own the resource
- **Database errors**: Server-side issues

---

## Rate Limiting

Currently, there is no rate limiting on API endpoints except for login attempts (handled at the application level, not API level).

---

## CORS

CORS is not configured. API endpoints are intended for same-origin requests only.

---

## Future Endpoints

Planned endpoints for future versions:
- `GET /api/resources.php` - List resources with filtering
- `POST /api/resources.php` - Create new resource
- `PUT /api/resources/{id}.php` - Update resource
- `DELETE /api/resources/{id}.php` - Delete resource
- `GET /api/stats.php` - User statistics
