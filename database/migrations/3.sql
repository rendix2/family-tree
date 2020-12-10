ALTER TABLE `wedding`  ADD `addressId` INT NULL COMMENT 'Address of wedding'  AFTER `townId`,  ADD   INDEX  `K_Wedding_AddressId` (`addressId`);

ALTER TABLE `wedding` ADD CONSTRAINT `FK_Wedding_AddressId` FOREIGN KEY (`addressId`) REFERENCES `address`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `person` CHANGE `callName` `nameCall` VARCHAR(512) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL COMMENT 'Call name of person';