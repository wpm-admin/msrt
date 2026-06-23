<?php
class ModelVendorVendor extends Model {
	public function addVendor($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "vendor SET vendor_group_id = '" . (int)$data['vendor_group_id'] . "', prefix = '" . $this->db->escape($data['prefix']) . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', vendor_field = '" . $this->db->escape(isset($data['vendor_field']) ? json_encode($data['vendor_field']) : json_encode(array())) . "', newsletter = '" . (int)$data['newsletter'] . "', salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', status = '" . (int)$data['status'] . "', safe = '" . (int)$data['safe'] . "', date_added = NOW()");

		$vendor_id = $this->db->getLastId();

		if ($data['faccount_number']) {
			$this->db->query("UPDATE " . DB_PREFIX . "vendor SET account_number = '" . $this->db->escape($data['account_number']) . "' WHERE vendor_id = '" . (int)$vendor_id . "'");
		}

		if ($data['telephone2']) {
			$this->db->query("UPDATE " . DB_PREFIX . "vendor SET telephone2 = '" . $this->db->escape($data['telephone2']) . "' WHERE vendor_id = '" . (int)$vendor_id . "'");
		}

		if ($data['shipping']) {
			$this->db->query("UPDATE " . DB_PREFIX . "vendor SET shipping = '" . $this->db->escape($data['shipping']) . "' WHERE vendor_id = '" . (int)$vendor_id . "'");
		}

		
		if (isset($data['vendor_address'])) {
			foreach ($data['vendor_address'] as $vendor_address) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_address SET vendor_id = '" . (int)$vendor_id . "', firstname = '" . $this->db->escape($vendor_address['firstname']) . "', lastname = '" . $this->db->escape($vendor_address['lastname']) . "', company = '" . $this->db->escape($vendor_address['company']) . "', vendor_address_1 = '" . $this->db->escape($vendor_address['vendor_address_1']) . "', vendor_address_2 = '" . $this->db->escape($vendor_address['vendor_address_2']) . "', city = '" . $this->db->escape($vendor_address['city']) . "', postcode = '" . $this->db->escape($vendor_address['postcode']) . "', country_id = '" . (int)$vendor_address['country_id'] . "', zone_id = '" . (int)$vendor_address['zone_id'] . "', vendor_field = '" . $this->db->escape(isset($vendor_address['vendor_field']) ? json_encode($vendor_address['vendor_field']) : json_encode(array())) . "'");

				if (isset($vendor_address['default'])) {
					$vendor_address_id = $this->db->getLastId();

					$this->db->query("UPDATE " . DB_PREFIX . "vendor SET vendor_address_id = '" . (int)$vendor_address_id . "' WHERE vendor_id = '" . (int)$vendor_id . "'");
				}
			}
		}
		
		//if ($data['affiliate']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_affiliate SET vendor_id = '" . (int)$vendor_id . "', company = '" . $this->db->escape($data['company']) . "', website = '" . $this->db->escape($data['website']) . "', tracking = '" . $this->db->escape($data['tracking']) . "', commission = '" . (float)$data['commission'] . "', tax = '" . $this->db->escape($data['tax']) . "', payment = '" . $this->db->escape($data['payment']) . "', cheque = '" . $this->db->escape($data['cheque']) . "', paypal = '" . $this->db->escape($data['paypal']) . "', bank_name = '" . $this->db->escape($data['bank_name']) . "', bank_branch_number = '" . $this->db->escape($data['bank_branch_number']) . "', bank_swift_code = '" . $this->db->escape($data['bank_swift_code']) . "', bank_account_name = '" . $this->db->escape($data['bank_account_name']) . "', bank_account_number = '" . $this->db->escape($data['bank_account_number']) . "', vendor_field = '" . $this->db->escape(isset($data['vendor_field']) ? json_encode($data['vendor_field']) : json_encode(array())) . "', status = '" . (int)$data['affiliate'] . "', date_added = NOW()");
		//}
		
