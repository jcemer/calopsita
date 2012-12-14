CREATE TABLE IF NOT EXISTS `backup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `user` varchar(50) DEFAULT NULL,
  `size` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `event` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(50) DEFAULT NULL,
  `uri` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `schedule` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `place` varchar(255) DEFAULT NULL,
  `locale` varchar(255) DEFAULT NULL,
  `text` text,
  `img0_width` char(4) DEFAULT NULL,
  `img0_height` char(4) DEFAULT NULL,
  `img0_file` varchar(50) DEFAULT NULL,
  `img1_width` char(4) DEFAULT NULL,
  `img1_height` char(4) DEFAULT NULL,
  `img1_file` varchar(40) DEFAULT NULL,
  `photo_width` char(4) DEFAULT NULL,
  `photo_height` char(4) DEFAULT NULL,
  `photo_file` varchar(40) DEFAULT NULL,
  `active` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ind_uri` (`uri`),
  KEY `ind_active` (`active`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` varchar(50) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `login` varchar(20) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `active` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `user` (`id`, `created`, `created_by`, `modified`, `modified_by`, `description`, `login`, `password`, `active`) VALUES
(1, '2012-07-01 00:00:00', 'Admin', '2012-08-15 14:08:45', 'Admin', 'Admin', 'admin', 'e10adc3949ba59abbe56e057f20f883e', '1'),



CREATE TABLE IF NOT EXISTS `user_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(50) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `action` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
