DROP TABLE IF EXISTS `oc_product_coordinate`;
CREATE TABLE `oc_product_coordinate` (
  `product_id` int(11) NOT NULL,
  `list_product_id` int(11) NOT NULL,
	`num` int(11) NOT NULL,
  `coordiname` varchar(255) NOT NULL,
  PRIMARY KEY (`product_id`,`list_product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;