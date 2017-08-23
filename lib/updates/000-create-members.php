<?php
/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 8/18/17
 * @package politicalwinds
 */

$queries = [];

$queries[] = "CREATE TABLE `member` (
    `member_id`         varchar(127) NOT NULL,
    `first_name`        varchar(255) NOT NULL,
    `last_name`         varchar(255) NOT NULL,
    `full_name`         varchar(255) DEFAULT NULL,
    `bioguide_id`       varchar(255) DEFAULT NULL,
    `thomas_id`         varchar(255) DEFAULT NULL,
    `govtrack_id`       varchar(255) DEFAULT NULL,
    `opensecrets_id`    varchar(255) DEFAULT NULL,
    `votesmart_id`      varchar(255) DEFAULT NULL,
    `cspan_id`          varchar(255) DEFAULT NULL,
    `wikipedia_id`      varchar(255) DEFAULT NULL,
    `house_history_id`  varchar(255) DEFAULT NULL,
    `ballotpedia_id`    varchar(255) DEFAULT NULL,
    `maplight_id`       varchar(255) DEFAULT NULL,
    `icpsr_id`          varchar(255) DEFAULT NULL,
    `wikidata_id`       varchar(255) DEFAULT NULL,
    `google_entity_id`  varchar(255) DEFAULT NULL,
    `twitter_id`        varchar(255) DEFAULT NULL,
    `instagram_id`      varchar(255) DEFAULT NULL,
    `facebook_id`       varchar(255) DEFAULT NULL,
    `youtube_id`        varchar(255) DEFAULT NULL,
    `gender`            varchar(255) DEFAULT NULL,
    `religion`          varchar(255) DEFAULT NULL,
    `date_of_birth`     date DEFAULT NULL,
    `date_modified`     timestamp DEFAULT CURRENT_TIMESTAMP,
    `date_added`        timestamp DEFAULT '0000-00-00T00:00:00',
    
    PRIMARY KEY(`member_id`)
) ENGINE=InnoDB";

$queries[] = "CREATE TABLE `member_term` (
    `member_term_id`    varchar(127) NOT NULL,
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

$queries[] = "CREATE TABLE `member_committee` (
    `member_committee_id`   varchar(127) NOT NULL,
    `member_id`             varchar(127) NOT NULL,
    `committee_id`          varchar(127) NOT NULL,
    `date_modified`         timestamp DEFAULT CURRENT_TIMESTAMP,
    `date_added`            timestamp DEFAULT '0000-00-00T00:00:00',
    
    PRIMARY KEY(`member_committee_id`),
    KEY(`member_id`),
    KEY(`committee_id`)
    
) ENGINE=InnoDB";

foreach ($queries as $sql)
    if (!core\db\Mysql::conn()->query($sql))
        throw new Exception("Query failed: $sql");