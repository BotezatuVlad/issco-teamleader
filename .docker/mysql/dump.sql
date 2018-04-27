-- ---------------------------------------------------------
-- Drop customers table
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `customers`;

-- ---------------------------------------------------------
-- Create customers table
-- ---------------------------------------------------------
CREATE TABLE `customers` (
	`id` INT(10) UNSIGNED AUTO_INCREMENT,
	`name` VARCHAR(45) NOT NULL,
	`since` DATE NOT NULL,
	`revenue` DECIMAL(10, 2) UNSIGNED NOT NULL,
	 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci ROW_FORMAT=COMPRESSED;

-- ---------------------------------------------------------
-- Insert data in customers table
-- ---------------------------------------------------------
INSERT INTO `customers` (`id`, `name`, `since`, `revenue`) VALUES
(1, 'Coca Cola', '2014-06-28', 492.12),
(2, 'Teamleader', '2015-01-15', 1505.95),
(3, 'Jeroen De Wit', '2016-02-11', 0.00);

-- ---------------------------------------------------------
-- Drop customers table
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `products`;

-- ---------------------------------------------------------
-- Create customers table
-- ---------------------------------------------------------
CREATE TABLE `products` (
	`id` VARCHAR(45) NOT NULL,
	`description` VARCHAR(255) NOT NULL,
	`category` INT(10) UNSIGNED NOT NULL,
	`price` DECIMAL(10, 2) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci ROW_FORMAT=COMPRESSED;

-- ---------------------------------------------------------
-- Insert data in customers table
-- ---------------------------------------------------------
INSERT INTO `products` (`id`, `description`, `category`, `price`) VALUES
('A101', 'Screwdriver', 1, 9.75),
('A102', 'Electric screwdriver', 1, 49.50),
('B101', 'Basic on-off switch', 2, 4.99),
('B102', 'Press button', 2, 4.99),
('B103', 'Switch with motion detector', 2, 12.95);
