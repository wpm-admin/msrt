<?php
/*
DROP TABLE IF EXISTS `oc_consignment`;
CREATE TABLE `oc_consignment` (
  `consignment_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `mark_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `price` float NOT NULL,
  `description` text NOT NULL,
  `images` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `telephone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`consignment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/
class ControllerMailCustomForm extends Controller {
	public function add_garage_form() {
		
		return false;
		
		$this->load->model('catalog/category');
		$this->load->model('catalog/mark');
		
		$html = '<h1>CONSIGNMENT from maseratinet</h1>
					<ul>';
		
		if ($this->customer->isLogged()) {
			$this->load->model('account/customer');

			$customer = $this->model_account_customer->getCustomer($this->customer->getId());
			
			$html .= '<li>Customer name: <b>'.$customer['firstname'].' '.$customer['lastname'].'</b></li>';
			$html .= '<li>Telephone: <b>'.$customer['telephone'].'</b></li>';
			$html .= '<li>Email: <b>'.$customer['email'].'</b></li>';
			$html .= '<li></li>';
			
		}

		$post = $this->request->post;
		
		$sql = "INSERT INTO " . DB_PREFIX . "consignment SET
						`customer_id` = '".(int)$this->customer->getId()."',
						`category_id` = '".(int)$post['part_category']."',
						`mark_id` = '".(int)$post['mark']."',
						`year` = '".(int)$post['year']."',
						`price` = '".(float)$post['price']."',
						`description` = '',
						`images` = '" . $this->db->escape(implode(';', $post['image'])) . "',
						`name` = '" . $this->db->escape($post['name_company']) . "',
						`telephone` = '" . $this->db->escape($post['telephone']) . "',
						`email` = '" . $this->db->escape($post['email']) . "',
						`status` = '1',
						`date_added` = NOW()
		";
		
		$this->db->query($sql);
		
		$info = $this->model_catalog_category->getCategory((int)$post['part_category']);
		$html .= '<li>Category: <b>'.$info['name'].'</b></li>'."\n";

		$info = $this->model_catalog_mark->getMark((int)$post['modeli']);
		$html .= '<li>Mark: <b>'.$info['name'].'</b></li>'."\n";

		$info = $this->model_catalog_mark->getMark((int)$post['mark']);
		$html .= '<li>Model: <b>'.$info['name'].'</b></li>'."\n";

		unset($post['mark']);
		unset($post['modeli']);
		unset($post['part_category']);
		
		foreach($post as $index => $row){
			
			if($index == 'image'){
				foreach($row as $image){
					if($image != ''){
						$html .= '<li><a href="'.$image.'"><img src="'.$image.'" style="width:300px;"></a></li>';
					}
				}
			}else{
				$html .= '<li>'.$index.': <b>'.$row.'</b></li>'."\n";
			}
		}
		
		
		$mail = new Mail($this->config->get('config_mail_engine'));
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo($this->config->get('config_email'));
		//$mail->setTo('folder.list@gmail.com');
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		$mail->setSubject('CONSIGNMENT from maseratinet');
		$mail->setHtml($html);
		$mail->send();
		
	}
}
