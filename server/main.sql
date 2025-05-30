-- Admin table
CREATE TABLE admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admins (name, email, password, created_at) 
VALUES ('hridoy09bg', 'hridoy@gmail.com', '$2y$10$BY6uFabgmHxr9GOqK/ju.OXoWWu3tzxa2c391YikCkX4DPHIpi.0i', '2025-05-23 19:56:18');


CREATE TABLE category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




-- -- 1. Products Table
-- CREATE TABLE products (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     title VARCHAR(255) NOT NULL,
--     short_description TEXT,
--     long_description TEXT,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- ) ENGINE=InnoDB;
CREATE TABLE `products` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `short_description` TEXT,
  `long_description` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `price` DECIMAL(10,2) DEFAULT 0.00,
  `stock` INT DEFAULT 0,
  `category_id` INT UNSIGNED,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 2. Sizes Table (Static)
CREATE TABLE sizes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(10) UNIQUE NOT NULL
) ENGINE=InnoDB;

-- Populate sizes
INSERT INTO sizes (label) VALUES 
('XXS'), ('XS'), ('S'), ('M'), ('L'), 
('XL'), ('XXL'), ('3XL'), ('4XL'), ('5XL'), 
('6XL'), ('7XL'), ('8XL');

-- 3. Product Sizes Table (Many-to-Many)
CREATE TABLE product_sizes (
    product_id INT,
    size_id INT,
    PRIMARY KEY (product_id, size_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (size_id) REFERENCES sizes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Colors Table (Static or dynamic)
CREATE TABLE colors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
) ENGINE=InnoDB;

-- Example color insert
INSERT INTO colors (name) VALUES ('Red'), ('Blue'), ('Black'), ('White');

-- 5. Product Colors Table (Many-to-Many)
CREATE TABLE product_colors (
    product_id INT,
    color_id INT,
    PRIMARY KEY (product_id, color_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (color_id) REFERENCES colors(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Product Images Table
CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    image_path VARCHAR(255) NOT NULL,
    image_type ENUM('main', 'additional') DEFAULT 'additional',
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;



SELECT * FROM products
WHERE category_id IS NULL
   OR category_id NOT IN (SELECT id FROM category);


CREATE TABLE `carousels` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `image_path` VARCHAR(255) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Users table to store customer information
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Addresses table to store shipping/billing addresses
CREATE TABLE addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address_type ENUM('shipping', 'billing') NOT NULL,
    street_address VARCHAR(255) NOT NULL,
    division_id INT NOT NULL,
    division_name VARCHAR(100) NOT NULL,
    district_id INT NOT NULL,
    district_name VARCHAR(100) NOT NULL,
    upzila_id INT NOT NULL,
    upzila_name VARCHAR(100) NOT NULL,
    union_id INT NOT NULL,
    union_name VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    save_for_future BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Orders table to store order details
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    custom_order_id VARCHAR(20) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    shipping_address_id INT NOT NULL,
    billing_address_id INT,
    subtotal DECIMAL(10,2) NOT NULL,
    shipping_cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    tax DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    shipping_method ENUM('standard', 'express', 'overnight') NOT NULL,
    payment_method ENUM('card', 'paypal', 'apple_pay', 'bkash', 'nagad', 'rocket') NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (shipping_address_id) REFERENCES addresses(id) ON DELETE RESTRICT,
    FOREIGN KEY (billing_address_id) REFERENCES addresses(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Order Items table to store individual items in an order
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    color_id INT,
    size_id INT,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    FOREIGN KEY (color_id) REFERENCES colors(id) ON DELETE RESTRICT,
    FOREIGN KEY (size_id) REFERENCES sizes(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Payments table to store payment details
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method ENUM('card', 'paypal', 'apple_pay', 'bkash', 'nagad', 'rocket') NOT NULL,
    transaction_id VARCHAR(50),
    mobile_number VARCHAR(20),
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;



-- Site Settings Table
CREATE TABLE site_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    site_name VARCHAR(100) NOT NULL,
    site_description TEXT,
    site_logo VARCHAR(255),
    footer_tagline VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Social Links Table
CREATE TABLE social_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    facebook_url VARCHAR(255),
    twitter_url VARCHAR(255),
    instagram_url VARCHAR(255),
    linkedin_url VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contact Info Table
CREATE TABLE contact_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    phone_number VARCHAR(20),
    address TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Payment Info Table
CREATE TABLE payment_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    bkash_number VARCHAR(20),
    bkash_note VARCHAR(100),
    nagad_number VARCHAR(20),
    nagad_note VARCHAR(100),
    rocket_number VARCHAR(20),
    rocket_note VARCHAR(100),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample Data
INSERT INTO site_settings (site_name, site_description, site_logo, footer_tagline) VALUES
    ('My E-Commerce Store', 'Your one-stop shop for everything', 'uploads/logo_20250525_1006.png', '© 2025 Your Company. All rights reserved.');

INSERT INTO social_links (facebook_url, twitter_url, instagram_url, linkedin_url) VALUES
    ('https://facebook.com/hridoy', 'https://twitter.com/hridoy', 'https://instagram.com/hridoy', 'https://linkedin.com/in/hridoy');

INSERT INTO contact_info (phone_number, address) VALUES
    ('+8801234567890', '123 Main St, Dhaka, Bangladesh');

INSERT INTO payment_info (bkash_number, bkash_note, nagad_number, nagad_note, rocket_number, rocket_note) VALUES
    ('+8801712345678', 'Send as Personal', '+8801812345678', 'Use for payment only', '+8801912345678', 'Add order ID in reference');

