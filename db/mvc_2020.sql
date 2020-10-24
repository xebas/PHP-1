-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema mvc_2020
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `mvc_2020` ;

-- -----------------------------------------------------
-- Schema mvc_2020
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mvc_2020` DEFAULT CHARACTER SET utf8mb4 ;
USE `mvc_2020` ;

-- -----------------------------------------------------
-- Table `mvc_2020`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mvc_2020`.`users` (
  `id` VARCHAR(4) NOT NULL,
  `nick` VARCHAR(45) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `pass` VARCHAR(255) NOT NULL,
  `isEmailConfirmed` INT(11) NOT NULL DEFAULT 0,
  `emailToken` VARCHAR(20) NOT NULL,
  `registerDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `image` VARCHAR(100) NOT NULL DEFAULT 'uploads/user/user-no-image.png',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `mvc_2020`.`projects`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mvc_2020`.`projects` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(45) NOT NULL,
  `description` TEXT NOT NULL,
  `image` VARCHAR(100) NOT NULL,
  `projectDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `users_id` VARCHAR(4) NOT NULL,
  INDEX `fk_projects_users_idx` (`users_id` ASC),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_projects_users`
    FOREIGN KEY (`users_id`)
    REFERENCES `mvc_2020`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;



select * from users;
select * from projects;