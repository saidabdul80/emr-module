-- This table definition is loaded and then executed when the OpenEMR interface's install button is clicked.
/* CREATE TABLE IF NOT EXISTS `mod_sleep`(
    `id` INT(20)  PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `pid` VARCHAR(255) NOT NULL,
    `total_minutes_asleep` INT(11) NOT NULL,
    `day_date` DATE(6) NOT NULL,
); */


CREATE TABLE IF NOT EXISTS  `pghd_observation` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(200) NOT NULL,
  `subject` varchar(200) NOT NULL COMMENT 'Patient id',
  `category` varchar(200) NOT NULL COMMENT 'conforms with fhirs server',
  `code` varchar(300) NOT NULL,
  `value` varchar(200) NOT NULL,
  `effective` datetime(6) DEFAULT NULL COMMENT 'effective date',
  `device` varchar(200) DEFAULT NULL,
  `interpretation` varchar(1000) DEFAULT NULL,
  `name` varchar(1000) DEFAULT NULL,
  `components` longtext DEFAULT NULL COMMENT 'json',
   PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `pghd_observation`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `pghd_observation`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;