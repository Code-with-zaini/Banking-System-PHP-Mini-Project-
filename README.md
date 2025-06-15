# 🏦 Banking System

A simple yet comprehensive web-based banking management system built with PHP and MySQL. This project demonstrates core banking functionalities including account management, transactions, card services, and user management.

## 📋 Table of Contents
- [Features](#features)
- [Technologies Used](#technique-used)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Project Structure](#project-structure)
- [Usage](#usage)
- [Security Features](#security-features)
- [Contributing](#contributing)

## ✨ Features

### 🔐 User Authentication
- **Sign Up**: Create new user accounts with validation
- **Sign In**: Secure login with password verification
- **Session Management**: Secure user sessions
- **Profile Management**: Update personal information

### 💰 Account Management
- **Multiple Account Types**: Checking and Savings accounts
- **Account Overview**: View all accounts with balances
- **Account Numbers**: Auto-generated unique account numbers

### 💸 Transaction Services
- **Money Transfers**: Transfer funds between accounts
- **Deposits**: Add money to accounts
- **Withdrawals**: Withdraw money with balance validation
- **Transaction History**: View recent transactions on dashboard

### 💳 Card Services
- **Card Management**: View and manage debit/credit cards
- **Order New Cards**: Request new cards with auto-generated numbers
- **Block Cards**: Temporarily block cards for security
- **Delete Cards**: Remove blocked cards permanently

### 📝 Additional Features
- **Feedback System**: Submit ratings and comments
- **Customer Support**: Contact information and FAQ
- **Responsive Dashboard**: Clean, intuitive user interface

## 🛠 Techniques Used

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Security**: Password hashing, Session management, Input validation
- **Database Connection**: PDO (PHP Data Objects)

## 📋 Prerequisites

Before running this project, make sure you have:

- **Web Server**: Apache or Nginx
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **XAMPP/WAMP/LAMP**: For local development (recommended for beginners)

## 🚀 Installation

### Step 1: Clone the Repository
```bash
git clone https://github.com/yourusername/banking-system.git
cd banking-system
```

### Step 2: Setup Web Server
1. If using XAMPP:
   - Copy the project folder to `htdocs` directory
   - Start Apache and MySQL services from XAMPP Control Panel

2. If using a live server:
   - Upload files to your web hosting directory
   - Ensure PHP and MySQL are available

### Step 3: Configure Database Connection
1. Create a file named `db_connect.php` in the root directory:
```php
<?php
$host = 'localhost';
$dbname = 'banking_system';
$username = 'your_db_username';
$password = 'your_db_password';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
```

## 🗄 Database Setup

### Step 1: Create Database
```sql
CREATE DATABASE banking_system;
USE banking_system;
```

### Step 2: Run the SQL Script
Execute the SQL commands from `create_database.sql` file in your MySQL client or phpMyAdmin:

```sql
-- Users table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Accounts table
CREATE TABLE accounts (
    account_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    account_type ENUM('Checking', 'Savings') NOT NULL,
    account_number VARCHAR(11) UNIQUE NOT NULL,
    balance DECIMAL(11, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Transactions table
CREATE TABLE transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT,
    transaction_type ENUM('Deposit', 'Withdrawal', 'Transfer') NOT NULL,
    amount DECIMAL(11, 2) NOT NULL,
    description TEXT,
    target_account_id INT NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(account_id),
    FOREIGN KEY (target_account_id) REFERENCES accounts(account_id)
);

-- Cards table
CREATE TABLE cards (
    card_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    card_type ENUM('Debit', 'Credit') NOT NULL,
    card_number VARCHAR(15) UNIQUE NOT NULL,
    card_holder VARCHAR(255) NOT NULL,
    expiry_date VARCHAR(5) NOT NULL,
    is_blocked BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Feedback table
CREATE TABLE feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    category ENUM('General Feedback', 'Bug Report', 'Feature Request', 'Complaint', 'Compliment') NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comments TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
```

## 📁 Project Structure

```
banking-system/
│
├── login.php              # Login and registration page
├── dashboard.php          # Main dashboard
├── accounts.php           # Account overview
├── transfers.php          # Money transfer functionality
├── deposit.php            # Deposit and withdrawal
├── cards.php              # Card management
├── feedback.php           # Feedback system
├── profile.php            # User profile management
├── support.php            # Customer support
├── logout.php             # Logout functionality
├── db_connect.php         # Database connection (create this)
├── create_database.sql    # Database schema
├── style.css              # Main stylesheet (create this)
├── stylelogin.css         # Login page stylesheet (create this)
└── README.md              # This file
```

## 🎯 Usage

### For New Users:
1. Visit the login page (`login.php`)
2. Click "Sign Up" to create a new account
3. Fill in required information and agree to terms
4. After successful registration, sign in with your credentials
5. Two default accounts (Checking & Savings) will be created automatically

### Main Features:
- **Dashboard**: View total balance and recent transactions
- **Accounts**: See all your accounts and balances
- **Transfers**: Send money to other accounts using account numbers
- **Deposit/Withdrawal**: Add or remove money from your accounts
- **Cards**: Manage your debit/credit cards
- **Profile**: Update personal information
- **Feedback**: Rate the service and provide comments

## 🔒 Security Features

- **Password Hashing**: All passwords are securely hashed using PHP's `password_hash()`
- **SQL Injection Prevention**: Uses prepared statements with PDO
- **Session Management**: Secure session handling for user authentication
- **Input Validation**: Server-side validation and sanitization
- **XSS Protection**: HTML special characters are escaped

## 🎨 Customization

### Styling
- Edit `style.css` for main application styling
- Edit `stylelogin.css` for login page styling
- The design uses a clean, modern interface with sidebar navigation

### Adding Features
The modular structure makes it easy to add new features:
1. Create new PHP files for additional functionality
2. Add navigation links in the sidebar
3. Follow the existing code patterns for consistency

## 🐛 Troubleshooting

### Common Issues:

1. **Database Connection Error**
   - Check your database credentials in `db_connect.php`
   - Ensure MySQL service is running

2. **Session Issues**
   - Make sure PHP sessions are enabled
   - Check file permissions

3. **Styling Issues**
   - Ensure CSS files are in the correct directory
   - Check browser console for any errors

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request



## 📞 Support

If you encounter any issues or have questions:
- Create an issue in this repository
- Check the troubleshooting section above
- Review the code comments for implementation details

---
## 🚀 Getting Started Quickly

For beginners who want to get started immediately:

1. **Download XAMPP** from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. **Install and start** Apache and MySQL
3. **Clone this repository** to `xampp/htdocs/banking-system`
4. **Open phpMyAdmin** (http://localhost/phpmyadmin)
5. **Create database** named `banking_system`
6. **Import** the SQL from `create_database.sql`
7. **Create** `db_connect.php` with your database credentials
8. **Visit** http://localhost/banking-system/login.php
9. **Sign up** and start exploring!

Happy Banking! 🎉
