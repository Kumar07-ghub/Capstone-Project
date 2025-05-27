-- Drop and recreate the database
DROP DATABASE IF EXISTS grocery_store;
CREATE DATABASE grocery_store;
USE grocery_store;

-- Create products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL DEFAULT 'General'
);

-- Insert sample products
INSERT INTO products (name, price, image, category) VALUES
('Apples', 2.99, 'apples.jpg', 'Fruits'),
('Bananas', 1.29, 'bananas.jpg', 'Fruits'),
('Carrots', 1.79, 'carrots.jpg', 'Vegetables'),
('Broccoli', 2.25, 'broccoli.jpg', 'Vegetables'),
('Milk', 3.49, 'milk.jpg', 'Dairy'),
('Cheese', 5.99, 'cheese.jpg', 'Dairy'),
('Yogurt', 2.19, 'yogurt.jpg', 'Dairy'),
('Tomatoes', 2.89, 'tomatoes.jpg', 'Vegetables'),
('Oranges', 3.59, 'oranges.jpg', 'Fruits');
