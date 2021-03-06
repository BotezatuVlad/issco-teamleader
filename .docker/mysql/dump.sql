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
-- Drop categories table
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `categories`;

-- ---------------------------------------------------------
-- Create categories table
-- ---------------------------------------------------------
CREATE TABLE `categories` (
	`id` INT(10) UNSIGNED AUTO_INCREMENT,
	`name` VARCHAR(45) NOT NULL,
	 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci ROW_FORMAT=COMPRESSED;

-- ---------------------------------------------------------
-- Insert data in categories table
-- ---------------------------------------------------------
INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Tools'),
(2, 'Switches');

-- ---------------------------------------------------------
-- Drop products table
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `products`;

-- ---------------------------------------------------------
-- Create products table
-- ---------------------------------------------------------
CREATE TABLE `products` (
	`id` VARCHAR(45) NOT NULL,
	`description` VARCHAR(255) NOT NULL,
	`category` INT(10) UNSIGNED NOT NULL,
	`price` DECIMAL(10, 2) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci ROW_FORMAT=COMPRESSED;

-- ---------------------------------------------------------
-- Insert data in products table
-- ---------------------------------------------------------
INSERT INTO `products` (`id`, `description`, `category`, `price`) VALUES
('A101', 'Screwdriver', 1, 9.75),
('A102', 'Electric screwdriver', 1, 49.50),
('B101', 'Basic on-off switch', 2, 4.99),
('B102', 'Press button', 2, 4.99),
('B103', 'Switch with motion detector', 2, 12.95);

-- ---------------------------------------------------------
-- Drop categories_discounts table
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `categories_discounts`;

-- ---------------------------------------------------------
-- Create categories_discounts table
-- ---------------------------------------------------------
CREATE TABLE `categories_discounts` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`category` INT(10) UNSIGNED NOT NULL,
	`discpountType` VARCHAR(45) NOT NULL,
	`discountName` VARCHAR(255) NOT NULL,
	`discountPercentage` DECIMAL(10,2) UNSIGNED,
	`discountBuy` INT(10) UNSIGNED,
	`discountBonus` INT(10) UNSIGNED,
	 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci ROW_FORMAT=COMPRESSED;

-- ---------------------------------------------------------
-- Insert data in categories_discounts table
-- ---------------------------------------------------------
INSERT INTO `categories_discounts` (`id`, `category`, `discpountType`, `discountName`, `discountPercentage`, `discountBuy`, `discountBonus`) VALUES
(1, 2, 'bonusProduct', 'when you buy five, you get a sixth for free', NULL, 5, 1),
(2, 1, 'percentageOfCheapest', 'You get a 20% discount on the cheapest product', 20, null, null);
