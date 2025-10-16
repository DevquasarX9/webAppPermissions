# webAppPermissions

A web application built **without frameworks** that demonstrates authentication, authorization, and role-based permissions using pure PHP.

## Features

✅ **Authentication System**
- Login/Logout functionality
- Session-based authentication
- Bcrypt password hashing
- Database-backed session storage

✅ **Authorization & Permissions**
- Role-based access control (RBAC)
- Three roles: Admin, User, Guest
- Permission checks throughout the application
- Middleware-style route protection

✅ **Database Integration**
- PostgreSQL database
- User, Role, and Session management
- Migration scripts included

✅ **API Endpoints**
- Public API (no authentication required)
- Authenticated user API
- Admin-only API endpoints
- JSON responses

✅ **No Frameworks**
- Pure PHP implementation
- Custom routing system
- MVC-inspired architecture
- PSR-4 autoloading

## Technology Stack

- **PHP**: 8.4
- **Database**: PostgreSQL 16
- **Web Server**: Nginx
- **Environment**: Docker + Docker Compose
- **Password Hashing**: bcrypt
- **Session Storage**: Database

## Project Structure

```
webAppPermissions/
├── .cursor/memory-bank/    # Project documentation
├── docker/                # Docker configuration
├── public/                # Web-accessible files
│   ├── index.php         # Application entry point
│   └── style.css         # Styles
├── src/
│   ├── Auth/             # Authentication & authorization
│   │   ├── Auth.php
│   │   └── Middleware.php
│   ├── Config/           # Configuration classes
│   │   ├── App.php
│   │   └── Database.php
│   ├── Controllers/      # Request handlers
│   │   ├── ApiController.php
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   └── HomeController.php
│   ├── Database/         # Database connection
│   │   └── Connection.php
│   ├── Models/           # Data models
│   │   ├── Model.php
│   │   ├── Role.php
│   │   ├── Session.php
│   │   └── User.php
│   ├── Views/            # HTML templates
│   │   ├── layout.php
│   │   ├── login.php
│   │   ├── dashboard.php
│   │   ├── admin.php
│   │   ├── 403.php
│   │   └── 404.php
│   └── Router.php        # Custom routing
├── .env.example          # Environment variables template
├── composer.json         # Composer configuration
└── docker-compose.yml    # Docker services
```

## Installation & Setup

### Prerequisites

- Docker and Docker Compose
- Git

### Steps

1. **Clone the repository** (or you already have it)

2. **Copy environment file**
   ```bash
   cp .env.example .env
   ```
   
   Adjust the values in `.env` if needed (defaults should work).

3. **Start Docker containers**
   ```bash
   docker compose up -d
   ```

4. **Install dependencies** (if not already installed)
   ```bash
   docker compose exec php-fpm composer install
   ```

5. **Initialize the database**
   
   The database will be automatically initialized when the PostgreSQL container starts for the first time. It will:
   - Create all necessary tables
   - Seed roles (admin, user, guest)
   - Create test users

6. **Access the application**
   
   Open your browser and navigate to:
   ```
   http://localhost
   ```

## Test Accounts

The following test accounts are automatically created:

| Role  | Username | Password  | Description                    |
|-------|----------|-----------|--------------------------------|
| Admin | `admin`  | `admin123`| Full system access             |
| User  | `user`   | `user123` | Standard user access           |
| Guest | `guest`  | `guest123`| Limited access                 |

## API Endpoints

### Public Endpoints (No Authentication Required)

#### Get Application Info
```bash
GET /api/info
```

Returns general information about the application.

**Example:**
```bash
curl http://localhost/api/info
```

### Authenticated Endpoints

#### Get Current User
```bash
GET /api/me
```

Returns the currently authenticated user's information.

**Example:**
```bash
curl -H "Cookie: webapp_session=YOUR_SESSION_TOKEN" http://localhost/api/me
```

### Admin-Only Endpoints

#### Get All Users
```bash
GET /api/users
```

Returns a list of all users (admin only).

#### Get Statistics
```bash
GET /api/stats
```

Returns user and role statistics (admin only).

## Application Routes

| Method | Path          | Description                  | Auth Required | Role Required |
|--------|---------------|------------------------------|---------------|---------------|
| GET    | `/`           | Home page (redirects)        | No            | -             |
| GET    | `/login`      | Login form                   | No            | -             |
| POST   | `/login`      | Process login                | No            | -             |
| POST   | `/logout`     | Logout user                  | Yes           | -             |
| GET    | `/dashboard`  | User dashboard               | Yes           | -             |
| GET    | `/403`        | Forbidden page               | No            | -             |
| GET    | `/404`        | Not found page               | No            | -             |

## Architecture

### Authentication Flow

1. User submits username/password via login form
2. System verifies credentials against database
3. Password is checked using `password_verify()` with bcrypt
4. On success, creates session token (32-byte random)
5. Session stored in database with expiration time
6. Token sent to user via cookie
7. Subsequent requests include cookie for authentication
8. Session validated and refreshed on each request

### Authorization Flow

1. Middleware checks if user is authenticated
2. User role is retrieved from database
3. Permissions checked against role
4. Access granted or denied (403 Forbidden)

### Database Schema

#### Tables
- **roles**: Role definitions (admin, user, guest)
- **users**: User accounts with role assignment
- **sessions**: Active user sessions
- **permissions**: Role-based permissions

#### Relationships
- Each user has exactly one role
- Each session belongs to one user
- Permissions are assigned to roles

## Development

### Regenerate Autoload
```bash
docker compose exec php-fpm composer dump-autoload
```

### Access Database
```bash
docker compose exec postgres psql -U permissions -d webApp
```

### View Logs
```bash
# PHP-FPM logs
docker compose logs -f php-fpm

# Nginx logs
docker compose logs -f webserver

# PostgreSQL logs
docker compose logs -f postgres
```

### Stop Containers
```bash
docker compose down
```

### Reset Database
```bash
# Stop containers
docker compose down -v

# Start containers (database will be re-initialized)
docker compose up -d
```

## Security Features

- **Password Hashing**: bcrypt with automatic salt
- **SQL Injection Prevention**: PDO prepared statements
- **XSS Prevention**: Output escaping with `htmlspecialchars()`
- **Session Security**: 
  - Random token generation
  - Database storage
  - Expiration handling
  - HTTP-only cookies
  - SameSite cookie attribute

## Code Style

This project uses PHP-CS-Fixer for code style consistency.

```bash
# Check and fix code style
composer lint
```

## License

This is a demonstration project for educational purposes.

## Author

Ivelin Ivanov (ivelin.ivanov4@gmail.com)

---

**Built without frameworks** - Pure PHP implementation demonstrating fundamental web development concepts.

