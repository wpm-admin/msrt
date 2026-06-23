<?php
class ModelVendorVendorField extends Model {
	public function addVendorField($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "vendor_field` SET type = '" . $this->db->escape($data['type']) . "', value = '" . $this->db->escape($data['value']) . "', validation = '" . $this->db->escape($data['validation']) . "', location = '" . $this->db->escape($data['location']) . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "'");

		$vendor_field_id = $this->db->getLastId();

		foreach ($data['vendor_field_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_field_description SET vendor_field_id = '" . (int)$vendor_field_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		if (isset($data['vendor_field_vendor_group'])) {
			foreach ($data['vendor_field_vendor_group'] as $vendor_field_vendor_group) {
				if (isset($vendor_field_vendor_group['vendor_group_id'])) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_field_vendor_group SET vendor_field_id = '" . (int)$vendor_field_id . "', vendor_group_id = '" . (int)$vendor_field_vendor_group['vendor_group_id'] . "', required = '" . (int)(isset($vendor_field_vendor_group['required']) ? 1 : 0) . "'");
				}
			}
		}

		if (isset($data['vendor_field_value'])) {
			foreach ($data['vendor_field_value'] as $vendor_field_value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_field_value SET vendor_field_id = '" . (int)$vendor_field_id . "', sort_order = '" . (int)$vendor_field_value['sort_order'] . "'");

				$vendor_field_value_id = $this->db->getLastId();

				foreach ($vendor_field_value['vendor_field_value_description'] as $language_id => $vendor_field_value_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_field_value_description SET vendor_field_value_id = '" . (int)$vendor_field_value_id . "', language_id = '" . (int)$language_id . "', vendor_field_id = '" . (int)$vendor_field_id . "', name = '" . $this->db->escape($vendor_field_value_description['name']) . "'");
				}
			}
		}
		
