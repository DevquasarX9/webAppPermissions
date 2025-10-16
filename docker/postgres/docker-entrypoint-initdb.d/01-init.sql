-- Database initialization script for webAppPermissions
-- Run this script to create all necessary tables and seed initial data

-- Create roles table
CREATE TABLE IF NOT EXISTS roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role_id INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
);

-- Create sessions table
CREATE TABLE IF NOT EXISTS sessions (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    session_token VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create permissions table (for future extensibility)
CREATE TABLE IF NOT EXISTS permissions (
    id SERIAL PRIMARY KEY,
    role_id INTEGER NOT NULL,
    permission_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    UNIQUE(role_id, permission_name)
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_role_id ON users(role_id);
CREATE INDEX IF NOT EXISTS idx_sessions_token ON sessions(session_token);
CREATE INDEX IF NOT EXISTS idx_sessions_user_id ON sessions(user_id);
CREATE INDEX IF NOT EXISTS idx_sessions_expires ON sessions(expires_at);
CREATE INDEX IF NOT EXISTS idx_permissions_role_id ON permissions(role_id);

-- Seed roles
INSERT INTO roles (name, description) VALUES
    ('admin', 'Administrator with full system access'),
    ('user', 'Standard user with limited access'),
    ('guest', 'Guest user with minimal access')
ON CONFLICT (name) DO NOTHING;

-- Seed test users with bcrypt hashed passwords
-- Password for admin: admin123
-- Password for user: user123  
-- Password for guest: guest123
-- These are hashed with bcrypt

INSERT INTO users (username, email, password, role_id) VALUES
    ('admin', 'admin@example.com', '$2y$12$NFNIOXO1zfrpK3q7iRFIj.jolK4jPXWLwAR4C5m5GL3RILx2gIBfW', 
        (SELECT id FROM roles WHERE name = 'admin')),
    ('user', 'user@example.com', '$2y$12$riUScSPfFhpuEeYPDCUZ1eWOTu3L/PG475weGFF3uPkw15UW/RNpu',
        (SELECT id FROM roles WHERE name = 'user')),
    ('guest', 'guest@example.com', '$2y$12$ncOO9WwgqyYcAXYGQ8vBYuNaDjSwf1n.lHMGTY8VSOOwhrLuE1Gaq',
        (SELECT id FROM roles WHERE name = 'guest'))
ON CONFLICT (username) DO NOTHING;

-- Seed basic permissions
INSERT INTO permissions (role_id, permission_name) VALUES
    ((SELECT id FROM roles WHERE name = 'admin'), 'view_dashboard'),
    ((SELECT id FROM roles WHERE name = 'admin'), 'manage_users'),
    ((SELECT id FROM roles WHERE name = 'admin'), 'view_admin_panel'),
    ((SELECT id FROM roles WHERE name = 'admin'), 'delete_users'),
    ((SELECT id FROM roles WHERE name = 'user'), 'view_dashboard'),
    ((SELECT id FROM roles WHERE name = 'user'), 'edit_profile'),
    ((SELECT id FROM roles WHERE name = 'guest'), 'view_public')
ON CONFLICT (role_id, permission_name) DO NOTHING;

-- Function to clean up expired sessions
CREATE OR REPLACE FUNCTION cleanup_expired_sessions()
RETURNS void AS $$
BEGIN
    DELETE FROM sessions WHERE expires_at < NOW();
END;
$$ LANGUAGE plpgsql;

-- Display created tables
SELECT 'Database initialization complete!' AS status;
SELECT 'Roles created:' AS info;
SELECT id, name, description FROM roles;
SELECT 'Users created:' AS info;
SELECT id, username, email, role_id FROM users;

