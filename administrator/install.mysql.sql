-- phpMyAdmin SQL Dump
-- version 3.5.2
-- http://www.phpmyadmin.net
--
-- Host: centennial.patuxent.hclibrary.org
-- Generation Time: Jul 24, 2012 at 03:34 PM
-- Server version: 5.5.24-0ubuntu0.12.04.1-log
-- PHP Version: 5.3.10-1ubuntu3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `com_supply_order`
--

-- --------------------------------------------------------

--
-- Table structure for table `#__so_accounts`
--

CREATE TABLE IF NOT EXISTS `#__so_accounts` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `dept_head_id` int(11) NOT NULL,
  `account_num` varchar(255) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_desc` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`account_id`),
  KEY `fk_accounts_department_head1` (`dept_head_id`),
  KEY `fk_accounts_employee1` (`employee_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__so_comments`
--

CREATE TABLE IF NOT EXISTS `#__so_comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` bigint(20) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `comment_body` mediumtext,
  `date_sent` datetime NOT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `fk_order_comments_order1` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__so_department_head`
--

CREATE TABLE IF NOT EXISTS `#__so_department_head` (
  `dept_head_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  PRIMARY KEY (`dept_head_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__so_employee`
--

CREATE TABLE IF NOT EXISTS `#__so_employee` (
  `employee_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(45) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`employee_id`),
  KEY `fk_user_roles1` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__so_files`
--

CREATE TABLE IF NOT EXISTS `#__so_files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` bigint(20) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `file_location` varchar(255) NOT NULL,
  `date_posted` datetime NOT NULL,
  PRIMARY KEY (`file_id`),
  KEY `order_id_index` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__so_orders`
--

CREATE TABLE IF NOT EXISTS `#__so_orders` (
  `order_id` int(11) NOT NULL,
  `order_name` varchar(105) NOT NULL,
  `order_desc` varchar(1028) NOT NULL,
  `shipping_cost` varchar(45) DEFAULT NULL,
  `order_total` varchar(45) NOT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `#__so_requests`
--

CREATE TABLE IF NOT EXISTS `#__so_requests` (
  `request_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `request_status_id` int(11) NOT NULL DEFAULT '1',
  `order_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `approval_level_required` tinyint(4) NOT NULL DEFAULT '0',
  `vendor` varchar(512) DEFAULT NULL,
  `item_num` varchar(256) DEFAULT NULL,
  `item_desc` varchar(2048) DEFAULT NULL,
  `color` varchar(45) DEFAULT NULL,
  `url` varchar(512) DEFAULT NULL,
  `ship_to` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `unit_measure` varchar(45) DEFAULT NULL,
  `request_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `date_approved` datetime DEFAULT NULL,
  `date_required` datetime DEFAULT NULL,
  `date_submitted` datetime DEFAULT NULL,
  `po_number` varchar(255) NOT NULL DEFAULT '999999',
  `date_received` datetime DEFAULT NULL,
  PRIMARY KEY (`request_id`),
  KEY `fk_order_order_status1` (`request_status_id`),
  KEY `fk_order_employee1` (`employee_id`),
  KEY `fk_requests_order` (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__so_request_status`
--

CREATE TABLE IF NOT EXISTS `#__so_request_status` (
  `request_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `status_name` varchar(128) NOT NULL,
  `status_desc` varchar(255) NOT NULL,
  `approval_level` tinyint(4) NOT NULL,
  PRIMARY KEY (`request_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__so_roles`
--

CREATE TABLE IF NOT EXISTS `#__so_roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) NOT NULL COMMENT '	',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `#__so_accounts`
--
ALTER TABLE `#__so_accounts`
  ADD CONSTRAINT `fk_accounts_department_head1` FOREIGN KEY (`dept_head_id`) REFERENCES `#__so_department_head` (`dept_head_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_accounts_employee1` FOREIGN KEY (`employee_id`) REFERENCES `#__so_employee` (`employee_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `#__so_comments`
--
ALTER TABLE `#__so_comments`
  ADD CONSTRAINT `fk_order_comments_order1` FOREIGN KEY (`request_id`) REFERENCES `#__so_requests` (`request_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `#__so_employee`
--
ALTER TABLE `#__so_employee`
  ADD CONSTRAINT `fk_user_roles10` FOREIGN KEY (`role_id`) REFERENCES `#__so_roles` (`role_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `#__so_files`
--
ALTER TABLE `#__so_files`
  ADD CONSTRAINT `fk_files_order` FOREIGN KEY (`request_id`) REFERENCES `#__so_requests` (`request_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `#__so_requests`
--
ALTER TABLE `#__so_requests`
  ADD CONSTRAINT `fk_order_order_status1` FOREIGN KEY (`request_status_id`) REFERENCES `#__so_request_status` (`request_status_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_order_employee1` FOREIGN KEY (`employee_id`) REFERENCES `#__so_employee` (`employee_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_requests_order` FOREIGN KEY (`order_id`) REFERENCES `#__so_orders` (`order_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
