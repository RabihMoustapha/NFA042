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

## 🗄️ Full Database SQL (`database.sql`)

```sql
-- Create database
CREATE DATABASE IF NOT EXISTS gamers_social_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE gamers_social_db;

-- Users
CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Posts (topics created by admins)
CREATE TABLE posts (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    game_name VARCHAR(100) NOT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Comments (with optional image)
CREATE TABLE comments (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    post_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    body TEXT NOT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
