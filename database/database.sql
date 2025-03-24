--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders`
(
    `customer_id`      int(11) DEFAULT NULL,
    `customer_name`    varchar(12) DEFAULT NULL,
    `delivery_address` varchar(61) DEFAULT NULL,
    `invoice_address`  varchar(77) DEFAULT NULL,
    `order`            text,
    `order_date`       varchar(19) DEFAULT NULL,
    `order_status`     varchar(7)  DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Data for table `orders`
--

INSERT INTO `orders`
VALUES (1000001, 'Bruce Wayne', 'Bruce Wayne, 1007 Mountain Drive, Gotham', 'Bruce Wayne, 1007 Mountain Drive, Gotham',
        '{\"items\":[{\"item_type\":\"poster\",\"size_height\":80,\"size_width\":60,\"amount\":2,\"image\":\"/files/poster_1.jpg\"},{\"item_type\":\"poster\",\"size_height\":80,\"size_width\":60,\"amount\":1,\"image\":\"/files/poster_2.jpg\"}]}',
        '2021-01-01 00:00:00', 'ordered'),
       (1000001, 'Bruce Wayne', 'Bruce Wayne, 1007 Mountain Drive, Gotham', 'Bruce Wayne, 1007 Mountain Drive, Gotham',
        '{\"items\":[{\"item_type\":\"canvas\",\"size_height\":80,\"size_width\":80,\"amount\":1,\"image\":\"/files/canvas_1.jpg\"}]}',
        '2021-01-01 00:00:00', 'shipped'),
       (1000002, 'Peter Parker', 'Maybelle Parker, 15th Street, Queens, New York City, New York',
        'Peter Parker, 20 Ingram Street, Forest Hills, Queens, New York City, New York',
        '{\"items\":[{\"item_type\":\"canvas\",\"size_height\":80,\"size_width\":80,\"amount\":1,\"image\":\"/files/canvas_1.jpg\"},{\"item_type\":\"poster\",\"size_height\":20,\"size_width\":20,\"amount\":2,\"image\":\"/files/poster_123.jpg\"},{\"item_type\":\"canvas\",\"size_height\":80,\"size_width\":80,\"amount\":3,\"image\":\"/files/canvas_2.jpg\"},{\"item_category\":\"accessory\",\"item_type\":\"tape\",\"amount\":4},{\"item_category\":\"frame\",\"item_type\":\"wooden-frame-1\",\"size_height\":80,\"size_width\":80,\"amount\":3},{\"item_category\":\"frame\",\"item_type\":\"wooden-frame-2\",\"size_height\":20,\"size_width\":20,\"amount\":2}]}',
        '2021-01-01 00:00:00', 'slicing');
