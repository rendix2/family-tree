ALTER TABLE `address` ADD `gps` VARCHAR(100) NULL COMMENT 'GPS position' AFTER `countryId`;
ALTER TABLE `town` ADD `gps` VARCHAR(100) NULL COMMENT 'GPS position' AFTER `zipCode`;
