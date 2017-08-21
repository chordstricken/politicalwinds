<?php
/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 8/18/17
 * @package politicalwinds
 */

$queries = [];

$queries[] = "CREATE TABLE `members` (
    `member_id`     varchar(127) NOT NULL,
    `first_name`    varchar(255) NOT NULL,
    `last_name`     varchar(255) NOT NULL,
    `full_name`     varchar(255) DEFAULT NULL,
    `bioguide_id`   varchar(255) DEFAULT NULL,
    `thomas_id`     varchar(255) DEFAULT NULL,
    `govtrack_id`   varchar(255) DEFAULT NULL,
    `twitter_id`    varchar(255) DEFAULT NULL,
    `instagram_id`  varchar(255) DEFAULT NULL,
    `facebook_id`   varchar(255) DEFAULT NULL,
    `gender`        varchar(255) DEFAULT NULL,
    `date_of_birth` date DEFAULT NULL,
    `date_modified` timestamp DEFAULT CURRENT_TIMESTAMP,
    `date_added`    timestamp DEFAULT '0000-00-00T00:00:00',
    
    PRIMARY KEY(`member_id`)
) ENGINE=InnoDB";

$queries[] = "CREATE TABLE `members_terms` (
    `member_term_id`    bigint(20) NOT NULL AUTO_INCREMENT,
    `member_id`         varchar(127) NOT NULL,
    `start`             date DEFAULT NULL,
    `end`               date DEFAULT NULL,
    `how`               varchar(255) DEFAULT NULL,
    `party`             varchar(255) DEFAULT NULL,
    `type`              varchar(255) DEFAULT NULL,
    `address`           varchar(255) DEFAULT NULL,
    `district`          varchar(255) DEFAULT NULL,
    `office`            varchar(255) DEFAULT NULL,
    `phone`             varchar(255) DEFAULT NULL,
    `state`             varchar(255) DEFAULT NULL,
    `url`               varchar(255) DEFAULT NULL,
    `date_modified` timestamp DEFAULT CURRENT_TIMESTAMP,
    `date_added`    timestamp DEFAULT '0000-00-00T00:00:00',
    
    PRIMARY KEY(`member_term_id`),
    KEY(`member_id`)
    
) ENGINE=InnoDB";

$queries[] = "CREATE TABLE `members_committees` (
    `member_committee_id`   bigint(20) NOT NULL AUTO_INCREMENT,
    `member_id`             varchar(127) NOT NULL,
    `committee_id`          varchar(255) NOT NULL,
    `date_modified`         timestamp DEFAULT CURRENT_TIMESTAMP,
    `date_added`            timestamp DEFAULT '0000-00-00T00:00:00',
    
    PRIMARY KEY(`member_committee_id`),
    KEY(`member_id`),
    KEY(`committee_id`)
    
) ENGINE=InnoDB";

foreach ($queries as $sql)
    if (!core\db\Mysql::conn()->query($sql))
        throw new Exception("Query failed: $sql");