-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Час створення: Вер 29 2021 р., 14:31
-- Версія сервера: 10.4.10-MariaDB
-- Версія PHP: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База даних: `carmen.com.ua`
--

-- --------------------------------------------------------

--
-- Структура таблиці `wl_aliases`
--

DROP TABLE IF EXISTS `wl_aliases`;
CREATE TABLE IF NOT EXISTS `wl_aliases` (
  `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `alias` varchar(30) NOT NULL COMMENT 'основне посилання',
  `is_main` tinyint(1) DEFAULT 0,
  `access` enum('all','login','manager','admin') NOT NULL DEFAULT 'all',
  `service_id` int(2) DEFAULT 0,
  `seo_robot` tinyint(1) DEFAULT 0,
  `admin_sidebar` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_aliases`
--

INSERT INTO `wl_aliases` (`id`, `alias`, `is_main`, `access`, `service_id`, `seo_robot`, `admin_sidebar`) VALUES
(1, 'main', 1, 'all', 0, 0, 'O:8:\"stdClass\":3:{s:3:\"ico\";s:11:\"fas fa-home\";s:6:\"folder\";s:7:\"sidebar\";s:5:\"order\";i:20;}'),
(2, 'search', 0, 'all', 0, 0, NULL),
(3, 'profile', 0, 'login', 0, 0, NULL),
(4, 'login', 0, 'all', 0, 0, NULL),
(5, 'signup', 0, 'all', 0, 0, NULL),
(6, 'reset', 0, 'all', 0, 0, NULL),
(7, 'subscribe', 0, 'all', 0, 0, NULL);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_aliases_cooperation`
--

DROP TABLE IF EXISTS `wl_aliases_cooperation`;
CREATE TABLE IF NOT EXISTS `wl_aliases_cooperation` (
  `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `alias1_id` tinyint(3) UNSIGNED NOT NULL,
  `alias2_id` tinyint(3) UNSIGNED NOT NULL,
  `type` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias1` (`alias1_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_forms`
--

DROP TABLE IF EXISTS `wl_forms`;
CREATE TABLE IF NOT EXISTS `wl_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `in_sidebar` tinyint(1) NOT NULL,
  `name` varchar(12) NOT NULL,
  `use_captcha` tinyint(1) NOT NULL DEFAULT 1,
  `title` text DEFAULT NULL,
  `table` text DEFAULT NULL,
  `method` enum('POST','GET') NOT NULL,
  `insert_as` enum('fields','values') NOT NULL DEFAULT 'values',
  `success` tinyint(1) NOT NULL DEFAULT 0,
  `success_data` text NOT NULL,
  `send_mail` tinyint(1) NOT NULL DEFAULT 0,
  `send_sms` tinyint(1) NOT NULL DEFAULT 0,
  `sms_text` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_forms_fields`
--

DROP TABLE IF EXISTS `wl_forms_fields`;
CREATE TABLE IF NOT EXISTS `wl_forms_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL,
  `input_type_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `options` text NOT NULL DEFAULT '',
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `title` text NOT NULL,
  `placeholder` text NOT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_images_sizes`
--

DROP TABLE IF EXISTS `wl_images_sizes`;
CREATE TABLE IF NOT EXISTS `wl_images_sizes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias_id` tinyint(3) NOT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `name` text DEFAULT NULL,
  `prefix` tinytext DEFAULT NULL,
  `action` enum('resize','preview') NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `quality` tinyint(2) NOT NULL DEFAULT 100,
  PRIMARY KEY (`id`),
  KEY `alias_id` (`alias_id`,`active`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_images_sizes`
--

INSERT INTO `wl_images_sizes` (`id`, `alias_id`, `active`, `name`, `prefix`, `action`, `width`, `height`, `quality`) VALUES
(1, 0, 1, 'Значення по замовчуванню. Оригінал', NULL, 'resize', 1500, 1500, 100),
(2, 0, 1, 'Значення по замовчуванню. Панель керування', 'admin', 'preview', 150, 150, 100),
(3, 0, 1, 'Значення по замовчуванню. Header для соц. мереж', 'header', 'preview', 600, 315, 100);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_input_types`
--

DROP TABLE IF EXISTS `wl_input_types`;
CREATE TABLE IF NOT EXISTS `wl_input_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `use_options` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_input_types`
--

INSERT INTO `wl_input_types` (`id`, `name`, `use_options`) VALUES
(1, 'text', 0),
(2, 'number', 0),
(3, 'email', 0),
(4, 'tel', 0),
(5, 'date', 0),
(6, 'time', 0),
(7, 'datetime-local', 0),
(8, 'textarea', 0),
(9, 'radio', 1),
(10, 'select', 1),
(11, 'checkbox', 1),
(12, 'checkbox-select2', 1),
(13, 'file', 0),
(14, 'url', 0);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_language_words`
--

DROP TABLE IF EXISTS `wl_language_words`;
CREATE TABLE IF NOT EXISTS `wl_language_words` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias_id` int(11) NOT NULL,
  `word` text NOT NULL,
  `value` text DEFAULT NULL,
  `is_multilanguages` tinyint(1) NOT NULL DEFAULT 0,
  `input_type_id` tinyint(1) NOT NULL DEFAULT 1,
  `position` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_mail_history`
--

DROP TABLE IF EXISTS `wl_mail_history`;
CREATE TABLE IF NOT EXISTS `wl_mail_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `from` text DEFAULT NULL,
  `fromName` text DEFAULT NULL,
  `to` text DEFAULT NULL,
  `replyTo` text DEFAULT NULL,
  `subject` text DEFAULT NULL,
  `message` text DEFAULT NULL,
  `attach` text DEFAULT NULL,
  `flag_send` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `flag_send` (`flag_send`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_mail_history`
--

INSERT INTO `wl_mail_history` (`id`, `template_id`, `created_at`, `from`, `fromName`, `to`, `replyTo`, `subject`, `message`, `attach`, `flag_send`) VALUES
(1, 0, 1603480770, 'info@whitelion.cms', '', 'developer@webspirit.com.ua', 'info@whitelion.cms', 'Замовлення №1 whitelion.cms', '<html><head><title>Замовлення №1 whitelion.cms</title></head><body><p>Доброго дня <b>developer</b>!</p><p>Дякуємо за покупку в нашому магазині! Ми зв\'яжемося з Вами найближчим часом для підтвердження замовлення.</p><p>Ви можете відстежити статус свого замовлення в <a href=\"http://whitelion.cms/cart/1\">особистому кабінеті</a>.</p><table align=\"left\" width=\"100%\" cellpadding=\"5\" cellspacing=\"5\" border=\"0\" style=\"border-collapse:collapse;margin-bottom: 15px\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"left\" width=\"170\">Покупець</td>\r\n			<td align=\"left\">\r\n				<b>developer</b><br> developer@webspirit.com.ua<br> \r\n			</td>\r\n		</tr></td>\r\n			</tr></tbody>\r\n</table><br><h1><b>Замовлення №1</b> 23.10.2020 19:12</h1>\r\n<table align=\"left\" width=\"100%\" cellpadding=\"10\" cellspacing=\"0\" border=\"0\" style=\"border-collapse:collapse\">\r\n	<tbody><tr>\r\n		<th style=\"border-bottom:2px solid #f4f4f4\"></th>\r\n		<th align=\"left\" style=\"border-bottom:2px solid #f4f4f4\"><strong>Артикул</strong></th>\r\n		<th align=\"left\" style=\"border-bottom:2px solid #f4f4f4\"><strong>Товар</strong></th>\r\n		<th align=\"right\" style=\"border-bottom:2px solid #f4f4f4\"><strong>Ціна</strong></th>\r\n		<th align=\"right\" style=\"border-bottom:2px solid #f4f4f4\"><strong>Кількість</strong></th>\r\n		<th align=\"right\" style=\"border-bottom:2px solid #f4f4f4\"><strong>Сума</strong></th>\r\n	</tr>\r\n	<tr><tr>\r\n		                    <td align=\"left\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\">1</td>\r\n		                    <td align=\"left\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\"></td>\r\n		                    <td align=\"left\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\"><a href=\"http://whitelion.cms/\" style=\"color:#693319!important;text-decoration:underline\" target=\"_blank\"><span style=\"color:#693319\"></span></a><br>: <strong></strong><br>: <strong></strong><br>: <strong></strong><br>: <strong></strong></td>\r\n		                    <td align=\"right\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\"></td>\r\n		                    <td align=\"right\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\">2 шт.</td>\r\n		                    <td align=\"right\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\"><strong></strong>\r\n		    				</td>\r\n		                </tr><tr>\r\n		                    <td align=\"left\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\">2</td>\r\n		                    <td align=\"left\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\"></td>\r\n		                    <td align=\"left\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\"><a href=\"http://whitelion.cms/\" style=\"color:#693319!important;text-decoration:underline\" target=\"_blank\"><span style=\"color:#693319\"></span></a><br>: <strong></strong><br>: <strong></strong><br>: <strong></strong><br>: <strong></strong></td>\r\n		                    <td align=\"right\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\"></td>\r\n		                    <td align=\"right\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\">1 шт.</td>\r\n		                    <td align=\"right\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\"><strong></strong>\r\n		    				</td>\r\n		                </tr><tr><td colspan=\"6\" align=\"right\" style=\"border-top:2px solid #f4f4f4\">До оплати: <b></b></td></tr></tbody></table><p>Якщо у Вас є питання, телефонуйте: +38 (096) ********</p><p>З найкращими побажаннями, <a href=\"http://whitelion.cms/\" target=\"_blank\">компанія whitelion.cms</a></p></body></html>', 'a:0:{}', 0),
(2, 0, 1603480943, 'info@whitelion.cms', '', 'developer@webspirit.com.ua', 'info@whitelion.cms', 'Замовлення №2 whitelion.cms', '<html><head><title>Замовлення №2 whitelion.cms</title></head><body><p>Доброго дня <b>developer</b>!</p><p>Дякуємо за покупку в нашому магазині! Ми зв\'яжемося з Вами найближчим часом для підтвердження замовлення.</p><p>Ви можете відстежити статус свого замовлення в <a href=\"http://whitelion.cms/cart/2\">особистому кабінеті</a>.</p><table align=\"left\" width=\"100%\" cellpadding=\"5\" cellspacing=\"5\" border=\"0\" style=\"border-collapse:collapse;margin-bottom: 15px\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"left\" width=\"170\">Покупець</td>\r\n			<td align=\"left\">\r\n				<b>developer</b><br> developer@webspirit.com.ua<br> \r\n			</td>\r\n		</tr></td>\r\n			</tr></tbody>\r\n</table><br><h1><b>Замовлення №2</b> 23.10.2020 19:22</h1>\r\n<table align=\"left\" width=\"100%\" cellpadding=\"10\" cellspacing=\"0\" border=\"0\" style=\"border-collapse:collapse\">\r\n	<tbody><tr>\r\n		<th style=\"border-bottom:2px solid #f4f4f4\"></th>\r\n		<th align=\"left\" style=\"border-bottom:2px solid #f4f4f4\"><strong>Артикул</strong></th>\r\n		<th align=\"left\" style=\"border-bottom:2px solid #f4f4f4\"><strong>Товар</strong></th>\r\n		<th align=\"right\" style=\"border-bottom:2px solid #f4f4f4\"><strong>Ціна</strong></th>\r\n		<th align=\"right\" style=\"border-bottom:2px solid #f4f4f4\"><strong>Кількість</strong></th>\r\n		<th align=\"right\" style=\"border-bottom:2px solid #f4f4f4\"><strong>Сума</strong></th>\r\n	</tr>\r\n	<tr><tr>\r\n		                    <td align=\"left\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\">1</td>\r\n		                    <td align=\"left\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\">test</td>\r\n		                    <td align=\"left\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\"><a href=\"http://whitelion.cms/\" style=\"color:#693319!important;text-decoration:underline\" target=\"_blank\"><span style=\"color:#693319\">lviv excursion</span></a></td>\r\n		                    <td align=\"right\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\"></td>\r\n		                    <td align=\"right\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\">1 шт.</td>\r\n		                    <td align=\"right\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\"><strong></strong>\r\n		    				</td>\r\n		                </tr><tr><td colspan=\"6\" align=\"right\" style=\"border-top:2px solid #f4f4f4\">До оплати: <b></b></td></tr></tbody></table><p>Якщо у Вас є питання, телефонуйте: +38 (096) ********</p><p>З найкращими побажаннями, <a href=\"http://whitelion.cms/\" target=\"_blank\">компанія whitelion.cms</a></p></body></html>', 'a:0:{}', 0),
(3, 0, 1603482894, 'info@whitelion.cms', '', 'test@test.com', 'info@whitelion.cms', 'Замовлення №3 whitelion.cms', '<html><head><title>Замовлення №3 whitelion.cms</title></head><body><p>Доброго дня <b>test</b>!</p><p>Дякуємо за покупку в нашому магазині! Ми зв\'яжемося з Вами найближчим часом для підтвердження замовлення.</p><p>Ви можете відстежити статус свого замовлення в <a href=\"http://whitelion.cms/cart/3\">особистому кабінеті</a>.</p><table align=\"left\" width=\"100%\" cellpadding=\"5\" cellspacing=\"5\" border=\"0\" style=\"border-collapse:collapse;margin-bottom: 15px\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"left\" width=\"170\">Покупець</td>\r\n			<td align=\"left\">\r\n				<b>test</b><br> test@test.com<br> 380123456789\r\n			</td>\r\n		</tr><tr>\r\n				<td align=\"left\" width=\"170\" style=\"border-top:1px solid #f4f4f4\"><b><u>Увага!</u></b></td>\r\n				<td align=\"left\" style=\"border-top:1px solid #f4f4f4\">Ваш пароль до персонального кабінету: <b>7ef6aa88</b></td>\r\n			</tr></td>\r\n			</tr></tbody>\r\n</table><br><h1><b>Замовлення №3</b> 23.10.2020 19:43</h1>\r\n<table align=\"left\" width=\"100%\" cellpadding=\"10\" cellspacing=\"0\" border=\"0\" style=\"border-collapse:collapse\">\r\n	<tbody><tr>\r\n		<th style=\"border-bottom:2px solid #f4f4f4\"></th>\r\n		<th align=\"left\" style=\"border-bottom:2px solid #f4f4f4\"><strong>Артикул</strong></th>\r\n		<th align=\"left\" style=\"border-bottom:2px solid #f4f4f4\"><strong>Товар</strong></th>\r\n		<th align=\"right\" style=\"border-bottom:2px solid #f4f4f4\"><strong>Ціна</strong></th>\r\n		<th align=\"right\" style=\"border-bottom:2px solid #f4f4f4\"><strong>Кількість</strong></th>\r\n		<th align=\"right\" style=\"border-bottom:2px solid #f4f4f4\"><strong>Сума</strong></th>\r\n	</tr>\r\n	<tr><tr>\r\n		                    <td align=\"left\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\">1</td>\r\n		                    <td align=\"left\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\">test</td>\r\n		                    <td align=\"left\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\"><a href=\"http://whitelion.cms/\" style=\"color:#693319!important;text-decoration:underline\" target=\"_blank\"><span style=\"color:#693319\">lviv excursion</span></a></td>\r\n		                    <td align=\"right\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\"></td>\r\n		                    <td align=\"right\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\">1 шт.</td>\r\n		                    <td align=\"right\" valign=\"top\" style=\"border-top:1px solid #f4f4f4\"><strong></strong>\r\n		    				</td>\r\n		                </tr><tr><td colspan=\"6\" align=\"right\" style=\"border-top:2px solid #f4f4f4\">До оплати: <b>550</b></td></tr></tbody></table><p>Якщо у Вас є питання, телефонуйте: +38 (096) ********</p><p>З найкращими побажаннями, <a href=\"http://whitelion.cms/\" target=\"_blank\">компанія whitelion.cms</a></p></body></html>', 'a:0:{}', 0);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_mail_templates`
--

DROP TABLE IF EXISTS `wl_mail_templates`;
CREATE TABLE IF NOT EXISTS `wl_mail_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text DEFAULT NULL,
  `from` text DEFAULT NULL,
  `to` text DEFAULT NULL,
  `multilanguage` tinyint(1) NOT NULL DEFAULT 0,
  `savetohistory` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_media`
--

DROP TABLE IF EXISTS `wl_media`;
CREATE TABLE IF NOT EXISTS `wl_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias_id` tinyint(3) UNSIGNED NOT NULL,
  `content_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL DEFAULT 0,
  `media_type` enum('image','video','audio','file') NOT NULL,
  `position` int(11) NOT NULL,
  `filename` text NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL DEFAULT '',
  `created_by` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias_id`,`content_id`) USING BTREE,
  KEY `position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_media_text`
--

DROP TABLE IF EXISTS `wl_media_text`;
CREATE TABLE IF NOT EXISTS `wl_media_text` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('section','photo','video','image','mail') NOT NULL,
  `content_id` int(11) NOT NULL,
  `language` varchar(2) NOT NULL,
  `title` text NOT NULL DEFAULT '',
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `content` (`type`,`content_id`,`language`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_ntkd`
--

DROP TABLE IF EXISTS `wl_ntkd`;
CREATE TABLE IF NOT EXISTS `wl_ntkd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias_id` tinyint(3) NOT NULL,
  `content_id` int(11) NOT NULL,
  `language` varchar(2) DEFAULT NULL,
  `name` text DEFAULT NULL,
  `title` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `keywords` text DEFAULT NULL,
  `meta` text DEFAULT NULL,
  `text_short` text DEFAULT NULL,
  `text_full` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias_id`,`content_id`,`language`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_ntkd`
--

INSERT INTO `wl_ntkd` (`id`, `alias_id`, `content_id`, `language`, `name`, `title`, `description`, `keywords`, `meta`, `text_short`, `text_full`) VALUES
(1, 1, 0, 'uk', 'whitelion.cms', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 2, 0, 'uk', 'Пошук whitelion.cms', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 3, 0, 'uk', 'Мій кабінет whitelion.cms', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 4, 0, 'uk', 'Увійти у whitelion.cms', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 5, 0, 'uk', 'Реєстрація whitelion.cms', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 6, 0, 'uk', 'Відновлення паролю whitelion.cms', NULL, NULL, NULL, NULL, NULL, NULL),
(7, 7, 0, 'uk', 'Підписка', NULL, NULL, NULL, NULL, NULL, NULL),
(8, 1, 0, 'en', 'whitelion.cms', NULL, NULL, NULL, NULL, NULL, NULL),
(9, 2, 0, 'en', 'Пошук whitelion.cms', NULL, NULL, NULL, NULL, NULL, NULL),
(10, 3, 0, 'en', 'Мій кабінет whitelion.cms', NULL, NULL, NULL, NULL, NULL, NULL),
(11, 4, 0, 'en', 'Увійти у whitelion.cms', NULL, NULL, NULL, NULL, NULL, NULL),
(12, 5, 0, 'en', 'Реєстрація whitelion.cms', NULL, NULL, NULL, NULL, NULL, NULL),
(13, 6, 0, 'en', 'Відновлення паролю whitelion.cms', NULL, NULL, NULL, NULL, NULL, NULL),
(14, 7, 0, 'en', 'Підписка', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_ntkd_robot`
--

DROP TABLE IF EXISTS `wl_ntkd_robot`;
CREATE TABLE IF NOT EXISTS `wl_ntkd_robot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `language` varchar(2) DEFAULT NULL,
  `title` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `keywords` text DEFAULT NULL,
  `text_short` text DEFAULT NULL,
  `text_full` text DEFAULT NULL,
  `meta` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias_id`),
  KEY `content` (`content_id`),
  KEY `language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_options`
--

DROP TABLE IF EXISTS `wl_options`;
CREATE TABLE IF NOT EXISTS `wl_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` tinyint(3) NOT NULL,
  `alias_id` tinyint(3) NOT NULL,
  `name` text NOT NULL,
  `value` text DEFAULT NULL,
  `serialized` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `service` (`service_id`,`alias_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_options`
--

INSERT INTO `wl_options` (`id`, `service_id`, `alias_id`, `name`, `value`, `serialized`) VALUES
(1, 0, 0, 'sitemap_active', '0', 0),
(2, 0, 0, 'sitemap_autosent', '0', 0),
(3, 0, 0, 'sitemap_lastgenerate', '0', 0),
(4, 0, 0, 'sitemap_lastsent', '0', 0),
(5, 0, 0, 'sitemap_lastedit', '0', 0),
(6, 0, 0, 'statictic_set_page', '0', 0),
(7, 0, 0, 'sitemap_lastedit', '0', 0),
(8, 0, 0, 'global_MetaTags', '', 0),
(9, 0, 0, 'showTimeSiteGenerate', '0', 0),
(10, 0, 0, 'sendEmailForce', '0', 0),
(11, 0, 0, 'sendEmailSaveHistory', '0', 0),
(12, 0, 0, 'new_user_type', '4', 0),
(13, 0, 0, 'showInAdminWl_comments', '1', 0),
(14, 0, 0, 'paginator_per_page', '20', 0),
(15, 0, 0, 'userSignUp', '0', 0);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_sections`
--

DROP TABLE IF EXISTS `wl_sections`;
CREATE TABLE IF NOT EXISTS `wl_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `position` int(11) NOT NULL,
  `type` smallint(6) NOT NULL,
  `value` text NOT NULL,
  `title` text NOT NULL,
  `attr` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias_id`,`content_id`),
  KEY `position` (`position`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_services`
--

DROP TABLE IF EXISTS `wl_services`;
CREATE TABLE IF NOT EXISTS `wl_services` (
  `id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL COMMENT 'службова назва (папки)',
  `title` text NOT NULL COMMENT 'публічна назва',
  `description` text NOT NULL,
  `table` text NOT NULL COMMENT 'службова таблиця',
  `group` tinytext NOT NULL,
  `multi_alias` tinyint(1) NOT NULL,
  `version` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_sitemap`
--

DROP TABLE IF EXISTS `wl_sitemap`;
CREATE TABLE IF NOT EXISTS `wl_sitemap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link_sha1` char(40) NOT NULL,
  `link` text NOT NULL,
  `alias_id` smallint(6) NOT NULL,
  `content_id` int(11) NOT NULL,
  `code` smallint(5) UNSIGNED DEFAULT NULL,
  `data` text DEFAULT NULL,
  `time` int(11) NOT NULL,
  `changefreq` enum('always','hourly','daily','weekly','monthly','yearly','never') NOT NULL DEFAULT 'daily',
  `priority` tinyint(2) NOT NULL DEFAULT 5,
  PRIMARY KEY (`id`),
  UNIQUE KEY `link_sha1` (`link_sha1`),
  KEY `content` (`alias_id`,`content_id`) USING BTREE,
  KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_sitemap_from`
--

DROP TABLE IF EXISTS `wl_sitemap_from`;
CREATE TABLE IF NOT EXISTS `wl_sitemap_from` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sitemap_id` int(11) NOT NULL,
  `from` text NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sitemap` (`sitemap_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_statistic_pages`
--

DROP TABLE IF EXISTS `wl_statistic_pages`;
CREATE TABLE IF NOT EXISTS `wl_statistic_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `language` varchar(2) DEFAULT NULL,
  `day` int(10) UNSIGNED NOT NULL,
  `unique` int(10) UNSIGNED NOT NULL,
  `views` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `language` (`language`),
  KEY `alias` (`alias_id`,`content_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_statistic_views`
--

DROP TABLE IF EXISTS `wl_statistic_views`;
CREATE TABLE IF NOT EXISTS `wl_statistic_views` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `day` int(10) UNSIGNED NOT NULL,
  `cookie` int(10) UNSIGNED NOT NULL,
  `unique` int(10) UNSIGNED NOT NULL,
  `views` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `day` (`day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_users`
--

DROP TABLE IF EXISTS `wl_users`;
CREATE TABLE IF NOT EXISTS `wl_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL,
  `phone` char(12) NOT NULL,
  `name` text DEFAULT NULL,
  `photo` text DEFAULT NULL,
  `type_id` smallint(2) NOT NULL DEFAULT 4,
  `status_id` tinyint(1) NOT NULL DEFAULT 2,
  `permissions` text NOT NULL DEFAULT '',
  `registered` int(11) DEFAULT 0,
  `last_login` int(11) NOT NULL,
  `auth_id` char(32) DEFAULT NULL,
  `password` varchar(40) DEFAULT NULL,
  `reset_key` varchar(40) DEFAULT NULL,
  `reset_expires` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone` (`phone`),
  UNIQUE KEY `alias` (`uri`),
  KEY `email` (`email`),
  KEY `auth_id` (`auth_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_users`
--

INSERT INTO `wl_users` (`id`, `uri`, `email`, `phone`, `name`, `photo`, `type_id`, `status_id`, `permissions`, `registered`, `last_login`, `auth_id`, `password`, `reset_key`, `reset_expires`) VALUES
(1, 'developer', 'developer@webspirit.com.ua', '', 'developer', NULL, 1, 1, '', 1632925857, 0, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_auth`
--

DROP TABLE IF EXISTS `wl_user_auth`;
CREATE TABLE IF NOT EXISTS `wl_user_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `auth_key` char(60) NOT NULL,
  `last_login` int(11) NOT NULL,
  `from` text NOT NULL,
  `title` text NOT NULL,
  `created_at` int(11) NOT NULL,
  `created_by` varchar(225) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `auth_key` (`auth_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_info`
--

DROP TABLE IF EXISTS `wl_user_info`;
CREATE TABLE IF NOT EXISTS `wl_user_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `field` text NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_register`
--

DROP TABLE IF EXISTS `wl_user_register`;
CREATE TABLE IF NOT EXISTS `wl_user_register` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action_id` tinyint(4) NOT NULL,
  `action_at` int(11) NOT NULL,
  `additionally` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_register_actions`
--

DROP TABLE IF EXISTS `wl_user_register_actions`;
CREATE TABLE IF NOT EXISTS `wl_user_register_actions` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `public` tinyint(1) NOT NULL,
  `title` text NOT NULL,
  `title_public` text NOT NULL,
  `help_additionall` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_user_register_actions`
--

INSERT INTO `wl_user_register_actions` (`id`, `name`, `public`, `title`, `title_public`, `help_additionall`) VALUES
(1, 'signup', 1, 'Реєстрація нового користувача', 'Реєстрація користувача', ''),
(2, 'confirmed', 1, 'Підтвердження реєстрації користувача', 'Підтвердження реєстрації', ''),
(3, 'reset_sent', 0, 'Відновлення паролю. Вислано повідомлення із кодом відновлення.', '', ''),
(4, 'reset', 1, 'Відновлення паролю. Пароль змінено. Старий пароль у полі Додатково.', 'Зміна паролю користувачем', 'Попередній пароль у sha1'),
(5, 'profile_data', 0, 'Змінено особисті дані', '', 'field(id) - ід поля, value(text) - попередні дані'),
(6, 'login_bad', 0, 'Невірна спроба авторизації з ІР', '', 'ІР адреса'),
(7, 'profile_type', 1, 'Зміна типу користувача', 'Зміна типу користувача', 'user(id) - хто змінив, old_type(id) - попередній тип'),
(8, 'subscribe', 0, 'Підписався на оновлення', '', ''),
(9, 'reset_admin', 1, 'Відновлення паролю. Пароль змінено. Старий пароль у полі Додатково.', 'Зміна паролю адміністрацією', 'Зміна паролю адміністрацією. Пароль змінено. Старий пароль у полі Додатково.'),
(10, 'user_delete', 0, 'Видалив профіль користувача', 'Видалив профіль користувача', 'Id. Email. User name. Type. Date register'),
(11, 'alias_add', 0, 'Додано головну адресу', 'Додано головну адресу', 'Адреса посилання'),
(12, 'alias_delete', 0, 'Видалена головна адреса', 'Видалена головна адреса', 'Ід. Адреса. Сервіс.'),
(13, 'service_install', 0, 'Install service', 'Install service', 'Id. Service name (version)'),
(14, 'service_uninstall', 0, 'Uninstall service', 'Uninstall service', 'Id. Service name (version)'),
(15, 'login_as_user', 0, 'Вхід до профілю через панель керування', '', 'Хто зайшов'),
(16, 'logout_as_user', 0, 'Вихід з профілю через панель керування', '', 'Хто вийшов');

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_status`
--

DROP TABLE IF EXISTS `wl_user_status`;
CREATE TABLE IF NOT EXISTS `wl_user_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `title` text NOT NULL,
  `next` int(11) NOT NULL,
  `load` text NOT NULL,
  `color` varchar(7) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_user_status`
--

INSERT INTO `wl_user_status` (`id`, `name`, `title`, `next`, `load`, `color`) VALUES
(1, 'confirmed', 'Підтверджений', 0, '', 'success'),
(2, 'registered', 'Новозареєстрований', 1, 'signup/confirmed', 'warning'),
(3, 'banned', 'Заблокований', 0, '', 'danger');

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_types`
--

DROP TABLE IF EXISTS `wl_user_types`;
CREATE TABLE IF NOT EXISTS `wl_user_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `title` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_user_types`
--

INSERT INTO `wl_user_types` (`id`, `name`, `title`, `active`) VALUES
(1, 'admin', 'Адміністратор', 1),
(2, 'manager', 'Менеджер', 1),
(3, 'reserved', 'Резерв', 0),
(4, 'single', 'Користувач', 1),
(5, 'subscribe', 'Підписник', 1);

--
-- Індекси збережених таблиць
--

--
-- Індекси таблиці `wl_ntkd`
--
ALTER TABLE `wl_ntkd` ADD FULLTEXT KEY `name` (`name`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
