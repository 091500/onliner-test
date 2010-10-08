-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Окт 08 2010 г., 20:23
-- Версия сервера: 5.1.41
-- Версия PHP: 5.3.2-1ubuntu4.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `test`
--

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int(10) NOT NULL AUTO_INCREMENT,
  `left_key` int(10) NOT NULL,
  `right_key` int(10) NOT NULL,
  `level` int(10) NOT NULL,
  `comment_date` datetime NOT NULL,
  `author_name` varchar(50) NOT NULL,
  `comment_text` text NOT NULL,
  `file_id` int(10) NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `comments`
--


-- --------------------------------------------------------

--
-- Структура таблицы `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `files_id` int(10) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(50) NOT NULL,
  `date_value` datetime NOT NULL,
  `user_id` int(10) NOT NULL,
  `blob_file` mediumblob NOT NULL,
  `file_size` varchar(15) NOT NULL,
  `user_agent` varchar(200) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `user_ip` varchar(20) NOT NULL,
  `comment_flag` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`files_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `files`
--


-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_pwd` varchar(100) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `users`
--

