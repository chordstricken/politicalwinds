<?php
/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 8/18/17
 * @package politicalwinds
 */

$sql = "CREATE TABLE `bills` (
    `bill_id`   varchar(127) NOT NULL,
    `title`     varchar(255) NOT NULL,
    `session`   varchar(255) NOT NULL,
    `link`      text DEFAULT NULL,
    `name`      text DEFAULT NULL,
    
    `date_modified` timestamp DEFAULT CURRENT_TIMESTAMP,
    `date_added`    timestamp DEFAULT '0000-00-00T00:00:00',
    
    PRIMARY KEY(`bill_id`)
) ENGINE=InnoDB";

if (!core\db\Mysql::conn()->query($sql))
    throw new Exception("Query Failed: $sql");