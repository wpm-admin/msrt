<?php
class ModelExtensionModuleSmartMarketing extends Model {
	public function createTables() {
		// Table sm_subscriber
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "sm_subscriber` (`subscriber_id` int(11) NOT NULL AUTO_INCREMENT, `firstname` varchar(32) NOT NULL, `lastname` varchar(32) NOT NULL, `email` varchar(96) NOT NULL, `customer_id` int(11) NOT NULL, `customer_group_id` int(11) NOT NULL, `country_id` int(11) NOT NULL, `status` int(11) NOT NULL, `source` varchar(32) NOT NULL, `rating` int(11) NOT NULL, `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', `note` text NOT NULL, PRIMARY KEY (`subscriber_id`), UNIQUE KEY `email` (`email`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		// Table sm_campaign
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "sm_campaign` (`campaign_id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL, `sender_name` varchar(128) NOT NULL, `sender_email` varchar(96) NOT NULL, `subject` varchar(255) NOT NULL, `html_content` longtext NOT NULL, `plain_content` text NOT NULL, `template_id` varchar(64) NOT NULL COMMENT 'sendgrid format - chars', `status` int(11) NOT NULL, `date_scheduled` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',  `timezone_hour` time NOT NULL DEFAULT '00:00:00', `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', PRIMARY KEY (`campaign_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		// Table sm_campaign_segmentation
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "sm_campaign_segmentation` (`campaign_segmentation_id` int(11) NOT NULL AUTO_INCREMENT, `campaign_id` int(11) NOT NULL, `type` varchar(32) NOT NULL, `match` varchar(3) NOT NULL, `condition` text NOT NULL, PRIMARY KEY (`campaign_segmentation_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		// Table sm_campaign_task
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "sm_campaign_task` (`campaign_task_id` int(11) NOT NULL AUTO_INCREMENT, `campaign_id` int(11) NOT NULL, `subscriber_id` int(11) NOT NULL,   `sent` int(11) NOT NULL COMMENT 'sent to SendGrid', `delivered` int(11) NOT NULL COMMENT 'from SendGrid server to subscriber inbox', `open` int(11) NOT NULL, `click` int(11) NOT NULL, `bounce` int(11) NOT NULL, `unsubscribe` int(11) NOT NULL, `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', PRIMARY KEY (`campaign_task_id`), UNIQUE KEY `campaign_id_subscriber_id` (`campaign_id`,`subscriber_id`), KEY `campaign_id` (`campaign_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		$query = $this->db->query("SELECT COUNT(*) AS total FROM information_schema.columns WHERE table_name = '" . DB_PREFIX . "country' AND table_schema = '" . DB_DATABASE . "' AND column_name = 'utc'");

		if (!$query->row['total']) {
			// Add UTC timezone offset field in table country
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "country` ADD `utc` INT NOT NULL AFTER `iso_code_3`;");
		}

		// SET UTC for each country
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '-11' WHERE iso_code_2 IN ('NU','AS')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '-10' WHERE iso_code_2 IN ('CK')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '-9' WHERE iso_code_2 IN ('PF')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '-8' WHERE iso_code_2 IN ('PN')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '-6' WHERE iso_code_2 IN ('BZ','NI','HN','SV','EC','CR','GT','US','MX')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '-5' WHERE iso_code_2 IN ('KY','PA','PE','CA','JM','CO')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '-4' WHERE iso_code_2 IN ('VE','VG','CL','HT','GY','TC','CU','TT','GP','GD','DM','DO','VC','LC','BL','BS','AW','VI','CW','BQ','BB','PY','AG','KN','BR','MQ','PR','AI','BO','MS','MF')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '-3' WHERE iso_code_2 IN ('AR','BM','SR','GF','UY','FK')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '-2' WHERE iso_code_2 IN ('GS','PM')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '-1' WHERE iso_code_2 IN ('CV','GL')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '0' WHERE iso_code_2 IN ('ML','GM','MR','LR','AN','GN','GW','HM','SN','SL','SH','BV','TG','CI','GH','TA','IC','AC','IS','XK','BF')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '1' WHERE iso_code_2 IN ('EH','IE','JE','GG','NE','PT','BJ','GQ','NG','AO','IM','DZ','ST','CM','FO','GA','MA','CG','TN','GB','TD','CF','UM')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '2' WHERE iso_code_2 IN ('NL','ZA','NA','MZ','SZ','SM','MC','SJ','SD','MT','RW','RS','ES','CD','CH','PL','ZM','ZW','SE','ME','NO','SI','VA','SK','EG','BW','LI','LY','CZ','BI','LS','BE','GI','DE','FR','LU','BA','AL','AD','MW','AT','DK','MK','HU','IT','HR')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '3' WHERE iso_code_2 IN ('EE','FI','ET','TZ','GR','SA','IQ','IL','ER','DJ','PS','BH','AX','BY','YE','KM','UA','UG','TR','CY','BG','SY','SS','KW','JO','YT','KE','MD','LT','SO','LB','LV','MG','RO','QA')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '4' WHERE iso_code_2 IN ('SC','AE','GE','RE','AF','IR','OM','AM','MU','AZ')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '5' WHERE iso_code_2 IN ('TF','TM','AQ','TJ','KZ','UZ','NP','PK','IN','LK','MV')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '6' WHERE iso_code_2 IN ('KG','BT','CC','IO','BD','MM')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '7' WHERE iso_code_2 IN ('KH','CN','RU','LA','VN','CX','TH')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '8' WHERE iso_code_2 IN ('BN','KP','MN','MO','HK','PH','SG','MY','ID','TW')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '9' WHERE iso_code_2 IN ('TL','JP','PW','KR')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '10' WHERE iso_code_2 IN ('MP','AU','GU')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '11' WHERE iso_code_2 IN ('PG','NF','NC','VU','SB','FM')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '12' WHERE iso_code_2 IN ('TV','NR','MH','WF','NZ','FJ')");
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET utc = '13' WHERE iso_code_2 IN ('KI','TK','WS','TO')");
	}

	public function removeTables() {
		// not recommended to delete sm_subscriber because (if deleted and recreated) will import back unsubscribed emails
		//$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sm_subscriber`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sm_campaign`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sm_campaign_segmentation`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sm_campaign_task`");
	}
}
?>