		return $vendor_field_id;
	}

	public function editVendorField($vendor_field_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "vendor_field` SET type = '" . $this->db->escape($data['type']) . "', value = '" . $this->db->escape($data['value']) . "', validation = '" . $this->db->escape($data['validation']) . "', location = '" . $this->db->escape($data['location']) . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE vendor_field_id = '" . (int)$vendor_field_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_field_description WHERE vendor_field_id = '" . (int)$vendor_field_id . "'");

		foreach ($data['vendor_field_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_field_description SET vendor_field_id = '" . (int)$vendor_field_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_field_vendor_group WHERE vendor_field_id = '" . (int)$vendor_field_id . "'");

		if (isset($data['vendor_field_vendor_group'])) {
			foreach ($data['vendor_field_vendor_group'] as $vendor_field_vendor_group) {
				if (isset($vendor_field_vendor_group['vendor_group_id'])) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_field_vendor_group SET vendor_field_id = '" . (int)$vendor_field_id . "', vendor_group_id = '" . (int)$vendor_field_vendor_group['vendor_group_id'] . "', required = '" . (int)(isset($vendor_field_vendor_group['required']) ? 1 : 0) . "'");
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_field_value WHERE vendor_field_id = '" . (int)$vendor_field_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_field_value_description WHERE vendor_field_id = '" . (int)$vendor_field_id . "'");

		if (isset($data['vendor_field_value'])) {
			foreach ($data['vendor_field_value'] as $vendor_field_value) {
				if ($vendor_field_value['vendor_field_value_id']) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_field_value SET vendor_field_value_id = '" . (int)$vendor_field_value['vendor_field_value_id'] . "', vendor_field_id = '" . (int)$vendor_field_id . "', sort_order = '" . (int)$vendor_field_value['sort_order'] . "'");
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_field_value SET vendor_field_id = '" . (int)$vendor_field_id . "', sort_order = '" . (int)$vendor_field_value['sort_order'] . "'");
				}

				$vendor_field_value_id = $this->db->getLastId();

				foreach ($vendor_field_value['vendor_field_value_description'] as $language_id => $vendor_field_value_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_field_value_description SET vendor_field_value_id = '" . (int)$vendor_field_value_id . "', language_id = '" . (int)$language_id . "', vendor_field_id = '" . (int)$vendor_field_id . "', name = '" . $this->db->escape($vendor_field_value_description['name']) . "'");
				}
			}
		}
	}

	public function deleteVendorField($vendor_field_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "vendor_field` WHERE vendor_field_id = '" . (int)$vendor_field_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "vendor_field_description` WHERE vendor_field_id = '" . (int)$vendor_field_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "vendor_field_vendor_group` WHERE vendor_field_id = '" . (int)$vendor_field_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "vendor_field_value` WHERE vendor_field_id = '" . (int)$vendor_field_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "vendor_field_value_description` WHERE vendor_field_id = '" . (int)$vendor_field_id . "'");
	}

	public function getVendorField($vendor_field_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "vendor_field` cf LEFT JOIN " . DB_PREFIX . "vendor_field_description cfd ON (cf.vendor_field_id = cfd.vendor_field_id) WHERE cf.vendor_field_id = '" . (int)$vendor_field_id . "' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getVendorFields($data = array()) {
		if (empty($data['filter_vendor_group_id'])) {
			$sql = "SELECT * FROM `" . DB_PREFIX . "vendor_field` cf LEFT JOIN " . DB_PREFIX . "vendor_field_description cfd ON (cf.vendor_field_id = cfd.vendor_field_id) WHERE cfd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		} else {
			$sql = "SELECT * FROM " . DB_PREFIX . "vendor_field_vendor_group cfcg LEFT JOIN `" . DB_PREFIX . "vendor_field` cf ON (cfcg.vendor_field_id = cf.vendor_field_id) LEFT JOIN " . DB_PREFIX . "vendor_field_description cfd ON (cf.vendor_field_id = cfd.vendor_field_id) WHERE cfd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		}

		if (!empty($data['filter_name'])) {
			$sql .= " AND cfd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_vendor_group_id'])) {
			$sql .= " AND cfcg.vendor_group_id = '" . (int)$data['filter_vendor_group_id'] . "'";
		}

		$sort_data = array(
			'cfd.name',
			'cf.type',
			'cf.location',
			'cf.status',
			'cf.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY cfd.name";
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

	public function getVendorFieldDescriptions($vendor_field_id) {
		$vendor_field_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor_field_description WHERE vendor_field_id = '" . (int)$vendor_field_id . "'");

		foreach ($query->rows as $result) {
			$vendor_field_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $vendor_field_data;
	}
	
	public function getVendorFieldValue($vendor_field_value_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor_field_value cfv LEFT JOIN " . DB_PREFIX . "vendor_field_value_description cfvd ON (cfv.vendor_field_value_id = cfvd.vendor_field_value_id) WHERE cfv.vendor_field_value_id = '" . (int)$vendor_field_value_id . "' AND cfvd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}
	
	public function getVendorFieldValues($vendor_field_id) {
		$vendor_field_value_data = array();

		$vendor_field_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor_field_value cfv LEFT JOIN " . DB_PREFIX . "vendor_field_value_description cfvd ON (cfv.vendor_field_value_id = cfvd.vendor_field_value_id) WHERE cfv.vendor_field_id = '" . (int)$vendor_field_id . "' AND cfvd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY cfv.sort_order ASC");

		foreach ($vendor_field_value_query->rows as $vendor_field_value) {
			$vendor_field_value_data[$vendor_field_value['vendor_field_value_id']] = array(
				'vendor_field_value_id' => $vendor_field_value['vendor_field_value_id'],
				'name'                  => $vendor_field_value['name']
			);
		}

		return $vendor_field_value_data;
	}
	
	public function getVendorFieldVendorGroups($vendor_field_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "vendor_field_vendor_group` WHERE vendor_field_id = '" . (int)$vendor_field_id . "'");

		return $query->rows;
	}

	public function getVendorFieldValueDescriptions($vendor_field_id) {
		$vendor_field_value_data = array();

		$vendor_field_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor_field_value WHERE vendor_field_id = '" . (int)$vendor_field_id . "'");

		foreach ($vendor_field_value_query->rows as $vendor_field_value) {
			$vendor_field_value_description_data = array();

			$vendor_field_value_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor_field_value_description WHERE vendor_field_value_id = '" . (int)$vendor_field_value['vendor_field_value_id'] . "'");

			foreach ($vendor_field_value_description_query->rows as $vendor_field_value_description) {
				$vendor_field_value_description_data[$vendor_field_value_description['language_id']] = array('name' => $vendor_field_value_description['name']);
			}

			$vendor_field_value_data[] = array(
				'vendor_field_value_id'          => $vendor_field_value['vendor_field_value_id'],
				'vendor_field_value_description' => $vendor_field_value_description_data,
				'sort_order'                     => $vendor_field_value['sort_order']
			);
		}

		return $vendor_field_value_data;
	}

	public function getTotalVendorFields() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "vendor_field`");

		return $query->row['total'];
	}
}