-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8 ;
USE `mydb` ;

-- -----------------------------------------------------
-- Table `mydb`.`state`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`state` (
  `id` INT NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`city`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`city` (
  `id` INT NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  `state_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `state_idx` (`state_id` ASC) VISIBLE,
  CONSTRAINT `state`
    FOREIGN KEY (`state_id`)
    REFERENCES `mydb`.`state` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`address`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`address` (
  `id` INT NOT NULL,
  `street` VARCHAR(45) NULL,
  `number` INT NULL,
  `district` VARCHAR(45) NULL,
  `city_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `city_idx` (`city_id` ASC) VISIBLE,
  CONSTRAINT `city`
    FOREIGN KEY (`city_id`)
    REFERENCES `mydb`.`city` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(16) NOT NULL,
  `password` VARCHAR(32) NOT NULL,
  `email` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `full_name` VARCHAR(45) NULL,
  `admin` TINYINT NULL DEFAULT 0,
  `address` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `address_idx` (`address` ASC) VISIBLE,
  CONSTRAINT `address`
    FOREIGN KEY (`address`)
    REFERENCES `mydb`.`address` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`tasks`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`tasks` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(45) NOT NULL,
  `created_at` DATE NULL,
  `finished_at` DATE NULL,
  `due_date` DATE NULL,
  `priority` ENUM('urgent', 'high', 'normal', 'low') NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`task_users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`task_users` (
  `user_id` INT NOT NULL,
  `task_id` INT NOT NULL,
  INDEX `id_user_idx` (`user_id` ASC) VISIBLE,
  INDEX `id_task_idx` (`task_id` ASC) VISIBLE,
  CONSTRAINT `id_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `mydb`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `id_task`
    FOREIGN KEY (`task_id`)
    REFERENCES `mydb`.`tasks` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`task_comments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`task_comments` (
  `id` INT NOT NULL,
  `task_id` INT NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `task_mother_idx` (`task_id` ASC) VISIBLE,
  CONSTRAINT `task_mother`
    FOREIGN KEY (`task_id`)
    REFERENCES `mydb`.`tasks` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;