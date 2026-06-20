# Gamers Social

A community-driven discussion platform for gamers to share topics, ask questions, and engage in conversations about their favorite games.

## 🌟 Features

- **User authentication** – Register and log in with secure password hashing (bcrypt).
- **Role‑based access** – Admins can create, edit, and delete topics; regular users can comment.
- **Discussion topics** – Each post includes a title, game name, description, and an optional cover image.
- **Comments with images** – Users can reply to posts and attach an image to their comment.
- **Image upload** – Supports JPEG, PNG, and GIF images up to 2 MB.
- **Responsive design** – Adapts to light/dark system preference automatically.
- **Secure database interactions** – All queries use prepared statements to prevent SQL injection.

## 🛠️ Technologies

- **PHP 7.4+** – Server‑side scripting
- **MySQL / MariaDB** – Database
- **HTML5 & CSS3** – Custom styling with CSS variables for theming
- **JavaScript** – Minimal for file‑upload UI feedback

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/gamers-social.git
   cd gamers-social
   ```

2. **Set up the database**
   - Create a MySQL database (e.g., `gamers_social_db`).
   - Run the following SQL to create the necessary tables:

   ```sql
   CREATE TABLE users (
       id INT AUTO_INCREMENT PRIMARY KEY,
       username VARCHAR(50) UNIQUE NOT NULL,
       email VARCHAR(100) UNIQUE NOT NULL,
       password VARCHAR(255) NOT NULL,
       role VARCHAR(20) DEFAULT 'user',
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );

   CREATE TABLE posts (
       id INT AUTO_INCREMENT PRIMARY KEY,
       user_id INT NOT NULL,
       title VARCHAR(150) NOT NULL,
       description TEXT,
       game_name VARCHAR(100) NOT NULL,
       image_path VARCHAR(255),
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
   );

   CREATE TABLE comments (
       id INT AUTO_INCREMENT PRIMARY KEY,
       post_id INT NOT NULL,
       user_id INT NOT NULL,
       body TEXT NOT NULL,
       image_path VARCHAR(255),
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
       FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
   );
   ```

3. **Configure database connection**
   - Open `config/db.php` and update the credentials:
     ```php
     $db_host = 'localhost';
     $db_user = 'your_db_user';
     $db_pass = 'your_db_password';
     $db_name = 'gamers_social_db';
     ```

4. **Run the application**
   - Place the project in your web server's document root (e.g., `htdocs` for XAMPP).
   - Access `http://localhost/gamers-social` in your browser.

## 📖 Usage

- **Register** a new account (all users are regular users by default).
- **Log in** with your credentials.
- **Admin access** – To gain admin rights, manually set the `role` column to `'admin'` in the `users` table for your user.
- **Create topics** – Only admins can add new posts via the “+ New Topic” link.
- **Comment** – Logged‑in users can reply to any post and optionally attach an image.
- **Manage posts** – Admins can edit or delete any post (this also removes all its comments).

## 📁 Project Structure

```
gamers-social/
├── assets/
│   └── style.css              # Main stylesheet with light/dark themes
├── config/
│   └── db.php                 # Database connection
├── includes/
│   ├── header.php             # Page header with navigation
│   └── footer.php             # Page footer
├── images/                    # Uploaded images (created automatically)
├── dashboard.php              # Post feed (homepage)
├── delete_post.php            # Delete a post (admin only)
├── edit_post.php              # Edit a post (admin only)
├── index.php                  # Redirects to login/dashboard
├── login.php                  # User login
├── logout.php                 # Log out
├── new_post.php               # Create a new post (admin only)
├── post.php                   # View a single post with comments
├── register.php               # User registration
└── README.md                  # This file
```

## 🤝 Contributing

Contributions are welcome! Feel free to open issues or submit pull requests for improvements, bug fixes, or new features.

## 📄 License

This project is for educational purposes. You may use and modify it under the terms of the [MIT License](LICENSE).
