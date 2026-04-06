# Task Manager with Image Gallery – Full-Stack PHP Project

A complete, beginner-friendly web application built with **PHP (no frameworks)**, **MySQLi**, **HTML/CSS**, and optional JavaScript. Includes user authentication, task CRUD, image upload/gallery, and a modern responsive UI.

## Features

### Core (Task Manager)
- User registration and login (password hashing, session management)
- Create, read, update, delete tasks
- Tasks have title, description, status (pending/completed)
- Basic form validation (client + server)
- Logout functionality

### Image Gallery (Additional)
- Upload images (JPG, PNG, GIF, max 2MB)
- Automatic unique file renaming
- Store image path in database, files in `/uploads/` folder
- Display all images in a responsive grid gallery
- Download any image with forced PHP download
- Optional title for each image

### UI/UX
- Modern, clean design using **CSS variables** and **Google Fonts (Inter)**
- Fully responsive (mobile-friendly)
- Smooth hover effects and transitions
- **Dark mode toggle** (floating button)
- Consistent card-based layout, styled forms, buttons, tables, alerts

### Security
- **Prepared statements** (MySQLi) – SQL injection protection
- **XSS protection** – `htmlspecialchars()` on all output
- **File validation** – MIME type, extension, size limit
- **Path traversal protection** in download script
- Passwords hashed with `password_hash()`

## Technology Stack

| Layer       | Technology                          |
|-------------|-------------------------------------|
| Backend     | PHP 7+ (no frameworks)              |
| Database    | MySQL (MySQLi procedural)           |
| Frontend    | HTML5, CSS3, vanilla JS (optional)  |
| Server      | WAMP / XAMPP / any Apache + PHP + MySQL |
| Environment | Localhost development               |

## Project Structure
taskmanager/
│
├── assets/
│ └── style.css # Modern UI (with dark mode)
│
├── config/
│ └── db.php # Database connection (MySQLi)
│
├── includes/
│ ├── header.php # Session start, HTML head, nav start
│ └── footer.php # Closes container, body, optional dark mode script
│
├── uploads/ # Folder for uploaded images (create manually)
│
├── database.sql # Users + tasks tables
├── database_images.sql # Images table (run after main DB)
│
├── index.php # Redirects to login or dashboard
├── register.php # User registration
├── login.php # User login
├── logout.php # Destroy session
├── dashboard.php # Display user tasks
├── add_task.php # Create new task
├── edit_task.php # Edit existing task
├── delete_task.php # Delete task
│
├── upload.php # Image upload form + logic
├── gallery.php # Display all images with download buttons
└── download.php # Force image download


## Installation Guide (WAMP Server)

### 1. Setup WAMP
- Install WAMP and start the server (green icon)
- Create project folder: `C:\wamp64\www\taskmanager`

### 2. Copy Files
- Place all project files into the folder above, respecting the structure
- Create an empty folder named `uploads` inside the project root

### 3. Import Database
- Open phpMyAdmin: `http://localhost/phpmyadmin`
- Create a new database: `taskmanager_db`
- Import `database.sql` (creates `users` and `tasks` tables)
- Import `database_images.sql` (creates `images` table)

### 4. Configure Database Connection
- Open `config/db.php` and adjust credentials if needed (default: root / empty password)

### 5. Run the Project
- Open browser: `http://localhost/taskmanager/`
- Register a new account → login → manage tasks
- Upload images via `upload.php` → view in `gallery.php`

### 6. Dark Mode Toggle (Optional)
- The dark mode button appears automatically if you include the JavaScript snippet in `footer.php` (provided in the CSS upgrade section).

## Database Schema

### Table `users`
| Column     | Type         | Description                 |
|------------|--------------|-----------------------------|
| id         | INT(11) AUTO | Primary key                 |
| username   | VARCHAR(50)  | Unique login name           |
| email      | VARCHAR(100) | Unique email                |
| password   | VARCHAR(255) | Hashed password             |
| created_at | TIMESTAMP    | Registration time           |

### Table `tasks`
| Column      | Type         | Description                       |
|-------------|--------------|-----------------------------------|
| id          | INT(11) AUTO | Primary key                       |
| user_id     | INT(11)      | Foreign key → users(id)           |
| title       | VARCHAR(100) | Task title                        |
| description | TEXT         | Task details                      |
| status      | ENUM         | 'pending' or 'completed'          |
| created_at  | TIMESTAMP    | Creation time                     |

### Table `images`
| Column      | Type          | Description                       |
|-------------|---------------|-----------------------------------|
| id          | INT(11) AUTO  | Primary key                       |
| title       | VARCHAR(255)  | Optional image title              |
| image_path  | VARCHAR(500)  | Relative path (e.g., uploads/abc.jpg) |
| created_at  | TIMESTAMP     | Upload time                       |

## Code Explanation (Key Files)

### `config/db.php`
- Establishes MySQLi connection using procedural style.
- Sets UTF-8 charset.

### `includes/header.php`
- Starts session only once.
- Contains `<html>`, `<head>`, links CSS, and opens `.container` div.

### `register.php` / `login.php`
- Validate input, check duplicates, hash password, start session.

### `dashboard.php`
- Fetches tasks for logged-in user, displays them in a table.
- Provides edit/delete links.

### `upload.php`
- Handles file upload with MIME validation, size check.
- Renames file with `uniqid() + time()`.
- Saves path to `images` table.

### `gallery.php`
- Retrieves all images from DB.
- Displays in CSS Grid with preview thumbnails and download button.

### `download.php`
- Accepts `id` parameter.
- Fetches image path, validates with `realpath` to prevent directory traversal.
- Sends `Content-Disposition: attachment` headers.

### `style.css`
- Uses CSS variables for theming.
- Responsive grid, cards, form styling, table overflow.
- Includes dark mode variables (toggle via JavaScript).

## Security Notes

- All database queries use **prepared statements** (`mysqli_prepare`, `mysqli_stmt_bind_param`).
- File uploads are restricted by MIME type (not just extension) and size (2MB).
- Uploaded files are renamed – original names never exposed.
- Download script prevents path traversal – cannot access files outside project root.
- Passwords are hashed with `password_hash()` – never stored in plain text.
- Output is escaped with `htmlspecialchars()` to prevent XSS.

## Customization Ideas

- Add user-specific image galleries (link `images.user_id` to `users.id`)
- Allow editing/deleting images
- Add pagination for gallery
- Implement AJAX upload with preview
- Convert to OOP PHP or add a simple router

## Troubleshooting

| Issue                          | Solution                                                                 |
|--------------------------------|--------------------------------------------------------------------------|
| White screen or connection error | Check WAMP is running, verify database credentials in `config/db.php`   |
| Session start notice           | Ensure `header.php` is included first – never call `session_start()` again |
| Upload fails (move_uploaded_file) | Check `uploads/` folder exists and has write permissions (WAMP gives by default) |
| Download shows gibberish       | Make sure `download.php` has no whitespace before `<?php`               |
| Images not displaying          | Verify `image_path` in DB matches actual file location (relative to project root) |

## Credits

- Built as a learning project for PHP, MySQL, and modern frontend design.
- Font: [Inter](https://fonts.google.com/specimen/Inter) by Google Fonts.

## License

MIT – free to use, modify, and distribute.
