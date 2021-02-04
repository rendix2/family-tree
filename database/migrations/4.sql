CREATE TABLE `file`
(
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `personId`    INT          NOT NULL,
    `name`        VARCHAR(512) NOT NULL,
    `extension`   VARCHAR(5)   NOT NULL,
    `size`        INT          NOT NULL,
    `description` TEXT         NOT NULL,
    PRIMARY KEY (`id`),
    INDEX         `K_File_PersonId` (`personId`)
) ENGINE = InnoDB;

ALTER TABLE `file`
    ADD CONSTRAINT `FK_File_PersonId` FOREIGN KEY (`personId`) REFERENCES `person` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `file` CHANGE `name` `newName` VARCHAR(512) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL;
ALTER TABLE `file` ADD `originName` VARCHAR(512) NOT NULL COMMENT 'Origin name of file' AFTER `personId`;
ALTER TABLE `file` ADD `type` VARCHAR(50) NOT NULL COMMENT 'Type of file' AFTER `extension`;

