# Mailing List API Application

A simple PHP-based mailing list management system with RESTful API and web interface.

## Features

- View all contacts sorted by most recently added
- Add new email addresses to the mailing list
- Delete email addresses from the mailing list
- Simple web interface for easy interaction
- RESTful API endpoints for programmatic access

## Technical Stack

- **Backend**: Vanilla PHP 8.0+
- **Database**: SQLite (for simplicity, can be easily changed to MySQL)
- **Frontend**: HTML, CSS, JavaScript
- **Server**: PHP built-in server (for development)

## Setup Instructions

### Prerequisites

- PHP 8.0 or higher
- SQLite extension enabled (usually included by default)

### Installation

1. **Clone or download the project**
   ```bash
   git clone <repository-url>
   cd mailing-list-api
   ```

2. **Set up the database**
   ```bash
   php setup/database.php
   ```

3. **Start the development server**
   ```bash
   php -S localhost:8000 -t public
   ```

4. **Access the application**
   - Web Interface: http://localhost:8000
   - API Base URL: http://localhost:8000/api

## API Endpoints

### Get All Contacts
```
GET /api/contacts
```
Returns all contacts sorted by most recently added first.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "email_address": "john@example.com",
      "name": "John Doe",
      "created_at": "2024-01-15 10:30:00"
    }
  ]
}
```

### Add New Contact
```
POST /api/contacts
```
Adds a new contact to the mailing list.

**Request Body:**
```json
{
  "email_address": "jane@example.com",
  "name": "Jane Smith"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Contact added successfully",
  "data": {
    "id": 2,
    "email_address": "jane@example.com",
    "name": "Jane Smith",
    "created_at": "2024-01-15 11:00:00"
  }
}
```

### Delete Contact
```
DELETE /api/contacts/{id}
```
Deletes a contact by ID.

**Response:**
```json
{
  "success": true,
  "message": "Contact deleted successfully"
}
```

## Testing with curl

### Get all contacts
```bash
curl -X GET http://localhost:8000/api/contacts
```

### Add a new contact
```bash
curl -X POST http://localhost:8000/api/contacts \
  -H "Content-Type: application/json" \
  -d '{"email_address": "test@example.com", "name": "Test User"}'
```

### Delete a contact
```bash
curl -X DELETE http://localhost:8000/api/contacts/1
```

## Project Structure

```
mailing-list-api/
├── config/
│   └── database.php          # Database configuration
├── src/
│   ├── Controllers/
│   │   └── ContactController.php  # API logic
│   ├── Models/
│   │   └── Contact.php            # Contact model
│   └── Database/
│       └── Database.php           # Database connection
├── setup/
│   └── database.php              # Database setup script
├── public/
│   ├── index.php                 # Main entry point
│   ├── api.php                   # API router
│   ├── css/
│   │   └── style.css             # Styles
│   └── js/
│       └── app.js                # Frontend JavaScript
├── database/
│   └── contacts.db               # SQLite database (created on setup)
└── README.md
```

## Design Decisions

### Database Schema
The contact table includes:
- `id`: Primary key for unique identification
- `email_address`: Unique email with validation
- `name`: Contact name for better UX
- `created_at`: Timestamp for sorting and audit trail

### API Design
- RESTful endpoints following standard conventions
- JSON request/response format
- Consistent error handling
- HTTP status codes for different scenarios

### Security Considerations
For a production application, you would want to add:
- Input validation and sanitization
- CSRF protection
- Rate limiting
- Authentication and authorization
- HTTPS enforcement
- SQL injection prevention (already implemented with prepared statements)

### Code Organization
- MVC-like structure for separation of concerns
- Database abstraction layer
- Centralized configuration
- Error handling and logging

## Troubleshooting

### Common Issues

1. **Database not found**: Run `php setup/database.php` to create the database
2. **Permission errors**: Ensure the `database/` directory is writable
3. **Port already in use**: Change the port in the server command (e.g., `php -S localhost:8001`)

### Error Responses

The API returns appropriate HTTP status codes:
- `200`: Success
- `400`: Bad Request (validation errors)
- `404`: Not Found
- `500`: Internal Server Error

## Development Notes

This application demonstrates:
- Modern PHP practices without frameworks
- RESTful API design
- Database operations with prepared statements
- Frontend-backend integration
- Error handling and validation
- Clean code organization

The code is structured to be easily extensible for additional features like search, pagination, or authentication. 