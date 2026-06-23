<?php
class ModelExtensionModuleAbandonedCarts extends Model {
	public function checkDuplicates($ip){
		$query = $this->db->query("SELECT ip FROM " . DB_PREFIX . "order WHERE ip = '".$this->db->escape($ip)."'");

		return $query->num_rows;
	}
	
	public function addOrder($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($data['payment_country']) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($data['payment_zone']) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "', payment_custom_field = '" . $this->db->escape(isset($data['payment_custom_field']) ? json_encode($data['payment_custom_field']) : '') . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_country']) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', shipping_custom_field = '" . $this->db->escape(isset($data['shipping_custom_field']) ? json_encode($data['shipping_custom_field']) : '') . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float)$data['total'] . "', affiliate_id = '" . (int)$data['affiliate_id'] . "', commission = '" . (float)$data['commission'] . "', marketing_id = '" . (int)$data['marketing_id'] . "', tracking = '" . $this->db->escape($data['tracking']) . "', language_id = '" . (int)$data['language_id'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" .  $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', accept_language = '" . $this->db->escape($data['accept_language']) . "', date_added = NOW(), date_modified = NOW()");

		$order_id = $this->db->getLastId();

		// Products
		if (isset($data['products'])) {
			foreach ($data['products'] as $product) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "', tax = '" . (float)$product['tax'] . "', reward = '" . (int)$product['reward'] . "'");

				$order_product_id = $this->db->getLastId();

				foreach ($product['option'] as $option) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$option['product_option_id'] . "', product_option_value_id = '" . (int)$option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "'");
				}
			}
		}

		// Totals
		if (isset($data['totals'])) {
			foreach ($data['totals'] as $total) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
			}
		}

		return $order_id;
}
	public function recoverEmail($order_id) {
		$order_info = $this->getOrder($order_id);

		$language = new Language($order_info['language_code']);
		$language->load($order_info['language_code']);
		$language->load('extension/module/abandoned_carts');

		$text  = sprintf($language->get('failed_cart_greeting'),ucfirst($order_info['firstname']))."\n\n";
		$text .= $language->get('failed_cart_intro') . "\n\n";
		$text .= $language->get('failed_cart_contents') . "\n";
		$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

		foreach ($order_product_query->rows as $product) {
			$data['products'] = array();
				$data['products'][] = array(
					'name' => $product['name']
				);
		}

		foreach ($order_product_query->rows as $product) {
			$text .= $product['quantity'] . 'x ' . $product['name'] . "\n";

		}

		$text .= "\n".$language->get('failed_cart_body') . "\n\n";
		$text .= $language->get('failed_cart_footer') . "\n\n";
		$text .= $language->get('failed_cart_signoff') . "\n\n";
		$text .= $language->get('failed_cart_signature') . "\n\n";
		$text .= $order_info['store_name'] . "\n";
		$text .= $order_info['store_url'] . "\n";

		$mail = new Mail();
		$mail->protocol      = $this->config->get('config_mail_protocol');
		$mail->parameter     = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port     = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout  = $this->config->get('config_mail_smtp_timeout');
		$mail->setTo($order_info['email']);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
		$mail->setSubject($language->get(html_entity_decode($language->get('subject_prefix')).' '.$order_info['store_name']));
		$mail->setText($text);
		$mail->send();

		//now that we sent an email, mark it so we dont bug them again
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET abandoned = '1' WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getOrders($data = array()) {
		$implode = array();

		if ($this->config->get('abandoned_carts_criteria')){
			foreach ($this->config->get('abandoned_carts_criteria') as $criteria) {
				$implode[] = "'" . (int)$criteria . "'";
			}

		$criteria_statuses = implode(" OR o.order_status_id=", $implode);
		}

		//$sql = "SELECT o.order_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS order_status, o.ip, o.user_agent, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified, o.abandoned FROM `" . DB_PREFIX . "order` o";
		$sql = "SELECT CONCAT(c.firstname, ' ', c.lastname) AS customer, (SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "cart` o2 WHERE o.session_id = o2.session_id) AS product_total,
				o.session_id,  o.date_added, c.customer_id, c.telephone, c.email
				FROM `" . DB_PREFIX . "cart` o
				LEFT JOIN `" . DB_PREFIX . "customer` c ON (c.customer_id = o.customer_id)";

		$sql .= " WHERE o.date_added >= DATE_SUB(NOW(), INTERVAL ".$this->config->get('abandoned_carts_limit')." DAY) && NOW() >= DATE_ADD(o.date_added, INTERVAL 1 HOUR)
			GROUP BY o.session_id ORDER BY o.date_added DESC";

		if (!empty($implode)){
			//$sql .= " || o.order_status_id=" . $criteria_statuses;
		}

		$sort_data = array(
			'o.order_id',
			'customer',
			'order_status',
			'o.total',
			'o.date_added',
			'o.date_modified',
			'o.abandoned',
			'o.total'
		);

		/*
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY o.order_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
		*/
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);
		
		return $query->rows;
	}
	
	public function getOrderOnCustomerId($customer_id) {
		$implode = array();

		if ($this->config->get('abandoned_carts_criteria')){
			foreach ($this->config->get('abandoned_carts_criteria') as $criteria) {
				$implode[] = "'" . (int)$criteria . "'";
			}

		$criteria_statuses = implode(" OR o.order_status_id=", $implode);
		}

		//$sql = "SELECT o.order_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS order_status, o.ip, o.user_agent, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified, o.abandoned FROM `" . DB_PREFIX . "order` o";
		$sql = "SELECT CONCAT(c.firstname, ' ', c.lastname) AS customer, (SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "cart` o2 WHERE o.session_id = o2.session_id) AS product_total,
				o.session_id,  o.date_added, c.customer_id, c.telephone, c.email
				FROM `" . DB_PREFIX . "cart` o
				LEFT JOIN `" . DB_PREFIX . "customer` c ON (c.customer_id = o.customer_id)";

		$sql .= " WHERE o.customer_id = '".(int)$customer_id."'";

		$sql .= " LIMIT 1";

		$query = $this->db->query($sql);
		
		return $query->row;
	}

	public function getTotalOrders($data = array()) {
		$implode = array();

		if ($this->config->get('abandoned_carts_criteria')){
			foreach ($this->config->get('abandoned_carts_criteria') as $criteria) {
				$implode[] = "'" . (int)$criteria . "'";
			}

		$criteria_statuses = implode(" OR o.order_status_id=", $implode);
		}

		$sql = "SELECT COUNT(DISTINCT session_id) AS total FROM `" . DB_PREFIX . "cart` o WHERE o.date_added >= DATE_SUB(NOW(),
		INTERVAL ".$this->config->get('abandoned_carts_limit')." DAY) && NOW() >= DATE_ADD(o.date_added, INTERVAL 1 HOUR)
		";

		if (!empty($implode)){
			//$sql .= " || o.order_status_id=" . $criteria_statuses;
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function deleteCart($session_id) {
		$sql = "DELETE FROM `" . DB_PREFIX . "cart`  WHERE session_id = '".$session_id."'";

		$this->db->query($sql);
	}

	public function getOrderProducts($session_id) {
		
		$sql = "SELECT *, c.quantity FROM " . DB_PREFIX . "cart c
					LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = c.product_id)
					LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
					WHERE c.session_id = '".$session_id."' AND pd.language_id = '".(int)$this->config->get('config_language_id')."' ORDER BY pd.name ASC";
		
		$query = $this->db->query($sql);

		return $query->rows;
	}

}
