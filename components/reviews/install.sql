CREATE TABLE IF NOT EXISTS `#__reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT '0',
  `pubdate` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `target` varchar(11) NOT NULL DEFAULT 'review',
  `target_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `link` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `rating` int(3) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__reviews_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `seolink` text NOT NULL,
  `published` int(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;