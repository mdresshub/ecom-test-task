<?php

declare(strict_types=1);

namespace Shop\Migration;

use PDO;
use PDOException;

class MigrateNormalized
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function migrate(): void
    {
        try {
            $this->pdo->beginTransaction();

            $this->createNewTables();

            $this->migrateCustomers();
            $this->migrateAddresses();
            $this->migrateOrders();
            $this->migrateOrderItems();

            $this->pdo->commit();
        } catch (PDOException $exception) {
            $this->pdo->rollBack();

            throw $exception;
        }
    }

    private function createNewTables(): void
    {
        $sql = file_get_contents(__DIR__ . '/../../database/database_normalized.sql');
        $this->pdo->exec($sql);
    }

    private function migrateCustomers(): void
    {
        $sql = 'INSERT INTO customers (customer_id, firstname, lastname)
                SELECT DISTINCT customer_id, SUBSTRING_INDEX(customer_name, " ", 1) AS firstname, SUBSTRING_INDEX(customer_name, " ", -1) AS lastname
                FROM orders_origin';

        $this->pdo->exec($sql);
    }

    private function migrateAddresses(): void
    {
        $sql = 'INSERT INTO addresses (street, addition, city, state, zipcode)
                SELECT DISTINCT
                    TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(address, ",", 2), ",", -1)) AS street,
                    IF(LENGTH(address) - LENGTH(REPLACE(address, ",", "")) = 5, TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(address, ",", 3), ",", -1)), NULL) AS addition,
                    CASE
                        WHEN LENGTH(address) - LENGTH(REPLACE(address, ",", "")) = 5 THEN TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(address, ",", 5), ",", -1))
                        WHEN LENGTH(address) - LENGTH(REPLACE(address, ",", "")) = 4 THEN TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(address, ",", 4), ",", -1))
                    END AS city,
                    CASE
                        WHEN LENGTH(address) - LENGTH(REPLACE(address, ",", "")) = 5 THEN TRIM(SUBSTRING_INDEX(address, ",", -1))
                        WHEN LENGTH(address) - LENGTH(REPLACE(address, ",", "")) = 4 THEN TRIM(SUBSTRING_INDEX(address, ",", -1))
                    END AS state,
                    CASE
                        WHEN LENGTH(address) - LENGTH(REPLACE(address, ",", "")) = 5 THEN TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(address, ",", 4), ",", -1))
                        WHEN LENGTH(address) - LENGTH(REPLACE(address, ",", "")) = 4 THEN TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(address, ",", 3), ",", -1))
                        WHEN LENGTH(address) - LENGTH(REPLACE(address, ",", "")) = 2 THEN TRIM(SUBSTRING_INDEX(address, ",", -1))
                    END AS zipcode
                FROM (
                    SELECT delivery_address AS address FROM orders_origin
                    UNION
                    SELECT invoice_address AS address FROM orders_origin
                ) AS combined_addresses';

        $this->pdo->exec($sql);
    }

    private function migrateOrders(): void
    {
        $sql = 'INSERT INTO orders (customer_id, delivery_address_id, invoice_address_id, order_date, order_status)
                SELECT 
                    (SELECT id FROM customers c WHERE c.customer_id = o.customer_id) AS customer_id,
                    (SELECT id FROM addresses a WHERE a.street = TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(o.delivery_address, ",", 2), ",", -1)) LIMIT 1) AS delivery_address_id,
                    (SELECT id FROM addresses a WHERE a.street = TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(o.invoice_address, ",", 2), ",", -1)) LIMIT 1) AS invoice_address_id,
                    o.order_date,
                    o.order_status
                FROM orders_origin o';

        $this->pdo->exec($sql);
    }

    private function migrateOrderItems(): void
    {
        $sql = 'INSERT INTO order_items (order_id, item_type, item_category, size_height, size_width, amount, image)
                SELECT
                    (SELECT id FROM orders o WHERE (SELECT customer_id FROM customers c WHERE c.id = o.customer_id LIMIT 1) = oo.customer_id AND o.order_date = oo.order_date AND o.order_status = oo.order_status LIMIT 1) AS order_id,
                    JSON_UNQUOTE(JSON_EXTRACT(oi.item, "$.item_type")) AS item_type,
                    JSON_UNQUOTE(JSON_EXTRACT(oi.item, "$.item_category")) AS item_category,
                    JSON_UNQUOTE(JSON_EXTRACT(oi.item, "$.size_height")) AS size_height,
                    JSON_UNQUOTE(JSON_EXTRACT(oi.item, "$.size_width")) AS size_width,
                    JSON_UNQUOTE(JSON_EXTRACT(oi.item, "$.amount")) AS amount,
                    JSON_UNQUOTE(JSON_EXTRACT(oi.item, "$.image")) AS image
                FROM orders_origin oo
                         CROSS JOIN JSON_TABLE(oo.order, "$.items[*]" COLUMNS (
                    item JSON PATH "$"
                )) AS oi';

        $this->pdo->exec($sql);
    }
}
