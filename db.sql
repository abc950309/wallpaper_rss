SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `wallpaper_page_sheet` (
  `uuid` bigint(20) NOT NULL AUTO_INCREMENT,
  `method` varchar(32) NOT NULL,
  `id` int(11) NOT NULL,
  `title` tinytext NOT NULL,
  `sort` varchar(32) NOT NULL,
  `min_x` int(11) NOT NULL,
  `min_y` int(11) NOT NULL,
  `min_num` int(11) NOT NULL,
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `wallpaper_pic_sheet` (
  `uuid` bigint(20) NOT NULL AUTO_INCREMENT,
  `src` text NOT NULL,
  `page_uuid` bigint(20) NOT NULL,
  `length` bigint(20) NOT NULL,
  `title` varchar(30) NOT NULL,
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
