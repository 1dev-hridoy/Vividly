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




-- 1. Products Table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    short_description TEXT,
    long_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

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
