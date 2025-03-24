-- Table structure for table `customers`
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers`
(
    `id`   INT(11) AUTO_INCREMENT PRIMARY KEY,
    `customer_id`   INT(11) NOT NULL,
    `firstname` VARCHAR(255) NOT NULL,
    `lastname` VARCHAR(255) NOT NULL,
    CONSTRAINT `customer_id` UNIQUE (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `addresses`
DROP TABLE IF EXISTS `addresses`;
CREATE TABLE `addresses`
(
    `id`      INT(11) AUTO_INCREMENT PRIMARY KEY,
    `street`  VARCHAR(255) NOT NULL,
    `addition`  VARCHAR(255) DEFAULT NULL,
    `city`    VARCHAR(255) DEFAULT NULL,
    `zipcode` VARCHAR(10)  DEFAULT NULL,
    `state`   VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `orders`
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders`
(
    `id`               INT(11) AUTO_INCREMENT PRIMARY KEY,
    `customer_id`      INT(11) NOT NULL,
    `delivery_address_id` INT(11) NOT NULL,
    `invoice_address_id`  INT(11) NOT NULL,
    `order_date`       DATETIME NOT NULL,
    `order_status`     VARCHAR(50) NOT NULL,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`),
    FOREIGN KEY (`delivery_address_id`) REFERENCES `addresses`(`id`),
    FOREIGN KEY (`invoice_address_id`) REFERENCES `addresses`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `order_items`
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items`
(
    `id`        INT(11) AUTO_INCREMENT PRIMARY KEY,
    `order_id`  INT(11) NOT NULL,
    `item_category` VARCHAR(50) DEFAULT NULL,
    `item_type` VARCHAR(50) NOT NULL,
    `size_height` INT(11) DEFAULT NULL,
    `size_width` INT(11) DEFAULT NULL,
    `amount`    INT(11) NOT NULL,
    `image`     VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (`order_id`) REFERENCES orders(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
