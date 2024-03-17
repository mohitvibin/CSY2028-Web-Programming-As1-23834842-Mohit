-- Adminer 4.8.1 MySQL 11.3.2-MariaDB-1:11.3.2+maria~ubu2204 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `auctions`;
CREATE TABLE `auctions` (
  `End_date` date NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `image_path` varchar(10000) NOT NULL,
  `id` int(250) NOT NULL AUTO_INCREMENT,
  `car_name` varchar(250) NOT NULL,
  `category_id` int(11) NOT NULL,
  `description` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `auctions` (`End_date`, `uploaded_by`, `image_path`, `id`, `car_name`, `category_id`, `description`) VALUES
('2024-03-21',	2,	'uploads/0x0.jpeg',	178,	'New SUV',	11,	'this is car description'),
('2024-03-22',	17,	'uploads/BMW 3 Series Touring.jpg',	192,	'BMW 3 Series Touring',	4,	'this is car description'),
('2024-03-23',	17,	'uploads/tesla.jpeg',	193,	'Tesla Model X',	5,	'this is car description'),
('2024-03-24',	17,	'uploads/Honda Civic.jpg',	194,	'Honda Civic',	6,	'this is car description'),
('2024-03-25',	17,	'uploads/Lamborni Urus.jpg',	195,	'Lamborghini Urus',	9,	'this is car description'),
('2024-03-26',	17,	'uploads/Lexus NX.jpg',	196,	' Lexus NX',	10,	'this is car description'),
('2024-03-27',	17,	'uploads/Toyota Sequoia.jpg',	197,	'Toyota Sequoia',	11,	'this is car description'),
('2024-03-28',	17,	'uploads/Porsche 911.jpg',	198,	'Porsche 911',	20,	'this is car description'),
('2024-03-29',	17,	'uploads/Jeep Wrangler.jpg',	199,	'Jeep Wrangler',	21,	'this is car description'),
('2024-03-30',	17,	'uploads/Jaguar E-Type.jpg',	200,	'Jaguar E-Type',	22,	'this is car description');

DROP TABLE IF EXISTS `auction_bids`;
CREATE TABLE `auction_bids` (
  `user_id` int(250) NOT NULL,
  `amount` varchar(250) NOT NULL,
  `auction_id` int(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `auction_bids` (`user_id`, `amount`, `auction_id`) VALUES
(1,	'5000',	178),
(2,	'700',	190),
(17,	'200',	191),
(17,	'200',	192),
(17,	'300',	193),
(17,	'400',	194),
(17,	'500',	195),
(17,	'600',	196),
(17,	'700',	197),
(17,	'800',	198),
(17,	'900',	199),
(17,	'1000',	200),
(17,	'200',	201);

DROP TABLE IF EXISTS `Category_only`;
CREATE TABLE `Category_only` (
  `category_uploaded_by` varchar(255) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `Category_only` (`category_uploaded_by`, `id`, `category_name`) VALUES
('Sunil',	4,	'Estate'),
('Sunil',	5,	'Electric'),
('Sunil',	6,	'Sedan'),
('Sunil',	9,	'Sports'),
('Sunil',	10,	'Hybrid'),
('Sunil',	11,	'SUV'),
('Sunil',	20,	'Coupe'),
('Sunil',	21,	'4x4'),
('Sunil',	22,	'Vintage');

DROP TABLE IF EXISTS `register`;
CREATE TABLE `register` (
  `id` int(250) NOT NULL AUTO_INCREMENT,
  `Role` varchar(250) NOT NULL,
  `Username` varchar(250) NOT NULL,
  `Password` varchar(250) NOT NULL,
  `Email` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `register` (`id`, `Role`, `Username`, `Password`, `Email`) VALUES
(2,	'admin',	'Sunil',	'$2y$10$QYeIbegBAVjFQZS6B4VKqOZIlChAvCfFxBNkvOLdZJABymKPa7QEe',	'mohitbudhathoki040@gmail.com'),
(17,	'user',	'Sunil Chaulagain',	'$2y$10$hjJ.FZmfQggpqc9nJ6lm5O6FnFAPpe3J.Djte3VyZrnYYj49X.LXi',	'sunilchaulagain96@gmail.com');

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `auction_id` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `review_text` varchar(500) NOT NULL,
  `review_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `reviews` (`auction_id`, `id`, `user_id`, `reviewer_id`, `review_text`, `review_date`) VALUES
(178,	14,	2,	15,	'Nice Car Sunil.',	'0000-00-00 00:00:00'),
(182,	27,	15,	2,	'Nice Car Rashik',	'0000-00-00 00:00:00');

-- 2024-03-17 06:14:32
