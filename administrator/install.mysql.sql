-- -----------------------------------------------------
-- Table `com_supplyorder`.`#__so_department_head`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS  `#__so_department_head` (
  `dept_head_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `employee_id` INT(11) NOT NULL ,
  PRIMARY KEY (`dept_head_id`) )
ENGINE = InnoDB


-- -----------------------------------------------------
-- Table `com_supplyorder`.`#__so_accounts`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS  `#__so_accounts` (
  `account_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `employee_id` INT(11) NOT NULL ,
  `dept_head_id` INT(11) NOT NULL ,
  `account_num` VARCHAR(255) NOT NULL ,
  `account_name` VARCHAR(255) NOT NULL ,
  `account_desc` VARCHAR(1024) NULL DEFAULT NULL ,
  PRIMARY KEY (`account_id`) ,
  INDEX `fk_accounts_department_head1` (`dept_head_id` ASC) ,
  CONSTRAINT `fk_accounts_department_head1`
    FOREIGN KEY (`dept_head_id` )
    REFERENCES `#__so_department_head` (`dept_head_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `com_supplyorder`.`#__so_request_status`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS  `#__so_request_status` (
  `request_status_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `status_name` VARCHAR(128) NOT NULL ,
  `status_desc` VARCHAR(255) NOT NULL ,
  `approval_level` TINYINT(4) NOT NULL ,
  PRIMARY KEY (`request_status_id`) )
ENGINE = InnoDB


-- -----------------------------------------------------
-- Table `com_supplyorder`.`#__so_requests`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS  `#__so_requests` (
  `request_id` BIGINT(20) NOT NULL AUTO_INCREMENT ,
  `request_status_id` INT(11) NOT NULL DEFAULT '1' ,
  `order_id` INT NOT NULL ,
  `employee_id` INT(11) NOT NULL ,
  `account_id` INT(11) NOT NULL ,
  `approval_level_required` TINYINT(4) NOT NULL DEFAULT '0' ,
  `vendor` VARCHAR(512) NULL DEFAULT NULL ,
  `item_num` VARCHAR(256) NULL DEFAULT NULL ,
  `item_desc` VARCHAR(2048) NULL DEFAULT NULL ,
  `color` VARCHAR(45) NULL DEFAULT NULL ,
  `url` VARCHAR(512) NULL DEFAULT NULL ,
  `ship_to` VARCHAR(255) NULL DEFAULT NULL ,
  `quantity` INT(11) NULL DEFAULT NULL ,
  `unit_cost` DECIMAL(10,2) NOT NULL DEFAULT '0.00' ,
  `unit_measure` VARCHAR(45) NULL ,
  `request_cost` DECIMAL(10,2) NOT NULL DEFAULT '0.00' ,
  `date_approved` DATETIME NULL DEFAULT NULL ,
  `date_required` DATETIME NULL ,
  `date_submitted` DATETIME NULL ,
  `po_number` VARCHAR(255) NOT NULL DEFAULT '999999' ,
  `date_received` DATETIME NULL ,
  PRIMARY KEY (`request_id`) ,
  INDEX `fk_order_order_status1` (`request_status_id` ASC) ,
  INDEX `fk_requests_order` (`order_id` ASC) ,
  CONSTRAINT `fk_order_order_status1`
    FOREIGN KEY (`request_status_id` )
    REFERENCES  `#__so_request_status` (`request_status_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `com_supplyorder`.`#__so_roles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS  `#__so_roles` (
  `role_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `role_name` VARCHAR(100) NOT NULL COMMENT '	' ,
  PRIMARY KEY (`role_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `com_supplyorder`.`#__so_files`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS  `#__so_files` (
  `file_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `request_id` BIGINT(20) NOT NULL ,
  `employee_id` INT(11) NOT NULL ,
  `file_location` VARCHAR(255) NOT NULL ,
  `file_name` VARCHAR(145) NULL ,
  `date_posted` DATETIME NOT NULL ,
  PRIMARY KEY (`file_id`) ,
  INDEX `order_id_index` (`request_id` ASC) ,
  CONSTRAINT `fk_files_order`
    FOREIGN KEY (`request_id` )
    REFERENCES `#__so_requests` (`request_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `com_supplyorder`.`#__so_comments`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS  `#__so_comments` (
  `comment_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `request_id` BIGINT(20) NOT NULL ,
  `employee_id` INT(11) NOT NULL ,
  `comment_body` MEDIUMTEXT NULL ,
  `date_sent` DATETIME NOT NULL ,
  PRIMARY KEY (`comment_id`) ,
  INDEX `fk_order_comments_order1` (`request_id` ASC) ,
  CONSTRAINT `fk_order_comments_order1`
    FOREIGN KEY (`request_id` )
    REFERENCES `#__so_requests` (`request_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `com_supplyorder`.`#__so_orders`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS  `#__so_orders` (
  `order_id` INT NOT NULL ,
  `order_name` VARCHAR(105) NOT NULL ,
  `order_desc` VARCHAR(1028) NOT NULL ,
  `shipping_cost` VARCHAR(45) NULL ,
  `order_total` VARCHAR(45) NOT NULL ,
  `date_ordered` DATETIME NOT NULL ,
  PRIMARY KEY (`order_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `com_supplyorder`.`#__so_title_roles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS  `#__so_title_roles` (
  `title` VARCHAR(205) NOT NULL ,
  `role_id` INT NOT NULL ,
  INDEX `fk_#__so_title_roles_#__so_roles1` (`role_id` ASC) ,
  CONSTRAINT `fk_#__so_title_roles_#__so_roles1`
    FOREIGN KEY (`role_id` )
    REFERENCES `#__so_roles` (`role_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



  
-- Add role data
INSERT INTO `#__so_roles` (`role_id`, `role_name`) VALUES
(1, 'Registered'),
(2, 'Tier 1 Approver'),
(3, 'Tier 2 Approver'),
(4, 'Accounting');

-- Add request statuses
INSERT INTO `#__so_request_status` (`request_status_id`, `status_name`, `status_desc`, `approval_level`) VALUES
(1, 'Saved', 'Order is saved', 0),
(2, 'Pending Level 1', 'Pending 1st approval', 0),
(3, 'Pending Level 2', 'Pending 2nd approval', 1),
(4, 'Pending Level 3', 'Pending 3rd approval', 2),
(5, 'Pending Purchase', 'Approved and awaiting purchase', 3),
(6, 'Purchased', 'Approved and purchased', 3),
(7, 'Received', 'Received', 3);


-- Sample data for accounts - can be removed after initial testing
INSERT INTO `#__so_department_head` (`dept_head_id`, `employee_id`) VALUES
(1, 62);

INSERT INTO `#__so_accounts` (`account_id`, `employee_id`, `dept_head_id`, `account_num`, `account_name`, `account_desc`) VALUES
(1, 62, 1, '123456', 'Test Account', 'My Account');

-- Sample data for Request Table for new request alter
INSERT INTO `#__so_requests` (`request_id`, `request_status_id`, 
							`order_id`, `employee_id`, `account_id`, 
							`approval_level_required`, `vendor`, 
							`item_num`, `item_desc`, `color`, `url`, 
							`ship_to`, `quantity`, `unit_cost`, 
							`unit_measure`, `request_cost`, 
							`date_approved`, `date_required`, 
							`date_submitted`, `po_number`, 
							`date_received`) VALUES 
							('1', '2', '0', '62', '1', '0', 'CVendor', 
							'1234456789', 'FItem Description 1', 'None', 
							'hclibrary.org', 'ADM@CEN', '11', '0.00', 'Each', 
							'12.34', NULL, '2012-08-31 00:00:00', 
							'2012-08-02 00:00:00', '999999', NULL),
							('2', '2', '0', '62', '1', '0', 'AVendor', 
							'1234456789', 'DItem Description 1', 'None', 
							'hclibrary.org', 'ADM@CEN', '11', '0.00', 'Each', 
							'12.34', NULL, '2012-08-31 00:00:00', 
							'2012-08-02 00:00:00', '999999', NULL),
							('3', '2', '0', '62', '1', '0', 'BVendor', 
							'1234456789', 'CItem Description 1', 'None', 
							'hclibrary.org', 'ADM@CEN', '11', '0.00', 'Each', 
							'12.34', NULL, '2012-08-31 00:00:00', 
							'2012-08-02 00:00:00', '999999', NULL);
							
							
							
							
							
							