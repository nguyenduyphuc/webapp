ALTER TABLE `user` ADD `credentials` VARCHAR(255) NULL AFTER `password`;
ALTER TABLE `user` ADD `disclosures` TEXT NULL AFTER `credentials`;

ALTER TABLE `sessions` ADD `agenda` TEXT NULL AFTER `description`;

ALTER TABLE `user_project_access` CHANGE `level` `level` ENUM('attendee','moderator','presenter','admin','exhibitor') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `sponsor_booth_admin` DROP `project_id`;
ALTER TABLE `your_conference_live`.`sponsor_booth_admin` ADD UNIQUE `admin_per_booth` (`user_id`, `booth_id`);

