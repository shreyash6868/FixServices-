CREATE DATABASE IF NOT EXISTS fixservices CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fixservices;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(20),
  address TEXT,
  password VARCHAR(255) NOT NULL,
  role ENUM('customer','provider','admin') NOT NULL DEFAULT 'customer',
  provider_status ENUM('pending','approved','rejected') DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  description VARCHAR(255),
  active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  provider_id INT NOT NULL,
  category_id INT NOT NULL,
  title VARCHAR(150) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_service_provider FOREIGN KEY (provider_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_service_category FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB;

CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  service_id INT NOT NULL,
  booking_date DATE NOT NULL,
  booking_time TIME NOT NULL,
  address TEXT NOT NULL,
  problem_description TEXT,
  status ENUM('pending','accepted','rejected','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_booking_customer FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_booking_service FOREIGN KEY (service_id) REFERENCES services(id)
) ENGINE=InnoDB;

CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL UNIQUE,
  customer_id INT NOT NULL,
  service_id INT NOT NULL,
  rating TINYINT NOT NULL,
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT chk_rating CHECK (rating BETWEEN 1 AND 5),
  CONSTRAINT fk_review_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  CONSTRAINT fk_review_customer FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_review_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE complaints (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  booking_id INT NULL,
  subject VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  status ENUM('open','in_progress','resolved') NOT NULL DEFAULT 'open',
  admin_response TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_complaint_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_complaint_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT IGNORE INTO categories (name, description) VALUES
('Electrical','Electrical repair and installation'),
('Plumbing','Pipes, taps and water leaks'),
('Cleaning','Home and room cleaning'),
('AC Repair','AC servicing and repair'),
('Painting','Home painting services'),
('Carpentry','Furniture and woodwork'),
('Appliance Repair','Repair of household appliances'),
('Computer Repair','Laptop and computer support');

-- Create an admin account through register.php using role=admin is intentionally disabled.
-- After registering a normal account, promote it to admin with:
-- UPDATE users SET role='admin' WHERE email='your-email@example.com';
