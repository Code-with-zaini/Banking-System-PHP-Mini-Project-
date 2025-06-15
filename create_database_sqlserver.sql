CREATE TABLE users (
    user_id INT IDENTITY(1,1) PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT GETDATE()
);

CREATE TABLE accounts (
    account_id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT,
    account_type VARCHAR(20) CHECK (account_type IN ('Checking', 'Savings')) NOT NULL,
    account_number VARCHAR(16) UNIQUE NOT NULL,
    balance DECIMAL(15, 2) DEFAULT 0.00,
    created_at DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE transactions (
    transaction_id INT IDENTITY(1,1) PRIMARY KEY,
    account_id INT,
    transaction_type VARCHAR(20) CHECK (transaction_type IN ('Deposit', 'Withdrawal', 'Transfer')) NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    description TEXT,
    target_account_id INT NULL,
    transaction_date DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (account_id) REFERENCES accounts(account_id),
    FOREIGN KEY (target_account_id) REFERENCES accounts(account_id)
);

CREATE TABLE cards (
    card_id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT,
    card_type VARCHAR(20) CHECK (card_type IN ('Debit', 'Credit')) NOT NULL,
    card_number VARCHAR(16) UNIQUE NOT NULL,
    card_holder VARCHAR(255) NOT NULL,
    expiry_date VARCHAR(5) NOT NULL,
    credit_limit DECIMAL(15, 2) DEFAULT NULL,
    available_credit DECIMAL(15, 2) DEFAULT NULL,
    is_blocked BIT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE feedback (
    feedback_id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT,
    category VARCHAR(50) CHECK (category IN ('General Feedback', 'Bug Report', 'Feature Request', 'Complaint', 'Compliment')) NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comments TEXT,
    submitted_at DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);