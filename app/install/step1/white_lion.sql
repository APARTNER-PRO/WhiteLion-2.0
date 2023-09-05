-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Час створення: Лют 19 2021 р., 22:38
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
-- База даних: `whitelion_v2.cms`
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
(3, 'profile', 0, 'all', 0, 0, NULL),
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
  `alias1` tinyint(3) UNSIGNED NOT NULL,
  `alias2` tinyint(3) UNSIGNED NOT NULL,
  `type` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias1` (`alias1`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_audio`
--

DROP TABLE IF EXISTS `wl_audio`;
CREATE TABLE IF NOT EXISTS `wl_audio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` tinyint(3) UNSIGNED NOT NULL,
  `content` int(11) NOT NULL,
  `name` text NOT NULL,
  `text` text NOT NULL,
  `extension` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`,`content`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_comments`
--

DROP TABLE IF EXISTS `wl_comments`;
CREATE TABLE IF NOT EXISTS `wl_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` int(11) NOT NULL,
  `content` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `images` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` int(11) NOT NULL,
  `manager` int(11) DEFAULT NULL,
  `date_manage` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`,`content`),
  KEY `user` (`user`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_fields`
--

DROP TABLE IF EXISTS `wl_fields`;
CREATE TABLE IF NOT EXISTS `wl_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form` int(11) NOT NULL,
  `name` text NOT NULL,
  `position` int(11) DEFAULT 0,
  `input_type` int(11) NOT NULL,
  `required` tinyint(1) DEFAULT 0,
  `title` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_fields_options`
--

DROP TABLE IF EXISTS `wl_fields_options`;
CREATE TABLE IF NOT EXISTS `wl_fields_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field` int(11) NOT NULL,
  `value` text DEFAULT NULL,
  `title` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_files`
--

DROP TABLE IF EXISTS `wl_files`;
CREATE TABLE IF NOT EXISTS `wl_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` int(11) NOT NULL,
  `content` int(11) NOT NULL,
  `name` text NOT NULL,
  `text` text NOT NULL,
  `extension` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`,`content`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_forms`
--

DROP TABLE IF EXISTS `wl_forms`;
CREATE TABLE IF NOT EXISTS `wl_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sidebar` tinyint(1) NOT NULL,
  `name` text NOT NULL,
  `captcha` tinyint(1) DEFAULT 0,
  `title` text DEFAULT NULL,
  `table` text DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1-GET, 2-POST',
  `type_data` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1-fields, 2-values',
  `send_mail` tinyint(1) DEFAULT NULL,
  `success` tinyint(1) DEFAULT NULL,
  `success_data` text DEFAULT NULL,
  `send_sms` tinyint(1) NOT NULL DEFAULT 0,
  `sms_text` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_images`
--

DROP TABLE IF EXISTS `wl_images`;
CREATE TABLE IF NOT EXISTS `wl_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` int(11) NOT NULL,
  `content` int(11) NOT NULL,
  `section` int(11) DEFAULT NULL,
  `file_name` text DEFAULT NULL,
  `title` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`),
  KEY `content` (`content`)
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
  `type` tinyint(1) NOT NULL COMMENT '1 resize, 2 preview',
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `quality` tinyint(2) NOT NULL DEFAULT 100,
  PRIMARY KEY (`id`),
  KEY `alias_id` (`alias_id`,`active`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_images_sizes`
--

INSERT INTO `wl_images_sizes` (`id`, `alias_id`, `active`, `name`, `prefix`, `type`, `width`, `height`, `quality`) VALUES
(1, 0, 1, 'Значення по замовчуванню. Оригінал', NULL, 1, 1500, 1500, 100),
(2, 0, 1, 'Значення по замовчуванню. Панель керування', 'admin', 2, 150, 150, 100),
(3, 0, 1, 'Значення по замовчуванню. Header для соц. мереж', 'header', 2, 600, 315, 100);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_input_types`
--

DROP TABLE IF EXISTS `wl_input_types`;
CREATE TABLE IF NOT EXISTS `wl_input_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `options` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_input_types`
--

INSERT INTO `wl_input_types` (`id`, `name`, `options`) VALUES
(1, 'text', 0),
(2, 'number', 0),
(3, 'email', 0),
(4, 'url', 0),
(5, 'date', 0),
(6, 'time', 0),
(7, 'datetime', 0),
(8, 'textarea', 0),
(9, 'radio', 1),
(10, 'select', 1),
(11, 'checkbox', 1),
(12, 'checkbox-select2', 1),
(13, 'file', 0);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_language_values`
--

DROP TABLE IF EXISTS `wl_language_values`;
CREATE TABLE IF NOT EXISTS `wl_language_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` int(11) NOT NULL,
  `language` varchar(2) DEFAULT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_language_words`
--

DROP TABLE IF EXISTS `wl_language_words`;
CREATE TABLE IF NOT EXISTS `wl_language_words` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` text DEFAULT NULL,
  `alias` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_mail_active`
--

DROP TABLE IF EXISTS `wl_mail_active`;
CREATE TABLE IF NOT EXISTS `wl_mail_active` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template` int(11) NOT NULL,
  `form` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_mail_history`
--

DROP TABLE IF EXISTS `wl_mail_history`;
CREATE TABLE IF NOT EXISTS `wl_mail_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `from` text DEFAULT NULL,
  `fromName` text DEFAULT NULL,
  `to` text DEFAULT NULL,
  `replyTo` text DEFAULT NULL,
  `subject` text DEFAULT NULL,
  `message` text DEFAULT NULL,
  `attach` text DEFAULT NULL,
  `send_email` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `multilanguage` tinyint(1) DEFAULT NULL,
  `savetohistory` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_mail_templats_data`
--

DROP TABLE IF EXISTS `wl_mail_templats_data`;
CREATE TABLE IF NOT EXISTS `wl_mail_templats_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template` int(11) NOT NULL,
  `language` varchar(2) DEFAULT NULL,
  `title` text DEFAULT NULL,
  `text` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_media_text`
--

DROP TABLE IF EXISTS `wl_media_text`;
CREATE TABLE IF NOT EXISTS `wl_media_text` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('photo','video') NOT NULL,
  `content` int(11) NOT NULL,
  `language` varchar(2) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `content` (`type`,`content`,`language`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_ntkd`
--

DROP TABLE IF EXISTS `wl_ntkd`;
CREATE TABLE IF NOT EXISTS `wl_ntkd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` tinyint(3) NOT NULL,
  `content` int(11) NOT NULL,
  `language` varchar(2) DEFAULT NULL,
  `use_sections` tinyint(1) DEFAULT NULL,
  `get_ivafc` varchar(5) DEFAULT NULL,
  `name` text DEFAULT NULL,
  `title` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `keywords` text DEFAULT NULL,
  `text` text DEFAULT NULL,
  `list` text DEFAULT NULL,
  `meta` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`,`content`,`language`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_ntkd_robot`
--

DROP TABLE IF EXISTS `wl_ntkd_robot`;
CREATE TABLE IF NOT EXISTS `wl_ntkd_robot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` int(11) NOT NULL,
  `content` int(11) NOT NULL,
  `language` varchar(2) DEFAULT NULL,
  `title` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `keywords` text DEFAULT NULL,
  `text` text DEFAULT NULL,
  `list` text DEFAULT NULL,
  `meta` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`),
  KEY `content` (`content`),
  KEY `language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_options`
--

DROP TABLE IF EXISTS `wl_options`;
CREATE TABLE IF NOT EXISTS `wl_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service` tinyint(3) NOT NULL,
  `alias` tinyint(3) NOT NULL,
  `name` text NOT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service` (`service`,`alias`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_options`
--

INSERT INTO `wl_options` (`id`, `service`, `alias`, `name`, `value`) VALUES
(1, 0, 0, 'sitemap_active', '0'),
(2, 0, 0, 'sitemap_autosent', '0'),
(3, 0, 0, 'sitemap_lastgenerate', '0'),
(4, 0, 0, 'sitemap_lastsent', '0'),
(5, 0, 0, 'sitemap_lastedit', '0'),
(6, 0, 0, 'statictic_set_page', '0'),
(7, 0, 0, 'sitemap_lastedit', '0'),
(8, 0, 0, 'global_MetaTags', ''),
(9, 0, 0, 'showTimeSiteGenerate', '1'),
(10, 0, 0, 'sendEmailForce', '0'),
(11, 0, 0, 'sendEmailSaveHistory', '0'),
(12, 0, 0, 'new_user_type', '4'),
(13, 0, 0, 'showInAdminWl_comments', '0'),
(14, 0, 0, 'paginator_per_page', '20'),
(15, 0, 1, 'folder', 'main');

-- --------------------------------------------------------

--
-- Структура таблиці `wl_sections`
--

DROP TABLE IF EXISTS `wl_sections`;
CREATE TABLE IF NOT EXISTS `wl_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` int(11) NOT NULL,
  `content` int(11) NOT NULL,
  `name` text NOT NULL,
  `global` tinyint(1) DEFAULT NULL,
  `position` int(11) NOT NULL,
  `type` smallint(6) NOT NULL,
  `value` text NOT NULL,
  `title` text NOT NULL,
  `attr` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`,`content`),
  KEY `global` (`global`),
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_sitemap`
--

DROP TABLE IF EXISTS `wl_sitemap`;
CREATE TABLE IF NOT EXISTS `wl_sitemap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link_sha1` char(40) NOT NULL,
  `link` text NOT NULL,
  `alias` smallint(6) DEFAULT NULL,
  `content` int(11) DEFAULT NULL,
  `code` smallint(5) UNSIGNED DEFAULT NULL,
  `data` text DEFAULT NULL,
  `time` int(11) NOT NULL,
  `changefreq` enum('always','hourly','daily','weekly','monthly','yearly','never') NOT NULL DEFAULT 'daily',
  `priority` tinyint(2) NOT NULL DEFAULT 5,
  PRIMARY KEY (`id`),
  UNIQUE KEY `link_sha1` (`link_sha1`),
  KEY `content` (`alias`,`content`) USING BTREE,
  KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_sitemap_from`
--

DROP TABLE IF EXISTS `wl_sitemap_from`;
CREATE TABLE IF NOT EXISTS `wl_sitemap_from` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sitemap` int(11) NOT NULL,
  `from` text NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sitemap` (`sitemap`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_statistic_pages`
--

DROP TABLE IF EXISTS `wl_statistic_pages`;
CREATE TABLE IF NOT EXISTS `wl_statistic_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` int(11) DEFAULT NULL,
  `content` int(11) DEFAULT NULL,
  `language` varchar(2) DEFAULT NULL,
  `day` int(10) UNSIGNED NOT NULL,
  `unique` int(10) UNSIGNED NOT NULL,
  `views` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`),
  KEY `content` (`content`),
  KEY `language` (`language`)
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
  `alias` text DEFAULT NULL,
  `email` text NOT NULL,
  `name` text DEFAULT NULL,
  `photo` text DEFAULT NULL,
  `type` smallint(2) NOT NULL DEFAULT 4,
  `status` tinyint(1) NOT NULL DEFAULT 2,
  `registered` int(11) DEFAULT 0,
  `last_login` int(11) NOT NULL,
  `auth_id` text DEFAULT NULL,
  `password` text DEFAULT NULL,
  `reset_key` text DEFAULT NULL,
  `reset_expires` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_info`
--

DROP TABLE IF EXISTS `wl_user_info`;
CREATE TABLE IF NOT EXISTS `wl_user_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `field` text NOT NULL,
  `value` text DEFAULT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_permissions`
--

DROP TABLE IF EXISTS `wl_user_permissions`;
CREATE TABLE IF NOT EXISTS `wl_user_permissions` (
  `user` int(11) NOT NULL,
  `permission` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_register`
--

DROP TABLE IF EXISTS `wl_user_register`;
CREATE TABLE IF NOT EXISTS `wl_user_register` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `do` tinyint(4) NOT NULL,
  `user` int(11) NOT NULL,
  `additionally` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_register_do`
--

DROP TABLE IF EXISTS `wl_user_register_do`;
CREATE TABLE IF NOT EXISTS `wl_user_register_do` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `public` tinyint(1) NOT NULL,
  `title` text NOT NULL,
  `title_public` text NOT NULL,
  `help_additionall` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_user_register_do`
--

INSERT INTO `wl_user_register_do` (`id`, `name`, `public`, `title`, `title_public`, `help_additionall`) VALUES
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_user_status`
--

INSERT INTO `wl_user_status` (`id`, `name`, `title`, `next`, `load`, `color`) VALUES
(1, 'confirmed', 'Підтверджений', 0, 'profile', 'success'),
(2, 'registered', 'Новозареєстрований', 1, 'login/confirmed', 'warning'),
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
(3, 'reserved', 'Резерв', 1),
(4, 'single', 'Користувач', 1),
(5, 'subscribe', 'Підписник', 1);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_video`
--

DROP TABLE IF EXISTS `wl_video`;
CREATE TABLE IF NOT EXISTS `wl_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` int(11) NOT NULL,
  `content` int(11) NOT NULL,
  `author` int(11) NOT NULL,
  `date_add` int(11) NOT NULL,
  `site` text DEFAULT NULL COMMENT 'youtube, vimeo',
  `link` text DEFAULT NULL,
  `active` int(1) DEFAULT 1 COMMENT '0 - видалене',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
