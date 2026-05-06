# Task Manager with Image Gallery – Full‑Stack PHP Project

A complete, secure, and beginner‑friendly web application built with **plain PHP (no frameworks)**, **MySQLi (object‑oriented)**, **HTML/CSS**, and a touch of vanilla JavaScript. Includes user authentication, task CRUD, image upload/gallery with ownership control, and a clean, system‑themed responsive UI.

## Features

### Core (Task Manager)
- User registration and login – bcrypt hashing, session management
- Create, read, update, delete tasks  
- Tasks have title, description, status (pending/completed)  
- Form validation on both client and server side  
- Logout

### Image Gallery (Additional)
- Upload images (JPG, PNG, GIF, max 2MB)  
- Automatic unique file renaming (keeps original filename for download)  
- Image ownership – only the uploader can edit/delete  
- Full CRUD for images: **edit title**, **delete** (with file removal)  
- Download any image – original filename is sent to the browser  
- Images stored in `/uploads/` folder, path stored in database

### UI/UX
- Clean, simple design – no heavy frameworks  
- System‑native fonts – instant loading, no external dependencies  
- Fully responsive (mobile‑friendly)  
- **Automatic light/dark theme** – follows the OS preference (`prefers-color-scheme: dark`)  
- Consistent navigation bar on every page  
- Card‑based gallery, styled forms, tables, buttons, alerts

### Security
- **Prepared statements** (MySQLi OOP) everywhere – no SQL injection  
- Output escaped with `htmlspecialchars()` to prevent XSS  
- File upload validation – MIME type (via `finfo`), extension whitelist, size limit  
- Path traversal protection in download script (`realpath()` check)  
- Session regeneration after login  
- Passwords hashed with `password_hash(PASSWORD_BCRYPT, ['cost' => 12])`

## Technology Stack

| Layer       | Technology                          |
|-------------|-------------------------------------|
| Backend     | PHP 8+ (object‑oriented, no frameworks) |
| Database    | MySQL (MySQLi OOP)                  |
| Frontend    | HTML5, CSS3, vanilla JS             |
| Server      | WAMP / XAMPP / Apache + PHP + MySQL |
| Environment | Localhost development               |


## Installation Guide (WAMP Server)

### 1. Setup WAMP
- Install WAMP and start the server (green icon).
- Create project folder: `C:\wamp64\www\taskmanager`

### 2. Copy Files
- Place all project files into the folder above, respecting the structure.
- The `uploads/` folder will be created automatically on the first upload – make sure your web server can write to the project directory.

### 3. Import Database
- Open phpMyAdmin: `http://localhost/phpmyadmin`
- Create a new database named `taskmanager_db`
- Import the single `database.sql` file (it creates `users`, `tasks`, and `images` tables with correct foreign keys)

### 4. Configure Database Connection
- Open `config/db.php` and adjust credentials if needed (default: `root` / empty password).  
  **In production, use a dedicated user with a strong password.**

### 5. Run the Project
- Open browser: `http://localhost/taskmanager/`
- Register a new account → login → manage tasks
- Upload images via `upload.php` → view/edit/delete in `gallery.php`

## Database Schema

### Table `users`
| Column      | Type         | Description                 |
|-------------|--------------|-----------------------------|
| id          | INT(11) AUTO | Primary key                 |
| username    | VARCHAR(50)  | Unique login name           |
| email       | VARCHAR(100) | Unique email                |
| password    | VARCHAR(255) | Hashed password             |
| created_at  | TIMESTAMP    | Registration time           |

### Table `tasks`
| Column      | Type         | Description                       |
|-------------|--------------|-----------------------------------|
| id          | INT(11) AUTO | Primary key                       |
| user_id     | INT(11)      | Foreign key → users(id)           |
| title       | VARCHAR(100) | Task title                        |
| description | TEXT         | Task details                      |
| status      | ENUM('pending','completed') | Status        |
| created_at  | TIMESTAMP    | Creation time                     |

### Table `images`
| Column            | Type          | Description                          |
|-------------------|---------------|--------------------------------------|
| id                | INT(11) AUTO  | Primary key                          |
| user_id           | INT(11)       | Foreign key → users(id) (owner)      |
| title             | VARCHAR(100)  | Optional image title                 |
| image_path        | VARCHAR(255)  | Relative path (e.g., `uploads/abc.jpg`) |
| original_filename | VARCHAR(255)  | Original file name from upload       |
| created_at        | TIMESTAMP     | Upload time                          |

## Code Explanation (Key Files)

### `config/db.php`
- Object‑oriented MySQLi connection with exception mode.
- Sets UTF‑8 charset, handles connection errors gracefully.

### `includes/header.php`
- Starts session only if not already active.
- Outputs HTML `<head>` and `.container` opening div.

### `register.php` / `login.php`
- Validate input, check duplicates, hash password with bcrypt.
- Session regeneration on login to prevent fixation.

### `dashboard.php`
- Fetches only tasks belonging to the logged‑in user.
- Styled table with edit/delete links.

### `upload.php`
- Requires login – every image is tied to a user.
- Validates file (MIME, extension, size).
- Renames file on disk with `uniqid()`, stores `original_filename` in DB.
- Saves `user_id`, title, paths.

### `gallery.php`
- Displays all images (order by newest first).
- Shows **Edit** and **Delete** buttons **only to the owner**.
- Action buttons are evenly sized and centrally aligned in each card.

### `edit_image.php`
- Allows the owner to update the image title.
- Verifies ownership before showing form or processing update.

### `delete_image.php`
- Checks ownership, removes database record and deletes the file from disk.
- Redirects back to gallery.

### `download.php`
- Fetches image path and original filename.
- Verifies the resolved path is inside the project root (`realpath`).
- Forces download with the **original** filename.

### `style.css`
- Uses CSS custom properties for effortless light/dark theming.
- `@media (prefers-color-scheme: dark)` automatically switches colours – no manual toggle.
- System font stack, responsive grid, and clean card/table/button styles.

## Security Notes

- Every database query uses **prepared statements** (MySQLi OOP).
- File uploads are validated by both MIME type (`finfo`) and extension whitelist.
- No original filename is used on disk – prevents overwrites and path attacks.
- Download script uses `realpath()` to stop directory traversal.
- All user‑supplied data is escaped with `htmlspecialchars()` before output.
- Passwords are stored using bcrypt (cost 12) – never plain text.
- Session ID is regenerated after login to prevent session fixation.

## Customization Ideas

- Add user profile pages or avatar uploads  
- Enable sharing tasks with other users  
- Implement a trash/recycle bin for tasks and images  
- Add AJAX upload with progress bar  
- Paginate the gallery  

## Troubleshooting

| Issue                               | Solution                                                                 |
|-------------------------------------|--------------------------------------------------------------------------|
| White screen or connection error    | Check WAMP is running, and verify database credentials in `config/db.php` |
| Session errors / headers already sent | Ensure no whitespace before `<?php` in `header.php`                     |
| Upload fails (move_uploaded_file)   | Make sure the `uploads/` folder exists (it’s auto‑created) and is writable |
| Download shows gibberish            | Verify `download.php` has no output before the headers                  |
| Images not displaying               | Check that `image_path` in DB matches the actual file location          |
| ‘You do not have permission’ error  | Your session might have expired – log in again                           |

## License

MIT – free to use, modify, and distribute.
