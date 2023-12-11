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
  `email` VARCHAR(255) NOT NULL UNIQUE,
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
  `id` INT NOT NULL AUTO_INCREMENT,
  `task_id` INT NULL,
  `description` TEXT NULL,
  `image` LONGTEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `task_mother_idx` (`task_id` ASC) VISIBLE,
  CONSTRAINT `task_mother`
    FOREIGN KEY (`task_id`)
    REFERENCES `mydb`.`tasks` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- Inserindo dados de teste para state
INSERT INTO `state` (`id`, `name`) VALUES
(1, 'São Paulo'),
(2, 'Rio de Janeiro'),
(3, 'Minas Gerais');

-- Inserindo dados de teste para city
INSERT INTO `city` (`id`, `name`, `state_id`) VALUES
(1, 'São Paulo City', 1),
(2, 'Rio de Janeiro City', 2),
(3, 'Belo Horizonte', 3);

-- Inserindo dados de teste para address
INSERT INTO `address` (`id`, `street`, `number`, `district`, `city_id`) VALUES
(1, 'Avenida Paulista', 123, 'Bela Vista', 1),
(2, 'Copacabana Beach', 456, 'Copacabana', 2),
(3, 'Savassi Street', 789, 'Savassi', 3);

-- Inserindo dados de teste para user
INSERT INTO `user` (`id`, `username`, `password`, `email`, `full_name`, `admin`, `address`) VALUES
(1, 'user1', 'pass1', 'user1@example.com', 'User One', 0, 1),
(2, 'admin', 'adminpass', 'admin@example.com', 'Administrator', 1, 2),
(3, 'user2', 'pass2', 'user2@example.com', 'User Two', 0, 3);

-- Inserindo dados de teste para tasks
INSERT INTO `tasks` (`id`, `title`, `created_at`, `finished_at`, `due_date`, `priority`) VALUES
(1, 'Task 1', '2023-12-10', '2023-12-15', '2023-12-20', 'high'),
(2, 'Task 2', '2023-12-11', NULL, '2023-12-25', 'normal'),
(3, 'Task 3', '2023-12-12', NULL, '2023-12-30', 'low');

-- Inserindo dados de teste para task_users
INSERT INTO `task_users` (`user_id`, `task_id`) VALUES
(1, 1),
(2, 1),
(3, 2),
(1, 3);

-- Inserindo dados de teste para task_comments com imagens em base64
INSERT INTO `task_comments` (`id`, `task_id`, `description`, `image`) VALUES
(2, 1, 'Comentario na Task 1', ''),
(3, 3, 'Comentario na Task 3', '');


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;