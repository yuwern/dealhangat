-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2011 at 06:38 AM
-- Server version: 5.2.8
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dealhangat`
--

--
-- Dumping data for table `blocks`
--

INSERT INTO `blocks` (`id`, `title`, `body`, `region`, `created`, `modified`) VALUES
(2, 'Berikan Hadiah', 'Sample textzzz. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Proin non velit. Integer neque. Aliquam erat volutpat. Integer congue sollicitudin eros. Ut laoreet. Etiam lacinia mollis massa. Proin adipiscing. Vestibulum venenatis eleifend sem. In vel leo vitae risus venenatis malesuada. Donec euismod scelerisque lacus.\r\n\r\nPraesent vitae eros. Fusce ultrices posuere ante. Fusce velit sapien, hendrerit sit amet, commodo a, pulvinar eget, pede. Curabitur molestie ligula vel lorem. Pellentesque interdum iaculis orci. Nulla tortor leo, tempor sit amet, scelerisque ut, rhoncus non, enim. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', '1', '2011-11-08 18:23:35', '2011-11-22 12:50:22');

INSERT INTO `translations` (`id`, `created`, `modified`, `language_id`, `key`, `lang_text`, `is_translated`, `is_google_translate`, `is_verified`) VALUES
(9814, '2011-11-22 10:54:51', '2011-11-22 11:20:09', 42, 'Manage Blocks', 'Manage Blocks', 1, 0, 1),
(9815, '2011-11-22 10:54:51', '2011-11-22 10:54:51', 102, 'Manage Blocks', 'Blok', 1, 0, 1);

