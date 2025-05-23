-- Admin table
CREATE TABLE admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admins (name, email, password, created_at) 
VALUES ('hridoy09bg', 'hridoy@gmail.com', '$2y$10$wHpoZ4HHsUwzZy.kf6ZtE.QjP64CldpdWDjTbhJ4XU9OvGbKvhQZG', '2025-05-23 19:56:18');
