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
    category VARCHAR(50) NOT NULL DEFAULT 'General',
    description TEXT,
    unit VARCHAR(50),
    stock_quantity INT DEFAULT 0
);

-- Sample product data with full details
INSERT INTO products (name, price, image, category, description, unit, stock_quantity) VALUES
('Apples', 2.99, 'apples.jpg', 'Fruits', 'Fresh red apples sourced locally.', '1 lb', 100),
('Bananas', 1.29, 'bananas.jpg', 'Fruits', 'Organic bananas rich in potassium.', '1 bunch', 120),
('Carrots', 1.79, 'carrots.jpg', 'Vegetables', 'Crunchy and sweet carrots.', '1 lb', 90),
('Broccoli', 2.25, 'broccoli.jpg', 'Vegetables', 'Fresh green broccoli florets.', '1 head', 80),
('Milk', 3.49, 'milk.jpg', 'Dairy', '2% pasteurized milk, great for daily use.', '1 litre', 75),
('Cheese', 5.99, 'cheese.jpg', 'Dairy', 'Cheddar cheese block.', '200g', 50),
('Yogurt', 2.19, 'yogurt.jpg', 'Dairy', 'Plain yogurt with probiotics.', '500 ml', 60),
('Tomatoes', 2.89, 'tomatoes.jpg', 'Vegetables', 'Juicy red tomatoes ideal for salads.', '1 lb', 95),
('Oranges', 3.59, 'oranges.jpg', 'Fruits', 'Sweet and tangy oranges.', '1 kg', 85);

-- ============================
-- USERS TABLE
-- ============================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);
