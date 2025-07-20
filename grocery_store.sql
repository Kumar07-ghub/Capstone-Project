-- Drop and recreate the database
DROP DATABASE IF EXISTS grocery_store;
CREATE DATABASE grocery_store;
USE grocery_store;

-- ============================
-- PRODUCTS TABLE
-- ============================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(100) NOT NULL,
    image_alt VARCHAR(255) DEFAULT NULL, 
    category VARCHAR(50) NOT NULL DEFAULT 'General',
    description TEXT,
    average_rating DECIMAL(3,2) DEFAULT NULL,
    unit VARCHAR(50),
    stock_quantity INT DEFAULT 0
);

-- ============================
-- USERS TABLE
-- ============================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================
-- PRODUCT RATINGS TABLE
-- ============================
CREATE TABLE product_ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT DEFAULT NULL, -- Optional: if you want to track who rated
    rating INT NOT NULL,
    review TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================
-- Sample product data
-- ============================
INSERT INTO products (name, price, image, image_alt, category, description, unit, stock_quantity) VALUES
('Apples', 2.99, 'apples.jpg', 'Fresh red apples on a wooden table', 'Fruits', 'Fresh red apples sourced locally.', '1 lb', 100),
('Bananas', 1.29, 'bananas.jpg', 'Organic bananas in a bunch', 'Fruits', 'Organic bananas rich in potassium.', '1 bunch', 120),
('Carrots', 1.79, 'carrots.jpg', 'Crunchy orange carrots', 'Vegetables', 'Crunchy and sweet carrots.', '1 lb', 90),
('Broccoli', 2.25, 'broccoli.jpg', 'Green broccoli florets', 'Vegetables', 'Fresh green broccoli florets.', '1 head', 80),
('Milk', 3.49, 'milk.jpg', '1-litre milk bottle with blue label', 'Dairy', '2% pasteurized milk, great for daily use.', '1 litre', 75),
('Cheese', 5.99, 'cheese.jpg', 'Block of cheddar cheese', 'Dairy', 'Cheddar cheese block.', '200g', 50),
('Yogurt', 2.19, 'yogurt.jpg', 'Plain yogurt container', 'Dairy', 'Plain yogurt with probiotics.', '500 ml', 60),
('Tomatoes', 2.89, 'tomatoes.jpg', 'Bright red tomatoes in a bowl', 'Vegetables', 'Juicy red tomatoes ideal for salads.', '1 lb', 95),
('Oranges', 3.59, 'oranges.jpg', 'Fresh oranges in a basket', 'Fruits', 'Sweet and tangy oranges.', '1 kg', 85);

-- ============================
-- ORDERS TABLE (Summary)
-- ============================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    province VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100),
    instructions TEXT,
    payment_method VARCHAR(50),
    status VARCHAR(50) NOT NULL DEFAULT 'Pending',
    total DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================
-- ORDER ITEMS TABLE
-- ============================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

-- Insert admin directly

INSERT INTO users (name, email, password, role) 
VALUES ('Admin User', 'admin@example.com', '$2y$10$.rZ7GfAXAI6F.bxLKav6FOL6DJKPnXF/XsSC8aO5v/TKOWJzEvfei', 'admin');

select * from users;
SELECT email, password, role FROM users WHERE email = 'admin@example.com';
