# 🎮 Gamers Social Media

A full‑stack social platform for gamers, built with **HTML**, **CSS**, **JavaScript**, **PHP (MySQLi)**, and **MySQL**.  
Admins create discussion topics about video games (with images), and users can comment, upload images, and interact.  
Originally developed for the **NFA041** university course.

---

## ✨ Features

- **User Authentication**
  - Register with username, email, and password (bcrypt hashed).
  - Login using username or email.
  - Session‑based authentication (no tokens).
  - Role‑based access: `user` and `admin`.

- **Admin Capabilities**
  - Create new topics (title, game name, description, cover image).
  - Edit and delete any post.
  - Delete any comment.
  - Only admins can manage posts.

- **User Interaction**
  - Browse the feed of all topics (public).
  - View a single post with full details.
  - Add comments (text + optional image upload).
  - Delete your own comments (or admin can delete any).

- **Image Handling**
  - Upload images for posts (cover) and comments (attachment).
  - File type validation (JPG, PNG, GIF, max 2 MB).
  - Automatic deletion of image files when a post/comment is removed.

- **Database Design**
  - Relational MySQL schema with foreign keys and cascading deletes.
  - Auto‑increment user IDs (with optional gap‑filling to reuse deleted IDs).
  - Prepared statements everywhere to prevent SQL injection.

- **Responsive & Accessible UI**
  - Light/dark mode follows OS preference.
  - System font stack, clean card‑based layout.
  - File‑input custom styling with live filename display.

---

## 🛠️ Technologies Used

| Layer       | Technology                          |
|-------------|-------------------------------------|
| Frontend    | HTML5, CSS3 (custom properties), vanilla JavaScript |
| Backend     | PHP 7.4+ (procedural style, MySQLi) |
| Database    | MySQL 8+ (InnoDB)                   |
| Environment | XAMPP / WAMP / MAMP (Apache server) |

---

## 📁 Project Structure
gamers-social/
├── assets/
│ └── style.css # Global stylesheet
├── config/
│ └── db.php # Database connection (MySQLi)
├── includes/
│ ├── header.php # Session start, <head>, nav bar
│ └── footer.php # Closing tags
├── images/ # Uploaded images (posts & comments)
├── index.php # Redirect based on auth status
├── login.php # User login form
├── register.php # User registration (with ID gap-filling)
├── logout.php # Destroy session
├── dashboard.php # Main feed (all posts, cards)
├── post.php # Single post view + comments
├── new_post.php # Create new topic (admin only)
├── edit_post.php # Edit existing topic (admin only)
├── delete_post.php # Delete a post (admin only)
├── delete_comment.php # Delete a comment (owner or admin)
├── database.sql # Full database schema and initial tables
└── README.md # This file


---

## 🧱 Database Schema

The database `gamers_social_db` contains three tables:

### `users`
| Column       | Type         | Description                     |
|--------------|--------------|---------------------------------|
| id           | INT(11) PK   | Auto‑increment (gap‑filling used)|
| username     | VARCHAR(50)  | Unique                          |
| email        | VARCHAR(100) | Unique                          |
| password     | VARCHAR(255) | Bcrypt hash                     |
| role         | ENUM('user','admin') | Default 'user'           |
| created_at   | TIMESTAMP    | Auto‑generated                  |

### `posts`
| Column       | Type         | Description                        |
|--------------|--------------|------------------------------------|
| id           | INT(11) PK   | Auto‑increment                     |
| user_id      | INT(11) FK   | References `users.id` (CASCADE)    |
| title        | VARCHAR(150) |                                    |
| description  | TEXT         |                                    |
| game_name    | VARCHAR(100) | Name of the game being discussed   |
| image_path   | VARCHAR(255) | Optional cover image path          |
| created_at   | TIMESTAMP    | Auto‑generated                     |

### `comments`
| Column       | Type         | Description                        |
|--------------|--------------|------------------------------------|
| id           | INT(11) PK   | Auto‑increment                     |
| post_id      | INT(11) FK   | References `posts.id` (CASCADE)    |
| user_id      | INT(11) FK   | References `users.id` (CASCADE)    |
| body         | TEXT         | Comment text                       |
| image_path   | VARCHAR(255) | Optional attachment image          |
| created_at   | TIMESTAMP    | Auto‑generated                     |

All foreign keys use `ON DELETE CASCADE`, so deleting a user removes their posts and comments, and deleting a post removes all associated comments.

---

## 🚀 Installation & Setup

### 1. Clone / Download the Project
Place the entire `gamers-social` folder inside your local server’s root directory:
- XAMPP: `htdocs/`
- WAMP: `www/`
- MAMP: `htdocs/`

### 2. Configure Database Connection
Open `config/db.php` and update the credentials:
```php
$db_host = 'localhost';
$db_user = 'root';      // your MySQL username
$db_pass = '';          // your MySQL password
$db_name = 'gamers_social_db';

### 3. Import the Database
- Open phpMyAdmin (http://localhost/phpmyadmin).
- Create a new database named gamers_social_db (or import the file directly).
- Select the database and import the database.sql file from the project.

### 4. Create the images/ Folder
Inside the project root, create a folder called images/.
Make sure it has write permissions (777 or 755 depending on your server).

### 5. Roles
**Features Available to Guests (Not Logged In)**
- View the feed (list of all topics).
- View a single post and its comments.
- Cannot comment, create posts, or delete anything.

**Features for Logged‑in Users**
- Browse the feed.
- View posts.
- Add comments (with optional image upload).
- Delete their own comments.

**Features for Admins**
- Everything a regular user can do.
- Create new topics with a title, game name, description, and cover image.
- Edit any post (change title, description, game name, replace image).
- Delete any post (removes the post, all comments, and associated images).
- Delete any comment.
