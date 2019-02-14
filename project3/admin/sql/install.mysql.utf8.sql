-- Table structure for table `#__hwcategories`
--

DROP TABLE IF EXISTS `#__hwcategories`;

CREATE TABLE `#__hwcategories` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10)     NOT NULL DEFAULT '0',
	`access_id` INT(10) NOT NULL DEFAULT '6',
	`title` VARCHAR(25) NOT NULL,
	`images` varchar(255) NOT NULL,
	`detail` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	`spublishdate` datetime NOT NULL,
	`epublishdate` datetime NOT NULL,
	`featured` int(1) NOT NULL,
	`lang` VARCHAR(25)    NOT NULL,
	`created_time`  DATETIME    NOT NULL DEFAULT '0000-00-00 00:00:00',
	`created_user_id`  INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`published` tinyint(4) NOT NULL DEFAULT '1',
	`modified_time`  DATETIME    NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified_user_id`  INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`params`   VARCHAR(1024) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
)
	ENGINE =MyISAM
	AUTO_INCREMENT =0
	DEFAULT CHARSET =utf8;
	
-- --------------------------------------------------------

--
-- Table structure for table `#__hwitems`
--

DROP TABLE IF EXISTS `#__hwitems`;

CREATE TABLE IF NOT EXISTS `#__hwitems` (
    `id` INT(11)     NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10)     NOT NULL DEFAULT '0',
	`access_id` INT(10) NOT NULL DEFAULT '6',
	`name` VARCHAR(25) NOT NULL,
	`images` varchar(255) NOT NULL,
	`description` varchar(255),
	`detail` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	`spublishdate` datetime NOT NULL,
	`epublishdate` datetime NOT NULL,
	`created_time`  DATETIME    NOT NULL DEFAULT '0000-00-00 00:00:00',
	`created_user_id`  INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`catid`	    int(11)    NOT NULL DEFAULT '0',
	`featured` int(1) NOT NULL,
	`lang` VARCHAR(25)    NOT NULL,
	`published` tinyint(4) NOT NULL DEFAULT '1',
	`modified_time`  DATETIME    NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified_user_id`  INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`params`   VARCHAR(1024) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) 
	ENGINE =MyISAM
	AUTO_INCREMENT =0
	DEFAULT CHARSET =utf8;

-- ----------------------------------------------