		return $vendor_id;
	}

	public function editVendor($vendor_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "vendor SET vendor_group_id = '" . (int)$data['vendor_group_id'] . "', prefix = '" . $this->db->escape($data['prefix']) . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', vendor_field = '" . $this->db->escape(isset($data['vendor_field']) ? json_encode($data['vendor_field']) : json_encode(array())) . "', newsletter = '" . (int)$data['newsletter'] . "', status = '" . (int)$data['status'] . "', safe = '" . (int)$data['safe'] . "' WHERE vendor_id = '" . (int)$vendor_id . "'");

		if ($data['password']) {
			$this->db->query("UPDATE " . DB_PREFIX . "vendor SET salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE vendor_id = '" . (int)$vendor_id . "'");
		}
		
		if ($data['account_number']) {
			$this->db->query("UPDATE " . DB_PREFIX . "vendor SET account_number = '" . $this->db->escape($data['account_number']) . "' WHERE vendor_id = '" . (int)$vendor_id . "'");
		}

		if ($data['telephone2']) {
			$this->db->query("UPDATE " . DB_PREFIX . "vendor SET telephone2 = '" . $this->db->escape($data['telephone2']) . "' WHERE vendor_id = '" . (int)$vendor_id . "'");
		}

		if ($data['shipping']) {
			$this->db->query("UPDATE " . DB_PREFIX . "vendor SET shipping = '" . $this->db->escape($data['shipping']) . "' WHERE vendor_id = '" . (int)$vendor_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_address WHERE vendor_id = '" . (int)$vendor_id . "'");

		if (isset($data['vendor_address'])) {
			foreach ($data['vendor_address'] as $vendor_address) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_address SET vendor_address_id = '" . (int)$vendor_address['vendor_address_id'] . "', vendor_id = '" . (int)$vendor_id . "', firstname = '" . $this->db->escape($vendor_address['firstname']) . "', lastname = '" . $this->db->escape($vendor_address['lastname']) . "', company = '" . $this->db->escape($vendor_address['company']) . "', vendor_address_1 = '" . $this->db->escape($vendor_address['vendor_address_1']) . "', vendor_address_2 = '" . $this->db->escape($vendor_address['vendor_address_2']) . "', city = '" . $this->db->escape($vendor_address['city']) . "', postcode = '" . $this->db->escape($vendor_address['postcode']) . "', country_id = '" . (int)$vendor_address['country_id'] . "', zone_id = '" . (int)$vendor_address['zone_id'] . "', vendor_field = '" . $this->db->escape(isset($vendor_address['vendor_field']) ? json_encode($vendor_address['vendor_field']) : json_encode(array())) . "'");

				if (isset($vendor_address['default'])) {
					$vendor_address_id = $this->db->getLastId();

					$this->db->query("UPDATE " . DB_PREFIX . "vendor SET vendor_address_id = '" . (int)$vendor_address_id . "' WHERE vendor_id = '" . (int)$vendor_id . "'");
				}
			}
		}
		
		//if ($data['affiliate']) {
			$this->db->query("REPLACE INTO " . DB_PREFIX . "vendor_affiliate SET vendor_id = '" . (int)$vendor_id . "', company = '" . $this->db->escape($data['company']) . "', website = '" . $this->db->escape($data['website']) . "', tracking = '" . $this->db->escape($data['tracking']) . "', commission = '" . (float)$data['commission'] . "', tax = '" . $this->db->escape($data['tax']) . "', payment = '" . $this->db->escape($data['payment']) . "', cheque = '" . $this->db->escape($data['cheque']) . "', paypal = '" . $this->db->escape($data['paypal']) . "', bank_name = '" . $this->db->escape($data['bank_name']) . "', bank_branch_number = '" . $this->db->escape($data['bank_branch_number']) . "', bank_swift_code = '" . $this->db->escape($data['bank_swift_code']) . "', bank_account_name = '" . $this->db->escape($data['bank_account_name']) . "', bank_account_number = '" . $this->db->escape($data['bank_account_number']) . "', status = '" . (int)$data['affiliate'] . "', date_added = NOW()");
		//}		
	}

	public function editToken($vendor_id, $token) {
		$this->db->query("UPDATE " . DB_PREFIX . "vendor SET token = '" . $this->db->escape($token) . "' WHERE vendor_id = '" . (int)$vendor_id . "'");
	}

	public function deleteVendor($vendor_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor WHERE vendor_id = '" . (int)$vendor_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_activity WHERE vendor_id = '" . (int)$vendor_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_affiliate WHERE vendor_id = '" . (int)$vendor_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_approval WHERE vendor_id = '" . (int)$vendor_id . "'");
 		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_history WHERE vendor_id = '" . (int)$vendor_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_reward WHERE vendor_id = '" . (int)$vendor_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_transaction WHERE vendor_id = '" . (int)$vendor_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_ip WHERE vendor_id = '" . (int)$vendor_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_address WHERE vendor_id = '" . (int)$vendor_id . "'");
	}

	public function getVendor($vendor_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "vendor WHERE vendor_id = '" . (int)$vendor_id . "'");

		return $query->row;
	}
		
	public function dellProducts($vendor_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_to_product WHERE p2v.vendor_id = '" . (int)$vendor_id . "'");
	}
	
	public function getProducts($vendor_id) {
		$query = $this->db->query("SELECT DISTINCT *, p.model AS model, p.sku AS sku, p2v.model AS vendor_model, p2v.sku AS vendor_sku FROM " . DB_PREFIX . "vendor_to_product p2v
								  LEFT JOIN " . DB_PREFIX . "product p ON (p2v.product_id = p.product_id)
								  LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
								  WHERE p2v.vendor_id = '" . (int)$vendor_id . "' AND pd.language_id = '".$this->config->get('config_language_id')."'");

		return $query->rows;
	}

	public function getVendorByEmail($email) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "vendor WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}
	
	public function getVendorIdOnEmail($email) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "vendor WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' LIMIT 1");

		if($query->num_rows == 0){
			return false;
		}
		
		return (int)$query->row['vendor_id'];
	}
	
	public function getVendorOnName($name) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "vendor WHERE LCASE(firstname) = '" . $this->db->escape(utf8_strtolower($name)) . "' OR LCASE(lastname) = '" . $this->db->escape(utf8_strtolower($name)) . "' LIMIT 1");

		if($query->num_rows == 0){
			return false;
		}
		
		return (int)$query->row['vendor_id'];
	}
	
	public function getVendors($data = array()) {
		$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cgd.name AS vendor_group FROM " . DB_PREFIX . "vendor c LEFT JOIN " . DB_PREFIX . "vendor_group_description cgd ON (c.vendor_group_id = cgd.vendor_group_id)";
		
		if (!empty($data['filter_affiliate'])) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "vendor_affiliate ca ON (c.vendor_id = ca.vendor_id)";
		}		
		
		$sql .= " WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (!empty($data['filter_account_number'])) {
			$implode[] = "c.account_number LIKE '" . $this->db->escape($data['filter_account_number']) . "%'";
		}

		if (!empty($data['filter_shipping'])) {
			$implode[] = "c.shipping LIKE '" . $this->db->escape($data['filter_shipping']) . "%'";
		}

		if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
			$implode[] = "c.newsletter = '" . (int)$data['filter_newsletter'] . "'";
		}

		if (!empty($data['filter_vendor_group_id'])) {
			$implode[] = "c.vendor_group_id = '" . (int)$data['filter_vendor_group_id'] . "'";
		}

		if (!empty($data['filter_affiliate'])) {
			$implode[] = "ca.status = '" . (int)$data['filter_affiliate'] . "'";
		}
		
		if (!empty($data['filter_ip'])) {
			$implode[] = "c.vendor_id IN (SELECT vendor_id FROM " . DB_PREFIX . "vendor_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'name',
			'c.account_number',
			'c.shipping',
			'c.email',
			'vendor_group',
			'c.status',
			'c.ip',
			'c.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

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

	public function getAddress($vendor_address_id) {
		$vendor_address_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor_address WHERE vendor_address_id = '" . (int)$vendor_address_id . "'");

		if ($vendor_address_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$vendor_address_query->row['country_id'] . "'");

			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$vendor_address_format = $country_query->row['vendor_address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';
				$vendor_address_format = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$vendor_address_query->row['zone_id'] . "'");

			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}

			return array(
				'vendor_address_id'     => $vendor_address_query->row['vendor_address_id'],
				'vendor_id'    => $vendor_address_query->row['vendor_id'],
				'firstname'      => $vendor_address_query->row['firstname'],
				'lastname'       => $vendor_address_query->row['lastname'],
				'company'        => $vendor_address_query->row['company'],
				'vendor_address_1'      => $vendor_address_query->row['vendor_address_1'],
				'vendor_address_2'      => $vendor_address_query->row['vendor_address_2'],
				'postcode'       => $vendor_address_query->row['postcode'],
				'city'           => $vendor_address_query->row['city'],
				'zone_id'        => $vendor_address_query->row['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $vendor_address_query->row['country_id'],
				'country'        => $country,
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'vendor_address_format' => $vendor_address_format,
				'vendor_field'   => json_decode($vendor_address_query->row['vendor_field'], true)
			);
		}
	}

	public function getAddresses($vendor_id) {
		$vendor_address_data = array();

		$query = $this->db->query("SELECT vendor_address_id FROM " . DB_PREFIX . "vendor_address WHERE vendor_id = '" . (int)$vendor_id . "'");

		foreach ($query->rows as $result) {
			$vendor_address_info = $this->getAddress($result['vendor_address_id']);

			if ($vendor_address_info) {
				$vendor_address_data[$result['vendor_address_id']] = $vendor_address_info;
			}
		}

		return $vendor_address_data;
	}

	public function getTotalVendors($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "vendor c";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}
	
		if (!empty($data['filter_account_number'])) {
			$implode[] = "c.account_number LIKE '" . $this->db->escape($data['filter_account_number']) . "%'";
		}

		if (!empty($data['filter_shipping'])) {
			$implode[] = "c.shipping LIKE '" . $this->db->escape($data['filter_shipping']) . "%'";
		}

		if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
			$implode[] = "newsletter = '" . (int)$data['filter_newsletter'] . "'";
		}

		if (!empty($data['filter_vendor_group_id'])) {
			$implode[] = "vendor_group_id = '" . (int)$data['filter_vendor_group_id'] . "'";
		}

		if (!empty($data['filter_ip'])) {
			$implode[] = "vendor_id IN (SELECT vendor_id FROM " . DB_PREFIX . "vendor_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$implode[] = "status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
        
    public function getAffiliateByTracking($tracking) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor_affiliate WHERE tracking = '" . $this->db->escape($tracking) . "'");
                
        return $query->row;
    }
	
	public function getAffiliate($vendor_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor_affiliate WHERE vendor_id = '" . (int)$vendor_id . "'");

		return $query->row;
	}
	
	public function getAffiliates($data = array()) {
		$sql = "SELECT DISTINCT *, CONCAT(c.firstname, ' ', c.lastname) AS name FROM " . DB_PREFIX . "vendor_affiliate ca LEFT JOIN " . DB_PREFIX . "vendor c ON (ca.vendor_id = c.vendor_id)";
		
		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}		
		
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
						
		$query = $this->db->query($sql . "ORDER BY name");

		return $query->rows;
	}
	
	public function getTotalAffiliates($data = array()) {
		$sql = "SELECT DISTINCT COUNT(*) AS total FROM " . DB_PREFIX . "vendor_affiliate ca LEFT JOIN " . DB_PREFIX . "vendor c ON (ca.vendor_id = c.vendor_id)";
		
		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}		
		
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		
		return $query->row['total'];
	}

	public function getTotalAddressesByVendorId($vendor_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "vendor_address WHERE vendor_id = '" . (int)$vendor_id . "'");

		return $query->row['total'];
	}

	public function getTotalAddressesByCountryId($country_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "vendor_address WHERE country_id = '" . (int)$country_id . "'");

		return $query->row['total'];
	}

	public function getTotalAddressesByZoneId($zone_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "vendor_address WHERE zone_id = '" . (int)$zone_id . "'");

		return $query->row['total'];
	}

	public function getTotalVendorsByVendorGroupId($vendor_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "vendor WHERE vendor_group_id = '" . (int)$vendor_group_id . "'");

		return $query->row['total'];
	}

	public function addHistory($vendor_id, $comment) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_history SET vendor_id = '" . (int)$vendor_id . "', comment = '" . $this->db->escape(strip_tags($comment)) . "', date_added = NOW()");
	}

	public function getHistories($vendor_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT comment, date_added FROM " . DB_PREFIX . "vendor_history WHERE vendor_id = '" . (int)$vendor_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalHistories($vendor_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "vendor_history WHERE vendor_id = '" . (int)$vendor_id . "'");

		return $query->row['total'];
	}

	public function addTransaction($vendor_id, $description = '', $amount = '', $order_id = 0) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_transaction SET vendor_id = '" . (int)$vendor_id . "', order_id = '" . (int)$order_id . "', description = '" . $this->db->escape($description) . "', amount = '" . (float)$amount . "', date_added = NOW()");
	}

	public function deleteTransactionByOrderId($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_transaction WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getTransactions($vendor_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor_transaction WHERE vendor_id = '" . (int)$vendor_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalTransactions($vendor_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total  FROM " . DB_PREFIX . "vendor_transaction WHERE vendor_id = '" . (int)$vendor_id . "'");

		return $query->row['total'];
	}

	public function getTransactionTotal($vendor_id) {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "vendor_transaction WHERE vendor_id = '" . (int)$vendor_id . "'");

		return $query->row['total'];
	}

	public function getTotalTransactionsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "vendor_transaction WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function addReward($vendor_id, $description = '', $points = '', $order_id = 0) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_reward SET vendor_id = '" . (int)$vendor_id . "', order_id = '" . (int)$order_id . "', points = '" . (int)$points . "', description = '" . $this->db->escape($description) . "', date_added = NOW()");
	}

	public function deleteReward($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_reward WHERE order_id = '" . (int)$order_id . "' AND points > 0");
	}

	public function getRewards($vendor_id, $start = 0, $limit = 10) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor_reward WHERE vendor_id = '" . (int)$vendor_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalRewards($vendor_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "vendor_reward WHERE vendor_id = '" . (int)$vendor_id . "'");

		return $query->row['total'];
	}

	public function getRewardTotal($vendor_id) {
		$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "vendor_reward WHERE vendor_id = '" . (int)$vendor_id . "'");

		return $query->row['total'];
	}

	public function getTotalVendorRewardsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "vendor_reward WHERE order_id = '" . (int)$order_id . "' AND points > 0");

		return $query->row['total'];
	}

	public function getIps($vendor_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}
		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor_ip WHERE vendor_id = '" . (int)$vendor_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);
		
		return $query->rows;
	}

	public function getTotalIps($vendor_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "vendor_ip WHERE vendor_id = '" . (int)$vendor_id . "'");

		return $query->row['total'];
	}

	public function getTotalVendorsByIp($ip) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "vendor_ip WHERE ip = '" . $this->db->escape($ip) . "'");

		return $query->row['total'];
	}

	public function getTotalLoginAttempts($email) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "vendor_login` WHERE `email` = '" . $this->db->escape($email) . "'");

		return $query->row;
	}

	public function deleteLoginAttempts($email) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "vendor_login` WHERE `email` = '" . $this->db->escape($email) . "'");
	}
}
