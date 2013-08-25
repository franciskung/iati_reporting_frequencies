-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 25, 2013 at 01:52 PM
-- Server version: 5.5.24-log
-- PHP Version: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `iatireportcard`
--

-- --------------------------------------------------------

--
-- Table structure for table `iati_update`
--

CREATE TABLE IF NOT EXISTS `iati_update` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iati_activity_id` varchar(255) NOT NULL,
  `iati_revision_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `publisher_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `activity_count` int(11) NOT NULL,
  `activity_delta` int(11) NOT NULL,
  `current` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `publisher_id` (`publisher_id`,`recipient_id`),
  KEY `iati_activity_id` (`iati_activity_id`),
  KEY `iati_revision_id` (`iati_revision_id`),
  KEY `current` (`current`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `publisher`
--

CREATE TABLE IF NOT EXISTS `publisher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iati_id` varchar(255) NOT NULL,
  `iati_group` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `num_countries` int(11) NOT NULL,
  `num_activities` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `iati_id` (`iati_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `recipient`
--

CREATE TABLE IF NOT EXISTS `recipient` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_code` varchar(255) NOT NULL,
  `country_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `num_activities` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `country_id` (`country_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

