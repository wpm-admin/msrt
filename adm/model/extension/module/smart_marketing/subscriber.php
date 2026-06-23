<?php
class ModelExtensionModuleSmartMarketingSubscriber extends Model {
	public function addSubscriber($data) {
		$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "sm_subscriber SET firstname = '" . $this->db->escape(isset($data['firstname']) ? $data['firstname'] : '') . "', lastname = '" . $this->db->escape(isset($data['lastname']) ? $data['lastname'] : '') . "', email = '" . $this->db->escape($data['email']) . "', customer_id = '" . (int)(isset($data['customer_id']) ? $data['customer_id'] : '0') . "', customer_group_id = '" . (int)(isset($data['customer_group_id']) ? $data['customer_group_id'] : '0') . "', country_id = '" . (int)(isset($data['country_id']) ? $data['country_id'] : '0') . "', status = '1', source = 'manual', date_added = NOW()");
	}

	public function editSubscriber($subscriber_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "sm_subscriber SET firstname = '" . $this->db->escape(isset($data['firstname']) ? $data['firstname'] : '') . "', lastname = '" . $this->db->escape(isset($data['lastname']) ? $data['lastname'] : '') . "', email = '" . $this->db->escape($data['email']) . "', customer_id = '" . (int)(isset($data['customer_id']) ? $data['customer_id'] : '0') . "', customer_group_id = '" . (int)(isset($data['customer_group_id']) ? $data['customer_group_id'] : '0') . "', status = '" . (int)(isset($data['status']) ? $data['status'] : '1') . "', source = '" . $this->db->escape(isset($data['source']) ? $data['source'] : '') . "', date_modified = NOW() WHERE subscriber_id = '" . (int)$subscriber_id . "'");
	}

	public function deleteSubscriber($subscriber_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "sm_subscriber WHERE subscriber_id = '" . (int)$subscriber_id . "'");
	}

	public function getSubscriber($subscriber_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sm_subscriber WHERE subscriber_id = '" . (int)$subscriber_id . "'");

		return $query->row;
	}

	public function getSubscriberByEmail($email) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sm_subscriber WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function getSubscribers($data = array()) {
		$sql = "SELECT *, CONCAT(s.firstname, ' ', s.lastname) AS name, s.status AS status, cgd.name AS customer_group, c.name AS country, c.iso_code_2, c.utc FROM " . DB_PREFIX . "sm_subscriber s LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (s.customer_group_id = cgd.customer_group_id AND cgd.language_id = '" . (int)$this->config->get('config_language_id') . "') LEFT JOIN " . DB_PREFIX . "country c ON (s.country_id = c.country_id)";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(s.firstname, ' ', s.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "s.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (!empty($data['filter_customer_id'])) {
			$implode[] = "s.customer_id = '" . (int)$data['filter_customer_id'] . "'";
		}

		if (!empty($data['filter_customer_group_id'])) {
			$implode[] = "s.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}


		if (!empty($data['filter_country_id'])) {
			$implode[] = "s.country_id = '" . (int)$data['filter_country_id'] . "'";
		}

		if (isset($data['filter_rating']) && !is_null($data['filter_rating'])) {
			$implode[] = "s.rating = '" . (int)$data['filter_rating'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "s.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_source'])) {
			$implode[] = "s.source = '" . $this->db->escape($data['filter_source']) . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(s.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(s.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'subscriber_id',
			'name',
			's.email',
			'customer_group',
			's.country_id',
			's.status',
			's.rating',
			's.source',
			's.date_added',
			's.date_modified'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY subscriber_id";
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

	public function getTotalSubscribers($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "sm_subscriber s";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(s.firstname, ' ', s.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "s.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (!empty($data['filter_customer_id'])) {
			$implode[] = "s.customer_id = '" . (int)$data['filter_customer_id'] . "'";
		}

		if (!empty($data['filter_customer_group_id'])) {
			$implode[] = "s.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}

		if (!empty($data['filter_country_id'])) {
			$implode[] = "s.country_id = '" . (int)$data['filter_country_id'] . "'";
		}

		if (isset($data['filter_rating']) && !is_null($data['filter_rating'])) {
			$implode[] = "s.rating = '" . (int)$data['filter_rating'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "s.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_source'])) {
			$implode[] = "s.source = '" . $this->db->escape($data['filter_source']) . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(s.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(s.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function customImport($data) {
		$this->load->model('extension/module/smart_marketing/timer');

		// MYSQL time can be diff. compared with PHP time
		$now = $this->model_extension_module_smart_marketing_timer->getNow();

		$total_imported = 0;

		$subscribers = array();

		$rows = explode("\r\n", $data['import_list']);

		if ($rows) {
			foreach ($rows as $row) {
				$columns = explode("\t", $row);

				if ($columns) {
					$email = isset($columns[0]) ? trim(utf8_strtolower($columns[0])) : '';
					$firstname = isset($columns[1]) ? ucwords($columns[1]) : '';
					$lastname = isset($columns[2]) ? ucwords($columns[2]) : '';
					$customer_id = isset($columns[3]) ? $columns[3] : '0';
					$customer_group_id = isset($columns[4]) ? $columns[4] : '0';
					$status = isset($columns[5]) ? $columns[5] : '1';
					$source = isset($columns[6]) ? $columns[6] : 'custom-import';

					// check if email is valid - otherwise no reason to import
					if ((utf8_strlen($email) < 96) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
						$subscribers[] = array(
							'firstname' 		  => $this->db->escape($firstname),
							'lastname'  		  => $this->db->escape($lastname),
							'email'     		  => $this->db->escape($email),
							'customer_id'       => (int)$customer_id,
							'customer_group_id' => (int)$customer_group_id,
							'status' 			  => (int)$status,
							'source' 			  => $this->db->escape($source),
							'date_added'        => $this->db->escape($now)
						);
					}
				}
			}
		}

		// Try faster insert :)
		if ($subscribers) {
			$implode = array();

			foreach ($subscribers as $subscriber) {
				$implode[] = "('" . implode("', '", $subscriber) . "')";
			}

			$query = $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "sm_subscriber (firstname, lastname, email, customer_id, customer_group_id, status, source, date_added) VALUES " . implode(", ", $implode));

			$total_imported = $this->db->countAffected();
		}

		return $total_imported;
	}

	public function customUnsubscribe($data) {
		$total_unsubscribed = 0;

		$unsubscribers = array();

		$rows = explode("\r\n", $data['unsubscribe_list']);

		if ($rows) {
			foreach ($rows as $row) {
				$email = trim(strtolower($row));

				if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$unsubscribers[] = trim(utf8_strtolower($email));
				}
			}
		}

		if ($unsubscribers) {
			// Split in smaller chunks to avoid too long query  ?????
			$unsubscriber_lists = array_chunk($unsubscribers, 2000);

			if ($unsubscriber_lists) {
				foreach ($unsubscriber_lists as $unsubscriber_list) {
					$query = $this->db->query("UPDATE " . DB_PREFIX . "sm_subscriber SET status = '0', date_modified = NOW(), note = 'custom-unsubscribe' WHERE LOWER(email) IN ('" . implode("','", $unsubscriber_list) . "')");

					$total_unsubscribed += $this->db->countAffected();
				}
			}
		}

		return $total_unsubscribed;
	}

	public function getSources() {
		$query = $this->db->query("SELECT DISTINCT LOWER(source) AS source FROM " . DB_PREFIX . "sm_subscriber");

		return $query->rows;
	}
}
