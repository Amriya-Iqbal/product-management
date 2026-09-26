CREATE DATABASE IF NOT EXISTS product_management;

USE product_management;


-- ==========================================
-- ADMINS TABLE
-- ==========================================

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- ==========================================
-- CATEGORIES TABLE
-- ==========================================

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- ==========================================
-- PRODUCTS TABLE
-- ==========================================

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,

    category_id INT NULL,

    name VARCHAR(150) NOT NULL,

    sku VARCHAR(100) NOT NULL UNIQUE,

    description TEXT,

    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    stock INT NOT NULL DEFAULT 0,

    low_stock_limit INT NOT NULL DEFAULT 10,

    image VARCHAR(255) DEFAULT NULL,

    status ENUM('Active','Inactive') DEFAULT 'Active',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_product_category
        FOREIGN KEY (category_id)
        REFERENCES categories(id)
        ON DELETE SET NULL
);


-- ==========================================
-- SAMPLE CATEGORIES
-- ==========================================

INSERT INTO categories (name, description)
VALUES
('Electronics', 'Electronic products and accessories'),
('Fashion', 'Clothing and fashion products'),
('Shoes', 'Shoes and footwear'),
('Accessories', 'Fashion and lifestyle accessories');


-- ==========================================
-- SAMPLE PRODUCTS
-- ==========================================

INSERT INTO products
(category_id, name, sku, description, price, stock, low_stock_limit, status)
VALUES

(
    1,
    'Wireless Headphones',
    'WH-1001',
    'Premium wireless headphones',
    12500.00,
    25,
    10,
    'Active'
),

(
    2,
    'Premium T-Shirt',
    'TS-2001',
    'Premium cotton t-shirt',
    3500.00,
    50,
    10,
    'Active'
),

(
    3,
    'Running Shoes',
    'RS-3001',
    'Comfortable running shoes',
    18500.00,
    8,
    10,
    'Active'
);