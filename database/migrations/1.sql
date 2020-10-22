ALTER TABLE `person`
    ADD `birthAddressId` INT NULL DEFAULT NULL COMMENT 'Person birth address ID' AFTER `birthTownId`,
    ADD INDEX `K_Person_BirthAddressId` (`birthAddressId`);

ALTER TABLE `person`
    ADD `deathAddressId` INT NULL DEFAULT NULL COMMENT 'Person death address ID' AFTER `deathTownId`,
    ADD INDEX `K_Person_DeathAddressId` (`deathAddressId`);

ALTER TABLE `person`
    ADD `gravedAddressId` INT NULL DEFAULT NULL COMMENT 'Person graved address ID' AFTER `gravedTownId`,
    ADD INDEX `K_Person_GravedAddressId` (`gravedAddressId`);

ALTER TABLE `person`
    ADD CONSTRAINT `FK_Person_BirthAddressId` FOREIGN KEY (`birthAddressId`) REFERENCES `address`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `person`
    ADD CONSTRAINT `FK_Person_DeathAddressId` FOREIGN KEY (`deathAddressId`) REFERENCES `address`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `person`
    ADD CONSTRAINT `FK_Person_GravedAddressId` FOREIGN KEY (`gravedAddressId`) REFERENCES `address`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--    UPDATE person set birthAddressId = null, deathAddressId = null, gravedAddressId = null