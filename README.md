# Pro Gym 

Pro Gym (Gympro) is a modern, responsive, premium web application designed to manage gym memberships, trainer scheduling, branch locations, and subscriptions. Built using a lightweight **PHP + MySQL** stack, the project features high-fidelity custom styles, interactive elements, and a clean file architecture.

---

## 🚀 Key Features

* **User Authentication**: Secure user registration and login, including session-state checking for members.
* **Subscription Management**: Interface for choosing customized subscription packages (Beginner, Elite, Expert, etc.).
* **Payment Processing**: Multi-input card payment simulation with details captured to database records.
* **Trainer Profiles**: Clean profile layouts showcasing trainer biographies and contact handles.
* **Branches Showcase**: Interactive listing of branch locations with customized views for users.
* **Modular Codebase**: Organized separation of frontend pages, style definitions, backend handler pipelines, and assets.

---

## 🛠️ Tech Stack & Conventions

* **Backend**: PHP 8+
* **Database**: MySQL (designed for XAMPP environments)
* **Frontend**: HTML5, Vanilla CSS3 (Custom responsive styling with Boxicons & FontAwesome support)
* **Email System**: PHPMailer (located in `includes/PHPMailer-master/`)
* **Styling**: `PascalCase.css` matching its companion `PascalCase.php` page filename.

---

## ⚙️ Setup & Installation (Local Development)

### 1. Prerequisites
Ensure you have a local web server environment installed. We recommend **XAMPP**, **WAMP**, or **MAMP** containing PHP and MySQL.

### 2. Database Configuration
1. Start your local Apache and MySQL services in the XAMPP Control Panel.
2. Navigate to your local phpMyAdmin (`http://localhost/phpmyadmin`).
3. Create a new database named **`gymster`**.
4. Import the database schema (if provided/available) or ensure you have a `members_data` table structure to hold member data.
5. Create a `database.php` file inside the `config/` directory with the following structure:
   ```php
   <?php
   $servername = "localhost";
   $username = "root";
   $password = ""; // Your local MySQL password
   $database = "gymster";

   $conn = new mysqli($servername, $username, $password, $database);

   if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
   }
   ?>
   ```

### 3. Deploy Project
1. Clone the repository into your local web server root (e.g., `C:\xampp\htdocs\Pro-Gym`).
2. Open your web browser and navigate to:
   ```
   http://localhost/Pro-Gym/
   ```
   *(The root `index.php` will automatically redirect you to the public guest page at `pages/MainPage.php`)*.
