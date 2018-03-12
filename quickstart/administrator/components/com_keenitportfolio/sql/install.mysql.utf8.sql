CREATE TABLE IF NOT EXISTS `#__keenitportfolio_portfolio` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`project_name` VARCHAR(255)  NOT NULL ,
`client_name` VARCHAR(255)  NOT NULL ,
`final_date` DATE NOT NULL ,
`project_url` VARCHAR(255)  NOT NULL ,
`category` INT(11)  NOT NULL ,
`image` VARCHAR(255)  NOT NULL ,
`desc` TEXT NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

