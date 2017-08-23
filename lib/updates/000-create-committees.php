<?php
/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 8/18/17
 * @package politicalwinds
 */

$queries = [];

$queries[] = "CREATE TABLE `committee` (
    `committee_id`  varchar(127) NOT NULL,
    `parent`        varchar(127) DEFAULT NULL,
    `thomas_id`     varchar(255) DEFAULT NULL,
    `type`          varchar(255) DEFAULT NULL,
    `name`          varchar(255) DEFAULT NULL,
    `url`           varchar(255) DEFAULT NULL,
    `minority_url`  varchar(255) DEFAULT NULL,
    `address`       varchar(255) DEFAULT NULL,
    `phone`         varchar(255) DEFAULT NULL,
    `date_modified` timestamp DEFAULT CURRENT_TIMESTAMP,
    `date_added`    timestamp DEFAULT '0000-00-00T00:00:00',
    
    PRIMARY KEY(`committee_id`),
    KEY(`parent`)
    
) ENGINE=InnoDB";


foreach ($queries as $sql)
    if (!core\db\Mysql::conn()->query($sql))
        throw new Exception("Query failed: $sql");