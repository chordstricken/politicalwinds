<?php
/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 8/18/17
 * @package politicalwinds
 */

$queries = [];

$queries[] = "CREATE TABLE `bill` (
    `bill_id`       varchar(127) NOT NULL,
    `code`          varchar(255) NOT NULL,
    `amends_bill`   varchar(127) DEFAULT NULL,
    `title`         text DEFAULT NULL,
    `title_senate`  text DEFAULT NULL,
    `title_house`   text DEFAULT NULL,
    `summary`       mediumtext DEFAULT NULL,
    `session`       varchar(255) DEFAULT NULL,
    `link`          text DEFAULT NULL,
    `sponsor`       varchar(127) DEFAULT NULL,
    `document_full` mediumtext DEFAULT NULL,               
    
    `date_modified` timestamp DEFAULT CURRENT_TIMESTAMP,
    `date_added`    timestamp DEFAULT '0000-00-00T00:00:00',
    
    PRIMARY KEY(`bill_id`),
    KEY(`code`)
    
) ENGINE=InnoDB";

$queries[] = "CREATE TABLE `bill_action` (
    `bill_action_id`    varchar(127) NOT NULL,
    `bill_id`           varchar(127) NOT NULL,
    `index`             int(11) NOT NULL,
    `date`              date DEFAULT NULL,
    `chamber`           varchar(255) DEFAULT NULL,
    `note`              text DEFAULT NULL,
    
    `date_modified` timestamp DEFAULT CURRENT_TIMESTAMP,
    `date_added`    timestamp DEFAULT '0000-00-00T00:00:00',
    
    PRIMARY KEY(`bill_action_id`),
    KEY(`bill_id`)
    
) ENGINE=InnoDB";

$queries[] = "CREATE TABLE `bill_cosponsor` (
    `bill_cosponsor_id` varchar(127) NOT NULL,
    `bill_id`           varchar(127) NOT NULL,
    `member_id`         varchar(127) NOT NULL,
    `date`              date DEFAULT NULL,
    
    `date_modified` timestamp DEFAULT CURRENT_TIMESTAMP,
    `date_added`    timestamp DEFAULT '0000-00-00T00:00:00',
    
    PRIMARY KEY(`bill_cosponsor_id`),
    KEY(`bill_id`),
    KEY(`member_id`)
    
) ENGINE=InnoDB";


$queries[] = "CREATE TABLE `bill_committee` (
    `bill_committee_id` varchar(127) NOT NULL,
    `bill_id`           varchar(127) NOT NULL,
    `committee_id`      varchar(127) NOT NULL,
    `date`              date DEFAULT NULL,
    
    `date_modified` timestamp DEFAULT CURRENT_TIMESTAMP,
    `date_added`    timestamp DEFAULT '0000-00-00T00:00:00',
    
    PRIMARY KEY(`bill_committee_id`),
    KEY(`bill_id`)
    
) ENGINE=InnoDB";


foreach ($queries as $sql)
    if (!core\db\Mysql::conn()->query($sql))
        throw new Exception("Query failed: $sql");