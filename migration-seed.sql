-- phpMyAdmin SQL Dump
-- version 4.0.10.15
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 06, 2016 at 10:46 AM
-- Server version: 5.5.48-cll-lve
-- PHP Version: 5.4.45

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `texla_ru`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE IF NOT EXISTS `admin_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `action_object` varchar(50) NOT NULL,
  `user` varchar(50) NOT NULL,
  `ip` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `date`, `action_type`, `action_object`, `user`, `ip`) VALUES
(1, '2016-10-06 10:42:21', '', '', '', ''),
(2, '2016-10-06 10:42:31', 'dcms>page', 'id=1?id= 1', 'andrey', '141.105.66.30'),
(3, '2016-10-06 10:42:31', 'dcms>page', 'page=1', 'andrey', '141.105.66.30'),
(4, '2016-10-06 10:42:33', 'dcms>maket', '', 'andrey', '141.105.66.30');

-- --------------------------------------------------------

--
-- Table structure for table `coms`
--

CREATE TABLE IF NOT EXISTS `coms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `table_name` varchar(255) NOT NULL,
  `list_prefix` text NOT NULL,
  `list_sufix` text NOT NULL,
  `access` int(11) NOT NULL,
  `item` text NOT NULL,
  `query` varchar(255) NOT NULL,
  `show_one_page` int(11) NOT NULL,
  `code` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `coms_fields`
--

CREATE TABLE IF NOT EXISTS `coms_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enname` varchar(255) NOT NULL,
  `parent` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `param` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `auto_select` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  `show_table` tinyint(1) NOT NULL DEFAULT '0',
  `show_edit` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `com_page_extension`
--

CREATE TABLE IF NOT EXISTS `com_page_extension` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_item` int(11) DEFAULT NULL,
  `d_sort` int(11) DEFAULT NULL,
  `sys_date` date DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `pic` varchar(120) DEFAULT NULL,
  `page_id` varchar(120) DEFAULT NULL,
  `zagolovok_dlya_bannera` varchar(500) DEFAULT NULL,
  `tekst_dlya_bannera` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `designes`
--

CREATE TABLE IF NOT EXISTS `designes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `parent` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `designes_items`
--

CREATE TABLE IF NOT EXISTS `designes_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL,
  `text` longtext NOT NULL,
  `editor` int(11) NOT NULL,
  `for_all` tinyint(1) NOT NULL COMMENT '(hidden)',
  `ful_copy_id` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `rules` int(11) NOT NULL DEFAULT '1',
  `komp` int(11) NOT NULL,
  `design` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `parent` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `title` varchar(500) NOT NULL,
  `keyw` text NOT NULL,
  `descr` text NOT NULL,
  `design` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  `off` tinyint(1) NOT NULL DEFAULT '0',
  `hide_child` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Скрывать подстраницы',
  `sost` tinyint(4) NOT NULL DEFAULT '0',
  `sub_design` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `name`, `parent`, `path`, `title`, `keyw`, `descr`, `design`, `sort`, `off`, `hide_child`, `sost`, `sub_design`) VALUES
(1, '315', 0, '315', '315', '315', '', 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pages_items`
--

CREATE TABLE IF NOT EXISTS `pages_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL DEFAULT '1',
  `design` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT 'no_name',
  `text` longtext NOT NULL,
  `editor` int(11) NOT NULL DEFAULT '1',
  `komp` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  `rules` int(11) NOT NULL,
  `ful_copy_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `rights` int(11) NOT NULL,
  `create_date` date NOT NULL COMMENT 'При создании',
  `password` varchar(255) NOT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `login`, `name`, `mail`, `rights`, `create_date`, `password`) VALUES
(10, 'andrey', 'andrey', 'dathim@gmail.com', 1, '0000-00-00', 'M65g308E2e27fc3deb028b12555268fc25fb580dm3nv1rTe'),
(18, 'test', 'test', 'test@test.com', 1, '0000-00-00', 'M65g308E0bd34ff685bcec69268098ff5dbafd8dm3nv1rTe');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
